<?php

namespace App\Services\Admin\DataMaster;

use App\Models\Admin\DataMaster\Karyawan;
use App\Models\Admin\DataMaster\RiwayatGolongan;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class GolonganService
{
    /**
     * Hitung golongan seorang karyawan pada tanggal acuan.
     *
     * @return array{golongan:string, masa_kerja:int, tanggal_efektif:Carbon, tanggal_naik_berikutnya:?Carbon}|null
     */
    public function hitung(Karyawan $karyawan, ?Carbon $acuan = null): ?array
    {
        $acuan = ($acuan ?? now())->copy()->startOfDay();
        $urutan = config('golongan.urutan');
        $step = (int) config('golongan.tahun_per_tingkat', 4);

        $namaJenjang = $karyawan->jenjangPendidikan?->nama_jenjang_pendidikan;
        if (! $karyawan->mulai_tugas || ! $namaJenjang) {
            return null;
        }

        $awal = collect(config('golongan.awal'))
            ->mapWithKeys(fn ($v, $k) => [mb_strtolower(trim($k)) => $v])
            ->get(mb_strtolower(trim($namaJenjang)));
        $indexAwal = $awal ? array_search($awal, $urutan, true) : false;
        if ($indexAwal === false) {
            return null;
        }

        $mulai = $karyawan->mulai_tugas->copy()->startOfDay();
        $masaKerja = $mulai->greaterThan($acuan) ? 0 : (int) $mulai->diffInYears($acuan);
        // Angka di tabel = tahun masa kerja yang sudah genap: tingkat naik saat genap 5, 9, 13, ... tahun.
        $naik = max(0, intdiv($masaKerja - 1, $step));

        $indexMax = count($urutan) - 1;
        $index = min($indexAwal + $naik, $indexMax);
        $naikEfektif = $index - $indexAwal; // jumlah kenaikan yang benar-benar terjadi

        return [
            'golongan'                => $urutan[$index],
            'masa_kerja'              => $masaKerja,
            'tanggal_efektif'         => $naikEfektif > 0
                ? $mulai->copy()->addYears($naikEfektif * $step + 1)
                : $mulai,
            'tanggal_naik_berikutnya' => $index < $indexMax
                ? $mulai->copy()->addYears(($naikEfektif + 1) * $step + 1)
                : null,
            'golongan_berikutnya'     => $index < $indexMax ? $urutan[$index + 1] : null,
        ];
    }

    /**
     * Sinkronkan kolom golongan seluruh karyawan & catat riwayat kenaikan.
     *
     * @return array{diperiksa:int, berubah:int, dilewati:int}
     */
    public function sinkronkan(bool $dryRun = false): array
    {
        $hasil = ['diperiksa' => 0, 'berubah' => 0, 'dilewati' => 0];

        Karyawan::with('jenjangPendidikan')->orderBy('nipy')->chunk(200, function ($chunk) use (&$hasil, $dryRun) {
            foreach ($chunk as $karyawan) {
                $hasil['diperiksa']++;
                $hitung = $this->hitung($karyawan);

                if (! $hitung) {
                    $hasil['dilewati']++;
                    continue;
                }

                if ($this->normalisasi($karyawan->golongan) === $this->normalisasi($hitung['golongan'])) {
                    continue;
                }

                $hasil['berubah']++;
                if ($dryRun) {
                    continue;
                }

                RiwayatGolongan::updateOrCreate(
                    ['karyawan_nipy' => $karyawan->nipy, 'golongan_baru' => $hitung['golongan']],
                    [
                        'golongan_lama'    => $karyawan->golongan,
                        'masa_kerja_tahun' => $hitung['masa_kerja'],
                        'tanggal_efektif'  => $hitung['tanggal_efektif'],
                        'keterangan'       => 'Kenaikan golongan otomatis (masa kerja & jenjang pendidikan)',
                    ]
                );

                $karyawan->forceFill(['golongan' => $hitung['golongan']])->save();
            }
        });

        return $hasil;
    }

    /**
     * Karyawan yang baru naik golongan dalam N hari terakhir (untuk dashboard admin).
     */
    public function baruNaik(?int $hari = null): Collection
    {
        $hari ??= (int) config('golongan.notifikasi_hari_lalu', 30);

        return RiwayatGolongan::with('karyawan:nipy,nama_lengkap,gelar,unit,jabatan')
            ->whereDate('tanggal_efektif', '>=', now()->subDays($hari)->toDateString())
            ->whereDate('tanggal_efektif', '<=', now()->toDateString())
            ->orderByDesc('tanggal_efektif')
            ->get();
    }

    /**
     * Karyawan yang akan naik golongan dalam N hari ke depan.
     */
    public function akanNaik(?int $hari = null): Collection
    {
        $hari ??= (int) config('golongan.notifikasi_hari_depan', 60);
        $batas = now()->startOfDay()->addDays($hari);

        return Karyawan::with('jenjangPendidikan')
            ->select('nipy', 'nama_lengkap', 'gelar', 'unit', 'jabatan', 'golongan', 'mulai_tugas', 'akademik_pelengkap_jenjang_pendidikan_id')
            ->whereNotNull('mulai_tugas')
            ->get()
            ->map(function (Karyawan $k) use ($batas) {
                $h = $this->hitung($k);
                if (! $h || ! $h['tanggal_naik_berikutnya'] || $h['tanggal_naik_berikutnya']->greaterThan($batas)) {
                    return null;
                }

                return (object) [
                    'karyawan'         => $k,
                    'golongan_saat_ini' => $h['golongan'],
                    'golongan_baru'    => $h['golongan_berikutnya'],
                    'tanggal'          => $h['tanggal_naik_berikutnya'],
                ];
            })
            ->filter()
            ->sortBy('tanggal')
            ->values();
    }

    private function normalisasi(?string $golongan): string
    {
        return strtoupper(preg_replace('/[^A-Za-z]/', '', (string) $golongan));
    }
}
