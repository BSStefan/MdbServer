<?php

namespace App\Repositories;


use App\Models\Keyword;
use App\Repositories\Eloquent\Repository;

class KeywordRepository extends Repository
{
    protected $modelClass = Keyword::class;
}