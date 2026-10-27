<?php

namespace App\Models\Admin\SuratKeputusan;

use App\Models\Admin\DataMaster\Karyawan;
use App\Models\Admin\DataMaster\TahunAjaran;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class SuratKeputusan extends Model
{
    use HasFactory;

    /**
     * Nama tabel pada database modular monolith.
     */
    protected $table = 'kepegawaian_surat_keputusan';

    /**
     * Kolom yang dapat diisi secara massal (Mass Assignment).
     */
    protected $fillable = [
        'nomor_sk',
        'karyawan_nipy',
        'akademik_master_tahun_ajaran_id',
        'jabatan',
        'berkas',
        'tanggal_sk',
        'keterangan',
    ];

    /**
     * Konversi tipe data otomatis (Casting).
     */
    protected $casts = [
        'akademik_master_tahun_ajaran_id' => 'integer',
        'tanggal_sk'                      => 'date',
    ];

    /**
     * Atribut tambahan yang otomatis disertakan saat serialisasi JSON / API.
     */
    protected $appends = [
        'berkas_url',
        'berkas_filename',
        'formatted_tanggal_sk',
    ];

    /**
     * Relasi ke Master Karyawan via foreign key karyawan_nipy (String Key ke nipy).
     */
    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_nipy', 'nipy');
    }

    /**
     * Relasi ke Master Tahun Ajaran via akademik_master_tahun_ajaran_id.
     */
    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'akademik_master_tahun_ajaran_id', 'id');
    }

    /**
     * Accessor untuk mendapatkan public URL dari berkas yang diunggah.
     */
    public function getBerkasUrlAttribute(): ?string
    {
        if (empty($this->berkas)) {
            return null;
        }

        return Storage::disk('public')->url($this->berkas);
    }

    /**
     * Accessor untuk mendapatkan nama file asli / basename dari path berkas.
     */
    public function getBerkasFilenameAttribute(): ?string
    {
        return $this->berkas ? basename($this->berkas) : null;
    }

    /**
     * Accessor tanggal SK terformat dalam standar Indonesia (contoh: 15 Juli 2026).
     */
    public function getFormattedTanggalSkAttribute(): ?string
    {
        if (! $this->tanggal_sk) {
            return null;
        }

        return Carbon::parse($this->tanggal_sk)->translatedFormat('d F Y');
    }

    /**
     * Scope untuk memfilter data berdasarkan tahun ajaran tertentu.
     */
    public function scopeByTahunAjaran(Builder $query, int $tahunAjaranId): Builder
    {
        return $query->where('akademik_master_tahun_ajaran_id', $tahunAjaranId);
    }

    /**
     * Scope untuk memfilter data berdasarkan karyawan NIPY tertentu.
     */
    public function scopeByKaryawan(Builder $query, string $nipy): Builder
    {
        return $query->where('karyawan_nipy', $nipy);
    }

    /**
     * Scope filter dinamis berkinerja tinggi (tanpa N+1 query).
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
            ->when(! empty($filters['jabatan']), function (Builder $q) use ($filters) {
                $q->where('jabatan', 'like', '%' . trim($filters['jabatan']) . '%');
            })
            ->when(! empty($filters['search']), function (Builder $q) use ($filters) {
                $search = trim($filters['search']);
                $q->where(function (Builder $sq) use ($search) {
                    $sq->where('nomor_sk', 'like', "%{$search}%")
                        ->orWhere('jabatan', 'like', "%{$search}%")
                        ->orWhere('karyawan_nipy', 'like', "%{$search}%")
                        ->orWhereHas('karyawan', function (Builder $kq) use ($search) {
                            $kq->where('nama_lengkap', 'like', "%{$search}%");
                        });
                });
            });
    }
}
