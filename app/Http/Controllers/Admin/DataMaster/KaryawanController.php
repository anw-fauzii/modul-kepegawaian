<?php

namespace App\Http\Controllers\Admin\DataMaster;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DataMaster\KaryawanRequest;
use App\Models\Admin\DataMaster\JenjangPendidikan;
use App\Models\Admin\DataMaster\Karyawan;
use App\Models\Admin\DataMaster\Kategori;
use App\Models\Admin\DataMaster\JamKerja;
use App\Services\Admin\DataMaster\KaryawanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class KaryawanController extends Controller
{
    public function __construct(
        protected KaryawanService $service
    ) {}

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
            'fasilitas',
            'diklat',
            'rapotKinerja',
            'suratKeputusan',
            'sertifikatPenghargaan',
            'tupoksi',
            'kehadiranKegiatan',
            'presensi',
        ]);

        return view('admin.dataMaster.karyawan.show', compact('karyawan'));
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

    private function formOptions(): array
    {
        return [
            'kategori' => Kategori::orderBy('nama_kategori')->get(),
            'jenjang'  => JenjangPendidikan::orderBy('id')->get(),
            'jamKerja' => JamKerja::orderBy('id')->get(),
        ];
    }
}
