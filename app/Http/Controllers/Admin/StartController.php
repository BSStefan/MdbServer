<?php

namespace App\Http\Controllers\Admin;

use App\Http\Response\JsonResponse;
use App\Repositories\ActorRepository;
use App\Repositories\DirectorRepository;
use App\Repositories\GenreRepository;
use App\Repositories\KeywordRepository;
use App\Repositories\MovieRepository;
use App\Repositories\Admin\TmdbRepository;
use App\Repositories\WriterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use App\Repositories\UserRepository;

class StartController extends Controller
{
    private $tmdbRepository;
    private $actorRepository;
    private $directorRepository;
    private $writerRepository;
    private $genreRepository;
    private $movieRepository;
    private $keywordRepository;
    private $userRepository;

    public function __construct(
        TmdbRepository $tmdbRepository,
        ActorRepository $actorRepository,
        DirectorRepository $directorRepository,
        WriterRepository $writerRepository,
        GenreRepository $genreRepository,
        MovieRepository $movieRepository,
        KeywordRepository $keywordRepository,
        UserRepository $userRepository
    )
    {
        $this->tmdbRepository     = $tmdbRepository;
        $this->actorRepository    = $actorRepository;
        $this->directorRepository = $directorRepository;
        $this->writerRepository   = $writerRepository;
        $this->genreRepository    = $genreRepository;
        $this->movieRepository    = $movieRepository;
        $this->keywordRepository  = $keywordRepository;
        $this->userRepository     = $userRepository;
    }

    public function getInfo()
    {
        $movies    = $this->movieRepository->count();
        $actors    = $this->actorRepository->count();
        $writers   = $this->writerRepository->count();
        $directors = $this->directorRepository->count();
        $users     = $this->userRepository->count();

        return response()->json(new JsonResponse([
            'movies'    => $movies,
            'actors'    => $actors,
            'writers'   => $writers,
            'directors' => $directors,
            'users'     => $users
        ]));
    }

    public function saveImageFromUrl($url, $path)
    {
        $extension = pathinfo($url,PATHINFO_EXTENSION);
        $fullName =  $path . '/' . md5(microtime()) . '.' . $extension;
        Image::make($url)->save(public_path($fullName));
        return $fullName;
    }

    public function getTopImage($page)
    {
        $topMovies = $this->tmdbRepository->getPopularMovies($page);
        foreach ($topMovies as $movie) {
            $movie = $this->tmdbRepository->getMovie($movie['movie']['tmdb_id']);
            $this->saveImageFromUrl($movie['movie']['image_url'], 'frontimage2');
        }

    }

}
