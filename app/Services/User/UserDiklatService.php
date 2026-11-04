<?php

namespace App\Services\User;

use App\Models\Admin\DataMaster\Karyawan;
use App\Models\Admin\Diklat\Diklat;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class UserDiklatService
{
    /**
     * Mengambil riwayat keikutsertaan Diklat & Pelatihan milik pegawai login.
     *
     * @param User $user
     * @param array $filters
     * @return array
     */
    public function getUserDiklatData(User $user, array $filters = []): array
    {
        $karyawan = $user->karyawan ?? Karyawan::where('nipy', $user->no_induk)->first();
        $nipy = $karyawan?->nipy ?? $user->no_induk;

        $selectedYear = null;
        if (isset($filters['tahun'])) {
            if ($filters['tahun'] !== 'semua' && $filters['tahun'] !== '') {
                $selectedYear = (int) $filters['tahun'];
            }
        } else {
            $selectedYear = Carbon::now()->year;
        }

        $selectedKategori = in_array($filters['kategori'] ?? null, ['mandiri', 'lembaga'], true)
            ? $filters['kategori']
            : null;

        $search = !empty($filters['search']) ? trim((string) $filters['search']) : null;

        $statistik = $this->getStatistikDiklat($nipy, $selectedYear);
        $daftarDiklat = $this->getDaftarDiklat($nipy, $selectedYear, $selectedKategori, $search);
        $tahunOptions = $this->getTahunOptions($nipy);

        return [
            'user'             => $user,
            'karyawan'         => $karyawan,
            'kpi'              => [
                'total_diklat'   => $statistik['total_diklat'],
                'total_jam'      => $statistik['total_jam'],
                'diklat_lembaga' => $statistik['total_lembaga'],
                'diklat_mandiri' => $statistik['total_mandiri'],
            ],
            'totalDiklat'      => $statistik['total_diklat'],
            'totalJam'         => $statistik['total_jam'],
            'totalLembaga'     => $statistik['total_lembaga'],
            'totalMandiri'     => $statistik['total_mandiri'],
            'daftarDiklat'     => $daftarDiklat,
            'diklatList'       => $daftarDiklat,
            'selectedYear'     => $selectedYear,
            'selectedKategori' => $selectedKategori,
            'tahunOptions'     => $tahunOptions,
            'daftarTahun'      => $tahunOptions,
            'filters'          => $filters,
        ];
    }

    /**
     * Hitung statistik diklat pegawai.
     *
     * @param string|null $nipy
     * @param int|null $tahun
     * @return array
     */
    public function getStatistikDiklat(?string $nipy, ?int $tahun = null): array
    {
        if (!$nipy) {
            return [
                'total_diklat'  => 0,
                'total_jam'     => 0,
                'total_lembaga' => 0,
                'total_mandiri' => 0,
            ];
        }

        $query = Diklat::where('karyawan_nipy', $nipy);

        if ($tahun) {
            $query->whereYear('tanggal', $tahun);
        }

        $row = $query->selectRaw("
            COUNT(*) as total_diklat,
            COALESCE(SUM(jumlah_jam), 0) as total_jam,
            COALESCE(SUM(CASE WHEN LOWER(kategori) = 'lembaga' THEN 1 ELSE 0 END), 0) as total_lembaga,
            COALESCE(SUM(CASE WHEN LOWER(kategori) = 'mandiri' THEN 1 ELSE 0 END), 0) as total_mandiri
        ")->first();

        return [
            'total_diklat'  => (int) ($row->total_diklat ?? 0),
            'total_jam'     => (int) ($row->total_jam ?? 0),
            'total_lembaga' => (int) ($row->total_lembaga ?? 0),
            'total_mandiri' => (int) ($row->total_mandiri ?? 0),
        ];
    }

    /**
     * Mengambil daftar diklat pegawai login.
     *
     * @param string|null $nipy
     * @param int|null $tahun
     * @param string|null $kategori
     * @return Collection
     */
    public function getDaftarDiklat(?string $nipy, ?int $tahun = null, ?string $kategori = null, ?string $search = null): Collection
    {
        if (!$nipy) {
            return new Collection();
        }

        $query = Diklat::with('kegiatanLembaga:id,nama_kegiatan,penyelenggara')
            ->where('karyawan_nipy', $nipy);

        if ($tahun) {
            $query->whereYear('tanggal', $tahun);
        }

        if ($kategori) {
            $query->where('kategori', $kategori);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_kegiatan', 'like', "%{$search}%")
                  ->orWhere('materi', 'like', "%{$search}%")
                  ->orWhere('pelaksana', 'like', "%{$search}%");
            });
        }

        return $query->orderByDesc('tanggal')->orderByDesc('id')->get();
    }

    /**
     * Opsi tahun keikutsertaan diklat pegawai.
     *
     * @param string|null $nipy
     * @return array<int>
     */
    public function getTahunOptions(?string $nipy): array
    {
        $currentYear = Carbon::now()->year;
        $dbYears = [];

        if ($nipy) {
            $dbYears = Diklat::where('karyawan_nipy', $nipy)
                ->selectRaw('DISTINCT YEAR(tanggal) as tahun')
                ->orderByDesc('tahun')
                ->pluck('tahun')
                ->toArray();
        }

        $years = array_unique(array_merge([$currentYear, $currentYear - 1], $dbYears));
        rsort($years);

        return $years;
    }
}

