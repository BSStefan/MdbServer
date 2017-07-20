<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cinema extends Model
{
    public function projections()
    {
        $this->hasMany(MovieCinema::class);
    }
}
