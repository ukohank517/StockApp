<?php

namespace DDApp\Http\Controllers\Work;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DDApp\Http\Controllers\Controller;
use DDApp\Ordersheet;
use DDApp\Param;
use DDApp\Skutransfer;
use DDApp\Zonecode;


class ItemSearchController extends Controller
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
    *
    *
    * @return \Illuminate\Http\Response
    */
    public function index(){
        $year = NULL;
        $month = NULL;
        $box = NULL;
        $sku = NULL;
        $line = NULL;
        $orderid = NULL;
        $stat = NULL;
        $ordersheets = Ordersheet::paginate(30);

        return view('work.item_search', compact('ordersheets', 'year', 'month', 'box', 'sku', 'line', 'orderid', 'stat'));
    }


    public function search(Request $request){
        $request->validate([
            'year'=>'nullable|integer',
            'month'=>'nullable|integer',
            'line'=>'nullable|integer',
        ]);

        $year = $request->year;
        $month = $request->month;
        $box = $request->box;
        $sku = $request->sku;
        $line = $request->line;
        $orderid = $request->orderid;
        $stat = $request->stat;

        $query = Ordersheet::query();

        if($year!=NULL) $query->whereYear('date', '=', $year);
        if($month!=NULL) $query->whereMonth('date', '=', $month);
        if($box!=NULL) $query->where('box', $box);
        if($sku!=NULL) $query->where('sku', $sku);
        if($line!=NULL) $query->where('line', $line);
        if($orderid!=NULL) $query->where('order_id', $orderid);

        if($line != NULL){
            $items=$query->get();

            $ids=array();
            $plural_markers=array();
            foreach($items as $item){
                if($item->plural_marker != NULL) $plural_markers[] = $item->plural_marker;
                else $ids[] = $item->id;
            }

            $query = Ordersheet::query();
            $query->wherein('id', $ids);

            foreach($plural_markers as $plural_marker){
                $query->orWhere('plural_marker', $plural_marker);
            }
        }

        if($stat != NULL){
            $items = $query->get();

            $iddone = array();
            $idnotyet = array();
            foreach($items as $item){
                if(($item->stock_num == $item->aim_num) || ($item->box != NULL)) {
                    $iddone[] = $item->id;
                }
                else {
                    $idnotyet[] = $item->id;
                }
            }

            $query = Ordersheet::query();
            if($stat == "done")$query->wherein('id', $iddone);
            else $query->wherein('id', $idnotyet);
        }

        $ordersheets = $query->paginate(30)->appends([
            'year' => $year,
            'month' => $month,
            'box' => $box,
            'sku' => $sku,
            'line' => $line,
            'orderid' => $orderid,
            'stat' => $stat,
        ]);

        return view('work.item_search', compact('ordersheets', 'year', 'month', 'box', 'sku', 'line', 'orderid', 'stat'));
    }

}
