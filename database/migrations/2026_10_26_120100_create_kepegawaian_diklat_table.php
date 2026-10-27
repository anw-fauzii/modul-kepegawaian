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
        Schema::create('kepegawaian_diklat', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Foreign Key ke kepegawaian_master_karyawan (NIPY)
            $table->string('karyawan_nipy');
            $table->foreign('karyawan_nipy')
                ->references('nipy')
                ->on('kepegawaian_master_karyawan')
                ->cascadeOnDelete();

            // Kategori: 'mandiri' atau 'lembaga'
            $table->string('kategori', 50)->comment('Kategori diklat: mandiri atau lembaga');

            // Nama Kegiatan Diklat / Pelatihan
            $table->string('nama_kegiatan', 255);

            // Penyelenggara / Pelaksana
            $table->string('pelaksana', 255);

            // Materi / Topik Pelatihan
            $table->text('materi');

            // Tanggal Pelaksanaan
            $table->date('tanggal');
            $table->date('tanggal_selesai')->nullable();

            // Informasi Tambahan
            $table->string('tempat', 255)->nullable();
            $table->unsignedInteger('jumlah_jam')->nullable()->comment('Jumlah Jam Pelajaran (JP)');

            // Relasi ke kepegawaian_kegiatan_lembaga (opsional, khusus jika kategori lembaga dan jenis_kegiatan pelatihan)
            $table->foreignId('kepegawaian_kegiatan_lembaga_id')
                ->nullable()
                ->constrained(
                    table: 'kepegawaian_kegiatan_lembaga',
                    indexName: 'fk_diklat_kegiatan_lembaga'
                )
                ->nullOnDelete();

            // Path penyimpanan sertifikat / berkas bukti diklat
            $table->string('berkas', 255)->nullable();

            // Keterangan / Catatan Tambahan
            $table->text('keterangan')->nullable();

            $table->timestamps();

            // Performance Indexes
            $table->index('karyawan_nipy', 'idx_diklat_karyawan_nipy');
            $table->index('kategori', 'idx_diklat_kategori');
            $table->index('tanggal', 'idx_diklat_tanggal');
            $table->index('kepegawaian_kegiatan_lembaga_id', 'idx_diklat_kegiatan_lembaga');
            $table->index(['karyawan_nipy', 'kategori'], 'idx_diklat_karyawan_kategori');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kepegawaian_diklat');
    }
};
