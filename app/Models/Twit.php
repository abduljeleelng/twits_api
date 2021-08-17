<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Twit extends Model
{
    //

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function comment(){
        return $this->hasMany(Comment::class, "twits_id");
    }
    public function likes(){
        return $this->hasMany(Likes::class, "twits_id");
    }
}
