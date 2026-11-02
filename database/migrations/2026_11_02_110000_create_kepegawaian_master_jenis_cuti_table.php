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
        Schema::create('kepegawaian_master_jenis_cuti', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nama');
            $table->unsignedInteger('lama_hari')->default(1)->comment('Maksimal durasi cuti dalam hari');
            $table->text('keterangan')->nullable()->comment('Ketentuan atau catatan jenis cuti');
            $table->boolean('is_active')->default(true)->comment('Status aktif jenis cuti');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kepegawaian_master_jenis_cuti');
    }
};
