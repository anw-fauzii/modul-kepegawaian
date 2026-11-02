<?php

namespace App\Http\Requests\Admin\Presensi;

use Illuminate\Foundation\Http\FormRequest;

class PresensiShaumRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('shaum')) {
            $this->merge([
                'shaum' => $this->boolean('shaum'),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'shaum' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'shaum.required' => 'Status shaum wajib ditentukan (true/false).',
            'shaum.boolean'  => 'Format status shaum harus berupa boolean (true atau false).',
        ];
    }
}
