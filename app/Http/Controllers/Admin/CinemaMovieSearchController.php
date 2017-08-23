<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\MovieCinemaReporitory;
use App\Repositories\MovieRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Admin\CrawlerRepository;
use App\Repositories\CinemaRepository;
use Carbon\Carbon;
use App\Http\Response\JsonResponse;

class CinemaMovieSearchController extends Controller
{
    protected $crawlerRepository;
    protected $cinemaRepository;
    protected $movieRepository;
    protected $movieCinemaReporitory;

    public function __construct(
        CrawlerRepository $crawlerRepository,
        CinemaRepository $cinemaRepository,
        MovieRepository $movieRepository,
        MovieCinemaReporitory $movieCinemaReporitory
    )
    {
        $this->crawlerRepository     = $crawlerRepository;
        $this->cinemaRepository      = $cinemaRepository;
        $this->movieRepository       = $movieRepository;
        $this->movieCinemaReporitory = $movieCinemaReporitory;
    }

    public function findTimesCurrentMovies()
    {
        $cinemas = $this->cinemaRepository->all();

        $weekInformation = [];

        foreach($cinemas as $cinema){
            $cinemaMovies = [];
            for($i = 0; $i < 7; $i++){
                $date                = Carbon::now()->addDays($i)->toDateString();
                $url                 = str_replace('*', $date, $cinema->crawler_link);
                $cinemaMovies[$date] = $this->crawlerRepository->findTimes($url);
            }
            $weekInformation[$cinema->id] = $cinemaMovies;
        }

        return $weekInformation;
    }

    public function findTimeCurrentMoviesInCinema()
    {
        $currentMoviesTitleId = $this->movieRepository->findCurrentInCinema();

        $weekInformation      = $this->findTimesCurrentMovies();

        if($this->movieCinemaReporitory->saveNewMoviesInCinema($currentMoviesTitleId, $weekInformation)) {
            return response()->json(new JsonResponse([
                'success' => true
            ], '', 200));
        }
        else {
            return response()->json(new JsonResponse([
                'success' => false
            ], 'There was an problem', 400));
        }
    }
}
