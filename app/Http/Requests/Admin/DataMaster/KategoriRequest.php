<?php

namespace App\Http\Requests\Admin\DataMaster;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KategoriRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $kategoriId = $this->route('kategori');

        return [
            'nama_kategori' => [
                'required',
                'string',
                'max:255',
                Rule::unique('kepegawaian_master_kategori', 'nama_kategori')->ignore($kategoriId),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_kategori.required' => 'Nama Kategori wajib diisi.',
            'nama_kategori.unique'   => 'Nama Kategori ini sudah ada dalam sistem.',
        ];
    }
}