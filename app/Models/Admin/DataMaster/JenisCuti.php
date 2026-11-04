<?php

namespace App\Models\Admin\DataMaster;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nama', 'lama_hari', 'keterangan', 'is_active'])]
#[Table('kepegawaian_master_jenis_cuti')]
class JenisCuti extends Model
{
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'lama_hari' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function cuti(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Admin\Cuti\Cuti::class, 'kepegawaian_master_jenis_cuti_id');
    }

    /**
     * Memeriksa apakah jenis cuti ini adalah Cuti Melahirkan (untuk karyawati/ibu yang bersalin)
     * yang memiliki ketentuan khusus 90 hari kalender penuh.
     * Catatan: "Cuti Istri Melahirkan" (untuk suami) berdurasi 2 hari kerja biasa, bukan 90 hari.
     */
    public function isMelahirkan(): bool
    {
        $nama = strtolower($this->nama ?? '');

        // Pengecualian: Cuti istri melahirkan (untuk suami/pendamping) dihitung hari kerja biasa (misal 2 hari)
        if (str_contains($nama, 'istri') || str_contains($nama, 'suami') || str_contains($nama, 'mendampingi')) {
            return false;
        }

        // Cuti Melahirkan ibu/karyawati (90 hari kalender penuh)
        return str_contains($nama, 'melahirkan');
    }
}
