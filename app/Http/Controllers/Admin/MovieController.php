<?php

namespace App\Http\Controllers\Admin;

use App\Http\Response\JsonResponse;
use App\Models\Director;
use App\Models\Movie;
use App\Models\Writer;
use App\Repositories\ActorRepository;
use App\Repositories\Admin\CrawlerRepository;
use App\Repositories\Admin\TmdbRepository;
use App\Repositories\DirectorRepository;
use App\Repositories\GenreRepository;
use App\Repositories\KeywordRepository;
use App\Repositories\MovieRepository;
use App\Repositories\WriterRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tmdb\Client;
use Tmdb\Repository\SearchRepository;
use App\Repositories\Eloquent\Repository;

class MovieController extends Controller
{
    /**
     * @var TmdbRepository $tmdbRepository
     */
    private $tmdbRepository;

    /**
     *@var MovieRepository $movieRepository
     */
    private $movieRepository;

    /**
     * @var DirectorRepository $directorRepository
     */
    private $directorRepository;
    private $actorRepository;
    private $writerRepository;
    private $genreRepository;
    private $keywordRepository;
    private $crawlerRepository;

    public function __construct(
        TmdbRepository $tmdbRepository,
        MovieRepository $movieRepository,
        DirectorRepository $directorRepository,
        ActorRepository $actorRepository,
        WriterRepository $writerRepository,
        GenreRepository $genreRepository,
        KeywordRepository $keywordRepository,
        CrawlerRepository $crawlerRepository
    )
    {
        $this->tmdbRepository     = $tmdbRepository;
        $this->movieRepository    = $movieRepository;
        $this->directorRepository = $directorRepository;
        $this->actorRepository    = $actorRepository;
        $this->writerRepository   = $writerRepository;
        $this->genreRepository    = $genreRepository;
        $this->keywordRepository  = $keywordRepository;
        $this->crawlerRepository  = $crawlerRepository;
    }

    /**
     * Save movie from tmdb id
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function postMovieFromTmdb($id)
    {
        try{
            $movie = $this->movieRepository->findBy('tmdb_id', $id);
            return response()->json(new JsonResponse([
                'movie' => $movie->title,
                'success' => false
            ],'Movie already exists',400),400);
        }
        catch(ModelNotFoundException $e){}

        $movie = $this->tmdbRepository->getMovie($id);

        if($this->saveMovieFromTmdb($movie)){
            return response()->json(new JsonResponse([
                'movie' => $movie['movie']['title'],
                'success' => true
            ], 'Movie successfully saved', 200));
        }
    }

    /**
     * Get popular movie from tmdb and show them
     *
     * @param int $page
     *
     * @return JsonResponse
     */
    public function getTopMoviesFromTmdb($page)
    {
        return response()->json(new JsonResponse($this->tmdbRepository->getPopularMovies($page)));
    }

    /**
     * Save top movies
     *
     * @param int $page
     *
     * @return JsonResponse
     */
    public function postTopMoviesFromTmdb($page)
    {
        $popularMovies = $this->tmdbRepository->getPopularMovies($page);

        $response = $this->saveMultipleMovies($popularMovies);

        return response()->json(new JsonResponse($response));
    }

    /**
     * Get new movie from tmdb and show them
     *
     * @param int $page
     *
     * @return JsonResponse
     */
    public function getNewestFromTmdb($page)
    {
        return response()->json(new JsonResponse($this->tmdbRepository->getNowPlayingMovies($page)));
    }

    /**
     * Find new movies and save them
     *
     * @param int $page
     *
     * @return JsonResponse
     */
    public function postNewestFromTmdb($page)
    {
        $newMovies = $this->tmdbRepository->getNowPlayingMovies($page);

        $response = $this->saveMultipleMovies($newMovies);

        return response()->json(new JsonResponse($response));
    }

    /**
     * Get upcoming movie from tmdb and show them
     *
     * @param int $page
     *
     * @return JsonResponse
     */
    public function getUpcomingFromTmdb($page)
    {
        return response()->json(new JsonResponse($this->tmdbRepository->getUpcomingMovies($page)));
    }

    /**
     * Find upcoming movies and save them
     *
     * @param int $page
     *
     * @return JsonResponse
     */
    public function postUpcomingFromTmdb($page)
    {
        $upcomingMovies = $this->tmdbRepository->getUpcomingMovies($page);

        $response = $this->saveMultipleMovies($upcomingMovies);

        return response()->json(new JsonResponse($response));
    }

