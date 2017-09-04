<?php

namespace App\Repositories;

use App\Models\WatchMovie;
use App\Repositories\Eloquent\Repository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class WatchMovieRepository extends Repository
{
    protected $modelClass = WatchMovie::class;

    public function addToList($movieId, $userId, $toBeWatched)
    {
        try{
            $movieWatchModel = $this->model->select()
                ->where('movie_id', '=', $movieId)
                ->where('user_id', '=', $userId)
                ->firstOrFail();
            if($toBeWatched){
                if(!$movieWatchModel->to_be_watched){
                    $movieWatchModel->to_be_watched    = true;
                    $movieWatchModel->already_watched = false;
                }
                else{
                    $movieWatchModel->to_be_watched    = false;
                    $movieWatchModel->already_watched = false;
                }
            }
            else{
                if(!$movieWatchModel->already_watched){
                    $movieWatchModel->to_be_watched    = false;
                    $movieWatchModel->already_watched = true;
                }
                else{
                    $movieWatchModel->already_watched = false;
                    $movieWatchModel->to_be_watched    = false;
                }
            }
            $movieWatchModel->save();
        }

        catch(ModelNotFoundException $e){
            $movieWatchModel = null;
        }

        if(!$movieWatchModel){
            $watched         = $toBeWatched ? false : true;
            $movieWatchModel = $this->save([
                'user_id'         => $userId,
                'movie_id'        => $movieId,
                'to_be_watched'   => $toBeWatched,
                'already_watched' => $watched
            ]);
        }

        return $movieWatchModel;
    }

    public function getMovies($userId, $field, $perPage)
    {
        $watchList = $this->model
            ->where('user_id', $userId)
            ->where($field, true)
            ->paginate($perPage);
        $paginator = [
            'previous_page' => $watchList->previousPageUrl(),
            'next_page'  => $watchList->nextPageUrl()
        ];
        $movies = [];
        foreach($watchList as $one){
            $movie = $one->movie;
            $movies[$movie->id] = $movie->getAttributes();
        }

        return [$movies, $paginator];
    }
}
