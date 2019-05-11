<?php

namespace DDApp\Http\Controllers\OrderReceive;

use Illuminate\Http\Request;
use Illuminata\Support\Facades\Auth;
use DDApp\Http\Controllers\Controller;
use DDApp\Model\OrderReceive\Orderdocument;
use DDApp\Model\OrderReceive\Itemrelation;
use DDApp\Model\OrderReceive\Stockitem;
use Excel;

class ReceiveController extends Controller
{
    public function index(){
        \Session::forget('detail_flag');
        $items = Orderdocument::all();
        $docids = [];
        $ids = [];
        foreach($items as $item){
            if($item->done == true) continue;
            if(!in_array($item->doc_id, $docids)){
                $docids[] = $item->doc_id;
                $ids[] = $item->id;
            }
        }

        $query = Orderdocument::query();
        $query->wherein('id', $ids);
        $orderdocuments = $query->paginate(15);


        return view('order_receive.receive.documentsearch', compact('orderdocuments', 'docids'));
    }

    public function research(Request $request){
        \Session::forget('detail_flag');
        $request->validate([
            'search_sku'=>'string',
            'search_num'=>'integer|nullable',
        ]);


        $search_jan = $request->search_sku;
        $its = Itemrelation::where('child_jan', $search_jan)->get();

        $search_skus = array();
        if($its != null) {
            foreach($its as $it){
                $search_skus[] = $it->parent_sku;
            }
        }
        else $search_skus = null;
        $search_num = $request->search_num;
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
            foreach($search_skus as $search_sku){
                if($search_num != null){
                    if(($item->parent_sku == $search_sku)&&($item->parent_num == $search_num)){
                        if(!in_array($item->doc_id, $docids)){
                            $docids[] = $item->doc_id;
                            $ids[] = $item->id;
                        }
                    }
                }else{
                    if((strcmp($item->parent_sku , $search_sku)==0)){
                        if(!in_array($item->doc_id, $docids)){
                            $docids[] = $item->doc_id;
                            $ids[] = $item->id;
                        }
                    }
                }
            }
        }


        $query = Orderdocument::query();
        $query->wherein('id', $ids);
        $orderdocuments = $query->paginate(15);


        return view('order_receive.receive.documentsearch', compact('orderdocuments', 'docids'));
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
        $orderdocuments = $query->paginate(40);

        $search_doc = $request->doc_id;
        $query = Orderdocument::query();
        $query->where('doc_id', $search_doc)->orderBy('id');
        $detailitems = $query->paginate(40);

        \Session::flash('detail_flag',$search_doc);
        return view('order_receive.receive.documentsearch', compact('orderdocuments', 'docids', 'detailitems'));
    }

    public function receive(Request $request){
        $orderdocuments = Orderdocument::where('doc_id', $request->doc_id)->get();

        foreach($orderdocuments as $item){
            if($item->warehouse=="M"){
                $stock = Stockitem::firstOrCreate([
                    'parent_sku' => $item->parent_sku,
                ]);
                $stock->stock_num = $stock->stock_num + $item->parent_num;
                $stock->place = $item->product_place;

                $formeritem = Stockitem::where('parent_sku', $item->parent_sku)->first();
                if($formeritem == null){
                    $stock->place = $item->product_place;
                    $stock->price = $item->price;
                }

                $stock->save();
            }

            $item->done = true;
            $item->save();
        }

        return redirect()->route('order_receive::receive.index');
    }
}
