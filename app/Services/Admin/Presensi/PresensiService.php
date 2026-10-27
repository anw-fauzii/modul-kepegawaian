<?php

namespace App\Services\Admin\Presensi;

use App\Models\Admin\DataMaster\Bulan;
use App\Models\Admin\DataMaster\Karyawan;
use App\Models\Admin\DataMaster\TahunAjaran;
use App\Models\Admin\Presensi\Presensi;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PresensiService
{
    /**
     * Mengambil daftar presensi terpaginasi dengan Eager Loading (Zero N+1 Query).
     */
    public function getPaginated(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return Presensi::query()
            ->select([
                'id',
                'karyawan_nipy',
                'kepegawaian_master_bulan_id',
                'kepegawaian_master_jam_kerja_id',
                'tanggal',
                'jam_masuk',
                'jam_pulang',
                'status',
                'keterlambatan_menit',
                'izin_terlambat',
                'pulang_cepat_menit',
                'izin_pulang_cepat',
                'catatan',
                'created_at',
            ])
            ->with([
                'karyawan' => fn ($query) => $query
                    ->select('nipy', 'nama_lengkap', 'gelar', 'kepegawaian_master_kategori_id')
                    ->with([
                        'kategori' => fn ($kq) => $kq->select('id', 'nama_kategori'),
                    ]),
                'bulan' => fn ($query) => $query
                    ->select('id', 'nama_bulan', 'akademik_master_tahun_ajaran_id')
                    ->with([
                        'tahunAjaran' => fn ($tq) => $tq->select('id', 'nama_tahun_ajaran'),
                    ]),
                'jamKerja' => fn ($query) => $query
                    ->select('id', 'nama', 'jam_masuk', 'jam_pulang_senin_kamis', 'jam_pulang_jumat'),
            ])
            ->filter($filters)
            ->latest('tanggal')
            ->latest('jam_masuk')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Mengambil daftar karyawan berdasarkan kategori untuk form input bulk presensi.
     * Mengikutsertakan snapshot jam kerja dan presensi yang sudah tercatat pada tanggal terkait.
     */
    public function getKaryawanForBulk(int $kategoriId, string $tanggal): Collection
    {
        return Karyawan::query()
            ->select('nipy', 'nama_lengkap', 'gelar', 'kepegawaian_master_kategori_id', 'kepegawaian_master_jam_kerja_id')
            ->with([
                'jamKerja:id,nama,jam_masuk,jam_pulang_senin_kamis,jam_pulang_jumat',
                'kategori:id,nama_kategori',
                'presensi' => fn ($q) => $q->whereDate('tanggal', $tanggal),
            ])
            ->where('kepegawaian_master_kategori_id', $kategoriId)
            ->orderBy('nama_lengkap')
            ->get();
    }

    /**
     * Menyimpan presensi secara massal (Bulk) per kategori dalam 1 transaksi database.
     * Baris karyawan yang statusnya kosong / belum hadir akan dilewati tanpa error.
     */
    public function storeBulk(array $data): int
    {
        return DB::transaction(function () use ($data) {
            $tanggal = $data['tanggal'];
            $kategoriId = (int) $data['kepegawaian_master_kategori_id'];
            $bulanId = ! empty($data['kepegawaian_master_bulan_id'])
                ? (int) $data['kepegawaian_master_bulan_id']
                : $this->determineBulanId($tanggal);

            $karyawanCollection = Karyawan::with('jamKerja')
                ->where('kepegawaian_master_kategori_id', $kategoriId)
                ->get()
                ->keyBy('nipy');

            $savedCount = 0;

            foreach ($data['items'] ?? [] as $item) {
                $nipy = $item['karyawan_nipy'] ?? null;
                if (! $nipy || ! isset($karyawanCollection[$nipy])) {
                    continue;
                }

                $karyawan = $karyawanCollection[$nipy];
                $status = $item['status'] ?? null;
                $jamMasuk = ! empty($item['jam_masuk']) ? $item['jam_masuk'] : null;
                $jamPulang = ! empty($item['jam_pulang']) ? $item['jam_pulang'] : null;

                // Jika status kosong dan jam masuk kosong, lewati (belum hadir)
                if (empty($status) && empty($jamMasuk)) {
                    continue;
                }

                // Default status jika jam_masuk diisi tapi status belum dipilih
                if (empty($status) && ! empty($jamMasuk)) {
                    $status = 'hadir';
                }

                $calculated = $this->calculateLateAndEarly(
                    jamKerja: $karyawan->jamKerja,
                    tanggal: $tanggal,
                    jamMasuk: $jamMasuk,
                    jamPulang: $jamPulang,
                    currentStatus: $status
                );

                Presensi::updateOrCreate(
                    [
                        'karyawan_nipy' => $nipy,
                        'tanggal'       => $tanggal,
                    ],
                    [
                        'kepegawaian_master_bulan_id'     => $bulanId,
                        'kepegawaian_master_jam_kerja_id' => $karyawan->kepegawaian_master_jam_kerja_id,
                        'status'                          => $calculated['status'],
                        'jam_masuk'                       => $jamMasuk,
                        'jam_pulang'                      => $jamPulang,
                        'keterlambatan_menit'             => $calculated['keterlambatan_menit'],
                        'izin_terlambat'                  => ! empty($item['izin_terlambat']),
                        'pulang_cepat_menit'              => $calculated['pulang_cepat_menit'],
                        'izin_pulang_cepat'               => ! empty($item['izin_pulang_cepat']),
                        'catatan'                         => $item['catatan'] ?? null,
                    ]
                );

                $savedCount++;
            }

            return $savedCount;
        });
    }

    /**
     * Menyimpan data presensi manual satuan oleh admin.
     */
    public function store(array $data): Presensi
    {
        return DB::transaction(function () use ($data) {
            $karyawan = Karyawan::with('jamKerja')
                ->where('nipy', $data['karyawan_nipy'])
                ->firstOrFail();

            $bulanId = $data['kepegawaian_master_bulan_id'] ?? $this->determineBulanId($data['tanggal']);

            $calculated = $this->calculateLateAndEarly(
                jamKerja: $karyawan->jamKerja,
                tanggal: $data['tanggal'],
                jamMasuk: $data['jam_masuk'] ?? null,
                jamPulang: $data['jam_pulang'] ?? null,
                currentStatus: $data['status']
            );

            return Presensi::updateOrCreate(
                [
                    'karyawan_nipy' => $data['karyawan_nipy'],
                    'tanggal'       => $data['tanggal'],
                ],
                [
                    'kepegawaian_master_bulan_id'     => $bulanId,
                    'kepegawaian_master_jam_kerja_id' => $karyawan->kepegawaian_master_jam_kerja_id,
                    'status'                          => $calculated['status'],
                    'jam_masuk'                       => $data['jam_masuk'] ?? null,
                    'jam_pulang'                      => $data['jam_pulang'] ?? null,
                    'keterlambatan_menit'             => $calculated['keterlambatan_menit'],
                    'izin_terlambat'                  => (bool) ($data['izin_terlambat'] ?? false),
                    'pulang_cepat_menit'              => $calculated['pulang_cepat_menit'],
                    'izin_pulang_cepat'               => (bool) ($data['izin_pulang_cepat'] ?? false),
                    'catatan'                         => $data['catatan'] ?? null,
                ]
            );
        });
    }

    /**
     * Memperbarui data presensi satuan.
     */
    public function update(Presensi $presensi, array $data): bool
    {
        return DB::transaction(function () use ($presensi, $data) {
            $karyawan = Karyawan::with('jamKerja')
                ->where('nipy', $data['karyawan_nipy'])
                ->firstOrFail();

            $bulanId = $data['kepegawaian_master_bulan_id'] ?? $this->determineBulanId($data['tanggal']);

            $calculated = $this->calculateLateAndEarly(
                jamKerja: $karyawan->jamKerja,
                tanggal: $data['tanggal'],
                jamMasuk: $data['jam_masuk'] ?? null,
                jamPulang: $data['jam_pulang'] ?? null,
                currentStatus: $data['status']
            );

            return $presensi->update([
                'karyawan_nipy'                   => $data['karyawan_nipy'],
                'kepegawaian_master_bulan_id'     => $bulanId,
                'kepegawaian_master_jam_kerja_id' => $karyawan->kepegawaian_master_jam_kerja_id,
                'tanggal'                         => $data['tanggal'],
                'status'                          => $calculated['status'],
                'jam_masuk'                       => $data['jam_masuk'] ?? null,
                'jam_pulang'                      => $data['jam_pulang'] ?? null,
                'keterlambatan_menit'             => $calculated['keterlambatan_menit'],
                'izin_terlambat'                  => (bool) ($data['izin_terlambat'] ?? false),
                'pulang_cepat_menit'              => $calculated['pulang_cepat_menit'],
                'izin_pulang_cepat'               => (bool) ($data['izin_pulang_cepat'] ?? false),
                'catatan'                         => $data['catatan'] ?? null,
            ]);
        });
    }

    /**
     * Menghapus catatan presensi.
     */
    public function delete(Presensi $presensi): bool
    {
        return DB::transaction(fn () => $presensi->delete());
    }

    /**
     * Rekap Bulanan Teroptimasi Berdasarkan Master Bulan & Kategori (Zero N+1 Query).
     */
    public function getRekapBulanan(int $bulanId, ?int $kategoriId = null): Collection
    {
        return Karyawan::query()
            ->select('nipy', 'nama_lengkap', 'gelar', 'kepegawaian_master_kategori_id')
            ->with(['kategori:id,nama_kategori'])
            ->when($kategoriId, fn ($q) => $q->where('kepegawaian_master_kategori_id', $kategoriId))
            ->withCount([
                'presensi as total_hadir' => fn ($q) => $q->where('kepegawaian_master_bulan_id', $bulanId)->where('status', 'hadir'),
                'presensi as total_terlambat' => fn ($q) => $q->where('kepegawaian_master_bulan_id', $bulanId)->where('status', 'terlambat'),
                'presensi as total_terlambat_murni' => fn ($q) => $q->where('kepegawaian_master_bulan_id', $bulanId)->where('status', 'terlambat')->where('izin_terlambat', false),
                'presensi as total_terlambat_izin' => fn ($q) => $q->where('kepegawaian_master_bulan_id', $bulanId)->where('status', 'terlambat')->where('izin_terlambat', true),
                'presensi as total_pulang_cepat' => fn ($q) => $q->where('kepegawaian_master_bulan_id', $bulanId)->where('pulang_cepat_menit', '>', 0),
                'presensi as total_pulang_cepat_izin' => fn ($q) => $q->where('kepegawaian_master_bulan_id', $bulanId)->where('pulang_cepat_menit', '>', 0)->where('izin_pulang_cepat', true),
                'presensi as total_izin' => fn ($q) => $q->where('kepegawaian_master_bulan_id', $bulanId)->where('status', 'izin'),
                'presensi as total_sakit' => fn ($q) => $q->where('kepegawaian_master_bulan_id', $bulanId)->where('status', 'sakit'),
                'presensi as total_cuti' => fn ($q) => $q->where('kepegawaian_master_bulan_id', $bulanId)->where('status', 'cuti'),
                'presensi as total_tugas' => fn ($q) => $q->where('kepegawaian_master_bulan_id', $bulanId)->where('status', 'tugas'),
                'presensi as total_alpa' => fn ($q) => $q->where('kepegawaian_master_bulan_id', $bulanId)->where('status', 'alpa'),
            ])
            ->withSum([
                'presensi as akumulasi_keterlambatan_menit' => fn ($q) => $q->where('kepegawaian_master_bulan_id', $bulanId)->where('izin_terlambat', false),
            ], 'keterlambatan_menit')
            ->withSum([
                'presensi as akumulasi_pulang_cepat_menit' => fn ($q) => $q->where('kepegawaian_master_bulan_id', $bulanId)->where('izin_pulang_cepat', false),
            ], 'pulang_cepat_menit')
            ->orderBy('nama_lengkap')
            ->get();
    }

    /**
     * Statistik Presensi Tahunan Terpadu Berdasarkan Akademik Master Tahun Ajaran ID.
     * Mengagregasi data dari seluruh kepegawaian_master_bulan di bawah tahun ajaran tersebut.
     */
    public function getStatistikTahunan(int $tahunAjaranId, ?int $kategoriId = null): array
    {
        $tahunAjaran = TahunAjaran::findOrFail($tahunAjaranId);

        // Ambil seluruh bulan yang terdaftar pada tahun ajaran ini
        $daftarBulan = Bulan::where('akademik_master_tahun_ajaran_id', $tahunAjaranId)
            ->orderBy('id')
            ->get();

        $bulanIds = $daftarBulan->pluck('id')->all();

        if (empty($bulanIds)) {
            return [
                'tahunAjaran'      => $tahunAjaran,
                'daftarBulan'      => $daftarBulan,
                'breakdownBulanan' => collect(),
                'kpiTahunan'       => $this->emptyKpi(),
                'rekapPegawai'     => collect(),
                'topDisiplin'      => collect(),
                'topTerlambat'     => collect(),
                'topAlpa'          => collect(),
                'chartData'        => [
                    'labels'          => [],
                    'hadir'           => [],
                    'terlambat'       => [],
                    'tugas'           => [],
                    'tidakHadir'      => [],
                    'donutData'       => [0, 0, 0, 0, 0, 0, 0],
                ],
            ];
        }

        // 1. Agregasi Presensi per Bulan (Single Aggregation Query)
        $rawPerBulan = Presensi::query()
            ->whereIn('kepegawaian_master_bulan_id', $bulanIds)
            ->when($kategoriId, fn ($q) => $q->whereHas('karyawan', fn ($kq) => $kq->where('kepegawaian_master_kategori_id', $kategoriId)))
            ->selectRaw("
                kepegawaian_master_bulan_id,
                COUNT(*) as total_log,
                COUNT(DISTINCT karyawan_nipy) as total_pegawai,
                SUM(CASE WHEN status = 'hadir' THEN 1 ELSE 0 END) as sum_hadir,
                SUM(CASE WHEN status = 'terlambat' THEN 1 ELSE 0 END) as sum_terlambat,
                SUM(CASE WHEN status = 'terlambat' AND izin_terlambat = 1 THEN 1 ELSE 0 END) as sum_terlambat_izin,
                SUM(CASE WHEN status = 'terlambat' AND izin_terlambat = 0 THEN 1 ELSE 0 END) as sum_terlambat_murni,
                SUM(CASE WHEN status = 'terlambat' AND izin_terlambat = 0 THEN keterlambatan_menit ELSE 0 END) as sum_menit_terlambat,
                SUM(CASE WHEN status = 'tugas' THEN 1 ELSE 0 END) as sum_tugas,
                SUM(CASE WHEN status = 'izin' THEN 1 ELSE 0 END) as sum_izin,
                SUM(CASE WHEN status = 'sakit' THEN 1 ELSE 0 END) as sum_sakit,
                SUM(CASE WHEN status = 'cuti' THEN 1 ELSE 0 END) as sum_cuti,
                SUM(CASE WHEN status = 'alpa' THEN 1 ELSE 0 END) as sum_alpa,
                SUM(CASE WHEN pulang_cepat_menit > 0 THEN 1 ELSE 0 END) as sum_pulang_cepat,
                SUM(CASE WHEN pulang_cepat_menit > 0 AND izin_pulang_cepat = 0 THEN pulang_cepat_menit ELSE 0 END) as sum_menit_pulang_cepat
            ")
            ->groupBy('kepegawaian_master_bulan_id')
            ->get()
            ->keyBy('kepegawaian_master_bulan_id');

        // Petakan data ke seluruh bulan terdaftar
        $breakdownBulanan = $daftarBulan->map(function ($bulan) use ($rawPerBulan) {
            $data = $rawPerBulan->get($bulan->id);
            $sumHadir = (int) ($data->sum_hadir ?? 0);
            $sumTerlambat = (int) ($data->sum_terlambat ?? 0);
            $sumTugas = (int) ($data->sum_tugas ?? 0);
            $sumIzin = (int) ($data->sum_izin ?? 0);
            $sumSakit = (int) ($data->sum_sakit ?? 0);
            $sumCuti = (int) ($data->sum_cuti ?? 0);
            $sumAlpa = (int) ($data->sum_alpa ?? 0);
            $sumTerlambatIzin = (int) ($data->sum_terlambat_izin ?? 0);
            $sumMenitTerlambat = (int) ($data->sum_menit_terlambat ?? 0);
            $sumPulangCepat = (int) ($data->sum_pulang_cepat ?? 0);
            $sumMenitPulangCepat = (int) ($data->sum_menit_pulang_cepat ?? 0);
            $totalPegawai = (int) ($data->total_pegawai ?? 0);

            $hadirEfektif = $sumHadir + $sumTerlambat;
            $totalAbsen = $sumIzin + $sumSakit + $sumCuti + $sumAlpa;
            $totalHari = $hadirEfektif + $totalAbsen;
            $rate = $totalHari > 0 ? round(($hadirEfektif / $totalHari) * 100, 1) : 0;

            return (object) [
                'bulan_id'               => $bulan->id,
                'nama_bulan'             => $bulan->nama_bulan,
                'status_bulan'           => $bulan->status,
                'total_pegawai'          => $totalPegawai,
                'sum_hadir'              => $sumHadir,
                'sum_terlambat'          => $sumTerlambat,
                'sum_terlambat_izin'     => $sumTerlambatIzin,
                'sum_menit_terlambat'    => $sumMenitTerlambat,
                'sum_tugas'              => $sumTugas,
                'sum_izin'               => $sumIzin,
                'sum_sakit'              => $sumSakit,
                'sum_cuti'               => $sumCuti,
                'sum_alpa'               => $sumAlpa,
                'sum_pulang_cepat'       => $sumPulangCepat,
                'sum_menit_pulang_cepat' => $sumMenitPulangCepat,
                'hadir_efektif'          => $hadirEfektif,
                'total_catatan'          => $totalHari,
                'rate_kehadiran'         => $rate,
            ];
        });

        // 2. Ringkasan Total Tahunan (Executive KPI Cards)
        $totalHadir = $breakdownBulanan->sum('sum_hadir');
        $totalTerlambat = $breakdownBulanan->sum('sum_terlambat');
        $totalTerlambatIzin = $breakdownBulanan->sum('sum_terlambat_izin');
        $totalMenitTerlambat = $breakdownBulanan->sum('sum_menit_terlambat');
        $totalTugas = $breakdownBulanan->sum('sum_tugas');
        $totalIzin = $breakdownBulanan->sum('sum_izin');
        $totalSakit = $breakdownBulanan->sum('sum_sakit');
        $totalCuti = $breakdownBulanan->sum('sum_cuti');
        $totalAlpa = $breakdownBulanan->sum('sum_alpa');
        $totalPulangCepat = $breakdownBulanan->sum('sum_pulang_cepat');
        $totalMenitPulangCepat = $breakdownBulanan->sum('sum_menit_pulang_cepat');

        // Kehadiran statistik hanya dihitung dari Hadir dan Terlambat (Tugas tidak termasuk statistik)
        $totalHadirEfektif = $totalHadir + $totalTerlambat;
        $totalTidakHadir = $totalIzin + $totalSakit + $totalCuti + $totalAlpa;
        $totalHariAkumulasi = $totalHadirEfektif + $totalTidakHadir;
        $persenKehadiranTahunan = $totalHariAkumulasi > 0
            ? round(($totalHadirEfektif / $totalHariAkumulasi) * 100, 1)
            : 0;

        $kpiTahunan = [
            'total_log'                => $totalHariAkumulasi,
            'total_hadir'              => $totalHadir,
            'total_terlambat'          => $totalTerlambat,
            'total_terlambat_izin'     => $totalTerlambatIzin,
            'total_menit_terlambat'    => $totalMenitTerlambat,
            'total_tugas'              => $totalTugas,
            'total_izin'               => $totalIzin,
            'total_sakit'              => $totalSakit,
            'total_cuti'               => $totalCuti,
            'total_alpa'               => $totalAlpa,
            'total_pulang_cepat'       => $totalPulangCepat,
            'total_menit_pulang_cepat' => $totalMenitPulangCepat,
            'total_hadir_efektif'      => $totalHadirEfektif,
            'total_tidak_hadir'        => $totalTidakHadir,
            'persen_kehadiran'         => $persenKehadiranTahunan,
        ];

        // 3. Rekap Tahunan per Pegawai (Eager Loading withCount & withSum)
        $rekapPegawai = Karyawan::query()
            ->select('nipy', 'nama_lengkap', 'gelar', 'kepegawaian_master_kategori_id')
            ->with(['kategori:id,nama_kategori'])
            ->when($kategoriId, fn ($q) => $q->where('kepegawaian_master_kategori_id', $kategoriId))
            ->withCount([
                'presensi as total_hadir' => fn ($q) => $q->whereIn('kepegawaian_master_bulan_id', $bulanIds)->where('status', 'hadir'),
                'presensi as total_terlambat' => fn ($q) => $q->whereIn('kepegawaian_master_bulan_id', $bulanIds)->where('status', 'terlambat'),
                'presensi as total_terlambat_izin' => fn ($q) => $q->whereIn('kepegawaian_master_bulan_id', $bulanIds)->where('status', 'terlambat')->where('izin_terlambat', true),
                'presensi as total_tugas' => fn ($q) => $q->whereIn('kepegawaian_master_bulan_id', $bulanIds)->where('status', 'tugas'),
                'presensi as total_izin' => fn ($q) => $q->whereIn('kepegawaian_master_bulan_id', $bulanIds)->where('status', 'izin'),
                'presensi as total_sakit' => fn ($q) => $q->whereIn('kepegawaian_master_bulan_id', $bulanIds)->where('status', 'sakit'),
                'presensi as total_cuti' => fn ($q) => $q->whereIn('kepegawaian_master_bulan_id', $bulanIds)->where('status', 'cuti'),
                'presensi as total_alpa' => fn ($q) => $q->whereIn('kepegawaian_master_bulan_id', $bulanIds)->where('status', 'alpa'),
                'presensi as total_pulang_cepat' => fn ($q) => $q->whereIn('kepegawaian_master_bulan_id', $bulanIds)->where('pulang_cepat_menit', '>', 0),
            ])
            ->withSum([
                'presensi as akumulasi_keterlambatan_menit' => fn ($q) => $q->whereIn('kepegawaian_master_bulan_id', $bulanIds)->where('izin_terlambat', false),
            ], 'keterlambatan_menit')
            ->withSum([
                'presensi as akumulasi_pulang_cepat_menit' => fn ($q) => $q->whereIn('kepegawaian_master_bulan_id', $bulanIds)->where('izin_pulang_cepat', false),
            ], 'pulang_cepat_menit')
            ->get()
            ->map(function ($karyawan) {
                $hadir = (int) $karyawan->total_hadir;
                $terlambat = (int) $karyawan->total_terlambat;
                $izin = (int) $karyawan->total_izin;
                $sakit = (int) $karyawan->total_sakit;
                $cuti = (int) $karyawan->total_cuti;
                $alpa = (int) $karyawan->total_alpa;

                // Kehadiran statistik hanya dihitung dari Hadir dan Terlambat (Tugas tidak termasuk statistik)
                $totalHadirEfektif = $hadir + $terlambat;
                $totalAbsen = $izin + $sakit + $cuti + $alpa;
                $totalCatatan = $totalHadirEfektif + $totalAbsen;

                $rate = $totalCatatan > 0 ? round(($totalHadirEfektif / $totalCatatan) * 100, 1) : 0;

                $karyawan->total_hadir_efektif = $totalHadirEfektif;
                $karyawan->total_catatan = $totalCatatan;
                $karyawan->persentase_kehadiran = $rate;

                return $karyawan;
            })
            ->sortBy('nama_lengkap')
            ->values();

        // 4. Highlight & Early Warning HRD
        $karyawanDenganCatatan = $rekapPegawai->filter(fn ($k) => $k->total_catatan > 0);

        // Top Disiplin: Kehadiran >= 90%, alpa = 0, diurutkan persentase tertinggi & menit terlambat terendah
        $topDisiplin = $karyawanDenganCatatan
            ->filter(fn ($k) => $k->total_alpa == 0)
            ->sortByDesc(fn ($k) => ($k->persentase_kehadiran * 1000) - ($k->akumulasi_keterlambatan_menit ?? 0))
            ->take(5)
            ->values();

        // Top Keterlambatan: Akumulasi menit terlambat terbanyak
        $topTerlambat = $karyawanDenganCatatan
            ->filter(fn ($k) => ($k->akumulasi_keterlambatan_menit ?? 0) > 0 || $k->total_terlambat > 0)
            ->sortByDesc('akumulasi_keterlambatan_menit')
            ->take(5)
            ->values();

        // Top Alpa: Sering tidak hadir tanpa keterangan
        $topAlpa = $karyawanDenganCatatan
            ->filter(fn ($k) => $k->total_alpa > 0)
            ->sortByDesc('total_alpa')
            ->take(5)
            ->values();

        // 5. Data Chart (Chart.js) - Hanya Hadir, Terlambat, dan Tidak Hadir
        $chartData = [
            'labels'     => $breakdownBulanan->pluck('nama_bulan')->toArray(),
            'hadir'      => $breakdownBulanan->pluck('sum_hadir')->toArray(),
            'terlambat'  => $breakdownBulanan->pluck('sum_terlambat')->toArray(),
            'tidakHadir' => $breakdownBulanan->map(fn ($b) => $b->sum_izin + $b->sum_sakit + $b->sum_cuti + $b->sum_alpa)->toArray(),
            'donutData'  => [
                $totalHadir,
                $totalTerlambat,
                $totalIzin,
                $totalSakit,
                $totalCuti,
                $totalAlpa,
            ],
        ];

        return [
            'tahunAjaran'      => $tahunAjaran,
            'daftarBulan'      => $daftarBulan,
            'breakdownBulanan' => $breakdownBulanan,
            'kpiTahunan'       => $kpiTahunan,
            'rekapPegawai'     => $rekapPegawai,
            'topDisiplin'      => $topDisiplin,
            'topTerlambat'     => $topTerlambat,
            'topAlpa'          => $topAlpa,
            'chartData'        => $chartData,
        ];
    }

    protected function emptyKpi(): array
    {
        return [
            'total_log'                => 0,
            'total_hadir'              => 0,
            'total_terlambat'          => 0,
            'total_terlambat_izin'     => 0,
            'total_menit_terlambat'    => 0,
            'total_tugas'              => 0,
            'total_izin'               => 0,
            'total_sakit'              => 0,
            'total_cuti'               => 0,
            'total_alpa'               => 0,
            'total_pulang_cepat'       => 0,
            'total_menit_pulang_cepat' => 0,
            'total_hadir_efektif'      => 0,
            'total_tidak_hadir'        => 0,
            'persen_kehadiran'         => 0,
        ];
    }

    /**
     * Menentukan ID Bulan secara otomatis berdasarkan aturan Tutup Buku tanggal 28.
     * Tanggal 1 s/d 28 = Bulan tersebut.
     * Tanggal 29 s/d akhir bulan = Masuk ke Bulan berikutnya.
     */
    public function determineBulanId(string $tanggal): ?int
    {
        $date = Carbon::parse($tanggal);

        // Jika tanggal > 28, masuk periode bulan berikutnya
        $targetDate = $date->day > 28 ? $date->copy()->addMonthNoOverflow() : $date;
        $namaBulanIndo = $this->getNamaBulanIndonesia($targetDate->month);

        // Cari di database master bulan
        $bulan = Bulan::where('nama_bulan', 'LIKE', "%{$namaBulanIndo}%")->first();

        // Fallback ke bulan aktif jika tidak ditemukan
        if (! $bulan) {
            $bulan = Bulan::where('status', true)->first();
        }

        return $bulan?->id;
    }

    /**
     * Logika internal kalkulasi keterlambatan dan kepulangan lebih awal.
     */
    protected function calculateLateAndEarly(
        $jamKerja,
        string $tanggal,
        ?string $jamMasuk,
        ?string $jamPulang,
        string $currentStatus
    ): array {
        $keterlambatanMenit = 0;
        $pulangCepatMenit = 0;
        $status = $currentStatus;

        // Jika status bukan hadir/terlambat (misal izin/sakit/cuti/alpa), tidak perlu kalkulasi menit jam
        if (! in_array($currentStatus, ['hadir', 'terlambat'])) {
            return [
                'status'              => $currentStatus,
                'keterlambatan_menit' => 0,
                'pulang_cepat_menit'  => 0,
            ];
        }

        if ($jamKerja && $jamMasuk) {
            $waktuMasuk = Carbon::parse($tanggal . ' ' . $jamMasuk);
            $jadwalMasuk = Carbon::parse($tanggal . ' ' . $jamKerja->jam_masuk);

            if ($waktuMasuk->greaterThan($jadwalMasuk)) {
                $keterlambatanMenit = max(0, abs((int) $waktuMasuk->diffInMinutes($jadwalMasuk)));
                $status = 'terlambat';
            } else {
                $status = 'hadir';
            }
        }

        if ($jamKerja && $jamPulang) {
            $isFriday = Carbon::parse($tanggal)->isFriday();
            $jadwalPulangStr = $isFriday ? $jamKerja->jam_pulang_jumat : $jamKerja->jam_pulang_senin_kamis;

            if ($jadwalPulangStr) {
                $waktuPulang = Carbon::parse($tanggal . ' ' . $jamPulang);
                $jadwalPulang = Carbon::parse($tanggal . ' ' . $jadwalPulangStr);

                if ($waktuPulang->lessThan($jadwalPulang)) {
                    $pulangCepatMenit = max(0, abs((int) $jadwalPulang->diffInMinutes($waktuPulang)));
                }
            }
        }

        return [
            'status'              => $status,
            'keterlambatan_menit' => max(0, abs((int) $keterlambatanMenit)),
            'pulang_cepat_menit'  => max(0, abs((int) $pulangCepatMenit)),
        ];
    }

    /**
     * Mengambil matriks kehadiran harian (Grid View Tanggal x Pegawai)
     * untuk monitoring kehadiran dan mendeteksi pegawai yang belum absen.
     */
    public function getMatriksBulanan(int $bulanId, ?int $kategoriId = null, bool $onlyWorkdays = true): array
    {
        $bulan = Bulan::with('tahunAjaran')->findOrFail($bulanId);

        // Tentukan periode tanggal mulai dan selesai
        [$startDate, $endDate] = $this->getPeriodeTanggalBulan($bulan);

        // Ambil data tanggal-tanggal yang memiliki catatan presensi di database untuk bulan ini
        $presensiDatesMap = Presensi::where('kepegawaian_master_bulan_id', $bulan->id)
            ->selectRaw('DISTINCT DATE(tanggal) as tgl')
            ->pluck('tgl')
            ->map(fn ($d) => Carbon::parse($d)->format('Y-m-d'))
            ->flip()
            ->all();

        // Bangun koleksi tanggal (Kolom Matriks)
        $daftarTanggal = collect();
        $cursor = $startDate->copy()->startOfDay();
        $today = Carbon::today();

        while ($cursor->lte($endDate)) {
            $isWeekend = $cursor->isWeekend(); // Sabtu (6) atau Minggu (0)
            $dateStr = $cursor->format('Y-m-d');
            $hasPresensi = isset($presensiDatesMap[$dateStr]);

            // Jika onlyWorkdays aktif, skip weekend KECUALI jika ada presensi yang tercatat di tanggal itu
            if (! $onlyWorkdays || ! $isWeekend || $hasPresensi) {
                $isPast = $cursor->lessThan($today);
                $isToday = $cursor->isSameDay($today);
                $isFuture = $cursor->greaterThan($today);

                $daftarTanggal->push([
                    'date'        => $cursor->copy(),
                    'date_string' => $dateStr,
                    'day'         => $cursor->format('d'),
                    'month'       => $cursor->format('m'),
                    'formatted'   => $cursor->format('d/m'),
                    'day_name'    => $this->getNamaHariIndonesia($cursor->dayOfWeekIso),
                    'day_short'   => $this->getNamaHariSingkatIndonesia($cursor->dayOfWeekIso),
                    'is_weekend'  => $isWeekend,
                    'is_today'    => $isToday,
                    'is_past'     => $isPast,
                    'is_future'   => $isFuture,
                    'is_workday'  => ! $isWeekend,
                ]);
            }

            $cursor->addDay();
        }

        // Ambil seluruh karyawan
        $karyawanList = Karyawan::query()
            ->select('nipy', 'nama_lengkap', 'gelar', 'kepegawaian_master_kategori_id', 'unit')
            ->with([
                'kategori:id,nama_kategori',
                'user:id,no_induk,is_active',
            ])
            ->when($kategoriId, fn ($q) => $q->where('kepegawaian_master_kategori_id', $kategoriId))
            ->orderBy('nama_lengkap')
            ->get();

        // Ambil seluruh record presensi untuk bulan ini
        $presensiList = Presensi::query()
            ->where('kepegawaian_master_bulan_id', $bulan->id)
            ->get()
            ->groupBy(function ($item) {
                $tgl = Carbon::parse($item->tanggal)->format('Y-m-d');
                return $item->karyawan_nipy . '_' . $tgl;
            });

        // Inisialisasi statistik kolom per tanggal
        $columnTotals = [];
        foreach ($daftarTanggal as $tgl) {
            $columnTotals[$tgl['date_string']] = [
                'hadir'       => 0,
                'terlambat'   => 0,
                'izin'        => 0,
                'sakit'       => 0,
                'cuti'        => 0,
                'tugas'       => 0,
                'alpa'        => 0,
                'belum_absen' => 0,
            ];
        }

        $totalPegawaiBelumAbsenHariIni = 0;
        $todayDateStr = $today->format('Y-m-d');

        // Bangun baris data per pegawai
        $pegawaiRows = $karyawanList->map(function ($karyawan) use (
            $daftarTanggal,
            $presensiList,
            &$columnTotals,
            &$totalPegawaiBelumAbsenHariIni,
            $todayDateStr
        ) {
            $records = [];
            $totals = [
                'hadir'              => 0,
                'terlambat'          => 0,
                'izin'               => 0,
                'sakit'              => 0,
                'cuti'               => 0,
                'tugas'              => 0,
                'alpa'               => 0,
                'belum_absen'        => 0,
                'total_hadir_semua'  => 0,
                'total_tercatat'     => 0,
                'persen_kehadiran'   => 0,
            ];

            $belumAbsenHariIni = false;

            foreach ($daftarTanggal as $tgl) {
                $dateStr = $tgl['date_string'];
                $key = $karyawan->nipy . '_' . $dateStr;
                $presensi = $presensiList->get($key)?->first();

                if ($presensi) {
                    $status = strtolower($presensi->status);
                    if (isset($totals[$status])) {
                        $totals[$status]++;
                    }
                    if (in_array($status, ['hadir', 'terlambat'])) {
                        $totals['total_hadir_semua']++;
                    }
                    $totals['total_tercatat']++;

                    if (isset($columnTotals[$dateStr][$status])) {
                        $columnTotals[$dateStr][$status]++;
                    }

                    $records[$dateStr] = [
                        'status'              => $status,
                        'symbol'              => $this->getSymbolForStatus($status),
                        'jam_masuk'           => $presensi->jam_masuk ? substr($presensi->jam_masuk, 0, 5) : null,
                        'jam_pulang'          => $presensi->jam_pulang ? substr($presensi->jam_pulang, 0, 5) : null,
                        'keterlambatan_menit' => $presensi->keterlambatan_menit,
                        'catatan'             => $presensi->catatan,
                        'is_recorded'         => true,
                        'is_unrecorded'       => false,
                    ];
                } else {
                    // Cek apakah tanggal ini hari kerja dan sudah terlewati atau hari ini
                    $isUnrecorded = ($tgl['is_past'] || $tgl['is_today']) && $tgl['is_workday'];

                    if ($isUnrecorded) {
                        $totals['belum_absen']++;
                        $columnTotals[$dateStr]['belum_absen']++;

                        if ($dateStr === $todayDateStr) {
                            $belumAbsenHariIni = true;
                        }
                    }

                    $records[$dateStr] = [
                        'status'              => $isUnrecorded ? 'belum_absen' : 'empty',
                        'symbol'              => $isUnrecorded ? '-' : '',
                        'jam_masuk'           => null,
                        'jam_pulang'          => null,
                        'keterlambatan_menit' => 0,
                        'catatan'             => null,
                        'is_recorded'         => false,
                        'is_unrecorded'       => $isUnrecorded,
                    ];
                }
            }

            if ($belumAbsenHariIni) {
                $totalPegawaiBelumAbsenHariIni++;
            }

            $totalHariEfektif = $totals['total_hadir_semua'] + $totals['izin'] + $totals['sakit'] + $totals['cuti'] + $totals['alpa'] + $totals['belum_absen'];
            $totals['persen_kehadiran'] = $totalHariEfektif > 0
                ? round(($totals['total_hadir_semua'] / $totalHariEfektif) * 100, 1)
                : 0;

            return [
                'karyawan'          => $karyawan,
                'status_pegawai'    => $karyawan->status_pegawai,
                'user_is_active'    => (bool) ($karyawan->user?->is_active ?? false),
                'belum_absen_hari_ini' => $belumAbsenHariIni,
                'records'           => $records,
                'totals'            => $totals,
            ];
        });

        return [
            'bulan'          => $bulan,
            'daftarTanggal'  => $daftarTanggal,
            'pegawaiRows'    => $pegawaiRows,
            'columnTotals'   => $columnTotals,
            'summary'        => [
                'total_pegawai'                => $karyawanList->count(),
                'total_pegawai_aktif'          => $karyawanList->filter(fn ($k) => (bool) ($k->user?->is_active ?? false))->count(),
                'total_pegawai_belum_absen_hari_ini' => $totalPegawaiBelumAbsenHariIni,
                'start_date'                   => $startDate,
                'end_date'                     => $endDate,
                'total_hari_tampil'            => $daftarTanggal->count(),
            ],
        ];
    }

    /**
     * Menghitung rentang tanggal mulai dan selesai untuk Periode Master Bulan Yayasan (Tutup Buku tgl 28).
     * Disinkronkan dengan min/max presensi yang ada di database agar tidak ada catatan yang terlewat.
     */
    public function getPeriodeTanggalBulan(Bulan $bulan): array
    {
        $namaBulan = $bulan->nama_bulan;
        $targetMonth = null;
        $targetYear = null;

        $bulanMap = [
            'januari' => 1, 'februari' => 2, 'maret' => 3, 'april' => 4,
            'mei' => 5, 'juni' => 6, 'juli' => 7, 'agustus' => 8,
            'september' => 9, 'oktober' => 10, 'november' => 11, 'desember' => 12,
        ];

        foreach ($bulanMap as $nama => $nomor) {
            if (stripos($namaBulan, $nama) !== false) {
                $targetMonth = $nomor;
                break;
            }
        }

        if (preg_match('/\b(20\d{2})\b/', $namaBulan, $matches)) {
            $targetYear = (int) $matches[1];
        } elseif ($bulan->tahunAjaran && preg_match('/\b(20\d{2})\b/', $bulan->tahunAjaran->nama_tahun_ajaran, $yearMatches)) {
            $targetYear = (int) $yearMatches[1];
        }

        if (! $targetMonth) {
            $targetMonth = (int) date('m');
        }
        if (! $targetYear) {
            $targetYear = (int) date('Y');
        }

        // Tanggal standar cut-off 28:
        // Selesai: 28 targetMonth targetYear
        // Mulai: 29 targetMonth-1
        $standardEnd = Carbon::create($targetYear, $targetMonth, 28)->endOfDay();
        $standardStart = $standardEnd->copy()->subMonthNoOverflow()->day(29)->startOfDay();

        // Cek min dan max presensi yang terhubung dengan bulan ini agar presensi yang ada tidak terpotong
        $minPresensi = Presensi::where('kepegawaian_master_bulan_id', $bulan->id)->min('tanggal');
        $maxPresensi = Presensi::where('kepegawaian_master_bulan_id', $bulan->id)->max('tanggal');

        $startDate = $minPresensi ? min($standardStart, Carbon::parse($minPresensi)->startOfDay()) : $standardStart;
        $endDate = $maxPresensi ? max($standardEnd, Carbon::parse($maxPresensi)->endOfDay()) : $standardEnd;

        return [$startDate, $endDate];
    }

    /**
     * Simbol matriks presensi sesuai format standar.
     */
    protected function getSymbolForStatus(string $status): string
    {
        return match ($status) {
            'hadir', 'terlambat' => 'v',
            'izin'               => 'i',
            'sakit'              => 's',
            'cuti'               => 'c',
            'tugas'              => 't',
            'alpa'               => 'a',
            default              => '',
        };
    }

    protected function getNamaHariIndonesia(int $dayOfWeekIso): string
    {
        return match ($dayOfWeekIso) {
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
            default => '',
        };
    }

    protected function getNamaHariSingkatIndonesia(int $dayOfWeekIso): string
    {
        return match ($dayOfWeekIso) {
            1 => 'Sen',
            2 => 'Sel',
            3 => 'Rab',
            4 => 'Kam',
            5 => 'Jum',
            6 => 'Sab',
            7 => 'Min',
            default => '',
        };
    }

    protected function getNamaBulanIndonesia(int $monthNumber): string
    {
        return match ($monthNumber) {
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
            default => '',
        };
    }
}
