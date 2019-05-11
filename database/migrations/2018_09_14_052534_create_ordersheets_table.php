<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersheetsTable extends Migration
{
    /**
    * Run the migrations.
    *
    * @return void
    */
    public function up()
    {
        Schema::create('ordersheets', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date');
            $table->string('box')->nullable();
            $table->integer('id_in_box')->nullable();
            $table->string('sku');
            $table->integer('line');
            $table->string('stock_stat')->nullable();
            $table->string('sendway');
            $table->string('order_id');
            $table->string('customer_name')->nullable();
            $table->string('adress1')->nullable();
            $table->string('adress2')->nullable();
            $table->string('adress3')->nullable();
            $table->string('adress4')->nullable();
            $table->string('postid')->nullable();
            $table->string('country')->nullable();
            $table->string('country_code')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('goods_name')->nullable();
            $table->integer('aim_num');
            $table->integer('stock_num');
            $table->string('plural_marker')->nullable();
            $table->string('wait_box')->nullable();
        });
    }

    /**
    * Reverse the migrations.
    *
    * @return void
    */
    public function down()
    {
        Schema::dropIfExists('ordersheets');
    }
}
