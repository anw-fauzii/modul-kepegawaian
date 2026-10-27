<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kepegawaian_kehadiran_kegiatan', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Relasi ke kegiatan lembaga
            $table->foreignId('kepegawaian_kegiatan_lembaga_id')
                ->constrained(
                    table: 'kepegawaian_kegiatan_lembaga',
                    indexName: 'kmk_kegiatan_fk'
                )
                ->cascadeOnDelete();

            // Relasi ke master karyawan via NIPY (String Key)
            $table->string('karyawan_nipy');
            $table->foreign('karyawan_nipy')
                ->references('nipy')
                ->on('kepegawaian_master_karyawan')
                ->cascadeOnDelete();

            $table->timestamp('waktu_scan')->useCurrent();
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alpa'])->default('hadir');
            $table->enum('metode_presensi', ['qr_scan', 'manual', 'admin_scan'])->default('qr_scan');
            $table->text('catatan')->nullable();
            $table->string('device_info')->nullable()->comment('User agent / device identifier audit');

            $table->timestamps();

            // Integrity Constraint: Mencegah scan/kehadiran ganda untuk karyawan yang sama pada 1 kegiatan
            $table->unique(
                ['kepegawaian_kegiatan_lembaga_id', 'karyawan_nipy'],
                'unique_kehadiran_kegiatan_karyawan'
            );

            // Performance Indexes
            $table->index(['kepegawaian_kegiatan_lembaga_id', 'status'], 'idx_kehadiran_kegiatan_status');
            $table->index('karyawan_nipy', 'idx_kehadiran_karyawan_nipy');
            $table->index('waktu_scan', 'idx_kehadiran_waktu_scan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kepegawaian_kehadiran_kegiatan');
    }
};
