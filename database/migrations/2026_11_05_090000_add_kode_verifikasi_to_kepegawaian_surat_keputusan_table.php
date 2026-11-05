<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kepegawaian_surat_keputusan', function (Blueprint $table) {
            $table->string('kode_verifikasi', 20)->nullable()->unique()->after('nomor_sk');
        });

        // Backfill kode verifikasi untuk SK yang sudah ada
        DB::table('kepegawaian_surat_keputusan')->whereNull('kode_verifikasi')->orderBy('id')->each(function ($row) {
            DB::table('kepegawaian_surat_keputusan')
                ->where('id', $row->id)
                ->update(['kode_verifikasi' => strtoupper(Str::random(12))]);
        });
    }

    public function down(): void
    {
        Schema::table('kepegawaian_surat_keputusan', function (Blueprint $table) {
            $table->dropUnique(['kode_verifikasi']);
            $table->dropColumn('kode_verifikasi');
        });
    }
};
