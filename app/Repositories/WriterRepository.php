<?php

namespace App\Repositories;


use App\Models\Writer;
use App\Repositories\Eloquent\Repository;

class WriterRepository extends Repository
{
    protected $modelClass = Writer::class;

}