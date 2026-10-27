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
        Schema::create('kepegawaian_tupoksi', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Foreign Key ke kepegawaian_master_karyawan (NIPY)
            $table->string('karyawan_nipy');
            $table->foreign('karyawan_nipy')
                ->references('nipy')
                ->on('kepegawaian_master_karyawan')
                ->cascadeOnDelete();

            // Foreign Key ke akademik_master_tahun_ajaran
            $table->foreignId('akademik_master_tahun_ajaran_id')
                ->constrained(
                    table: 'akademik_master_tahun_ajaran',
                    indexName: 'fk_tupoksi_tahun_ajaran'
                )
                ->cascadeOnDelete();

            // Nama Jabatan / Penugasan Tupoksi
            $table->string('jabatan', 255);

            // Path penyimpanan berkas dokumen Tupoksi di storage
            $table->string('berkas', 255)->nullable();

            // Keterangan atau catatan uraian tugas tambahan
            $table->text('keterangan')->nullable();

            $table->timestamps();

            // Performance Indexes untuk filtering performa tinggi
            $table->index('karyawan_nipy', 'idx_tupoksi_karyawan_nipy');
            $table->index('akademik_master_tahun_ajaran_id', 'idx_tupoksi_tahun_ajaran');
            $table->index('jabatan', 'idx_tupoksi_jabatan');
            $table->index(['karyawan_nipy', 'akademik_master_tahun_ajaran_id'], 'idx_tupoksi_karyawan_tahun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kepegawaian_tupoksi');
    }
};
