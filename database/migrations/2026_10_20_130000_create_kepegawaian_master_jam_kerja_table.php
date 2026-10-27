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
        Schema::create('kepegawaian_master_jam_kerja', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nama');
            $table->time('jam_masuk');
            $table->time('jam_pulang_senin_kamis');
            $table->time('jam_pulang_jumat');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kepegawaian_master_jam_kerja');
    }
};
