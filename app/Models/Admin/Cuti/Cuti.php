<?php

namespace App\Models\Admin\Cuti;

use App\Models\Admin\DataMaster\HariLibur;
use App\Models\Admin\DataMaster\JenisCuti;
use App\Models\Admin\DataMaster\Karyawan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cuti extends Model
{
    use HasFactory;

    /**
     * Nama tabel modular kepegawaian.
     */
    protected $table = 'kepegawaian_cuti';

    /**
     * Atribut yang dapat diisi secara mass-assignment.
     */
    protected $fillable = [
        'karyawan_nipy',
        'kepegawaian_master_jenis_cuti_id',
        'awal_cuti',
        'akhir_cuti',
        'jumlah_hari',
        'alamat',
        'telepon',
        'alasan',
        'acc',
        'catatan_acc',
        'tgl_acc',
        'acc_by',
    ];

    /**
     * Casting tipe data otomatis.
     */
    protected $casts = [
        'awal_cuti'   => 'date',
        'akhir_cuti'  => 'date',
        'jumlah_hari' => 'integer',
        'acc'         => 'boolean',
        'tgl_acc'     => 'datetime',
    ];

    /* -------------------------------------------------------------------------- */
    /*                              RELATIONSHIPS                                 */
    /* -------------------------------------------------------------------------- */

    /**
     * Relasi ke Karyawan pemohon cuti.
     */
    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_nipy', 'nipy');
    }

    /**
     * Relasi ke Master Jenis Cuti.
     */
    public function jenisCuti(): BelongsTo
    {
        return $this->belongsTo(JenisCuti::class, 'kepegawaian_master_jenis_cuti_id', 'id');
    }

    /**
     * Relasi ke User yang menyetujui (ACC).
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'acc_by', 'id');
    }

    /* -------------------------------------------------------------------------- */
    /*                                   SCOPES                                   */
    /* -------------------------------------------------------------------------- */

    /**
     * Scope pengajuan yang sudah di-ACC / disetujui.
     */
    public function scopeDisetujui(Builder $query): Builder
    {
        return $query->where('acc', true);
    }

    /**
     * Scope pengajuan yang masih menunggu persetujuan (Menunggu ACC).
     */
    public function scopeMenunggu(Builder $query): Builder
    {
        return $query->where('acc', false);
    }

    /**
     * Scope filter dinamis untuk pencarian dan filter data cuti.
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when(! empty($filters['karyawan_nipy']), function (Builder $q) use ($filters) {
                $q->where('karyawan_nipy', $filters['karyawan_nipy']);
            })
            ->when(! empty($filters['kepegawaian_master_jenis_cuti_id']), function (Builder $q) use ($filters) {
                $q->where('kepegawaian_master_jenis_cuti_id', $filters['kepegawaian_master_jenis_cuti_id']);
            })
            ->when(isset($filters['acc']) && $filters['acc'] !== '' && $filters['acc'] !== null, function (Builder $q) use ($filters) {
                $statusAcc = filter_var($filters['acc'], FILTER_VALIDATE_BOOLEAN);
                $q->where('acc', $statusAcc);
            })
            ->when(! empty($filters['tahun']), function (Builder $q) use ($filters) {
                $tahun = (int) $filters['tahun'];
                $q->whereYear('awal_cuti', $tahun);
            })
            ->when(! empty($filters['search']), function (Builder $q) use ($filters) {
                $search = trim($filters['search']);
                $q->where(function (Builder $sq) use ($search) {
                    $sq->where('karyawan_nipy', 'like', "%{$search}%")
                        ->orWhere('alamat', 'like', "%{$search}%")
                        ->orWhere('telepon', 'like', "%{$search}%")
                        ->orWhere('alasan', 'like', "%{$search}%")
                        ->orWhereHas('karyawan', function (Builder $kq) use ($search) {
                            $kq->where('nama_lengkap', 'like', "%{$search}%");
                        })
                        ->orWhereHas('jenisCuti', function (Builder $jq) use ($search) {
                            $jq->where('nama', 'like', "%{$search}%");
                        });
                });
            });
    }

    /* -------------------------------------------------------------------------- */
    /*                                 ACCESSORS                                  */
    /* -------------------------------------------------------------------------- */

    /**
     * Label status ACC yang mudah dipahami.
     */
    public function getAccLabelAttribute(): string
    {
        return $this->acc ? 'Disetujui (ACC)' : 'Menunggu ACC';
    }

    /**
     * Badge class CSS bootstrap untuk status ACC.
     */
    public function getAccBadgeClassAttribute(): string
    {
        return $this->acc
            ? 'badge badge-soft-success text-success border border-success'
            : 'badge badge-soft-warning text-warning border border-warning';
    }

    /**
     * Format rentang tanggal cuti human-friendly.
     */
    public function getPeriodeFormattedAttribute(): string
    {
        if (! $this->awal_cuti || ! $this->akhir_cuti) {
            return '-';
        }

        if ($this->awal_cuti->equalTo($this->akhir_cuti)) {
            return $this->awal_cuti->isoFormat('D MMMM Y');
        }

        return $this->awal_cuti->isoFormat('D MMM Y') . ' s/d ' . $this->akhir_cuti->isoFormat('D MMM Y');
    }

    /**
     * Kalkulasi otomatis durasi jumlah hari kerja cuti:
     * - Mengecualikan Sabtu & Minggu (Weekend)
     * - Mengecualikan Hari Libur Nasional & Cuti Bersama resmi.
     *
     * @param string|Carbon $awal
     * @param string|Carbon $akhir
     * @param array<string, string>|null $holidays Map tanggal libur ['YYYY-MM-DD' => 'Keterangan']
     * @return int
     */
    public static function hitungJumlahHari(string|Carbon $awal, string|Carbon $akhir, ?array $holidays = null): int
    {
        $startDate = Carbon::parse($awal)->startOfDay();
        $endDate = Carbon::parse($akhir)->startOfDay();

        if ($endDate->lessThan($startDate)) {
            return 0;
        }

        if ($holidays === null) {
            $holidays = HariLibur::getHolidayMap();
        }

        $workingDays = 0;
        $current = $startDate->copy();

        while ($current->lessThanOrEqualTo($endDate)) {
            $dateStr = $current->toDateString();
            $isWeekend = $current->isWeekend();
            $isHoliday = isset($holidays[$dateStr]);

            if (! $isWeekend && ! $isHoliday) {
                $workingDays++;
            }
            $current->addDay();
        }

        return $workingDays;
    }

    /**
     * Hitung tanggal akhir cuti berdasarkan tanggal awal dan jumlah hari kerja:
     * - Sabtu & Minggu dilewati.
     * - Hari Libur Nasional & Cuti Bersama dilewati (tidak dihitung sebagai cuti).
     *
     * @param string|Carbon $awal
     * @param int $lamaHari
     * @param array<string, string>|null $holidays Map tanggal libur ['YYYY-MM-DD' => 'Keterangan']
     * @return Carbon
     */
    public static function hitungAkhirCuti(string|Carbon $awal, int $lamaHari, ?array $holidays = null): Carbon
    {
        $current = Carbon::parse($awal)->startOfDay();
        $targetDays = max(1, $lamaHari);

        if ($holidays === null) {
            $holidays = HariLibur::getHolidayMap();
        }

        $counted = 0;
        while ($counted < $targetDays) {
            $dateStr = $current->toDateString();
            $isWeekend = $current->isWeekend();
            $isHoliday = isset($holidays[$dateStr]);

            if (! $isWeekend && ! $isHoliday) {
                $counted++;
                if ($counted === $targetDays) {
                    break;
                }
            }
            $current->addDay();
        }

        return $current;
    }

    /**
     * Ambil daftar hari libur nasional yang terdapat dalam rentang cuti.
     *
     * @param string|Carbon $awal
     * @param string|Carbon $akhir
     * @param array<string, string>|null $holidays
     * @return array<int, array{tanggal: string, nama: string}>
     */
    public static function getLiburDalamRentang(string|Carbon $awal, string|Carbon $akhir, ?array $holidays = null): array
    {
        $startDate = Carbon::parse($awal)->startOfDay();
        $endDate = Carbon::parse($akhir)->startOfDay();

        if ($endDate->lessThan($startDate)) {
            return [];
        }

        if ($holidays === null) {
            $holidays = HariLibur::getHolidayMap();
        }

        $result = [];
        $current = $startDate->copy();

        while ($current->lessThanOrEqualTo($endDate)) {
            $dateStr = $current->toDateString();
            if (isset($holidays[$dateStr])) {
                $result[] = [
                    'tanggal' => $dateStr,
                    'nama'    => $holidays[$dateStr],
                ];
            }
            $current->addDay();
        }

        return $result;
    }
}
