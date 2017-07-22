<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Admin\CrawlerRepository;
use App\Repositories\CinemaRepository;
use Carbon\Carbon;

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
        $movices = $this->crawlerRepository->findTitles('http://www.cineplexx.rs/filmovi/u-bioskopu');

        return $movies;
    }

    public function findTimeCurrentMoviesInCinema()
    {
        $cinemas = $this->cinemaRepository->all();

        $weekInformation = [];

        foreach($cinemas as $cinema) {
            $cinemaMovies = [];
            for($i=0; $i < 7; $i++){
                $date = Carbon::now()->addDays($i)->toDateString();
                $url = str_replace('*', $date, $cinema->crawler_link);
                $cinemaMovies[$date] = $this->crawlerRepository->findTimes($url);
            }
            $weekInformation[$cinema->id] = $cinemaMovies;
        }

        return $weekInformation;
    }
}
