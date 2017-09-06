<?php

namespace App\Http\Controllers\User;

use App\Http\Response\JsonResponse;
use App\Repositories\Admin\TmdbRepository;
use App\Repositories\GenreRepository;
use App\Repositories\KeywordRepository;
use App\Repositories\LikeDislikeRepository;
use App\Repositories\MovieRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tmdb\Repository\SearchRepository;
use Tymon\JWTAuth\Facades\JWTAuth;

class MovieController extends Controller
{
    private $movieRepository;
    private $genreRepository;
    private $keywordRepository;
    private $likeDislikeRepository;

    public function __construct(
        MovieRepository $movieRepository,
        GenreRepository $genreRepository,
        KeywordRepository $keywordRepository,
        LikeDislikeRepository $likeDislikeRepository
    )
    {
        $this->movieRepository       = $movieRepository;
        $this->genreRepository       = $genreRepository;
        $this->keywordRepository     = $keywordRepository;
        $this->likeDislikeRepository = $likeDislikeRepository;
    }

    public function getMovie($id)
    {
        $movie = $this->movieRepository->findMovie($id);
        $user  = JWTAuth::user();
        $userReaction = $this->movieRepository->findUserReaction($id, $user->id);
        return response()->json(new JsonResponse([
            'movie' => $movie,
            'user_reaction' => $userReaction
        ]));
    }

    public function getMoviePerGenre($id, $perGenre)
    {
        $user  = JWTAuth::user();
        $response = $this->genreRepository->getMovies($id, $perGenre);
        $movies = $response[0];
        $formattedMovies = $this->formatMovieOptions($movies, $user->id);

        return response()->json(new JsonResponse(['movies' => $formattedMovies, 'paginator' => $response[1]]));
    }

    public function getMoviePerKeyword($id)
    {
        $user  = JWTAuth::user();
        $response = $this->keywordRepository->getMovies($id, 20);
        $movies = $response[0];
        $formattedMovies = $this->formatMovieOptions($movies, $user->id);

        return response()->json(new JsonResponse(['movies' => $formattedMovies, 'paginator' => $response[1]]));
    }

    public function getNewMovies($perPage)
    {
        $user  = JWTAuth::user();
        $response = $this->movieRepository->getNewMovies($perPage);
        $movies = $response[0];
        $formattedMovies = $this->formatMovieOptions($movies, $user->id);

        return response()->json(new JsonResponse(['movies' => $formattedMovies, 'paginator' => $response[1]]));
    }

    public function getCurrentInCinema($perPage)
    {
        $user  = JWTAuth::user();
        $response = $this->movieRepository->getCurrentInCinemaMovies($perPage);
        $movies = $response[0];
        $formattedMovies = $this->formatMovieOptions($movies, $user->id);

        return response()->json(new JsonResponse(['movies' => $formattedMovies, 'paginator' => $response[1]]));
    }

    public function getLikeDislikeMovies($type, $perPage)
    {
        $user  = JWTAuth::user();
        $response = $this->likeDislikeRepository->getLikesDislikes($user->id, $type, $perPage);
        $movies = $response[0];

        $formated = [];
        foreach($movies as $movie){
            array_push($formated, $movie);
        }

        return response()->json(new JsonResponse(['movies' => $formated,'paginator' => $response[1]]));
    }

    public function getMostLiked($perPage)
    {
        $user  = JWTAuth::user();
        $response = $this->likeDislikeRepository->getMostLiked($perPage);
        $movies = $response[0];

        $formattedMovies = $this->formatMovieOptions($movies, $user->id);

        return response()->json(new JsonResponse(['movies' => $formattedMovies,'paginator' => $response[1]]));
    }

    private function formatMovieOptions($movies, $userId)
    {
        $formattedMovies = [];
        foreach($movies as $movie){
            $userReaction                  = $this->movieRepository->findUserReaction($movie['id'], $userId);
            $formattedMovies[] = [
                'movie'         => array_merge($movie, $this->movieRepository->findNumberOfLikesDislikes($movie['id'])),
                'user_reaction' => $userReaction
            ];
        }

        return $formattedMovies;
    }

    public function getSearchMovie(Request $request, SearchRepository $searchRepository, TmdbRepository $tmdbRepository)
    {
        $this->validate($request,[
           'movie' => 'required|min:3'
        ]);

        $movies = $this->movieRepository->searchMovie($request->get('movie'));

        if($movies) {
            return response()->json(new JsonResponse($movies));
        }
        else {
            $titles = $tmdbRepository->findMoviesByName($request->get('movie'), null, $searchRepository);
            if($titles){
                return response()->json(new JsonResponse($titles));
            }
            else{
                return response()->json(new JsonResponse(['sucess' => false],'',400),400);
            }
        }
    }


}
