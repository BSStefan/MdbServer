<?php

namespace App\Repositories;

use App\Models\Actor;
use App\Repositories\Eloquent\Repository;
use Illuminate\Container\Container as App;

class ActorRepository extends Repository
{
    protected $modelClass = Actor::class;

}