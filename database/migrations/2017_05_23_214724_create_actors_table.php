<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actors', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tmdb_id')->unsigned();
            $table->string('name', 255);
            $table->string('place_of_birth', 255)->nullable();
            $table->string('biography',10000);
            $table->date('birthday')->nullable();
            $table->date('dead_day')->nullable();
            $table->enum('gender',['male','female'])->default('male');
            $table->string('image_url',255);
            $table->timestamps();
            $table->index('name');
            $table->index('tmdb_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('actor_movie');
        Schema::dropIfExists('actors');
    }
}
