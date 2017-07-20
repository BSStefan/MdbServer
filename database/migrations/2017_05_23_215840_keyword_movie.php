<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class KeywordMovie extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('keyword_movie', function (Blueprint $table) {
           $table->increments('id');
           $table->integer('keyword_id')->unsigned();
           $table->integer('movie_id')->unsigned();
            $table->timestamp('created_at');
           $table->foreign('keyword_id')->references('id')->on('keywords');
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
        Schema::dropIfExists('keyword_movie');
    }
}
