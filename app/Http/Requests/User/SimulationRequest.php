<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class SimulationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'simulation_type_id' => ['required', 'exists:simulation_types,id'],
            'initial_amount'     => ['required', 'numeric', 'min:100000', 'max:100000000000'],
            'duration'           => ['required', 'integer', 'min:1', 'max:30'],
        ];
    }

    public function messages(): array
    {
        return [
            'simulation_type_id.required' => 'Jenis investasi wajib dipilih.',
            'simulation_type_id.exists'   => 'Jenis investasi tidak valid.',
            'initial_amount.required'     => 'Modal awal wajib diisi.',
            'initial_amount.numeric'      => 'Modal awal harus berupa angka.',
            'initial_amount.min'          => 'Modal awal minimal Rp 100.000.',
            'duration.required'           => 'Jangka waktu wajib diisi.',
            'duration.min'                => 'Jangka waktu minimal 1 tahun.',
            'duration.max'                => 'Jangka waktu maksimal 30 tahun.',
        ];
    }
}