<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'General',          'slug' => 'general'],
            ['name' => 'Green Bonds',       'slug' => 'green-bonds'],
            ['name' => 'Energi Surya',      'slug' => 'energi-surya'],
            ['name' => 'ESG',               'slug' => 'esg'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}