<?php

namespace App\Http\Controllers\User;

use App\Http\Response\JsonResponse;
use App\Repositories\MovieRepository;
use App\Repositories\WatchMovieRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;

class WatchMovieController extends Controller
{
    private $movieRepository;
    private $watchMovieRepository;

    public function __construct(
        MovieRepository $movieRepository,
        WatchMovieRepository $watchMovieRepository
    )
    {
        $this->movieRepository      = $movieRepository;
        $this->watchMovieRepository = $watchMovieRepository;
    }

    public function getMovies($type)
    {
        $user = JWTAuth::user();
        $movies = [];
        if($type == 'watchlist') {
            $movies = $this->watchMovieRepository->getMovies($user->id, 'to_be_watched');
        }
        else if($type == 'already'){
            $movies = $this->watchMovieRepository->getMovies($user->id, 'already_watched');
        }

        return response()->json(new JsonResponse(['movies' => $movies]));
    }

    public function addMovie(Request $request)
    {
        $this->validate($request, [
           'movie_id' => 'required|integer',
           'to_be_watched' => 'required|boolean'
        ]);
        $user = JWTAuth::user();
        $movie = $this->movieRepository->find($request->input('movie_id'));
        $watchMovie = $this->watchMovieRepository->addToList($movie->id, $user->id, $request->input('to_be_watched'));
        if($watchMovie){
            return response()->json(new JsonResponse([
                'success' => true,
                'movie_id' => $movie->id,
                'to_be_watched' => $watchMovie->to_be_watched,
                'already_watched' => $watchMovie->already_watched
            ]));
        }
        return response()->json(new JsonResponse([
            'success' => false,
            'movie_id' => $movie->id,
            'to_be_watched' => null,
            'already_watched' => null
        ], '', 400), 400);
    }
}
