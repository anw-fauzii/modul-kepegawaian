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
        Schema::create('kepegawaian_sertifikat_penghargaan', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Foreign Key ke kepegawaian_master_karyawan (NIPY)
            $table->string('karyawan_nipy');
            $table->foreign('karyawan_nipy')
                ->references('nipy')
                ->on('kepegawaian_master_karyawan')
                ->cascadeOnDelete();

            // Judul / Nama Sertifikat Penghargaan
            $table->string('judul_penghargaan', 255);

            // Tahun Penghargaan Diterima (4 digit)
            $table->unsignedSmallInteger('tahun');

            // Path penyimpanan berkas fisik sertifikat di storage
            $table->string('berkas', 255);

            // Catatan atau keterangan tambahan opsional
            $table->text('keterangan')->nullable();

            $table->timestamps();

            // Indeks performa tinggi untuk pencarian dan pemfilteran
            $table->index('karyawan_nipy', 'idx_penghargaan_karyawan_nipy');
            $table->index('tahun', 'idx_penghargaan_tahun');
            $table->index(['karyawan_nipy', 'tahun'], 'idx_penghargaan_karyawan_tahun');
            $table->index('judul_penghargaan', 'idx_penghargaan_judul');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kepegawaian_sertifikat_penghargaan');
    }
};
