<?php

namespace App\Services\Admin\Fasilitas;

use App\Models\Admin\Fasilitas\Fasilitas;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FasilitasService
{
    /**
     * Mengambil daftar fasilitas terpaginasi dengan Eager Loading anti N+1 Query dan kolom selektif.
     */
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Fasilitas::query()
            ->select([
                'id',
                'karyawan_nipy',
                'nama_fasilitas',
                'jumlah',
                'tahun_terima',
                'status',
                'tanggal_kembali',
                'catatan',
                'created_at',
            ])
            ->with([
                // Eager loading relasi karyawan dengan spesifik kolom (Zero N+1)
                'karyawan' => fn ($query) => $query
                    ->select('nipy', 'nama_lengkap', 'gelar', 'unit', 'jabatan', 'kepegawaian_master_kategori_id')
                    ->with([
                        'kategori:id,nama_kategori',
                    ]),
            ])
            ->filter($filters)
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Mengambil detail fasilitas berdasarkan ID dengan relasi lengkap.
     */
    public function getById(int $id): Fasilitas
    {
        return Fasilitas::query()
            ->select([
                'id',
                'karyawan_nipy',
                'nama_fasilitas',
                'jumlah',
                'tahun_terima',
                'status',
                'tanggal_kembali',
                'catatan',
                'created_at',
                'updated_at',
            ])
            ->with([
                'karyawan' => fn ($query) => $query
                    ->select('nipy', 'nama_lengkap', 'gelar', 'unit', 'jabatan', 'whatsapp', 'kepegawaian_master_kategori_id')
                    ->with('kategori:id,nama_kategori'),
            ])
            ->findOrFail($id);
    }

    /**
     * Membuat pencatatan fasilitas baru dalam transaksi database.
     */
    public function create(array $data): Fasilitas
    {
        return DB::transaction(function () use ($data) {
            // Normalisasi kunci foreign key jika terkirim 'nipy'
            if (! empty($data['nipy']) && empty($data['karyawan_nipy'])) {
                $data['karyawan_nipy'] = $data['nipy'];
                unset($data['nipy']);
            }

            // Normalisasi status dan tanggal kembali
            $isReturned = ! empty($data['status']);
            $data['status'] = $isReturned;
            
            if ($isReturned && empty($data['tanggal_kembali'])) {
                $data['tanggal_kembali'] = Carbon::now()->toDateString();
            } elseif (! $isReturned) {
                $data['tanggal_kembali'] = null;
            }

            $fasilitas = Fasilitas::create($data);

            Log::info("Fasilitas #{$fasilitas->id} ({$fasilitas->nama_fasilitas}) berhasil dicatat untuk karyawan NIPY {$fasilitas->karyawan_nipy}.");

            return $fasilitas;
        });
    }

    /**
     * Memperbarui data fasilitas dalam transaksi database yang aman.
     */
    public function update(Fasilitas $fasilitas, array $data): Fasilitas
    {
        return DB::transaction(function () use ($fasilitas, $data) {
            if (! empty($data['nipy']) && empty($data['karyawan_nipy'])) {
                $data['karyawan_nipy'] = $data['nipy'];
                unset($data['nipy']);
            }

            $isReturned = ! empty($data['status']);
            $data['status'] = $isReturned;

            if ($isReturned && empty($data['tanggal_kembali'])) {
                $data['tanggal_kembali'] = Carbon::now()->toDateString();
            } elseif (! $isReturned) {
                $data['tanggal_kembali'] = null;
            }

            $fasilitas->update($data);

            Log::info("Fasilitas #{$fasilitas->id} berhasil diperbarui.");

            return $fasilitas->fresh();
        });
    }

    /**
     * Menghapus catatan fasilitas dalam transaksi database.
     */
    public function delete(Fasilitas $fasilitas): bool
    {
        return DB::transaction(function () use ($fasilitas) {
            $fasilitasId = $fasilitas->id;
            $deleted = $fasilitas->delete();

            Log::warning("Fasilitas #{$fasilitasId} dihapus oleh sistem/pengguna.");

            return (bool) $deleted;
        });
    }

    /**
     * Toggle status pengembalian fasilitas (belum <-> sudah dikembalikan).
     */
    public function toggleStatus(Fasilitas $fasilitas, ?string $catatan = null): Fasilitas
    {
        return DB::transaction(function () use ($fasilitas, $catatan) {
            $newStatus = ! $fasilitas->status;
            
            $payload = [
                'status'          => $newStatus,
                'tanggal_kembali' => $newStatus ? Carbon::now()->toDateString() : null,
            ];

            if ($catatan !== null) {
                $payload['catatan'] = $catatan;
            }

            $fasilitas->update($payload);

            Log::info("Status Fasilitas #{$fasilitas->id} diubah menjadi: " . ($newStatus ? 'Sudah Dikembalikan' : 'Belum Dikembalikan'));

            return $fasilitas->fresh();
        });
    }

    /**
     * Menghitung rekapitulasi ringkasan statistik fasilitas dalam 1 single optimized query.
     */
    public function getRekapStatistik(): object
    {
        return DB::table('kepegawaian_fasilitas')
            ->selectRaw("
                COUNT(*) as total_item,
                COALESCE(SUM(jumlah), 0) as total_unit,
                COALESCE(SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END), 0) as total_belum_kembali,
                COALESCE(SUM(CASE WHEN status = 0 THEN jumlah ELSE 0 END), 0) as unit_belum_kembali,
                COALESCE(SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END), 0) as total_sudah_kembali,
                COALESCE(SUM(CASE WHEN status = 1 THEN jumlah ELSE 0 END), 0) as unit_sudah_kembali
            ")
            ->first();
    }

    /**
     * Pemrosesan data besar secara hemat memori menggunakan Cursor / Chunking (Memory Efficient).
     *
     * @param  array  $filters
     * @param  callable(Fasilitas): void  $callback
     */
    public function processChunked(array $filters, callable $callback, int $chunkSize = 200): void
    {
        Fasilitas::query()
            ->select(['id', 'karyawan_nipy', 'nama_fasilitas', 'jumlah', 'tahun_terima', 'status', 'tanggal_kembali'])
            ->with(['karyawan:nipy,nama_lengkap,unit,jabatan'])
            ->filter($filters)
            ->orderBy('id')
            ->chunkById($chunkSize, function ($batch) use ($callback) {
                foreach ($batch as $fasilitas) {
                    $callback($fasilitas);
                }
            });
    }
}
