<?php

namespace App\Repositories;


use App\Models\Genre;
use App\Repositories\Eloquent\Repository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GenreRepository extends Repository
{
    protected $modelClass = Genre::class;

    public function saveAllGenres(array $genres)
    {
        $data = [];

        foreach($genres as $genre){
            array_push($data, [
                'name'       => $genre,
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString()
            ]);
        }

        if(Genre::insert($data)){
            return true;
        }

        return false;
    }

    public function getMovies($genreId)
    {
        $genre = $this->find($genreId);
        $movies = $genre->movies;
        $moviesFormatted = [];
        foreach($movies as $movie){
            $moviesFormatted[$movie->id] = $movie->getAttributes();
        }

        return $moviesFormatted;
    }
}