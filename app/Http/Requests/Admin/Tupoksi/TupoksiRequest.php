<?php

namespace App\Http\Requests\Admin\Tupoksi;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TupoksiRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diotorisasi untuk melakukan request ini.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Aturan validasi untuk pembuatan dan pembaruan Tupoksi.
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
            'jabatan' => [
                'required',
                'string',
                'max:255',
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
                'max:10240', // Maksimal 10 MB
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
            'jabatan'                         => 'Jabatan / Tugas',
            'akademik_master_tahun_ajaran_id' => 'Tahun Ajaran',
            'berkas'                          => 'Berkas Dokumen Tupoksi',
            'keterangan'                      => 'Keterangan / Uraian Tugas',
        ];
    }

    /**
     * Pesan validasi kustom dalam bahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi atau dipilih.',
            'exists'   => ':attribute yang dipilih tidak valid atau tidak ditemukan di sistem.',
            'max'      => ':attribute tidak boleh lebih dari :max karakter.',
            'file'     => ':attribute harus berupa file yang valid.',
            'mimes'    => ':attribute harus berformat: :values.',
            'berkas.max' => 'Ukuran berkas tidak boleh melebihi 10 Megabytes.',
        ];
    }

    /**
     * Persiapan dan sanitasi data input sebelum validasi.
     */
    protected function prepareForValidation(): void
    {
        $sanitized = [];

        if ($this->has('jabatan')) {
            $sanitized['jabatan'] = trim((string) $this->jabatan);
        }

        if ($this->has('keterangan')) {
            $sanitized['keterangan'] = trim((string) $this->keterangan);
        }

        if ($this->has('akademik_master_tahun_ajaran_id')) {
            $sanitized['akademik_master_tahun_ajaran_id'] = (int) $this->akademik_master_tahun_ajaran_id;
        }

        $this->merge($sanitized);
    }
}
