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
        Schema::table('kepegawaian_presensi', function (Blueprint $table) {
            $table->boolean('shaum')->default(false)->after('catatan');
            $table->index(['tanggal', 'shaum'], 'idx_presensi_tanggal_shaum');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kepegawaian_presensi', function (Blueprint $table) {
            $table->dropIndex('idx_presensi_tanggal_shaum');
            $table->dropColumn('shaum');
        });
    }
};
