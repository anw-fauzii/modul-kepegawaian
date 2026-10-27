<?php

namespace App\Http\Requests\Admin\Presensi;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PresensiStatistikRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'akademik_master_tahun_ajaran_id' => ['nullable', 'integer', Rule::exists('akademik_master_tahun_ajaran', 'id')],
            'kepegawaian_master_kategori_id'  => ['nullable', 'integer', Rule::exists('kepegawaian_master_kategori', 'id')],
        ];
    }

    public function messages(): array
    {
        return [
            'akademik_master_tahun_ajaran_id.exists' => 'Tahun ajaran yang dipilih tidak valid.',
            'kepegawaian_master_kategori_id.exists'  => 'Kategori pegawai tidak valid.',
        ];
    }
}
