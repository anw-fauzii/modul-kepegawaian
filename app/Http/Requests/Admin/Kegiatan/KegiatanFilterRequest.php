<?php

namespace App\Http\Requests\Admin\Kegiatan;

use Illuminate\Foundation\Http\FormRequest;

class KegiatanFilterRequest extends FormRequest
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
            'search'                          => ['nullable', 'string', 'max:100'],
            'tanggal_mulai'                   => ['nullable', 'date'],
            'tanggal_selesai'                 => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'akademik_master_tahun_ajaran_id' => ['nullable', 'integer', 'exists:akademik_master_tahun_ajaran,id'],
            'is_active'                       => ['nullable', 'in:0,1'],
            'per_page'                        => ['nullable', 'integer', 'min:5', 'max:100'],
        ];
    }
}
