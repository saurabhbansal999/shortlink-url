<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShortUrlFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'      => User::factory(),
            'original_url' => $this->faker->url(),
            'short_code'   => null,
        ];
    }
}
