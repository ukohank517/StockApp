<?php

namespace DDApp;

use Illuminate\Database\Eloquent\Model;

class Param extends Model
{
    protected $table = "params";

    public $timestamps = false;

    protected $fillable =[
        'param_name',
        'value',
        'description'
    ];
}
