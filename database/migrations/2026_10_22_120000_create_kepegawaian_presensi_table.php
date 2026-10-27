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
        Schema::create('kepegawaian_presensi', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Foreign key to kepegawaian_master_karyawan (NIPY)
            $table->string('karyawan_nipy');
            $table->foreign('karyawan_nipy')
                ->references('nipy')
                ->on('kepegawaian_master_karyawan')
                ->cascadeOnDelete();

            // Snapshot of Jam Kerja
            $table->foreignId('kepegawaian_master_jam_kerja_id')
                ->nullable()
                ->constrained('kepegawaian_master_jam_kerja')
                ->nullOnDelete();

            // Relasi ke Periode Master Bulan (Tutup Buku setiap tgl 28)
            $table->foreignId('kepegawaian_master_bulan_id')
                ->nullable()
                ->constrained('kepegawaian_master_bulan')
                ->nullOnDelete();

            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_pulang')->nullable();

            $table->enum('status', ['hadir', 'terlambat', 'izin', 'sakit', 'cuti', 'alpa', 'tugas'])
                ->default('hadir');

            // Kalkulasi keterlambatan & izin dispensasi
            $table->integer('keterlambatan_menit')->default(0);
            $table->boolean('izin_terlambat')->default(false);

            // Kalkulasi kepulangan lebih awal & izin dispensasi
            $table->integer('pulang_cepat_menit')->default(0);
            $table->boolean('izin_pulang_cepat')->default(false);

            $table->text('catatan')->nullable();
            $table->timestamps();

            // Performance & Integrity Indexes
            $table->unique(['karyawan_nipy', 'tanggal'], 'unique_presensi_karyawan_tanggal');
            $table->index(['kepegawaian_master_bulan_id', 'status'], 'idx_presensi_bulan_status');
            $table->index(['tanggal', 'status'], 'idx_presensi_tanggal_status');
            $table->index('tanggal', 'idx_presensi_tanggal');
            $table->index('status', 'idx_presensi_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kepegawaian_presensi');
    }
};
