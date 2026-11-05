<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kepegawaian_riwayat_golongan', function (Blueprint $table) {
            $table->id();
            $table->string('karyawan_nipy');
            $table->string('golongan_lama')->nullable();
            $table->string('golongan_baru');
            $table->unsignedSmallInteger('masa_kerja_tahun')->default(0);
            $table->date('tanggal_efektif');
            $table->string('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('karyawan_nipy')
                ->references('nipy')
                ->on('kepegawaian_master_karyawan')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->unique(['karyawan_nipy', 'golongan_baru'], 'uq_riwayat_golongan');
            $table->index('tanggal_efektif');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kepegawaian_riwayat_golongan');
    }
};
