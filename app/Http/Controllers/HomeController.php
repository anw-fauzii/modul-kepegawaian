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
use App\Models\Admin\RapotKinerja\RapotKinerja;
use App\Models\Admin\SertifikatPenghargaan\SertifikatPenghargaan;
use App\Models\Admin\SuratKeputusan\SuratKeputusan;
use App\Models\Admin\Tupoksi\Tupoksi;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
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
    public function index(): Renderable
    {
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

        $todayTotalTercatat = $todayPresensi->count();
        $todayTotalHadirSemua = $todayHadir + $todayTerlambat;
        $todayTingkatKehadiran = $todayTotalTercatat > 0
            ? round(($todayTotalHadirSemua / $todayTotalTercatat) * 100, 1)
            : 0;

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
}
