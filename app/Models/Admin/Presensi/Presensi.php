<?php

namespace App\Models\Admin\Presensi;

use App\Models\Admin\DataMaster\Bulan;
use App\Models\Admin\DataMaster\JamKerja;
use App\Models\Admin\DataMaster\Karyawan;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'karyawan_nipy',
    'kepegawaian_master_bulan_id',
    'kepegawaian_master_jam_kerja_id',
    'tanggal',
    'jam_masuk',
    'jam_pulang',
    'status',
    'keterlambatan_menit',
    'izin_terlambat',
    'pulang_cepat_menit',
    'izin_pulang_cepat',
    'catatan',
])]
#[Table('kepegawaian_presensi')]
class Presensi extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'tanggal'             => 'date',
            'keterlambatan_menit' => 'integer',
            'izin_terlambat'      => 'boolean',
            'pulang_cepat_menit'  => 'integer',
            'izin_pulang_cepat'   => 'boolean',
        ];
    }

    /* -------------------------------------------------------------------------- */
    /*                              RELATIONS                                     */
    /* -------------------------------------------------------------------------- */

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_nipy', 'nipy');
    }

    public function bulan(): BelongsTo
    {
        return $this->belongsTo(Bulan::class, 'kepegawaian_master_bulan_id');
    }

    public function jamKerja(): BelongsTo
    {
        return $this->belongsTo(JamKerja::class, 'kepegawaian_master_jam_kerja_id');
    }

    /* -------------------------------------------------------------------------- */
    /*                              ACCESSORS                                     */
    /* -------------------------------------------------------------------------- */

    protected function statusBadge(): Attribute
    {
        return Attribute::get(function () {
            if ($this->status === 'terlambat') {
                if ($this->izin_terlambat) {
                    return [
                        'label' => 'Terlambat (Dispensasi/Izin)',
                        'class' => 'badge-info',
                        'color' => '#0284c7',
                    ];
                }
                return [
                    'label' => 'Terlambat (' . ($this->keterlambatan_menit ?? 0) . ' mnt)',
                    'class' => 'badge-warning',
                    'color' => '#f59e0b',
                ];
            }

            return match ($this->status) {
                'hadir'     => ['label' => 'Hadir Tepat Waktu', 'class' => 'badge-success', 'color' => '#10b981'],
                'tugas'     => ['label' => 'Tugas', 'class' => 'badge-primary', 'color' => '#6366f1'],
                'izin'      => ['label' => 'Izin',  'class' => 'badge-info',    'color' => '#0ea5e9'],
                'sakit'     => ['label' => 'Sakit', 'class' => 'badge-secondary', 'color' => '#64748b'],
                'cuti'      => ['label' => 'Cuti',  'class' => 'badge-primary', 'color' => '#8b5cf6'],
                'alpa'      => ['label' => 'Alpa',  'class' => 'badge-danger',  'color' => '#ef4444'],
                default     => ['label' => ucfirst((string) $this->status), 'class' => 'badge-light', 'color' => '#94a3b8'],
            };
        });
    }

    protected function durasiKerja(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->jam_masuk || ! $this->jam_pulang) {
                return '-';
            }

            try {
                $masuk = Carbon::parse($this->jam_masuk);
                $pulang = Carbon::parse($this->jam_pulang);
                $diffMinutes = $masuk->diffInMinutes($pulang);

                $jam = floor($diffMinutes / 60);
                $menit = $diffMinutes % 60;

                return "{$jam} jam {$menit} menit";
            } catch (\Exception) {
                return '-';
            }
        });
    }

    /* -------------------------------------------------------------------------- */
    /*                              SCOPES (Query Optimization)                  */
    /* -------------------------------------------------------------------------- */

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['kepegawaian_master_bulan_id'] ?? null, fn ($q, $bulanId) => $q->where('kepegawaian_master_bulan_id', $bulanId))
            ->when($filters['tanggal_mulai'] ?? null, fn ($q, $start) => $q->whereDate('tanggal', '>=', $start))
            ->when($filters['tanggal_selesai'] ?? null, fn ($q, $end) => $q->whereDate('tanggal', '<=', $end))
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->when($filters['kepegawaian_master_kategori_id'] ?? null, function ($q, $kategoriId) {
                $q->whereHas('karyawan', fn ($kq) => $kq->where('kepegawaian_master_kategori_id', $kategoriId));
            })
            ->when($filters['search'] ?? null, function ($q, $search) {
                $q->where(function ($subQuery) use ($search) {
                    $subQuery->where('karyawan_nipy', 'LIKE', "%{$search}%")
                        ->orWhereHas('karyawan', fn ($kq) => $kq->where('nama_lengkap', 'LIKE', "%{$search}%"));
                });
            });
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('tanggal', Carbon::today());
    }

    public function scopeByBulan(Builder $query, int $bulanId): Builder
    {
        return $query->where('kepegawaian_master_bulan_id', $bulanId);
    }
}
