<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'max:500'],
            'content'     => ['required', 'string'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status'      => ['nullable', 'in:published,draft'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'       => 'Judul artikel wajib diisi.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists'   => 'Kategori tidak valid.',
            'content.required'     => 'Konten artikel wajib diisi.',
            'cover_image.image'    => 'File harus berupa gambar.',
            'cover_image.max'      => 'Ukuran gambar maksimal 2MB.',
        ];
    }
}