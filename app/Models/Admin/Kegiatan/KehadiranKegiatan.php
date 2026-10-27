<?php

namespace App\Models\Admin\Kegiatan;

use App\Models\Admin\DataMaster\Karyawan;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'kepegawaian_kegiatan_lembaga_id',
    'karyawan_nipy',
    'waktu_scan',
    'status',
    'metode_presensi',
    'catatan',
    'device_info',
])]
#[Table('kepegawaian_kehadiran_kegiatan')]
class KehadiranKegiatan extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'waktu_scan' => 'datetime',
        ];
    }

    /* -------------------------------------------------------------------------- */
    /*                              RELATIONSHIPS                                 */
    /* -------------------------------------------------------------------------- */

    /**
     * Relasi ke Kegiatan Lembaga terkait.
     */
    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(KegiatanLembaga::class, 'kepegawaian_kegiatan_lembaga_id');
    }

    /**
     * Relasi ke profil Karyawan via NIPY.
     */
    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_nipy', 'nipy');
    }

    /* -------------------------------------------------------------------------- */
    /*                              QUERY SCOPES                                  */
    /* -------------------------------------------------------------------------- */

    public function scopeHadir(Builder $query): Builder
    {
        return $query->where('status', 'hadir');
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['status'] ?? null, fn (Builder $q, $status) => $q->where('status', $status))
            ->when($filters['search'] ?? null, function (Builder $q, string $search) {
                $q->whereHas('karyawan', function (Builder $kq) use ($search) {
                    $kq->where('nama_lengkap', 'like', "%{$search}%")
                       ->orWhere('nipy', 'like', "%{$search}%");
                });
            });
    }
}
