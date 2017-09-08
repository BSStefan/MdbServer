<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovieModel extends Model
{
    public function getActorsAttribute($value)
    {
        return explode('/', $value);
    }

    public function getWritersAttribute($value)
    {
        return explode('/', $value);
    }
    public function getKeywordsAttribute($value)
    {
        return explode('/', $value);
    }
    public function getGenresAttribute($value)
    {
        return explode('/', $value);
    }
}
