<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\ActorRepository;
use App\Repositories\Admin\TmdbRepository;
use App\Repositories\DirectorRepository;
use App\Repositories\GenreRepository;
use App\Repositories\KeywordRepository;
use App\Repositories\MovieRepository;
use App\Repositories\WriterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MovieController extends Controller
{
    private $tmdbRepository,
            $movieRepository,
            $directorRepository,
            $actorRepository,
            $writerRepository,
            $genreRepository,
            $keywordRepository;

    public function __construct(
        TmdbRepository $tmdbRepository,
        MovieRepository $movieRepository,
        DirectorRepository $directorRepository,
        ActorRepository $actorRepository,
        WriterRepository $writerRepository,
        GenreRepository $genreRepository,
        KeywordRepository $keywordRepository
    )
    {
        $this->tmdbRepository = $tmdbRepository;
        $this->movieRepository = $movieRepository;
        $this->directorRepository = $directorRepository;
        $this->actorRepository = $actorRepository;
        $this->writerRepository = $writerRepository;
        $this->genreRepository = $genreRepository;
        $this->keywordRepository = $keywordRepository;
    }

    public function getMovieFromTmdb($id)
    {
        $genres = [];
        $keywords = [];
        $cast = [];
        $writers = [];

        $movie = $this->tmdbRepository->getMovie($id);

        $movie['movie']['director_id'] = $this->checkDirector($movie['crew']['director'][0])->id;
        $movieModel = $this->movieRepository->save($movie['movie']);

        foreach ($movie['genres'] as $genre) {
            array_push($genres, $this->genreRepository->findBy('name', $genre)->id);
        }
        $movieModel->genres()->attach($genres);

        foreach ($movie['keywords'] as $word) {
            if(!$wordModel = $this->keywordRepository->findBy('word', $word)) {
                $wordModel = $this->keywordRepository->save(['word' => $word]);
            }
            array_push($keywords, $wordModel->id);
        }
        $movieModel->keywords()->attach($keywords);

        foreach ($movie['cast'] as $actor) {
            $actorModel = $this->checkActor($actor[0]);
            array_push($cast, $actorModel->id);
        }
        $movieModel->actors()->attach($cast);

        foreach ($movie['crew']['writers'] as $writer) {
            $writerModel = $this->checkWriter($writer[0]);
            array_push($writers, $writerModel->id);
        }
        $movieModel->writers()->attach($writers);

        return response()->json('Movie successfully saved.');
    }

    protected function checkDirector($directorId) {
        $director = $this->directorRepository->findBy('tmdb_id', $directorId);
        if(!$director) {
            $director = $this->tmdbRepository->getPerson($directorId);
            $director['role'] = 'director';
            $director = $this->savePersonPerRole($director);
        }
        return $director;
    }

    protected function checkActor($actorId) {
        $actorModel = $this->actorRepository->findBy('tmdb_id', $actorId);
        if(!$actorModel) {
            $actorTmdb = $this->tmdbRepository->getPerson($actorId);
            $actorTmdb['role'] = 'actor';
            $actorModel = $this->savePersonPerRole($actorTmdb);
        }
        return $actorModel;
    }

    protected function checkWriter($writerId) {
        if(!$writerModel = $this->writerRepository->findBy('tmdb_id', $writerId)) {
            $writerTmdb = $this->tmdbRepository->getPerson($writerId);
            $writerTmdb['role'] = 'writer';
            $writerModel = $this->savePersonPerRole($writerTmdb);
        }
        return $writerModel;
    }

    private function savePersonPerRole($person){
        switch ($person['role']){
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
        return $this->tmdbRepository->getTopRatedMovies($page);
    }

    public function getNewestFromTmdb()
    {
        return $this->tmdbRepository->getNowPlayingMovies();
    }

    public function getUpcomingFromTmdb()
    {
        return $this->tmdbRepository->getUpcomingMovies();
    }
}
