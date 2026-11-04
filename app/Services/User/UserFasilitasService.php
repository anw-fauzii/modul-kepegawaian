<?php

namespace App\Services\User;

use App\Models\Admin\DataMaster\Karyawan;
use App\Models\Admin\Fasilitas\Fasilitas;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserFasilitasService
{
    /**
     * Mengambil data fasilitas inventaris yang dipinjam/diterima pegawai login.
     *
     * @param User $user
     * @param array $filters
     * @return array
     */
    public function getUserFasilitasData(User $user, array $filters = []): array
    {
        $karyawan = $user->karyawan ?? Karyawan::where('nipy', $user->no_induk)->first();
        $nipy = $karyawan?->nipy ?? $user->no_induk;

        $rawStatus = $filters['status'] ?? null;
        $selectedStatus = null;
        if (in_array($rawStatus, ['belum', 'dipinjam'], true)) {
            $selectedStatus = 'belum';
        } elseif (in_array($rawStatus, ['sudah', 'kembali'], true)) {
            $selectedStatus = 'sudah';
        }

        $search = !empty($filters['search']) ? trim((string) $filters['search']) : null;

        $statistik = $this->getStatistikFasilitas($nipy);
        $daftarFasilitas = $this->getDaftarFasilitas($nipy, $selectedStatus, $search);

        return [
            'user'            => $user,
            'karyawan'        => $karyawan,
            'kpi'             => [
                'total_aset'    => $statistik['total_fasilitas'],
                'belum_kembali' => $statistik['total_belum'],
                'sudah_kembali' => $statistik['total_sudah'],
                'total_unit'    => $statistik['total_unit_aktif'],
            ],
            'totalFasilitas'  => $statistik['total_fasilitas'],
            'totalBelum'      => $statistik['total_belum'],
            'totalSudah'      => $statistik['total_sudah'],
            'totalUnitAktif'  => $statistik['total_unit_aktif'],
            'daftarFasilitas' => $daftarFasilitas,
            'fasilitas'       => $daftarFasilitas,
            'selectedStatus'  => $selectedStatus,
            'filters'         => $filters,
        ];
    }

    /**
     * Hitung ringkasan statistik fasilitas inventaris pegawai.
     *
     * @param string|null $nipy
     * @return array
     */
    public function getStatistikFasilitas(?string $nipy): array
    {
        if (!$nipy) {
            return [
                'total_fasilitas'  => 0,
                'total_belum'      => 0,
                'total_sudah'      => 0,
                'total_unit_aktif' => 0,
            ];
        }

        $row = Fasilitas::where('karyawan_nipy', $nipy)->selectRaw("
            COUNT(*) as total_fasilitas,
            COALESCE(SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END), 0) as total_belum,
            COALESCE(SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END), 0) as total_sudah,
            COALESCE(SUM(CASE WHEN status = 0 THEN jumlah ELSE 0 END), 0) as total_unit_aktif
        ")->first();

        return [
            'total_fasilitas'  => (int) ($row->total_fasilitas ?? 0),
            'total_belum'      => (int) ($row->total_belum ?? 0),
            'total_sudah'      => (int) ($row->total_sudah ?? 0),
            'total_unit_aktif' => (int) ($row->total_unit_aktif ?? 0),
        ];
    }

    /**
     * Mengambil daftar fasilitas pegawai login.
     *
     * @param string|null $nipy
     * @param string|null $status
     * @return Collection
     */
    public function getDaftarFasilitas(?string $nipy, ?string $status = null, ?string $search = null): Collection
    {
        if (!$nipy) {
            return new Collection();
        }

        $query = Fasilitas::where('karyawan_nipy', $nipy);

        if ($status === 'belum') {
            $query->where('status', false);
        } elseif ($status === 'sudah') {
            $query->where('status', true);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_fasilitas', 'like', "%{$search}%")
                  ->orWhere('catatan', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('status')
            ->orderByDesc('tahun_terima')
            ->orderByDesc('id')
            ->get();
    }
}

