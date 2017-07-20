<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $fillable = [
        'title', 'budget', 'homepage', 'description', 'language', 'tag_line', 'release_day', 'image_url', 'director_id'
    ];

    public function actors()
    {
        return $this->belongsToMany(Actor::class);
    }

    public function director()
    {
        return $this->belongsTo(Director::class);
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }

    public function keywords()
    {
        return $this->belongsToMany(Keyword::class);
    }

    public function writers()
    {
        return $this->belongsToMany(Writer::class);
    }

    public function projections()
    {
        $this->hasMany(MovieCinema::class);
    }
}
