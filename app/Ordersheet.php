<?php

namespace DDApp;

use Illuminate\Database\Eloquent\Model;

class Ordersheet extends Model
{
    public $timestamps = false;

    protected $fillable =[
        'date',
        'box',
        'id_in_box',
        'sku',
        'line',
        'stock_stat',
        'sendway',
        'order_id',
        'customer_name',
        'adress1',
        'adress2',
        'adress3',
        'adress4',
        'postid',
        'country',
        'country_code',
        'phone_number',
        'goods_name',
        'aim_num',
        'stock_num',
        'plural_marker',
        'wait_box'
    ];
}
