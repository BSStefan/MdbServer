<?php

namespace App\Repositories;

use App\Models\WatchedMovie;
use App\Repositories\Eloquent\Repository;

class WatchedMovieRepository extends Repository
{
    protected $modelClass = WatchedMovie::class;
}