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

        $director = $this->directorRepository->findBy('tmdb_id', $movie['crew']['director'][0]);
        if(!$director) {
            $director = $this->tmdbRepository->getPerson($movie['crew']['director'][0]);
            $director['image_url'] = $director['image_url'] ?
                $this->saveImageFromUrl($director['image_url'], 'images/directors') : 'No image';
            unset($director['role']);
            $director = $this->directorRepository->save($director);
        }
        $movie['movie']['director_id'] = $director['id'];

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
            $this->actorRepository->findBy('tmdb_id', $actor[0]);
            if(!$actorModel = $this->actorRepository->findBy('tmdb_id', $actor[0])) {
                $actorTmdb = $this->tmdbRepository->getPerson($actor[0]);
                $actorTmdb['image_url'] = $actorTmdb['image_url'] ?
                    $this->saveImageFromUrl($actorTmdb['image_url'], 'images/directors') : 'No image';
                unset($actorTmdb['role']);
                $actorModel = $this->actorRepository->save($actorTmdb);
            }
            array_push($cast, $actorModel->id);
        }
        $movieModel->actors()->attach($cast);

        foreach ($movie['crew']['writers'] as $writer) {
            if(!$writerModel = $this->writerRepository->findBy('tmdb_id', $writer[0])) {
                $writerTmdb = $this->tmdbRepository->getPerson($writer[0]);
                $writerTmdb['image_url'] = $writerTmdb['image_url'] ?
                    $this->saveImageFromUrl($writerTmdb['image_url'], 'images/directors') : 'No image';
                unset($writerTmdb['role']);
                $writerModel = $this->writerRepository->save($writerTmdb);
            }
            array_push($cast, $writerModel->id);
        }
        $movieModel->writers()->attach($writers);

        return response()->json('Movie successfully saved.');
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
