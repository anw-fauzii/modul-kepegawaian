<?php

namespace App\Services\Admin\SuratKeputusan;

use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Facades\Log;
use setasign\Fpdi\Fpdi;
use Throwable;

/**
 * Menempelkan QR verifikasi di bagian bawah halaman TERAKHIR berkas PDF SK.
 */
class SuratKeputusanQrStamper
{
    /**
     * @param  string  $pdfPath  Path absolut berkas PDF (akan ditimpa dengan versi ber-QR)
     */
    public function stamp(string $pdfPath, string $kode, string $verifyUrl): bool
    {
        try {
            try {
                $this->render($pdfPath, $pdfPath, $kode, $verifyUrl);
            } catch (Throwable $e) {
                // PDF 1.5+ (xref terkompresi) tidak bisa dibaca FPDI gratis -> normalisasi via Ghostscript lalu coba lagi
                $normalized = $this->normalizeWithGhostscript($pdfPath);
                if (! $normalized) {
                    throw $e;
                }
                try {
                    $this->render($normalized, $pdfPath, $kode, $verifyUrl);
                } finally {
                    @unlink($normalized);
                }
            }

            return true;
        } catch (Throwable $e) {
            Log::warning("Gagal menempelkan QR verifikasi pada {$pdfPath}: {$e->getMessage()}");

            return false;
        }
    }

    protected function render(string $source, string $target, string $kode, string $verifyUrl): void
    {
        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($source);

        $qrFile = $this->makeQrPng($verifyUrl);

        try {
            for ($i = 1; $i <= $pageCount; $i++) {
                $tpl = $pdf->importPage($i);
                $size = $pdf->getTemplateSize($tpl);
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($tpl);

                if ($i === $pageCount) {
                    $this->drawQrBlock($pdf, $size, $qrFile, $kode);
                }
            }

            $tmp = $target . '.stamping';
            $pdf->Output('F', $tmp);
            rename($tmp, $target);
        } finally {
            @unlink($qrFile);
        }
    }

    protected function drawQrBlock(Fpdi $pdf, array $size, string $qrFile, string $kode): void
    {
        $qr = 20;           // Ukuran QR 20x20 mm
        $x = 15;            // Margin kiri 15 mm
        $bottomMargin = 25; // Dinaikkan menjadi 25 mm dari tepi bawah
        $y = $size['height'] - $bottomMargin - $qr;

        // Cetak gambar QR tanpa kotak pembungkus
        $pdf->Image($qrFile, $x, $y, $qr, $qr, 'PNG');

        // Cetak kode verifikasi tepat di bawah QR
        $pdf->SetTextColor(71, 85, 105);
        $pdf->SetFont('Helvetica', 'B', 7);
        $pdf->SetXY($x - 1, $y + $qr + 1.2);
        $pdf->Cell($qr + 2, 3, $kode, 0, 0, 'C');
    }

    protected function makeQrPng(string $url): string
    {
        $options = new QROptions([
            'outputInterface' => QRGdImagePNG::class,
            'outputBase64'    => false,
            'scale'           => 8,
            'quietzoneSize'   => 1,
        ]);

        $file = tempnam(sys_get_temp_dir(), 'skqr_') . '.png';
        file_put_contents($file, (new QRCode($options))->render($url));

        return $file;
    }

    protected function normalizeWithGhostscript(string $pdfPath): ?string
    {
        $out = tempnam(sys_get_temp_dir(), 'skgs_') . '.pdf';
        $cmd = sprintf(
            'gs -q -dNOPAUSE -dBATCH -dSAFER -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -sOutputFile=%s %s 2>&1',
            escapeshellarg($out),
            escapeshellarg($pdfPath)
        );
        exec($cmd, $output, $code);

        if ($code !== 0 || ! is_file($out) || filesize($out) === 0) {
            @unlink($out);

            return null;
        }

        return $out;
    }
}
