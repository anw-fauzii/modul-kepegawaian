<?php

namespace App\Http\Requests\Admin\Kegiatan;

use Illuminate\Foundation\Http\FormRequest;

class KehadiranManualRequest extends FormRequest
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
            'karyawan_nipy'   => ['required', 'array', 'min:1'],
            'karyawan_nipy.*' => ['required', 'string', 'exists:kepegawaian_master_karyawan,nipy'],
            'status'          => ['required', 'in:hadir,izin,sakit,alpa'],
            'catatan'         => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Custom attributes.
     */
    public function attributes(): array
    {
        return [
            'karyawan_nipy'   => 'Daftar Karyawan (NIPY)',
            'karyawan_nipy.*' => 'NIPY Karyawan',
            'status'          => 'Status Kehadiran',
            'catatan'         => 'Catatan Tambahan',
        ];
    }

    /**
     * Normalize single NIPY into an array if needed.
     */
    protected function prepareForValidation(): void
    {
        $nipy = $this->karyawan_nipy;
        if (is_string($nipy)) {
            $this->merge([
                'karyawan_nipy' => [$nipy],
            ]);
        }
    }
}
