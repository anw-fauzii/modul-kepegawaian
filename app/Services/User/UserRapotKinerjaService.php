<?php

namespace App\Services\User;

use App\Models\Admin\DataMaster\Karyawan;
use App\Models\Admin\DataMaster\TahunAjaran;
use App\Models\Admin\RapotKinerja\RapotKinerja;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRapotKinerjaService
{
    /**
     * Mengambil daftar dokumen rapot kinerja milik pegawai login.
     *
     * @param User $user
     * @param array $filters
     * @return array
     */
    public function getUserRapotKinerjaData(User $user, array $filters = []): array
    {
        $karyawan = $user->karyawan ?? Karyawan::where('nipy', $user->no_induk)->first();
        $nipy = $karyawan?->nipy ?? $user->no_induk;

        $taInput = $filters['tahun_ajaran_id'] ?? $filters['tahun_ajaran'] ?? null;
        $selectedTa = !empty($taInput) && $taInput !== 'semua'
            ? (int) $taInput
            : null;

        $search = !empty($filters['search']) ? trim((string) $filters['search']) : null;

        $statistik = $this->getStatistikRapot($nipy);
        $daftarRapot = $this->getDaftarRapot($nipy, $selectedTa, $search);
        $tahunAjaranOptions = $this->getTahunAjaranOptions($nipy);

        return [
            'user'               => $user,
            'karyawan'           => $karyawan,
            'kpi'                => [
                'total_rapot'     => $statistik['total_rapot'],
                'total_ta'        => $statistik['total_ta'],
                'rapot_terbaru'   => $statistik['rapot_terbaru'],
                'berkas_tersedia' => $statistik['total_berkas'],
            ],
            'totalRapot'         => $statistik['total_rapot'],
            'totalTa'            => $statistik['total_ta'],
            'rapotTerbaru'       => $statistik['rapot_terbaru'],
            'totalBerkas'        => $statistik['total_berkas'],
            'daftarRapot'        => $daftarRapot,
            'rapotList'          => $daftarRapot,
            'selectedTa'         => $selectedTa,
            'tahunAjaranOptions' => $tahunAjaranOptions,
            'daftarTahunAjaran'  => $tahunAjaranOptions,
            'filters'            => $filters,
        ];
    }

    /**
     * Hitung statistik rapot kinerja pegawai.
     *
     * @param string|null $nipy
     * @return array
     */
    public function getStatistikRapot(?string $nipy): array
    {
        if (!$nipy) {
            return [
                'total_rapot'   => 0,
                'total_ta'      => 0,
                'rapot_terbaru' => '-',
                'total_berkas'  => 0,
            ];
        }

        $row = RapotKinerja::where('karyawan_nipy', $nipy)->selectRaw("
            COUNT(*) as total_rapot,
            COUNT(DISTINCT akademik_master_tahun_ajaran_id) as total_ta,
            COALESCE(SUM(CASE WHEN berkas IS NOT NULL AND berkas <> '' THEN 1 ELSE 0 END), 0) as total_berkas
        ")->first();

        $latest = RapotKinerja::with('tahunAjaran')
            ->where('karyawan_nipy', $nipy)
            ->orderByDesc('akademik_master_tahun_ajaran_id')
            ->first();

        return [
            'total_rapot'   => (int) ($row->total_rapot ?? 0),
            'total_ta'      => (int) ($row->total_ta ?? 0),
            'rapot_terbaru' => $latest?->tahunAjaran?->nama_tahun_ajaran ?? '-',
            'total_berkas'  => (int) ($row->total_berkas ?? 0),
        ];
    }

    /**
     * Mengambil daftar rapot kinerja pegawai login.
     *
     * @param string|null $nipy
     * @param int|null $taId
     * @return Collection
     */
    public function getDaftarRapot(?string $nipy, ?int $taId = null, ?string $search = null): Collection
    {
        if (!$nipy) {
            return new Collection();
        }

        $query = RapotKinerja::with('tahunAjaran:id,nama_tahun_ajaran,semester')
            ->where('karyawan_nipy', $nipy);

        if ($taId) {
            $query->where('akademik_master_tahun_ajaran_id', $taId);
        }

        if ($search) {
            $query->where('keterangan', 'like', "%{$search}%");
        }

        return $query->orderByDesc('akademik_master_tahun_ajaran_id')
            ->orderByDesc('id')
            ->get();
    }

    /**
     * Opsi tahun ajaran rapot kinerja pegawai.
     *
     * @param string|null $nipy
     * @return array
     */
    public function getTahunAjaranOptions(?string $nipy): array
    {
        if (!$nipy) {
            return [];
        }

        $taIds = RapotKinerja::where('karyawan_nipy', $nipy)
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

