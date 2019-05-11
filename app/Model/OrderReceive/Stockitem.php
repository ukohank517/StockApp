<?php

namespace DDApp\Model\OrderReceive;

use Illuminate\Database\Eloquent\Model;

class Stockitem extends Model
{
    protected $table = "stockitems";
    public $timestamps = false;

    protected $fillable = [
        'parent_sku',
        'name',
        'stock_num',
        'price',
        'place',
        'memo',
    ];
}
