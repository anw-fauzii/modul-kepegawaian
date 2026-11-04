<?php

namespace App\Services\User;

use App\Models\Admin\DataMaster\Karyawan;
use App\Models\Admin\Presensi\Presensi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class UserPresensiService
{
    /**
     * Mengambil dan memproses seluruh data presensi mandiri pegawai untuk tampilan UI.
     *
     * @param User $user
     * @param array $filters
     * @return array
     */
    public function getUserPresensiData(User $user, array $filters = []): array
    {
        // 1. Resolusi identitas Karyawan (NIPY) yang terikat dengan user login
        $karyawan = $user->karyawan ?? Karyawan::where('nipy', $user->no_induk)->first();
        $nipy = $karyawan?->nipy ?? $user->no_induk;

        // 2. Filter Periode (Default: Bulan & Tahun Berjalan)
        $selectedMonth = isset($filters['bulan']) ? (int) $filters['bulan'] : Carbon::now()->month;
        $selectedYear = isset($filters['tahun']) ? (int) $filters['tahun'] : Carbon::now()->year;

        // 3. Rekapitulasi Bulan Ini (Agregasi 1 Query Bersatu)
        $rekap = $this->getRekapitulasiBulanan($nipy, $selectedMonth, $selectedYear);

        // 4. Riwayat Seluruh Presensi Harian Bulan Ini (Tanpa Paginasi - data ~20-22 hari kerja)
        $riwayatPresensi = $this->getRiwayatPresensi($nipy, $filters, $selectedMonth, $selectedYear);

        // 5. Presensi Hari Ini
        $todayPresensi = $this->getTodayPresensi($nipy);

        // 6. Daftar Bulan Indonesia
        $bulanOptions = $this->getBulanOptions();

        // 7. Kalkulasi Metrik Persentase (Kehadiran, Keterlambatan, dan Ketidakhadiran)
        $totalHariKerja = $rekap['total_hari_kerja'];
        $totalTidakHadir = $rekap['total_izin_sakit'] + $rekap['total_alfa'];

        $persentaseHadir = $totalHariKerja > 0 
            ? round(($rekap['total_hadir'] / $totalHariKerja) * 100, 1) 
            : 0;

        $persentaseTerlambat = $totalHariKerja > 0 
            ? round(($rekap['total_terlambat'] / $totalHariKerja) * 100, 1) 
            : 0;

        $persentaseTidakHadir = $totalHariKerja > 0 
            ? round(($totalTidakHadir / $totalHariKerja) * 100, 1) 
            : 0;

        return [
            'user'                 => $user,
            'karyawan'             => $karyawan,
            'userId'               => $user->id,
            'totalHadir'           => $rekap['total_hadir'],
            'totalTerlambat'       => $rekap['total_terlambat'],
            'totalIzinSakit'       => $rekap['total_izin_sakit'],
            'totalAlfa'            => $rekap['total_alfa'],
            'totalTidakHadir'      => $totalTidakHadir,
            'totalHariKerja'       => $totalHariKerja,
            'persentaseHadir'      => $persentaseHadir,
            'persentaseTerlambat'  => $persentaseTerlambat,
            'persentaseTidakHadir' => $persentaseTidakHadir,
            'riwayatPresensi'      => $riwayatPresensi,
            'todayPresensi'        => $todayPresensi,
            'selectedMonth'        => $selectedMonth,
            'selectedYear'         => $selectedYear,
            'bulanOptions'         => $bulanOptions,
        ];
    }

    /**
     * Menghitung total rekapitulasi kehadiran bulanan (Hadir, Terlambat, Izin/Sakit, Alfa).
     * Menggunakan single-query conditional aggregation untuk performa optimal.
     *
     * @param string|null $nipy
     * @param int $bulan
     * @param int $tahun
     * @return array
     */
    public function getRekapitulasiBulanan(?string $nipy, int $bulan, int $tahun): array
    {
        if (!$nipy) {
            return [
                'total_hadir'      => 0,
                'total_terlambat'  => 0,
                'total_izin_sakit' => 0,
                'total_alfa'       => 0,
                'total_hari_kerja' => 0,
            ];
        }

        $row = Presensi::where('karyawan_nipy', $nipy)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->selectRaw("
                SUM(CASE WHEN status IN ('hadir', 'tugas') THEN 1 ELSE 0 END) as total_hadir,
                SUM(CASE WHEN status = 'terlambat' THEN 1 ELSE 0 END) as total_terlambat,
                SUM(CASE WHEN status IN ('izin', 'sakit', 'cuti') THEN 1 ELSE 0 END) as total_izin_sakit,
                SUM(CASE WHEN status = 'alpa' THEN 1 ELSE 0 END) as total_alfa,
                COUNT(*) as total_hari_kerja
            ")->first();

        return [
            'total_hadir'      => (int) ($row->total_hadir ?? 0),
            'total_terlambat'  => (int) ($row->total_terlambat ?? 0),
            'total_izin_sakit' => (int) ($row->total_izin_sakit ?? 0),
            'total_alfa'       => (int) ($row->total_alfa ?? 0),
            'total_hari_kerja' => (int) ($row->total_hari_kerja ?? 0),
        ];
    }

    /**
     * Mengambil seluruh rekaman presensi pada periode terpilih (tanpa paginasi).
     *
     * @param string|null $nipy
     * @param array $filters
     * @param int $selectedMonth
     * @param int $selectedYear
     * @return Collection
     */
    public function getRiwayatPresensi(?string $nipy, array $filters = [], int $selectedMonth = 0, int $selectedYear = 0): Collection
    {
        $query = Presensi::with(['jamKerja', 'bulan'])
            ->where('karyawan_nipy', $nipy);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if ($selectedMonth > 0 && $selectedYear > 0) {
            $query->whereMonth('tanggal', $selectedMonth)
                  ->whereYear('tanggal', $selectedYear);
        }

        return $query->orderByDesc('tanggal')
            ->orderByDesc('jam_masuk')
            ->get();
    }

    /**
     * Mengambil rekaman presensi user pada hari ini jika ada.
     *
     * @param string|null $nipy
     * @return Presensi|null
     */
    public function getTodayPresensi(?string $nipy): ?Presensi
    {
        if (!$nipy) {
            return null;
        }

        return Presensi::where('karyawan_nipy', $nipy)
            ->whereDate('tanggal', Carbon::today()->toDateString())
            ->first();
    }

    /**
     * Mengembalikan daftar opsi nama bulan dalam Bahasa Indonesia.
     *
     * @return array<int, string>
     */
    public function getBulanOptions(): array
    {
        return [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
    }
}
