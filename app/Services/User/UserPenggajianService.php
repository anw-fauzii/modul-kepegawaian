<?php

namespace App\Services\User;

use App\Models\Admin\DataMaster\Karyawan;
use App\Models\Admin\Penggajian\Penggajian;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class UserPenggajianService
{
    /**
     * Mengambil dan memproses seluruh data penggajian pribadi pegawai untuk tampilan UI.
     *
     * @param User $user
     * @param array $filters
     * @return array
     */
    public function getUserPenggajianData(User $user, array $filters = []): array
    {
        // 1. Resolusi identitas Karyawan (NIPY) yang terikat dengan user login
        $karyawan = $user->karyawan ?? Karyawan::where('nipy', $user->no_induk)->first();
        $nipy = $karyawan?->nipy ?? $user->no_induk;

        // 2. Filter Periode & Jenis
        $selectedYear = null;
        if (isset($filters['tahun'])) {
            if ($filters['tahun'] !== 'semua' && $filters['tahun'] !== '') {
                $selectedYear = (int) $filters['tahun'];
            }
        } else {
            $selectedYear = Carbon::now()->year;
        }

        $selectedJenis = !empty($filters['jenis']) ? $filters['jenis'] : null;
        $search = !empty($filters['search']) ? trim((string) $filters['search']) : null;

        // 3. Ringkasan Statistik Bulanan/Tahunan (1 Query Agregasi Bersatu)
        $statistik = $this->getStatistikGaji($nipy, $selectedYear, $selectedJenis, $search);

        // 4. Riwayat Slip Gaji Pegawai (Zero N+1 Query Eager Loading)
        $riwayatPenggajian = $this->getRiwayatGaji($nipy, $selectedYear, $selectedJenis, $search);

        // 5. Opsi Filter Tahun dari riwayat yang pernah tercatat
        $tahunOptions = $this->getTahunOptions($nipy);

        // 6. Daftar Kamus Jenis & Komponen Gaji
        $jenisList = Penggajian::JENIS;
        $statusList = Penggajian::STATUS;
        $penerimaanList = Penggajian::PENERIMAAN;
        $potonganList = Penggajian::POTONGAN;

        // Slip gaji terakhir yang dibayarkan / final
        $latestSlip = $riwayatPenggajian->first();

        return [
            'user'               => $user,
            'karyawan'           => $karyawan,
            'userId'             => $user->id,
            'totalGajiBersih'    => $statistik['total_gaji_bersih'],
            'totalPenerimaan'    => $statistik['total_penerimaan'],
            'totalPotongan'      => $statistik['total_potongan'],
            'totalSlip'          => $statistik['total_slip'],
            'riwayatPenggajian'  => $riwayatPenggajian,
            'latestSlip'         => $latestSlip,
            'selectedYear'       => $selectedYear,
            'selectedJenis'      => $selectedJenis,
            'tahunOptions'       => $tahunOptions,
            'jenisList'          => $jenisList,
            'statusList'         => $statusList,
            'penerimaanList'     => $penerimaanList,
            'potonganList'       => $potonganList,
            'filters'            => $filters,
            'search'             => $search,
        ];
    }

    /**
     * Menghitung total ringkasan gaji (Penerimaan, Potongan, Gaji Bersih, Total Slip).
     *
     * @param string|null $nipy
     * @param int|null $tahun
     * @param string|null $jenis
     * @param string|null $search
     * @return array
     */
    public function getStatistikGaji(?string $nipy, ?int $tahun = null, ?string $jenis = null, ?string $search = null): array
    {
        if (!$nipy) {
            return [
                'total_gaji_bersih' => 0.0,
                'total_penerimaan'  => 0.0,
                'total_potongan'    => 0.0,
                'total_slip'        => 0,
            ];
        }

        $query = Penggajian::where('karyawan_nipy', $nipy);

        if ($tahun) {
            $query->whereYear('created_at', $tahun);
        }

        if ($jenis) {
            $query->where('jenis', $jenis);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('bulan', function ($bq) use ($search) {
                    $bq->where('nama_bulan', 'like', "%{$search}%");
                })
                ->orWhere('jenis', 'like', "%{$search}%")
                ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $row = $query->selectRaw("
            COALESCE(SUM(gaji_bersih), 0) as total_gaji_bersih,
            COALESCE(SUM(penerimaan), 0) as total_penerimaan,
            COALESCE(SUM(potongan), 0) as total_potongan,
            COUNT(*) as total_slip
        ")->first();

        return [
            'total_gaji_bersih' => (float) ($row->total_gaji_bersih ?? 0),
            'total_penerimaan'  => (float) ($row->total_penerimaan ?? 0),
            'total_potongan'    => (float) ($row->total_potongan ?? 0),
            'total_slip'        => (int) ($row->total_slip ?? 0),
        ];
    }

    /**
     * Mengambil seluruh rekaman slip gaji user dengan relasi bulan & tahun ajaran.
     *
     * @param string|null $nipy
     * @param int|null $tahun
     * @param string|null $jenis
     * @param string|null $search
     * @return Collection
     */
    public function getRiwayatGaji(?string $nipy, ?int $tahun = null, ?string $jenis = null, ?string $search = null): Collection
    {
        if (!$nipy) {
            return new Collection();
        }

        $query = Penggajian::with([
                'bulan:id,nama_bulan,status,akademik_master_tahun_ajaran_id',
                'bulan.tahunAjaran:id,nama_tahun_ajaran,semester',
            ])
            ->where('karyawan_nipy', $nipy);

        if ($tahun) {
            $query->whereYear('created_at', $tahun);
        }

        if ($jenis) {
            $query->where('jenis', $jenis);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('bulan', function ($bq) use ($search) {
                    $bq->where('nama_bulan', 'like', "%{$search}%");
                })
                ->orWhere('jenis', 'like', "%{$search}%")
                ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        return $query->orderByDesc('id')->get();
    }

    /**
     * Mengambil detail satu slip gaji dengan validasi kepemilikan pribadi.
     *
     * @param User $user
     * @param Penggajian $penggajian
     * @return array
     */
    public function getSlipDetail(User $user, Penggajian $penggajian): array
    {
        $karyawan = $user->karyawan ?? Karyawan::where('nipy', $user->no_induk)->first();
        $nipy = $karyawan?->nipy ?? $user->no_induk;

        // Proteksi Otorisasi: Hanya izinkan pemilik slip yang bersangkutan
        abort_if(
            $penggajian->karyawan_nipy !== $nipy, 
            403, 
            'Akses Ditolak: Anda hanya dapat mengakses dan mencetak slip gaji pribadi Anda.'
        );

        $penggajian->load([
            'karyawan.kategori',
            'bulan.tahunAjaran',
        ]);

        return [
            'penggajian'     => $penggajian,
            'karyawan'       => $penggajian->karyawan ?? $karyawan,
            'statusList'     => Penggajian::STATUS,
            'penerimaanList' => Penggajian::PENERIMAAN,
            'potonganList'   => Penggajian::POTONGAN,
        ];
    }

    /**
     * Mengambil daftar opsi tahun penggajian dari rekaman data yang tersedia.
     *
     * @param string|null $nipy
     * @return array<int>
     */
    public function getTahunOptions(?string $nipy): array
    {
        $currentYear = Carbon::now()->year;
        $dbYears = [];

        if ($nipy) {
            $dbYears = Penggajian::where('karyawan_nipy', $nipy)
                ->selectRaw('DISTINCT YEAR(created_at) as tahun')
                ->orderByDesc('tahun')
                ->pluck('tahun')
                ->toArray();
        }

        $years = array_unique(array_merge([$currentYear, $currentYear - 1, $currentYear - 2], $dbYears));
        rsort($years);

        return $years;
    }
}
