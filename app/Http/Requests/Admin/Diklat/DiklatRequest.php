<?php

namespace App\Http\Requests\Admin\Diklat;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DiklatRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diotorisasi untuk melakukan request ini.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Aturan validasi untuk pembuatan dan pembaruan data Diklat.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'karyawan_nipy' => [
                'required',
                'string',
                Rule::exists('kepegawaian_master_karyawan', 'nipy'),
            ],
            'kategori' => [
                'required',
                'string',
                Rule::in(['mandiri', 'lembaga']),
            ],
            'kepegawaian_kegiatan_lembaga_id' => [
                'nullable',
                'integer',
                Rule::exists('kepegawaian_kegiatan_lembaga', 'id'),
            ],
            'nama_kegiatan' => [
                'required',
                'string',
                'max:255',
            ],
            'pelaksana' => [
                'required',
                'string',
                'max:255',
            ],
            'materi' => [
                'required',
                'string',
            ],
            'tanggal' => [
                'required',
                'date',
            ],
            'tanggal_selesai' => [
                'nullable',
                'date',
                'after_or_equal:tanggal',
            ],
            'tempat' => [
                'nullable',
                'string',
                'max:255',
            ],
            'jumlah_jam' => [
                'nullable',
                'integer',
                'min:1',
                'max:999',
            ],
            'keterangan' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'berkas' => [
                'nullable',
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
            'kategori'                        => 'Kategori Diklat',
            'kepegawaian_kegiatan_lembaga_id' => 'Kegiatan Lembaga Terkait',
            'nama_kegiatan'                   => 'Nama Kegiatan Diklat',
            'pelaksana'                       => 'Penyelenggara / Pelaksana',
            'materi'                          => 'Materi / Topik Pelatihan',
            'tanggal'                         => 'Tanggal Pelaksanaan',
            'tanggal_selesai'                 => 'Tanggal Selesai',
            'tempat'                          => 'Tempat / Lokasi',
            'jumlah_jam'                      => 'Jumlah Jam (JP)',
            'berkas'                          => 'Berkas Sertifikat / Bukti Diklat',
            'keterangan'                      => 'Keterangan Tambahan',
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
            'required'               => ':attribute wajib diisi.',
            'exists'                 => ':attribute yang dipilih tidak valid atau tidak terdaftar.',
            'in'                     => ':attribute harus berupa Mandiri atau Lembaga.',
            'after_or_equal'         => ':attribute tidak boleh sebelum tanggal mulai.',
            'file'                   => ':attribute harus berupa dokumen file.',
            'mimes'                  => ':attribute harus berekstensi: :values.',
            'berkas.max'             => 'Ukuran file sertifikat maksimal 10 Megabytes.',
        ];
    }

    /**
     * Sanitasi dan persiapan data sebelum validasi.
     */
    protected function prepareForValidation(): void
    {
        $sanitized = [];

        if ($this->has('kategori')) {
            $sanitized['kategori'] = strtolower(trim((string) $this->kategori));
        }

        if ($this->has('nama_kegiatan')) {
            $sanitized['nama_kegiatan'] = trim((string) $this->nama_kegiatan);
        }

        if ($this->has('pelaksana')) {
            $sanitized['pelaksana'] = trim((string) $this->pelaksana);
        }

        if ($this->has('kepegawaian_kegiatan_lembaga_id') && $this->kepegawaian_kegiatan_lembaga_id !== '') {
            $sanitized['kepegawaian_kegiatan_lembaga_id'] = (int) $this->kepegawaian_kegiatan_lembaga_id;
        } else {
            $sanitized['kepegawaian_kegiatan_lembaga_id'] = null;
        }

        if ($this->has('jumlah_jam') && $this->jumlah_jam !== '') {
            $sanitized['jumlah_jam'] = (int) $this->jumlah_jam;
        }

        $this->merge($sanitized);
    }
}
