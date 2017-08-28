<?php

namespace App\Repositories;


use App\Models\Keyword;
use App\Repositories\Eloquent\Repository;

class KeywordRepository extends Repository
{
    protected $modelClass = Keyword::class;

    public function getMovies($keywordId)
    {
        $keyword = $this->find($keywordId);
        $movies = $keyword->movies;
        $moviesFormatted = [];
        foreach($movies as $movie){
            $moviesFormatted[$movie->id] = $movie->getAttributes();
        }

        return $moviesFormatted;
    }
}