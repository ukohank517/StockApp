<?php

namespace DDApp\Http\Controllers\OrderReceive;

use Illuminate\Http\Request;
use Illuminata\Support\Facades\Auth;
use DDApp\Http\Controllers\Controller;
//  モデルuseする必要がある。 use DDApp\Model\;
use Excel;

/*
発注関連
*/
class StockOrderController extends Controller
{
    public function index()
    {
        return view('order_receive.info');
    }

}
