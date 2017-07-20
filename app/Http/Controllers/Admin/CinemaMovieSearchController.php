<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Admin\CrawlerRepository;
use App\Repositories\CinemaRepository;

class CinemaMovieSearchController extends Controller
{
    protected $crawlerRepository;
    protected $cinemaRepository;

    public function __construct(
        CrawlerRepository $crawlerRepository,
        CinemaRepository $cinemaRepository
    )
    {
        $this->crawlerRepository = $crawlerRepository;
        $this->cinemaRepository  = $cinemaRepository;
    }

    public function findCurrentMoviesInCinema()
    {
        $movies = $this->crawlerRepository->findTitles('http://www.cineplexx.rs/filmovi/u-bioskopu');

        return $movies;
    }

    public function findTimeCurrentMoviesInCinema()
    {
        $cinemas = $this->cinemaRepository->all();
        $weekInformation = [];
        foreach($cinemas as $cinema) {
            array_push($weekInformation, $this->crawlerRepository->findTimes($cinema->crawler_link));
        }


    }
}
