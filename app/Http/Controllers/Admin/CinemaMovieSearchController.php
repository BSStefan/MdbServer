<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Admin\CrolerRepository;

class CinemaMovieSearchController extends Controller
{
    protected $crolerRepository;

    public function __construct(CrolerRepository $crolerRepository)
    {
        $this->crolerRepository = $crolerRepository;
    }

    public function findCurrentMoviesInCinema()
    {
        $movies = $this->crolerRepository->findTitles('http://www.cineplexx.rs/filmovi/u-bioskopu');

        return $movies;
    }
}
