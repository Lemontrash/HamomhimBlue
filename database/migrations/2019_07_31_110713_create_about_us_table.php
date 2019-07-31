<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAboutUsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('about_us', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('title');
            $table->text('subtitle');
            $table->text('text');
            $table->text('video');
            $table->text('title_second');
            $table->text('textSecond');
            $table->text('title_on_blue');
            $table->text('text_on_blue');
            $table->text('image_on_blue');
            $table->integer('coworkers');
            $table->integer('architects');
            $table->integer('workers');
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
        Schema::dropIfExists('about_us');
    }
}
