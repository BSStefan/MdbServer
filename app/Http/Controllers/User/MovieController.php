<?php

namespace App\Http\Controllers\User;

use App\Http\Response\JsonResponse;
use App\Repositories\GenreRepository;
use App\Repositories\KeywordRepository;
use App\Repositories\MovieRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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

    public function getMovie($id)
    {
        $movie = $this->movieRepository->findMovie(1);
        //User autentifikacija, ako je user onda porveri rekakciju ako nije onda nis
        $userReaction = $this->movieRepository->findUserReaction($id,1);
        return response()->json(new JsonResponse([
            'movie' => $movie,
            'user_reaction' => $userReaction
        ]));
    }

    public function getMoviePerGenre(Request $request)
    {
        $movies = $this->genreRepository->getMovies($request->genre_id);
        $formattedMovies = $this->formatMovieOptions($movies);

        return response()->json(new JsonResponse($formattedMovies));
    }

    public function getMoviePerKeyword(Request $request)
    {
        $movies = $this->keywordRepository->getMovies($request->keyword_id);
        $formattedMovies = $this->formatMovieOptions($movies);

        return response()->json(new JsonResponse($formattedMovies));
    }

    public function getNewMovies()
    {
        $movies = $this->movieRepository->getNewMovies();
        $formattedMovies = $this->formatMovieOptions($movies);

        return response()->json(new JsonResponse($formattedMovies));
    }

    private function formatMovieOptions($movies)
    {
        $formattedMovies = [];
        foreach($movies as $movie){
            $userReaction                  = $this->movieRepository->findUserReaction($movie['id'], 1);
            $formattedMovies[$movie['id']] = [
                'movie'         => array_merge($movie, $this->movieRepository->findNumberOfLikesDislikes($movie['id'])),
                'user_reaction' => $userReaction
            ];
        }

        return $formattedMovies;
    }
}
