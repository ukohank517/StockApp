<?php

namespace DDApp\Http\Controllers\Work;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DDApp\Http\Controllers\Controller;
use DDApp\Ordersheet;
use DDApp\Param;
use DDApp\Skutransfer;
use DDApp\Zonecode;

use DateTime;

use ZendPdf\PdfDocument;
use ZendPdf\Font;
use ZendPdf\Page;

class PrintController extends Controller
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
    * Show the work page
    *
    * @return \Illuminate\Http\Response
    */
    public function index(){
        return view('work.print');
    }



    public function print(Request $request){
        $request->validate([
            'box_name'=>'required',
            'page'=>'required',
        ]);

        $box_name = $request->box_name;
        $page = $request->page;

        $items = Ordersheet::where('box', $box_name)
        ->orderBy('id_in_box', 'asc')
        ->get();

        if(in_array("greenlabel", $page)){
            \Session::flash('greenlabel_flag', 'flag');
            $this->make_greenlabel($box_name);
        }
        if(in_array("invoice", $page)){
            \Session::flash('invoice_flag', 'flag');
            $filename = "pic/invoice/".$box_name.".pdf";
            $this->make_invoice($box_name,$filename);
        }


        return view('work.print', compact('box_name', 'items'));
    }

    public function single_index(Request $request){
        $items=Ordersheet::where('box','single_waiting')
        ->orderBy('id_in_box', 'asc')
        ->get();
        return view('work.single_print', compact('items'));
    }

    public function addition(Request $request){
        $request->validate([
            'month'=>'required|integer',
            'line'=>'required|integer',
        ]);

        $month = $request->month;
        $line = $request->line;

        $box = 'single_waiting';

        $items = Ordersheet::where('line', $line)
        ->whereMonth('date', '=', $month)
        ->get();
        if(count($items) == 0){
            \Session::flash('addition_fail', '商品見つかりませんでした。');
            return redirect()->route('work::print.single_index');
        }
        if($items[0]->box == $box){
            \Session::flash('addition_fail', '既に処理した商品です！');
            return redirect()->route('work::print.single_index');
        }

        if($items[0]->plural_marker!=NULL){
            $plural_marker = $items[0]->plural_marker;
            $items = Ordersheet::where('plural_marker', $plural_marker)->get();
        }

        $id_in_box = 1;
        $waiting_item = Ordersheet::where('box', $box)
        ->orderBy('id_in_box', 'desc')
        ->first();
        if($waiting_item!=NULL)
        $id_in_box = $waiting_item->id_in_box + 1;

        foreach($items as $item){
            $item->box= $box;
            $item->id_in_box = $id_in_box;

            $item->save();
        }

        \Session::flash('addition_success', $line);
        return redirect()->route('work::print.single_index');
    }

    public function addition_clear(){
        $box = 'single_waiting';

        $items = Ordersheet::where('box', $box)
        ->get();
        foreach($items as $item){
            $item->box = "single_send";
            $item->save();
        }

        return redirect()->route('work::print.single_index');
    }

    public function single_print(Request $request){
        $box = 'single_waiting';

        $filename = "single_invoice.pdf";
        $this->make_invoice($box,$filename);

        \Session::flash('file_exist', 'flag');
        return redirect()->route('work::print.single_index');
    }



    public function make_greenlabel($box_name){
        $filename = "pic/greenlabel/".$box_name.".pdf";
        \File::delete($filename);
        $pdf = new PdfDocument();
        $width = 213.5;
        $height = 292.5;

        $items = Ordersheet::where('box', $box_name)
        ->orderBy('id_in_box', 'asc')
        ->get();

        if(count($items) == 0) return ;
        for($page = 0; $page < $items[count($items)-1]->id_in_box / 8 ;$page++){
            $pdfPage = new Page(Page::SIZE_A4_LANDSCAPE);
            //$pdfPage->rotate(0,0, M_PI*0.5);
            $font = Font::fontWithPath('fonts/HanaMinA.ttf');
            $pdfPage->setFont($font, 8);
            for($i = 0; $i < 4; $i=$i+1){
                for($j = 0; $j < 2; $j=$j+1){

                    $starti = $i * $width;
                    $startj = (1-$j) * $height;

                    $item = Ordersheet::where('box', $box_name)
                    ->where('id_in_box', $page*8+$i*2+$j+1)
                    ->get();
                    if(count($item) == 0) break;
                    $names = explode("(", $item[0]->goods_name);

                    $num = 0;
                    foreach($item as $de){
                        $num += $de->aim_num;
                    }
                    $totalprice = (int)(10/$num) * $num;



                    $pdfPage->drawText(substr($names[0], 0, 20), $starti+28, $startj+119, 'UTF-8');
                    if(count($names)>=2){
                        $namestr = explode(")",$names[1]);
                        $pdfPage->drawText('('.$namestr[0].')', $starti+28, $startj+105, 'UTF-8');
                        if(count($namestr) >=2){
                            $pdfPage->drawText($namestr[1], $starti+28, $startj+91, 'UTF-8');
                        }
                    }
                    $pdfPage->drawText('g', $starti+112, $startj+101, 'UTF-8');
                    $pdfPage->drawText('USD', $starti+134.4, $startj+119, 'UTF-8');
                    $pdfPage->drawText($totalprice, $starti+134.4, $startj+105, 'UTF-8');
                    $pdfPage->drawText('g', $starti+100, $startj+64, 'UTF-8');
                    $pdfPage->drawText('USD', $starti+159.6, $startj+65, 'UTF-8');
                    $pdfPage->drawText($totalprice, $starti+159.6, $startj+75, 'UTF-8');
                    $pdfPage->drawText($box_name.'/'.($page*8+$i*2+$j+1), $starti+34, $startj+15, 'UTF-8');
                    $pdfPage->drawText(date("Y/m/d")."   Manabu Hano", $starti+84, $startj+15, 'UTF-8');
                }
            }
            $pdf->pages[] = $pdfPage;
        }

        $pdf->save($filename);
    }

    public function make_invoice($box_name, $filename){
        \File::delete($filename);

        $pdf = PdfDocument::load("pic/invoice_base.pdf"); // PDFドキュメント作成
        $template = $pdf->pages[0];

        $items = Ordersheet::where('box', $box_name)
        ->orderBy('id_in_box', 'desc')
        ->get();


        if(count($items)==0) return;
        for($idx = 0; $idx < $items[0]->id_in_box; $idx++){
            $pdfPage = new Page($template);


            $item = Ordersheet::where('box', $box_name)
            ->where('id_in_box', $idx+1)
            ->get();

            $cnt = $idx+1;
            $zone_info = Zonecode::where('code', $item[0]->country_code)->get();
            if(count($zone_info)>0){$cnt = $cnt.'/'.$zone_info[0]->no;}
            $num = 0;
            foreach($item as $de){
                $num += $de->aim_num;
            }
            $singleprice = (int)(10/$num);
            $totalprice = $singleprice * $num;

            $adress1 = "";
            $adress2 = "";
            $adress3 = "";
            $adress4 = "";
            $adress5 = "";
            $adress6 = "";
            $onelinechar = 35;

            $adress1 = $item[0]->adress1;
            $adress1.= " ";
            $adress1.= $item[0]->adress2;
            $adress1.= " ";
            $adress1.= $item[0]->adress3;
            $adress1.= " ";
            $adress1.= $item[0]->adress4;

            if(strlen($adress1) > $onelinechar){
                $space_idx = 0;
                for($i = $onelinechar; ; $i--){
                    if($adress1[$i] == " "){
                        $space_idx = $i;
                        break;
                    }
                }
                $adress2 = substr($adress1, $space_idx);
                $adress1 = substr($adress1, 0, $space_idx);
            }
            if(strlen($adress2) > $onelinechar){
                $space_idx = 0;
                for($i = $onelinechar; ; $i--){
                    if($adress2[$i] == " "){
                        $space_idx = $i;
                        break;
                    }
                }
                $adress3 = substr($adress2, $space_idx);
                $adress2 = substr($adress2, 0, $space_idx);
            }
            if(strlen($adress3) > $onelinechar){
                $space_idx = 0;
                for($i = $onelinechar; ; $i--){
                    if($adress3[$i] == " "){
                        $space_idx = $i;
                        break;
                    }
                }
                $adress4 = substr($adress3, $space_idx);
                $adress3 = substr($adress3, 0, $space_idx);
            }
            if(strlen($adress4) > $onelinechar){
                $space_idx = 0;
                for($i = $onelinechar; ; $i--){
                    if($adress4[$i] == " "){
                        $space_idx = $i;
                        break;
                    }
                }
                $adress5 = substr($adress4, $space_idx);
                $adress4 = substr($adress4, 0, $space_idx);
            }
            if(strlen($adress5) > $onelinechar){
                $space_idx = 0;
                for($i = $onelinechar; ; $i--){
                    if($adress5[$i] == " "){
                        $space_idx = $i;
                        break;
                    }
                }
                $adress6 = substr($adress5, $space_idx);
                $adress5 = substr($adress5, 0, $space_idx);
            }

            $font = Font::fontWithPath('fonts/HanaMinA.ttf');// 日本語も使用可能のフォント
            $pdfPage->setFont($font, 9);          //フォント設定

            // left up
            $pdfPage->drawText($item[0]->customer_name, 30, 540, 'UTF-8');
            $pdfPage->drawText($adress1, 30, 530, 'UTF-8');
            $pdfPage->drawText($adress2, 30, 520, 'UTF-8');
            $pdfPage->drawText($adress3, 30, 510, 'UTF-8');
            $pdfPage->drawText($adress4, 30, 500, 'UTF-8');
            $pdfPage->drawText($adress5, 30, 490, 'UTF-8');
            $pdfPage->drawText($adress6, 30, 480, 'UTF-8');

            $pdfPage->drawText($item[0]->postid, 55, 467, 'UTF-8');
            $pdfPage->drawText($item[0]->country, 35, 451, 'UTF-8');
            $pdfPage->drawText($item[0]->phone_number, 45, 434, 'UTF-8');

            // left down
            $pdfPage->drawText($item[0]->customer_name, 25, 270, 'UTF-8');
            $pdfPage->drawText($adress1, 25, 260, 'UTF-8');
            $pdfPage->drawText($adress2, 25, 250, 'UTF-8');
            $pdfPage->drawText($adress3, 25, 240, 'UTF-8');
            $pdfPage->drawText($adress4, 25, 230, 'UTF-8');
            $pdfPage->drawText($adress5, 25, 220, 'UTF-8');
            $pdfPage->drawText($adress6, 25, 210, 'UTF-8');

            $pdfPage->drawText($item[0]->phone_number, 40, 200, 'UTF-8');

            // right up
            $pdfPage->drawText($item[0]->line.'~'.$item[count($item)-1]->line, 235, 429, 'UTF-8');
            $pdfPage->drawText($cnt, 375, 429, 'UTF-8');
            $pdfPage->drawText(date('Y/m/d'), 345, 399, 'UTF-8');


            $pdfPage->setFont($font, 16);          //フォント設定
            $pdfPage->drawText($item[0]->sendway, 185, 429, 'UTF-8');
            $pdfPage->drawText($item[0]->sendway, 230, 315, 'UTF-8');

            // detail
            $pdfPage->drawText(substr($item[0]->goods_name, 0 , 30), 20, 123, 'UTF-8');
            $pdfPage->drawText($num, 280, 123, 'UTF-8');
            $pdfPage->drawText($singleprice, 320, 123, 'UTF-8');
            $pdfPage->drawText($totalprice, 370, 123, 'UTF-8');

            $pdfPage->setFont($font, 8);          //フォント設定
            $pdfPage->drawText($totalprice, 370, 63, 'UTF-8');

            $pdfPage->setFont($font, 10);          //フォント設定
            $pdfPage->drawText("box:".$box_name,60, 30, 'UTF-8');

            $pdf->pages[] = $pdfPage;
        }

        unset($pdf->pages[0]);
        $pdf->save($filename);
    }

}
