<?php

namespace App\Repositories\Admin;

use Carbon\Carbon;
use Intervention\Image\Facades\Image;
use Tmdb\Helper\ImageHelper;
use Tmdb\Model\Person;
use Tmdb\Model\Search\SearchQuery\MovieSearchQuery;
use Tmdb\Repository\GenreRepository;
use Tmdb\Repository\MovieRepository;
use Tmdb\Repository\PeopleRepository;
use Tmdb\Repository\SearchRepository;

class TmdbRepository
{
    /**
     * @var MovieRepository
     */
    private $movieRepository;

    /**
     * @var PeopleRepository
     */
    private $peopleRepository;

    /**
     * @var ImageHelper
     */
    private $imageHelper;

    /**
     * @var GenreRepository
     */
    private $genreRepository;

    /**
     * @param MovieRepository $movieRepository
     * @param PeopleRepository $peopleRepository
     * @param ImageHelper $imageHelper
     * @param GenreRepository $genreRepository
     */
    public function __construct(
        MovieRepository $movieRepository,
        PeopleRepository $peopleRepository,
        ImageHelper $imageHelper,
        GenreRepository $genreRepository
    )
    {
        $this->movieRepository  = $movieRepository;
        $this->peopleRepository = $peopleRepository;
        $this->imageHelper      = $imageHelper;
        $this->genreRepository  = $genreRepository;
    }

    /**
     * Find popular people and return array of People object with all information
     *
     * @param int $page
     * @return array
     */
    public function getPopularPeople($page)
    {
        $popularPeople = $this->peopleRepository->getPopular(['page' => $page]);
        $people        = [];
        foreach($popularPeople as $one){
            array_push($people, $this->getPerson($one->getId()));
        }

        return $people;
    }

    /**
     * Find people and return object with all information
     *
     * @param int $id
     * @return array
     */
    public function getPerson($id)
    {
        return $this->formatPerson($this->peopleRepository->load($id));
        //$actor['image_url'] = $this->saveImageFromUrl($actor['image_url'], $actor['name'], 'images/actors/');
    }

    /**
     * Nzm gde se koristi TODO
     *
     * @param array $list
     * @return array
     */
    public function getPeopleList($list)
    {
        $people = [];
        foreach($list as $id){
            array_push($people, $this->getPerson($id));
        }

        return $people;
    }

    /**
     * Format Person object and return array with information
     *
     * @param Person $person
     * @return array
     */
    private function formatPerson($person)
    {
        $details                   = [];
        $details['name']           = $person->getName();
        $details['tmdb_id']        = $person->getId();
        $details['biography']      = trim(str_replace("\n", "", $person->getBiography()));
        $details['role']           = $this->findRole($details['biography']);
        $details['birthday']       = $person->getBirthday() ? date_format($person->getBirthday(), "Y/m/d") : null; 
        $details['dead_day']       = $person->getDeathday() ? date_format($person->getDeathday(), "Y/m/d") : null;
        $details['place_of_birth'] = $person->getPlaceOfBirth();
        $details['gender']         = $person->isMale() ? 'male' : 'female';
        $details['image_url']      = 'http:' . $this->imageHelper->getUrl($person->getProfileImage());

        return $details;
    }

    /**
     * Find top rated movies on same page
     *
     * @param int $page
     * @return array
     */
    public function getTopRatedMovies($page)
    {
        $movies        = [];
        $popularMovies = $this->movieRepository->getTopRated(['page' => $page]);
        foreach($popularMovies as $movie){
            array_push($movies, $this->formatMovie($movie));
        }

        return $movies;
    }

    /**
     * Find top rated movies on same page
     * Return array of movies with less information
     *
     * @param int $page
     * @return array
     */
    public function getPopularMovies($page)
    {
        $movies        = [];
        $popularMovies = $this->movieRepository->getPopular(['page' => $page]);
        foreach($popularMovies as $movie){
            array_push($movies, $this->formatMovie($movie));
        }

        return $movies;
    }

    /**
     * Find now playing movies on same page
     * Return array of movies with less information
     *
     * @return array
     */
    public function getNowPlayingMovies($page)
    {
        $movies    = [];
        $newMovies = $this->movieRepository->getNowPlaying(['page' => $page]);
        foreach($newMovies as $movie){
            array_push($movies, $this->formatMovie($movie));
        }

        return $movies;
    }

