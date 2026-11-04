<?php

namespace App\Services\User;

use App\Models\Admin\DataMaster\Karyawan;
use App\Models\Admin\DataMaster\TahunAjaran;
use App\Models\Admin\Tupoksi\Tupoksi;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserTupoksiService
{
    /**
     * Mengambil daftar tugas pokok dan fungsi (Tupoksi) milik pegawai login.
     *
     * @param User $user
     * @param array $filters
     * @return array
     */
    public function getUserTupoksiData(User $user, array $filters = []): array
    {
        $karyawan = $user->karyawan ?? Karyawan::where('nipy', $user->no_induk)->first();
        $nipy = $karyawan?->nipy ?? $user->no_induk;

        $taInput = $filters['tahun_ajaran_id'] ?? $filters['tahun_ajaran'] ?? null;
        $selectedTa = !empty($taInput) && $taInput !== 'semua'
            ? (int) $taInput
            : null;

        $search = !empty($filters['search']) ? trim((string) $filters['search']) : null;

        $statistik = $this->getStatistikTupoksi($nipy);
        $daftarTupoksi = $this->getDaftarTupoksi($nipy, $selectedTa, $search);
        $tahunAjaranOptions = $this->getTahunAjaranOptions($nipy);

        return [
            'user'               => $user,
            'karyawan'           => $karyawan,
            'kpi'                => [
                'total_tupoksi'   => $statistik['total_tupoksi'],
                'total_jabatan'   => $statistik['total_jabatan'],
                'total_ta'        => $statistik['total_ta'],
                'berkas_tersedia' => $statistik['total_berkas'],
            ],
            'totalTupoksi'       => $statistik['total_tupoksi'],
            'totalJabatan'       => $statistik['total_jabatan'],
            'totalTa'            => $statistik['total_ta'],
            'totalBerkas'        => $statistik['total_berkas'],
            'daftarTupoksi'      => $daftarTupoksi,
            'tupoksiList'        => $daftarTupoksi,
            'selectedTa'         => $selectedTa,
            'tahunAjaranOptions' => $tahunAjaranOptions,
            'daftarTahunAjaran'  => $tahunAjaranOptions,
            'filters'            => $filters,
        ];
    }

    /**
     * Hitung statistik tupoksi pegawai.
     *
     * @param string|null $nipy
     * @return array
     */
    public function getStatistikTupoksi(?string $nipy): array
    {
        if (!$nipy) {
            return [
                'total_tupoksi' => 0,
                'total_jabatan' => 0,
                'total_ta'      => 0,
                'total_berkas'  => 0,
            ];
        }

        $row = Tupoksi::where('karyawan_nipy', $nipy)->selectRaw("
            COUNT(*) as total_tupoksi,
            COUNT(DISTINCT jabatan) as total_jabatan,
            COUNT(DISTINCT akademik_master_tahun_ajaran_id) as total_ta,
            COALESCE(SUM(CASE WHEN berkas IS NOT NULL AND berkas <> '' THEN 1 ELSE 0 END), 0) as total_berkas
        ")->first();

        return [
            'total_tupoksi' => (int) ($row->total_tupoksi ?? 0),
            'total_jabatan' => (int) ($row->total_jabatan ?? 0),
            'total_ta'      => (int) ($row->total_ta ?? 0),
            'total_berkas'  => (int) ($row->total_berkas ?? 0),
        ];
    }

    /**
     * Mengambil daftar tupoksi pegawai login.
     *
     * @param string|null $nipy
     * @param int|null $taId
     * @return Collection
     */
    public function getDaftarTupoksi(?string $nipy, ?int $taId = null, ?string $search = null): Collection
    {
        if (!$nipy) {
            return new Collection();
        }

        $query = Tupoksi::with('tahunAjaran:id,nama_tahun_ajaran,semester')
            ->where('karyawan_nipy', $nipy);

        if ($taId) {
            $query->where('akademik_master_tahun_ajaran_id', $taId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('jabatan', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        return $query->orderByDesc('akademik_master_tahun_ajaran_id')
            ->orderByDesc('id')
            ->get();
    }

    /**
     * Opsi tahun ajaran tupoksi pegawai.
     *
     * @param string|null $nipy
     * @return array
     */
    public function getTahunAjaranOptions(?string $nipy): array
    {
        if (!$nipy) {
            return [];
        }

        $taIds = Tupoksi::where('karyawan_nipy', $nipy)
            ->whereNotNull('akademik_master_tahun_ajaran_id')
            ->distinct()
            ->pluck('akademik_master_tahun_ajaran_id')
            ->toArray();

        return TahunAjaran::whereIn('id', $taIds)
            ->orderByDesc('id')
            ->get(['id', 'nama_tahun_ajaran', 'semester'])
            ->mapWithKeys(fn ($t) => [
                $t->id => $t->nama_tahun_ajaran . ($t->semester ? ' - ' . ucfirst((string) $t->semester) : ''),
            ])
            ->toArray();
    }
}

