<?php

namespace App\Http\Requests\Admin\DataMaster;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class JenisCutiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Pastikan status aktif menjadi boolean (misal jika dari checkbox/select)
        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => filter_var($this->input('is_active'), FILTER_VALIDATE_BOOLEAN),
            ]);
        } else if ($this->isMethod('post')) {
            $this->merge([
                'is_active' => true,
            ]);
        }
    }

    public function rules(): array
    {
        $jenisCuti = $this->route('jenis_cuti');
        $jenisCutiId = is_object($jenisCuti) ? $jenisCuti->id : $jenisCuti;

        return [
            'nama' => [
                'required',
                'string',
                'max:255',
                Rule::unique('kepegawaian_master_jenis_cuti', 'nama')->ignore($jenisCutiId),
            ],
            'lama_hari' => [
                'required',
                'integer',
                'min:1',
                'max:365',
            ],
            'keterangan' => [
                'nullable',
                'string',
                'max:500',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required'      => 'Nama jenis cuti wajib diisi.',
            'nama.unique'        => 'Nama jenis cuti ini sudah terdaftar.',
            'lama_hari.required' => 'Lama hari cuti wajib diisi.',
            'lama_hari.integer'  => 'Lama hari harus berupa bilangan bulat angka.',
            'lama_hari.min'      => 'Lama hari minimal 1 hari.',
            'lama_hari.max'      => 'Lama hari maksimal 365 hari.',
        ];
    }
}
