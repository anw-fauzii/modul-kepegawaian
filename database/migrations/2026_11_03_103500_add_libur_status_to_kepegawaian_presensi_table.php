<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('kepegawaian_presensi')) {
            $driver = Schema::getConnection()->getDriverName();

            if (in_array($driver, ['mysql', 'mariadb'])) {
                DB::statement("ALTER TABLE `kepegawaian_presensi` MODIFY COLUMN `status` ENUM('hadir', 'terlambat', 'izin', 'sakit', 'cuti', 'alpa', 'tugas', 'libur') NOT NULL DEFAULT 'hadir'");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('kepegawaian_presensi')) {
            $driver = Schema::getConnection()->getDriverName();

            if (in_array($driver, ['mysql', 'mariadb'])) {
                DB::table('kepegawaian_presensi')->where('status', 'libur')->update(['status' => 'izin']);
                DB::statement("ALTER TABLE `kepegawaian_presensi` MODIFY COLUMN `status` ENUM('hadir', 'terlambat', 'izin', 'sakit', 'cuti', 'alpa', 'tugas') NOT NULL DEFAULT 'hadir'");
            }
        }
    }
};

