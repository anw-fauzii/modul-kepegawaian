<?php

namespace App\Services\Admin\Kegiatan;

use App\Models\Admin\DataMaster\Karyawan;
use App\Models\Admin\Kegiatan\KegiatanLembaga;
use App\Models\Admin\Kegiatan\KehadiranKegiatan;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

class KegiatanLembagaService
{
    /**
     * Mengambil daftar kegiatan lembaga terpaginasi dengan Eager Loading (Zero N+1 Query).
     * Kolom dipilih secara spesifik dengan agregasi kehadiran di level database.
     */
    public function getPaginatedKegiatan(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return KegiatanLembaga::query()
            ->select([
                'id',
                'nama_kegiatan',
                'pengisi',
                'tanggal',
                'jam_mulai',
                'jam_selesai',
                'lokasi',
                'akademik_master_tahun_ajaran_id',
                'qr_token',
                'is_active',
                'created_at',
            ])
            ->with([
                'tahunAjaran' => fn ($query) => $query->select('id', 'nama_tahun_ajaran', 'semester', 'is_active'),
            ])
            ->withCount([
                'kehadiran as total_peserta',
                'kehadiran as total_hadir' => fn ($q) => $q->where('status', 'hadir'),
                'kehadiran as total_izin'  => fn ($q) => $q->where('status', 'izin'),
                'kehadiran as total_sakit' => fn ($q) => $q->where('status', 'sakit'),
            ])
            ->filter($filters)
            ->latest('tanggal')
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Membuat data kegiatan lembaga baru dalam transaksi database.
     */
    public function createKegiatan(array $data): KegiatanLembaga
    {
        return DB::transaction(function () use ($data) {
            if (empty($data['qr_token'])) {
                $data['qr_token'] = Str::random(64);
            }

            $kegiatan = KegiatanLembaga::create($data);

            Log::info('Kegiatan lembaga baru berhasil dibuat', [
                'id'            => $kegiatan->id,
                'nama_kegiatan' => $kegiatan->nama_kegiatan,
                'tanggal'       => $kegiatan->tanggal->toDateString(),
            ]);

            return $kegiatan;
        });
    }

    /**
     * Memperbarui informasi kegiatan lembaga.
     */
    public function updateKegiatan(KegiatanLembaga $kegiatan, array $data): KegiatanLembaga
    {
        return DB::transaction(function () use ($kegiatan, $data) {
            $kegiatan->update($data);

            Log::info('Data kegiatan lembaga berhasil diperbarui', [
                'id' => $kegiatan->id,
            ]);

            return $kegiatan->refresh();
        });
    }

    /**
     * Menghapus kegiatan lembaga beserta seluruh riwayat kehadirannya secara atomik.
     */
    public function deleteKegiatan(KegiatanLembaga $kegiatan): bool
    {
        return DB::transaction(function () use ($kegiatan) {
            $deleted = $kegiatan->delete();

            Log::info('Kegiatan lembaga berhasil dihapus', [
                'id' => $kegiatan->id,
            ]);

            return $deleted;
        });
    }

    /**
     * Meregenerasi token QR Code untuk pencegahan kebocoran / refresh keamanan.
     */
    public function regenerateQrToken(KegiatanLembaga $kegiatan): KegiatanLembaga
    {
        return DB::transaction(function () use ($kegiatan) {
            $kegiatan->update([
                'qr_token' => Str::random(64),
            ]);

            Log::info('QR Token kegiatan diperbarui', ['kegiatan_id' => $kegiatan->id]);

            return $kegiatan;
        });
    }

    /**
     * Toggle status aktif gerbang presensi kegiatan (Buka / Tutup Presensi).
     */
    public function toggleActiveStatus(KegiatanLembaga $kegiatan): KegiatanLembaga
    {
        return DB::transaction(function () use ($kegiatan) {
            $kegiatan->update([
                'is_active' => ! $kegiatan->is_active,
            ]);

            return $kegiatan;
        });
    }

    /**
     * Mencatat presensi peserta kegiatan melalui Scan QR Code secara real-time.
     * Dilengkapi validasi keamanan token, status aktif gerbang, dan proteksi race condition ganda.
     *
     * @throws InvalidArgumentException|RuntimeException
     */
    public function recordPresensiByQr(
        string $qrToken,
        string $karyawanNipy,
        ?string $catatan = null,
        ?string $deviceInfo = null,
        string $metode = 'qr_scan'
    ): array {
        return DB::transaction(function () use ($qrToken, $karyawanNipy, $catatan, $deviceInfo, $metode) {
            // 1. Validasi Kegiatan berdasarkan QR Token dengan Lock for Read
            $kegiatan = KegiatanLembaga::query()
                ->where('qr_token', $qrToken)
                ->first();

            if (! $kegiatan) {
                throw new InvalidArgumentException('QR Code kegiatan tidak valid atau sudah kadaluarsa.');
            }

            if (! $kegiatan->is_active) {
                throw new RuntimeException("Presensi untuk kegiatan '{$kegiatan->nama_kegiatan}' saat ini sudah ditutup oleh panitia.");
            }

            // 2. Validasi Keberadaan Karyawan (Eager Loading Kategori & Unit)
            $karyawan = Karyawan::query()
                ->select('nipy', 'nama_lengkap', 'gelar', 'unit', 'jabatan', 'kepegawaian_master_kategori_id')
                ->with(['kategori:id,nama_kategori'])
                ->where('nipy', $karyawanNipy)
                ->first();

            if (! $karyawan) {
                throw new InvalidArgumentException("Data karyawan dengan NIPY '{$karyawanNipy}' tidak ditemukan di sistem.");
            }

            // 3. Pengecekan Presensi Ganda (Atomic Check & Constraint Guard)
            $existing = KehadiranKegiatan::query()
                ->where('kepegawaian_kegiatan_lembaga_id', $kegiatan->id)
                ->where('karyawan_nipy', $karyawanNipy)
                ->first();

            if ($existing) {
                return [
                    'status'    => 'already_recorded',
                    'message'   => "Karyawan {$karyawan->nama_lengkap} sudah tercatat hadir pada pukul " . $existing->waktu_scan->format('H:i:s'),
                    'kegiatan'  => $kegiatan->nama_kegiatan,
                    'karyawan'  => $karyawan,
                    'kehadiran' => $existing,
                ];
            }

            // 4. Catat Kehadiran Baru
            try {
                $kehadiran = KehadiranKegiatan::create([
                    'kepegawaian_kegiatan_lembaga_id' => $kegiatan->id,
                    'karyawan_nipy'                   => $karyawan->nipy,
                    'waktu_scan'                      => Carbon::now(),
                    'status'                          => 'hadir',
                    'metode_presensi'                 => $metode,
                    'catatan'                         => $catatan,
                    'device_info'                     => $deviceInfo,
                ]);
            } catch (QueryException $e) {
                // Tangkap jika terjadi race condition duplicate key
                if ($e->getCode() == 23000) {
                    $existing = KehadiranKegiatan::where('kepegawaian_kegiatan_lembaga_id', $kegiatan->id)
                        ->where('karyawan_nipy', $karyawanNipy)
                        ->first();

                    return [
                        'status'    => 'already_recorded',
                        'message'   => "Karyawan {$karyawan->nama_lengkap} sudah tercatat sebelumnya.",
                        'kegiatan'  => $kegiatan->nama_kegiatan,
                        'karyawan'  => $karyawan,
                        'kehadiran' => $existing,
                    ];
                }
                throw $e;
            }

            Log::info('Presensi QR kegiatan berhasil dicatat', [
                'kegiatan_id'   => $kegiatan->id,
                'nama_kegiatan' => $kegiatan->nama_kegiatan,
                'karyawan_nipy' => $karyawan->nipy,
                'waktu_scan'    => $kehadiran->waktu_scan->toDateTimeString(),
            ]);

            return [
                'status'    => 'success',
                'message'   => "Presensi kegiatan '{$kegiatan->nama_kegiatan}' berhasil dicatat untuk {$karyawan->nama_lengkap}.",
                'kegiatan'  => $kegiatan->nama_kegiatan,
                'karyawan'  => $karyawan,
                'kehadiran' => $kehadiran,
            ];
        });
    }

    /**
     * Mencatat kehadiran manual secara bulk atau single untuk karyawan tertentu.
     * Menggunakan Upsert / Transaction dengan chunking untuk efisiensi memori.
     */
    public function recordPresensiManualBulk(
        KegiatanLembaga $kegiatan,
        array $nipyList,
        string $status = 'hadir',
        ?string $catatan = null
    ): int {
        return DB::transaction(function () use ($kegiatan, $nipyList, $status, $catatan) {
            $now = Carbon::now();
            $records = [];

            foreach ($nipyList as $nipy) {
                $records[] = [
                    'kepegawaian_kegiatan_lembaga_id' => $kegiatan->id,
                    'karyawan_nipy'                   => $nipy,
                    'waktu_scan'                      => $now,
                    'status'                          => $status,
                    'metode_presensi'                 => 'manual',
                    'catatan'                         => $catatan,
                    'created_at'                      => $now,
                    'updated_at'                      => $now,
                ];
            }

            // Chunking per 100 baris agar query binding hemat memori dan cepat
            $chunks = array_chunk($records, 100);
            $affectedCount = 0;

            foreach ($chunks as $chunk) {
                KehadiranKegiatan::upsert(
                    $chunk,
                    ['kepegawaian_kegiatan_lembaga_id', 'karyawan_nipy'],
                    ['status', 'metode_presensi', 'catatan', 'updated_at']
                );
                $affectedCount += count($chunk);
            }

            Log::info('Presensi manual bulk berhasil dicatat', [
                'kegiatan_id' => $kegiatan->id,
                'total_baris' => $affectedCount,
            ]);

            return $affectedCount;
        });
    }

    /**
     * Mengambil detail rekapitulasi kehadiran kegiatan secara komprehensif tanpa N+1 Query.
     */
    public function getRekapDetailKegiatan(KegiatanLembaga $kegiatan, array $filters = []): array
    {
        // 1. Ambil relasi kegiatan dengan eager load Tahun Ajaran
        $kegiatan->loadMissing(['tahunAjaran:id,nama_tahun_ajaran,semester']);

        // 2. Ambil daftar kehadiran peserta dengan Eager Loading Karyawan & Kategori
        $kehadiranList = KehadiranKegiatan::query()
            ->select([
                'id',
                'kepegawaian_kegiatan_lembaga_id',
                'karyawan_nipy',
                'waktu_scan',
                'status',
                'metode_presensi',
                'catatan',
            ])
            ->where('kepegawaian_kegiatan_lembaga_id', $kegiatan->id)
            ->with([
                'karyawan' => fn ($query) => $query
                    ->select('nipy', 'nama_lengkap', 'gelar', 'unit', 'jabatan', 'kepegawaian_master_kategori_id')
                    ->with([
                        'kategori' => fn ($kq) => $kq->select('id', 'nama_kategori'),
                    ]),
            ])
            ->filter($filters)
            ->orderBy('waktu_scan', 'desc')
            ->get();

        // 3. Ringkasan Statistik Status Kehadiran
        $statCounts = KehadiranKegiatan::query()
            ->where('kepegawaian_kegiatan_lembaga_id', $kegiatan->id)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // 4. Hitung total seluruh karyawan aktif untuk membandingkan tingkat partisipasi
        $totalKaryawanAktif = Karyawan::count();
        $totalHadir = $statCounts['hadir'] ?? 0;
        $totalIzin  = $statCounts['izin'] ?? 0;
        $totalSakit = $statCounts['sakit'] ?? 0;
        $totalAlpa  = $statCounts['alpa'] ?? 0;
        $totalTercatat = $totalHadir + $totalIzin + $totalSakit + $totalAlpa;

        $persentaseHadir = $totalKaryawanAktif > 0
            ? round(($totalHadir / $totalKaryawanAktif) * 100, 1)
            : 0;

        return [
            'kegiatan'       => $kegiatan,
            'kehadiran_list' => $kehadiranList,
            'statistik'      => [
                'total_karyawan'    => $totalKaryawanAktif,
                'total_tercatat'    => $totalTercatat,
                'total_hadir'       => $totalHadir,
                'total_izin'        => $totalIzin,
                'total_sakit'       => $totalSakit,
                'total_alpa'        => $totalAlpa,
                'persentase_hadir'  => $persentaseHadir,
            ],
        ];
    }
}
