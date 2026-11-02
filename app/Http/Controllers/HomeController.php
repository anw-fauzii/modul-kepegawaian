<?php

namespace App\Http\Controllers;

use App\Models\Admin\DataMaster\Bulan;
use App\Models\Admin\DataMaster\JamKerja;
use App\Models\Admin\DataMaster\Karyawan;
use App\Models\Admin\DataMaster\Kategori;
use App\Models\Admin\DataMaster\TahunAjaran;
use App\Models\Admin\Presensi\Presensi;
use App\Models\Admin\Diklat\Diklat;
use App\Models\Admin\Fasilitas\Fasilitas;
use App\Models\Admin\Kegiatan\KegiatanLembaga;
use App\Models\Admin\Kegiatan\KehadiranKegiatan;
use App\Models\Admin\Penggajian\Penggajian;
use App\Models\Admin\RapotKinerja\RapotKinerja;
use App\Models\Admin\SertifikatPenghargaan\SertifikatPenghargaan;
use App\Models\Admin\SuratKeputusan\SuratKeputusan;
use App\Models\Admin\Tupoksi\Tupoksi;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Menampilkan dashboard utama kepegawaian dengan ringkasan analitik real-time.
     */
    public function index(Request $request): Renderable
    {
        if ($request->query('view') === 'user') {
            return $this->userDashboard($request);
        }

        $today = Carbon::today()->format('Y-m-d');

        // 1. STATISTIK DATA KARYAWAN
        $totalKaryawan = Karyawan::count();
        $totalLaki = Karyawan::where('jk', 'L')->count();
        $totalPerempuan = Karyawan::where('jk', 'P')->count();
        $totalSertifikasi = Karyawan::where('sertifikasi', true)->count();

        // Status Pegawai (Tetap, Kontrak, Honor) berdasarkan suffix NIPY
        $statusPegawaiStats = [
            'tetap'   => Karyawan::where(function ($q) {
                $q->where('nipy', 'LIKE', '%-1')->orWhere('nipy', 'LIKE', '%-01')->orWhere('nipy', 'LIKE', '%01');
            })->count(),
            'kontrak' => Karyawan::where(function ($q) {
                $q->where('nipy', 'LIKE', '%-2')->orWhere('nipy', 'LIKE', '%-02')->orWhere('nipy', 'LIKE', '%02');
            })->count(),
            'honor'   => Karyawan::where(function ($q) {
                $q->where('nipy', 'LIKE', '%-3')->orWhere('nipy', 'LIKE', '%-03')->orWhere('nipy', 'LIKE', '%03');
            })->count(),
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

        $todayTotalTercatat = $todayPresensi->count();
        $todayTotalHadirSemua = $todayHadir + $todayTerlambat;
        $todayTingkatKehadiran = $todayTotalTercatat > 0
            ? round(($todayTotalHadirSemua / $todayTotalTercatat) * 100, 1)
            : 0;

        // Presensi user yang sedang login hari ini (untuk status shaum)
        $userNipy = auth()->user()?->no_induk ?? auth()->user()?->karyawan?->nipy;
        $myPresensiToday = null;
        if ($userNipy) {
            $myPresensiToday = Presensi::where('karyawan_nipy', $userNipy)
                ->whereDate('tanggal', $today)
                ->first();
        }

        // Feed presensi terbaru hari ini (maksimal 6 data)
        $latestPresensiToday = Presensi::with(['karyawan.kategori'])
            ->whereDate('tanggal', $today)
            ->latest('jam_masuk')
            ->take(6)
            ->get();

        // Karyawan terlambat hari ini (maksimal 6 data)
        $latePresensiToday = Presensi::with(['karyawan.kategori'])
            ->whereDate('tanggal', $today)
            ->where('status', 'terlambat')
            ->orderByDesc('keterlambatan_menit')
            ->take(6)
            ->get();

        // 3. TREN PRESENSI 7 HARI TERAKHIR (1 Query Agregat Cepat)
        $startDate = Carbon::today()->subDays(6)->format('Y-m-d');
        $endDate = Carbon::today()->format('Y-m-d');

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

        // 4. DISTRIBUSI PEGAWAI PER KATEGORI (TOP KATEGORI)
        $kategoriDistribution = Kategori::withCount('karyawan')
            ->having('karyawan_count', '>', 0)
            ->orderByDesc('karyawan_count')
            ->get();

        // 5. MASTER OPERASIONAL
        $activeBulan = Bulan::with('tahunAjaran')->where('status', true)->first();
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
        $kegiatanHariIni = KegiatanLembaga::withCount('kehadiran')
            ->whereDate('tanggal', $today)
            ->orderBy('jam_mulai')
            ->take(5)
            ->get();
        $kegiatanAktifCount = KegiatanLembaga::where('is_active', true)->count();
        $sertifikasiPersen = $totalKaryawan > 0 ? round(($totalSertifikasi / $totalKaryawan) * 100) : 0;

        return view('home', compact(
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
        ));
    }

    /**
     * Menampilkan dashboard mandiri personal untuk user / pegawai / guru.
     */
    public function userDashboard(Request $request): Renderable
    {
        $user = auth()->user();
        $userNipy = $user?->no_induk ?? $user?->karyawan?->nipy;

        $karyawan = null;
        $isPreview = false;

        if ($userNipy) {
            $karyawan = Karyawan::with(['kategori', 'jamKerja', 'user'])
                ->where('nipy', $userNipy)
                ->first();
        }

        // Jika user admin/akun testing belum tertaut dengan NIPY karyawan, ambil profil sampel sebagai preview
        if (! $karyawan) {
            $karyawan = Karyawan::with(['kategori', 'jamKerja', 'user'])->first();
            $isPreview = true;
        }

        $today = Carbon::today()->format('Y-m-d');
        $activeBulan = Bulan::with('tahunAjaran')->where('status', true)->first();
        $activeTahunAjaran = TahunAjaran::where('is_active', true)->first();

        // 1. Presensi Hari Ini
        $myPresensiToday = null;
        if ($karyawan && ! $isPreview) {
            $myPresensiToday = Presensi::where('karyawan_nipy', $karyawan->nipy)
                ->whereDate('tanggal', $today)
                ->first();
        }

        // Syarat konfirmasi shaum: Pegawai harus SUDAH melakukan presensi harian
        $hasAbsenToday = ($myPresensiToday !== null && (!empty($myPresensiToday->jam_masuk) || in_array($myPresensiToday->status, ['hadir', 'terlambat', 'tugas']))) && ! $isPreview;

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

        // 3. Statistik Presensi Pribadi Bulan Ini
        $myPresensiMonthQuery = Presensi::where('karyawan_nipy', $karyawan?->nipy);
        if ($activeBulan) {
            $myPresensiMonthQuery->where('kepegawaian_master_bulan_id', $activeBulan->id);
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
        $myShaumDays = $myMonthlyRecords->where('shaum', true)->count();

        $totalMasuk = $myHadir + $myTerlambat + $myTugas;
        $totalTercatat = $myMonthlyRecords->count();
        $myAttendanceRate = $totalTercatat > 0 ? round(($totalMasuk / $totalTercatat) * 100, 1) : 100;

        // 4. Log Riwayat Presensi 7 Hari Terakhir
        $myRecentPresensi = Presensi::with('jamKerja')
            ->where('karyawan_nipy', $karyawan?->nipy)
            ->latest('tanggal')
            ->take(7)
            ->get();

        // 5. Kegiatan Lembaga Terjadwal & Status Kehadiran Saya
        $upcomingKegiatan = KegiatanLembaga::whereDate('tanggal', '>=', $today)
            ->orderBy('tanggal', 'asc')
            ->orderBy('jam_mulai', 'asc')
            ->take(5)
            ->get();

        $myKehadiranKegiatan = KehadiranKegiatan::where('karyawan_nipy', $karyawan?->nipy)
            ->whereIn('kepegawaian_kegiatan_lembaga_id', $upcomingKegiatan->pluck('id'))
            ->get()
            ->keyBy('kepegawaian_kegiatan_lembaga_id');

        // 6. Ringkasan Jumlah Dokumen & Portofolio Mandiri Pegawai (Count Only)
        $docCounts = [
            'gaji'        => Penggajian::where('karyawan_nipy', $karyawan?->nipy)->count(),
            'sk'          => SuratKeputusan::where('karyawan_nipy', $karyawan?->nipy)->count(),
            'rapot'       => RapotKinerja::where('karyawan_nipy', $karyawan?->nipy)->count(),
            'tupoksi'     => Tupoksi::where('karyawan_nipy', $karyawan?->nipy)->count(),
            'fasilitas'   => Fasilitas::where('karyawan_nipy', $karyawan?->nipy)->count(),
            'diklat'      => Diklat::where('karyawan_nipy', $karyawan?->nipy)->count(),
            'penghargaan' => SertifikatPenghargaan::where('karyawan_nipy', $karyawan?->nipy)->count(),
            'cuti'        => \App\Models\Admin\Cuti\Cuti::where('karyawan_nipy', $karyawan?->nipy)->count(),
        ];
        $fasilitasDipinjamCount = Fasilitas::where('karyawan_nipy', $karyawan?->nipy)->where('status', false)->count();

        return view('home_user', compact(
            'user',
            'karyawan',
            'isPreview',
            'today',
            'activeBulan',
            'activeTahunAjaran',
            'myPresensiToday',
            'hasAbsenToday',
            'todayShaumCount',
            'jamKerja',
            'jamMasukNormal',
            'jamPulangNormal',
            'jamToleransi',
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
            'totalMasuk',
            'totalTercatat',
            'myAttendanceRate',
            'myRecentPresensi',
            'upcomingKegiatan',
            'myKehadiranKegiatan',
            'docCounts',
            'fasilitasDipinjamCount'
        ));
    }
}
