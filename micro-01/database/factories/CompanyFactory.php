<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::factory()->create(),
            'name' => $this->faker->unique()->name(),
            'email' => $this->faker->unique()->email(),
            'whatsapp' => $this->faker->unique()->numberBetween(1000000000, 99999999999),
            'image' => 'eti.png',
        ];
    }
}
