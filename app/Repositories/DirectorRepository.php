<?php

namespace App\Repositories;

use App\Models\Director;
use App\Repositories\Eloquent\Repository;

class DirectorRepository extends Repository
{
    protected $modelClass = Director::class;

    public function getDirectorWithDetails($directorId)
    {
        $director = $this->find($directorId);
        $director->movies;

        return ['director' => $director];
    }
}