    /**
     * Find upcoming movies on same page
     * Return array of movies with less information
     *
     * @return array
     */
    public function getUpcomingMovies($page)
    {
        $movies    = [];
        $newMovies = $this->movieRepository->getUpcoming(['page' => $page]);
        foreach($newMovies as $movie){
            array_push($movies, $this->formatMovie($movie));
        }

        return $movies;
    }

    /**
     * NZm gde se koristi TODO
     *
     * @param array $list
     * @return array
     */
    public function getMovieList(array $list)
    {
        $movies = [];
        foreach($list as $id){
            array_push($movies, $this->getMovie($id));
        }

        return $movies;
    }

    /**
     * Find movie by id
     *
     * @param int $id
     * @return array
     */
    public function getMovie($id)
    {
        return $this->formatMovie($this->movieRepository->load($id));
    }

    /**
     * Find all genres
     *
     * @var GenreRepository $genreRepository
     * @return array
     */
    public function getGenres()
    {

        $genreList = $this->genreRepository->loadMovieCollection();

        return $this->formatSimpleCollection($genreList);
    }

    /**
     * Format collection
     *
     * @param mixed $list
     * @return array
     */
    private function formatSimpleCollection($list)
    {
        $newList = [];
        foreach($list as $item){
            array_push($newList, $item->getName());
        }

        return $newList;
    }

    /**
     * Format movie from Object Movie
     * Return array with information
     *
     * @param Movie
     * @return array;
     */
    private function formatMovie($movie)
    {
        $newMovie                         = [];
        $newMovie['movie']['tmdb_id']     = $movie->getId();
        $newMovie['movie']['homepage']    = $movie->getHomepage();
        $newMovie['movie']['title']       = $movie->getTitle();
        $newMovie['movie']['language']    = $movie->getOriginalLanguage();
        $newMovie['movie']['release_day'] = $movie->getReleaseDate()->format('Y-m-d H:i:s');
        $newMovie['movie']['runtime']     = $movie->getRuntime();
        $newMovie['movie']['tag_line']    = $movie->getTagline();
        $newMovie['movie']['budget']      = $movie->getBudget();
        $newMovie['movie']['description'] = $movie->getOverview();
        $newMovie['genres']               = $this->formatSimpleCollection($movie->getGenres());
        $newMovie['keywords']             = $this->formatSimpleCollection($movie->getKeywords());
        $newMovie['movie']['image_url']   = 'http:' . $this->imageHelper->getUrl($movie->getPosterImage());
        $newMovie['cast']                 = $this->formatCast($movie->getCredits()->getCast());
        $newMovie['crew']                 = $this->formatCrew($movie->getCredits()->getCrew());

        return $newMovie;
    }

    /**
     * Find role of person
     *
     * @param string $biography
     * @return string
     */
    private function findRole($biography)
    {
        if(strpos($biography, 'actor') !== false or strpos($biography, 'actress')){
            return 'actor';
        }
        if(strpos($biography, 'director') !== false){
            return 'director';
        }
        if(strpos($biography, 'writer')){
            return 'writer';
        }

        return 'unknown';
    }

    /**
     * Format Cast
     * Return array of cast information
     *
     * @param Tmdb\Model\Collection\People\Cast
     * @return array
     */
    private function formatCast($cast)
    {
        $newCast = [];
        $i       = 0;
        foreach($cast as $person){
            if($i < 5){
                array_push($newCast, [$person->getId(), $person->getName()]);
                $i++;
            }
            else{
                break;
            }
        }

        return $newCast;
    }

    /**
     * Format Crew
     * Return array of crew information
     *
     * @param Tmdb\Model\Collection\People\Crew
     * @return array
     */
    private function formatCrew($crew)
    {
        $newCrew = [
            'director' => null,
            'writers'  => []
        ];

        foreach($crew as $person){
            if($person->getDepartment() == 'Directing' and !$newCrew['director']){
                $newCrew['director'] = [$person->getId(), $person->getName()];
            }
            else if($person->getDepartment() == 'Writing'){
                array_push($newCrew['writers'], [$person->getId(), $person->getName()]);
            }
        }

        return $newCrew;
    }

    /**
     * Find movie by name
     * Return id of movie
     *
     * @param string $movie
     * @param int $year
     * @param SearchRepository $searchRepository
     * @return int id
     */
    public function findByName($movie, $year = null, SearchRepository $searchRepository)
    {
        $options = new MovieSearchQuery();
        $options->includeAdult(false)->year($year);
        $movies = $searchRepository->searchMovie($movie, $options);

        foreach($movies as $movie){
            return $movie->getId();
        }

        return null;
    }

}