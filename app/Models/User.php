<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public function provider()
    {
        return $this->hasOne(UserProvider::class);
    }
}
