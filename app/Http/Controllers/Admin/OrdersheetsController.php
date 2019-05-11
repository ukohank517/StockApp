<?php

namespace DDApp\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminata\Support\Facades\Auth;
use DDApp\Http\Controllers\Controller;
use DDApp\Ordersheet;
use Excel;

class OrdersheetsController extends Controller
{
    public function index()
    {
        $ordersheets = Ordersheet::paginate(15);
        return view('admin.ordersheets.index', compact('ordersheets'));
    }


    public function upload(Request $request)
    {

        $this->validate($request, [
            'csv_file' => 'required|mimes:xlsx,txt|max:1500'
        ]);

        $file = $request->file('csv_file');

        // ファイルの読み込み
        try{
            $reader = Excel::load($file->getRealPath());
            if($reader == null)
            {
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

        // ファイル中身のデータをチェックする


        //Ordersheet::truncate();// DB を clear

        if(count($rows)){
            foreach ($rows as $row) {
                Ordersheet::firstOrCreate($row);
                continue;
            }
        }

        \Session::flash('flash_message', '更新しました。');
        return redirect()->route('admin::ordersheets');
    }

    public function download(){
        $ordersheet = Ordersheet::get()->toArray();
        return \Excel::create('ordersheet', function($excel) use ($ordersheet) {
            $excel->sheet('sheet', function($sheet) use ($ordersheet)
            {
                $sheet->fromArray($ordersheet);
            });
        })->download('xlsx');
    }


    //deleteはgetで処理
    public function deletelines(Request $request){
        $this->validate($request, [
            'fromidx' => 'required|integer',
            'tillidx' => 'required|integer',
        ]);

        $fromidx = $request->fromidx;
        $tillidx = $request->tillidx;

        if($fromidx > $tillidx){
            \Session::flash('e_flash_message', '入力数値の大小関係？');
            return redirect()->route('admin::ordersheets');
        }

        for($i = $fromidx; $i <= $tillidx; $i ++){
            $ordersheet = Ordersheet::find($i);
            if($ordersheet==null)continue;
            $ordersheet->delete();
        }

        \Session::flash('flash_message', '消せました。');
        return redirect()->route('admin::ordersheets');
    }

    public function test(){
        $user = Auth::user();
        return $user;
    }
}
