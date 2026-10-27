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
        Schema::create('kepegawaian_rapot_kinerja', function (Blueprint $table) {
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
                    indexName: 'fk_rapot_tahun_ajaran'
                )
                ->cascadeOnDelete();

            // Path penyimpanan berkas dokumen Rapot Kinerja di storage
            $table->string('berkas', 255);

            // Keterangan atau catatan evaluasi tambahan
            $table->text('keterangan')->nullable();

            $table->timestamps();

            // Performance Indexes untuk filtering performa tinggi
            $table->index('karyawan_nipy', 'idx_rapot_karyawan_nipy');
            $table->index('akademik_master_tahun_ajaran_id', 'idx_rapot_tahun_ajaran');
            $table->index(['karyawan_nipy', 'akademik_master_tahun_ajaran_id'], 'idx_rapot_karyawan_tahun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kepegawaian_rapot_kinerja');
    }
};
