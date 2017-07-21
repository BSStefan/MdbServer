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
        $movies = $this->crawlerRepository->findTitles('http://www.cineplexx.rs/filmovi/u-bioskopu');

        return $movies;
    }

    public function findTimeCurrentMoviesInCinema()
    {
        //$cinemas = $this->cinemaRepository->all();
        $cinemas = ['http://www.cineplexx.rs/service/program.php?type=program&centerId=616&date=*&sorting=alpha&undefined=Svi&view=detail&page=1'];
        $weekInformation = [];
        $date = Carbon::now()->addDays(0)->toDateString();
        $url = str_replace('*', $date, $cinemas[0]); //TODO changesS

        array_push($weekInformation, $this->crawlerRepository->findTimes($url));


        //foreach($cinemas as $cinema) {
        //    for($i=0; $i < 7; $i++){
        //        $date = Carbon::now()->addDays($i)->toDateString();
        //        $url = str_replace('*', $date, $cinema); //TODO changesS
        //        array_push($weekInformation, $this->crawlerRepository->findTimes($url));
        //    }
        //}

        return $weekInformation;
    }
}
