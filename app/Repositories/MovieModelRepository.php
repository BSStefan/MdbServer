<?php


namespace App\Repositories;

use App\Models\MovieModel;
use App\Repositories\Eloquent\Repository;

class MovieModelRepository extends Repository
{
    protected $modelClass = MovieModel::class;

    public function getAllOthers($id)
    {
        return $this->model->where('movie_id', '!=', $id)->get();
    }

    public function getNotInArray(array $ids)
    {
        return $this->model->whereNotIn('id', $ids)->get();
    }

    public function findLast()
    {
        return $this->model->select()->orderBy('id', 'desc')->limit(1)->first();
    }

    public function getOthersExceptLikedAndDislikedWatched($likedDisliked, $watched)
    {
        $likedDislikedArray = [];
        foreach($likedDisliked as $one) {
            array_push($likedDislikedArray, $one->movie_id);
        }
        foreach($watched as $one) {
            if(!in_array($one->movie_id, $likedDislikedArray)){
                array_push($likedDislikedArray, $one->movie_id);
            }
        }

        return $this->getNotInArray($likedDislikedArray);
    }

    public function findInArray($ids)
    {
        return $this->model->whereIn('movie_id', $ids)->get();
    }
}