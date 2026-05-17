<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'current_password'          => ['required', 'string'],
            'new_password'              => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@$!%*#?&_\-])[A-Za-z\d@$!%*#?&_\-]+$/',
                'confirmed',
            ],
            'new_password_confirmation' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required'  => 'Password saat ini wajib diisi.',
            'new_password.required'      => 'Password baru wajib diisi.',
            'new_password.min'           => 'Password baru minimal 8 karakter.',
            'new_password.regex'         => 'Password baru harus mengandung huruf, angka, dan simbol.',
            'new_password.confirmed'     => 'Konfirmasi password tidak cocok.',
        ];
    }
}