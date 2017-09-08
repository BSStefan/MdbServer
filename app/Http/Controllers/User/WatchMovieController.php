<?php

namespace App\Http\Controllers\User;

use App\Http\Response\JsonResponse;
use App\Repositories\MovieRepository;
use App\Repositories\UserRecommendationRepository;
use App\Repositories\WatchMovieRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;

class WatchMovieController extends Controller
{
    private $movieRepository;
    private $watchMovieRepository;
    private $userRecommendationRepository;

    public function __construct(
        MovieRepository $movieRepository,
        WatchMovieRepository $watchMovieRepository,
        UserRecommendationRepository $userRecommendationRepository
    )
    {
        $this->movieRepository      = $movieRepository;
        $this->watchMovieRepository = $watchMovieRepository;
        $this->userRecommendationRepository = $userRecommendationRepository;
    }

    public function getMovies($type, $perPage)
    {
        $user = JWTAuth::user();
        if($type == 'watchlist') {
            $response = $this->watchMovieRepository->getMovies($user->id, 'to_be_watched', $perPage);
        }
        else if($type == 'already'){
            $response = $this->watchMovieRepository->getMovies($user->id, 'already_watched', $perPage);
        }
        $formated = [];
        foreach($response[0] as $movie){
            array_push($formated, $movie);
        }

        return response()->json(new JsonResponse(['movies' => $formated, 'paginator' => $response[1]]));
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
        if($watchMovie && $watchMovie->already_watched){
            $this->deleteMovieFromRecommendations($watchMovie->movie_id, $user);
        }
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

    private function deleteMovieFromRecommendations($id, $user)
    {
        $userRecommendation = $this->userRecommendationRepository->findBy('user_id', $user->id);
        $movies = $userRecommendation->movies;
        if(isset($movies[$id])){
            unset($movies[$id]);
        }

        return $this->userRecommendationRepository->updateRecommendation($userRecommendation->id, $movies);
    }
}
