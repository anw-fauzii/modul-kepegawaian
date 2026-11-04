<?php

namespace App\Http\Requests\Admin\Cuti;

use App\Models\Admin\Cuti\Cuti;
use Illuminate\Foundation\Http\FormRequest;

class CutiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $mergeData = [];

        if ($this->has('acc')) {
            $mergeData['acc'] = filter_var($this->input('acc'), FILTER_VALIDATE_BOOLEAN);
        }

        $jenisCutiId = $this->input('kepegawaian_master_jenis_cuti_id');
        $isMelahirkan = false;
        if ($jenisCutiId) {
            $jc = \App\Models\Admin\DataMaster\JenisCuti::find($jenisCutiId);
            $isMelahirkan = $jc?->isMelahirkan() ?? false;
        }

        if ($this->filled(['awal_cuti', 'akhir_cuti'])) {
            $mergeData['jumlah_hari'] = Cuti::hitungJumlahHari(
                $this->input('awal_cuti'),
                $this->input('akhir_cuti'),
                null,
                $isMelahirkan
            );
        } elseif ($this->filled(['awal_cuti', 'jumlah_hari']) && ! $this->filled('akhir_cuti')) {
            $mergeData['akhir_cuti'] = Cuti::hitungAkhirCuti(
                $this->input('awal_cuti'),
                (int) $this->input('jumlah_hari'),
                null,
                $isMelahirkan
            )->toDateString();
        }

        if (! empty($mergeData)) {
            $this->merge($mergeData);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'karyawan_nipy' => [
                'required',
                'string',
                'exists:kepegawaian_master_karyawan,nipy',
            ],
            'kepegawaian_master_jenis_cuti_id' => [
                'required',
                'integer',
                'exists:kepegawaian_master_jenis_cuti,id',
            ],
            'awal_cuti' => [
                'required',
                'date',
            ],
            'akhir_cuti' => [
                'required',
                'date',
                'after_or_equal:awal_cuti',
            ],
            'alamat' => [
                'required',
                'string',
                'max:500',
            ],
            'telepon' => [
                'required',
                'string',
                'max:50',
            ],
            'alasan' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'acc' => [
                'nullable',
                'boolean',
            ],
            'catatan_acc' => [
                'nullable',
                'string',
                'max:500',
            ],
            'jumlah_hari' => [
                'nullable',
                'integer',
                'min:1',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'karyawan_nipy.required'                    => 'Pegawai pemohon cuti wajib dipilih.',
            'karyawan_nipy.exists'                      => 'Pegawai yang dipilih tidak ditemukan dalam data karyawan.',
            'kepegawaian_master_jenis_cuti_id.required' => 'Jenis cuti wajib dipilih.',
            'kepegawaian_master_jenis_cuti_id.exists'   => 'Jenis cuti yang dipilih tidak valid.',
            'awal_cuti.required'                        => 'Tanggal awal cuti wajib diisi.',
            'awal_cuti.date'                            => 'Format tanggal awal cuti tidak valid.',
            'akhir_cuti.required'                       => 'Tanggal akhir cuti wajib diisi.',
            'akhir_cuti.date'                           => 'Format tanggal akhir cuti tidak valid.',
            'akhir_cuti.after_or_equal'                 => 'Tanggal akhir cuti harus sama atau setelah tanggal awal cuti.',
            'alamat.required'                           => 'Alamat selama menjalankan cuti wajib diisi.',
            'telepon.required'                          => 'Nomor telepon yang dapat dihubungi wajib diisi.',
        ];
    }
}
