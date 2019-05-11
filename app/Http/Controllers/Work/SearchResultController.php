<?php

namespace DDApp\Http\Controllers\Work;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use DDApp\Http\Controllers\Controller;
use DDApp\Ordersheet;
use DDApp\Param;
use DDApp\Skutransfer;
use DDApp\Zonecode;


class SearchResultController extends Controller
{
    /**
    * Create a new controller instance.
    *
    * @return void
    */
    public function __construct(){
        $this->middleware('auth');
    }

    /**
    * 指定されたskuにより検索する。
    *
    * @return \Illuminate\Http\Response
    */
    public function index(Request $request){
        $info_message="ヒットした結果:";
        $sku_token = $request->sku_token;

        // skuが一致、在庫<目標、boxがNULL、在庫ではない商品
        $found_item = Ordersheet::where('sku', $sku_token)
        ->whereColumn('stock_num', '<', 'aim_num')
        ->where('box',NULL)
        ->where('stock_stat', NULL)
        ->first();

        $pos_num = 0;
        if($found_item != NULL){
            $pos_num = $found_item->aim_num - $found_item->stock_num;
        }

        $hit_items = [];// 返り値

        $wait_box = NULL;

        //flag初期値
        $cancel_flag = False;
        $plural_flag = False;
        $sendway_flag = False;
        $overtime_flag = False;

        //ヒットすればはいる
        if($found_item != NULL){
            $hit_items[] = $found_item; // todo
            $plural_marker = $found_item->plural_marker;

            // -----------Flag-----------
            if($found_item->sendway == "c") $cancel_flag = True;
            else if(($found_item->sendway != "air")&&($found_item->sendway != "*")) $sendway_flag = True;

            $delay_time = Param::where('param_name', 'delay_days')->get();
            $overtime_value = $delay_time[0]->value ;
            if($overtime_value < (strtotime("now")-strtotime($found_item->date))/60/60/24) $overtime_flag = True;
            if(($found_item->aim_num > 1)||($plural_marker != NULL)) $plural_flag = True;
            // --------------------------


            $wait_box = $found_item->wait_box;
            if($plural_marker!=NULL){
                $same_box_items = Ordersheet::where('plural_marker', $plural_marker)->get();
                foreach($same_box_items as $item){
                    if($wait_box==NULL){$wait_box=$item->wait_box;}
                }
                $hit_items = $same_box_items;
            }
            $found_item->wait_box = $wait_box;
            $found_item->save();
        }

        // 複数で待ちボックスなければ、それを生成する。
        if($plural_flag && $wait_box==NULL){
            $max_wait_name = Param::where('param_name', 'max_wait_name')->first()->value;
            $wait_name_index = Param::where('param_name', 'wait_name_index')->first();

            $wait_box = $wait_name_index->value;
            $wait_name_index->value = ($wait_name_index->value+1)%$max_wait_name;
            $wait_name_index->save();

            $found_item->wait_box = $wait_box;
            $found_item->save();
        }

        // アラートフラグ
        if($cancel_flag)\Session::flash('cancel_flag','flag');
        if($plural_flag)\Session::flash('plural_flag','flag');
        if($sendway_flag)\Session::flash('sendway_flag','flag');
        if($overtime_flag)\Session::flash('overtime_flag','flag');

        return view('work.search_result', compact('info_message', 'sku_token', 'wait_box', 'hit_items', 'overtime_value', 'pos_num'));
    }





    // id用いて+1処理
    public function deal_item(Request $request){
        $info_message = "処理した結果:";
        $delay_time = Param::where('param_name', 'delay_days')->get();
        $overtime_value = $delay_time[0]->value ;

        $id = $request->id;
        $add_num = $request->add_num;

        // 検索する内容は:
        $hit_item = Ordersheet::where('id', $id)->first();

        $sku_token = $hit_item->sku;
        $wait_box = $hit_item->wait_box;

        $hit_item->stock_num = $hit_item->stock_num + $add_num;
        if($hit_item->stock_num > $hit_item->aim_num){
            \Session::flash('overlapping_flag', 'flag');
            redirect()->route('work::work');
        }
        $hit_item->save();

        $hit_items = [];// 返り値
        if($hit_item->plural_marker == NULL){// 単品
            $hit_items[] = $hit_item;
        }else{// 複数
            $hit_items = Ordersheet::where('plural_marker', $hit_item->plural_marker)->get();
        }

        // 全完フラグ
        $fin_flag = True;
        foreach($hit_items as $item){
            if($item->stock_num < $item->aim_num){
                $fin_flag = False;
            }
        }
        $owner_fin_flag = True;
        foreach($hit_items as $item){
            if($item->stock_stat != NULL)continue;
            else if($item->stock_num < $item->aim_num){
                $owner_fin_flag = False;
            }
        }


        \Session::forget('cancel_flag');
        \Session::forget('plural_flag');
        \Session::forget('sendway_flag');
        \Session::forget('overtime_flag');
        \Session::forget('stockstat_flag');

        \Session::flash('secondtime_flag', 'flag');
        if($fin_flag)	\Session::flash('fin_flag', 'flag');
        if($owner_fin_flag)	\Session::flash('owner_fin_flag', 'flag');
        return view('work.search_result', compact('info_message', 'sku_token', 'wait_box', 'hit_items', 'overtime_value'));
    }

    public function put_into_box(Request $request){
        $id = $request->id;
        $hit_item = Ordersheet::where('id', $id)->first();
        $hit_items = [];

        if($hit_item->plural_marker==NULL){
            $hit_items[]=$hit_item;
        }else{
            $hit_items=Ordersheet::where('plural_marker', $hit_item->plural_marker)->get();
        }

        // hit_itemsをすべてboxに入れる。
        $user = Auth::user();
        // まだ処理したことないなら、作業ボックスを新たに振り分け
        if($user->dealing_box_name == 0){
            $box_name_index = Param::where('param_name', 'box_name_index');
            $box_name_index->value = $box_name_index->value + 1;
            $box_name_index->save();

            $user->dealing_box_name = $box_name_index->value - 1;
            $user->save();
        }
        $dealing_box_name = $user->dealing_box_name;
        $item_indexs = Ordersheet::where('box', $dealing_box_name)->orderBy('id_in_box', 'desc')->get();

        if(count($item_indexs) != 0){
            $index = $item_indexs[0]->id_in_box+1;
        }else{
            $index = 1;
        }

        foreach($hit_items as $item){
            $item->box = $dealing_box_name;
            $item->id_in_box = $index;
            $item->save();
        }

        return redirect()->route('work::work');
    }

}
