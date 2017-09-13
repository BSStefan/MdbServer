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
            $moviesTitleIdArray[$movie->original_title] = $movie->id;
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
            array_push($actors, ['id' => $actor->id, 'name' => $actor->name]);
        }
        foreach($writersModels as $writer){
            array_push($writers, ['id' => $writer->id, 'name' => $writer->name]);
        }
        foreach($genresModels as $genre){
            array_push($genres, ['id' => $genre->id, 'name' => $genre->name]);
        }
        foreach($keywordsModels as $keyword){
            array_push($keywords, ['id' => $keyword->id, 'word' => $keyword->word]);
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
            'dislikes' => count($movie->dislike)
        ];
    }

    public function findUserReaction($movieId, $userId)
    {
        $userLiked       = $this->checkIfUserReactMovie($movieId, $userId, 'like') ? true : false;
        $userDisliked    = $this->checkIfUserReactMovie($movieId, $userId, 'dislike') ? true : false;
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
        return $this->model->select('watch_movies.id')
            ->join('watch_movies', 'movies.id', '=', 'watch_movies.movie_id')
            ->where('watch_movies.movie_id', $movieId)
            ->where('watch_movies.user_id', $userId)
            ->where('watch_movies.' . $watchOrToBeWatched, true)
            ->first();
    }

    public function getNewMovies($perPage)
    {
        $movies = $this->model
            ->orderBy('release_day', 'DESC')
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

    public function getCurrentInCinemaMovies($perPage)
    {
        $movies = $this->model
            ->where('in_cinema', '=', true)
            ->orderBy('release_day', 'DESC')
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

    public function searchMovie($title)
    {
        $movies = $this->model->select()
            ->where('title', 'LIKE', '%'.$title.'%')
            ->limit(5)
            ->get();

        $moviesFormated = [];
        foreach($movies as $movie){
            array_push($moviesFormated, $movie->title);
        }

        return $moviesFormated;
    }

    public function findRecommendation($perPage, $array)
    {
        return $this->model->whereIn('id', $array)->paginate($perPage);
    }
}