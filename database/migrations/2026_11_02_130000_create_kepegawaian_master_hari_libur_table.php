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
        Schema::create('kepegawaian_master_hari_libur', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('tanggal')->unique()->comment('Tanggal hari libur');
            $table->string('nama')->comment('Nama hari libur nasional / cuti bersama / libur yayasan');
            $table->text('keterangan')->nullable()->comment('Keterangan tambahan');
            $table->boolean('is_libur_nasional')->default(true)->comment('Status apakah libur nasional resmi atau libur internal yayasan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kepegawaian_master_hari_libur');
    }
};
