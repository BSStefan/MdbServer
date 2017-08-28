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
        return $this->belongsToMany(Actor::class)->withTimestamps();
    }

    public function director()
    {
        return $this->belongsTo(Director::class);
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class)->withTimestamps();
    }

    public function keywords()
    {
        return $this->belongsToMany(Keyword::class)->withTimestamps();
    }

    public function writers()
    {
        return $this->belongsToMany(Writer::class)->withTimestamps();
    }

    public function projections()
    {
        return $this->hasMany(MovieCinema::class)->withTimestamps();
    }

    public function likes()
    {
        return $this->hasMany(LikeDislike::class);
    }

    public function like()
    {
        return $this->hasMany(LikeDislike::class)->where('is_like', true);
    }

    public function dislike()
    {
        return $this->hasMany(LikeDislike::class)->where('is_like', false);
    }
}
