<?php

namespace App\Http\Requests\Admin\Kegiatan;

use Illuminate\Foundation\Http\FormRequest;

class ScanQrKegiatanRequest extends FormRequest
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
            'qr_token'      => ['required', 'string', 'size:64', 'exists:kepegawaian_kegiatan_lembaga,qr_token'],
            'karyawan_nipy' => ['nullable', 'string', 'exists:kepegawaian_master_karyawan,nipy'],
            'catatan'       => ['nullable', 'string', 'max:255'],
            'device_info'   => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Custom attributes.
     */
    public function attributes(): array
    {
        return [
            'qr_token'      => 'QR Token Kegiatan',
            'karyawan_nipy' => 'NIPY Karyawan',
            'catatan'       => 'Catatan Presensi',
            'device_info'   => 'Informasi Perangkat',
        ];
    }

    /**
     * Prepare data: jika karyawan_nipy tidak dikirim via payload,
     * otomatis fallback ke user/karyawan yang sedang login (jika ada).
     */
    protected function prepareForValidation(): void
    {
        $nipy = $this->karyawan_nipy;

        if (empty($nipy) && auth()->check()) {
            $user = auth()->user();
            // Cek apakah user memiliki no_induk / nipy
            $nipy = $user->no_induk ?? $user->nipy ?? null;
        }

        $rawToken = trim($this->qr_token ?? '');
        if (str_contains($rawToken, '/')) {
            $pathSegments = explode('/', trim(parse_url($rawToken, PHP_URL_PATH) ?? '', '/'));
            $rawToken = end($pathSegments);
        }

        $this->merge([
            'karyawan_nipy' => $nipy ? trim($nipy) : null,
            'qr_token'      => $rawToken,
            'device_info'   => $this->device_info ?: request()->userAgent(),
        ]);
    }
}
