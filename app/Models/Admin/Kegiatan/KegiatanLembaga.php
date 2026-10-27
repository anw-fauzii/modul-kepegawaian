<?php

namespace App\Models\Admin\Kegiatan;

use App\Models\Admin\DataMaster\Karyawan;
use App\Models\Admin\DataMaster\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable([
    'nama_kegiatan',
    'jenis_kegiatan',
    'pengisi',
    'tanggal',
    'jam_mulai',
    'jam_selesai',
    'lokasi',
    'akademik_master_tahun_ajaran_id',
    'qr_token',
    'is_active',
    'deskripsi',
])]
#[Table('kepegawaian_kegiatan_lembaga')]
class KegiatanLembaga extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'tanggal'   => 'date',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Bootstrap model events.
     * Secara otomatis menghasilkan qr_token unik 64 karakter saat model dibuat.
     */
    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->qr_token)) {
                $model->qr_token = Str::random(64);
            }
        });
    }

    /* -------------------------------------------------------------------------- */
    /*                              RELATIONSHIPS                                 */
    /* -------------------------------------------------------------------------- */

    /**
     * Relasi ke Tahun Ajaran aktif kegiatan.
     */
    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'akademik_master_tahun_ajaran_id');
    }

    /**
     * Relasi ke seluruh catatan kehadiran peserta kegiatan.
     */
    public function kehadiran(): HasMany
    {
        return $this->hasMany(KehadiranKegiatan::class, 'kepegawaian_kegiatan_lembaga_id');
    }

    /**
     * Relasi many-to-many ke Karyawan yang hadir melalui tabel pivot kehadiran.
     */
    public function peserta(): BelongsToMany
    {
        return $this->belongsToMany(
            Karyawan::class,
            'kepegawaian_kehadiran_kegiatan',
            'kepegawaian_kegiatan_lembaga_id',
            'karyawan_nipy',
            'id',
            'nipy'
        )->withPivot(['waktu_scan', 'status', 'metode_presensi', 'catatan', 'device_info'])
         ->withTimestamps();
    }

    /* -------------------------------------------------------------------------- */
    /*                              QUERY SCOPES                                  */
    /* -------------------------------------------------------------------------- */

    /**
     * Scope untuk kegiatan yang sedang aktif (gerbang presensi dibuka).
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk kegiatan hari ini.
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('tanggal', Carbon::today());
    }

    /**
     * Scope untuk kegiatan berjenis pelatihan / diklat lembaga.
     */
    public function scopePelatihan(Builder $query): Builder
    {
        return $query->where('jenis_kegiatan', 'pelatihan');
    }

    /**
     * Cek apakah kegiatan merupakan pelatihan/diklat lembaga.
     */
    public function isPelatihan(): bool
    {
        return ($this->jenis_kegiatan ?? '') === 'pelatihan';
    }

    /**
     * Scope filter dinamis untuk pencarian performan tinggi.
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['search'] ?? null, function (Builder $q, string $search) {
                $q->where(function (Builder $sub) use ($search) {
                    $sub->where('nama_kegiatan', 'like', "%{$search}%")
                        ->orWhere('pengisi', 'like', "%{$search}%")
                        ->orWhere('lokasi', 'like', "%{$search}%");
                });
            })
            ->when($filters['tanggal_mulai'] ?? null, fn (Builder $q, $start) => $q->whereDate('tanggal', '>=', $start))
            ->when($filters['tanggal_selesai'] ?? null, fn (Builder $q, $end) => $q->whereDate('tanggal', '<=', $end))
            ->when($filters['akademik_master_tahun_ajaran_id'] ?? null, fn (Builder $q, $ta) => $q->where('akademik_master_tahun_ajaran_id', $ta))
            ->when(isset($filters['is_active']) && $filters['is_active'] !== '', fn (Builder $q) => $q->where('is_active', (bool) $filters['is_active']));
    }

    /* -------------------------------------------------------------------------- */
    /*                              HELPER METHODS                                */
    /* -------------------------------------------------------------------------- */

    /**
     * Mengecek apakah kegiatan masih relevan / dibuka untuk presensi.
     */
    public function isAvailableForPresensi(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        // Opsional: periksa apakah tanggal kegiatan adalah hari ini
        return $this->tanggal->isToday();
    }
}
