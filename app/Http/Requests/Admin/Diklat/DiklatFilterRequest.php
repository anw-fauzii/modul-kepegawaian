<?php

namespace App\Http\Requests\Admin\Diklat;

use Illuminate\Foundation\Http\FormRequest;

class DiklatFilterRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diotorisasi untuk melakukan request ini.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Aturan validasi untuk filter data Diklat.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'karyawan_nipy'   => ['nullable', 'string', 'max:50'],
            'kategori'        => ['nullable', 'string', 'in:mandiri,lembaga'],
            'tanggal_mulai'   => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'search'          => ['nullable', 'string', 'max:255'],
            'per_page'        => ['nullable', 'integer', 'min:5', 'max:100'],
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

        if ($this->has('kategori') && $this->kategori !== '') {
            $sanitized['kategori'] = strtolower(trim((string) $this->kategori));
        }

        $this->merge($sanitized);
    }
}
