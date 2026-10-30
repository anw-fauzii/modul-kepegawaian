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
        Schema::table('kepegawaian_master_karyawan', function (Blueprint $table) {
            $table->string('perguruan')->nullable()->after('akademik_pelengkap_jenjang_pendidikan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kepegawaian_master_karyawan', function (Blueprint $table) {
            $table->dropColumn('perguruan');
        });
    }
};
