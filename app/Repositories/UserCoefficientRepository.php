<?php

namespace App\Repositories;

use App\Models\UserCoefficient;
use App\Repositories\Eloquent\Repository;

class UserCoefficientRepository extends Repository
{
    protected $modelClass = UserCoefficient::class;

    //TODO promeni koeficijente
    public function setToDefault($userId)
    {
        return $this->save([
            'user_id'  => $userId,
            'director' => 1,
            'actors'   => 1,
            'writers'  => 1,
            'genres'   => 1,
            'keywords' => 1
        ]);
    }
}