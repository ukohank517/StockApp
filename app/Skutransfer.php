<?php

namespace DDApp;

use Illuminate\Database\Eloquent\Model;

class Skutransfer extends Model
{
    public $timestamps = false;

    protected $fillable =[
        'asin',
        'before',
        'after'
    ];
}
