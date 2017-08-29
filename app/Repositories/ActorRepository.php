<?php

namespace App\Repositories;

use App\Models\Actor;
use App\Repositories\Eloquent\Repository;
use Illuminate\Container\Container as App;

class ActorRepository extends Repository
{
    protected $modelClass = Actor::class;

    public function getActorDetails($actorId)
    {
        $actor = $this->find($actorId);
        $actor->movies;

        return ['actor' => $actor];

    }

}