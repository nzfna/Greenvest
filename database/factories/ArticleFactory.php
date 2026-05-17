<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ArticleFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->sentence(6);
        return [
            'title'          => $title,
            'slug'           => Str::slug($title) . '-' . Str::random(4),
            'category_id'    => Category::factory(),
            'author_id'      => User::factory(),
            'description'    => fake()->paragraph(),
            'content'        => '<p>' . implode('</p><p>', fake()->paragraphs(4)) . '</p>',
            'cover_image'    => null,
            'status'         => 'published',
            'revision_count' => 1,
            'published_at'   => now(),
        ];
    }

    public function draft(): static
    {
        return $this->state(['status' => 'draft', 'published_at' => null]);
    }

    public function published(): static
    {
        return $this->state(['status' => 'published', 'published_at' => now()]);
    }
}
