<?php

namespace App\Http\Requests\Admin\Presensi;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PresensiFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'kepegawaian_master_bulan_id'    => ['nullable', 'integer', Rule::exists('kepegawaian_master_bulan', 'id')],
            'kepegawaian_master_kategori_id' => ['nullable', 'integer', Rule::exists('kepegawaian_master_kategori', 'id')],
            'tanggal_mulai'                  => ['nullable', 'date'],
            'tanggal_selesai'                => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'status'                         => ['nullable', Rule::in(['hadir', 'terlambat', 'izin', 'sakit', 'cuti', 'alpa', 'tugas'])],
            'search'                         => ['nullable', 'string', 'max:100'],
            'per_page'                       => ['nullable', 'integer', 'min:5', 'max:100'],
        ];
    }
}
