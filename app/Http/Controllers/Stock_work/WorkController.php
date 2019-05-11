<?php

namespace DDApp\Http\Controllers\Stock_work;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DDApp\Http\Controllers\Controller;
use DDApp\Model\OrderReceive\Orderdocument;
use DDApp\Model\OrderReceive\Stockitem;
use DDApp\Model\OrderReceive\Itemrelation;
use DDApp\Ordersheet;
use DDApp\Zonecode;
use DDApp\Param;

use DateTime;

use ZendPdf\PdfDocument;
use ZendPdf\Font;
use ZendPdf\Page;

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
        $zero_ignore = "false";
        $former_sku = "";
        $skuinfo ="";
        $stockinfo = "";
        $selfitem = self::selfbox();
        $box_name = Param::where('param_name', 'floor_three_box_name')->first()->value;
        return view('stock_work.work', compact('zero_ignore', 'former_sku', 'skuinfo', 'stockinfo', 'selfitem', 'box_name'));
    }


    public function deal_and_recommend(Request $request){
        $zero_ignore = $request->zero_ignore;
        $former_sku = $request->former_sku;
        $ans = self::search_helper($request);

        $ordersheets = $ans[0];
        $former_sku = $ans[1];

        $skuinfo = self::skuinfo($former_sku);
        $stockinfo = self::stockinfo($skuinfo);

        $selfitem = self::selfbox();
        if(count($selfitem)==0)$selfidx = 0;
        else $selfidx = $selfitem[0]->id_in_box;

        if($request->deal_ids !=null){
            $box_name = Param::where('param_name', 'floor_three_box_name')->first();
            foreach($request->deal_ids as $id){
                //検索する内容
                $hit_item = Ordersheet::where('id', $id)->first();
                //この内容と同じ注文番号のものをhit_itemsに
                $hit_items=array();
                if($hit_item->plural_marker != null){
                    $hit_items = Ordersheet::where('plural_marker', $hit_item->plural_marker)->get();
                }else{
                    $hit_items[] = $hit_item;
                }
                //これらをすべてboxに入れておく、idx+1
                $selfidx = $selfidx + 1;
                foreach($hit_items as $item){
                    if($item->id_in_box != 0) continue;
                    $item->box = $box_name->value;
                    $item->id_in_box = $selfidx;
                    $item->save();

                    // TODO: 商品数を引く
                    if(strcmp($item->stock_stat,"在庫")==0){
                        $itemrelations = Itemrelation::where("child_sku", $item->sku)->get();
                        $procedure = array();
                        $skulist = array();

                        foreach($itemrelations as $i){
                            if(in_array($i->parent_sku, $skulist)){
                                continue;
                            }else{
                                $skulist[] = $i->parent_sku;
                                $it["sku"] = $i->parent_sku;
                                $it["num"] = $i->parent_num;
                                $procedure[] = $it;
                            }
                        }

                        foreach($procedure as $it){
                            $i = Stockitem::where("parent_sku", $it["sku"])->first();
                            $i->stock_num = $i->stock_num - $item->aim_num*$it["num"];
                            $i->save();
                        }


                    }

                }


            }
        }

        $selfitem = self::selfbox();
        $box_name = Param::where('param_name', 'floor_three_box_name')->first()->value;
        return view('stock_work.recommend', compact('ordersheets', 'zero_ignore', 'former_sku', 'skuinfo', 'stockinfo', 'box_name', 'selfitem'));
    }

    public function print(Request $request){
        $box_name = Param::where('param_name', 'floor_three_box_name')->first()->value;

        $this->make_greenlabel($box_name);
        $this->make_invoice($box_name);

        \Session::flash('print_flag', 'flag');
        if($request->to_url == 0){
            return redirect()->route('stock_work::work');
        }else{
            return redirect()->route('stock_work::work.deal_and_recommend', ['zero_ignore' => $request->zero_ignore, 'former_sku' => $request->former_sku]);
        }

    }

    public function print_in_work(Request $request){
        $box_name = Param::where('param_name', 'floor_three_box_name')->first()->value;

        $this->make_greenlabel($box_name);
        $this->make_invoice($box_name);

        \Session::flash('print_flag', 'flag');
        return redirect()->route('stock_work::work');
    }

    public function renew_box(Request $request){
        $box_name = self::take_one_box();
        $param = Param::where('param_name', 'floor_three_box_name')->first();
        $param->value = $box_name;
        $param->save();

        \Session::forget('print_flag', 'flag');
        if($request->to_url == 0){
            return redirect()->route('stock_work::work');
        }else{
            return redirect()->route('stock_work::work.deal_and_recommend', ['zero_ignore' => $request->zero_ignore, 'former_sku' => $request->former_sku]);
        }
    }

    // TODO:
    public function work_by_line(Request $request){
        return view('stock_work.deal_by_lines');
    }


    public static function make_greenlabel($box_name){
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

    public static function make_invoice($box_name){
        $filename = "pic/invoice/".$box_name.".pdf";
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

            $adress1 = $item[0]->adress1;
            $adress2 = $item[0]->adress2;
            $adress3 = $item[0]->adress3;
            $adress4 = $item[0]->adress4;
            $adress5 = "";
            $adress6 = "";

            if(strlen($adress3)> 42){
                $space_idx = 0;
                for($i = 42; ; $i--){
                    if($adress3[$i] == " "){
                        $space_idx = $i;
                        break;
                    }
                }
                $adress5 = $adress4;
                $adress4 = substr($adress3, $space_idx);
                $adress3 = substr($adress3, 0, $space_idx);
            }

            if(strlen($adress2)> 42){
                $space_idx = 0;
                for($i = 42; ; $i--){
                    if($adress2[$i] == " "){
                        $space_idx = $i;
                        break;
                    }
                }
                $adress6 = $adress5;
                $adress5 = $adress4;
                $adress4 = $adress3;
                $adress3 = substr($adress2, $space_idx);
                $adress2 = substr($adress2, 0, $space_idx);
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

    private static function stockinfo($skuinfo){
        $ret = array();
        if($skuinfo == null) return null;

        foreach($skuinfo as $item){
            $stockitem = Stockitem::where('parent_sku', $item->parent_sku)->first();
            if($stockitem != null){
                $it["num"] = $stockitem->stock_num;
                $it["name"] = $stockitem->name;
                $it["place"] = $stockitem->place;
            }else{
                $it["num"] = "no information";
                $it["name"] = "noname";
                $it["place"] = "not found";
            }
            $ret[$item->parent_sku] =$it;
        }
        return $ret;
    }

    private static function stockname($skuinfo){
        $ret = array();
        if($skuinfo == null) return null;

        foreach($skuinfo as $item){
            $stockitem = Stockitem::where('parent_sku', $item->parent_sku)->first();
            if($stockitem != null){
                $ret[$item->parent_sku] = $stockitem->name;
            }else{
                $ret[$item->parent_sku] = "noname";
            }
        }
        return $ret;
    }

    private static function stockplace($skuinfo){
        $ret = array();
        if($skuinfo == null) return null;

        foreach($skuinfo as $item){
            $stockitem = Stockitem::where('parent_sku', $item->parent_sku)->first();
            if($stockitem != null){
                $ret[$item->parent_sku] = $stockitem->place;
            }else{
                $ret[$item->parent_sku] = "not fonund";
            }
        }
        return $ret;
    }

    private static function skuinfo($sku){
        $itemrelations = Itemrelation::where('child_sku', $sku)->get();
        $ret = array();
        $skulist = array();

        foreach($itemrelations as $item){
            if(in_array($item->parent_sku, $skulist)){
                continue;
            }else{
                $skulist[] = $item->parent_sku;
                $ret[] = $item;
            }
        }

        return $ret;
    }


    private static function selfbox(){
        //一回目(システムの運用上)のみ、ボックスを用意
        $box_name = Param::where('param_name', 'floor_three_box_name')->first();
        if($box_name == null){
            $box_name = self::take_one_box();
            $param = Param::firstOrCreate([
                'param_name' => 'floor_three_box_name',
                'value' => $box_name,
                'description' => '3階作業用のbox_nameが格納されている',
            ],[]);
            $param->save();
            $box_name = Param::where('param_name', 'floor_three_box_name')->first();
        }

        $ordersheets = Ordersheet::where('box', $box_name->value)->orderBy('id_in_box', 'desc')->get();
        return $ordersheets;
    }

    private static function take_one_box(){
        $box_name_index = Param::where('param_name', 'box_name_index')->first();
        $box_name = $box_name_index->value;

        $max_box_name = Param::where('param_name', 'max_box_name')->first();
        $box_name_index->value = ($box_name_index->value + 1) % $max_box_name->value;
        $box_name_index->save();

        return $box_name;
    }

    private static function search_helper($request){
        $zero_ignore = $request->zero_ignore;
        $former_sku = $request->former_sku;

        $ordersheets = Ordersheet::where('stock_stat', ['在庫'])->whereNull('box')->get();

        $skulists = array();

        foreach($ordersheets as $item){
            if(in_array($item->sku, $skulists)) continue;
            $skulists[] = $item->sku;
        }
        if(count($skulists) == 0 || $former_sku == "FINISH"){
            $former_sku = "FINISH";
            $ans[] = $ordersheets;
            $ans[] = $former_sku;
            return $ans;
        }

        if($former_sku == null)  $former_sku = $skulists[0];
        else if($former_sku == $skulists[count($skulists)-1]){
            // すべてが検索終了処理！！！
            $former_sku = "FINISH";
        }
        else{
            for($i = 0; $i < count($skulists); $i++){
                if($skulists[$i] == $former_sku){
                    $former_sku = $skulists[$i + 1];
                    break;
                }
            }
        }

        $items = Ordersheet::where('stock_stat', '在庫')->where('sku', $former_sku)->whereNull('box')->get();

        $ordersheets = array();

        foreach($items as $item){
            if($item->plural_marker!=null){
                $ordersheet = Ordersheet::where('plural_marker', $item->plural_marker)->get();
            }else{
                $ordersheet = Ordersheet::where('id', $item->id)->get();
            }
            $ordersheets[] = $ordersheet;
        }

        $ans[] = $ordersheets;
        $ans[] = $former_sku;
        return $ans;
    }

}
