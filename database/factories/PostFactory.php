<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence(),
            'slug' => Str::slug($this->faker->sentence()),
            'excerpt' => $this->faker->text(100),
            'content' => $this->faker->paragraphs(3, true),
            'user_id' => User::factory(), // Crea un usuario asociado
        ];
    }
}
