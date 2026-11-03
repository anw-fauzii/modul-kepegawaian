<?php

namespace App\Console\Commands;

use App\Services\Fingerspot\FingerspotService;
use Illuminate\Console\Command;

class SyncFingerspotCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fingerspot:sync 
                            {--start= : Tanggal mulai format Y-m-d H:i:s} 
                            {--end= : Tanggal selesai format Y-m-d H:i:s}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi log presensi dari Fingerspot Cloud Developer API';

    /**
     * Execute the console command.
     */
    public function handle(FingerspotService $fingerspotService): int
    {
        $startDate = $this->option('start');
        $endDate   = $this->option('end');

        $this->info('Memulai sinkronisasi presensi Fingerspot...');
        $this->comment('Periode: ' . ($startDate ?: 'Hari ini (Awal)') . ' s/d ' . ($endDate ?: 'Hari ini (Akhir)'));

        $result = $fingerspotService->syncManual($startDate, $endDate);

        $this->table(
            ['Metrik', 'Jumlah'],
            [
                ['Total Log Diambil', $result['total_fetched']],
                ['Presensi Masuk Diproses', $result['masuk']],
                ['Presensi Pulang Diproses', $result['pulang']],
                ['Scan Diabaikan/Duplikat', $result['ignored']],
                ['Error/Tidak Ditemukan', $result['errors']],
            ]
        );

        $this->info($result['message']);

        return Command::SUCCESS;
    }
}
