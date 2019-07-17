<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArchitectRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('architect_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('address');
            $table->string('name');
            $table->text('company_name');
            $table->mediumText('description');
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->integer('architectId');
            $table->integer('workerId');
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
        Schema::dropIfExists('architect_requests');
    }
}
