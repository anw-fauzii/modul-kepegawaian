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
        Schema::create('kepegawaian_fasilitas', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Foreign Key ke kepegawaian_master_karyawan (NIPY)
            $table->string('karyawan_nipy');
            $table->foreign('karyawan_nipy')
                ->references('nipy')
                ->on('kepegawaian_master_karyawan')
                ->cascadeOnDelete();

            $table->string('nama_fasilitas');
            $table->unsignedInteger('jumlah')->default(1);
            $table->unsignedSmallInteger('tahun_terima');
            
            // Status: true = sudah dikembalikan, false = belum dikembalikan (default: false)
            $table->boolean('status')->default(false);
            
            // Kolom pelengkap audit & histori pengembalian
            $table->date('tanggal_kembali')->nullable();
            $table->text('catatan')->nullable();

            $table->timestamps();

            // Indexes untuk optimalisasi query filtering & pencegahan slow query
            $table->index('karyawan_nipy', 'idx_fasilitas_karyawan_nipy');
            $table->index('status', 'idx_fasilitas_status');
            $table->index('tahun_terima', 'idx_fasilitas_tahun_terima');
            $table->index(['karyawan_nipy', 'status'], 'idx_fasilitas_karyawan_status');
            $table->index(['status', 'tahun_terima'], 'idx_fasilitas_status_tahun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kepegawaian_fasilitas');
    }
};
