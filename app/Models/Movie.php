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
        $this->belongsToMany(Actor::class);
    }

    public function director()
    {
        $this->belongsTo(Director::class);
    }

    public function genres()
    {
        $this->belongsToMany(Genre::class);
    }

    public function keywords()
    {
        $this->belongsToMany(Keyword::class);
    }

    public function writers()
    {
        $this->belongsToMany(Writer::class);
    }
}
