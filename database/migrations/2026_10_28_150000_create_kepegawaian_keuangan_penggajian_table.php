<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kepegawaian_keuangan_penggajian', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Identitas (nama & email diambil dari relasi karyawan, tidak diduplikasi)
            $table->string('karyawan_nipy');
            $table->foreign('karyawan_nipy')
                ->references('nipy')
                ->on('kepegawaian_master_karyawan')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('kepegawaian_master_bulan_id')->nullable();
            $table->foreign('kepegawaian_master_bulan_id', 'fk_penggajian_bulan')
                ->references('id')
                ->on('kepegawaian_master_bulan')
                ->nullOnDelete();

            // Snapshot data kepegawaian saat slip dibuat (riwayat tetap utuh walau karyawan pindah unit/jabatan)
            $table->string('unit')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('golongan')->nullable();

            // Penerimaan
            foreach ([
                'gaji_pokok', 'tpp', 'pembulatan', 't_struktural', 't_fungsional',
                't_amanah_tambahan', 't_transport', 't_makan', 't_kesehatan', 't_keluarga',
                't_pendidikan_anak', 't_kuota', 'lain_lain', 'reward_kehadiran', 'kekurangan_gaji',
            ] as $col) {
                $table->decimal($col, 15, 2)->default(0);
            }

            // Potongan
            foreach ([
                'pot_makan', 'pot_kehadiran', 'pot_pendidikan', 'pot_kesehatan',
                'infaq', 'adm_sekolah', 'kelebihan_gaji',
            ] as $col) {
                $table->decimal($col, 15, 2)->default(0);
            }

            // Total (dihitung otomatis oleh model)
            $table->decimal('penerimaan', 15, 2)->default(0);
            $table->decimal('potongan', 15, 2)->default(0);
            $table->decimal('gaji_bersih', 15, 2)->default(0);

            $table->enum('status', ['draft', 'final', 'dibayar'])->default('draft');
            $table->timestamps();

            $table->unique(['karyawan_nipy', 'kepegawaian_master_bulan_id'], 'unique_penggajian_karyawan_bulan');
            $table->index('status', 'idx_penggajian_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kepegawaian_keuangan_penggajian');
    }
};
