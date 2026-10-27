<?php

namespace App\Services\Admin\Tupoksi;

use App\Models\Admin\Tupoksi\Tupoksi;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TupoksiService
{
    /**
     * Nama disk storage untuk penyimpanan berkas tupoksi.
     */
    protected const DISK = 'public';

    /**
     * Direktori basis penyimpanan berkas tupoksi.
     */
    protected const BASE_STORAGE_PATH = 'kepegawaian/tupoksi';

    /**
     * Mengambil daftar Tupoksi terpaginasi dengan Zero N+1 Eager Loading.
     */
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Tupoksi::query()
            ->select([
                'id',
                'karyawan_nipy',
                'akademik_master_tahun_ajaran_id',
                'jabatan',
                'berkas',
                'keterangan',
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
     * Mengambil detail Tupoksi berdasarkan ID dengan relasi lengkap.
     */
    public function getById(int $id): Tupoksi
    {
        return Tupoksi::query()
            ->select([
                'id',
                'karyawan_nipy',
                'akademik_master_tahun_ajaran_id',
                'jabatan',
                'berkas',
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
     * Menyimpan data Tupoksi baru dalam transaksi database yang aman dari orphan files.
     */
    public function create(array $data, ?UploadedFile $file = null): Tupoksi
    {
        $uploadedPath = null;

        try {
            // 1. Upload berkas fisik ke disk jika ada
            if ($file instanceof UploadedFile) {
                $nipy = $data['karyawan_nipy'] ?? 'unknown';
                $tahunAjaranId = (int) ($data['akademik_master_tahun_ajaran_id'] ?? 0);
                $uploadedPath = $this->uploadBerkas($file, $nipy, $tahunAjaranId);
                $data['berkas'] = $uploadedPath;
            }

            // 2. Simpan record di database dalam transaksi
            $tupoksi = DB::transaction(function () use ($data) {
                return Tupoksi::create([
                    'karyawan_nipy'                   => $data['karyawan_nipy'],
                    'akademik_master_tahun_ajaran_id' => (int) $data['akademik_master_tahun_ajaran_id'],
                    'jabatan'                         => $data['jabatan'],
                    'berkas'                          => $data['berkas'] ?? null,
                    'keterangan'                      => $data['keterangan'] ?? null,
                ]);
            });

            return $tupoksi;
        } catch (Exception $e) {
            // Rollback fisik: Hapus berkas yang sudah terunggah jika DB gagal
            if ($uploadedPath && Storage::disk(self::DISK)->exists($uploadedPath)) {
                Storage::disk(self::DISK)->delete($uploadedPath);
            }

            Log::error('Gagal menyimpan Tupoksi: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'data'  => $data,
            ]);

            throw new Exception('Terjadi kesalahan saat menyimpan data Tupoksi: ' . $e->getMessage());
        }
    }

    /**
     * Memperbarui data Tupoksi dan mengelola penggantian berkas.
     */
    public function update(int $id, array $data, ?UploadedFile $file = null): Tupoksi
    {
        $tupoksi = Tupoksi::findOrFail($id);
        $oldFilePath = $tupoksi->berkas;
        $newUploadedPath = null;

        try {
            // 1. Upload berkas baru jika ada file yang diunggah
            if ($file instanceof UploadedFile) {
                $nipy = $data['karyawan_nipy'] ?? $tupoksi->karyawan_nipy;
                $tahunAjaranId = (int) ($data['akademik_master_tahun_ajaran_id'] ?? $tupoksi->akademik_master_tahun_ajaran_id);
                $newUploadedPath = $this->uploadBerkas($file, $nipy, $tahunAjaranId);
                $data['berkas'] = $newUploadedPath;
            }

            // 2. Update record dalam transaksi database
            DB::transaction(function () use ($tupoksi, $data) {
                $tupoksi->update([
                    'karyawan_nipy'                   => $data['karyawan_nipy'] ?? $tupoksi->karyawan_nipy,
                    'akademik_master_tahun_ajaran_id' => (int) ($data['akademik_master_tahun_ajaran_id'] ?? $tupoksi->akademik_master_tahun_ajaran_id),
                    'jabatan'                         => $data['jabatan'] ?? $tupoksi->jabatan,
                    'berkas'                          => $data['berkas'] ?? $tupoksi->berkas,
                    'keterangan'                      => array_key_exists('keterangan', $data) ? $data['keterangan'] : $tupoksi->keterangan,
                ]);
            });

            // 3. Hapus berkas lama secara fisik jika berkas baru berhasil diupdate
            if ($newUploadedPath && $oldFilePath && $oldFilePath !== $newUploadedPath) {
                $this->deleteFilePhysically($oldFilePath);
            }

            return $tupoksi->fresh();
        } catch (Exception $e) {
            // Rollback fisik berkas baru jika transaksi gagal
            if ($newUploadedPath && Storage::disk(self::DISK)->exists($newUploadedPath)) {
                Storage::disk(self::DISK)->delete($newUploadedPath);
            }

            Log::error("Gagal memperbarui Tupoksi ID {$id}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'data'  => $data,
            ]);

            throw new Exception('Terjadi kesalahan saat memperbarui data Tupoksi: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus data Tupoksi beserta berkas fisiknya.
     */
    public function delete(int $id): bool
    {
        $tupoksi = Tupoksi::findOrFail($id);
        $filePath = $tupoksi->berkas;

        try {
            DB::transaction(function () use ($tupoksi) {
                $tupoksi->delete();
            });

            // Hapus file fisik setelah database berhasil terhapus
            if ($filePath) {
                $this->deleteFilePhysically($filePath);
            }

            return true;
        } catch (Exception $e) {
            Log::error("Gagal menghapus Tupoksi ID {$id}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            throw new Exception('Gagal menghapus data Tupoksi: ' . $e->getMessage());
        }
    }

    /**
     * Mengunduh berkas fisik dokumen Tupoksi.
     */
    public function downloadBerkas(int $id): BinaryFileResponse|StreamedResponse
    {
        $tupoksi = Tupoksi::findOrFail($id);

        if (empty($tupoksi->berkas) || ! Storage::disk(self::DISK)->exists($tupoksi->berkas)) {
            abort(404, 'Berkas dokumen Tupoksi tidak ditemukan di storage server.');
        }

        $extension = pathinfo($tupoksi->berkas, PATHINFO_EXTENSION);
        $cleanNipy = Str::slug($tupoksi->karyawan_nipy);
        $cleanJabatan = Str::slug($tupoksi->jabatan);
        $downloadName = "TUPOKSI_{$cleanNipy}_{$cleanJabatan}.{$extension}";

        return Storage::disk(self::DISK)->download($tupoksi->berkas, $downloadName);
    }

    /**
     * Menghitung statistik ringkasan data Tupoksi untuk dashboard / KPI cards.
     */
    public function getStatistik(?int $tahunAjaranId = null): object
    {
        $query = Tupoksi::query();

        if ($tahunAjaranId) {
            $query->where('akademik_master_tahun_ajaran_id', $tahunAjaranId);
        }

        $totalTupoksi = (clone $query)->count();
        $totalKaryawan = (clone $query)->distinct('karyawan_nipy')->count('karyawan_nipy');
        $totalBerkas = (clone $query)->whereNotNull('berkas')->where('berkas', '!=', '')->count();

        return (object) [
            'total_tupoksi'                    => $totalTupoksi,
            'total_karyawan_memiliki_tupoksi'  => $totalKaryawan,
            'total_berkas_terunggah'           => $totalBerkas,
        ];
    }

    /**
     * Upload dan enkapsulasi penamaan berkas fisik di disk public.
     */
    protected function uploadBerkas(UploadedFile $file, string $nipy, int $tahunAjaranId): string
    {
        $cleanNipy = Str::slug($nipy);
        $randomHash = Str::lower(Str::random(8));
        $timestamp = now()->format('Ymd_His');
        $extension = $file->getClientOriginalExtension() ?: 'pdf';

        $filename = "tupoksi_{$cleanNipy}_{$timestamp}_{$randomHash}.{$extension}";
        $folder = self::BASE_STORAGE_PATH . ($tahunAjaranId ? "/ta_{$tahunAjaranId}" : '');

        return $file->storeAs($folder, $filename, self::DISK);
    }

    /**
     * Hapus berkas fisik secara aman.
     */
    protected function deleteFilePhysically(?string $path): void
    {
        if ($path && Storage::disk(self::DISK)->exists($path)) {
            try {
                Storage::disk(self::DISK)->delete($path);
            } catch (Exception $e) {
                Log::warning("Gagal menghapus file fisik di '{$path}': " . $e->getMessage());
            }
        }
    }
}
