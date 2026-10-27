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
        Schema::create('kepegawaian_kegiatan_lembaga', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nama_kegiatan');
            $table->string('pengisi')->nullable()->comment('Narasumber, pemateri, atau pembina kegiatan');
            $table->date('tanggal');
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->string('lokasi')->nullable();

            // Relasi ke master tahun ajaran aktif
            $table->foreignId('akademik_master_tahun_ajaran_id')
                ->constrained(
                    table: 'akademik_master_tahun_ajaran',
                    indexName: 'kmk_master_tahun_ajaran_fk'
                )
                ->cascadeOnDelete();

            // Token acak unik untuk QR Code (tamper-resistant)
            $table->string('qr_token', 64)->unique('unique_kegiatan_qr_token');

            // Flag buka / tutup gerbang presensi
            $table->boolean('is_active')->default(true);
            $table->text('deskripsi')->nullable();

            $table->timestamps();

            // Performance Indexes
            $table->index(['tanggal', 'is_active'], 'idx_kegiatan_tanggal_active');
            $table->index('akademik_master_tahun_ajaran_id', 'idx_kegiatan_tahun_ajaran');
            $table->index('nama_kegiatan', 'idx_kegiatan_nama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kepegawaian_kegiatan_lembaga');
    }
};
