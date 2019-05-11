<?php

namespace DDApp\Http\Controllers\OrderReceive;

use Illuminate\Http\Request;
use Illuminata\Support\Facades\Auth;
use DDApp\Http\Controllers\Controller;
use DDApp\Model\OrderReceive\Itemborder;
use Excel;

class ItembordersController extends Controller
{
    public function index()
    {
        $itemborders = Itemborder::paginate(15);
        return view('order_receive.itemborders.index', compact('itemborders'));
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
        //return $rows;

        Itemborder::truncate();// DB を clear

        if(count($rows)){
            foreach ($rows as $row) {
                Itemborder::firstOrCreate($row);
                continue;
            }
        }

        \Session::flash('flash_message', '更新しました。');
        return redirect()->route('order_receive::itemborders.index');
    }

    public function download(){
        $itemborders = Itemborder::get()->toArray();
        return \Excel::create('itemborder', function($excel) use ($itemborders) {
            $excel->sheet('sheet', function($sheet) use ($itemborders)
            {
                $sheet->fromArray($itemborders);
            });
        })->download('xlsx');
    }
}
