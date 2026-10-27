<?php

namespace App\Http\Requests\Admin\SertifikatPenghargaan;

use Illuminate\Foundation\Http\FormRequest;

class SertifikatPenghargaanFilterRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diotorisasi untuk melakukan request ini.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Aturan validasi untuk filter data Sertifikat Penghargaan.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'karyawan_nipy' => ['nullable', 'string', 'max:50'],
            'tahun'         => ['nullable', 'integer', 'digits:4'],
            'search'        => ['nullable', 'string', 'max:255'],
            'per_page'      => ['nullable', 'integer', 'min:5', 'max:100'],
        ];
    }

    /**
     * Sanitasi parameter filter sebelum validasi.
     */
    protected function prepareForValidation(): void
    {
        $sanitized = [];

        if ($this->has('search')) {
            $sanitized['search'] = trim((string) $this->search);
        }

        if ($this->has('tahun') && $this->tahun !== '') {
            $sanitized['tahun'] = (int) $this->tahun;
        }

        $this->merge($sanitized);
    }
}
