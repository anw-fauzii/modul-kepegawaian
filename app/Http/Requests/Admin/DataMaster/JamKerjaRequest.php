<?php

namespace App\Http\Requests\Admin\DataMaster;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class JamKerjaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $jamKerjaId = $this->route('jam_kerja');

        return [
            'nama' => [
                'required',
                'string',
                'max:255',
                Rule::unique('kepegawaian_master_jam_kerja', 'nama')->ignore($jamKerjaId),
            ],
            'jam_masuk'              => ['required', 'date_format:H:i'],
            'jam_pulang_senin_kamis' => ['required', 'date_format:H:i', 'after:jam_masuk'],
            'jam_pulang_jumat'       => ['required', 'date_format:H:i', 'after:jam_masuk'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama jam kerja wajib diisi.',
            'nama.unique'   => 'Nama jam kerja ini sudah ada dalam sistem.',
            'jam_masuk.required' => 'Jam masuk wajib diisi.',
            'jam_masuk.date_format' => 'Format jam masuk harus HH:MM.',
            'jam_pulang_senin_kamis.required' => 'Jam pulang Senin - Kamis wajib diisi.',
            'jam_pulang_senin_kamis.date_format' => 'Format jam pulang Senin - Kamis harus HH:MM.',
            'jam_pulang_senin_kamis.after' => 'Jam pulang Senin - Kamis harus setelah jam masuk.',
            'jam_pulang_jumat.required' => 'Jam pulang Jumat wajib diisi.',
            'jam_pulang_jumat.date_format' => 'Format jam pulang Jumat harus HH:MM.',
            'jam_pulang_jumat.after' => 'Jam pulang Jumat harus setelah jam masuk.',
        ];
    }
}
