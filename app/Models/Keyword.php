<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keyword extends Model
{
    protected $fillable = [
        'word'
    ];

    public function movies()
    {
        $this->belongsToMany(Movie::class);
    }
}
