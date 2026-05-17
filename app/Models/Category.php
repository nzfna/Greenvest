<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'slug'];

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function simulationTypes()
    {
        return $this->hasMany(SimulationType::class);
    }

    // Category icons mapping
    public function getIconAttribute(): string
    {
        return match($this->slug) {
            'general'        => 'ph-newspaper',
            'green-bonds'    => 'ph-hand-coins',
            'energi-surya'   => 'ph-sun',
            'esg'            => 'ph-leaf',
            default          => 'ph-file-text',
        };
    }

    public function getColorAttribute(): string
    {
        return match($this->slug) {
            'general'        => 'bg-gray-100 text-gray-600',
            'green-bonds'    => 'bg-forest-100 text-forest-800',
            'energi-surya'   => 'bg-yellow-100 text-yellow-700',
            'esg'            => 'bg-emerald-100 text-emerald-700',
            default          => 'bg-gray-100 text-gray-600',
        };
    }
}
