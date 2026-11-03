<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kepegawaian_riwayat_jabatan', function (Blueprint $table) {
            $table->id();
            $table->string('karyawan_nipy');
            $table->unsignedBigInteger('kepegawaian_master_kategori_id')->nullable();
            $table->string('jabatan');
            $table->string('unit')->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('karyawan_nipy')
                ->references('nipy')
                ->on('kepegawaian_master_karyawan')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreign('kepegawaian_master_kategori_id', 'fk_riwayat_jabatan_kategori')
                ->references('id')
                ->on('kepegawaian_master_kategori')
                ->nullOnDelete();

            $table->index(['karyawan_nipy', 'is_aktif']);
            $table->index(['karyawan_nipy', 'tanggal_mulai']);
        });

        // Backfill histori jabatan awal dari data master karyawan yang sudah memiliki jabatan
        if (Schema::hasTable('kepegawaian_master_karyawan')) {
            $now = now();
            $existingKaryawan = DB::table('kepegawaian_master_karyawan')
                ->whereNotNull('jabatan')
                ->where('jabatan', '!=', '')
                ->get([
                    'nipy',
                    'kepegawaian_master_kategori_id',
                    'jabatan',
                    'unit',
                    'mulai_tugas',
                ]);

            $insertRows = [];
            foreach ($existingKaryawan as $row) {
                $insertRows[] = [
                    'karyawan_nipy'                  => $row->nipy,
                    'kepegawaian_master_kategori_id' => $row->kepegawaian_master_kategori_id,
                    'jabatan'                        => $row->jabatan,
                    'unit'                           => $row->unit,
                    'tanggal_mulai'                  => $row->mulai_tugas,
                    'tanggal_selesai'                => null,
                    'is_aktif'                       => true,
                    'keterangan'                     => 'Jabatan awal dari Data Master Karyawan',
                    'created_at'                     => $now,
                    'updated_at'                     => $now,
                ];
            }

            if (! empty($insertRows)) {
                foreach (array_chunk($insertRows, 200) as $chunk) {
                    DB::table('kepegawaian_riwayat_jabatan')->insert($chunk);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kepegawaian_riwayat_jabatan');
    }
};

