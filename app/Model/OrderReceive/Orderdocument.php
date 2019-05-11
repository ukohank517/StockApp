<?php

namespace DDApp\Model\OrderReceive;

use Illuminate\Database\Eloquent\Model;

class Orderdocument extends Model
{
    protected $table = "orderdocuments";
    public $timestamps = false;

    protected $fillable = [
        'doc_id',
        'order_date',
        'parent_sku',
        'parent_num',
        'price',
        'supplier',
        'price',
        'warehouse',
        'product_place',
        'memo',
        'done'
    ];
}
