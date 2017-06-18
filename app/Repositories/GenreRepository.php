<?php

namespace App\Repositories;


use App\Models\Genre;
use App\Repositories\Eloquent\Repository;

class GenreRepository extends Repository
{
    protected $modelClass = Genre::class;
}