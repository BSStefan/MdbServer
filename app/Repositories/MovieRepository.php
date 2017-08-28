<?php

namespace App\Repositories;

use App\Models\Movie;
use App\Repositories\Eloquent\Repository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MovieRepository extends Repository
{
    protected $modelClass = Movie::class;

    public function findCurrentInCinema()
    {
        $movies = $this->findWhere('in_cinema', true);
        $moviesTitleIdArray = [];

        foreach($movies as $movie) {
            $moviesTitleIdArray[$movie->title] = $movie->id;
        }

        return $moviesTitleIdArray;
    }

    public function restartCurrentInCinema()
    {
        $currentInCinema = $this->findWhere('in_cinema', true);
        foreach($currentInCinema as $movie) {
            $this->save(['in_cinema' => false], $movie->id);
        }
    }

    public function findMovie($id)
    {
        $movie = $this->find($id);

        $actorsModels   = $movie->actors;
        $directorModel  = $movie->director;
        $writersModels  = $movie->writers;
        $genresModels   = $movie->genres;
        $keywordsModels = $movie->keywords;
        $likeCounted    = count($movie->like);
        $dislikeCounted = count($movie->dislike);
        $actors         = [];
        $genres         = [];
        $writers        = [];
        $keywords       = [];

        foreach($actorsModels as $actor){
            $actors[$actor->id] = $actor->name;
        }
        foreach($writersModels as $writer){
            $writers[$writer->id] = $writer->name;
        }
        foreach($genresModels as $genre){
            $genres[$genre->id] = $genre->name;
        }
        foreach($keywordsModels as $keyword){
            $keywords[$keyword->id] = $keyword->word;
        }
        $director[$directorModel->id] = $directorModel->name;

        return [
          'details' => $movie->getAttributes(),
          'director'  => $director,
          'actors' => $actors,
          'writers' => $writers,
          'genres' => $genres,
          'keywords' => $keywords,
          'likes' => $likeCounted,
          'dislikes' => $dislikeCounted
        ];
    }

    public function findNumberOfLikesDislikes($id)
    {
        $movie = $this->find($id);
        return [
            'likes' => count($movie->like),
            'dislike' => count($movie->dislike)
        ];
    }

    public function findUserReaction($movieId, $userId)
    {
        $userLiked       = $this->checkIfUserReactMovie($movieId, 1, 'like') ? true : false;
        $userDisliked    = $this->checkIfUserReactMovie($movieId, 1, 'dislike') ? true : false;
        $userWatched     = $this->checkIfUserWatchedMovie($movieId, $userId, 'already_watched') ? true : false;
        $userInWatchList = $this->checkIfUserWatchedMovie($movieId, $userId, 'to_be_watched') ? true : false;

        return [
            'liked'     => $userLiked,
            'disliked'  => $userDisliked,
            'watched'   => $userWatched,
            'watchlist' => $userInWatchList
        ];
    }

    public function checkIfUserReactMovie($movieId, $userId, $reaction)
    {
        $is_like = $reaction == 'like' ? true : false;
        return $this->model->select('like_dislikes.id')
            ->join('like_dislikes', 'movies.id', '=', 'like_dislikes.movie_id')
            ->where('like_dislikes.movie_id', $movieId)
            ->where('like_dislikes.user_id', $userId)
            ->where('like_dislikes.is_like', $is_like)
            ->first();
    }

    public function checkIfUserWatchedMovie($movieId, $userId, $watchOrToBeWatched)
    {
        return $this->model->select('watched_movies.id')
            ->join('watched_movies', 'movies.id', '=', 'watched_movies.movie_id')
            ->where('watched_movies.movie_id', $movieId)
            ->where('watched_movies.user_id', $userId)
            ->where('watched_movies.' . $watchOrToBeWatched, true)
            ->first();
    }

    public function getNewMovies()
    {
        $movies = $this->model
            ->orderBy('release_day', 'DESC')
            ->limit(12)
            ->get();

        $moviesFormatted = [];
        foreach($movies as $movie){
            $moviesFormatted[$movie->id] = $movie->getAttributes();
        }

        return $moviesFormatted;
    }
}