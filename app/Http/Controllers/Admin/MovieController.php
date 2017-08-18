<?php

namespace App\Http\Controllers\Admin;

use App\Http\Response\JsonResponse;
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

    public function getMovieFromTmdb($id)
    {
        try{
            $movie = $this->movieRepository->findBy('tmdb_id', $id);
            return response()->json(new JsonResponse([
                'movie' => $movie,
                'success' => false
            ],'Movie already exists',400),400);
        }
        catch(ModelNotFoundException $e){}
        $genres   = [];
        $keywords = [];
        $cast     = [];
        $writers  = [];

        $movie = $this->tmdbRepository->getMovie($id);

        $movie['movie']['director_id'] = $this->checkDirector($movie['crew']['director'][0])->id;
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
            $actorModel = $this->checkActor($actor[0]);
            array_push($cast, $actorModel->id);
        }
        $movieModel->actors()->attach($cast);

        foreach($movie['crew']['writers'] as $writer){
            $writerModel = $this->checkWriter($writer[0]);
            array_push($writers, $writerModel->id);
        }
        $movieModel->writers()->attach($writers);

        return response()->json(new JsonResponse([
            'movie' => $movie,
            'success' => true
        ], 'Movie successfully saved', 200));
    }

    protected function checkDirector($directorId)
    {
        try{
            $director = $this->directorRepository->findBy('tmdb_id', $directorId);
        }
        catch(ModelNotFoundException $e){
            $director = null;
        }
        if(!$director){
            $director         = $this->tmdbRepository->getPerson($directorId);
            $director['role'] = 'director';
            $director         = $this->savePersonPerRole($director);
        }

        return $director;
    }

    protected function checkActor($actorId)
    {
        try{
            $actor = $this->actorRepository->findBy('tmdb_id', $actorId);
        }
        catch(ModelNotFoundException $e){
            $actor = null;
        }
        if(!$actor){
            $actor         = $this->tmdbRepository->getPerson($actorId);
            $actor['role'] = 'actor';
            $actor         = $this->savePersonPerRole($actor);
        }

        return $actor;
    }

    protected function checkWriter($writerId)
    {
        try{
            $writer = $this->writerRepository->findBy('tmdb_id', $writerId);
        }
        catch(ModelNotFoundException $e){
            $writer = null;
        }
        if(!$writer){
            $writer         = $this->tmdbRepository->getPerson($writerId);
            $writer['role'] = 'writer';
            $writer         = $this->savePersonPerRole($writer);
        }

        return $writer;
    }

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

    public function getTopMoviesFromTmdb($page)
    {
        return $this->tmdbRepository->getPopularMovies($page);
    }

    public function saveTopMoviesFromTmdb($page)
    {
        $popularMovies = $this->tmdbRepository->getPopularMovies($page);
        $response = [];
        foreach($popularMovies as $movie) {
            array_push($response, $this->getMovieFromTmdb($movie['movie']['tmdb_id']));
        }

        return $response;
    }

    public function getNewestFromTmdb($page)
    {
        $newMovies = $this->tmdbRepository->getNowPlayingMovies($page);
        $response  = [];
        foreach($newMovies as $movie){
            array_push($response, $this->getMovieFromTmdb($movie['movie']['tmdb_id']));
        }

        return $response;
    }

    public function getUpcomingFromTmdb($page)
    {
        $upcomingMovies = $this->tmdbRepository->getUpcomingMovies($page);
        $response       = [];
        foreach($upcomingMovies as $movie){
            array_push($response, $this->getMovieFromTmdb($movie['movie']['tmdb_id']));
        }

        return $response;
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
}
