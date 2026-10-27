<?php

namespace App\Http\Requests\Admin\SuratKeputusan;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SuratKeputusanRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diotorisasi untuk melakukan request ini.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Aturan validasi yang berlaku untuk request pembuatan atau pembaruan Surat Keputusan.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isCreate = $this->isMethod('post');

        return [
            'karyawan_nipy' => [
                'required',
                'string',
                Rule::exists('kepegawaian_master_karyawan', 'nipy'),
            ],
            'akademik_master_tahun_ajaran_id' => [
                'required',
                'integer',
                Rule::exists('akademik_master_tahun_ajaran', 'id'),
            ],
            'jabatan' => [
                'required',
                'string',
                'max:150',
            ],
            'nomor_sk' => [
                'nullable',
                'string',
                'max:100',
            ],
            'tanggal_sk' => [
                'nullable',
                'date',
            ],
            'keterangan' => [
                'nullable',
                'string',
                'max:1000',
            ],
            // Berkas wajib diunggah pada saat create (store), namun opsional pada saat update
            'berkas' => [
                $isCreate ? 'required' : 'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png,doc,docx',
                'max:5120', // Maksimal 5 MB
            ],
        ];
    }

    /**
     * Nama atribut yang mudah dibaca pengguna dalam bahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'karyawan_nipy'                   => 'Karyawan (NIPY)',
            'akademik_master_tahun_ajaran_id' => 'Tahun Ajaran',
            'jabatan'                         => 'Jabatan SK',
            'nomor_sk'                        => 'Nomor Surat Keputusan',
            'tanggal_sk'                      => 'Tanggal SK',
            'keterangan'                      => 'Keterangan',
            'berkas'                          => 'Berkas Dokumen SK',
        ];
    }

    /**
     * Pesan kustom untuk penanganan validasi input.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'karyawan_nipy.required'                   => ':attribute wajib dipilih.',
            'karyawan_nipy.exists'                     => ':attribute tidak valid atau tidak terdaftar pada master karyawan.',
            'akademik_master_tahun_ajaran_id.required' => ':attribute wajib dipilih.',
            'akademik_master_tahun_ajaran_id.exists'   => ':attribute tidak valid atau tidak ditemukan.',
            'jabatan.required'                         => ':attribute wajib diisi.',
            'jabatan.max'                              => ':attribute maksimal :max karakter.',
            'berkas.required'                          => ':attribute wajib diunggah.',
            'berkas.file'                              => ':attribute harus berupa file yang valid.',
            'berkas.mimes'                             => ':attribute harus berformat PDF, JPG, PNG, atau DOC/DOCX.',
            'berkas.max'                               => ':attribute tidak boleh lebih dari 5 MB (5120 KB).',
            'tanggal_sk.date'                          => ':attribute harus berupa format tanggal yang valid.',
        ];
    }

    /**
     * Sanitasi data input sebelum validasi dieksekusi.
     */
    protected function prepareForValidation(): void
    {
        $sanitized = [];

        // Support alternatif nama key nipy dari form frontend
        $nipy = $this->karyawan_nipy ?? $this->nipy;
        if ($nipy !== null) {
            $sanitized['karyawan_nipy'] = trim((string) $nipy);
        }

        if ($this->has('akademik_master_tahun_ajaran_id')) {
            $sanitized['akademik_master_tahun_ajaran_id'] = (int) $this->akademik_master_tahun_ajaran_id;
        }

        if ($this->has('jabatan')) {
            $sanitized['jabatan'] = trim(strip_tags((string) $this->jabatan));
        }

        if ($this->has('nomor_sk')) {
            $sanitized['nomor_sk'] = $this->nomor_sk ? trim(strip_tags((string) $this->nomor_sk)) : null;
        }

        if ($this->has('keterangan')) {
            $sanitized['keterangan'] = $this->keterangan ? trim(strip_tags((string) $this->keterangan)) : null;
        }

        $this->merge($sanitized);
    }
}
