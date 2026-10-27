<?php

namespace App\Http\Requests\Admin\Fasilitas;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FasilitasFilterRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diotorisasi untuk melakukan request ini.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Aturan validasi untuk filter index data fasilitas.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'karyawan_nipy' => ['nullable', 'string', Rule::exists('kepegawaian_master_karyawan', 'nipy')],
            'nipy'          => ['nullable', 'string', Rule::exists('kepegawaian_master_karyawan', 'nipy')],
            'status'        => ['nullable', Rule::in(['0', '1', 'true', 'false'])],
            'tahun_terima'  => ['nullable', 'integer', 'digits:4'],
            'search'        => ['nullable', 'string', 'max:100'],
            'per_page'      => ['nullable', 'integer', 'min:5', 'max:100'],
        ];
    }
}
