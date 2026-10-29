<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Buat index mandiri pada karyawan_nipy agar FK tidak menahan unique constraint lama
        Schema::table('kepegawaian_keuangan_penggajian', function (Blueprint $table) {
            $table->index('karyawan_nipy', 'idx_pg_karyawan_nipy');
        });

        // 2. Drop unique lama dan pasang composite unique baru bersama jenis
        Schema::table('kepegawaian_keuangan_penggajian', function (Blueprint $table) {
            if (! Schema::hasColumn('kepegawaian_keuangan_penggajian', 'jenis')) {
                $table->enum('jenis', ['gaji', 'thr', 'bonus'])
                    ->default('gaji')
                    ->after('kepegawaian_master_bulan_id');
            }

            $table->dropUnique('unique_penggajian_karyawan_bulan');

            $table->unique(
                ['karyawan_nipy', 'kepegawaian_master_bulan_id', 'jenis'],
                'unique_penggajian_karyawan_bulan_jenis'
            );
            $table->index('jenis', 'idx_penggajian_jenis');
        });
    }

    public function down(): void
    {
        Schema::table('kepegawaian_keuangan_penggajian', function (Blueprint $table) {
            $table->dropIndex('idx_penggajian_jenis');
            $table->dropUnique('unique_penggajian_karyawan_bulan_jenis');

            $table->unique(
                ['karyawan_nipy', 'kepegawaian_master_bulan_id'],
                'unique_penggajian_karyawan_bulan'
            );

            $table->dropIndex('idx_pg_karyawan_nipy');

            if (Schema::hasColumn('kepegawaian_keuangan_penggajian', 'jenis')) {
                $table->dropColumn('jenis');
            }
        });
    }
};
