<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cinema extends Model
{
    protected $fillable = ['name', 'address', 'phone', 'crawler_link'];

    public function projections()
    {
        $this->hasMany(MovieCinema::class)->withTimestamps();
    }
}
