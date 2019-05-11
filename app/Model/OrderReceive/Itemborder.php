<?php

namespace DDApp\Model\OrderReceive;

use Illuminate\Database\Eloquent\Model;

class Itemborder extends Model
{
    protected $table = "itemborders";
    public $timestamps = false;

    protected $fillable = [
        'parent_sku',
        'yellow_border',
        'red_border'
    ];
}
