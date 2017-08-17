<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProvider extends Model
{
    public function user()
    {
        $this->belongsTo(User::class);
    }
}
