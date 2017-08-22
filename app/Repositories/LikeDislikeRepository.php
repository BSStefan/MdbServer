<?php

namespace App\Repositories;

use App\Models\LikeDislike;
use App\Repositories\Eloquent\Repository;

class LikeDislikeRepository extends Repository
{
    protected $modelClass = LikeDislike::class;

    public function checkIfUserAlreadyLikedDislikedMovie($userId, $movieId)
    {
        return $this->model->where('user_id', $userId)
            ->where('movie_id', $movieId)
            ->firstOrFail();
    }

}