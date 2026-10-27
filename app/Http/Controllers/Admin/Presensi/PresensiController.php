<?php

namespace App\Http\Controllers\Admin\Presensi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Presensi\PresensiBulkRequest;
use App\Http\Requests\Admin\Presensi\PresensiFilterRequest;
use App\Http\Requests\Admin\Presensi\PresensiRequest;
use App\Http\Requests\Admin\Presensi\PresensiStatistikRequest;
use App\Models\Admin\DataMaster\Bulan;
use App\Models\Admin\DataMaster\Kategori;
use App\Models\Admin\DataMaster\Karyawan;
use App\Models\Admin\DataMaster\TahunAjaran;
use App\Models\Admin\Presensi\Presensi;
use App\Services\Admin\Presensi\PresensiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PresensiController extends Controller
{
    public function __construct(
        protected PresensiService $service
    ) {}

    /**
     * Menampilkan daftar riwayat presensi karyawan (terpaginasi & terfilter by Kategori & Bulan).
     * Data hanya dieksekusi query-nya ketika filter/pencarian dipilih agar performa tetap cepat.
     */
    public function index(PresensiFilterRequest $request): View
    {
        $filters = $request->validated();

        // Cek apakah ada filter atau kriteria pencarian yang aktif
        $hasFilter = $request->filled('kepegawaian_master_bulan_id')
            || $request->filled('tanggal_mulai')
            || $request->filled('tanggal_selesai')
            || $request->filled('kepegawaian_master_kategori_id')
            || $request->filled('status')
            || $request->filled('search');

        $activeBulan = Bulan::where('status', true)->first();

        if ($hasFilter) {
            $presensi = $this->service->getPaginated(
                filters: $filters,
                perPage: (int) $request->input('per_page', 15)
            );
        } else {
            // Gunakan paginator kosong agar tidak membebani query database saat pertama kali membuka menu
            $presensi = new \Illuminate\Pagination\LengthAwarePaginator(
                items: collect(),
                total: 0,
                perPage: (int) $request->input('per_page', 15),
                currentPage: 1,
                options: ['path' => $request->url(), 'query' => $request->query()]
            );
        }

        $kategoriList = Kategori::orderBy('nama_kategori')->get();
        $bulanList = Bulan::with('tahunAjaran')->orderByDesc('id')->get();

        return view('admin.presensi.index', [
            'presensi'     => $presensi,
            'kategoriList' => $kategoriList,
            'bulanList'    => $bulanList,
            'activeBulan'  => $activeBulan,
            'hasFilter'    => $hasFilter,
            'filters'      => $filters,
        ]);
    }

    /**
     * Menampilkan formulir input presensi BULK berdasarkan Kategori Karyawan.
     */
    public function create(Request $request): View
    {
        $kategoriList = Kategori::orderBy('nama_kategori')->get();
        $bulanList = Bulan::with('tahunAjaran')->orderByDesc('id')->get();
        $activeBulan = Bulan::where('status', true)->first();

        $selectedKategoriId = $request->input('kepegawaian_master_kategori_id');
        $selectedTanggal = $request->input('tanggal', date('Y-m-d'));
        $selectedBulanId = $request->input('kepegawaian_master_bulan_id', $activeBulan?->id);

        $karyawanList = $selectedKategoriId
            ? $this->service->getKaryawanForBulk((int) $selectedKategoriId, $selectedTanggal)
            : collect();

        return view('admin.presensi.create', [
            'kategoriList'       => $kategoriList,
            'bulanList'          => $bulanList,
            'activeBulan'        => $activeBulan,
            'selectedKategoriId' => $selectedKategoriId,
            'selectedTanggal'    => $selectedTanggal,
            'selectedBulanId'    => $selectedBulanId,
            'karyawanList'       => $karyawanList,
        ]);
    }

    /**
     * Menyimpan data presensi massal (Bulk) per kategori dalam 1 transaksi.
     */
    public function storeBulk(PresensiBulkRequest $request): RedirectResponse
    {
        $savedCount = $this->service->storeBulk($request->validated());

        return redirect()->route('presensi.index', [
            'kepegawaian_master_kategori_id' => $request->input('kepegawaian_master_kategori_id'),
            'tanggal_mulai'                  => $request->input('tanggal'),
            'tanggal_selesai'                => $request->input('tanggal'),
        ])->with('success', "Berhasil menyimpan {$savedCount} data presensi karyawan.");
    }

    /**
     * Menyimpan data presensi manual satuan (Single).
     */
    public function store(PresensiRequest $request): RedirectResponse
    {
        $presensi = $this->service->store($request->validated());

        return redirect()->route('presensi.index', [
            'tanggal_mulai'   => $request->input('tanggal'),
            'tanggal_selesai' => $request->input('tanggal'),
            'search'          => $request->input('karyawan_nipy'),
        ])->with('success', 'Data presensi berhasil disimpan.');
    }

    /**
     * Menampilkan formulir edit presensi satuan.
     */
    public function edit(Presensi $presensi): View
    {
        $presensi->loadMissing(['karyawan.kategori', 'karyawan.jamKerja']);
        $karyawanList = Karyawan::with('kategori')->select('nipy', 'nama_lengkap', 'kepegawaian_master_kategori_id')
            ->orderBy('nama_lengkap')
            ->get();

        $bulanList = Bulan::with('tahunAjaran')->orderByDesc('id')->get();

        return view('admin.presensi.edit', [
            'presensi'     => $presensi,
            'karyawanList' => $karyawanList,
            'bulanList'    => $bulanList,
        ]);
    }

    /**
     * Memperbarui data presensi satuan melalui Service.
     */
    public function update(PresensiRequest $request, Presensi $presensi): RedirectResponse
    {
        $this->service->update($presensi, $request->validated());

        return redirect()->route('presensi.index', [
            'tanggal_mulai'   => $request->input('tanggal'),
            'tanggal_selesai' => $request->input('tanggal'),
            'search'          => $presensi->karyawan_nipy,
        ])->with('success', 'Data presensi berhasil diperbarui.');
    }

    /**
     * Menghapus catatan presensi.
     */
    public function destroy(Presensi $presensi): RedirectResponse
    {
        $this->service->delete($presensi);

        return redirect()->back()
            ->with('success', 'Data presensi berhasil dihapus.');
    }

    /**
     * Menampilkan rekapitulasi presensi bulanan per Kategori (Tutup Buku tgl 28).
     */
    public function rekap(Request $request): View
    {
        $bulanList = Bulan::with('tahunAjaran')->orderByDesc('id')->get();
        $kategoriList = Kategori::orderBy('nama_kategori')->get();

        $selectedBulanId = (int) $request->input(
            'bulan_id',
            $bulanList->firstWhere('status', true)?->id ?? $bulanList->first()?->id ?? 0
        );

        $selectedBulan = $bulanList->firstWhere('id', $selectedBulanId);
        $selectedKategoriId = $request->input('kepegawaian_master_kategori_id');

        $rekapData = $selectedBulanId > 0
            ? $this->service->getRekapBulanan($selectedBulanId, $selectedKategoriId ? (int) $selectedKategoriId : null)
            : collect();

        return view('admin.presensi.rekap', [
            'rekapData'          => $rekapData,
            'bulanList'          => $bulanList,
            'kategoriList'       => $kategoriList,
            'selectedBulan'      => $selectedBulan,
            'selectedBulanId'    => $selectedBulanId,
            'selectedKategoriId' => $selectedKategoriId,
        ]);
    }

    /**
     * Menampilkan Matriks Presensi Harian (Grid View Tanggal x Pegawai).
     * Memudahkan admin melihat detail kehadiran dan mendeteksi siapa saja yang belum absen.
     */
    public function matriks(Request $request): View
    {
        $bulanList = Bulan::with('tahunAjaran')->orderByDesc('id')->get();
        $kategoriList = Kategori::orderBy('nama_kategori')->get();

        $selectedBulanId = (int) $request->input(
            'bulan_id',
            $bulanList->firstWhere('status', true)?->id ?? $bulanList->first()?->id ?? 0
        );

        $selectedBulan = $bulanList->firstWhere('id', $selectedBulanId);
        $selectedKategoriId = $request->input('kepegawaian_master_kategori_id');
        $onlyWorkdays = $request->has('only_workdays') ? $request->boolean('only_workdays') : true;

        $matriksData = $selectedBulanId > 0
            ? $this->service->getMatriksBulanan(
                bulanId: $selectedBulanId,
                kategoriId: $selectedKategoriId ? (int) $selectedKategoriId : null,
                onlyWorkdays: $onlyWorkdays
            )
            : [
                'bulan'         => null,
                'daftarTanggal' => collect(),
                'pegawaiRows'   => collect(),
                'columnTotals'  => [],
                'summary'       => [
                    'total_pegawai'                      => 0,
                    'total_pegawai_aktif'                => 0,
                    'total_pegawai_belum_absen_hari_ini' => 0,
                    'start_date'                         => null,
                    'end_date'                           => null,
                    'total_hari_tampil'                  => 0,
                ],
            ];

        return view('admin.presensi.matriks', array_merge($matriksData, [
            'bulanList'          => $bulanList,
            'kategoriList'       => $kategoriList,
            'selectedBulan'      => $selectedBulan,
            'selectedBulanId'    => $selectedBulanId,
            'selectedKategoriId' => $selectedKategoriId,
            'onlyWorkdays'       => $onlyWorkdays,
        ]));
    }

    /**
     * Menampilkan dashboard statistik tahunan berdasarkan Akademik Master Tahun Ajaran ID.
     */
    public function statistikTahunan(PresensiStatistikRequest $request): View
    {
        $tahunAjaranList = TahunAjaran::orderByDesc('id')->get();
        $kategoriList = Kategori::orderBy('nama_kategori')->get();

        $selectedTahunAjaranId = (int) $request->input(
            'akademik_master_tahun_ajaran_id',
            $tahunAjaranList->firstWhere('is_active', true)?->id ?? $tahunAjaranList->first()?->id ?? 0
        );

        $selectedKategoriId = $request->filled('kepegawaian_master_kategori_id')
            ? (int) $request->input('kepegawaian_master_kategori_id')
            : null;

        $stats = $selectedTahunAjaranId > 0
            ? $this->service->getStatistikTahunan($selectedTahunAjaranId, $selectedKategoriId)
            : [
                'tahunAjaran'      => null,
                'daftarBulan'      => collect(),
                'breakdownBulanan' => collect(),
                'kpiTahunan'       => [],
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

        return view('admin.presensi.statistik_tahunan', array_merge($stats, [
            'tahunAjaranList'       => $tahunAjaranList,
            'kategoriList'          => $kategoriList,
            'selectedTahunAjaranId' => $selectedTahunAjaranId,
            'selectedKategoriId'    => $selectedKategoriId,
        ]));
    }
}
