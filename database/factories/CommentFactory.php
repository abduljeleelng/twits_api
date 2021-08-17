<?php

namespace Database\Factories;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
    {
    	return [
            'text' =>$this->faker->paragraph(1),
            'twits_id' => mt_rand(1, 20),
            'user_id' => mt_rand(1, 10),
    	];
    }
}