    public function findCurrentMoviesInCinema(SearchRepository $searchRepository)
    {
        $this->movieRepository->restartCurrentInCinema();

        $movies = $this->crawlerRepository->findTitles('http://www.cineplexx.rs/filmovi/u-bioskopu');

        foreach($movies as $movie) {
            try{
                $movieModel = $this->movieRepository->findBy('title', $movie);
            }
            catch(ModelNotFoundException $e){
                $id = $this->tmdbRepository->findByName($movie, Carbon::today()->year, $searchRepository);
                $movieModel = $this->getMovieFromTmdb($id)['movie'];
            }
            $movieModel->in_cinema = true;

            $this->movieRepository->save($movieModel->getAttributes(), $movieModel->id);
        }
    }

    /**
     * Method for saving multiple movies from array
     *
     * @param array $movies
     *
     * @return array
     */
    private function saveMultipleMovies($movies)
    {
        $response  = [];
        foreach($movies as $movie){
            try{
                $movieModel = $this->movieRepository->findBy('tmdb_id', $movie['movie']['tmdb_id']);
            }
            catch(ModelNotFoundException $e){
                $movieModel = null;
            }
            if(!$movieModel){
                $movieTmdb = $this->tmdbRepository->getMovie($movie['movie']['tmdb_id']);
                if($movieModel = $this->saveMovieFromTmdb($movieTmdb)){
                    $response[$movieModel->title] = true;
                }
                else{
                    $response[$movie['movie']['title']] = false;
                }
                //TODO obrisati
                return $response;
            }
            else{
                $response[$movieModel->title] = 'Already exists';
            }
        }

        return $response;
    }

    /**
     * Save movie
     *
     * @param array $movie
     *
     * @return Movie
     */
    private function saveMovieFromTmdb($movie)
    {
        $genres   = [];
        $keywords = [];
        $cast     = [];
        $writers  = [];

        $movie['movie']['director_id'] = $this->checkPersonAndSave($movie['crew']['director'][0], 'director', $this->directorRepository)->id;
        $movie['movie']['image_url']   = $this->saveImageFromUrl($movie['movie']['image_url'], 'images/movies');
        $movieModel                    = $this->movieRepository->save($movie['movie']);

        foreach($movie['genres'] as $genre){
            array_push($genres, $this->genreRepository->findBy('name', $genre)->id);
        }
        $movieModel->genres()->attach($genres);

        foreach($movie['keywords'] as $word){
            try{
                $wordModel = $this->keywordRepository->findBy('word', $word);
            }
            catch(ModelNotFoundException $e){
                $wordModel = null;
            }
            if(!$wordModel){
                $wordModel = $this->keywordRepository->save(['word' => $word]);
            }
            array_push($keywords, $wordModel->id);
        }
        $movieModel->keywords()->attach($keywords);

        foreach($movie['cast'] as $actor){
            $actorModel = $this->checkPersonAndSave($actor[0], 'actor', $this->actorRepository);
            array_push($cast, $actorModel->id);
        }
        $movieModel->actors()->attach($cast);

        foreach($movie['crew']['writers'] as $writer){
            $writerModel = $this->checkPersonAndSave($writer[0], 'writer', $this->writerRepository);
            array_push($writers, $writerModel->id);
        }
        $movieModel->writers()->attach($writers);

        return $movieModel;
    }

    /**
     * Check if person exists, if not find and save
     *
     * @param int $id
     * @param string $role
     * @param Repository $repository
     *
     * @return mixed
     */
    private function checkPersonAndSave($id, $role, $repository)
    {
        try{
            $person = $repository->findBy('tmdb_id', $id);
        }
        catch(ModelNotFoundException $e){
            $person = null;
        }
        if(!$person){
            $person         = $this->tmdbRepository->getPerson($id);
            $person['role'] = $role;
            $person         = $this->savePersonPerRole($person);
        }

        return $person;
    }

    /**
     * Save person per role
     *
     * @param mixed $person
     *
     * @return mixed
     */
    private function savePersonPerRole($person)
    {
        switch($person['role']){
            case 'actor':
                $person['image_url'] = $person['image_url'] ?
                    $this->saveImageFromUrl($person['image_url'], 'images/actors') : 'No image';
                unset($person['role']);

                return $this->actorRepository->save($person);
            case 'director':
                $person['image_url'] = $person['image_url'] ?
                    $this->saveImageFromUrl($person['image_url'], 'images/directors') : 'No image';
                unset($person['role']);

                return $this->directorRepository->save($person);
            case 'writer':
                $person['image_url'] = $person['image_url'] ?
                    $this->saveImageFromUrl($person['image_url'], 'images/writers') : 'No image';
                unset($person['role']);

                return $this->writerRepository->save($person);
            default:
                return null;
        }
    }
}
