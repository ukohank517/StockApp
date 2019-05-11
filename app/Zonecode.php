<?php

namespace DDApp;

use Illuminate\Database\Eloquent\Model;

class Zonecode extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'country',
        'code',
        'no'
    ];
}
