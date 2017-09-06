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
        $formatedInfo = [];
        foreach($info as $cinemaId => $dates) {
            foreach($dates as $k =>$date) {
                foreach($date as $infoMovie) {
                    if(isset($moviesTitleIdArray[$infoMovie['title']])) {
                        $movieFormated['date'] = $k;
                        $movieFormated['movie_id'] = $moviesTitleIdArray[$infoMovie['title']];
                        $movieFormated['cinema_id'] = $cinemaId;
                        $movieFormated['time'] = $infoMovie['time'];
                        $movieFormated['room'] = $infoMovie['room'];
                        $movieFormated['url'] = $infoMovie['url'];
                        $movieFormated['created_at'] = Carbon::now()->toDateTimeString();
                        $movieFormated['updated_at'] = Carbon::now()->toDateTimeString();
                        array_push($formatedInfo, $movieFormated);
                    }
                }
            }
        }

        return DB::table('movie_cinema')->insert($formatedInfo);
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