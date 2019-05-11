<?php

namespace DDApp\Http\Controllers\OrderReceive;

use Illuminate\Http\Request;
use Illuminata\Support\Facades\Auth;
use DDApp\Http\Controllers\Controller;
use DDApp\Model\OrderReceive\Itemrelation;
use Excel;

class ItemrelationsController extends Controller
{
    public function index()
    {
        $itemrelations = Itemrelation::paginate(15);
        return view('order_receive.itemrelations.index', compact('itemrelations'));
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

        Itemrelation::truncate();// DB を clear

        if(count($rows)){
            foreach ($rows as $row) {
                Itemrelation::firstOrCreate($row);
                continue;
            }
        }

        \Session::flash('flash_message', '更新しました。');
        return redirect()->route('order_receive::itemrelations.index');
    }

    public function download(){
        $itemrelations = Itemrelation::get()->toArray();
        return \Excel::create('itemrelation', function($excel) use ($itemrelations) {
            $excel->sheet('sheet', function($sheet) use ($itemrelations)
            {
                $sheet->fromArray($itemrelations);
            });
        })->download('xlsx');
    }

    public function select(Request $request){

    }
    public function edit(Request $request){

    }

}
