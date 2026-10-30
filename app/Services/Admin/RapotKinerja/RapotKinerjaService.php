<?php

namespace App\Services\Admin\RapotKinerja;

use App\Models\Admin\RapotKinerja\RapotKinerja;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RapotKinerjaService
{
    /**
     * Nama disk storage untuk penyimpanan berkas rapot kinerja.
     */
    protected const DISK = 'private';

    /**
     * Direktori basis penyimpanan berkas rapot kinerja.
     */
    protected const BASE_STORAGE_PATH = 'kepegawaian/rapot_kinerja';

    /**
     * Mengambil daftar Rapot Kinerja terpaginasi dengan Zero N+1 Eager Loading.
     */
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return RapotKinerja::query()
            ->select([
                'id',
                'karyawan_nipy',
                'akademik_master_tahun_ajaran_id',
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
     * Mengambil detail Rapot Kinerja berdasarkan ID.
     */
    public function getById(int $id): RapotKinerja
    {
        return RapotKinerja::query()
            ->select([
                'id',
                'karyawan_nipy',
                'akademik_master_tahun_ajaran_id',
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
     * Menyimpan data Rapot Kinerja baru dalam transaksi database yang aman dari orphan files.
     */
    public function create(array $data, ?UploadedFile $file = null): RapotKinerja
    {
        $uploadedPath = null;

        try {
            // 1. Upload berkas fisik ke disk
            if ($file instanceof UploadedFile) {
                $nipy = $data['karyawan_nipy'] ?? ($data['nipy'] ?? 'unknown');
                $tahunAjaranId = (int) ($data['akademik_master_tahun_ajaran_id'] ?? 0);
                $uploadedPath = $this->uploadBerkas($file, $nipy, $tahunAjaranId);
                $data['berkas'] = $uploadedPath;
            }

            // 2. Simpan record di database dalam transaksi
            $rapotKinerja = DB::transaction(function () use ($data) {
                if (! empty($data['nipy']) && empty($data['karyawan_nipy'])) {
                    $data['karyawan_nipy'] = $data['nipy'];
                    unset($data['nipy']);
                }

                $record = RapotKinerja::create($data);

                Log::info("Rapot Kinerja #{$record->id} berhasil diunggah untuk karyawan NIPY {$record->karyawan_nipy}.");

                return $record;
            });

            return $rapotKinerja->load([
                'karyawan:nipy,nama_lengkap,gelar,unit',
                'tahunAjaran:id,nama_tahun_ajaran,semester',
            ]);
        } catch (Exception $e) {
            // Rollback cleanup file
            if ($uploadedPath && Storage::disk(self::DISK)->exists($uploadedPath)) {
                Storage::disk(self::DISK)->delete($uploadedPath);
                Log::warning("File unggahan {$uploadedPath} dibatalkan & dibersihkan karena rollback database: {$e->getMessage()}");
            }

            Log::error("Gagal menyimpan Rapot Kinerja: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Memperbarui data Rapot Kinerja dan mengganti berkas secara aman.
     */
    public function update(RapotKinerja $rapot, array $data, ?UploadedFile $newFile = null): RapotKinerja
    {
        $newUploadedPath = null;
        $oldFilePath = $rapot->berkas;

        try {
            if ($newFile instanceof UploadedFile) {
                $nipy = $data['karyawan_nipy'] ?? $rapot->karyawan_nipy;
                $tahunAjaranId = (int) ($data['akademik_master_tahun_ajaran_id'] ?? $rapot->akademik_master_tahun_ajaran_id);
                $newUploadedPath = $this->uploadBerkas($newFile, $nipy, $tahunAjaranId);
                $data['berkas'] = $newUploadedPath;
            }

            DB::transaction(function () use ($rapot, $data) {
                if (! empty($data['nipy']) && empty($data['karyawan_nipy'])) {
                    $data['karyawan_nipy'] = $data['nipy'];
                    unset($data['nipy']);
                }

                $rapot->update($data);
                Log::info("Rapot Kinerja #{$rapot->id} berhasil diperbarui.");
            });

            // Hapus file lama hanya setelah commit DB sukses
            if ($newUploadedPath && $oldFilePath && $oldFilePath !== $newUploadedPath) {
                $this->deletePhysicalFile($oldFilePath);
            }

            return $rapot->fresh()->load([
                'karyawan:nipy,nama_lengkap,gelar,unit',
                'tahunAjaran:id,nama_tahun_ajaran,semester',
            ]);
        } catch (Exception $e) {
            if ($newUploadedPath && Storage::disk(self::DISK)->exists($newUploadedPath)) {
                Storage::disk(self::DISK)->delete($newUploadedPath);
            }

            Log::error("Gagal memperbarui Rapot Kinerja #{$rapot->id}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Menghapus Rapot Kinerja beserta berkas fisiknya.
     */
    public function delete(RapotKinerja $rapot): bool
    {
        $filePath = $rapot->berkas;

        return DB::transaction(function () use ($rapot, $filePath) {
            $rapotId = $rapot->id;
            $deleted = $rapot->delete();

            if ($deleted && $filePath) {
                $this->deletePhysicalFile($filePath);
            }

            Log::warning("Rapot Kinerja #{$rapotId} beserta berkas fisiknya telah dihapus.");
            return (bool) $deleted;
        });
    }

    /**
     * Menampilkan preview berkas Rapot Kinerja secara inline (untuk iframe/img).
     */
    public function previewBerkas(RapotKinerja $rapot): StreamedResponse|BinaryFileResponse
    {
        if (empty($rapot->berkas)) {
            abort(404, 'Berkas dokumen fisik Rapot Kinerja tidak ditemukan.');
        }

        $disk = Storage::disk(self::DISK)->exists($rapot->berkas) ? self::DISK : 'public';

        if (! Storage::disk($disk)->exists($rapot->berkas)) {
            abort(404, 'Berkas dokumen fisik Rapot Kinerja tidak ditemukan di server.');
        }

        return Storage::disk($disk)->response($rapot->berkas);
    }

    /**
     * Download berkas Rapot Kinerja dengan penamaan file terstruktur.
     */
    public function downloadBerkas(RapotKinerja $rapot): StreamedResponse|BinaryFileResponse
    {
        $disk = (! empty($rapot->berkas) && Storage::disk(self::DISK)->exists($rapot->berkas)) ? self::DISK : 'public';

        if (empty($rapot->berkas) || ! Storage::disk($disk)->exists($rapot->berkas)) {
            throw new Exception('Berkas dokumen fisik Rapot Kinerja tidak ditemukan di server.');
        }

        $rapot->loadMissing(['karyawan:nipy,nama_lengkap', 'tahunAjaran:id,nama_tahun_ajaran']);

        $cleanNama = Str::slug($rapot->karyawan?->nama_lengkap ?? $rapot->karyawan_nipy, '_');
        $cleanTahun = Str::slug($rapot->tahunAjaran?->nama_tahun_ajaran ?? 'tahun', '_');
        $extension = pathinfo($rapot->berkas, PATHINFO_EXTENSION) ?: 'pdf';

        $downloadFilename = "Rapot_Kinerja_{$cleanNama}_{$cleanTahun}.{$extension}";

        return Storage::disk($disk)->download($rapot->berkas, $downloadFilename);
    }

    /**
     * Menghitung statistik ringkasan rapot kinerja dalam 1 single query.
     */
    public function getStatistik(?int $tahunAjaranId = null): object
    {
        $query = DB::table('kepegawaian_rapot_kinerja');

        if ($tahunAjaranId !== null) {
            $query->where('akademik_master_tahun_ajaran_id', $tahunAjaranId);
        }

        return $query->selectRaw("
            COUNT(*) as total_rapot,
            COUNT(DISTINCT karyawan_nipy) as total_karyawan_memiliki_rapot,
            COUNT(CASE WHEN berkas IS NOT NULL AND berkas != '' THEN 1 END) as total_berkas_terunggah
        ")->first();
    }

    /**
     * Pemrosesan data besar secara hemat memori (Chunking).
     *
     * @param  array  $filters
     * @param  callable(RapotKinerja): void  $callback
     */
    public function processChunked(array $filters, callable $callback, int $chunkSize = 200): void
    {
        RapotKinerja::query()
            ->select(['id', 'karyawan_nipy', 'akademik_master_tahun_ajaran_id', 'berkas', 'keterangan'])
            ->with(['karyawan:nipy,nama_lengkap,unit', 'tahunAjaran:id,nama_tahun_ajaran,semester'])
            ->filter($filters)
            ->orderBy('id')
            ->chunkById($chunkSize, function ($records) use ($callback) {
                foreach ($records as $item) {
                    $callback($item);
                }
            });
    }

    protected function uploadBerkas(UploadedFile $file, string $nipy, int $tahunAjaranId): string
    {
        $sanitizedNipy = preg_replace('/[^A-Za-z0-9_\-]/', '_', $nipy);
        $timestamp = now()->format('Ymd_His');
        $random = Str::random(6);
        $extension = $file->getClientOriginalExtension() ?: 'pdf';

        $filename = "rapot_{$sanitizedNipy}_{$timestamp}_{$random}.{$extension}";
        $directory = self::BASE_STORAGE_PATH . "/ta_{$tahunAjaranId}/{$sanitizedNipy}";

        return $file->storeAs($directory, $filename, self::DISK);
    }

    protected function deletePhysicalFile(?string $path): void
    {
        if (! empty($path)) {
            if (Storage::disk(self::DISK)->exists($path)) {
                Storage::disk(self::DISK)->delete($path);
            }
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            Log::info("Berkas fisik rapot dihapus dari storage: {$path}");
        }
    }
}
