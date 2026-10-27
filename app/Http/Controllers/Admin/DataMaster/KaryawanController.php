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
        $karyawan->loadMissing(['kategori', 'jenjangPendidikan', 'jamKerja', 'user']);

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
