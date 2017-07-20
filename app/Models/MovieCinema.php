<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovieCinema extends Model
{
    public function movie()
    {
        $this->belongsTo(Movie::class);
    }

    public function cinema()
    {
        $this->belongsTo(Cinema::class);
    }
}
