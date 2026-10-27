<?php

namespace App\Http\Requests\Admin\Kegiatan;

use Illuminate\Foundation\Http\FormRequest;

class KegiatanLembagaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama_kegiatan'                  => ['required', 'string', 'max:255'],
            'jenis_kegiatan'                 => ['required', 'string', 'in:pelatihan,rapat,seminar,pembinaan,lainnya'],
            'pengisi'                        => ['nullable', 'string', 'max:255'],
            'tanggal'                        => ['required', 'date'],
            'jam_mulai'                      => ['nullable', 'date_format:H:i'],
            'jam_selesai'                    => ['nullable', 'date_format:H:i', 'after_or_equal:jam_mulai'],
            'lokasi'                         => ['nullable', 'string', 'max:255'],
            'akademik_master_tahun_ajaran_id'=> ['required', 'integer', 'exists:akademik_master_tahun_ajaran,id'],
            'is_active'                      => ['sometimes', 'boolean'],
            'deskripsi'                      => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nama_kegiatan'                  => 'Nama Kegiatan',
            'pengisi'                        => 'Pengisi / Narasumber',
            'tanggal'                        => 'Tanggal Kegiatan',
            'jam_mulai'                      => 'Jam Mulai',
            'jam_selesai'                    => 'Jam Selesai',
            'lokasi'                         => 'Lokasi Kegiatan',
            'akademik_master_tahun_ajaran_id'=> 'Tahun Ajaran',
            'is_active'                      => 'Status Aktif Presensi',
            'deskripsi'                      => 'Deskripsi Kegiatan',
        ];
    }

    /**
     * Prepare data for validation (sanitasi input).
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'nama_kegiatan' => trim(strip_tags($this->nama_kegiatan ?? '')),
            'pengisi'       => $this->pengisi ? trim(strip_tags($this->pengisi)) : null,
            'lokasi'        => $this->lokasi ? trim(strip_tags($this->lokasi)) : null,
            'is_active'     => $this->boolean('is_active', true),
        ]);
    }
}
