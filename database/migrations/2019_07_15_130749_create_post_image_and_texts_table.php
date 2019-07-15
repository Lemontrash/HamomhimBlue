<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostImageAndTextsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_image_and_texts', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->string('postId');
            $table->longText('content');
            $table->mediumText('image');
            $table->enum('imagePosition', ['left', 'right']);
            $table->integer('order');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_image_and_texts');
    }
}
