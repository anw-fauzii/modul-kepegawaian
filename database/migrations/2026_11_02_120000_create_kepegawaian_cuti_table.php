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
        Schema::create('kepegawaian_cuti', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('karyawan_nipy', 50)->index()->comment('Foreign key ke kepegawaian_master_karyawan.nipy');
            $table->unsignedBigInteger('kepegawaian_master_jenis_cuti_id')->index()->comment('Foreign key ke kepegawaian_master_jenis_cuti.id');
            $table->date('awal_cuti')->comment('Tanggal mulai/awal cuti');
            $table->date('akhir_cuti')->comment('Tanggal selesai/akhir cuti');
            $table->unsignedInteger('jumlah_hari')->default(1)->comment('Durasi hari cuti kerja');
            $table->text('alamat')->comment('Alamat lengkap selama menjalankan cuti');
            $table->string('telepon', 50)->comment('Nomor telepon / kontak darurat yang dapat dihubungi');
            $table->text('alasan')->nullable()->comment('Alasan atau keperluan pengajuan cuti');
            $table->boolean('acc')->default(false)->comment('Status persetujuan: false = Menunggu ACC, true = Disetujui (ACC)');
            $table->text('catatan_acc')->nullable()->comment('Catatan pimpinan atau catatan persetujuan/penolakan');
            $table->timestamp('tgl_acc')->nullable()->comment('Waktu persetujuan cuti');
            $table->unsignedBigInteger('acc_by')->nullable()->comment('ID user yang melakukan ACC');
            $table->timestamps();

            // Foreign keys
            $table->foreign('karyawan_nipy')
                ->references('nipy')
                ->on('kepegawaian_master_karyawan')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('kepegawaian_master_jenis_cuti_id')
                ->references('id')
                ->on('kepegawaian_master_jenis_cuti')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kepegawaian_cuti');
    }
};
