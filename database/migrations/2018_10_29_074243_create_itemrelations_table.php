<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemrelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
        関係は :
        親sku A * 1 + 親sku B * 2 = 子sku
        基本の個は、親で管理
        jan = 子　で管理する。
        */
        Schema::create('itemrelations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('parent_sku');
            $table->string('child_sku');
            $table->integer('parent_num');
            $table->string('child_jan');
            $table->string('child_asin');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('itemrelations');
    }
}
