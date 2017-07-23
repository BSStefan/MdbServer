<?php

namespace App\Repositories;

use App\Repositories\Eloquent\Repository;
use App\Models\MovieCinema;
use Illuminate\Support\Facades\DB;

class MovieCinemaReporitory extends Repository
{
    protected $modelClass = MovieCinema::class;

    public function saveNewMoviesInCinema($moviesTitleIdArray, $info)
    {
        $formatedInfo = [];
        foreach($info as $cinemaId => $dates) {
            foreach($dates as $date) {
                foreach($date as $infoMovie) {
                    if(isset($moviesTitleIdArray[$infoMovie['title']])) {
                        $infoMovie['movie_id'] = $moviesTitleIdArray[$infoMovie['title']];
                        $infoMovie['cinema_id'] = $cinemaId;
                        unset($infoMovie['cinema']);
                        unset($infoMovie['movie']);
                        array_push($formatedInfo, $infoMovie);
                    }
                }
            }
        }
        DB::table('movie_cinema')->insert($formatedInfo);
    }
}