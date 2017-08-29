<?php

namespace App\Repositories;


use App\Models\Keyword;
use App\Repositories\Eloquent\Repository;

class KeywordRepository extends Repository
{
    protected $modelClass = Keyword::class;

    public function getMovies($keywordId, $perPage)
    {
        $keyword = $this->find($keywordId);
        $movies = $keyword->movies()->paginate($perPage);
        $paginator = [
            'previous_page' => $movies->previousPageUrl(),
            'next_page'  => $movies->nextPageUrl()
        ];
        $moviesFormatted = [];
        foreach($movies as $movie){
            $moviesFormatted[$movie->id] = $movie->getAttributes();
        }

        return [$moviesFormatted, $paginator];
    }
}