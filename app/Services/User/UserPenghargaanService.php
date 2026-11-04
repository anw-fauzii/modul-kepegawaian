<?php

namespace App\Services\User;

use App\Models\Admin\DataMaster\Karyawan;
use App\Models\Admin\SertifikatPenghargaan\SertifikatPenghargaan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class UserPenghargaanService
{
    /**
     * Mengambil riwayat sertifikat dan penghargaan milik pegawai login.
     *
     * @param User $user
     * @param array $filters
     * @return array
     */
    public function getUserPenghargaanData(User $user, array $filters = []): array
    {
        $karyawan = $user->karyawan ?? Karyawan::where('nipy', $user->no_induk)->first();
        $nipy = $karyawan?->nipy ?? $user->no_induk;

        $selectedYear = null;
        if (isset($filters['tahun'])) {
            if ($filters['tahun'] !== 'semua' && $filters['tahun'] !== '') {
                $selectedYear = (int) $filters['tahun'];
            }
        } else {
            $selectedYear = null; // Default menampilkan seluruh prestasi pegawai
        }

        $search = !empty($filters['search']) ? trim((string) $filters['search']) : null;

        $statistik = $this->getStatistikPenghargaan($nipy);
        $daftarPenghargaan = $this->getDaftarPenghargaan($nipy, $selectedYear, $search);
        $tahunOptions = $this->getTahunOptions($nipy);

        return [
            'user'              => $user,
            'karyawan'          => $karyawan,
            'kpi'               => [
                'total_penghargaan' => $statistik['total_penghargaan'],
                'tahun_ini'         => $statistik['total_tahun_ini'],
                'tahun_terbaru'     => $statistik['tahun_terbaru'],
                'berkas_tersedia'   => $statistik['total_berkas'],
            ],
            'totalPenghargaan'  => $statistik['total_penghargaan'],
            'totalTahunIni'     => $statistik['total_tahun_ini'],
            'tahunTerbaru'      => $statistik['tahun_terbaru'],
            'totalBerkas'       => $statistik['total_berkas'],
            'daftarPenghargaan' => $daftarPenghargaan,
            'penghargaanList'   => $daftarPenghargaan,
            'selectedYear'      => $selectedYear,
            'tahunOptions'      => $tahunOptions,
            'daftarTahun'       => $tahunOptions,
            'filters'           => $filters,
        ];
    }

    /**
     * Hitung statistik sertifikat penghargaan pegawai.
     *
     * @param string|null $nipy
     * @return array
     */
    public function getStatistikPenghargaan(?string $nipy): array
    {
        if (!$nipy) {
            return [
                'total_penghargaan' => 0,
                'total_tahun_ini'   => 0,
                'tahun_terbaru'     => '-',
                'total_berkas'      => 0,
            ];
        }

        $currentYear = Carbon::now()->year;

        $row = SertifikatPenghargaan::where('karyawan_nipy', $nipy)->selectRaw("
            COUNT(*) as total_penghargaan,
            COALESCE(SUM(CASE WHEN tahun = ? THEN 1 ELSE 0 END), 0) as total_tahun_ini,
            MAX(tahun) as tahun_terbaru,
            COALESCE(SUM(CASE WHEN berkas IS NOT NULL AND berkas <> '' THEN 1 ELSE 0 END), 0) as total_berkas
        ", [$currentYear])->first();

        return [
            'total_penghargaan' => (int) ($row->total_penghargaan ?? 0),
            'total_tahun_ini'   => (int) ($row->total_tahun_ini ?? 0),
            'tahun_terbaru'     => $row->tahun_terbaru ? (string) $row->tahun_terbaru : '-',
            'total_berkas'      => (int) ($row->total_berkas ?? 0),
        ];
    }

    /**
     * Mengambil daftar sertifikat penghargaan pegawai login.
     *
     * @param string|null $nipy
     * @param int|null $tahun
     * @return Collection
     */
    public function getDaftarPenghargaan(?string $nipy, ?int $tahun = null, ?string $search = null): Collection
    {
        if (!$nipy) {
            return new Collection();
        }

        $query = SertifikatPenghargaan::where('karyawan_nipy', $nipy);

        if ($tahun) {
            $query->where('tahun', $tahun);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul_penghargaan', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        return $query->orderByDesc('tahun')->orderByDesc('id')->get();
    }

    /**
     * Opsi tahun sertifikat penghargaan pegawai.
     *
     * @param string|null $nipy
     * @return array<int>
     */
    public function getTahunOptions(?string $nipy): array
    {
        if (!$nipy) {
            return [];
        }

        return SertifikatPenghargaan::where('karyawan_nipy', $nipy)
            ->whereNotNull('tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun')
            ->toArray();
    }
}

