<?php


namespace App\Repositories;

use App\Models\MovieModel;
use App\Repositories\Eloquent\Repository;

class MovieModelRepository extends Repository
{
    protected $modelClass = MovieModel::class;
}