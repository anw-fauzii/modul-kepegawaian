<?php

namespace App\Http\Requests\Admin\DataMaster;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['status' => $this->boolean('status')]);
    }

    public function rules(): array
    {
        $bulanId = $this->route('bulan');

        return [
            'nama_bulan' => [
                'required',
                'string',
                'max:255',
                // Nama bulan unik per tahun ajaran
                Rule::unique('kepegawaian_master_bulan', 'nama_bulan')
                    ->where('akademik_master_tahun_ajaran_id', $this->input('akademik_master_tahun_ajaran_id'))
                    ->ignore($bulanId),
            ],
            'akademik_master_tahun_ajaran_id' => [
                'required',
                Rule::exists('akademik_master_tahun_ajaran', 'id'),
            ],
            'status' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_bulan.required' => 'Nama Bulan wajib diisi.',
            'nama_bulan.unique'   => 'Nama Bulan ini sudah ada pada Tahun Ajaran tersebut.',
            'akademik_master_tahun_ajaran_id.required' => 'Tahun Ajaran wajib dipilih.',
            'akademik_master_tahun_ajaran_id.exists'   => 'Tahun Ajaran tidak valid.',
        ];
    }
}
