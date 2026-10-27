<?php

namespace App\Http\Requests\Admin\SertifikatPenghargaan;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SertifikatPenghargaanRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diotorisasi untuk melakukan request ini.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Aturan validasi untuk pembuatan dan pembaruan Sertifikat Penghargaan.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isCreate = $this->isMethod('post');
        $maxYear = (int) date('Y') + 1;

        return [
            'karyawan_nipy' => [
                'required',
                'string',
                Rule::exists('kepegawaian_master_karyawan', 'nipy'),
            ],
            'judul_penghargaan' => [
                'required',
                'string',
                'max:255',
            ],
            'tahun' => [
                'required',
                'integer',
                'digits:4',
                'min:1990',
                "max:{$maxYear}",
            ],
            'keterangan' => [
                'nullable',
                'string',
                'max:1000',
            ],
            // Berkas wajib diunggah saat create (store), opsional saat update jika tidak mengganti
            'berkas' => [
                $isCreate ? 'required' : 'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png,doc,docx',
                'max:5120', // Maksimal 5 MB
            ],
        ];
    }

    /**
     * Label kustom atribut dalam bahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'karyawan_nipy'     => 'Karyawan (NIPY)',
            'judul_penghargaan' => 'Judul Penghargaan',
            'tahun'             => 'Tahun Penghargaan',
            'berkas'            => 'Berkas Sertifikat',
            'keterangan'        => 'Catatan / Keterangan',
        ];
    }

    /**
     * Pesan kustom untuk penanganan kegagalan validasi.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'karyawan_nipy.required'     => ':attribute wajib dipilih.',
            'karyawan_nipy.exists'       => ':attribute tidak valid atau tidak terdaftar pada master karyawan.',
            'judul_penghargaan.required' => ':attribute wajib diisi.',
            'judul_penghargaan.max'      => ':attribute maksimal :max karakter.',
            'tahun.required'             => ':attribute wajib diisi.',
            'tahun.digits'               => ':attribute harus berupa 4 digit angka tahun (contoh: 2026).',
            'tahun.min'                  => ':attribute minimal tahun 1990.',
            'tahun.max'                  => ':attribute melebihi batas tahun yang diizinkan.',
            'berkas.required'            => ':attribute wajib diunggah.',
            'berkas.file'                => ':attribute harus berupa file yang valid.',
            'berkas.mimes'               => ':attribute harus berformat PDF, JPG, PNG, atau DOC/DOCX.',
            'berkas.max'                 => ':attribute tidak boleh lebih dari 5 MB (5120 KB).',
        ];
    }

    /**
     * Sanitasi data input sebelum validasi dieksekusi.
     */
    protected function prepareForValidation(): void
    {
        $sanitized = [];

        // Support alternatif nama 'nipy' dari form input
        $nipy = $this->karyawan_nipy ?? $this->nipy;
        if ($nipy !== null) {
            $sanitized['karyawan_nipy'] = trim((string) $nipy);
        }

        if ($this->has('judul_penghargaan')) {
            $sanitized['judul_penghargaan'] = trim(strip_tags((string) $this->judul_penghargaan));
        }

        if ($this->has('tahun') && $this->tahun !== '') {
            $sanitized['tahun'] = (int) $this->tahun;
        }

        if ($this->has('keterangan')) {
            $sanitized['keterangan'] = $this->keterangan ? trim(strip_tags((string) $this->keterangan)) : null;
        }

        $this->merge($sanitized);
    }
}
