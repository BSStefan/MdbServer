<?php

namespace App\Http\Controllers\User;

use App\Http\Response\JsonResponse;
use App\Repositories\GenreRepository;
use App\Repositories\KeywordRepository;
use App\Repositories\MovieRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;

class MovieController extends Controller
{
    private $movieRepository;
    private $genreRepository;
    private $keywordRepository;

    public function __construct(
        MovieRepository $movieRepository,
        GenreRepository $genreRepository,
        KeywordRepository $keywordRepository
    )
    {
        $this->movieRepository = $movieRepository;
        $this->genreRepository = $genreRepository;
        $this->keywordRepository = $keywordRepository;
    }

    public function getMovie(Request $request)
    {
        $movie = $this->movieRepository->findMovie($request->get('movie_id'));
        $user  = JWTAuth::user();
        if(!$user->is_admin){
            $userReaction = $this->movieRepository->findUserReaction($request->get('movie_id'), $user->id);
            return response()->json(new JsonResponse([
                'movie' => $movie,
                'user_reaction' => $userReaction
            ]));
        }
        return response()->json(new JsonResponse([
            'movie' => null
        ]));
    }

    public function getMoviePerGenre(Request $request)
    {
        $user  = JWTAuth::user();
        $movies = $this->genreRepository->getMovies($request->get('genre_id'));
        $formattedMovies = $this->formatMovieOptions($movies, $user->id);

        return response()->json(new JsonResponse($formattedMovies));
    }

    public function getMoviePerKeyword(Request $request)
    {
        $user  = JWTAuth::user();
        $movies = $this->keywordRepository->getMovies($request->keyword_id);
        $formattedMovies = $this->formatMovieOptions($movies, $user->id);

        return response()->json(new JsonResponse($formattedMovies));
    }

    public function getNewMovies($perPage)
    {
        $user  = JWTAuth::user();
        $response = $this->movieRepository->getNewMovies($perPage);
        $movies = $response[0];
        $formattedMovies = $this->formatMovieOptions($movies, $user->id);

        return response()->json(new JsonResponse(['movies' => $formattedMovies, 'paginator' => $response[1]]));
    }

    private function formatMovieOptions($movies, $userId)
    {
        $formattedMovies = [];
        foreach($movies as $movie){
            $userReaction                  = $this->movieRepository->findUserReaction($movie['id'], $userId);
            $formattedMovies[$movie['id']] = [
                'movie'         => array_merge($movie, $this->movieRepository->findNumberOfLikesDislikes($movie['id'])),
                'user_reaction' => $userReaction
            ];
        }

        return $formattedMovies;
    }
}
