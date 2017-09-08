<?php


namespace App\Helpers;

class FormatCoefficients
{
    public static function formatUserCoefficients($likesDislikesModels, $currentCoefficients)
    {
        $actors = [];
        $genres = [];
        $directors = [];

        foreach($likesDislikesModels as $model)
        {
            if(isset($directors[$model->director_id])){
                $directors[$model->director_id] +=1;
            }
            else{
                $directors[$model->director_id] =1;
            }

            foreach($model->actors as $actor) {
                if(isset($actors[$actor])){
                    $actors[$actor] +=1;
                }
                else{
                    $actors[$actor] =1;
                }
            }

            foreach($model->genres as $genre) {
                if(isset($genres[$genre])){
                    $genres[$genre] +=1;
                }
                else{
                    $genres[$genre] =1;
                }
            }

        }

        return self::createNewCoefficients($currentCoefficients, $actors, $directors, $genres);

    }

    public static function createNewCoefficients($old, $actors, $directors, $genres)
    {
        $updated = false;

        if(max($directors) > 6 && $old->director <= 2.2) {
            $old->director += 0.2;
            $old->actors -= 0.05;
            $old->writers -= 0.05;
            $old->genres -= 0.05;
            $old->keywords -= 0.05;
            $updated= true;
        }
        else if(max($directors) > 3 && $old->director <= 2) {
            $old->director += 0.2;
            $old->actors -= 0.05;
            $old->writers -= 0.05;
            $old->genres -= 0.05;
            $old->keywords -= 0.05;
            $updated= true;
        }

        if(max($actors) > 6 && $old->actors <= 3.2) {
            $old->actors += 0.2;
            $old->director -= 0.05;
            $old->writers -= 0.05;
            $old->genres -= 0.05;
            $old->keywords -= 0.05;
            $updated= true;
        }
        else if(max($actors) > 3 && $old->actors <= 3) {
            $old->actors += 0.2;
            $old->director -= 0.05;
            $old->writers -= 0.05;
            $old->genres -= 0.05;
            $old->keywords -= 0.05;
            $updated= true;
        }

        if(max($genres) > 20 && $old->genres <= 1.2) {
            $old->genres += 0.2;
            $old->director -= 0.05;
            $old->writers -= 0.05;
            $old->actors -= 0.05;
            $old->keywords -= 0.05;
            $updated= true;
        }
        else if(max($genres) > 10 && $old->genres <= 1) {
            $old->genres += 0.2;
            $old->director -= 0.05;
            $old->writers -= 0.05;
            $old->actors -= 0.05;
            $old->keywords -= 0.05;
            $updated= true;
        }
        return [$updated, $old->getAttributes()];
    }



}