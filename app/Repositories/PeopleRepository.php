<?php

namespace App\Repositories;

use App\Repositories\Eloquent\Repository;

abstract class PeopleRepository extends Repository
{
    protected $tmdbRepository;

    public function __construct(TmdbRepository $tmdbRepository)
    {
        $this->tmdbRepository = $tmdbRepository;
    }

    public function getPopularPeople($page = 1)
    {
        return $this->tmdbRepository->getPopularPeople($page);
    }

}