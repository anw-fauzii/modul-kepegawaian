<?php

namespace App\Http\Requests\Admin\Presensi;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PresensiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'izin_terlambat'    => $this->boolean('izin_terlambat'),
            'izin_pulang_cepat' => $this->boolean('izin_pulang_cepat'),
            'catatan'           => $this->filled('catatan') ? strip_tags(trim($this->catatan)) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'karyawan_nipy'                => ['required', 'string', Rule::exists('kepegawaian_master_karyawan', 'nipy')],
            'kepegawaian_master_bulan_id'  => ['nullable', Rule::exists('kepegawaian_master_bulan', 'id')],
            'tanggal'                      => ['required', 'date'],
            'status'                       => ['required', Rule::in(['hadir', 'terlambat', 'izin', 'sakit', 'cuti', 'alpa', 'tugas'])],
            'jam_masuk'                    => ['nullable', 'date_format:H:i'],
            'jam_pulang'                   => ['nullable', 'date_format:H:i'],
            'izin_terlambat'               => ['boolean'],
            'izin_pulang_cepat'            => ['boolean'],
            'catatan'                      => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'karyawan_nipy.required'               => 'Karyawan wajib dipilih.',
            'karyawan_nipy.exists'                 => 'Data karyawan tidak ditemukan dalam sistem.',
            'kepegawaian_master_bulan_id.exists'   => 'Periode bulan tidak valid.',
            'tanggal.required'                     => 'Tanggal presensi wajib diisi.',
            'tanggal.date'                         => 'Format tanggal tidak valid.',
            'status.required'                      => 'Status presensi wajib ditentukan.',
            'status.in'                            => 'Status presensi harus salah satu dari: Hadir, Terlambat, Izin, Sakit, Cuti, Alpa, atau Tugas.',
            'jam_masuk.date_format'                => 'Format jam masuk harus berupa JJ:MM (contoh: 07:15).',
            'jam_pulang.date_format'               => 'Format jam pulang harus berupa JJ:MM (contoh: 16:00).',
        ];
    }
}
