<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Twit;

Class TwitsPolicy {
    public function delete(User $user, Twit $twit){
        return $twit->user_id === $user->id;
    }
}
