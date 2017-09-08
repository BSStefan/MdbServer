<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SimilarMovie extends Model
{
    public function getSimilarMovieAttribute($value)
    {
        return explode('/', $value);
    }
}
