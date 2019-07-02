<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSystemMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_media', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('filename', 100)->index();
            $table->string('ext', 20)->default('')->index();
            $table->unsignedInteger('category_id')->default(0)->index();
            $table->string('path');
            $table->string('size')->default('0')->comment('单位 Byte');
            $table->string('mime_type', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_media');
    }
}
