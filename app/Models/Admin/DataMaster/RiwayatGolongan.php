<?php

namespace App\Models\Admin\DataMaster;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'karyawan_nipy',
    'golongan_lama',
    'golongan_baru',
    'masa_kerja_tahun',
    'tanggal_efektif',
    'keterangan',
])]
#[Table('kepegawaian_riwayat_golongan')]
class RiwayatGolongan extends Model
{
    protected function casts(): array
    {
        return [
            'tanggal_efektif' => 'date',
        ];
    }

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_nipy', 'nipy');
    }
}
