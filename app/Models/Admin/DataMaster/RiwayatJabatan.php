<?php

namespace App\Models\Admin\DataMaster;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'karyawan_nipy',
    'kepegawaian_master_kategori_id',
    'jabatan',
    'unit',
    'tanggal_mulai',
    'tanggal_selesai',
    'is_aktif',
    'keterangan',
])]
#[Table('kepegawaian_riwayat_jabatan')]
class RiwayatJabatan extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'kepegawaian_master_kategori_id' => 'integer',
            'tanggal_mulai'                  => 'date',
            'tanggal_selesai'                => 'date',
            'is_aktif'                       => 'boolean',
        ];
    }

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_nipy', 'nipy');
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kepegawaian_master_kategori_id');
    }

    protected function masaJabatan(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->tanggal_mulai) {
                return '-';
            }

            $end = $this->tanggal_selesai ? $this->tanggal_selesai->startOfDay() : now()->startOfDay();
            $diff = $this->tanggal_mulai->startOfDay()->diff($end);

            if ($diff->invert) {
                return '0 bln';
            }

            if ($diff->y > 0 && $diff->m > 0) {
                return "{$diff->y} thn {$diff->m} bln";
            }
            if ($diff->y > 0) {
                return "{$diff->y} tahun";
            }
            if ($diff->m > 0) {
                return "{$diff->m} bulan";
            }

            return max(1, $diff->d) . ' hari';
        });
    }
}

