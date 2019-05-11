<?php

namespace DDApp\Model\OrderReceive;

use Illuminate\Database\Eloquent\Model;

class Itemrelation extends Model
{
    protected $table = "itemrelations";
    public $timestamps = false;

    protected $fillable = [
        'parent_sku',
        'child_sku',
        'parent_num',
        'child_jan',
        'child_asin'
    ];
}
