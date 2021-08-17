<?php

namespace Database\Factories;

use App\Models\Likes;
use Illuminate\Database\Eloquent\Factories\Factory;

class LikesFactory extends Factory
{
    protected $model = Likes::class;

    public function definition(): array
    {
    	return [
            'twits_id' => mt_rand(1, 10),
            'user_id' => mt_rand(1, 10),
    	];
    }
}
