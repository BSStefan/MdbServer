<?php

namespace App\Repositories;

use App\Models\Movie;
use App\Repositories\Eloquent\Repository;

class MovieRepository extends Repository
{
    protected $modelClass = Movie::class;
}