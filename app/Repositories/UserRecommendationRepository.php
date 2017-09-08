<?php

namespace App\Repositories;

use App\Models\UserRecommendation;
use App\Repositories\Eloquent\Repository;
use Carbon\Carbon;

class UserRecommendationRepository extends Repository
{
    protected $modelClass = UserRecommendation::class;

    public function saveNewRecommendation($user, $recommendation, $lastMovieId, $userRecommendationId = 0, $updatedAll = false)
    {
        $recommendationString = '';
        foreach($recommendation as $movieId => $mark) {
            $recommendationString .= $movieId.'-'.$mark.'/';
        }
        $recommendationString = substr($recommendationString,0,-1);
        if($updatedAll) {
            return $this->save([
                'user_id' => $user,
                'movies' => $recommendationString,
                'last_movie_calculated' => $lastMovieId
            ], $userRecommendationId);
        }
        return $this->save([
            'user_id' => $user,
            'movies' => $recommendationString,
            'last_movie_calculated' => $lastMovieId,
            'last_updated' => Carbon::now()->toDateTimeString()
        ], $userRecommendationId);
    }

    public function updateRecommendation($recommendationId, $newRecommendation)
    {
        $recommendationString = '';
        foreach($newRecommendation as $movieId => $mark) {
            $recommendationString .= $movieId.'-'.$mark.'/';
        }
        $recommendationString = substr($recommendationString,0,-1);

        return $this->save([
            'movies' => $recommendationString,
        ], $recommendationId);
    }

    public function updateAllRecommendation($recommendationId, $newRecommendation)
    {

    }
}