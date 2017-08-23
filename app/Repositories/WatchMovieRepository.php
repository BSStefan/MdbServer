<?php

namespace App\Repositories;

use App\Models\WatchMovie;
use App\Repositories\Eloquent\Repository;

class WatchMovieRepository extends Repository
{
    protected $modelClass = WatchMovie::class;
}