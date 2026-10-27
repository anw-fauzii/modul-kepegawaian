<?php

namespace App\Http\Requests\Admin\DataMaster;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KaryawanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['sertifikasi' => $this->boolean('sertifikasi')]);
    }

    public function rules(): array
    {
        $nipyRules = ['required', 'string', 'max:50'];
        $emailRules = ['required', 'string', 'max:50'];

        if ($this->isMethod('POST')) {
            $nipyRules[] = Rule::unique('kepegawaian_master_karyawan', 'nipy');
            $nipyRules[] = Rule::unique('users', 'no_induk');
            $emailRules[] = Rule::unique('users', 'email');
        }  

        return [
            'nipy'          => $nipyRules,
            'email'         => $emailRules,
            'nama_lengkap'  => ['required', 'string', 'max:255'],
            'gelar'         => ['nullable', 'string', 'max:255'],
            'kepegawaian_master_kategori_id' => ['required', Rule::exists('kepegawaian_master_kategori', 'id')],
            'tempat_lahir'  => ['required', 'string', 'max:255'],
            'tanggal_lahir' => ['required', 'date', 'before:today'],
            'alamat'        => ['required', 'string'],
            'rt'            => ['required', 'string', 'max:5'],
            'rw'            => ['required', 'string', 'max:5'],
            'kelurahan'     => ['required', 'string', 'max:255'],
            'kecamatan'     => ['required', 'string', 'max:255'],
            'kabupaten'     => ['required', 'string', 'max:255'],
            'jk'            => ['required', Rule::in(['L', 'P'])],
            'jabatan'       => ['required', 'string', 'max:255'],
            'golongan'      => ['nullable', 'string', 'max:255'],
            'unit'          => ['required', 'string', 'max:255'],
            'mulai_tugas'   => ['required', 'date'],
            'akademik_pelengkap_jenjang_pendidikan_id' => [
                'required',
                Rule::exists('akademik_pelengkap_jenjang_pendidikan', 'id'),
            ],
            'prodi'         => ['nullable', 'string', 'max:255'],
            'tahun_lulus'   => ['required', 'digits:4'],
            'whatsapp'      => ['nullable', 'string', 'max:20'],
            'nuptk'         => ['nullable', 'string', 'max:255'],
            'sertifikasi'   => ['boolean'],
            'nik'           => ['nullable', 'string', 'max:20'],
            'kepegawaian_master_jam_kerja_id' => ['required', Rule::exists('kepegawaian_master_jam_kerja', 'id')],
        ];
    }

    public function messages(): array
    {
        return [
            'nipy.required'  => 'NIPY wajib diisi.',
            'nipy.unique'    => 'NIPY ini sudah ada dalam sistem.',
            'email.required' => 'Email wajib diisi.',
            'email.unique'   => 'Email ini sudah ada dalam sistem.',    
            'nama_lengkap.required' => 'Nama Lengkap wajib diisi.',
            'kepegawaian_master_kategori_id.required' => 'Kategori wajib dipilih.',
            'tempat_lahir.required'  => 'Tempat Lahir wajib diisi.',
            'tanggal_lahir.required' => 'Tanggal Lahir wajib diisi.',
            'alamat.required'        => 'Alamat wajib diisi.',
            'rt.required'            => 'RT wajib diisi.',
            'rw.required'            => 'RW wajib diisi.',
            'kelurahan.required'     => 'Kelurahan wajib diisi.',
            'kecamatan.required'     => 'Kecamatan wajib diisi.',
            'kabupaten.required'     => 'Kabupaten wajib diisi.',
            'jk.required'            => 'Jenis Kelamin wajib dipilih.',
            'jabatan.required'       => 'Jabatan wajib diisi.',
            'unit.required'          => 'Unit wajib diisi.',
            'mulai_tugas.required'   => 'Mulai Tugas wajib diisi.',
            'akademik_pelengkap_jenjang_pendidikan_id.required' => 'Jenjang Pendidikan wajib dipilih.',
            'tahun_lulus.required'   => 'Tahun Lulus wajib diisi.',
            'tahun_lulus.digits'     => 'Tahun Lulus harus 4 digit.',
            'kepegawaian_master_jam_kerja_id.required' => 'Jam Kerja wajib dipilih.',
        ];
    }
}
