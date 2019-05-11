<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItembordersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     /*
     時間span(1ヶ月)で見るとき、
     ordersheetの売る数と、今の在庫数で比較してみる
     boderは1より小さい小数の時、それは直近1ヶ月の売るパーセンテージとみなす
     */
    public function up()
    {
        Schema::create('itemborders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('parent_sku');
            $table->double('yellow_border', 10, 4);
            $table->double('red_border', 10, 4);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('itemborders');
    }
}
