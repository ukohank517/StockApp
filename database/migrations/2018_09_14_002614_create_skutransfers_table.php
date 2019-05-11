<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSkutransfersTable extends Migration
{
    /**
    * Run the migrations.
    *
    * @return void
    */
    public function up()
    {
        Schema::create('skutransfers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('asin')->nullable();
            $table->string('before');
            $table->string('after')->nullable();
        });
    }

    /**
    * Reverse the migrations.
    *
    * @return void
    */
    public function down()
    {
        Schema::dropIfExists('skutransfers');
    }
}
