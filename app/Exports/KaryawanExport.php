<?php

namespace App\Exports;

use App\Models\Admin\DataMaster\Karyawan;
use App\Models\Admin\DataMaster\Kategori;
use App\View\Composers\SidebarComposer;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KaryawanExport implements Export, FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithTitle
{
    public function __construct(
        protected ?string $unit = null
    ) {}

    public function title(): string
    {
        return $this->unit ? 'Karyawan Unit ' . $this->unit : 'Data Karyawan';
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
        $query = Karyawan::with(['kategori', 'jenjangPendidikan', 'user', 'jamKerja']);

        if ($this->unit) {
            $categories = Kategori::all()->filter(function ($k) {
                return strcasecmp(SidebarComposer::extractUnitName($k->nama_kategori), $this->unit) === 0;
            });
            $kategoriIds = $categories->pluck('id')->all();

            $query->where(function ($q) use ($kategoriIds) {
                if (! empty($kategoriIds)) {
                    $q->whereIn('kepegawaian_master_kategori_id', $kategoriIds);
                }
                $q->orWhere('unit', 'LIKE', '%' . $this->unit . '%');
            });
        }

        return $query->orderBy('nama_lengkap')->get()->map(function (Karyawan $k) {
            return [
                (string) $k->nipy,
                (string) ($k->nik ?? ''),
                (string) $k->nama_lengkap,
                (string) ($k->gelar ?? ''),
                (string) $k->jk,
                (string) $k->tempat_lahir,
                $k->tanggal_lahir ? $k->tanggal_lahir->format('Y-m-d') : '',
                (string) ($k->user?->email ?? ''),
                (string) ($k->whatsapp ?? ''),
                (string) $k->alamat,
                (string) $k->rt,
                (string) $k->rw,
                (string) $k->kelurahan,
                (string) $k->kecamatan,
                (string) $k->kabupaten,
                (string) ($k->kategori?->nama_kategori ?? ''),
                (string) $k->unit,
                (string) $k->jabatan,
                (string) ($k->golongan ?? ''),
                (string) ($k->jamKerja?->nama ?? ''),
                $k->mulai_tugas ? $k->mulai_tugas->format('Y-m-d') : '',
                (string) ($k->jenjangPendidikan?->nama_jenjang_pendidikan ?? ''),
                (string) ($k->prodi ?? ''),
                (string) $k->tahun_lulus,
                (string) ($k->nuptk ?? ''),
                $k->sertifikasi ? 'Ya' : 'Tidak',
                ($k->user?->is_active ?? true) ? 'Aktif' : 'Nonaktif',
            ];
        });
    }

    public function styles(Worksheet $sheet): array
    {
        $lastCol = $sheet->getHighestColumn();
        $highestRow = $sheet->getHighestRow();

        // Header Styling (Dark Slate & Elegant Orange Accents)
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

        // Format Text untuk kolom yang rawan format angka (NIPY, NIK, WhatsApp, RT, RW, NUPTK)
        if ($highestRow > 1) {
            $textCols = ['A', 'B', 'I', 'K', 'L', 'Y'];
            foreach ($textCols as $col) {
                $sheet->getStyle("{$col}2:{$col}{$highestRow}")
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_TEXT);
            }

            // Zebra striping lembut & borders
            $sheet->getStyle("A1:{$lastCol}{$highestRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => 'CBD5E1'],
                    ],
                ],
            ]);

            $sheet->getStyle("A2:{$lastCol}{$highestRow}")->applyFromArray([
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);
        }

        return [];
    }
}
