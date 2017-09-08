<?php

namespace App\Repositories;

use App\Models\LikeDislike;
use App\Repositories\Eloquent\Repository;
use Illuminate\Support\Facades\DB;

class LikeDislikeRepository extends Repository
{
    protected $modelClass = LikeDislike::class;

    public function checkIfUserAlreadyLikedDislikedMovie($userId, $movieId)
    {
        return $this->model->where('user_id', $userId)
            ->where('movie_id', $movieId)
            ->firstOrFail();
    }

    public function getLikesDislikes($userId, $type, $perPage)
    {
        $isLike = $type == 'like' ? true : false;
        $likesDislikesModel = $this->model
            ->select()
            ->where('user_id', '=', $userId)
            ->where('is_like', '=', $isLike)
            ->paginate($perPage);

        $paginator = [
            'previous_page' => $likesDislikesModel->previousPageUrl(),
            'next_page'  => $likesDislikesModel->nextPageUrl()
        ];
        $moviesFormatted = [];
        foreach($likesDislikesModel as $movie){
            $moviesFormatted[$movie->id] = $movie->movie->getAttributes();
        }

        return [$moviesFormatted, $paginator];
    }

    public function getMostLiked($perPage)
    {
        $movies = $this->model->select(DB::raw('count(like_dislikes.movie_id) as count'), 'movies.*')
            ->join('movies', 'movies.id', '=', 'like_dislikes.movie_id')
            ->where('like_dislikes.is_like', '=', true)
            ->groupBy('like_dislikes.movie_id')
            ->orderBy('count', 'desc')
            ->paginate($perPage);

        $paginator = [
            'previous_page' => $movies->previousPageUrl(),
            'next_page'  => $movies->nextPageUrl()
        ];
        $moviesFormatted = [];
        foreach($movies as $movie){
            $moviesFormatted[$movie->id] = $movie->getAttributes();
        }

        return [$moviesFormatted, $paginator];
    }

    public function getAllLikes($userId)
    {
        $movies = $this->model->select()
            ->where('user_id', '=', $userId)
            ->where('is_like', '=', true)
            ->get();

        $formatted = [];
        foreach($movies as $movie){
            array_push($formatted, $movie->movie_id);
        }
        return $formatted;
    }

}