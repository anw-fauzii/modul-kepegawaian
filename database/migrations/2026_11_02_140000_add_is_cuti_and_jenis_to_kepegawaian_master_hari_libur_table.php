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
        Schema::table('kepegawaian_master_hari_libur', function (Blueprint $table) {
            if (! Schema::hasColumn('kepegawaian_master_hari_libur', 'is_cuti')) {
                $table->boolean('is_cuti')->default(false)->after('keterangan')->comment('Penanda apakah cuti bersama');
            }
            if (! Schema::hasColumn('kepegawaian_master_hari_libur', 'jenis')) {
                $table->string('jenis')->default('Nasional')->nullable()->after('is_cuti')->comment('Jenis hari libur: Nasional / Cuti Bersama / Yayasan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kepegawaian_master_hari_libur', function (Blueprint $table) {
            if (Schema::hasColumn('kepegawaian_master_hari_libur', 'jenis')) {
                $table->dropColumn('jenis');
            }
            if (Schema::hasColumn('kepegawaian_master_hari_libur', 'is_cuti')) {
                $table->dropColumn('is_cuti');
            }
        });
    }
};
