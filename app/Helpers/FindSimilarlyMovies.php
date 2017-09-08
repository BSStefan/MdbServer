<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 8.9.17.
 * Time: 10.41
 */

namespace App\Helpers;

class FindSimilarlyMovies
{
    public static function findSimilarMovies($movieModel, $otherMovieModels, $coefficients)
    {
        $similarMovies = [];
        foreach($otherMovieModels as $movie){
            $similarity = self::findSimilarity($movieModel, $movie, $coefficients);
            if($similarity > 0.4) {
                $similarMovies[$movie->movie_id]=floatval($similarity);
            }
        }

        return $similarMovies;
    }

    public static function findSimilarity($movie1, $movie2, $coefficients)
    {
        $director = $coefficients->director * ($movie1->director_id == $movie2->director_id ? 1 : 0);
        $actors = $coefficients->actors * (self::findGroupSimilarity($movie1->actors, $movie2->actors));
        $writers = $coefficients->writers * (self::findGroupSimilarity($movie1->writers, $movie2->writers));
        $keywords = $coefficients->keywords * (self::findGroupSimilarity($movie1->keywords, $movie2->keywords));
        $genres = $coefficients->genres * (self::findGroupSimilarity($movie1->genres, $movie2->genres));

        return number_format($actors+$director+$writers+$keywords+$genres, 2);
    }

    public static function findGroupSimilarity($firstGroup, $secondGroup)
    {
        $similarity = 0;
        foreach($firstGroup as $one) {
            if(in_array($one, $secondGroup)){
                $similarity+=1;
            }
        }

        return $similarity/count($firstGroup);
    }
}