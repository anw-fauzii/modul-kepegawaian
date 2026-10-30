<?php

namespace App\Services\Admin\SertifikatPenghargaan;

use App\Models\Admin\SertifikatPenghargaan\SertifikatPenghargaan;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SertifikatPenghargaanService
{
    /**
     * Nama disk storage untuk penyimpanan berkas sertifikat penghargaan.
     */
    protected const DISK = 'private';

    /**
     * Direktori basis penyimpanan berkas sertifikat penghargaan di storage.
     */
    protected const BASE_STORAGE_PATH = 'kepegawaian/sertifikat_penghargaan';

    /**
     * Mengambil daftar Sertifikat Penghargaan dengan Zero N+1 Eager Loading dan kolom selektif.
     */
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return SertifikatPenghargaan::query()
            ->select([
                'id',
                'karyawan_nipy',
                'judul_penghargaan',
                'tahun',
                'berkas',
                'keterangan',
                'created_at',
            ])
            ->with([
                // Zero N+1: Eager load Karyawan beserta Kategori dengan selective columns
                'karyawan' => fn ($query) => $query
                    ->select('nipy', 'nama_lengkap', 'gelar', 'unit', 'jabatan', 'kepegawaian_master_kategori_id')
                    ->with('kategori:id,nama_kategori'),
            ])
            ->filter($filters)
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Mengambil detail Sertifikat Penghargaan berdasarkan ID dengan relasi lengkap.
     */
    public function getById(int $id): SertifikatPenghargaan
    {
        return SertifikatPenghargaan::query()
            ->select([
                'id',
                'karyawan_nipy',
                'judul_penghargaan',
                'tahun',
                'berkas',
                'keterangan',
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
     * Menyimpan data Sertifikat Penghargaan baru beserta berkas fisik.
     * Menggunakan transaksi database dan auto-cleanup berkas jika terjadi error.
     */
    public function create(array $data, ?UploadedFile $file = null): SertifikatPenghargaan
    {
        $uploadedPath = null;

        try {
            // 1. Upload berkas fisik jika ada
            if ($file instanceof UploadedFile) {
                $nipy = $data['karyawan_nipy'] ?? ($data['nipy'] ?? 'unknown');
                $tahun = (int) ($data['tahun'] ?? date('Y'));
                $uploadedPath = $this->uploadBerkas($file, $nipy, $tahun);
                $data['berkas'] = $uploadedPath;
            }

            // 2. Transaksi database penyimpanan record
            $penghargaan = DB::transaction(function () use ($data) {
                if (! empty($data['nipy']) && empty($data['karyawan_nipy'])) {
                    $data['karyawan_nipy'] = $data['nipy'];
                    unset($data['nipy']);
                }

                $record = SertifikatPenghargaan::create($data);

                Log::info("Sertifikat Penghargaan #{$record->id} ({$record->judul_penghargaan}) berhasil dicatat untuk karyawan NIPY {$record->karyawan_nipy}.");

                return $record;
            });

            return $penghargaan->load([
                'karyawan:nipy,nama_lengkap,gelar,unit,jabatan',
            ]);
        } catch (Exception $e) {
            // Bersihkan file unggahan jika transaksi database gagal
            if ($uploadedPath && Storage::disk(self::DISK)->exists($uploadedPath)) {
                Storage::disk(self::DISK)->delete($uploadedPath);
                Log::warning("Berkas sertifikat {$uploadedPath} dihapus karena rollback database: {$e->getMessage()}");
            }

            Log::error("Gagal menyimpan Sertifikat Penghargaan: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Memperbarui data Sertifikat Penghargaan dan mengganti berkas secara aman.
     */
    public function update(SertifikatPenghargaan $penghargaan, array $data, ?UploadedFile $newFile = null): SertifikatPenghargaan
    {
        $newUploadedPath = null;
        $oldFilePath = $penghargaan->berkas;

        try {
            if ($newFile instanceof UploadedFile) {
                $nipy = $data['karyawan_nipy'] ?? $penghargaan->karyawan_nipy;
                $tahun = (int) ($data['tahun'] ?? $penghargaan->tahun);
                $newUploadedPath = $this->uploadBerkas($newFile, $nipy, $tahun);
                $data['berkas'] = $newUploadedPath;
            }

            DB::transaction(function () use ($penghargaan, $data) {
                if (! empty($data['nipy']) && empty($data['karyawan_nipy'])) {
                    $data['karyawan_nipy'] = $data['nipy'];
                    unset($data['nipy']);
                }

                $penghargaan->update($data);
                Log::info("Sertifikat Penghargaan #{$penghargaan->id} berhasil diperbarui.");
            });

            // Hapus file lama hanya setelah commit DB sukses
            if ($newUploadedPath && $oldFilePath && $oldFilePath !== $newUploadedPath) {
                $this->deletePhysicalFile($oldFilePath);
            }

            return $penghargaan->fresh()->load([
                'karyawan:nipy,nama_lengkap,gelar,unit,jabatan',
            ]);
        } catch (Exception $e) {
            if ($newUploadedPath && Storage::disk(self::DISK)->exists($newUploadedPath)) {
                Storage::disk(self::DISK)->delete($newUploadedPath);
            }

            Log::error("Gagal memperbarui Sertifikat Penghargaan #{$penghargaan->id}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Menghapus Sertifikat Penghargaan beserta berkas fisik dari storage.
     */
    public function delete(SertifikatPenghargaan $penghargaan): bool
    {
        $filePath = $penghargaan->berkas;

        return DB::transaction(function () use ($penghargaan, $filePath) {
            $id = $penghargaan->id;
            $deleted = $penghargaan->delete();

            if ($deleted && $filePath) {
                $this->deletePhysicalFile($filePath);
            }

            Log::warning("Sertifikat Penghargaan #{$id} beserta berkas fisiknya telah dihapus.");
            return (bool) $deleted;
        });
    }

    /**
     * Menampilkan preview berkas fisik sertifikat secara inline (untuk iframe/img).
     */
    public function previewBerkas(SertifikatPenghargaan $penghargaan): StreamedResponse|BinaryFileResponse
    {
        if (empty($penghargaan->berkas)) {
            abort(404, 'Berkas fisik dokumen sertifikat tidak ditemukan.');
        }

        $disk = Storage::disk(self::DISK)->exists($penghargaan->berkas) ? self::DISK : 'public';

        if (! Storage::disk($disk)->exists($penghargaan->berkas)) {
            abort(404, 'Berkas fisik dokumen sertifikat tidak ditemukan di server.');
        }

        return Storage::disk($disk)->response($penghargaan->berkas);
    }

    /**
     * Mengunduh berkas fisik sertifikat dengan nama file yang rapi.
     */
    public function downloadBerkas(SertifikatPenghargaan $penghargaan): StreamedResponse|BinaryFileResponse
    {
        $disk = (! empty($penghargaan->berkas) && Storage::disk(self::DISK)->exists($penghargaan->berkas)) ? self::DISK : 'public';

        if (empty($penghargaan->berkas) || ! Storage::disk($disk)->exists($penghargaan->berkas)) {
            throw new Exception('Berkas fisik dokumen sertifikat tidak ditemukan di server.');
        }

        $penghargaan->loadMissing('karyawan:nipy,nama_lengkap');

        $cleanJudul = Str::slug($penghargaan->judul_penghargaan, '_');
        $cleanNama = Str::slug($penghargaan->karyawan?->nama_lengkap ?? $penghargaan->karyawan_nipy, '_');
        $extension = pathinfo($penghargaan->berkas, PATHINFO_EXTENSION) ?: 'pdf';

        $downloadFilename = "Sertifikat_{$cleanJudul}_{$cleanNama}_{$penghargaan->tahun}.{$extension}";

        return Storage::disk($disk)->download($penghargaan->berkas, $downloadFilename);
    }

    /**
     * Menghitung statistik ringkasan dalam 1 single query teroptimasi.
     */
    public function getStatistik(?int $tahun = null): object
    {
        $query = DB::table('kepegawaian_sertifikat_penghargaan');

        if ($tahun !== null) {
            $query->where('tahun', $tahun);
        }

        return $query->selectRaw("
            COUNT(*) as total_sertifikat,
            COUNT(DISTINCT karyawan_nipy) as total_karyawan_berprestasi,
            COUNT(DISTINCT tahun) as total_tahun_tercatat,
            COUNT(CASE WHEN berkas IS NOT NULL AND berkas != '' THEN 1 END) as total_berkas_terunggah
        ")->first();
    }

    /**
     * Pemrosesan data besar secara hemat memori (Memory Efficient Chunking).
     *
     * @param  array  $filters
     * @param  callable(SertifikatPenghargaan): void  $callback
     */
    public function processChunked(array $filters, callable $callback, int $chunkSize = 200): void
    {
        SertifikatPenghargaan::query()
            ->select(['id', 'karyawan_nipy', 'judul_penghargaan', 'tahun', 'berkas', 'keterangan'])
            ->with(['karyawan:nipy,nama_lengkap,unit,jabatan'])
            ->filter($filters)
            ->orderBy('id')
            ->chunkById($chunkSize, function ($records) use ($callback) {
                foreach ($records as $item) {
                    $callback($item);
                }
            });
    }

    /**
     * Helper internal untuk mengunggah file ke direktori yang terorganisir.
     */
    protected function uploadBerkas(UploadedFile $file, string $nipy, int $tahun): string
    {
        $sanitizedNipy = preg_replace('/[^A-Za-z0-9_\-]/', '_', $nipy);
        $timestamp = now()->format('Ymd_His');
        $random = Str::random(6);
        $extension = $file->getClientOriginalExtension() ?: 'pdf';

        $filename = "sertifikat_{$sanitizedNipy}_{$timestamp}_{$random}.{$extension}";
        $directory = self::BASE_STORAGE_PATH . "/{$tahun}/{$sanitizedNipy}";

        return $file->storeAs($directory, $filename, self::DISK);
    }

    /**
     * Helper internal untuk menghapus berkas fisik dari disk storage.
     */
    protected function deletePhysicalFile(?string $path): void
    {
        if (! empty($path)) {
            if (Storage::disk(self::DISK)->exists($path)) {
                Storage::disk(self::DISK)->delete($path);
            }
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            Log::info("Berkas fisik sertifikat dihapus dari storage: {$path}");
        }
    }
}
