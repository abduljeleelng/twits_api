<?php

namespace Database\Factories;

use App\Models\Twit;
use Illuminate\Database\Eloquent\Factories\Factory;

class TwitFactory extends Factory
{
    protected $model = Twit::class;

    public function definition(): array
    {
    	return [
            'user_id' => mt_rand(1,10),
            'text' =>$this->faker->paragraph(1),
    	];
    }
}
