<?php


namespace App\Helpers;

class FormatMarks
{
    public static function formatFromMultipleArrays($array)
    {
        $formattedArray = [];
        foreach($array as $marksPerMovie)
        {
            foreach($marksPerMovie as $movieId => $mark){
                if(isset($formattedArray[$movieId])){
                    $formattedArray[$movieId] = $formattedArray[$movieId] < $mark ?  $mark : $formattedArray[$movieId];
                }
                else {
                    $formattedArray[$movieId] = $mark;
                }
            }
        }
        arsort($formattedArray);

        return array_slice($formattedArray,0,100,true);
    }

    public static function formatDislikeFromMultipleArrays($currentRecommendation, $similarDisliked, $dislikedMovie)
    {
        $formattedArray = [];
        foreach($currentRecommendation as $movieId => $mark) {
            if(isset($similarDisliked[$movieId]) && $similarDisliked[$movieId]>2) {
                continue;
            }
            $formattedArray[$movieId] = $mark;
        }
        if(isset($formattedArray[$dislikedMovie->movie_id])){
            unset($formattedArray[$dislikedMovie->movie_id]);
        }
        arsort($formattedArray);

        return array_slice($formattedArray,0,100,true);
    }

    public static function formatLikeDislikeUpdateAll($similarLiked, $similarDisliked)
    {
        $formattedArray = [];
        foreach($similarLiked as $movieId => $mark) {
            if(isset($similarDisliked[$movieId]) && $similarDisliked[$movieId]>2) {
                continue;
            }
            $formattedArray[$movieId] = $mark;
        }

        arsort($formattedArray);

        return array_slice($formattedArray,0,100,true);
    }
}