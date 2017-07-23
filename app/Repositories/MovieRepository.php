<?php

namespace App\Repositories;

use App\Models\Movie;
use App\Repositories\Eloquent\Repository;

class MovieRepository extends Repository
{
    protected $modelClass = Movie::class;

    public function findCurrentInCinema()
    {
        $movies = $this->findBy('in_cinema', true);
        $moviesTitleIdArray = [];

        foreach($movies as $movie) {
            $moviesTitleIdArray[$movie->title] = $movie->id;
        }

        return $moviesTitleIdArray;
    }
}