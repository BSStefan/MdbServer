<?php

namespace App\Http\Controllers\Admin;

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

class StartController extends Controller
{
    private $tmdbRepository,
            $actorRepository,
            $directorRepository,
            $writerRepository,
            $genreRepository,
            $movieRepository,
            $keywordRepository;

    public function __construct(
        TmdbRepository $tmdbRepository,
        ActorRepository $actorRepository,
        DirectorRepository $directorRepository,
        WriterRepository $writerRepository,
        GenreRepository $genreRepository,
        MovieRepository $movieRepository,
        KeywordRepository $keywordRepository
    )
    {
        $this->tmdbRepository = $tmdbRepository;
        $this->actorRepository = $actorRepository;
        $this->directorRepository = $directorRepository;
        $this->writerRepository = $writerRepository;
        $this->genreRepository = $genreRepository;
        $this->movieRepository = $movieRepository;
        $this->keywordRepository = $keywordRepository;
    }

    //TODO EXCEPTION



    public function getPopularPeople($page)
    {
        $people = $this->tmdbRepository->getPopularPeople($page);

        foreach ($people as $person) {
            switch ($person['role']){
                case 'actor':
                    $person['image_url'] = $person['image_url'] ?
                        $this->saveImageFromUrl($person['image_url'], 'images/actors') : 'No image';
                        $this->actorRepository->save($person);
                    break;
                case 'director':
                    $person['image_url'] = $person['image_url'] ?
                        $this->saveImageFromUrl($person['image_url'], 'images/directors') : 'No image';
                        $this->directorRepository->save($person);
                    break;
                case 'writer':
                    $person['image_url'] = $person['image_url'] ?
                        $this->saveImageFromUrl($person['image_url'], 'images/writers') : 'No image';
                        $this->writerRepository->save($person);
                    break;
            }
        }
        return response()->json('Actors are successfully saved', 200);
    }

    public function getGenres()
    {
        $genres = $this->tmdbRepository->getGenres();

        foreach ($genres as $item) {
            $this->genreRepository->save(['name' => $item]);
        }

        return response()->json('The genres were successfully saved.', 200);
    }

    public function getMovie($id)
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