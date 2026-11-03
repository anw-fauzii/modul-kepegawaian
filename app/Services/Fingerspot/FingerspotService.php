<?php

namespace App\Services\Fingerspot;

use App\Models\Admin\DataMaster\JamKerja;
use App\Models\Admin\DataMaster\Karyawan;
use App\Models\Admin\Presensi\Presensi;
use App\Services\Admin\Presensi\PresensiService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FingerspotService
{
    protected ?string $apiToken;
    protected array $cloudIds;
    protected string $baseUrl;
    protected string $maxJamMasuk;

    public function __construct(
        protected PresensiService $presensiService
    ) {
        $this->apiToken    = (string) config('services.fingerspot.api_token');
        $this->cloudIds    = (array) config('services.fingerspot.cloud_ids', []);
        $this->baseUrl     = (string) config('services.fingerspot.base_url', 'https://developer.fingerspot.io/api');
        $this->maxJamMasuk = (string) config('services.fingerspot.max_jam_masuk', '10:00:00');
    }

    /**
     * Memproses satu data scan presensi (dari webhook realtime atau sync manual).
     *
     * @param  string|int  $pin
     * @param  Carbon|string  $scanTime
     * @param  string|null  $cloudId
     * @return array{success: bool, action: string, message: string, presensi: ?Presensi}
     */
    public function processScan(string|int $pin, Carbon|string $scanTime, ?string $cloudId = null): array
    {
        $pinStr = trim((string) $pin);
        $scan = $scanTime instanceof Carbon
            ? $scanTime->copy()->timezone('Asia/Jakarta')
            : Carbon::parse($scanTime, 'Asia/Jakarta');

        $tanggal = $scan->toDateString();
        $jam     = $scan->toTimeString();

        // 1. Cari Karyawan berdasarkan PIN (NIPY di mesin fingerspot)
        $karyawan = Karyawan::with('jamKerja')
            ->where('nipy', 'like', $pinStr . '-%')
            ->orWhere('nipy', $pinStr)
            ->first();

        // Fallback: Jika PIN numerik, coba cari dengan zero padding 8 digit
        if (! $karyawan && is_numeric($pinStr)) {
            $paddedPin = str_pad($pinStr, 8, '0', STR_PAD_LEFT);
            $karyawan = Karyawan::with('jamKerja')
                ->where('nipy', 'like', $paddedPin . '-%')
                ->orWhere('nipy', $paddedPin)
                ->first();
        }

        if (! $karyawan) {
            Log::warning('[Fingerspot] Karyawan tidak ditemukan untuk PIN: ' . $pinStr, [
                'pin'      => $pinStr,
                'scan'     => $scan->toDateTimeString(),
                'cloud_id' => $cloudId,
            ]);

            return [
                'success'  => false,
                'action'   => 'not_found',
                'message'  => "Pegawai dengan PIN/NIPY {$pinStr} tidak ditemukan dalam database.",
                'presensi' => null,
            ];
        }

        // 2. Tentukan Jam Kerja & Master Bulan
        $jamKerja = $karyawan->jamKerja ?: JamKerja::first();
        if (! $jamKerja) {
            Log::warning('[Fingerspot] Master Jam Kerja belum disetting untuk pegawai: ' . $karyawan->nipy);

            return [
                'success'  => false,
                'action'   => 'no_schedule',
                'message'  => "Master Jam Kerja belum disetting untuk pegawai {$karyawan->nama_lengkap}.",
                'presensi' => null,
            ];
        }

        $bulanId = $this->presensiService->determineBulanId($tanggal);

        // 3. Tentukan Jadwal Kerja Harian
        $jamMasukStandar = Carbon::parse($tanggal . ' ' . ($jamKerja->jam_masuk ?: '07:15:00'), 'Asia/Jakarta');
        $jamPulangStr = $scan->isFriday()
            ? ($jamKerja->jam_pulang_jumat ?: '11:30:00')
            : ($jamKerja->jam_pulang_senin_kamis ?: '15:30:00');
        $jamPulangStandar = Carbon::parse($tanggal . ' ' . $jamPulangStr, 'Asia/Jakarta');

        $batasAbsenMasuk = Carbon::parse($tanggal . ' ' . $this->maxJamMasuk, 'Asia/Jakarta');
        $minimalPulang   = $jamPulangStandar->copy()->subHours(2);

        // 4. Periksa apakah sudah ada presensi pada tanggal tersebut
        $presensi = Presensi::where('karyawan_nipy', $karyawan->nipy)
            ->whereDate('tanggal', $tanggal)
            ->first();

        // =========================================================================
        // KASUS A: BELUM ADA PRESENSI HARI INI (PROSES JAM MASUK)
        // =========================================================================
        if (! $presensi) {
            // Jika scan melewati batas maksimal absen masuk (misal jam 10:00)
            if ($scan->gt($batasAbsenMasuk)) {
                Log::info('[Fingerspot] Scan lewat batas maksimal jam masuk, tidak diproses sebagai masuk', [
                    'nipy'     => $karyawan->nipy,
                    'scan'     => $scan->toDateTimeString(),
                    'batas'    => $batasAbsenMasuk->toDateTimeString(),
                    'cloud_id' => $cloudId,
                ]);

                return [
                    'success'  => false,
                    'action'   => 'ignored_late_limit',
                    'message'  => "Scan presensi masuk melebihi batas jam {$this->maxJamMasuk}.",
                    'presensi' => null,
                ];
            }

            // Hitung keterlambatan (menit)
            $lateMinutes = $scan->gt($jamMasukStandar)
                ? (int) $jamMasukStandar->diffInMinutes($scan)
                : 0;

            $status = $lateMinutes > 0 ? 'terlambat' : 'hadir';

            $newPresensi = Presensi::create([
                'karyawan_nipy'                   => $karyawan->nipy,
                'kepegawaian_master_jam_kerja_id' => $karyawan->kepegawaian_master_jam_kerja_id ?: $jamKerja->id,
                'kepegawaian_master_bulan_id'     => $bulanId,
                'tanggal'                         => $tanggal,
                'jam_masuk'                       => $jam,
                'jam_pulang'                      => null,
                'status'                          => $status,
                'keterlambatan_menit'             => $lateMinutes,
                'izin_terlambat'                  => false,
                'pulang_cepat_menit'              => 0,
                'izin_pulang_cepat'               => false,
                'shaum'                           => false,
                'catatan'                         => 'Finger' . ($cloudId ? " [{$cloudId}]" : ''),
            ]);

            Log::info('[Fingerspot] Presensi masuk berhasil dicatat', [
                'nipy'        => $karyawan->nipy,
                'nama'        => $karyawan->nama_lengkap,
                'jam_masuk'   => $jam,
                'status'      => $status,
                'terlambat'   => $lateMinutes,
            ]);

            return [
                'success'  => true,
                'action'   => 'masuk',
                'message'  => "Presensi masuk {$karyawan->nama_lengkap} berhasil dicatat ({$status}).",
                'presensi' => $newPresensi,
            ];
        }

        // =========================================================================
        // KASUS B: SUDAH ADA PRESENSI HARI INI (PROSES JAM PULANG)
        // =========================================================================
        if (! $presensi->jam_masuk) {
            Log::info('[Fingerspot] Belum ada jam masuk tercatat, scan pulang diabaikan', [
                'nipy' => $karyawan->nipy,
                'scan' => $scan->toDateTimeString(),
            ]);

            return [
                'success'  => false,
                'action'   => 'ignored_no_checkin',
                'message'  => 'Jam masuk belum tercatat, jam pulang diabaikan.',
                'presensi' => $presensi,
            ];
        }

        // Cek apakah scan sudah memenuhi batas minimal kepulangan
        if ($scan->gte($minimalPulang)) {
            $pulangCepatMenit = $scan->lt($jamPulangStandar)
                ? (int) $jamPulangStandar->diffInMinutes($scan)
                : 0;

            // Perbarui jam pulang jika belum ada, atau jika scan baru lebih lambat dari jam pulang sebelumnya
            if (! $presensi->jam_pulang || $jam > $presensi->jam_pulang) {
                $presensi->update([
                    'jam_pulang'         => $jam,
                    'pulang_cepat_menit' => $pulangCepatMenit,
                ]);

                Log::info('[Fingerspot] Presensi pulang berhasil dicatat/diperbarui', [
                    'nipy'         => $karyawan->nipy,
                    'nama'         => $karyawan->nama_lengkap,
                    'jam_pulang'   => $jam,
                    'pulang_cepat' => $pulangCepatMenit,
                ]);

                return [
                    'success'  => true,
                    'action'   => 'pulang',
                    'message'  => "Presensi pulang {$karyawan->nama_lengkap} berhasil dicatat ({$jam}).",
                    'presensi' => $presensi->fresh(),
                ];
            }

            return [
                'success'  => true,
                'action'   => 'ignored_duplicate',
                'message'  => 'Jam pulang sebelumnya sudah lebih akhir.',
                'presensi' => $presensi,
            ];
        }

        return [
            'success'  => false,
            'action'   => 'ignored_too_early',
            'message'  => 'Scan belum mencapai batas waktu kepulangan.',
            'presensi' => $presensi,
        ];
    }

    /**
     * Mengambil log presensi mentah dari Fingerspot Cloud API untuk seluruh cloud_id yang terdaftar.
     *
     * @param  string  $startDate  Format: Y-m-d H:i:s
     * @param  string  $endDate    Format: Y-m-d H:i:s
     * @param  array|null  $cloudIds
     * @return array<int, array{pin: string, scan_time: Carbon, cloud_id: string}>
     */
    public function fetchAttlog(string $startDate, string $endDate, ?array $cloudIds = null): array
    {
        $targetCloudIds = ! empty($cloudIds) ? $cloudIds : $this->cloudIds;

        if (empty($this->apiToken)) {
            Log::error('[Fingerspot] API Token Fingerspot belum dikonfigurasi di .env atau services.php');
            return [];
        }

        $allLogs = [];

        foreach ($targetCloudIds as $cloudId) {
            $cloudId = trim((string) $cloudId);
            if (empty($cloudId)) {
                continue;
            }

            try {
                $response = Http::timeout(15)
                    ->retry(2, 200)
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $this->apiToken,
                        'Content-Type'  => 'application/json',
                    ])
                    ->post(rtrim($this->baseUrl, '/') . '/get_attlog', [
                        'trans_id'   => now()->timestamp,
                        'cloud_id'   => $cloudId,
                        'start_date' => $startDate,
                        'end_date'   => $endDate,
                    ]);

                if (! $response->successful()) {
                    Log::error('[Fingerspot] Gagal mengambil attlog dari cloud_id: ' . $cloudId, [
                        'status'   => $response->status(),
                        'response' => $response->body(),
                    ]);
                    continue;
                }

                $json = $response->json();
                if (! isset($json['data']) || ! is_array($json['data'])) {
                    Log::warning('[Fingerspot] Response tidak memiliki array data untuk cloud_id: ' . $cloudId, [
                        'response' => $json,
                    ]);
                    continue;
                }

                foreach ($json['data'] as $logItem) {
                    $pin = $logItem['pin'] ?? null;
                    $scanDateStr = $logItem['scan_date'] ?? ($logItem['scan'] ?? null);

                    if (! $pin || ! $scanDateStr) {
                        continue;
                    }

                    $allLogs[] = [
                        'pin'       => (string) $pin,
                        'scan_time' => Carbon::parse($scanDateStr, 'Asia/Jakarta'),
                        'cloud_id'  => $cloudId,
                    ];
                }
            } catch (\Throwable $e) {
                Log::error('[Fingerspot] Exception saat mengambil data Fingerspot cloud_id: ' . $cloudId, [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Urutkan seluruh log berdasarkan scan_time ASCENDING
        usort($allLogs, function ($a, $b) {
            return $a['scan_time']->timestamp <=> $b['scan_time']->timestamp;
        });

        return $allLogs;
    }

    /**
     * Menjalankan sinkronisasi presensi manual untuk rentang tanggal tertentu.
     *
     * @param  string|null  $startDate  Default: Awal hari ini (00:00:00)
     * @param  string|null  $endDate    Default: Akhir hari ini (23:59:59)
     * @param  array|null  $cloudIds
     * @return array{success: bool, total_fetched: int, masuk: int, pulang: int, ignored: int, errors: int, message: string}
     */
    public function syncManual(?string $startDate = null, ?string $endDate = null, ?array $cloudIds = null): array
    {
        $now = Carbon::now('Asia/Jakarta');

        $startCarbon = ! empty($startDate)
            ? Carbon::parse($startDate, 'Asia/Jakarta')->startOfDay()
            : (! empty($endDate)
                ? Carbon::parse($endDate, 'Asia/Jakarta')->startOfDay()
                : $now->copy()->startOfDay());

        $endCarbon = ! empty($endDate)
            ? Carbon::parse($endDate, 'Asia/Jakarta')->endOfDay()
            : (! empty($startDate)
                ? Carbon::parse($startDate, 'Asia/Jakarta')->endOfDay()
                : $now->copy()->endOfDay());

        if ($startCarbon->gt($endCarbon)) {
            [$startCarbon, $endCarbon] = [$endCarbon->copy()->startOfDay(), $startCarbon->copy()->endOfDay()];
        }

        $logs = [];
        $cursor = $startCarbon->copy();

        // Pecah per 2 hari agar sesuai dengan batas rentang tanggal API Fingerspot (/get_attlog)
        while ($cursor->lte($endCarbon)) {
            $chunkStart = $cursor->copy()->startOfDay();
            $chunkEnd   = $cursor->copy()->addDay()->endOfDay();

            if ($chunkEnd->gt($endCarbon)) {
                $chunkEnd = $endCarbon->copy();
            }

            $chunkLogs = $this->fetchAttlog(
                $chunkStart->format('Y-m-d H:i:s'),
                $chunkEnd->format('Y-m-d H:i:s'),
                $cloudIds
            );

            $logs = array_merge($logs, $chunkLogs);
            $cursor->addDays(2)->startOfDay();
        }

        usort($logs, function ($a, $b) {
            return $a['scan_time']->timestamp <=> $b['scan_time']->timestamp;
        });

        $stats = [
            'total_fetched' => count($logs),
            'masuk'         => 0,
            'pulang'        => 0,
            'ignored'       => 0,
            'errors'        => 0,
        ];

        foreach ($logs as $item) {
            try {
                $res = $this->processScan($item['pin'], $item['scan_time'], $item['cloud_id']);

                if ($res['success']) {
                    if ($res['action'] === 'masuk') {
                        $stats['masuk']++;
                    } elseif ($res['action'] === 'pulang') {
                        $stats['pulang']++;
                    } else {
                        $stats['ignored']++;
                    }
                } else {
                    if (str_starts_with($res['action'], 'ignored')) {
                        $stats['ignored']++;
                    } else {
                        $stats['errors']++;
                    }
                }
            } catch (\Throwable $e) {
                $stats['errors']++;
                Log::error('[Fingerspot] Error saat memproses log presensi', [
                    'pin'   => $item['pin'],
                    'waktu' => $item['scan_time']->toDateTimeString(),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return array_merge($stats, [
            'success' => true,
            'message' => "Sinkronisasi selesai. Ditemukan {$stats['total_fetched']} log: {$stats['masuk']} masuk, {$stats['pulang']} pulang, {$stats['ignored']} dilewati, {$stats['errors']} kendala/tidak ditemukan.",
        ]);
    }
}
