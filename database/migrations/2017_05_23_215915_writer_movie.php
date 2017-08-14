<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class WriterMovie extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movie_writer', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('writer_id')->unsigned();
            $table->integer('movie_id')->unsigned();
            $table->timestamps();
            $table->foreign('writer_id')->references('id')->on('writers');
            $table->foreign('movie_id')->references('id')->on('movies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movie_writer');
    }
}
