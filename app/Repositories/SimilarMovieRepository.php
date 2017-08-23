<?php

namespace App\Repositories;

use App\Models\SimilarMovie;
use App\Repositories\Eloquent\Repository;

class SimilarMovieRepository extends Repository
{
    protected $modelClass = SimilarMovie::class;
}