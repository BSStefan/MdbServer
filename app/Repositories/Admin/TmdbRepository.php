<?php

namespace App\Repositories\Admin;

use Carbon\Carbon;
use Intervention\Image\Facades\Image;
use Tmdb\Helper\ImageHelper;
use Tmdb\Model\Search\SearchQuery\MovieSearchQuery;
use Tmdb\Repository\GenreRepository;
use Tmdb\Repository\MovieRepository;
use Tmdb\Repository\PeopleRepository;
use Tmdb\Repository\SearchRepository;

class TmdbRepository
{
    private $movieRepository,
            $peopleRepository,
            $genreRepository,
            $searchRepository,
            $imageHelper;

    public function __construct(
        MovieRepository $movieRepository,
        PeopleRepository $peopleRepository,
        GenreRepository $genreRepository,
        SearchRepository $searchRepository,
        ImageHelper $imageHelper
    )
    {
        $this->movieRepository = $movieRepository;
        $this->peopleRepository = $peopleRepository;
        $this->genreRepository = $genreRepository;
        $this->searchRepository = $searchRepository;
        $this->imageHelper = $imageHelper;
    }

    /**
     * @param int $page
     * @return array
     */
    public function getPopularPeople($page)
    {
        $popularPeople = $this->peopleRepository->getPopular(['page' => $page]);
        $people = [];
        foreach ($popularPeople as $one)
        {
            array_push($people,$this->getPerson($one->getId()));
        }
        return $people;
    }

    /**
     * @param int $id
     * @return array
     */
    public function getPerson($id)
    {
        return $this->formatPerson($this->peopleRepository->load($id));
        //$actor['image_url'] = $this->saveImageFromUrl($actor['image_url'], $actor['name'], 'images/actors/');
    }

    /**
     * @param array $list
     * @return array
     */
    public function getPeopleList($list)
    {
        $people = [];
        foreach ($list as $id) {
            array_push($people, $this->getPerson($id));
        }
        return $people;
    }

    /**
     * @param Tmdb\Model\Person
     * @return array
     */
    private function formatPerson($person)
    {
        $details = [];
        $details['name'] = $person->getName();
        $details['tmdb_id'] = $person->getId();
        $details['biography'] = trim(str_replace("\n", "", $person->getBiography()));
        $details['role'] = $this->findRole($details['biography']);
        $details['birthday'] = date_format($person->getBirthday(),"Y/m/d");
        $details['dead_day'] = $person->getDeathday() ? date_format($person->getDeathday(), "Y/m/d") : null;
        $details['place_of_birth'] = $person->getPlaceOfBirth();
        $details['gender'] = $person->isMale() ? 'male' : 'female';
        $details['image_url'] = 'http:' . $this->imageHelper->getUrl($person->getProfileImage());
        return $details;
    }

    /**
     * @param int $page
     * @return array
     */
    public function getTopRatedMovies($page = 1)
    {
        $movies = [];
        $popularMovies = $this->movieRepository->getTopRated(['page' => $page]);
        foreach ($popularMovies as $movie) {
            array_push($movies, $this->formatMovie($movie));
        }
        return $movies;
    }

    public function getPopularMovies($page = 1)
    {
        $movies = [];
        $popularMovies = $this->movieRepository->getPopular(['page' => $page]);
        foreach ($popularMovies as $movie) {
            array_push($movies, $this->formatMovie($movie));
        }
        return $movies;
    }

    public function getNowPlayingMovies()
    {
        $movies = [];
        $newMovies = $this->movieRepository->getNowPlaying();
        foreach ($newMovies as $movie) {
            array_push($movies, $this->formatMovie($movie));
        }
        return $movies;
    }

    public function getUpcomingMovies()
    {
        $movies = [];
        $newMovies = $this->movieRepository->getUpcoming();
        foreach ($newMovies as $movie) {
            array_push($movies, $this->formatMovie($movie));
        }
        return $movies;
    }

    /**
     * @param array $list
     * @return array
     */
    public function getMovieList(array $list) {
        $movies = [];
        foreach ($list as $id) {
            array_push($movies, $this->getMovie($id));
        }
        return $movies;
    }

    /**
     * @param int $id
     * @return array
     */
    public function getMovie($id)
    {
        return $this->formatMovie($this->movieRepository->load($id));
    }

    /**
     * @return array
     */
    public function getGenres()
    {
        $genreList = $this->genreRepository->loadMovieCollection();
        return $this->formatSimpleCollection($genreList);
    }

    /**
     * @param mixed $list
     * @return array
     */
    private function formatSimpleCollection($list)
    {
        $newList = [];
        foreach ($list as $item)
        {
            array_push($newList, $item->getName());
        }
        return $newList;
    }

    /**
     * @param Tmdb\Model\Movie
     * @return array;
     */
    private function formatMovie($movie)
    {
        $newMovie = [];
        $newMovie['movie']['tmdb_id'] = $movie->getId();
        $newMovie['movie']['homepage'] = $movie->getHomepage();
        $newMovie['movie']['title'] = $movie->getTitle();
        $newMovie['movie']['language'] = $movie->getOriginalLanguage();
        $newMovie['movie']['release_day'] = $movie->getReleaseDate()->format('Y-m-d H:i:s');
        $newMovie['movie']['runtime'] = $movie->getRuntime();
        $newMovie['movie']['tag_line'] = $movie->getTagline();
        $newMovie['movie']['budget'] = $movie->getBudget();
        $newMovie['movie']['description'] = $movie->getOverview();
        $newMovie['genres'] = $this->formatSimpleCollection($movie->getGenres());
        $newMovie['keywords'] = $this->formatSimpleCollection($movie->getKeywords());
        $newMovie['movie']['image_url'] = 'http:' . $this->imageHelper->getUrl($movie->getPosterImage());
        $newMovie['cast'] = $this->formatCast($movie->getCredits()->getCast());
        $newMovie['crew'] = $this->formatCrew($movie->getCredits()->getCrew());
        return $newMovie;
    }

    /**
     * @param string $biography
     * @return string
     */
    private function findRole ($biography)
    {
        if (strpos($biography,'actor') !== false or strpos($biography,'actress')) {
            return 'actor';
        }
        if(strpos($biography,'director') !== false) {
            return 'director';
        }
        if (strpos($biography,'writer')) {
            return 'writer';
        }
        return 'unknown';
    }

    /**
     * @param Tmdb\Model\Collection\People\Cast
     * @return array
     */
    private function formatCast($cast)
    {
        $newCast = [];
        $i = 0;
        foreach ($cast as $person) {
            if($i < 5){
                array_push($newCast,[$person->getId(),$person->getName()]);
                $i++;
            } else{
                break;
            }
        }
        return $newCast;
    }

    private function formatCrew($crew)
    {
        $newCrew = [
            'director' => null,
            'writers' => []
        ];

        foreach ($crew as $person) {
            if ($person->getDepartment() == 'Directing' and !$newCrew['director']) {
                $newCrew['director'] = [$person->getId(), $person->getName()];
            } else if ($person->getDepartment() == 'Writing') {
                array_push($newCrew['writers'], [$person->getId(), $person->getName()]);
            }
        }

        return $newCrew;
    }

    public function findByName($movie, $year = null)
    {
        $options = new MovieSearchQuery();
        $options->includeAdult(false)->year($year);
        $movies = $this->searchRepository->searchMovie($movie, $options);

        foreach($movies as $movie){
            return $movie->getId();
        }

        return null;
    }



}