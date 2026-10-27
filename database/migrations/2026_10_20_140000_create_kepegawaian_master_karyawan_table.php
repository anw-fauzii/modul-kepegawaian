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
        Schema::create('kepegawaian_master_karyawan', function (Blueprint $table) { 
            $table->string('nipy')->primary();
            $table->string('nik')->nullable();
            $table->foreign('nipy')->references('email')->on('users')->cascadeOnDelete();

            $table->string('nama_lengkap');
            $table->string('gelar')->nullable();
            $table->string('golongan')->nullable();

            $table->foreignId('kepegawaian_master_kategori_id')
                ->constrained(
                    table: 'kepegawaian_master_kategori',
                    indexName: 'kmk_kategori_fk'
                );
            $table->foreignId('kepegawaian_master_jam_kerja_id')
                ->constrained(
                    table: 'kepegawaian_master_jam_kerja',
                    indexName: 'kmk_jam_kerja_fk'
                );
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->text('alamat');
            $table->string('rt', 5);
            $table->string('rw', 5);
            $table->string('kelurahan');
            $table->string('kecamatan');
            $table->string('kabupaten');
            $table->enum('jk', ['L', 'P']);
            $table->string('jabatan');
            $table->string('unit');
            $table->date('mulai_tugas');

            $table->foreignId('akademik_pelengkap_jenjang_pendidikan_id')
                ->constrained(
                    table: 'akademik_pelengkap_jenjang_pendidikan',
                    indexName: 'kmk_pendidikan_fk'
                );

            $table->string('prodi')->nullable();
            $table->string('tahun_lulus', 4);
            $table->string('whatsapp', 20)->nullable();
            $table->string('nuptk')->nullable();
            $table->boolean('sertifikasi')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kepegawaian_master_karyawan');
    }
};
