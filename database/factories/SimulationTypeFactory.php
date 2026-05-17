<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class SimulationTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name'        => fake()->randomElement([
                'Green Bonds (Obligasi Hijau)',
                'Saham Energi Surya',
                'ESG - Reksa Dana',
            ]),
            'return_rate' => fake()->randomFloat(4, 0.05, 0.15),
            'description' => fake()->sentence(),
        ];
    }
}
