<?php

namespace App\Services\User;

use App\Models\Admin\DataMaster\Karyawan;
use App\Models\Admin\DataMaster\TahunAjaran;
use App\Models\Admin\SuratKeputusan\SuratKeputusan;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserSuratKeputusanService
{
    /**
     * Mengambil daftar arsip Surat Keputusan (SK) milik pegawai login.
     *
     * @param User $user
     * @param array $filters
     * @return array
     */
    public function getUserSuratKeputusanData(User $user, array $filters = []): array
    {
        $karyawan = $user->karyawan ?? Karyawan::where('nipy', $user->no_induk)->first();
        $nipy = $karyawan?->nipy ?? $user->no_induk;

        $taInput = $filters['tahun_ajaran_id'] ?? $filters['tahun_ajaran'] ?? null;
        $selectedTa = !empty($taInput) && $taInput !== 'semua'
            ? (int) $taInput
            : null;

        $search = !empty($filters['search']) ? trim((string) $filters['search']) : null;

        $statistik = $this->getStatistikSk($nipy);
        $daftarSk = $this->getDaftarSk($nipy, $selectedTa, $search);
        $tahunAjaranOptions = $this->getTahunAjaranOptions($nipy);

        return [
            'user'               => $user,
            'karyawan'           => $karyawan,
            'kpi'                => [
                'total_sk'      => $statistik['total_sk'],
                'total_jabatan' => $statistik['total_jabatan'],
                'total_ta'      => $statistik['total_ta'],
                'sk_terbaru'    => $statistik['sk_terakhir'],
            ],
            'totalSk'            => $statistik['total_sk'],
            'totalJabatan'       => $statistik['total_jabatan'],
            'totalTa'            => $statistik['total_ta'],
            'skTerakhir'         => $statistik['sk_terakhir'],
            'daftarSk'           => $daftarSk,
            'skList'             => $daftarSk,
            'selectedTa'         => $selectedTa,
            'tahunAjaranOptions' => $tahunAjaranOptions,
            'daftarTahunAjaran'  => $tahunAjaranOptions,
            'filters'            => $filters,
        ];
    }

    /**
     * Hitung ringkasan statistik SK pegawai.
     *
     * @param string|null $nipy
     * @return array
     */
    public function getStatistikSk(?string $nipy): array
    {
        if (!$nipy) {
            return [
                'total_sk'      => 0,
                'total_jabatan' => 0,
                'total_ta'      => 0,
                'sk_terakhir'   => '-',
            ];
        }

        $row = SuratKeputusan::where('karyawan_nipy', $nipy)->selectRaw("
            COUNT(*) as total_sk,
            COUNT(DISTINCT jabatan) as total_jabatan,
            COUNT(DISTINCT akademik_master_tahun_ajaran_id) as total_ta,
            MAX(tanggal_sk) as sk_terakhir
        ")->first();

        return [
            'total_sk'      => (int) ($row->total_sk ?? 0),
            'total_jabatan' => (int) ($row->total_jabatan ?? 0),
            'total_ta'      => (int) ($row->total_ta ?? 0),
            'sk_terakhir'   => $row->sk_terakhir ? date('d/m/Y', strtotime($row->sk_terakhir)) : '-',
        ];
    }

    /**
     * Mengambil seluruh SK pegawai login.
     *
     * @param string|null $nipy
     * @param int|null $taId
     * @return Collection
     */
    public function getDaftarSk(?string $nipy, ?int $taId = null, ?string $search = null): Collection
    {
        if (!$nipy) {
            return new Collection();
        }

        $query = SuratKeputusan::with('tahunAjaran:id,nama_tahun_ajaran,semester')
            ->where('karyawan_nipy', $nipy);

        if ($taId) {
            $query->where('akademik_master_tahun_ajaran_id', $taId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_sk', 'like', "%{$search}%")
                  ->orWhere('jabatan', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        return $query->orderByDesc('tanggal_sk')->orderByDesc('id')->get();
    }

    /**
     * Opsi tahun ajaran yang pernah tercatat pada SK pegawai.
     *
     * @param string|null $nipy
     * @return array
     */
    public function getTahunAjaranOptions(?string $nipy): array
    {
        if (!$nipy) {
            return [];
        }

        $taIds = SuratKeputusan::where('karyawan_nipy', $nipy)
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

