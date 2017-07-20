<?php

namespace App\Repositories;

use App\Models\Cinema;
use App\Repositories\Eloquent\Repository;

class CinemaRepository extends Repository
{
    protected $modelClass = Cinema::class;
}