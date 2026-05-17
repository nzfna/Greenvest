<?php

namespace App\Services;

use App\Models\SimulationType;

class SimulationService
{
    public function calculate(int $typeId, float $initialAmount, int $duration): array
    {
        $type = SimulationType::findOrFail($typeId);
        $rate = (float) $type->return_rate;

        // Compound Interest: A = P(1 + r)^t
        $total           = $initialAmount * pow(1 + $rate, $duration);
        $estimatedReturn = $total - $initialAmount;

        return [
            'invested'         => (int) $initialAmount,
            'duration'         => $duration,
            'return_rate'      => round($rate * 100, 1),
            'estimated_return' => (int) round($estimatedReturn),
            'total'            => (int) round($total),
            'type_name'        => $type->name,
        ];
    }

    public function formatRupiah(int $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}