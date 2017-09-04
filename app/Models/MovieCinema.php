<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovieCinema extends Model
{
    public $table = 'movie_cinema';

    protected $fillable = [
        'movie_id', 'cinema_id', 'time', 'room', 'url'
    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function cinema()
    {
        return $this->belongsTo(Cinema::class);
    }
}
