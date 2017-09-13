<?php

namespace App\Repositories;

use App\Models\UserCoefficient;
use App\Repositories\Eloquent\Repository;

class UserCoefficientRepository extends Repository
{
    protected $modelClass = UserCoefficient::class;

    public function setToDefault($userId)
    {
        return $this->save([
            'user_id'  => $userId,
            'director' => 2,
            'actors'   => 3,
            'writers'  => 1,
            'genres'   => 1,
            'keywords' => 1
        ]);
    }
}