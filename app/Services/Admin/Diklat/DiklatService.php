<?php

namespace App\Services\Admin\Diklat;

use App\Models\Admin\Diklat\Diklat;
use App\Models\Admin\Kegiatan\KegiatanLembaga;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DiklatService
{
    /**
     * Nama disk storage untuk penyimpanan berkas sertifikat diklat.
     */
    protected const DISK = 'private';

    /**
     * Direktori basis penyimpanan berkas diklat.
     */
    protected const BASE_STORAGE_PATH = 'kepegawaian/diklat';

    /**
     * Mengambil daftar Diklat terpaginasi dengan Zero N+1 Eager Loading.
     */
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Diklat::query()
            ->select([
                'id',
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
                'created_at',
            ])
            ->with([
                // Zero N+1: Eager load Karyawan beserta Kategori
                'karyawan' => fn ($query) => $query
                    ->select('nipy', 'nama_lengkap', 'gelar', 'unit', 'jabatan', 'kepegawaian_master_kategori_id')
                    ->with('kategori:id,nama_kategori'),
                // Zero N+1: Eager load Kegiatan Lembaga terkait
                'kegiatanLembaga:id,nama_kegiatan,jenis_kegiatan,pengisi,tanggal,lokasi',
            ])
            ->filter($filters)
            ->latest('tanggal')
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Mengambil detail Diklat berdasarkan ID dengan relasi lengkap.
     */
    public function getById(int $id): Diklat
    {
        return Diklat::query()
            ->with([
                'karyawan' => fn ($query) => $query
                    ->select('nipy', 'nama_lengkap', 'gelar', 'unit', 'jabatan', 'whatsapp', 'kepegawaian_master_kategori_id')
                    ->with('kategori:id,nama_kategori'),
                'kegiatanLembaga',
            ])
            ->findOrFail($id);
    }

    /**
     * Menyimpan data Diklat baru dalam transaksi database yang aman dari orphan files.
     */
    public function create(array $data, ?UploadedFile $file = null): Diklat
    {
        $uploadedPath = null;

        try {
            // 1. Upload berkas sertifikat jika ada
            if ($file instanceof UploadedFile) {
                $nipy = $data['karyawan_nipy'] ?? 'unknown';
                $uploadedPath = $this->uploadBerkas($file, $nipy);
                $data['berkas'] = $uploadedPath;
            }

            // 2. Simpan record di database dalam transaksi
            $diklat = DB::transaction(function () use ($data) {
                return Diklat::create([
                    'karyawan_nipy'                   => $data['karyawan_nipy'],
                    'kategori'                        => strtolower($data['kategori']),
                    'nama_kegiatan'                   => $data['nama_kegiatan'],
                    'pelaksana'                       => $data['pelaksana'],
                    'materi'                          => $data['materi'],
                    'tanggal'                         => $data['tanggal'],
                    'tanggal_selesai'                 => $data['tanggal_selesai'] ?? null,
                    'tempat'                          => $data['tempat'] ?? null,
                    'jumlah_jam'                      => ! empty($data['jumlah_jam']) ? (int) $data['jumlah_jam'] : null,
                    'kepegawaian_kegiatan_lembaga_id' => $data['kepegawaian_kegiatan_lembaga_id'] ?? null,
                    'berkas'                          => $data['berkas'] ?? null,
                    'keterangan'                      => $data['keterangan'] ?? null,
                ]);
            });

            return $diklat;
        } catch (Exception $e) {
            // Rollback fisik: Hapus file jika database gagal menyimpan
            if ($uploadedPath && Storage::disk(self::DISK)->exists($uploadedPath)) {
                Storage::disk(self::DISK)->delete($uploadedPath);
            }

            Log::error('Gagal menyimpan data Diklat: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'data'  => $data,
            ]);

            throw new Exception('Terjadi kesalahan saat menyimpan data Diklat: ' . $e->getMessage());
        }
    }

    /**
     * Memperbarui data Diklat dan mengelola penggantian berkas sertifikat.
     */
    public function update(int $id, array $data, ?UploadedFile $file = null): Diklat
    {
        $diklat = Diklat::findOrFail($id);
        $oldFilePath = $diklat->berkas;
        $newUploadedPath = null;

        try {
            // 1. Upload berkas baru jika ada penggantian berkas
            if ($file instanceof UploadedFile) {
                $nipy = $data['karyawan_nipy'] ?? $diklat->karyawan_nipy;
                $newUploadedPath = $this->uploadBerkas($file, $nipy);
                $data['berkas'] = $newUploadedPath;
            }

            // 2. Update record dalam transaksi database
            DB::transaction(function () use ($diklat, $data) {
                $diklat->update([
                    'karyawan_nipy'                   => $data['karyawan_nipy'] ?? $diklat->karyawan_nipy,
                    'kategori'                        => strtolower($data['kategori'] ?? $diklat->kategori),
                    'nama_kegiatan'                   => $data['nama_kegiatan'] ?? $diklat->nama_kegiatan,
                    'pelaksana'                       => $data['pelaksana'] ?? $diklat->pelaksana,
                    'materi'                          => $data['materi'] ?? $diklat->materi,
                    'tanggal'                         => $data['tanggal'] ?? $diklat->tanggal,
                    'tanggal_selesai'                 => array_key_exists('tanggal_selesai', $data) ? $data['tanggal_selesai'] : $diklat->tanggal_selesai,
                    'tempat'                          => array_key_exists('tempat', $data) ? $data['tempat'] : $diklat->tempat,
                    'jumlah_jam'                      => array_key_exists('jumlah_jam', $data) ? ($data['jumlah_jam'] ? (int) $data['jumlah_jam'] : null) : $diklat->jumlah_jam,
                    'kepegawaian_kegiatan_lembaga_id' => array_key_exists('kepegawaian_kegiatan_lembaga_id', $data) ? $data['kepegawaian_kegiatan_lembaga_id'] : $diklat->kepegawaian_kegiatan_lembaga_id,
                    'berkas'                          => $data['berkas'] ?? $diklat->berkas,
                    'keterangan'                      => array_key_exists('keterangan', $data) ? $data['keterangan'] : $diklat->keterangan,
                ]);
            });

            // 3. Hapus berkas lama secara fisik jika berkas baru berhasil diupload
            if ($newUploadedPath && $oldFilePath && $oldFilePath !== $newUploadedPath) {
                $this->deleteFilePhysically($oldFilePath);
            }

            return $diklat->fresh();
        } catch (Exception $e) {
            // Rollback fisik berkas baru jika transaksi gagal
            if ($newUploadedPath && Storage::disk(self::DISK)->exists($newUploadedPath)) {
                Storage::disk(self::DISK)->delete($newUploadedPath);
            }

            Log::error("Gagal memperbarui Diklat ID {$id}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'data'  => $data,
            ]);

            throw new Exception('Terjadi kesalahan saat memperbarui data Diklat: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus record Diklat beserta berkas fisiknya.
     */
    public function delete(int $id): bool
    {
        $diklat = Diklat::findOrFail($id);
        $filePath = $diklat->berkas;

        try {
            DB::transaction(function () use ($diklat) {
                $diklat->delete();
            });

            if ($filePath) {
                $this->deleteFilePhysically($filePath);
            }

            return true;
        } catch (Exception $e) {
            Log::error("Gagal menghapus Diklat ID {$id}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            throw new Exception('Gagal menghapus data Diklat: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan preview berkas sertifikat Diklat secara inline.
     */
    public function previewBerkas(int $id): BinaryFileResponse|StreamedResponse
    {
        $diklat = Diklat::findOrFail($id);

        if (empty($diklat->berkas)) {
            abort(404, 'Berkas sertifikat diklat tidak ditemukan.');
        }

        $disk = Storage::disk(self::DISK)->exists($diklat->berkas) ? self::DISK : 'public';

        if (! Storage::disk($disk)->exists($diklat->berkas)) {
            abort(404, 'Berkas sertifikat diklat tidak ditemukan di storage server.');
        }

        return Storage::disk($disk)->response($diklat->berkas);
    }

    /**
     * Mengunduh berkas sertifikat / bukti fisik diklat.
     */
    public function downloadBerkas(int $id): BinaryFileResponse|StreamedResponse
    {
        $diklat = Diklat::findOrFail($id);

        $disk = (! empty($diklat->berkas) && Storage::disk(self::DISK)->exists($diklat->berkas)) ? self::DISK : 'public';

        if (empty($diklat->berkas) || ! Storage::disk($disk)->exists($diklat->berkas)) {
            abort(404, 'Berkas sertifikat diklat tidak ditemukan di storage server.');
        }

        $extension = pathinfo($diklat->berkas, PATHINFO_EXTENSION);
        $cleanNipy = Str::slug($diklat->karyawan_nipy);
        $cleanNama = Str::slug($diklat->nama_kegiatan);
        $downloadName = "SERTIFIKAT_DIKLAT_{$cleanNipy}_{$cleanNama}.{$extension}";

        return Storage::disk($disk)->download($diklat->berkas, $downloadName);
    }

    /**
     * Mengambil daftar kegiatan lembaga khusus yang berjenis 'pelatihan'.
     * Kegiatan berjenis rapat tidak disertakan sesuai aturan bisnis.
     */
    public function getKegiatanPelatihanList(): Collection
    {
        return KegiatanLembaga::query()
            ->where('jenis_kegiatan', 'pelatihan')
            ->select(['id', 'nama_kegiatan', 'pengisi', 'tanggal', 'lokasi', 'deskripsi'])
            ->orderByDesc('tanggal')
            ->get();
    }

    /**
     * Menghitung statistik ringkasan data Diklat untuk KPI cards.
     */
    public function getStatistik(): object
    {
        $totalDiklat = Diklat::count();
        $totalMandiri = Diklat::where('kategori', 'mandiri')->count();
        $totalLembaga = Diklat::where('kategori', 'lembaga')->count();
        $totalKaryawan = Diklat::distinct('karyawan_nipy')->count('karyawan_nipy');

        return (object) [
            'total_diklat'             => $totalDiklat,
            'total_mandiri'            => $totalMandiri,
            'total_lembaga'            => $totalLembaga,
            'total_karyawan_mengikuti' => $totalKaryawan,
        ];
    }

    /**
     * Upload berkas fisik ke disk storage publik.
     */
    protected function uploadBerkas(UploadedFile $file, string $nipy): string
    {
        $cleanNipy = Str::slug($nipy);
        $randomHash = Str::lower(Str::random(8));
        $timestamp = now()->format('Ymd_His');
        $extension = $file->getClientOriginalExtension() ?: 'pdf';

        $filename = "diklat_{$cleanNipy}_{$timestamp}_{$randomHash}.{$extension}";
        $folder = self::BASE_STORAGE_PATH . '/' . now()->format('Y');

        return $file->storeAs($folder, $filename, self::DISK);
    }

    /**
     * Hapus berkas fisik secara aman.
     */
    protected function deleteFilePhysically(?string $path): void
    {
        if ($path) {
            try {
                if (Storage::disk(self::DISK)->exists($path)) {
                    Storage::disk(self::DISK)->delete($path);
                }
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            } catch (Exception $e) {
                Log::warning("Gagal menghapus file fisik di '{$path}': " . $e->getMessage());
            }
        }
    }
}
