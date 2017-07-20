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

    public function findTimeCurrentMoviesInCinema()
    {
        //Upisati u bazu
        $cinemas = [
            'BIG Beograd'          => 'http://www.cineplexx.rs/service/program.php?type=program&centerId=616&sorting=alpha&undefined=Svi&view=detail&page=1',
            'Cineplexx Delta City' => 'http://www.cineplexx.rs/service/program.php?type=program&centerId=611&date=2017-07-20&sorting=alpha&undefined=Svi&view=detail&page=1',
            'Cinaplexx Kragujevac' => 'http://www.cineplexx.rs/service/program.php?type=program&centerId=612&date=2017-07-20&sorting=alpha&undefined=Svi&view=detail&page=1',
            'Cinaplexx Nis'        => 'http://www.cineplexx.rs/service/program.php?type=program&centerId=615&date=2017-07-20&sorting=alpha&undefined=Svi&view=detail&page=1',
            'Cinaplexx Usce'       => 'http://www.cineplexx.rs/service/program.php?type=program&centerId=614&date=2017-07-20&sorting=alpha&undefined=Svi&view=detail&page=1'
        ];

        $info = $this->crolerRepository->findTimes($cinemas['BIG Beograd']);
    }
}
