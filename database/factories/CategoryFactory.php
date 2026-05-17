<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->randomElement(['General', 'Green Bonds', 'Energi Surya', 'ESG', 'Test Category']);
        return [
            'name' => $name,
            'slug' => Str::slug($name),
        ];
    }
}
