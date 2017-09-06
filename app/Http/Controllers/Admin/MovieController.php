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
use App\Repositories\LikeDislikeRepository;
use App\Repositories\MovieModelRepository;
use App\Repositories\MovieRepository;
use App\Repositories\WriterRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tmdb\Client;
use Tmdb\Repository\SearchRepository;
use App\Repositories\Eloquent\Repository;
use Tymon\JWTAuth\Facades\JWTAuth;

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
    private $movieModelRepository;

    public function __construct(
        TmdbRepository $tmdbRepository,
        MovieRepository $movieRepository,
        DirectorRepository $directorRepository,
        ActorRepository $actorRepository,
        WriterRepository $writerRepository,
        GenreRepository $genreRepository,
        KeywordRepository $keywordRepository,
        CrawlerRepository $crawlerRepository,
        MovieModelRepository $movieModelRepository
    )
    {
        $this->tmdbRepository       = $tmdbRepository;
        $this->movieRepository      = $movieRepository;
        $this->directorRepository   = $directorRepository;
        $this->actorRepository      = $actorRepository;
        $this->writerRepository     = $writerRepository;
        $this->genreRepository      = $genreRepository;
        $this->keywordRepository    = $keywordRepository;
        $this->crawlerRepository    = $crawlerRepository;
        $this->movieModelRepository = $movieModelRepository;
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
                'movie' => ['title' => $movie['movie']['title'], 'tmdb_id' =>$movie['movie']['tmdb_id'] ],
                'success' => true
            ], 'Movie successfully saved', 200));
        }
    }

    /**
     * Save multiple movie from tmdb ids
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function postMultipleMoviesFromTmdb(Request $request)
    {
        $response = [];

        foreach($request->input('ids') as $id){
            try{
                $movieModel = $this->movieRepository->findBy('tmdb_id', $id);
                $response[$movieModel->title] = 'Already exists';
            }
            catch(ModelNotFoundException $e){
                $movieModel = null;
            }
            if(!$movieModel){
                $movie = $this->tmdbRepository->getMovie($id);
                if($movieModel = $this->saveMovieFromTmdb($movie)){
                    $response[$movieModel->title] = true;
                }
                else{
                    $response[$movieModel->title] = false;
                }
            }
        }

        return response()->json(new JsonResponse($response));
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
        $popularMovies = $this->tmdbRepository->getPopularMovies($page);
        $formattedPopularMovies = [];
        foreach($popularMovies['movies'] as $movie) {
            $exists = $this->checkIfMovieExists($movie['movie']['tmdb_id'], '');
            $movie = [
              'tmdb_id' => $movie['movie']['tmdb_id'],
              'title'   => $movie['movie']['original_title'],
              'exists'  => $exists
            ];
            array_push($formattedPopularMovies, $movie);
        }

        return response()->json(new JsonResponse([
            'movies' => $formattedPopularMovies,
            'currentPage' => $popularMovies['currentPage'],
            'totalPages' => $popularMovies['totalPages']
            ]));
    }

    public function checkIfMovieExists($tmdbId, $title)
    {
        $condition = $tmdbId === 0 ? ['original_title', $title] : ['tmdb_id', $tmdbId];
        try{
            $this->movieRepository->findBy($condition[0], $condition[1]);
            return true;
        }
        catch(ModelNotFoundException $e) {
            return false;
        }
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
        $newMovies = $this->tmdbRepository->getNowPlayingMovies($page);
        $formattedMovies = [];
        foreach($newMovies['movies'] as $movie) {
            $exists = $this->checkIfMovieExists($movie['movie']['tmdb_id'], '');
            $movie = [
                'tmdb_id' => $movie['movie']['tmdb_id'],
                'title'   => $movie['movie']['original_title'],
                'exists'  => $exists
            ];
            array_push($formattedMovies, $movie);
        }

        return response()->json(new JsonResponse([
            'movies' => $formattedMovies,
            'currentPage' => $newMovies['currentPage'],
            'totalPages' => $newMovies['totalPages']
        ]));
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

    /**
     * Find current movie in cinema and save them
     *
     * @param \Tmdb\Repository\SearchRepository  $searchRepository
     *
     * @return JsonResponse
     */
    public function findCurrentMoviesInCinema(SearchRepository $searchRepository)
    {
        $this->movieRepository->restartCurrentInCinema();

        $movies = $this->crawlerRepository->findTitles('http://www.cineplexx.rs/filmovi/u-bioskopu');

        $i = 0;
        $response = [];
        foreach($movies as $movie) {
            $i++;
            if($i>20) {
                return response()->json(new JsonResponse($response));
            }
            try{
                $movieModel = $this->movieRepository->findBy('original_title', $movie);
            }
            catch(ModelNotFoundException $e){
                $movieModel = null;
            }
            if(!$movieModel){
                try{
                    $id = $this->tmdbRepository->findByName($movie, Carbon::today()->year, $searchRepository);
                    $movieTmdb = $this->tmdbRepository->getMovie($id);
                    $movieModel = $this->movieRepository->findBy('tmdb_id', $id);
                }
                catch(ModelNotFoundException $e){
                    $movieModel = $this->saveMovieFromTmdb($movieTmdb);
                }
                catch(\Exception $e){
                    $response[$movie] = false;
                }
            }
            if(!isset($response[$movie])){
                $movieModel->in_cinema = true;

                $movieModel = $this->movieRepository->save($movieModel->getAttributes(), $movieModel->id);

                if($movieModel){
                    $response[$movie] = true;
                }
                else{
                    $response[$movie] = false;
                }
            }
        }

        return response()->json(new JsonResponse($response));
    }

    public function registerUserMovies(Request $request, LikeDislikeRepository $likeDislikeRepository, SearchRepository $searchRepository)
    {
        $this->validate($request, [
            'movie1' => 'required|min:3',
            'movie2' => 'required|min:3',
            'movie3' => 'required|min:3',
        ]);

        $user = JWTAuth::user();

        foreach($request->all() as $movie){
            try{
                $movieModel = $this->movieRepository->findBy('title', $movie);
            }
            catch(ModelNotFoundException $e){
                $id = $this->tmdbRepository->findByName($movie, null, $searchRepository);
                $movieTmdb = $this->tmdbRepository->getMovie($id);
                try{
                    $movieModel = $this->saveMovieFromTmdb($movieTmdb);
                }
                catch(\Exception $e) {
                }
            }
            $likeDislikeRepository->save([
                'user_id' => $user->id,
                'movie_id' => $movieModel->id,
                'is_like' => true
            ]);
        }

        return response()->json(new JsonResponse(['success' => true]));
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
        $genres          = [];
        $keywords        = [];
        $cast            = [];
        $writers         = [];
        $movieShortModel = [];

        $movie['movie']['director_id']  = $this->checkPersonAndSave($movie['crew']['director'][0], 'director', $this->directorRepository)->id;
        $movie['movie']['image_url']    = $this->saveImageFromUrl($movie['movie']['image_url'], 'images/movies');
        $movieModel                     = $this->movieRepository->save($movie['movie']);
        $movieShortModel['movie_id'] = $movieModel->id;
        $movieShortModel['director_id'] = $movieModel->director_id;

        foreach($movie['genres'] as $genre){
            $genreId = $this->genreRepository->findBy('name', $genre)->id;
            array_push($genres, $genreId);
        }
        $movieShortModel['genres'] = implode('/', $genres);
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
        $movieShortModel['keywords'] = implode("/", $keywords);
        $movieModel->keywords()->attach($keywords);

        foreach($movie['cast'] as $actor){
            $actorModel = $this->checkPersonAndSave($actor[0], 'actor', $this->actorRepository);
            array_push($cast, $actorModel->id);
        }
        $movieShortModel['actors'] = implode('/', $cast);
        $movieModel->actors()->attach($cast);

        foreach($movie['crew']['writers'] as $writer){
            $writerModel = $this->checkPersonAndSave($writer[0], 'writer', $this->writerRepository);
            array_push($writers, $writerModel->id);
        }
        $movieShortModel['writers'] = implode('/', $writers);
        $movieModel->writers()->attach($writers);
        $this->movieModelRepository->save($movieShortModel);

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
