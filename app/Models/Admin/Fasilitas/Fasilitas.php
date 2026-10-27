<?php

namespace App\Models\Admin\Fasilitas;

use App\Models\Admin\DataMaster\Karyawan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fasilitas extends Model
{
    use HasFactory;

    /**
     * Nama tabel pada database modular monolith.
     */
    protected $table = 'kepegawaian_fasilitas';

    /**
     * Kolom yang dapat diisi secara massal (Mass Assignment).
     */
    protected $fillable = [
        'karyawan_nipy',
        'nama_fasilitas',
        'jumlah',
        'tahun_terima',
        'status',
        'tanggal_kembali',
        'catatan',
    ];

    /**
     * Konversi tipe data otomatis (Casting).
     */
    protected $casts = [
        'jumlah'          => 'integer',
        'tahun_terima'    => 'integer',
        'status'          => 'boolean',
        'tanggal_kembali' => 'date',
    ];

    /**
     * Relasi ke Karyawan pemilik fasilitas via karyawan_nipy (String Key ke nipy).
     */
    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_nipy', 'nipy');
    }

    /**
     * Scope untuk memfilter fasilitas yang belum dikembalikan.
     */
    public function scopeBelumKembali(Builder $query): Builder
    {
        return $query->where('status', false);
    }

    /**
     * Scope untuk memfilter fasilitas yang sudah dikembalikan.
     */
    public function scopeSudahKembali(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    /**
     * Scope filter pencarian dan kriteria modular performan tinggi.
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when(! empty($filters['karyawan_nipy']) || ! empty($filters['nipy']), function (Builder $q) use ($filters) {
                $nipy = $filters['karyawan_nipy'] ?? $filters['nipy'];
                $q->where('karyawan_nipy', $nipy);
            })
            ->when(isset($filters['status']) && $filters['status'] !== '' && $filters['status'] !== null, function (Builder $q) use ($filters) {
                $statusBool = filter_var($filters['status'], FILTER_VALIDATE_BOOLEAN);
                $q->where('status', $statusBool);
            })
            ->when(! empty($filters['tahun_terima']), function (Builder $q) use ($filters) {
                $q->where('tahun_terima', (int) $filters['tahun_terima']);
            })
            ->when(! empty($filters['search']), function (Builder $q) use ($filters) {
                $search = trim($filters['search']);
                $q->where(function (Builder $sq) use ($search) {
                    $sq->where('nama_fasilitas', 'like', "%{$search}%")
                        ->orWhere('karyawan_nipy', 'like', "%{$search}%")
                        ->orWhereHas('karyawan', function (Builder $kq) use ($search) {
                            $kq->where('nama_lengkap', 'like', "%{$search}%");
                        });
                });
            });
    }

    /**
     * Accessor untuk label status yang human-readable.
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->status ? 'Sudah Dikembalikan' : 'Belum Dikembalikan';
    }

    /**
     * Accessor helper class badge UI bootstrap/tailwind.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return $this->status ? 'badge bg-success text-white' : 'badge bg-warning text-dark';
    }
}
