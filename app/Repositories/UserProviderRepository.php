<?php

namespace App\Repositories;

use App\Models\UserProvider;
use App\Repositories\Eloquent\Repository;

class UserProviderRepository extends Repository
{
    protected $modelClass = UserProvider::class;
}