<?php

namespace App\Models\Admin\SertifikatPenghargaan;

use App\Models\Admin\DataMaster\Karyawan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class SertifikatPenghargaan extends Model
{
    use HasFactory;

    /**
     * Nama tabel pada database modular monolith.
     */
    protected $table = 'kepegawaian_sertifikat_penghargaan';

    /**
     * Kolom yang dapat diisi secara massal (Mass Assignment).
     */
    protected $fillable = [
        'karyawan_nipy',
        'judul_penghargaan',
        'tahun',
        'berkas',
        'keterangan',
    ];

    /**
     * Konversi tipe data atribut secara otomatis (Casting).
     */
    protected $casts = [
        'tahun' => 'integer',
    ];

    /**
     * Atribut tambahan saat serialisasi JSON / API.
     */
    protected $appends = [
        'berkas_url',
        'berkas_filename',
    ];

    /**
     * Relasi ke Master Karyawan penerima sertifikat penghargaan.
     */
    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_nipy', 'nipy');
    }

    /**
     * Accessor untuk mendapatkan public URL berkas sertifikat di storage.
     */
    public function getBerkasUrlAttribute(): ?string
    {
        if (empty($this->berkas)) {
            return null;
        }

        return route('sertifikat-penghargaan.preview', $this->id);
    }

    /**
     * Accessor untuk mendapatkan nama asli berkas (basename).
     */
    public function getBerkasFilenameAttribute(): ?string
    {
        return $this->berkas ? basename($this->berkas) : null;
    }

    /**
     * Scope untuk memfilter penghargaan berdasarkan tahun tertentu.
     */
    public function scopeByTahun(Builder $query, int $tahun): Builder
    {
        return $query->where('tahun', $tahun);
    }

    /**
     * Scope untuk memfilter penghargaan berdasarkan karyawan tertentu.
     */
    public function scopeByKaryawan(Builder $query, string $nipy): Builder
    {
        return $query->where('karyawan_nipy', $nipy);
    }

    /**
     * Scope filter dinamis berkinerja tinggi tanpa N+1 query.
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when(! empty($filters['karyawan_nipy']) || ! empty($filters['nipy']), function (Builder $q) use ($filters) {
                $nipy = $filters['karyawan_nipy'] ?? $filters['nipy'];
                $q->where('karyawan_nipy', $nipy);
            })
            ->when(! empty($filters['tahun']), function (Builder $q) use ($filters) {
                $q->where('tahun', (int) $filters['tahun']);
            })
            ->when(! empty($filters['search']), function (Builder $q) use ($filters) {
                $search = trim($filters['search']);
                $q->where(function (Builder $sq) use ($search) {
                    $sq->where('judul_penghargaan', 'like', "%{$search}%")
                        ->orWhere('karyawan_nipy', 'like', "%{$search}%")
                        ->orWhereHas('karyawan', function (Builder $kq) use ($search) {
                            $kq->where('nama_lengkap', 'like', "%{$search}%")
                                ->orWhere('unit', 'like', "%{$search}%");
                        });
                });
            });
    }
}
