<?php

namespace App\Exports;

use App\Models\Admin\DataMaster\JamKerja;
use App\Models\Admin\DataMaster\JenjangPendidikan;
use App\Models\Admin\DataMaster\Kategori;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KaryawanTemplateExport implements Export, WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new KaryawanTemplateDataSheet(),
            new KaryawanTemplateReferenceSheet(),
        ];
    }
}

class KaryawanTemplateDataSheet implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithTitle
{
    public function title(): string
    {
        return 'Format Import Karyawan';
    }

    public function headings(): array
    {
        return [
            'nipy',
            'nik',
            'nama_lengkap',
            'gelar',
            'jk',
            'tempat_lahir',
            'tanggal_lahir',
            'email',
            'whatsapp',
            'alamat',
            'rt',
            'rw',
            'kelurahan',
            'kecamatan',
            'kabupaten',
            'kategori',
            'unit',
            'jabatan',
            'golongan',
            'jam_kerja',
            'mulai_tugas',
            'jenjang_pendidikan',
            'prodi',
            'tahun_lulus',
            'nuptk',
            'sertifikasi',
            'status_akun',
        ];
    }

    public function collection(): Collection
    {
        // 1 baris contoh pengisian data yang valid
        return collect([
            [
                '21990999-2',
                '3205011234560001',
                'Ahmad Fauzi (Contoh)',
                'S.Pd.',
                'L',
                'Garut',
                '1995-05-12',
                'ahmad.fauzi@primainsani.sch.id',
                '081234567890',
                'Jl. Pahlawan No. 10',
                '01',
                '02',
                'Sukagalih',
                'Tarogong Kidul',
                'Garut',
                'SD',
                'SD GIS Prima Insani',
                'Guru Kelas',
                'II/A',
                'SD Normal',
                '2021-07-01',
                'S1',
                'Pendidikan Guru SD',
                '2019',
                '',
                'Ya',
                'Aktif',
            ],
        ]);
    }

    public function styles(Worksheet $sheet): array
    {
        $lastCol = $sheet->getHighestColumn();

        // Style header baris 1
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 11,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E293B'], // Dark Slate
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // Format kolom teks (A, B, I, K, L, Y)
        $textCols = ['A', 'B', 'I', 'K', 'L', 'Y'];
        foreach ($textCols as $col) {
            $sheet->getStyle("{$col}2:{$col}100")
                ->getNumberFormat()
                ->setFormatCode(NumberFormat::FORMAT_TEXT);
        }

        // Contoh baris font italic & warna lembut
        $sheet->getStyle("A2:{$lastCol}2")->applyFromArray([
            'font' => [
                'italic' => true,
                'color'  => ['rgb' => '475569'],
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F8FAFC'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'CBD5E1'],
                ],
            ],
        ]);

        return [];
    }
}

class KaryawanTemplateReferenceSheet implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithTitle
{
    public function title(): string
    {
        return 'Panduan & Referensi Master';
    }

    public function headings(): array
    {
        return [
            'Tipe Data',
            'Nilai / Opsi Yang Tersedia',
            'Keterangan & Petunjuk',
        ];
    }

    public function collection(): Collection
    {
        $kategoriList = Kategori::pluck('nama_kategori')->implode(', ');
        $jamKerjaList = JamKerja::pluck('nama')->implode(', ');
        $jenjangList  = JenjangPendidikan::pluck('nama_jenjang_pendidikan')->implode(', ');

        return collect([
            [
                'kategori',
                $kategoriList ?: 'SD, Yayasan, Staff Yayasan, PG TK',
                'Bisa diisi Nama Kategori atau ID Kategori. Sistem otomatis mengenali.',
            ],
            [
                'jam_kerja',
                $jamKerjaList ?: 'SD Normal, Yayasan',
                'Bisa diisi Nama Jam Kerja atau ID Jam Kerja.',
            ],
            [
                'jenjang_pendidikan',
                $jenjangList ?: 'S1, SMA, D3',
                'Bisa diisi Nama Jenjang atau ID Jenjang.',
            ],
            [
                'jk (Jenis Kelamin)',
                'L / P',
                'L = Laki-laki, P = Perempuan.',
            ],
            [
                'tanggal_lahir & mulai_tugas',
                'YYYY-MM-DD (Contoh: 1995-05-12)',
                'Gunakan format standar tanggal ISO atau format tanggal Excel.',
            ],
            [
                'sertifikasi',
                'Ya / Tidak',
                'Bisa juga diisi 1 (Ya) atau 0 (Tidak).',
            ],
            [
                'status_akun',
                'Aktif / Nonaktif',
                'Menentukan apakah user dapat login ke sistem. Default: Aktif.',
            ],
            [
                'email',
                'alamat@domain.com',
                'Opsional. Jika dikosongkan, sistem otomatis membuat email {nipy}@primainsani.sch.id.',
            ],
            [
                'tahun_lulus',
                '4 digit (Contoh: 2020)',
                'Tahun kelulusan pendidikan terakhir.',
            ],
        ]);
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1:C1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 11,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0284C7'], // Sky Blue
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle("A1:C{$highestRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'CBD5E1'],
                ],
            ],
        ]);

        return [];
    }
}
