<?php

namespace App\Console\Commands;

use App\Models\HariLibur;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HariLiburCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:hari-libur {year? : Tahun yang ingin diimpor, default tahun berjalan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import hari libur nasional dari API ke database';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $year = (int) ($this->argument('year') ?? date('Y'));

        $this->info("Memulai proses sinkronisasi hari libur nasional untuk tahun {$year}...");

        // 1. Coba ambil dari sumber utama API dayoffapi
        $data = $this->fetchFromPrimaryApi($year);

        // 2. Jika sumber utama tidak merespons (misal maintenance/overlimit), gunakan sumber cadangan kalender resmi
        if (empty($data)) {
            $this->warn("Sumber utama (dayoffapi.vercel.app) tidak merespons atau over-limit. Mencoba sumber data alternatif kalender resmi...");
            $data = $this->fetchFromFallbackApi($year);
        }

        if (empty($data)) {
            $this->error("Gagal mengambil data hari libur untuk tahun {$year} dari semua sumber API.");
            Log::error("HariLiburCommand: Gagal mengambil data hari libur untuk tahun {$year}.");
            return 1;
        }

        $importedCount = 0;

        foreach ($data as $event) {
            $tanggal = $event['tanggal'] ?? null;
            $keterangan = $event['keterangan'] ?? null;
            $is_cuti = filter_var($event['is_cuti'] ?? false, FILTER_VALIDATE_BOOLEAN);

            if ($tanggal) {
                HariLibur::updateOrCreate(
                    ['tanggal' => $tanggal],
                    [
                        'nama'              => $keterangan ?? 'Hari Libur Nasional',
                        'keterangan'        => $keterangan,
                        'is_cuti'           => $is_cuti,
                        'jenis'             => $is_cuti ? 'Cuti Bersama' : 'Nasional',
                        'is_libur_nasional' => true,
                    ]
                );
                $importedCount++;
            }
        }

        $message = "Hari libur {$year} berhasil diimport/diupdate ({$importedCount} data) ✅";
        $this->info($message);
        Log::info("HariLiburCommand: {$message}");

        return 0;
    }

    /**
     * Ambil data dari API utama (dayoffapi.vercel.app).
     */
    protected function fetchFromPrimaryApi(int $year): array
    {
        try {
            $url = "https://dayoffapi.vercel.app/api?year={$year}";
            $response = Http::timeout(10)->get($url);

            if ($response->successful()) {
                $json = $response->json();
                if (is_array($json) && ! empty($json)) {
                    return $json;
                }
            }
        } catch (\Throwable $e) {
            $this->warn("Error menghubungi API utama: " . $e->getMessage());
        }

        return [];
    }

    /**
     * Sumber cadangan (GitHub APIHariLibur_V2) jika API utama sedang nonaktif/maintenance.
     */
    protected function fetchFromFallbackApi(int $year): array
    {
        try {
            $fallbackUrl = "https://raw.githubusercontent.com/guangrei/APIHariLibur_V2/main/calendar.json";
            $response = Http::timeout(15)->get($fallbackUrl);

            if ($response->successful()) {
                $calendar = $response->json();
                $results = [];

                if (is_array($calendar)) {
                    foreach ($calendar as $dateStr => $val) {
                        if (str_starts_with($dateStr, (string) $year)) {
                            $summary = ! empty($val['summary'])
                                ? (is_array($val['summary']) ? implode(', ', $val['summary']) : (string) $val['summary'])
                                : 'Hari Libur Nasional';

                            $desc = ! empty($val['description'])
                                ? (is_array($val['description']) ? implode(', ', $val['description']) : (string) $val['description'])
                                : '';

                            $isCuti = str_contains(strtolower($summary), 'cuti') || str_contains(strtolower($desc), 'cuti');

                            $results[] = [
                                'tanggal'    => $dateStr,
                                'keterangan' => $summary,
                                'is_cuti'    => $isCuti,
                            ];
                        }
                    }
                }

                return $results;
            }
        } catch (\Throwable $e) {
            $this->error("Error menghubungi API cadangan: " . $e->getMessage());
        }

        return [];
    }
}
