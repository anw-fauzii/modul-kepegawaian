<?php

namespace App\Http\Requests\Admin\RapotKinerja;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RapotKinerjaRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diotorisasi untuk melakukan request ini.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Aturan validasi untuk pembuatan dan pembaruan Rapot Kinerja.
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
            'keterangan' => [
                'nullable',
                'string',
                'max:1000',
            ],
            // Berkas wajib pada saat create (store), opsional pada update
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
            'karyawan_nipy'                   => 'Karyawan (NIPY)',
            'akademik_master_tahun_ajaran_id' => 'Tahun Ajaran',
            'berkas'                          => 'Berkas Rapot Kinerja',
            'keterangan'                      => 'Catatan / Keterangan',
        ];
    }

    /**
     * Pesan kustom kegagalan validasi.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'karyawan_nipy.required'                   => ':attribute wajib dipilih.',
            'karyawan_nipy.exists'                     => ':attribute tidak terdaftar pada master karyawan.',
            'akademik_master_tahun_ajaran_id.required' => ':attribute wajib dipilih.',
            'akademik_master_tahun_ajaran_id.exists'   => ':attribute tidak valid atau tidak ditemukan.',
            'berkas.required'                          => ':attribute wajib diunggah.',
            'berkas.file'                              => ':attribute harus berupa file yang valid.',
            'berkas.mimes'                             => ':attribute harus berformat PDF, JPG, PNG, atau DOC/DOCX.',
            'berkas.max'                               => ':attribute tidak boleh lebih dari 5 MB (5120 KB).',
        ];
    }

    /**
     * Sanitasi data input sebelum validasi dieksekusi.
     */
    protected function prepareForValidation(): void
    {
        $sanitized = [];

        // Support alternatif nama nipy jika dikirim dari input selector
        $nipy = $this->karyawan_nipy ?? $this->nipy;
        if ($nipy !== null) {
            $sanitized['karyawan_nipy'] = trim((string) $nipy);
        }

        if ($this->has('akademik_master_tahun_ajaran_id')) {
            $sanitized['akademik_master_tahun_ajaran_id'] = (int) $this->akademik_master_tahun_ajaran_id;
        }

        if ($this->has('keterangan')) {
            $sanitized['keterangan'] = $this->keterangan ? trim(strip_tags((string) $this->keterangan)) : null;
        }

        $this->merge($sanitized);
    }
}
