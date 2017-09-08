<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRecommendation extends Model
{
    public function getMoviesAttribute($value)
    {
        $movies = explode( '/',$value);
        $formattedMovies = [];
        foreach($movies as $movie) {
            $movieId = intval(explode('-', $movie)[0]);
            $movieMark = floatval(explode('-', $movie)[1]);
            $formattedMovies[$movieId] = $movieMark;
        }
        arsort($formattedMovies);

        return $formattedMovies;
    }
}
