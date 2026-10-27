<?php

namespace App\Models\Admin\Diklat;

use App\Models\Admin\DataMaster\Karyawan;
use App\Models\Admin\Kegiatan\KegiatanLembaga;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Diklat extends Model
{
    use HasFactory;

    /**
     * Nama tabel pada database modular monolith.
     */
    protected $table = 'kepegawaian_diklat';

    /**
     * Kolom yang dapat diisi secara massal (Mass Assignment).
     */
    protected $fillable = [
        'karyawan_nipy',
        'kategori',
        'nama_kegiatan',
        'pelaksana',
        'materi',
        'tanggal',
        'tanggal_selesai',
        'tempat',
        'jumlah_jam',
        'kepegawaian_kegiatan_lembaga_id',
        'berkas',
        'keterangan',
    ];

    /**
     * Konversi tipe data otomatis.
     */
    protected $casts = [
        'tanggal'                         => 'date',
        'tanggal_selesai'                 => 'date',
        'jumlah_jam'                      => 'integer',
        'kepegawaian_kegiatan_lembaga_id' => 'integer',
    ];

    /**
     * Atribut tambahan saat serialisasi array / JSON.
     */
    protected $appends = [
        'berkas_url',
        'berkas_filename',
    ];

    /**
     * Relasi ke Master Karyawan via foreign key karyawan_nipy (NIPY).
     */
    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_nipy', 'nipy');
    }

    /**
     * Relasi ke Kegiatan Lembaga (khusus kategori lembaga yang berjenis pelatihan).
     */
    public function kegiatanLembaga(): BelongsTo
    {
        return $this->belongsTo(KegiatanLembaga::class, 'kepegawaian_kegiatan_lembaga_id', 'id');
    }

    /**
     * Accessor URL berkas publik sertifikat / bukti diklat.
     */
    public function getBerkasUrlAttribute(): ?string
    {
        if (empty($this->berkas)) {
            return null;
        }

        return Storage::disk('public')->url($this->berkas);
    }

    /**
     * Accessor nama file asli dari berkas.
     */
    public function getBerkasFilenameAttribute(): ?string
    {
        return $this->berkas ? basename($this->berkas) : null;
    }

    /**
     * Scope filter berdasarkan kategori (mandiri / lembaga).
     */
    public function scopeByKategori(Builder $query, string $kategori): Builder
    {
        return $query->where('kategori', strtolower($kategori));
    }

    /**
     * Scope filter berdasarkan NIPY karyawan.
     */
    public function scopeByKaryawan(Builder $query, string $nipy): Builder
    {
        return $query->where('karyawan_nipy', $nipy);
    }

    /**
     * Scope filter dinamis berkinerja tinggi.
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when(! empty($filters['karyawan_nipy']) || ! empty($filters['nipy']), function (Builder $q) use ($filters) {
                $nipy = $filters['karyawan_nipy'] ?? $filters['nipy'];
                $q->where('karyawan_nipy', $nipy);
            })
            ->when(! empty($filters['kategori']), function (Builder $q) use ($filters) {
                $q->where('kategori', strtolower(trim($filters['kategori'])));
            })
            ->when(! empty($filters['tanggal_mulai']), function (Builder $q) use ($filters) {
                $q->whereDate('tanggal', '>=', $filters['tanggal_mulai']);
            })
            ->when(! empty($filters['tanggal_selesai']), function (Builder $q) use ($filters) {
                $q->whereDate('tanggal', '<=', $filters['tanggal_selesai']);
            })
            ->when(! empty($filters['search']), function (Builder $q) use ($filters) {
                $search = trim($filters['search']);
                $q->where(function (Builder $sq) use ($search) {
                    $sq->where('karyawan_nipy', 'like', "%{$search}%")
                        ->orWhere('nama_kegiatan', 'like', "%{$search}%")
                        ->orWhere('pelaksana', 'like', "%{$search}%")
                        ->orWhere('materi', 'like', "%{$search}%")
                        ->orWhere('tempat', 'like', "%{$search}%")
                        ->orWhereHas('karyawan', function (Builder $kq) use ($search) {
                            $kq->where('nama_lengkap', 'like', "%{$search}%")
                                ->orWhere('unit', 'like', "%{$search}%");
                        });
                });
            });
    }
}
