<?php

namespace App\Repositories;

use App\Repositories\Eloquent\Repository;
use App\Models\MovieCinema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MovieCinemaReporitory extends Repository
{
    protected $modelClass = MovieCinema::class;

    public function saveNewMoviesInCinema($moviesTitleIdArray, $info)
    {
        $formattedInfo = [];
        foreach($info as $cinemaId => $dates) {
            foreach($dates as $k =>$date) {
                foreach($date as $infoMovie) {
                    if(isset($moviesTitleIdArray[$infoMovie['title']])) {
                        $movieFormatted['date'] = $k;
                        $movieFormatted['movie_id'] = $moviesTitleIdArray[$infoMovie['title']];
                        $movieFormatted['cinema_id'] = $cinemaId;
                        $movieFormatted['time'] = $infoMovie['time'];
                        $movieFormatted['room'] = $infoMovie['room'];
                        $movieFormatted['url'] = $infoMovie['url'];
                        $movieFormatted['created_at'] = Carbon::now()->toDateTimeString();
                        $movieFormatted['updated_at'] = Carbon::now()->toDateTimeString();
                        array_push($formattedInfo, $movieFormatted);
                    }
                }
            }
        }

        return DB::table('movie_cinema')->insert($formattedInfo);
    }

    public function findProjections($id, $city)
    {
        $today = new Carbon();
        $date = $today->toDateString();
        $projections = $this->model->select('movie_cinema.*', 'cinemas.name')
            ->join('cinemas', 'movie_cinema.cinema_id', '=', 'cinemas.id')
            ->where('movie_cinema.movie_id', '=', $id)
            ->where('cinemas.city', '=', $city)
            ->where('date', '>=', $date)
            ->get();
        $format = [];
        foreach($projections as $projection){
            array_push($format, $projection->getAttributes());
        }

        return $format;
    }

    public function deleteAll()
    {
        DB::table('movie_cinema')->truncate();
    }
}