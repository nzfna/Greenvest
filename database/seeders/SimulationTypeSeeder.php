<?php

namespace Database\Seeders;

use App\Models\SimulationType;
use App\Models\Category;
use Illuminate\Database\Seeder;

class SimulationTypeSeeder extends Seeder
{
    public function run(): void
    {
        $greenBonds  = Category::where('slug', 'green-bonds')->first();
        $energiSurya = Category::where('slug', 'energi-surya')->first();
        $esg         = Category::where('slug', 'esg')->first();

        $types = [
            [
                'category_id' => $greenBonds->id,
                'name'        => 'Green Bonds (Obligasi Hijau)',
                'return_rate' => 0.0780,
                'description' => 'Bunga tetap Green Bond korporasi Indonesia rata-rata 7.8% per tahun.',
            ],
            [
                'category_id' => $energiSurya->id,
                'name'        => 'Saham Energi Surya',
                'return_rate' => 0.0610,
                'description' => 'Dividen + potensi apresiasi modal sektor surya, estimasi 6.1% per tahun.',
            ],
            [
                'category_id' => $esg->id,
                'name'        => 'ESG - Reksa Dana',
                'return_rate' => 0.1290,
                'description' => 'Rata-rata pengembalian perusahaan ESG global 12.9% per tahun.',
            ],
        ];

        foreach ($types as $type) {
            SimulationType::updateOrCreate(
                ['name' => $type['name']],
                $type
            );
        }
    }
}