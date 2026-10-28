<?php

namespace App\Http\Requests\Admin\Penggajian;

use App\Models\Admin\Penggajian\Penggajian;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PenggajianFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search'                      => ['nullable', 'string', 'max:100'],
            'kepegawaian_master_bulan_id' => ['nullable', 'integer'],
            'status'                      => ['nullable', Rule::in(array_keys(Penggajian::STATUS))],
            'per_page'                    => ['nullable', 'integer', 'min:5', 'max:100'],
        ];
    }
}
