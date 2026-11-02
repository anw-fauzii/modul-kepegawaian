<?php

namespace App\Http\Requests\Admin\DataMaster;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HariLiburRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('is_libur_nasional')) {
            $this->merge([
                'is_libur_nasional' => filter_var($this->input('is_libur_nasional'), FILTER_VALIDATE_BOOLEAN),
            ]);
        } else if ($this->isMethod('post')) {
            $this->merge([
                'is_libur_nasional' => true,
            ]);
        }
    }

    public function rules(): array
    {
        $hariLibur = $this->route('hari_libur');
        $hariLiburId = is_object($hariLibur) ? $hariLibur->id : $hariLibur;

        return [
            'tanggal' => [
                'required',
                'date',
                Rule::unique('kepegawaian_master_hari_libur', 'tanggal')->ignore($hariLiburId),
            ],
            'nama' => [
                'required',
                'string',
                'max:255',
            ],
            'keterangan' => [
                'nullable',
                'string',
                'max:500',
            ],
            'is_libur_nasional' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'tanggal.required' => 'Tanggal hari libur wajib diisi.',
            'tanggal.date'     => 'Format tanggal tidak valid.',
            'tanggal.unique'   => 'Hari libur pada tanggal ini sudah tercatat.',
            'nama.required'    => 'Nama hari libur wajib diisi.',
        ];
    }
}
