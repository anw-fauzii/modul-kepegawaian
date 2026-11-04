<?php

namespace App\Http\Controllers\Admin\DataMaster;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DataMaster\KaryawanRequest;
use App\Models\Admin\DataMaster\JenjangPendidikan;
use App\Models\Admin\DataMaster\Karyawan;
use App\Models\Admin\DataMaster\Kategori;
use App\Models\Admin\DataMaster\JamKerja;
use App\Models\Admin\DataMaster\RiwayatJabatan;
use App\Services\Admin\DataMaster\KaryawanService;
use App\Exports\KaryawanExport;
use App\Exports\KaryawanTemplateExport;
use App\Imports\KaryawanImport;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class KaryawanController extends Controller
{
    public function __construct(
        protected KaryawanService $service
    ) {
        $this->middleware('role:super admin');
    }

    public function foto(Karyawan $karyawan)
    {
        if (! $karyawan->foto) {
            abort(404);
        }

        if (Storage::disk('private')->exists($karyawan->foto)) {
            return Storage::disk('private')->response($karyawan->foto);
        }

        if (Storage::disk('public')->exists($karyawan->foto)) {
            return Storage::disk('public')->response($karyawan->foto);
        }

        abort(404);
    }

    public function index(): View
    {
        return view('admin.dataMaster.karyawan.index', [
            'karyawan' => $this->service->getAll()
        ]);
    }

    public function byUnit(string $unit): View
    {
        return view('admin.dataMaster.karyawan.index', [
            'karyawan'  => $this->service->getByUnit($unit),
            'unitAktif' => $unit,
        ]);
    }

    public function create(): View
    {
        return view('admin.dataMaster.karyawan.create', $this->formOptions());
    }

    public function store(KaryawanRequest $request): RedirectResponse
    {
        $this->service->store($request->validated());

        return redirect()->route('karyawan.index')
            ->with('success', 'Karyawan berhasil disimpan');
    }

    public function show(Karyawan $karyawan): View
    {
        $karyawan->load([
            'kategori',
            'jenjangPendidikan',
            'jamKerja',
            'user',
            'riwayatJabatan' => fn ($q) => $q->with('kategori')->orderByDesc('is_aktif')->orderByDesc('tanggal_mulai')->orderByDesc('id'),
            'fasilitas' => fn ($q) => $q->orderBy('status', 'asc')->latest('tahun_terima'),
            'diklat' => fn ($q) => $q->with('kegiatanLembaga')->latest('tanggal'),
            'rapotKinerja' => fn ($q) => $q->with('tahunAjaran')->latest('id'),
            'suratKeputusan' => fn ($q) => $q->with('tahunAjaran')->latest('tanggal_sk'),
            'sertifikatPenghargaan' => fn ($q) => $q->latest('tahun'),
            'tupoksi' => fn ($q) => $q->with('tahunAjaran')->latest('id'),
            'kehadiranKegiatan' => fn ($q) => $q->with('kegiatan')->latest('waktu_scan')->limit(20),
            'presensi' => fn ($q) => $q->with('bulan')->latest('tanggal')->limit(31),
        ]);

        $karyawan->loadCount([
            'riwayatJabatan',
            'fasilitas',
            'diklat',
            'rapotKinerja',
            'suratKeputusan',
            'sertifikatPenghargaan',
            'tupoksi',
            'kehadiranKegiatan',
            'presensi',
        ]);

        $kategoriList = Kategori::orderBy('nama_kategori')->get();

        return view('admin.dataMaster.karyawan.show', compact('karyawan', 'kategoriList'));
    }

    public function storeRiwayatJabatan(Request $request, Karyawan $karyawan): RedirectResponse
    {
        $validated = $request->validate([
            'jabatan'                        => ['required', 'string', 'max:255'],
            'unit'                           => ['nullable', 'string', 'max:255'],
            'kepegawaian_master_kategori_id' => ['nullable', 'exists:kepegawaian_master_kategori,id'],
            'tanggal_mulai'                  => ['nullable', 'date'],
            'tanggal_selesai'                => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'is_aktif'                       => ['nullable', 'boolean'],
            'sync_karyawan'                  => ['nullable', 'boolean'],
            'keterangan'                     => ['nullable', 'string', 'max:1000'],
        ]);

        $isAktif = $request->boolean('is_aktif');

        if ($isAktif) {
            RiwayatJabatan::where('karyawan_nipy', $karyawan->nipy)
                ->where('is_aktif', true)
                ->update([
                    'is_aktif'        => false,
                    'tanggal_selesai' => $validated['tanggal_mulai'] ?? now()->toDateString(),
                ]);
        }

        RiwayatJabatan::create([
            'karyawan_nipy'                  => $karyawan->nipy,
            'kepegawaian_master_kategori_id' => $validated['kepegawaian_master_kategori_id'] ?? $karyawan->kepegawaian_master_kategori_id,
            'jabatan'                        => $validated['jabatan'],
            'unit'                           => $validated['unit'] ?? $karyawan->unit,
            'tanggal_mulai'                  => $validated['tanggal_mulai'] ?? null,
            'tanggal_selesai'                => $isAktif ? null : ($validated['tanggal_selesai'] ?? null),
            'is_aktif'                       => $isAktif,
            'keterangan'                     => $validated['keterangan'] ?? null,
        ]);

        if ($isAktif && $request->boolean('sync_karyawan')) {
            $karyawan->update(array_filter([
                'jabatan'                        => $validated['jabatan'],
                'unit'                           => $validated['unit'] ?? $karyawan->unit,
                'kepegawaian_master_kategori_id' => $validated['kepegawaian_master_kategori_id'] ?? $karyawan->kepegawaian_master_kategori_id,
            ], fn ($v) => $v !== null));
        }

        return redirect()->route('karyawan.show', $karyawan->nipy)
            ->with('success', 'Histori jabatan karyawan berhasil ditambahkan.');
    }

    public function destroyRiwayatJabatan(Karyawan $karyawan, RiwayatJabatan $riwayatJabatan): RedirectResponse
    {
        if ($riwayatJabatan->karyawan_nipy !== $karyawan->nipy) {
            abort(403);
        }

        $riwayatJabatan->delete();

        return redirect()->route('karyawan.show', $karyawan->nipy)
            ->with('success', 'Histori jabatan berhasil dihapus.');
    }

    public function edit(Karyawan $karyawan): View
    {
        return view('admin.dataMaster.karyawan.edit', array_merge(
            compact('karyawan'),
            $this->formOptions()
        ));
    }

    public function update(KaryawanRequest $request, Karyawan $karyawan): RedirectResponse
    {
        $this->service->update($karyawan, $request->validated());

        return redirect()->route('karyawan.index')
            ->with('success', 'Karyawan berhasil diupdate');
    }

    public function destroy(Karyawan $karyawan): RedirectResponse
    {
        $this->service->delete($karyawan);

        return redirect()->route('karyawan.index')
            ->with('success', 'Karyawan berhasil dihapus');
    }

    public function export(Request $request)
    {
        $unit = $request->query('unit');
        $filename = $unit
            ? 'data-karyawan-unit-' . Str::slug($unit) . '.xlsx'
            : 'data-karyawan-' . date('Y-m-d') . '.xlsx';

        return Excel::download(new KaryawanExport($unit), $filename);
    }

    public function template()
    {
        return Excel::download(new KaryawanTemplateExport, 'template-import-karyawan.xlsx');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file'           => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
            'default_status' => ['nullable', 'in:aktif,nonaktif'],
        ], [
            'file.required' => 'File Excel wajib dipilih.',
            'file.mimes'    => 'File harus berformat .xlsx, .xls, atau .csv.',
            'file.max'      => 'Ukuran file maksimal 10 MB.',
        ]);

        $defaultActive = $request->input('default_status', 'aktif') === 'aktif';
        $import        = new KaryawanImport($defaultActive);

        try {
            Excel::import($import, $request->file('file'));
        } catch (Exception $e) {
            return back()->with('error', 'Import gagal: ' . $e->getMessage());
        }

        $msg = "Import selesai ({$import->created} karyawan baru ditambahkan, {$import->updated} data diperbarui).";
        if (! empty($import->errors)) {
            $msg .= ' Catatan: ' . implode(' | ', array_slice($import->errors, 0, 5))
                . (count($import->errors) > 5 ? ' ...(+' . (count($import->errors) - 5) . ' lainnya)' : '');
        }

        return redirect()
            ->route('karyawan.index')
            ->with(! empty($import->errors) ? 'warning' : 'success', $msg);
    }

    private function formOptions(): array
    {
        return [
            'kategori' => Kategori::orderBy('nama_kategori')->get(),
            'jenjang'  => JenjangPendidikan::orderBy('id')->get(),
            'jamKerja' => JamKerja::orderBy('id')->get(),
        ];
    }
}
