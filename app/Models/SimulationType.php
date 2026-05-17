<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SimulationType extends Model
{
    protected $fillable = ['category_id', 'name', 'return_rate', 'description'];

    protected $casts = [
        'return_rate' => 'float',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getReturnRatePercentAttribute(): float
    {
        return round($this->return_rate * 100, 1);
    }
}
