<?php

namespace App\Console\Commands;

use App\Services\Admin\DataMaster\GolonganService;
use Illuminate\Console\Command;

class SyncGolonganCommand extends Command
{
    protected $signature = 'golongan:sync {--dry-run : Hanya tampilkan jumlah perubahan tanpa menyimpan}';

    protected $description = 'Perbarui golongan karyawan otomatis berdasarkan masa kerja & jenjang pendidikan';

    public function handle(GolonganService $service): int
    {
        $hasil = $service->sinkronkan((bool) $this->option('dry-run'));

        $this->info("Diperiksa: {$hasil['diperiksa']} | Berubah: {$hasil['berubah']} | Dilewati (data tidak lengkap): {$hasil['dilewati']}");
        if ($this->option('dry-run')) {
            $this->comment('Mode dry-run: tidak ada data yang disimpan.');
        }

        return self::SUCCESS;
    }
}
