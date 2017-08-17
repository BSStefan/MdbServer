<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Eloquent\Repository;

class UserRepository extends Repository
{
    protected $modelClass = User::class;
}