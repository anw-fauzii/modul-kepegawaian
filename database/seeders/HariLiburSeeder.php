<?php

namespace Database\Seeders;

use App\Models\Admin\DataMaster\HariLibur;
use Illuminate\Database\Seeder;

class HariLiburSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $holidays = [
            // TAHUN 2026 (LIBUR NASIONAL & CUTI BERSAMA RESMI)
            ['tanggal' => '2026-01-01', 'nama' => 'Tahun Baru 2026 Masehi', 'is_libur_nasional' => true],
            ['tanggal' => '2026-01-16', 'nama' => 'Isra Miraj Nabi Muhammad SAW', 'is_libur_nasional' => true],
            ['tanggal' => '2026-02-17', 'nama' => 'Tahun Baru Imlek 2577 Kongzili', 'is_libur_nasional' => true],
            ['tanggal' => '2026-03-20', 'nama' => 'Hari Suci Nyepi (Tahun Baru Saka 1948)', 'is_libur_nasional' => true],
            ['tanggal' => '2026-03-21', 'nama' => 'Hari Raya Idul Fitri 1447 H (Hari Ke-1)', 'is_libur_nasional' => true],
            ['tanggal' => '2026-03-22', 'nama' => 'Hari Raya Idul Fitri 1447 H (Hari Ke-2)', 'is_libur_nasional' => true],
            ['tanggal' => '2026-03-23', 'nama' => 'Cuti Bersama Hari Raya Idul Fitri 1447 H', 'is_libur_nasional' => true],
            ['tanggal' => '2026-03-24', 'nama' => 'Cuti Bersama Hari Raya Idul Fitri 1447 H', 'is_libur_nasional' => true],
            ['tanggal' => '2026-04-03', 'nama' => 'Wafat Yesus Kristus (Jumat Agung)', 'is_libur_nasional' => true],
            ['tanggal' => '2026-04-05', 'nama' => 'Hari Kebangkitan Yesus Kristus (Paskah)', 'is_libur_nasional' => true],
            ['tanggal' => '2026-05-01', 'nama' => 'Hari Buruh Internasional', 'is_libur_nasional' => true],
            ['tanggal' => '2026-05-14', 'nama' => 'Kenaikan Yesus Kristus', 'is_libur_nasional' => true],
            ['tanggal' => '2026-05-27', 'nama' => 'Hari Raya Idul Adha 1447 H', 'is_libur_nasional' => true],
            ['tanggal' => '2026-05-31', 'nama' => 'Hari Raya Waisak 2570 BE', 'is_libur_nasional' => true],
            ['tanggal' => '2026-06-01', 'nama' => 'Hari Lahir Pancasila', 'is_libur_nasional' => true],
            ['tanggal' => '2026-06-16', 'nama' => 'Tahun Baru Islam 1448 Hijriah', 'is_libur_nasional' => true],
            ['tanggal' => '2026-08-17', 'nama' => 'Hari Kemerdekaan Republik Indonesia Ke-81', 'is_libur_nasional' => true],
            ['tanggal' => '2026-08-25', 'nama' => 'Maulid Nabi Muhammad SAW', 'is_libur_nasional' => true],
            ['tanggal' => '2026-12-25', 'nama' => 'Hari Raya Natal', 'is_libur_nasional' => true],
            ['tanggal' => '2026-12-26', 'nama' => 'Cuti Bersama Hari Raya Natal', 'is_libur_nasional' => true],

            // CONTOH TAHUN 2027
            ['tanggal' => '2027-01-01', 'nama' => 'Tahun Baru 2027 Masehi', 'is_libur_nasional' => true],
            ['tanggal' => '2027-08-17', 'nama' => 'Hari Kemerdekaan Republik Indonesia Ke-82', 'is_libur_nasional' => true],
            ['tanggal' => '2027-12-25', 'nama' => 'Hari Raya Natal', 'is_libur_nasional' => true],
        ];

        foreach ($holidays as $h) {
            HariLibur::firstOrCreate(
                ['tanggal' => $h['tanggal']],
                $h
            );
        }
    }
}
