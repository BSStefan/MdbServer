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
        if(!$user->is_admin){
            $userReaction = $this->movieRepository->findUserReaction($id, $user->id);
            return response()->json(new JsonResponse([
                'movie' => $movie,
                'user_reaction' => $userReaction
            ]));
        }
        return response()->json(new JsonResponse([
            'movie' => null
        ]));
    }

    public function getMoviePerGenre($id)
    {
        $user  = JWTAuth::user();
        $response = $this->genreRepository->getMovies($id, 20);
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

    public function getLikeDislikeMovies($type)
    {
        $user  = JWTAuth::user();
        $response = $this->likeDislikeRepository->getLikesDislikes($user->id, $type, 20);
        $movies = $response[0];

        $formattedMovies = $this->formatMovieOptions($movies, $user->id);

        return response()->json(new JsonResponse(['movies' => $formattedMovies,'paginator' => $response[1]]));
    }

    public function getMostLiked()
    {
        $user  = JWTAuth::user();
        $response = $this->likeDislikeRepository->getMostLiked(20);
        $movies = $response[0];

        $formattedMovies = $this->formatMovieOptions($movies, $user->id);

        return response()->json(new JsonResponse(['movies' => $formattedMovies,'paginator' => $response[1]]));
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
            $ids = $tmdbRepository->findMoviesByName($request->get('movie'), null, $searchRepository);
            $moviesTmdb = [];
            if($ids){
                foreach($ids as $id){
                    $movieTmdb = $tmdbRepository->getMovie($id);
                    array_push($moviesTmdb, $movieTmdb['movie']['title']);
                }
                return response()->json(new JsonResponse($moviesTmdb));
            }
            else{
                return response()->json(new JsonResponse(['sucess' => false],'',400),400);
            }
        }
    }


}
