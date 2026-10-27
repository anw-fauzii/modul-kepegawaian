<?php

namespace App\Http\Requests\Admin\Presensi;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PresensiBulkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'tanggal'                        => ['required', 'date'],
            'kepegawaian_master_kategori_id' => ['required', Rule::exists('kepegawaian_master_kategori', 'id')],
            'kepegawaian_master_bulan_id'    => ['nullable', Rule::exists('kepegawaian_master_bulan', 'id')],
            'items'                          => ['required', 'array'],
            'items.*.karyawan_nipy'          => ['required', 'string', Rule::exists('kepegawaian_master_karyawan', 'nipy')],
            'items.*.status'                 => ['nullable', Rule::in(['hadir', 'terlambat', 'izin', 'sakit', 'cuti', 'alpa', 'tugas'])],
            'items.*.jam_masuk'              => ['nullable', 'date_format:H:i'],
            'items.*.jam_pulang'             => ['nullable', 'date_format:H:i'],
            'items.*.izin_terlambat'         => ['nullable'],
            'items.*.izin_pulang_cepat'      => ['nullable'],
            'items.*.catatan'                => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'tanggal.required'                        => 'Tanggal presensi wajib diisi.',
            'kepegawaian_master_kategori_id.required' => 'Kategori karyawan wajib dipilih.',
            'kepegawaian_master_kategori_id.exists'   => 'Kategori tidak valid.',
            'items.required'                          => 'Tidak ada data karyawan yang diproses.',
        ];
    }
}
