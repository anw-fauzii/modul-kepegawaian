<?php

namespace App\Http\Requests\Admin\Penggajian;

use App\Models\Admin\Penggajian\Penggajian;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PenggajianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Kolom nominal yang dikosongkan dianggap 0.
     */
    protected function prepareForValidation(): void
    {
        $defaults = [];
        foreach (array_merge(array_keys(Penggajian::PENERIMAAN), array_keys(Penggajian::POTONGAN)) as $field) {
            $value = $this->input($field);
            $defaults[$field] = ($value === null || $value === '') ? 0 : $value;
        }

        $this->merge($defaults);
    }

    public function rules(): array
    {
        $current = $this->route('penggajian');
        $currentId = $current instanceof Penggajian ? $current->id : $current;

        $rules = [
            'karyawan_nipy' => [
                'required',
                Rule::exists('kepegawaian_master_karyawan', 'nipy'),
            ],
            'kepegawaian_master_bulan_id' => [
                'required',
                Rule::exists('kepegawaian_master_bulan', 'id'),
                Rule::unique('kepegawaian_keuangan_penggajian', 'kepegawaian_master_bulan_id')
                    ->where('karyawan_nipy', $this->input('karyawan_nipy'))
                    ->where('jenis', $this->input('jenis', 'gaji'))
                    ->ignore($currentId),
            ],
            'jenis'    => ['required', Rule::in(array_keys(Penggajian::JENIS))],
            'unit'     => ['nullable', 'string', 'max:255'],
            'jabatan'  => ['nullable', 'string', 'max:255'],
            'golongan' => ['nullable', 'string', 'max:255'],
            'status'   => ['required', Rule::in(array_keys(Penggajian::STATUS))],
        ];

        foreach (array_keys(Penggajian::PENERIMAAN) as $field) {
            // Pembulatan boleh bernilai negatif
            $rules[$field] = $field === 'pembulatan'
                ? ['required', 'numeric', 'between:-9999999999999,9999999999999']
                : ['required', 'numeric', 'min:0', 'max:9999999999999'];
        }

        foreach (array_keys(Penggajian::POTONGAN) as $field) {
            $rules[$field] = ['required', 'numeric', 'min:0', 'max:9999999999999'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'karyawan_nipy.required'                => 'Karyawan wajib dipilih.',
            'karyawan_nipy.exists'                  => 'Karyawan tidak ditemukan.',
            'kepegawaian_master_bulan_id.required'  => 'Periode bulan wajib dipilih.',
            'kepegawaian_master_bulan_id.unique'    => 'Slip gaji karyawan ini untuk bulan tersebut sudah ada.',
            'status.required'                       => 'Status wajib dipilih.',
            '*.numeric'                             => 'Nominal harus berupa angka.',
            '*.min'                                 => 'Nominal tidak boleh negatif.',
        ];
    }
}
