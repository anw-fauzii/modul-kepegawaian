<?php

namespace App\Http\Requests\Admin\Fasilitas;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FasilitasRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diotorisasi untuk melakukan request ini.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Aturan validasi yang berlaku untuk request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $currentYear = (int) date('Y');

        return [
            'karyawan_nipy' => [
                'required',
                'string',
                Rule::exists('kepegawaian_master_karyawan', 'nipy'),
            ],
            'nama_fasilitas' => [
                'required',
                'string',
                'max:255',
            ],
            'jumlah' => [
                'required',
                'integer',
                'min:1',
                'max:1000',
            ],
            'tahun_terima' => [
                'required',
                'integer',
                'digits:4',
                'min:1990',
                "max:{$currentYear}",
            ],
            'status' => [
                'sometimes',
                'boolean',
            ],
            'tanggal_kembali' => [
                'nullable',
                'date',
            ],
            'catatan' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    /**
     * Label nama atribut kustom untuk pesan validasi.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'karyawan_nipy'   => 'Karyawan (NIPY)',
            'nama_fasilitas'  => 'Nama Fasilitas',
            'jumlah'          => 'Jumlah Fasilitas',
            'tahun_terima'    => 'Tahun Terima',
            'status'          => 'Status Pengembalian',
            'tanggal_kembali' => 'Tanggal Pengembalian',
            'catatan'         => 'Catatan / Keterangan',
        ];
    }

    /**
     * Pesan kustom untuk kegagalan validasi.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'karyawan_nipy.required'  => ':attribute wajib dipilih.',
            'karyawan_nipy.exists'    => ':attribute tidak terdaftar pada master karyawan.',
            'nama_fasilitas.required' => ':attribute wajib diisi.',
            'jumlah.required'         => ':attribute wajib diisi.',
            'jumlah.min'              => ':attribute minimal 1 unit.',
            'tahun_terima.required'   => ':attribute wajib diisi.',
            'tahun_terima.digits'     => ':attribute harus berupa format 4 digit tahun (contoh: 2024).',
            'tahun_terima.min'        => ':attribute tidak valid (minimal tahun 1990).',
            'tahun_terima.max'        => ':attribute tidak boleh melebihi tahun saat ini.',
        ];
    }

    /**
     * Sanitasi data input sebelum validasi dieksekusi.
     */
    protected function prepareForValidation(): void
    {
        $sanitized = [];

        // Support karyawan_nipy maupun alias nipy dari payload
        $nipy = $this->karyawan_nipy ?? $this->nipy;
        if ($nipy !== null) {
            $sanitized['karyawan_nipy'] = trim((string) $nipy);
        }

        if ($this->has('nama_fasilitas')) {
            $sanitized['nama_fasilitas'] = trim(strip_tags((string) $this->nama_fasilitas));
        }

        if ($this->has('catatan')) {
            $sanitized['catatan'] = $this->catatan ? trim(strip_tags((string) $this->catatan)) : null;
        }

        // Normalisasi status boolean (HTML form checkbox sering mengirim 'on' / '1' / '0')
        if ($this->has('status')) {
            $sanitized['status'] = filter_var($this->status, FILTER_VALIDATE_BOOLEAN);
        } else {
            $sanitized['status'] = false;
        }

        $this->merge($sanitized);
    }
}
