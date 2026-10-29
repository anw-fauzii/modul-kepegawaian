<?php

namespace App\Exports;

use App\Models\Admin\DataMaster\Karyawan;
use App\Models\Admin\Penggajian\Penggajian;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PenggajianTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithTitle
{
    private function fields(): array
    {
        return array_merge(array_keys(Penggajian::PENERIMAAN), array_keys(Penggajian::POTONGAN));
    }

    public function title(): string
    {
        return 'Format Penggajian';
    }

    public function headings(): array
    {
        return array_merge(['nipy', 'nama'], $this->fields());
    }

    public function collection(): Collection
    {
        $zeros = array_fill(0, count($this->fields()), 0);

        return Karyawan::query()
            ->orderBy('nama_lengkap')
            ->get(['nipy', 'nama_lengkap'])
            ->map(function ($k) use ($zeros) {
                return array_merge([(string) $k->nipy, (string) $k->nama_lengkap], $zeros);
            });
    }

    public function styles(Worksheet $sheet): array
    {
        $lastCol = $sheet->getHighestColumn();
        $highestRow = $sheet->getHighestRow();

        // Style header baris 1 (Default: Dark Slate)
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E293B'],
            ],
        ]);

        // Warna Hijau untuk Kolom Penerimaan (C s/d Q)
        $penerimaanEndIndex = 2 + count(Penggajian::PENERIMAAN);
        $penerimaanEndCol = Coordinate::stringFromColumnIndex($penerimaanEndIndex);
        $sheet->getStyle("C1:{$penerimaanEndCol}1")->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '15803D'], // Emerald / Green
            ],
        ]);

        // Warna Merah untuk Kolom Potongan (setelah penerimaan s/d lastCol)
        $potonganStartIndex = $penerimaanEndIndex + 1;
        $potonganStartCol = Coordinate::stringFromColumnIndex($potonganStartIndex);
        $sheet->getStyle("{$potonganStartCol}1:{$lastCol}1")->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'B91C1C'], // Red
            ],
        ]);

        // Format angka (number format #,##0) untuk seluruh baris data nominal
        if ($highestRow > 1) {
            $sheet->getStyle("C2:{$lastCol}{$highestRow}")
                ->getNumberFormat()
                ->setFormatCode('#,##0');
        }

        // Set tinggi baris header
        $sheet->getRowDimension(1)->setRowHeight(28);

        return [];
    }
}
