<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Likes extends Model
{
    public function twits(){
        return $this->belongsToMany(Twit::class);
    }
}
