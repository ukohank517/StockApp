<?php

namespace DDApp\Http\Controllers\Work;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DDApp\Http\Controllers\Controller;
use DDApp\Ordersheet;
use DDApp\Param;
use DDApp\Skutransfer;
use DDApp\Zonecode;


class WorkController extends Controller
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
    * 作業ページ
    * ログインuserの処理している内容を表示する。
    *
    * @return \Illuminate\Http\Response
    */
    public function index(){
        // ログインしているユーザーが処理しているボックス
        $box_name = Auth::user()->dealing_box_name;

        // そのボックス内に入ってる商品
        $dealing_items = Ordersheet::where('box', $box_name)
        ->orderBy('id_in_box', 'desc')
        ->get();

        // 最大数に揃ったら検索できないように設定する。
        if(count($dealing_items)!=0){
            $max_goods_in_box = Param::where('param_name', 'max_goods_in_box')->first();
            if($dealing_items[0]->id_in_box == $max_goods_in_box->value){
                \Session::flash('full_flag', 'flag');
            }
        }

        return view('work.work', compact('box_name','dealing_items'));
    }

    /**
    * ログインユーザーの処理する最終行(複数注文も可)内容を削除
    * 処理終えた後、ボックス内の情報を更新して再表示
    *
    */
    public function delete_last_line(){
        $box_name = Auth::user()->dealing_box_name;
        $dealing_items = Ordersheet::where('box', $box_name)
        ->orderBy('id_in_box', 'desc')
        ->get();

        if(count($dealing_items)!=0){

            $last_item_no = $dealing_items[0]->id_in_box;
            $last_items = Ordersheet::where('box', $box_name)->where('id_in_box', $last_item_no)->get();

            foreach($last_items as $last_item){
                // ラストNOの処理痕跡を削除
                $last_item -> box = null;
                $last_item -> wait_box = null;
                $last_item -> stock_num = 0;
                $last_item -> save();
            }
            \Session::flash('flash_message', '最終行削除しました。行番号:['.$last_item->line
            .'],sku:['.$last_item->sku
            .'],注文番号:['.$last_item->order_id
            .']');
        }
        return redirect()->route('work::work');
    }

    /**
    * ログインuserのボックスを更新する。
    *
    */
    public function renew_box(){
        $user = Auth::user();
        $box_name = $user->dealing_box_name;
        $box_name_index = Param::where('param_name', 'box_name_index')->first();
        $box_name = $box_name_index -> value;

        $max_box_name = Param::where('param_name', 'max_box_name')->first();
        $box_name_index->value = ($box_name_index->value+1) % $max_box_name->value;
        $box_name_index->save();

        $user->dealing_box_name = $box_name;
        $user->save();

        return redirect()->route('work::work');
    }

}
