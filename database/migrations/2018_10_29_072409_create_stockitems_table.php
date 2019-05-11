<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockitemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stockitems', function (Blueprint $table) {
            $table->increments('id');
            $table->string('parent_sku');
            $table->string('name')->nullable();
            $table->integer('stock_num')->default(0);
            $table->integer('price')->nullable();
            $table->string('place')->nullable();
            $table->string('memo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stockitems');
    }
}
