<?php

namespace DDApp\Http\Controllers\OrderReceive;

use Illuminate\Http\Request;
use Illuminata\Support\Facades\Auth;
use DDApp\Http\Controllers\Controller;
use DDApp\Model\OrderReceive\Stockitem;
use Excel;

class StockitemsController extends Controller
{
    public function index(Request $request)
    {
        if($request->search_sku == null){
            $stockitems = Stockitem::paginate(15);
            $edit_id = -1;

        }else{
            $stockitems = Stockitem::where('parent_sku', $request->search_sku)->paginate(15);
            $edit_id = -1;
        }
        return view('order_receive.stockitems.index', compact('stockitems', 'edit_id'));
    }

    public function all_renew(Request $request){
        if($request->search_sku == null){
            $stockitems = Stockitem::paginate(15);
            $edit_id = -1;

        }else{
            $stockitems = Stockitem::where('parent_sku', $request->search_sku)->paginate(15);
            $edit_id = -1;
        }
        return view('order_receive.stockitems.all_renew', compact('stockitems', 'edit_id'));
    }


    public function upload(Request $request){
        $this->validate($request,[
            'csv_file' => 'required|mimes:xlsx,txt|max:1500'
        ]);

        $file = $request->file('csv_file');

        //ファイルの読み込み
        try{
            $reader = Excel::load($file->getRealPath());
            if($reader == null){
                throw new \Exception('読めませんでした。');
                return "error1";
            }

            if(preg_match('/SheetCollection$/', get_class($reader->all())))
            {
                // シートが複数
                $sheet = $reader->first();
            }
            else if(preg_match('/RowCollection$/', get_class($reader->all())))
            {
                // シートが一枚
                $sheet = $reader;
            }
            else
            {
                throw new \Exception('予期せぬエラー。');
            }
        }
        catch(\Exception $e){
            return $e;
        }

        $rows = $sheet->toArray();
        Stockitem::truncate();// DBをクリア
        if(count($rows)){
            foreach($rows as $row){
                $item = Stockitem::firstOrCreate($row);
                continue;
            }
        }
        \Session::flash('flash_message', '更新しました。');
        return redirect()->route('order_receive::stockitems.index');

    }

    public function download(){
        return "to be continue";
    }

    public function select(Request $request){
        $stockitems = Stockitem::paginate(15);
        $edit_id = $request->edit_id;
        $edit_data = Stockitem::where('id',$edit_id)->first();
        return view('order_receive.stockitems.index', compact('stockitems', 'edit_id', 'edit_data'));
    }

    public function edit(Request $request){
        $request->validate([
            'num'=>'integer',
            'price'=>'integer|nullable',
        ]);

        $stock = Stockitem::where('id', $request->edit_id)->first();
        $stock->stock_num = $request->num;
        $stock->price = $request->price;
        $stock->place = $request->place;
        $stock->memo = $request->memo;
        $stock->save();

        \Session::flash('success_msg', '更新が完了しました。');
        return redirect()->route('order_receive::stockitems.index');
    }

    public function test(Request $request){

        return $request;
    }
}
