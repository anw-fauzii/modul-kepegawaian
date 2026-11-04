<?php

namespace App\Services;

use App\Models\Admin\Cuti\Cuti;
use App\Models\Admin\DataMaster\Bulan;
use App\Models\Admin\DataMaster\JamKerja;
use App\Models\Admin\DataMaster\Karyawan;
use App\Models\Admin\DataMaster\Kategori;
use App\Models\Admin\DataMaster\TahunAjaran;
use App\Models\Admin\Diklat\Diklat;
use App\Models\Admin\Fasilitas\Fasilitas;
use App\Models\Admin\Kegiatan\KegiatanLembaga;
use App\Models\Admin\Kegiatan\KehadiranKegiatan;
use App\Models\Admin\Penggajian\Penggajian;
use App\Models\Admin\Presensi\Presensi;
use App\Models\Admin\RapotKinerja\RapotKinerja;
use App\Models\Admin\SertifikatPenghargaan\SertifikatPenghargaan;
use App\Models\Admin\SuratKeputusan\SuratKeputusan;
use App\Models\Admin\Tupoksi\Tupoksi;
use App\Models\User;
use App\Services\Admin\Presensi\PresensiService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function __construct(
        protected PresensiService $presensiService
    ) {}

    /**
     * Mengambil dan memproses seluruh data analitik untuk Admin Dashboard utama.
     */
    public function getAdminDashboardData(): array
    {
        $today = Carbon::today()->format('Y-m-d');

        // 1. STATISTIK DATA KARYAWAN (1 Query Agregasi Bersatu Cepat - Anti Overhead)
        $karyawanAggregates = Karyawan::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN jk = 'L' THEN 1 ELSE 0 END) as laki,
            SUM(CASE WHEN jk = 'P' THEN 1 ELSE 0 END) as perempuan,
            SUM(CASE WHEN sertifikasi = 1 THEN 1 ELSE 0 END) as sertifikasi,
            SUM(CASE WHEN nipy LIKE '%-1' OR nipy LIKE '%-01' OR nipy LIKE '%01' THEN 1 ELSE 0 END) as tetap,
            SUM(CASE WHEN nipy LIKE '%-2' OR nipy LIKE '%-02' OR nipy LIKE '%02' THEN 1 ELSE 0 END) as kontrak,
            SUM(CASE WHEN nipy LIKE '%-3' OR nipy LIKE '%-03' OR nipy LIKE '%03' THEN 1 ELSE 0 END) as honor
        ")->first();

        $totalKaryawan = (int) ($karyawanAggregates->total ?? 0);
        $totalLaki = (int) ($karyawanAggregates->laki ?? 0);
        $totalPerempuan = (int) ($karyawanAggregates->perempuan ?? 0);
        $totalSertifikasi = (int) ($karyawanAggregates->sertifikasi ?? 0);

        $statusPegawaiStats = [
            'tetap'   => (int) ($karyawanAggregates->tetap ?? 0),
            'kontrak' => (int) ($karyawanAggregates->kontrak ?? 0),
            'honor'   => (int) ($karyawanAggregates->honor ?? 0),
        ];
        $statusPegawaiStats['lainnya'] = max(0, $totalKaryawan - array_sum($statusPegawaiStats));

        // 2. STATISTIK PRESENSI HARI INI
        $todayPresensi = Presensi::whereDate('tanggal', $today)->get();
        $todayHadir = $todayPresensi->where('status', 'hadir')->count();
        $todayTerlambat = $todayPresensi->where('status', 'terlambat')->count();
        $todayTerlambatIzin = $todayPresensi->where('status', 'terlambat')->where('izin_terlambat', true)->count();
        $todayTugas = $todayPresensi->where('status', 'tugas')->count();
        $todayIzin = $todayPresensi->where('status', 'izin')->count();
        $todaySakit = $todayPresensi->where('status', 'sakit')->count();
        $todayCuti = $todayPresensi->where('status', 'cuti')->count();
        $todayAlpa = $todayPresensi->where('status', 'alpa')->count();
        $todayShaum = $todayPresensi->where('shaum', true)->count();

        $todayTotalTercatat = $todayPresensi->where('status', '!=', 'libur')->count();
        $todayTotalHadirSemua = $todayHadir + $todayTerlambat;
        $todayTingkatKehadiran = $todayTotalTercatat > 0
            ? round(($todayTotalHadirSemua / $todayTotalTercatat) * 100, 1)
            : 0;

        // Presensi user yang sedang login hari ini
        $user = auth()->user();
        $userNipy = $user?->no_induk;
        $myPresensiToday = null;
        if ($userNipy) {
            $myPresensiToday = Presensi::where('karyawan_nipy', $userNipy)
                ->whereDate('tanggal', $today)
                ->first();
        }

        // Feed presensi terbaru & keterlambatan hari ini (Pencegahan N+1 dengan Eager Loading: karyawan.kategori & karyawan.user)
        $latestPresensiToday = Presensi::with(['karyawan.kategori', 'karyawan.user'])
            ->whereDate('tanggal', $today)
            ->latest('jam_masuk')
            ->take(6)
            ->get();

        $latePresensiToday = Presensi::with(['karyawan.kategori', 'karyawan.user'])
            ->whereDate('tanggal', $today)
            ->where('status', 'terlambat')
            ->orderByDesc('keterlambatan_menit')
            ->take(6)
            ->get();

        // 3. TREN PRESENSI 7 HARI TERAKHIR (1 Query Agregat Cepat)
        $tren7Hari = $this->getTrenPresensi7Hari($today);

        // 4. DISTRIBUSI PEGAWAI PER KATEGORI (TOP KATEGORI)
        $kategoriDistribution = Kategori::withCount('karyawan')
            ->having('karyawan_count', '>', 0)
            ->orderByDesc('karyawan_count')
            ->get();

        // 5. MASTER OPERASIONAL
        $activeBulan = Bulan::with('tahunAjaran')->where('status', true)->orderByDesc('id')->first();
        $activeTahunAjaran = TahunAjaran::where('is_active', true)->first();
        $jamKerjaList = JamKerja::all();

        // 6. MODUL ARSIP & KEGIATAN
        $modulStats = [
            'kegiatan'    => KegiatanLembaga::count(),
            'fasilitas'   => Fasilitas::count(),
            'diklat'      => Diklat::count(),
            'rapot'       => RapotKinerja::count(),
            'sk'          => SuratKeputusan::count(),
            'penghargaan' => SertifikatPenghargaan::count(),
            'tupoksi'     => Tupoksi::count(),
        ];
        $fasilitasBelumKembali = Fasilitas::where('status', false)->count();

        // Kegiatan hari ini dengan eager-loading count kehadiran (Anti N+1)
        $kegiatanHariIni = KegiatanLembaga::withCount('kehadiran')
            ->whereDate('tanggal', $today)
            ->orderBy('jam_mulai')
            ->take(5)
            ->get();

        $kegiatanAktifCount = KegiatanLembaga::where('is_active', true)->count();
        $sertifikasiPersen = $totalKaryawan > 0 ? round(($totalSertifikasi / $totalKaryawan) * 100) : 0;

        return compact(
            'totalKaryawan',
            'totalLaki',
            'totalPerempuan',
            'totalSertifikasi',
            'statusPegawaiStats',
            'today',
            'todayHadir',
            'todayTerlambat',
            'todayTerlambatIzin',
            'todayTugas',
            'todayIzin',
            'todaySakit',
            'todayCuti',
            'todayAlpa',
            'todayShaum',
            'myPresensiToday',
            'todayTotalTercatat',
            'todayTotalHadirSemua',
            'todayTingkatKehadiran',
            'latestPresensiToday',
            'latePresensiToday',
            'tren7Hari',
            'kategoriDistribution',
            'activeBulan',
            'activeTahunAjaran',
            'jamKerjaList',
            'modulStats',
            'fasilitasBelumKembali',
            'kegiatanHariIni',
            'kegiatanAktifCount',
            'sertifikasiPersen'
        );
    }

    /**
     * Mengambil dan memproses data dashboard personal untuk User/Pegawai mandiri.
     */
    public function getUserDashboardData(?User $user): array
    {
        $userNipy = trim((string) ($user?->no_induk ?? ''));

        // Eager load relasi karyawan untuk mencegah N+1
        $karyawan = null;
        $isPreview = false;

        if ($userNipy !== '') {
            $karyawan = Karyawan::with(['kategori', 'jamKerja', 'user'])
                ->where('nipy', $userNipy)
                ->orWhere('nipy', 'like', $userNipy . '-%')
                ->first();
        }

        // Preview fallback jika akun admin belum tertaut ke NIPY karyawan
        if (! $karyawan) {
            $karyawan = Karyawan::with(['kategori', 'jamKerja', 'user'])->first();
            $isPreview = true;
        }

        $today = Carbon::today()->format('Y-m-d');
        $activeTahunAjaran = TahunAjaran::where('is_active', true)->first();

        // 1. Presensi Hari Ini
        $myPresensiToday = null;
        if ($karyawan) {
            $myPresensiToday = Presensi::where('karyawan_nipy', $karyawan->nipy)
                ->whereDate('tanggal', $today)
                ->first();
        }

        // Resolusi Bulan Aktif
        $determinedBulanId = $this->presensiService->determineBulanId($today);

        $targetBulanId = $myPresensiToday?->kepegawaian_master_bulan_id
            ?? ($determinedBulanId && $karyawan && Presensi::where('karyawan_nipy', $karyawan->nipy)->where('kepegawaian_master_bulan_id', $determinedBulanId)->exists()
                ? $determinedBulanId
                : null)
            ?? ($karyawan
                ? Presensi::where('karyawan_nipy', $karyawan->nipy)
                    ->whereNotNull('kepegawaian_master_bulan_id')
                    ->latest('tanggal')
                    ->value('kepegawaian_master_bulan_id')
                : null)
            ?? $determinedBulanId;

        $activeBulan = $targetBulanId
            ? Bulan::with('tahunAjaran')->find($targetBulanId)
            : Bulan::with('tahunAjaran')->where('status', true)->orderByDesc('id')->first();

        // Syarat konfirmasi shaum
        $hasAbsenToday = $myPresensiToday !== null
            && (! empty($myPresensiToday->jam_masuk) || in_array($myPresensiToday->status, ['hadir', 'terlambat', 'tugas']));

        $todayShaumCount = Presensi::whereDate('tanggal', $today)->where('shaum', true)->count();

        // 2. Jam Kerja Operasional Karyawan
        $jamKerja = $karyawan?->jamKerja ?? JamKerja::first();
        $jamMasukNormal = $jamKerja?->jam_masuk ? substr($jamKerja->jam_masuk, 0, 5) : '07:00';
        $isJumat = Carbon::today()->isFriday();
        $jamPulangNormal = $isJumat
            ? ($jamKerja?->jam_pulang_jumat ? substr($jamKerja->jam_pulang_jumat, 0, 5) : '11:30')
            : ($jamKerja?->jam_pulang_senin_kamis ? substr($jamKerja->jam_pulang_senin_kamis, 0, 5) : '15:00');

        try {
            $jamToleransi = Carbon::createFromFormat('H:i', $jamMasukNormal)->addMinutes(15)->format('H:i');
        } catch (\Exception $e) {
            $jamToleransi = '07:15';
        }

        // 3. Statistik Presensi Pribadi Bulan Ini & Akumulasi Shaum
        $myMonthlyStats = $this->getMyMonthlyPresensiStats($karyawan, $activeBulan);

        // 4. Log Riwayat Presensi 7 Hari Terakhir (Eager load jamKerja untuk cegah N+1)
        $myRecentPresensi = Presensi::with('jamKerja')
            ->where('karyawan_nipy', $karyawan?->nipy)
            ->latest('tanggal')
            ->take(7)
            ->get();

        // 5. Kegiatan Lembaga Terjadwal & Status Kehadiran (Eager lookup dengan whereIn & keyBy untuk cegah N+1)
        $upcomingKegiatan = KegiatanLembaga::whereDate('tanggal', '>=', $today)
            ->orderBy('tanggal', 'asc')
            ->orderBy('jam_mulai', 'asc')
            ->take(5)
            ->get();

        $myKehadiranKegiatan = KehadiranKegiatan::where('karyawan_nipy', $karyawan?->nipy)
            ->whereIn('kepegawaian_kegiatan_lembaga_id', $upcomingKegiatan->pluck('id'))
            ->get()
            ->keyBy('kepegawaian_kegiatan_lembaga_id');

        // 6. Ringkasan Jumlah Dokumen Portofolio Pegawai
        $karyawanNipy = $karyawan?->nipy;
        $docCounts = [
            'gaji'        => $karyawanNipy ? Penggajian::where('karyawan_nipy', $karyawanNipy)->count() : 0,
            'sk'          => $karyawanNipy ? SuratKeputusan::where('karyawan_nipy', $karyawanNipy)->count() : 0,
            'rapot'       => $karyawanNipy ? RapotKinerja::where('karyawan_nipy', $karyawanNipy)->count() : 0,
            'tupoksi'     => $karyawanNipy ? Tupoksi::where('karyawan_nipy', $karyawanNipy)->count() : 0,
            'fasilitas'   => $karyawanNipy ? Fasilitas::where('karyawan_nipy', $karyawanNipy)->count() : 0,
            'diklat'      => $karyawanNipy ? Diklat::where('karyawan_nipy', $karyawanNipy)->count() : 0,
            'penghargaan' => $karyawanNipy ? SertifikatPenghargaan::where('karyawan_nipy', $karyawanNipy)->count() : 0,
            'cuti'        => $karyawanNipy ? Cuti::where('karyawan_nipy', $karyawanNipy)->count() : 0,
        ];
        $fasilitasDipinjamCount = $karyawanNipy ? Fasilitas::where('karyawan_nipy', $karyawanNipy)->where('status', false)->count() : 0;

        return array_merge([
            'user'                   => $user,
            'karyawan'               => $karyawan,
            'isPreview'              => $isPreview,
            'today'                  => $today,
            'activeBulan'            => $activeBulan,
            'activeTahunAjaran'      => $activeTahunAjaran,
            'myPresensiToday'        => $myPresensiToday,
            'hasAbsenToday'          => $hasAbsenToday,
            'todayShaumCount'        => $todayShaumCount,
            'jamKerja'               => $jamKerja,
            'jamMasukNormal'         => $jamMasukNormal,
            'jamPulangNormal'        => $jamPulangNormal,
            'jamToleransi'           => $jamToleransi,
            'myRecentPresensi'       => $myRecentPresensi,
            'upcomingKegiatan'       => $upcomingKegiatan,
            'myKehadiranKegiatan'    => $myKehadiranKegiatan,
            'docCounts'              => $docCounts,
            'fasilitasDipinjamCount' => $fasilitasDipinjamCount,
        ], $myMonthlyStats);
    }

    /**
     * Menghitung tren presensi 7 hari terakhir dalam 1 query agregat cepat.
     */
    protected function getTrenPresensi7Hari(string $today): Collection
    {
        $startDate = Carbon::today()->subDays(6)->format('Y-m-d');
        $endDate = $today;

        $rawPresensi7Days = Presensi::select(
            DB::raw('DATE(tanggal) as tgl'),
            'status',
            DB::raw('COUNT(*) as total')
        )
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(tanggal)'), 'status')
            ->get();

        $tren7Hari = collect();
        for ($i = 6; $i >= 0; $i--) {
            $tgl = Carbon::today()->subDays($i);
            $tglStr = $tgl->format('Y-m-d');

            $records = $rawPresensi7Days->where('tgl', $tglStr);
            $hadir = (int) ($records->where('status', 'hadir')->sum('total'));
            $terlambat = (int) ($records->where('status', 'terlambat')->sum('total'));
            $tugas = (int) ($records->where('status', 'tugas')->sum('total'));
            $tidakHadir = (int) ($records->whereIn('status', ['izin', 'sakit', 'cuti', 'alpa'])->sum('total'));

            $tren7Hari->push([
                'label'      => $tgl->translatedFormat('D, d M'),
                'hadir'      => $hadir,
                'terlambat'  => $terlambat,
                'tugas'      => $tugas,
                'tidakHadir' => $tidakHadir,
            ]);
        }

        return $tren7Hari;
    }

    /**
     * Menghitung statistik presensi bulanan pegawai mandiri.
     */
    protected function getMyMonthlyPresensiStats(?Karyawan $karyawan, ?Bulan $activeBulan): array
    {
        if (! $karyawan) {
            return [
                'myHadir'          => 0,
                'myTerlambat'      => 0,
                'myTerlambatMenit' => 0,
                'myTerlambatIzin'  => 0,
                'myTugas'          => 0,
                'myIzin'           => 0,
                'mySakit'          => 0,
                'myCuti'           => 0,
                'myAlpa'           => 0,
                'myShaumDays'      => 0,
                'myShaumBulanIni'  => 0,
                'myTotalShaum'     => 0,
                'totalMasuk'       => 0,
                'totalTercatat'    => 0,
                'myAttendanceRate' => 100,
            ];
        }

        $myPresensiMonthQuery = Presensi::where('karyawan_nipy', $karyawan->nipy);
        if ($activeBulan) {
            [$periodeStart, $periodeEnd] = $this->presensiService->getPeriodeTanggalBulan($activeBulan);
            $myPresensiMonthQuery->where(function ($q) use ($activeBulan, $periodeStart, $periodeEnd) {
                $q->where('kepegawaian_master_bulan_id', $activeBulan->id)
                    ->orWhereBetween('tanggal', [$periodeStart->toDateString(), $periodeEnd->toDateString()]);
            });
        } else {
            $myPresensiMonthQuery->whereMonth('tanggal', Carbon::today()->month)
                ->whereYear('tanggal', Carbon::today()->year);
        }

        $myMonthlyRecords = $myPresensiMonthQuery->get();

        $myHadir = $myMonthlyRecords->where('status', 'hadir')->count();
        $myTerlambat = $myMonthlyRecords->where('status', 'terlambat')->count();
        $myTerlambatMenit = (int) $myMonthlyRecords->where('status', 'terlambat')->sum('keterlambatan_menit');
        $myTerlambatIzin = $myMonthlyRecords->where('status', 'terlambat')->where('izin_terlambat', true)->count();
        $myTugas = $myMonthlyRecords->where('status', 'tugas')->count();
        $myIzin = $myMonthlyRecords->where('status', 'izin')->count();
        $mySakit = $myMonthlyRecords->where('status', 'sakit')->count();
        $myCuti = $myMonthlyRecords->where('status', 'cuti')->count();
        $myAlpa = $myMonthlyRecords->where('status', 'alpa')->count();

        $myShaumBulanIni = $myMonthlyRecords->filter(fn ($r) => (bool) $r->shaum)->count();
        $myTotalShaum = Presensi::where('karyawan_nipy', $karyawan->nipy)->where('shaum', true)->count();
        $myShaumDays = max($myShaumBulanIni, $myTotalShaum);

        $totalMasuk = $myHadir + $myTerlambat + $myTugas;
        $totalTercatat = $myMonthlyRecords->where('status', '!=', 'libur')->count();
        $myAttendanceRate = $totalTercatat > 0 ? round(($totalMasuk / $totalTercatat) * 100, 1) : 100;

        return compact(
            'myHadir',
            'myTerlambat',
            'myTerlambatMenit',
            'myTerlambatIzin',
            'myTugas',
            'myIzin',
            'mySakit',
            'myCuti',
            'myAlpa',
            'myShaumDays',
            'myShaumBulanIni',
            'myTotalShaum',
            'totalMasuk',
            'totalTercatat',
            'myAttendanceRate'
        );
    }
}

