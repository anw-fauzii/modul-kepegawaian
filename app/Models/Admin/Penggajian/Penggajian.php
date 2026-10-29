<?php

namespace App\Models\Admin\Penggajian;

use App\Models\Admin\DataMaster\Bulan;
use App\Models\Admin\DataMaster\Karyawan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penggajian extends Model
{
    use HasFactory;

    protected $table = 'kepegawaian_keuangan_penggajian';

    /** Komponen penerimaan (label untuk form & slip). */
    public const PENERIMAAN = [
        'gaji_pokok'        => 'Gaji Pokok',
        'tpp'               => 'TPP',
        'pembulatan'        => 'Pembulatan',
        't_struktural'      => 'Tunj. Struktural',
        't_fungsional'      => 'Tunj. Fungsional',
        't_amanah_tambahan' => 'Tunj. Amanah Tambahan',
        't_transport'       => 'Tunj. Transport',
        't_makan'           => 'Tunj. Makan',
        't_kesehatan'       => 'Tunj. Kesehatan',
        't_keluarga'        => 'Tunj. Keluarga',
        't_pendidikan_anak' => 'Tunj. Pendidikan Anak',
        't_kuota'           => 'Tunj. Kuota',
        'lain_lain'         => 'Lain-lain',
        'reward_kehadiran'  => 'Reward Kehadiran',
        'kekurangan_gaji'   => 'Kekurangan Gaji (bulan lalu)',
    ];

    /** Komponen potongan. */
    public const POTONGAN = [
        'pot_makan'      => 'Potongan Makan',
        'pot_kehadiran'  => 'Potongan Kehadiran',
        'pot_pendidikan' => 'Potongan Pendidikan',
        'pot_kesehatan'  => 'Potongan Kesehatan',
        'infaq'          => 'Infaq',
        'adm_sekolah'    => 'Administrasi Sekolah',
        'kelebihan_gaji' => 'Kelebihan Gaji (bulan lalu)',
    ];

    public const STATUS = [
        'draft'   => 'Draft',
        'final'   => 'Final',
        'dibayar' => 'Dibayar',
    ];

    public const JENIS = [
        'gaji'  => 'Gaji Bulanan',
        'thr'   => 'Tunjangan Hari Raya (THR)',
        'bonus' => 'Bonus Kinerja',
    ];

    public const JENIS_SHORT = [
        'gaji'  => 'Gaji',
        'thr'   => 'THR',
        'bonus' => 'Bonus',
    ];

    protected $fillable = [
        'karyawan_nipy',
        'kepegawaian_master_bulan_id',
        'jenis',
        'unit',
        'jabatan',
        'golongan',
        'gaji_pokok',
        'tpp',
        'pembulatan',
        't_struktural',
        't_fungsional',
        't_amanah_tambahan',
        't_transport',
        't_makan',
        't_kesehatan',
        't_keluarga',
        't_pendidikan_anak',
        't_kuota',
        'lain_lain',
        'reward_kehadiran',
        'kekurangan_gaji',
        'pot_makan',
        'pot_kehadiran',
        'pot_pendidikan',
        'pot_kesehatan',
        'infaq',
        'adm_sekolah',
        'kelebihan_gaji',
        'penerimaan',
        'potongan',
        'gaji_bersih',
        'status',
    ];

    protected function casts(): array
    {
        $casts = [
            'penerimaan'  => 'decimal:2',
            'potongan'    => 'decimal:2',
            'gaji_bersih' => 'decimal:2',
        ];

        foreach (array_merge(array_keys(self::PENERIMAAN), array_keys(self::POTONGAN)) as $field) {
            $casts[$field] = 'decimal:2';
        }

        return $casts;
    }

    /**
     * Total penerimaan, potongan & gaji bersih selalu dihitung ulang saat disimpan.
     */
    protected static function booted(): void
    {
        static::saving(function (self $model) {
            $model->hitungTotal();
        });
    }

    public function hitungTotal(): void
    {
        $penerimaan = 0.0;
        foreach (array_keys(self::PENERIMAAN) as $field) {
            $penerimaan += (float) $this->getAttribute($field);
        }

        $potongan = 0.0;
        foreach (array_keys(self::POTONGAN) as $field) {
            $potongan += (float) $this->getAttribute($field);
        }

        $this->penerimaan = round($penerimaan, 2);
        $this->potongan = round($potongan, 2);
        $this->gaji_bersih = round($penerimaan - $potongan, 2);
    }

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_nipy', 'nipy');
    }

    public function bulan(): BelongsTo
    {
        return $this->belongsTo(Bulan::class, 'kepegawaian_master_bulan_id');
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when(! empty($filters['search']), function (Builder $q) use ($filters) {
                $search = trim($filters['search']);
                $q->where(function (Builder $sq) use ($search) {
                    $sq->where('karyawan_nipy', 'like', "%{$search}%")
                        ->orWhere('unit', 'like', "%{$search}%")
                        ->orWhere('jabatan', 'like', "%{$search}%")
                        ->orWhereHas('karyawan', fn (Builder $kq) => $kq->where('nama_lengkap', 'like', "%{$search}%"));
                });
            })
            ->when(! empty($filters['kepegawaian_master_bulan_id']), fn (Builder $q) => $q->where('kepegawaian_master_bulan_id', $filters['kepegawaian_master_bulan_id']))
            ->when(! empty($filters['status']), fn (Builder $q) => $q->where('status', $filters['status']))
            ->when(! empty($filters['jenis']), fn (Builder $q) => $q->where('jenis', $filters['jenis']));
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS[$this->status] ?? ucfirst((string) $this->status);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'dibayar' => 'badge-success',
            'final'   => 'badge-primary',
            default   => 'badge-secondary',
        };
    }

    public function getJenisLabelAttribute(): string
    {
        return self::JENIS[$this->jenis] ?? 'Gaji Bulanan';
    }

    public function getJenisShortLabelAttribute(): string
    {
        return self::JENIS_SHORT[$this->jenis] ?? $this->jenis_label;
    }

    public function getJenisBadgeClassAttribute(): string
    {
        return match ($this->jenis) {
            'thr'   => 'badge-success',
            'bonus' => 'badge-warning',
            default => 'badge-info',
        };
    }

    public function getSlipJudulAttribute(): string
    {
        return match ($this->jenis) {
            'thr'   => 'SLIP TUNJANGAN HARI RAYA (THR)',
            'bonus' => 'SLIP BONUS KINERJA',
            default => 'SLIP GAJI BULANAN',
        };
    }

    public function getTahunThrAttribute(): string
    {
        return $this->created_at ? $this->created_at->format('Y') : date('Y');
    }

    public function getPeriodeTeksAttribute(): string
    {
        if ($this->jenis === 'thr') {
            return 'Tahun ' . $this->tahun_thr;
        }

        if ($this->jenis === 'bonus') {
            return 'TA ' . ($this->bulan?->tahunAjaran?->nama_tahun_ajaran ?? date('Y'));
        }

        $bulan = $this->bulan?->nama_bulan ?? '-';
        $ta = $this->bulan?->tahunAjaran?->nama_tahun_ajaran;
        return $ta ? "{$bulan} ({$ta})" : $bulan;
    }
}
