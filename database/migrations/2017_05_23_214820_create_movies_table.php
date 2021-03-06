<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMoviesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tmdb_id')->unsigned()->unique();
            $table->integer('director_id')->unsigned();
            $table->string('title',255);
            $table->string('original_title',255);
            $table->double('budget');
            $table->string('homepage',255);
            $table->string('description', 10000);
            $table->string('language',4);
            $table->string('tag_line',255);
            $table->date('release_day');
            $table->integer('runtime');
            $table->string('image_url',255);
            $table->boolean('in_cinema')->default(false);
            $table->timestamps();
            $table->foreign('director_id')->references('id')->on('directors');
            $table->index('title');
            $table->index('tmdb_id');
            $table->index('in_cinema');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movie_cinemas');
        Schema::dropIfExists('watched_movies');
        Schema::dropIfExists('like_dislikes');
        Schema::dropIfExists('similar_movies');
        Schema::dropIfExists('movies');
    }
}
