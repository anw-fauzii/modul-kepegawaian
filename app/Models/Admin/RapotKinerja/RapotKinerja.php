<?php

namespace App\Models\Admin\RapotKinerja;

use App\Models\Admin\DataMaster\Karyawan;
use App\Models\Admin\DataMaster\TahunAjaran;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class RapotKinerja extends Model
{
    use HasFactory;

    /**
     * Nama tabel pada database.
     */
    protected $table = 'kepegawaian_rapot_kinerja';

    /**
     * Kolom yang dapat diisi secara massal (Mass Assignment).
     */
    protected $fillable = [
        'karyawan_nipy',
        'akademik_master_tahun_ajaran_id',
        'berkas',
        'keterangan',
    ];

    /**
     * Konversi tipe data otomatis.
     */
    protected $casts = [
        'akademik_master_tahun_ajaran_id' => 'integer',
    ];

    /**
     * Atribut tambahan saat serialisasi array / JSON.
     */
    protected $appends = [
        'berkas_url',
        'berkas_filename',
    ];

    /**
     * Relasi ke Master Karyawan.
     */
    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_nipy', 'nipy');
    }

    /**
     * Relasi ke Master Tahun Ajaran.
     */
    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'akademik_master_tahun_ajaran_id', 'id');
    }

    /**
     * Accessor public URL berkas di storage.
     */
    public function getBerkasUrlAttribute(): ?string
    {
        if (empty($this->berkas)) {
            return null;
        }

        return route('rapot-kinerja.preview', $this->id);
    }

    /**
     * Accessor nama asli berkas dari path file.
     */
    public function getBerkasFilenameAttribute(): ?string
    {
        return $this->berkas ? basename($this->berkas) : null;
    }

    /**
     * Scope filter berdasarkan tahun ajaran.
     */
    public function scopeByTahunAjaran(Builder $query, int $tahunAjaranId): Builder
    {
        return $query->where('akademik_master_tahun_ajaran_id', $tahunAjaranId);
    }

    /**
     * Scope filter berdasarkan NIPY karyawan.
     */
    public function scopeByKaryawan(Builder $query, string $nipy): Builder
    {
        return $query->where('karyawan_nipy', $nipy);
    }

    /**
     * Scope filter dinamis berkinerja tinggi tanpa N+1.
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when(! empty($filters['karyawan_nipy']) || ! empty($filters['nipy']), function (Builder $q) use ($filters) {
                $nipy = $filters['karyawan_nipy'] ?? $filters['nipy'];
                $q->where('karyawan_nipy', $nipy);
            })
            ->when(! empty($filters['akademik_master_tahun_ajaran_id']), function (Builder $q) use ($filters) {
                $q->where('akademik_master_tahun_ajaran_id', (int) $filters['akademik_master_tahun_ajaran_id']);
            })
            ->when(! empty($filters['search']), function (Builder $q) use ($filters) {
                $search = trim($filters['search']);
                $q->where(function (Builder $sq) use ($search) {
                    $sq->where('karyawan_nipy', 'like', "%{$search}%")
                        ->orWhereHas('karyawan', function (Builder $kq) use ($search) {
                            $kq->where('nama_lengkap', 'like', "%{$search}%")
                                ->orWhere('unit', 'like', "%{$search}%");
                        });
                });
            });
    }
}
