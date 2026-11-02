<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Penjadwalan Otomatis (Task Scheduling):
 * 1. Otomatis import data hari libur nasional setiap tahun baru (1 Januari pukul 00:01).
 * 2. Sinkronisasi berkala setiap awal bulan pukul 01:00 WIB untuk mengantisipasi revisi SKB 3 Menteri resmi.
 */
Schedule::command('import:hari-libur')
    ->yearlyOn(1, 1, '00:01')
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('import:hari-libur')
    ->monthlyOn(1, '01:00')
    ->withoutOverlapping()
    ->runInBackground();
