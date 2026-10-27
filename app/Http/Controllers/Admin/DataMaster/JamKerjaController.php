<?php

namespace App\Http\Controllers\Admin\DataMaster;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DataMaster\JamKerjaRequest;
use App\Models\Admin\DataMaster\JamKerja;
use App\Services\Admin\DataMaster\JamKerjaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class JamKerjaController extends Controller
{
    public function __construct(
        protected JamKerjaService $service
    ) {}

    public function index(): View
    {
        return view('admin.dataMaster.jamKerja.index', [
            'jamKerja' => $this->service->getAll()
        ]);
    }

    public function create(): View
    {
        return view('admin.dataMaster.jamKerja.create');
    }

    public function store(JamKerjaRequest $request): RedirectResponse
    {
        $this->service->store($request->validated());

        return redirect()->route('jam-kerja.index')
            ->with('success', 'Jam kerja berhasil disimpan');
    }

    public function edit(JamKerja $jamKerja): View
    {
        return view('admin.dataMaster.jamKerja.edit', compact('jamKerja'));
    }

    public function update(JamKerjaRequest $request, JamKerja $jamKerja): RedirectResponse
    {
        $this->service->update($jamKerja, $request->validated());

        return redirect()->route('jam-kerja.index')
            ->with('success', 'Jam kerja berhasil diupdate');
    }

    public function destroy(JamKerja $jamKerja): RedirectResponse
    {
        $this->service->delete($jamKerja);

        return redirect()->route('jam-kerja.index')
            ->with('success', 'Jam kerja berhasil dihapus');
    }
}
