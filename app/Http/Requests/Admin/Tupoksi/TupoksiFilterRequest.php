<?php

namespace App\Http\Requests\Admin\Tupoksi;

use Illuminate\Foundation\Http\FormRequest;

class TupoksiFilterRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diotorisasi untuk melakukan request ini.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Aturan validasi untuk filter data Tupoksi.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'karyawan_nipy'                   => ['nullable', 'string', 'max:50'],
            'akademik_master_tahun_ajaran_id' => ['nullable', 'integer'],
            'jabatan'                         => ['nullable', 'string', 'max:255'],
            'search'                          => ['nullable', 'string', 'max:255'],
            'per_page'                        => ['nullable', 'integer', 'min:5', 'max:100'],
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

        if ($this->has('jabatan')) {
            $sanitized['jabatan'] = trim((string) $this->jabatan);
        }

        if ($this->has('akademik_master_tahun_ajaran_id') && $this->akademik_master_tahun_ajaran_id !== '') {
            $sanitized['akademik_master_tahun_ajaran_id'] = (int) $this->akademik_master_tahun_ajaran_id;
        }

        $this->merge($sanitized);
    }
}
