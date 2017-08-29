<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Director extends Model
{
    protected $fillable = [
        'name', 'place_of_birth', 'biography', 'birthday', 'dead_day', 'gender', 'image_url'
    ];

    public function movies()
    {
         return $this->hasMany(Movie::class);
    }
}
