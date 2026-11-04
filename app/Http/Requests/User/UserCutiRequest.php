<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserCutiRequest extends FormRequest
{
    /**
     * Pastikan request hanya dapat dieksekusi oleh user yang terautentikasi dengan role user.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->hasRole('user');
    }

    /**
     * Aturan validasi permohonan cuti pegawai.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kepegawaian_master_jenis_cuti_id' => [
                'required',
                'integer',
                'exists:kepegawaian_master_jenis_cuti,id',
            ],
            'awal_cuti' => [
                'required',
                'date',
            ],
            'akhir_cuti' => [
                'required',
                'date',
                'after_or_equal:awal_cuti',
            ],
            'alamat' => [
                'required',
                'string',
                'max:500',
            ],
            'telepon' => [
                'required',
                'string',
                'max:50',
            ],
            'alasan' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    /**
     * Nama atribut kustom untuk pesan validasi.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'kepegawaian_master_jenis_cuti_id' => 'kategori jenis cuti',
            'awal_cuti'                        => 'tanggal awal cuti',
            'akhir_cuti'                       => 'tanggal akhir cuti',
            'alamat'                           => 'alamat selama cuti',
            'telepon'                          => 'nomor telepon / WhatsApp',
            'alasan'                           => 'alasan permohonan cuti',
        ];
    }

    /**
     * Pesan error kustom bahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'kepegawaian_master_jenis_cuti_id.required' => 'Kategori jenis cuti wajib dipilih.',
            'kepegawaian_master_jenis_cuti_id.exists'   => 'Kategori jenis cuti yang dipilih tidak valid.',
            'awal_cuti.required'                        => 'Tanggal awal mulai cuti wajib diisi.',
            'awal_cuti.date'                            => 'Format tanggal awal mulai cuti tidak valid.',
            'akhir_cuti.required'                       => 'Tanggal akhir cuti wajib diisi.',
            'akhir_cuti.date'                           => 'Format tanggal akhir cuti tidak valid.',
            'akhir_cuti.after_or_equal'                 => 'Tanggal akhir cuti harus sama atau setelah tanggal awal cuti.',
            'alamat.required'                           => 'Alamat selama menjalankan cuti wajib diisi.',
            'telepon.required'                          => 'Nomor telepon / WhatsApp aktif wajib diisi.',
        ];
    }
}

