<?php

namespace Database\Factories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'article_id'         => Article::factory(),
            'user_name'          => fake()->name(),
            'user_email'         => fake()->optional()->safeEmail(),
            'content'            => fake()->paragraph(),
            'admin_reply'        => null,
            'replied_at'         => null,
            'status'             => 'pending',
            'ip_address'         => fake()->ipv4(),
            'user_agent'         => fake()->userAgent(),
            'device_fingerprint' => hash('sha256', fake()->ipv4() . fake()->userAgent()),
        ];
    }

    public function approved(): static
    {
        return $this->state(['status' => 'approved']);
    }

    public function rejected(): static
    {
        return $this->state(['status' => 'rejected']);
    }
}
