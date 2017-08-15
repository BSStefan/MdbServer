<?php

namespace App\Repositories;

use App\Models\Movie;
use App\Repositories\Eloquent\Repository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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

    public function restartCurrentInCinema()
    {
        $currentInCinema = $this->findWhere('in_cinema', true);
        foreach($currentInCinema as $movie) {
            $this->save(['in_cinema' => false], $movie->id);
        }

    }

}