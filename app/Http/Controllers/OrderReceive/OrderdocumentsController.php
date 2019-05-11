<?php

namespace DDApp\Http\Controllers\OrderReceive;

use Illuminate\Http\Request;
use Illuminata\Support\Facades\Auth;
use DDApp\Http\Controllers\Controller;
use DDApp\Model\OrderReceive\Orderdocument;
use DDApp\Model\OrderReceive\Stockitem;
use Excel;

class OrderdocumentsController extends Controller
{
    public function index()
    {
        \Session::forget('detail_flag');
        $items = Orderdocument::all();
        $docids = [];
        $ids = [];
        foreach($items as $item){
            if(!in_array($item->doc_id, $docids)){
                $docids[] = $item->doc_id;
                $ids[] = $item->id;
            }
        }

        $query = Orderdocument::query();
        $query->wherein('id', $ids);
        $query->orderBy('id', 'desc');
        $orderdocuments = $query->paginate(15);

        $edit_id = -1;
        return view('order_receive.orderdocuments.upload', compact('orderdocuments', 'docids', 'edit_id'));
    }


    public function upload(Request $request)
    {
        $orderdocuments = Orderdocument::all();
        $docids = [];
        foreach ($orderdocuments as $orderdocument) {
            if(!in_array($orderdocument->doc_id, $docids)){
                $docids[] = $orderdocument->doc_id;
            }
        }
        if(in_array($request->doc_id, $docids)){
            \Session::flash('e_flash_message', '同じ発注IDは既に使用されました。');
            return redirect()->route('order_receive::orderdocuments.index');
        }

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
        try{
            $rows = $sheet->toArray();

            $doc_id = $request->doc_id;
            $order_date = $request->order_date;

                if(count($rows)){
                foreach ($rows as $row) {
                    if($row['parent_sku'] == NULL) continue;
                    if($row['parent_num'] == NULL) $row['parent_num'] = 0;

                    $item = Orderdocument::firstOrCreate([
                        'doc_id' => $doc_id,
                        'order_date' => $order_date,
                        'parent_sku' => $row['parent_sku'],
                        'parent_num' => $row['parent_num'],
                        'supplier' => $row['supplier'],
                        'price' => $row['price'],
                        'warehouse' => $row['warehouse'],
                        'product_place' => $row['product_place'],
                        'memo'=>$row['memo'],
                        'done' => false,
                    ],[
                    ]);

                    $items[] = $item;
                    continue;
                }
            }
            foreach($items as $item){
                $item->save();
            }

            \Session::flash('flash_message', '更新しました。');
            return redirect()->route('order_receive::orderdocuments.index');
        }
        catch(\Exception $e){
            \Session::flash('e_flash_message', $e);
            return redirect()->route('order_receive::orderdocuments.index');
        }

    }

    public function download(){
        $orderdocuments = Orderdocument::get()->toArray();
        return \Excel::create('orderdocument', function($excel) use ($orderdocuments) {
            $excel->sheet('sheet', function($sheet) use ($orderdocuments)
            {
                $sheet->fromArray($orderdocuments);
            });
        })->download('xlsx');
    }

    public function detail(Request $request){
        $search_doc = $request->search_doc;

        $orderdocuments = Orderdocument::all();

        $items = [];
        foreach($orderdocuments as $item){
            if(in_array($item->doc_id, $search_doc)){
                $items[] = $item;
            }
        }

        $docids = [];
        $ids = [];
        foreach($items as $item){
            if(!in_array($item->doc_id, $docids)){
                $docids[] = $item->doc_id;
                $ids[] = $item->id;
            }

        }

        $query = Orderdocument::query();
        $query->wherein('id', $ids);
        $orderdocuments = $query->paginate(15);

        $search_doc = $request->doc_id;
        $query = Orderdocument::query();
        $query->where('doc_id', $search_doc)->orderBy('id');
        $detailitems = $query->paginate(15);

        \Session::flash('detail_flag',$search_doc);
        $edit_id=-1;
        return view('order_receive.orderdocuments.upload', compact('orderdocuments', 'docids', 'detailitems', 'edit_id'));

    }

    public function select(Request $request){

        $search_doc = $request->search_doc;

        $orderdocuments = Orderdocument::all();

        $items = [];
        foreach($orderdocuments as $item){
            if(in_array($item->doc_id, $search_doc)){
                $items[] = $item;
            }
        }

        $docids = [];
        $ids = [];
        foreach($items as $item){
            if(!in_array($item->doc_id, $docids)){
                $docids[] = $item->doc_id;
                $ids[] = $item->id;
            }

        }

        $query = Orderdocument::query();
        $query->wherein('id', $ids);
        $orderdocuments = $query->paginate(15);

        $search_doc = $request->doc_id;
        $query = Orderdocument::query();
        $query->where('doc_id', $search_doc)->orderBy('id');
        $detailitems = $query->paginate(15);

        \Session::flash('detail_flag',$search_doc);
        $edit_id = $request->select_id;
        return view('order_receive.orderdocuments.upload', compact('orderdocuments', 'docids', 'detailitems', 'edit_id'));
    }




    public function edit(Request $request){
        $orderdocument = Orderdocument::where('id', $request->select_id)->first();
        $orderdocument->supplier = $request->supplier;
        $orderdocument->price = $request->price;
        $orderdocument->parent_num = $request->num;
        $orderdocument->save();

        \Session::flash('success_msg', '更新完了しました');





        $search_doc = $request->search_doc;

        $orderdocuments = Orderdocument::all();

        $items = [];
        foreach($orderdocuments as $item){
            if(in_array($item->doc_id, $search_doc)){
                $items[] = $item;
            }
        }

        $docids = [];
        $ids = [];
        foreach($items as $item){
            if(!in_array($item->doc_id, $docids)){
                $docids[] = $item->doc_id;
                $ids[] = $item->id;
            }

        }

        $query = Orderdocument::query();
        $query->wherein('id', $ids);
        $orderdocuments = $query->paginate(15);

        $search_doc = $request->doc_id;
        $query = Orderdocument::query();
        $query->where('doc_id', $search_doc)->orderBy('id');
        $detailitems = $query->paginate(15);

        \Session::flash('detail_flag',$search_doc);
        $edit_id = $request->select_id;
        return view('order_receive.orderdocuments.upload', compact('orderdocuments', 'docids', 'detailitems', 'edit_id'));


    }
}
