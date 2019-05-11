<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


/*
* description:
* アプリが使用する変数を格納する場所
*
* 基本はenvファイルを作成すればいいのですが、
* 課長が触れるため、データベースを作成した。
* 初期必要データはseedでデフォルトを生成するが、
* 後期の変更はphpmyadminより手動で操作。
*
*/
class CreateParamsTable extends Migration
{
    /**
    * Run the migrations.
    *
    * @return void
    */
    public function up()
    {
        Schema::create('params', function (Blueprint $table) {
            $table->increments('id');
            $table->string('param_name');
            $table->integer('value')->comment('ここのパラメータはすべて数値');
            $table->string('description');
            //$table->timestamps();
        });
    }

    /**
    * Reverse the migrations.
    *
    * @return void
    */
    public function down()
    {
        Schema::dropIfExists('params');
    }
}
