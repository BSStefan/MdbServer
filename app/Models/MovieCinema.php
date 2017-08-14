<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovieCinema extends Model
{
    protected $fillable = [
        'movie_id', 'cinema_id', 'time', 'room', 'url'
    ];

    public function movie()
    {
        $this->belongsTo(Movie::class);
    }

    public function cinema()
    {
        $this->belongsTo(Cinema::class);
    }
}
