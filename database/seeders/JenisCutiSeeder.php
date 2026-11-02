<?php

namespace Database\Seeders;

use App\Models\Admin\DataMaster\JenisCuti;
use Illuminate\Database\Seeder;

class JenisCutiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'nama'       => 'Cuti Tahunan',
                'lama_hari'  => 12,
                'keterangan' => 'Hak cuti tahunan reguler bagi karyawan tetap yang telah mengabdi minimal 1 tahun.',
                'is_active'  => true,
            ],
            [
                'nama'       => 'Cuti Melahirkan',
                'lama_hari'  => 90,
                'keterangan' => 'Diberikan kepada karyawati yang melahirkan (1,5 bulan sebelum dan 1,5 bulan sesudah melahirkan).',
                'is_active'  => true,
            ],
            [
                'nama'       => 'Cuti Menikah',
                'lama_hari'  => 3,
                'keterangan' => 'Diberikan untuk karyawan yang melangsungkan akad pernikahan.',
                'is_active'  => true,
            ],
            [
                'nama'       => 'Cuti Menikahkan Anak',
                'lama_hari'  => 2,
                'keterangan' => 'Diberikan kepada karyawan saat melangsungkan pernikahan putra/putri kandung.',
                'is_active'  => true,
            ],
            [
                'nama'       => 'Cuti Khitanan / Baptis Anak',
                'lama_hari'  => 2,
                'keterangan' => 'Diberikan untuk acara khitanan atau pembaptisan anak karyawan.',
                'is_active'  => true,
            ],
            [
                'nama'       => 'Cuti Kematian Keluarga Inti',
                'lama_hari'  => 3,
                'keterangan' => 'Diberikan jika suami/istri, orang tua/mertua, atau anak kandung meninggal dunia.',
                'is_active'  => true,
            ],
            [
                'nama'       => 'Cuti Ibadah Haji',
                'lama_hari'  => 40,
                'keterangan' => 'Diberikan untuk karyawan yang menunaikan ibadah haji ke Tanah Suci.',
                'is_active'  => true,
            ],
            [
                'nama'       => 'Cuti Ibadah Umroh',
                'lama_hari'  => 14,
                'keterangan' => 'Diberikan untuk karyawan yang menunaikan ibadah umroh.',
                'is_active'  => true,
            ],
            [
                'nama'       => 'Cuti Sakit Berkelanjutan',
                'lama_hari'  => 14,
                'keterangan' => 'Diberikan untuk sakit yang memerlukan rawat inap atau istirahat medis dibuktikan surat dokter.',
                'is_active'  => true,
            ],
            [
                'nama'       => 'Cuti Alasan Penting',
                'lama_hari'  => 3,
                'keterangan' => 'Diberikan untuk keperluan darurat keluarga atau musibah tak terduga.',
                'is_active'  => true,
            ],
        ];

        foreach ($data as $item) {
            JenisCuti::firstOrCreate(
                ['nama' => $item['nama']],
                $item
            );
        }
    }
}
