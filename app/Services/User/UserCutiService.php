<?php

namespace App\Services\User;

use App\Models\Admin\Cuti\Cuti;
use App\Models\Admin\DataMaster\HariLibur;
use App\Models\Admin\DataMaster\JenisCuti;
use App\Models\Admin\DataMaster\Karyawan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class UserCutiService
{
    /**
     * Mengambil dan memproses data riwayat cuti pribadi pegawai untuk halaman index.
     *
     * @param User $user
     * @param array $filters
     * @return array
     */
    public function getUserCutiData(User $user, array $filters = []): array
    {
        // 1. Resolusi identitas Karyawan (NIPY) yang terikat dengan user login
        $karyawan = $user->karyawan ?? Karyawan::where('nipy', $user->no_induk)->first();
        $nipy = $karyawan?->nipy ?? $user->no_induk;

        // 2. Filter Periode Tahun, Status & Pencarian
        $selectedYear = null;
        if (isset($filters['tahun'])) {
            if ($filters['tahun'] !== 'semua' && $filters['tahun'] !== '') {
                $selectedYear = (int) $filters['tahun'];
            }
        } else {
            $selectedYear = Carbon::now()->year;
        }

        $selectedStatus = !empty($filters['status']) ? $filters['status'] : null;
        $search = !empty($filters['search']) ? trim($filters['search']) : null;

        // 3. Ringkasan KPI Statistik (Agregasi 1 Query Bersatu)
        $statistik = $this->getStatistikCuti($nipy, $selectedYear, $search);

        // 4. Riwayat Seluruh Pengajuan Cuti Pegawai (Zero N+1 Eager Loading)
        $riwayatCuti = $this->getRiwayatCuti($nipy, $selectedYear, $selectedStatus, $search);

        // 5. Opsi Filter Tahun dari riwayat yang pernah tercatat
        $tahunOptions = $this->getTahunOptions($nipy);

        return [
            'user'           => $user,
            'karyawan'       => $karyawan,
            'totalPengajuan' => $statistik['total_pengajuan'],
            'totalDisetujui' => $statistik['total_disetujui'],
            'totalMenunggu'  => $statistik['total_menunggu'],
            'totalHariCuti'  => $statistik['total_hari_cuti'],
            'riwayatCuti'    => $riwayatCuti,
            'selectedYear'   => $selectedYear,
            'selectedStatus' => $selectedStatus,
            'tahunOptions'   => $tahunOptions,
            'filters'        => $filters,
            'search'         => $search,
        ];
    }

    /**
     * Mengambil data master yang diperlukan untuk formulir pengajuan cuti baru.
     *
     * @param User $user
     * @return array
     */
    public function getFormData(User $user): array
    {
        $karyawan = $user->karyawan ?? Karyawan::where('nipy', $user->no_induk)->first();

        $jenisCutiList = JenisCuti::query()
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();

        $hariLiburMap = HariLibur::getHolidayMap();

        return [
            'user'          => $user,
            'karyawan'      => $karyawan,
            'jenisCutiList' => $jenisCutiList,
            'hariLiburMap'  => $hariLiburMap,
        ];
    }

    /**
     * Menyimpan pengajuan cuti baru dari pegawai yang sedang login.
     *
     * @param User $user
     * @param array $data
     * @return Cuti
     * @throws ValidationException
     */
    public function storeCuti(User $user, array $data): Cuti
    {
        $karyawan = $user->karyawan ?? Karyawan::where('nipy', $user->no_induk)->first();
        $nipy = $karyawan?->nipy ?? $user->no_induk;

        if (!$nipy) {
            throw ValidationException::withMessages([
                'karyawan' => 'Data profil pegawai Anda tidak ditemukan dalam sistem. Harap hubungi admin kepegawaian.',
            ]);
        }

        // Cek overlap rentang tanggal cuti dengan pengajuan sebelumnya
        $hasOverlap = Cuti::where('karyawan_nipy', $nipy)
            ->whereDate('awal_cuti', '<=', $data['akhir_cuti'])
            ->whereDate('akhir_cuti', '>=', $data['awal_cuti'])
            ->exists();

        if ($hasOverlap) {
            throw ValidationException::withMessages([
                'awal_cuti' => 'Anda sudah memiliki pengajuan cuti pada rentang tanggal tersebut.',
            ]);
        }

        // Deteksi jenis cuti & pengecualian Cuti Melahirkan (90 hari kalender penuh)
        $jenisCuti = JenisCuti::find($data['kepegawaian_master_jenis_cuti_id']);
        $isMelahirkan = $jenisCuti?->isMelahirkan() ?? false;

        // Hitung durasi: Cuti Melahirkan = hari kalender penuh, Reguler = hari kerja (melewati akhir pekan & libur nasional)
        $jumlahHari = Cuti::hitungJumlahHari($data['awal_cuti'], $data['akhir_cuti'], null, $isMelahirkan);
        if ($jumlahHari < 1) {
            throw ValidationException::withMessages([
                'akhir_cuti' => $isMelahirkan
                    ? 'Rentang tanggal cuti melahirkan tidak valid.'
                    : 'Rentang tanggal yang dipilih hanya mencakup akhir pekan atau hari libur nasional.',
            ]);
        }

        return Cuti::create([
            'karyawan_nipy'                    => $nipy,
            'kepegawaian_master_jenis_cuti_id' => $data['kepegawaian_master_jenis_cuti_id'],
            'awal_cuti'                        => $data['awal_cuti'],
            'akhir_cuti'                       => $data['akhir_cuti'],
            'jumlah_hari'                      => $jumlahHari,
            'alamat'                           => $data['alamat'],
            'telepon'                          => $data['telepon'],
            'alasan'                           => $data['alasan'] ?? null,
            'acc'                              => false,
            'catatan_acc'                      => null,
            'tgl_acc'                          => null,
            'acc_by'                           => null,
        ]);
    }

    /**
     * Membatalkan pengajuan cuti pribadi jika statusnya masih menunggu ACC.
     *
     * @param User $user
     * @param Cuti $cuti
     * @return bool
     */
    public function batalkanCuti(User $user, Cuti $cuti): bool
    {
        $karyawan = $user->karyawan ?? Karyawan::where('nipy', $user->no_induk)->first();
        $nipy = $karyawan?->nipy ?? $user->no_induk;

        // Otorisasi: Pastikan cuti milik user yang bersangkutan
        abort_if(
            $cuti->karyawan_nipy !== $nipy,
            403,
            'Akses Ditolak: Anda hanya dapat membatalkan pengajuan cuti pribadi Anda.'
        );

        // Validasi: Pengajuan yang sudah di-ACC tidak dapat dibatalkan mandiri
        if ($cuti->acc) {
            abort(400, 'Pengajuan cuti yang sudah disetujui (ACC) tidak dapat dibatalkan secara mandiri. Harap hubungi admin kepegawaian.');
        }

        return (bool) $cuti->delete();
    }

    /**
     * Menghitung statistik rekapitulasi cuti (Total, Disetujui, Menunggu, Total Hari).
     *
     * @param string|null $nipy
     * @param int|null $tahun
     * @param string|null $search
     * @return array
     */
    public function getStatistikCuti(?string $nipy, ?int $tahun = null, ?string $search = null): array
    {
        if (!$nipy) {
            return [
                'total_pengajuan' => 0,
                'total_disetujui' => 0,
                'total_menunggu'  => 0,
                'total_hari_cuti' => 0,
            ];
        }

        $query = Cuti::where('karyawan_nipy', $nipy);

        if ($tahun) {
            $query->whereYear('awal_cuti', $tahun);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('alasan', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhere('telepon', 'like', "%{$search}%")
                  ->orWhereHas('jenisCuti', function ($sub) use ($search) {
                      $sub->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        $row = $query->selectRaw("
            COUNT(*) as total_pengajuan,
            COALESCE(SUM(CASE WHEN acc = 1 THEN 1 ELSE 0 END), 0) as total_disetujui,
            COALESCE(SUM(CASE WHEN acc = 0 THEN 1 ELSE 0 END), 0) as total_menunggu,
            COALESCE(SUM(CASE WHEN acc = 1 THEN jumlah_hari ELSE 0 END), 0) as total_hari_cuti
        ")->first();

        return [
            'total_pengajuan' => (int) ($row->total_pengajuan ?? 0),
            'total_disetujui' => (int) ($row->total_disetujui ?? 0),
            'total_menunggu'  => (int) ($row->total_menunggu ?? 0),
            'total_hari_cuti' => (int) ($row->total_hari_cuti ?? 0),
        ];
    }

    /**
     * Mengambil seluruh rekaman cuti pegawai dengan relasi jenis cuti & approver.
     *
     * @param string|null $nipy
     * @param int|null $tahun
     * @param string|null $status
     * @param string|null $search
     * @return Collection
     */
    public function getRiwayatCuti(?string $nipy, ?int $tahun = null, ?string $status = null, ?string $search = null): Collection
    {
        if (!$nipy) {
            return new Collection();
        }

        $query = Cuti::with([
                'jenisCuti:id,nama,lama_hari',
                'approver:id,name',
            ])
            ->where('karyawan_nipy', $nipy);

        if ($tahun) {
            $query->whereYear('awal_cuti', $tahun);
        }

        if ($status === 'disetujui') {
            $query->where('acc', true);
        } elseif ($status === 'menunggu') {
            $query->where('acc', false);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('alasan', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhere('telepon', 'like', "%{$search}%")
                  ->orWhereHas('jenisCuti', function ($sub) use ($search) {
                      $sub->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        return $query->orderByDesc('awal_cuti')->orderByDesc('id')->get();
    }

    /**
     * Mengambil daftar opsi tahun dari riwayat cuti yang tercatat.
     *
     * @param string|null $nipy
     * @return array<int>
     */
    public function getTahunOptions(?string $nipy): array
    {
        $currentYear = Carbon::now()->year;
        $dbYears = [];

        if ($nipy) {
            $dbYears = Cuti::where('karyawan_nipy', $nipy)
                ->selectRaw('DISTINCT YEAR(awal_cuti) as tahun')
                ->orderByDesc('tahun')
                ->pluck('tahun')
                ->toArray();
        }

        $years = array_unique(array_merge([$currentYear, $currentYear - 1], $dbYears));
        rsort($years);

        return $years;
    }
}

