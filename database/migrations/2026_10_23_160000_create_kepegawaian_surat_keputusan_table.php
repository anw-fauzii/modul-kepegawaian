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
        Schema::create('kepegawaian_surat_keputusan', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Nomor SK Resmi (opsional, dapat diisi sesuai penomoran administrasi)
            $table->string('nomor_sk', 100)->nullable();

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
                    indexName: 'fk_sk_tahun_ajaran'
                )
                ->cascadeOnDelete();

            // Jabatan dalam SK (misal: Guru Kelas, Kepala Lab, Wali Kelas, dll)
            $table->string('jabatan', 150);

            // Path penyimpanan berkas dokumen SK di storage
            $table->string('berkas', 255);

            // Tanggal penerbitan / berlakunya SK
            $table->date('tanggal_sk')->nullable();

            // Catatan atau keterangan tambahan
            $table->text('keterangan')->nullable();

            $table->timestamps();

            // Indeks untuk optimasi performa tinggi pada query filtering & join
            $table->index('karyawan_nipy', 'idx_sk_karyawan_nipy');
            $table->index('akademik_master_tahun_ajaran_id', 'idx_sk_tahun_ajaran');
            $table->index(['karyawan_nipy', 'akademik_master_tahun_ajaran_id'], 'idx_sk_karyawan_tahun');
            $table->index('jabatan', 'idx_sk_jabatan');
            $table->index('nomor_sk', 'idx_sk_nomor');
            $table->index('tanggal_sk', 'idx_sk_tanggal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kepegawaian_surat_keputusan');
    }
};
