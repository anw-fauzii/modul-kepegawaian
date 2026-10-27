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
        Schema::table('kepegawaian_kegiatan_lembaga', function (Blueprint $table) {
            if (! Schema::hasColumn('kepegawaian_kegiatan_lembaga', 'jenis_kegiatan')) {
                $table->string('jenis_kegiatan', 50)
                    ->default('rapat')
                    ->after('nama_kegiatan')
                    ->comment('Jenis kegiatan: pelatihan (diklat), rapat, pembinaan, seminar, lainnya');
                
                $table->index('jenis_kegiatan', 'idx_kegiatan_jenis');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kepegawaian_kegiatan_lembaga', function (Blueprint $table) {
            if (Schema::hasColumn('kepegawaian_kegiatan_lembaga', 'jenis_kegiatan')) {
                $table->dropIndex('idx_kegiatan_jenis');
                $table->dropColumn('jenis_kegiatan');
            }
        });
    }
};
