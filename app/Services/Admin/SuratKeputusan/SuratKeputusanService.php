<?php

namespace App\Services\Admin\SuratKeputusan;

use App\Models\Admin\SuratKeputusan\SuratKeputusan;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SuratKeputusanService
{
    /**
     * Nama disk storage yang digunakan untuk menyimpan berkas dokumen SK.
     */
    protected const DISK = 'public';

    /**
     * Direktori basis penyimpanan berkas SK pada storage disk.
     */
    protected const BASE_STORAGE_PATH = 'kepegawaian/surat_keputusan';

    /**
     * Mengambil daftar Surat Keputusan terpaginasi dengan Eager Loading anti N+1 Query dan selective columns.
     */
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return SuratKeputusan::query()
            ->select([
                'id',
                'nomor_sk',
                'karyawan_nipy',
                'akademik_master_tahun_ajaran_id',
                'jabatan',
                'berkas',
                'tanggal_sk',
                'created_at',
            ])
            ->with([
                // Zero N+1: Eager load Karyawan beserta Kategori dengan kolom terpilih
                'karyawan' => fn ($query) => $query
                    ->select('nipy', 'nama_lengkap', 'gelar', 'unit', 'jabatan', 'kepegawaian_master_kategori_id')
                    ->with('kategori:id,nama_kategori'),
                // Zero N+1: Eager load Tahun Ajaran
                'tahunAjaran:id,nama_tahun_ajaran,semester,is_active',
            ])
            ->filter($filters)
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Mengambil detail Surat Keputusan berdasarkan ID dengan relasi lengkap.
     */
    public function getById(int $id): SuratKeputusan
    {
        return SuratKeputusan::query()
            ->select([
                'id',
                'nomor_sk',
                'karyawan_nipy',
                'akademik_master_tahun_ajaran_id',
                'jabatan',
                'berkas',
                'tanggal_sk',
                'keterangan',
                'created_at',
                'updated_at',
            ])
            ->with([
                'karyawan' => fn ($query) => $query
                    ->select('nipy', 'nama_lengkap', 'gelar', 'unit', 'jabatan', 'whatsapp', 'kepegawaian_master_kategori_id')
                    ->with('kategori:id,nama_kategori'),
                'tahunAjaran:id,nama_tahun_ajaran,semester,is_active',
            ])
            ->findOrFail($id);
    }

    /**
     * Menyimpan data Surat Keputusan baru beserta upload berkas fisik.
     * Menggunakan DB::transaction dan auto-cleanup berkas jika terjadi kegagalan transaksi.
     */
    public function create(array $data, ?UploadedFile $file = null): SuratKeputusan
    {
        $uploadedPath = null;

        try {
            // 1. Proses upload file ke storage jika ada file yang dikirimkan
            if ($file instanceof UploadedFile) {
                $nipy = $data['karyawan_nipy'] ?? ($data['nipy'] ?? 'unknown');
                $tahunAjaranId = (int) ($data['akademik_master_tahun_ajaran_id'] ?? 0);
                $uploadedPath = $this->uploadBerkas($file, $nipy, $tahunAjaranId);
                $data['berkas'] = $uploadedPath;
            }

            // 2. Eksekusi penyimpanan database di dalam Transaksi Database
            $suratKeputusan = DB::transaction(function () use ($data) {
                // Normalisasi kunci nipy jika terkirim 'nipy'
                if (! empty($data['nipy']) && empty($data['karyawan_nipy'])) {
                    $data['karyawan_nipy'] = $data['nipy'];
                    unset($data['nipy']);
                }

                $record = SuratKeputusan::create($data);

                Log::info("Surat Keputusan #{$record->id} berhasil dibuat untuk karyawan {$record->karyawan_nipy} (Jabatan: {$record->jabatan}).");

                return $record;
            });

            return $suratKeputusan->load([
                'karyawan:nipy,nama_lengkap,gelar,unit',
                'tahunAjaran:id,nama_tahun_ajaran,semester',
            ]);
        } catch (Exception $e) {
            // Anti Orphaned Files: Bersihkan file yang sudah diunggah jika database gagal commit
            if ($uploadedPath && Storage::disk(self::DISK)->exists($uploadedPath)) {
                Storage::disk(self::DISK)->delete($uploadedPath);
                Log::warning("File unggahan {$uploadedPath} dibatalkan & dibersihkan karena rollback database: {$e->getMessage()}");
            }

            Log::error("Gagal membuat Surat Keputusan: {$e->getMessage()}", [
                'trace' => $e->getTraceAsString(),
                'data'  => $data,
            ]);

            throw $e;
        }
    }

    /**
     * Memperbarui data Surat Keputusan dengan penanganan pergantian berkas yang aman.
     */
    public function update(SuratKeputusan $sk, array $data, ?UploadedFile $newFile = null): SuratKeputusan
    {
        $newUploadedPath = null;
        $oldFilePath = $sk->berkas;

        try {
            // 1. Upload berkas baru jika pengguna mengunggah berkas pengganti
            if ($newFile instanceof UploadedFile) {
                $nipy = $data['karyawan_nipy'] ?? $sk->karyawan_nipy;
                $tahunAjaranId = (int) ($data['akademik_master_tahun_ajaran_id'] ?? $sk->akademik_master_tahun_ajaran_id);
                $newUploadedPath = $this->uploadBerkas($newFile, $nipy, $tahunAjaranId);
                $data['berkas'] = $newUploadedPath;
            }

            // 2. Transaksi pembaharuan di database
            DB::transaction(function () use ($sk, $data) {
                if (! empty($data['nipy']) && empty($data['karyawan_nipy'])) {
                    $data['karyawan_nipy'] = $data['nipy'];
                    unset($data['nipy']);
                }

                $sk->update($data);

                Log::info("Surat Keputusan #{$sk->id} berhasil diperbarui.");
            });

            // 3. Jika berkas baru berhasil di-commit di database, hapus berkas fisik lama
            if ($newUploadedPath && $oldFilePath && $oldFilePath !== $newUploadedPath) {
                $this->deletePhysicalFile($oldFilePath);
            }

            return $sk->fresh()->load([
                'karyawan:nipy,nama_lengkap,gelar,unit',
                'tahunAjaran:id,nama_tahun_ajaran,semester',
            ]);
        } catch (Exception $e) {
            // Bersihkan file baru jika transaksi gagal
            if ($newUploadedPath && Storage::disk(self::DISK)->exists($newUploadedPath)) {
                Storage::disk(self::DISK)->delete($newUploadedPath);
            }

            Log::error("Gagal memperbarui Surat Keputusan #{$sk->id}: {$e->getMessage()}");

            throw $e;
        }
    }

    /**
     * Menghapus Surat Keputusan beserta berkas fisiknya dari storage.
     */
    public function delete(SuratKeputusan $sk): bool
    {
        $filePath = $sk->berkas;

        return DB::transaction(function () use ($sk, $filePath) {
            $skId = $sk->id;
            $deleted = $sk->delete();

            // Hapus file dari disk setelah record berhasil dihapus
            if ($deleted && $filePath) {
                $this->deletePhysicalFile($filePath);
            }

            Log::warning("Surat Keputusan #{$skId} beserta berkasnya telah dihapus.");

            return (bool) $deleted;
        });
    }

    /**
     * Mengunduh berkas SK dengan nama file yang rapi dan terstandarisasi.
     */
    public function downloadBerkas(SuratKeputusan $sk): StreamedResponse|BinaryFileResponse
    {
        if (empty($sk->berkas) || ! Storage::disk(self::DISK)->exists($sk->berkas)) {
            throw new Exception('Berkas dokumen fisik Surat Keputusan tidak ditemukan di server.');
        }

        // Muat relasi jika belum termuat untuk format nama unduhan
        $sk->loadMissing(['karyawan:nipy,nama_lengkap', 'tahunAjaran:id,nama_tahun_ajaran']);

        $cleanNama = Str::slug($sk->karyawan?->nama_lengkap ?? $sk->karyawan_nipy, '_');
        $cleanJabatan = Str::slug($sk->jabatan, '_');
        $cleanTahun = Str::slug($sk->tahunAjaran?->nama_tahun_ajaran ?? 'tahun', '_');
        $extension = pathinfo($sk->berkas, PATHINFO_EXTENSION) ?: 'pdf';

        $downloadFilename = "SK_{$cleanJabatan}_{$cleanNama}_{$cleanTahun}.{$extension}";

        return Storage::disk(self::DISK)->download($sk->berkas, $downloadFilename);
    }

    /**
     * Menghitung ringkasan statistik SK dalam single optimized aggregate query.
     */
    public function getStatistik(?int $tahunAjaranId = null): object
    {
        $query = DB::table('kepegawaian_surat_keputusan');

        if ($tahunAjaranId !== null) {
            $query->where('akademik_master_tahun_ajaran_id', $tahunAjaranId);
        }

        return $query->selectRaw("
            COUNT(*) as total_sk,
            COUNT(DISTINCT karyawan_nipy) as total_karyawan_ber_sk,
            COUNT(DISTINCT jabatan) as total_variasi_jabatan,
            COUNT(CASE WHEN berkas IS NOT NULL AND berkas != '' THEN 1 END) as total_berkas_terunggah
        ")->first();
    }

    /**
     * Memproses data SK dalam jumlah besar secara hemat memori (Memory Efficient Chunking).
     *
     * @param  array  $filters
     * @param  callable(SuratKeputusan): void  $callback
     */
    public function processChunked(array $filters, callable $callback, int $chunkSize = 200): void
    {
        SuratKeputusan::query()
            ->select([
                'id',
                'nomor_sk',
                'karyawan_nipy',
                'akademik_master_tahun_ajaran_id',
                'jabatan',
                'berkas',
                'tanggal_sk',
            ])
            ->with([
                'karyawan:nipy,nama_lengkap,unit,jabatan',
                'tahunAjaran:id,nama_tahun_ajaran,semester',
            ])
            ->filter($filters)
            ->orderBy('id')
            ->chunkById($chunkSize, function ($records) use ($callback) {
                foreach ($records as $item) {
                    $callback($item);
                }
            });
    }

    /**
     * Helper internal untuk mengunggah berkas ke direktori yang terorganisir rapi.
     */
    protected function uploadBerkas(UploadedFile $file, string $nipy, int $tahunAjaranId): string
    {
        $sanitizedNipy = preg_replace('/[^A-Za-z0-9_\-]/', '_', $nipy);
        $timestamp = now()->format('Ymd_His');
        $random = Str::random(6);
        $extension = $file->getClientOriginalExtension() ?: 'pdf';

        $filename = "sk_{$sanitizedNipy}_{$timestamp}_{$random}.{$extension}";
        $directory = self::BASE_STORAGE_PATH . "/ta_{$tahunAjaranId}/{$sanitizedNipy}";

        return $file->storeAs($directory, $filename, self::DISK);
    }

    /**
     * Helper internal untuk menghapus berkas fisik dari storage disk.
     */
    protected function deletePhysicalFile(?string $path): void
    {
        if (! empty($path) && Storage::disk(self::DISK)->exists($path)) {
            Storage::disk(self::DISK)->delete($path);
            Log::info("Berkas fisik dihapus dari storage: {$path}");
        }
    }
}
