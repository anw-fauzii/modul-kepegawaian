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
        Schema::create('kepegawaian_master_bulan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nama_bulan');
            
            $table->foreignId('akademik_master_tahun_ajaran_id')
                ->constrained(
                    table: 'akademik_master_tahun_ajaran',
                    indexName: 'kmb_tahun_ajaran_fk'
                );

            $table->boolean('status')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kepegawaian_master_bulan');
    }
};
