<?php

namespace App\Http\Controllers\Admin\DataMaster;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DataMaster\JenisCutiRequest;
use App\Models\Admin\DataMaster\JenisCuti;
use App\Services\Admin\DataMaster\JenisCutiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class JenisCutiController extends Controller
{
    public function __construct(
        protected JenisCutiService $service
    ) {
        // Lapis 1: proteksi rute berbasis Role (Spatie)
        $this->middleware('role:super admin');
    }

    public function index(): View
    {
        return view('admin.dataMaster.jenisCuti.index', [
            'jenisCuti' => $this->service->getAll(),
        ]);
    }

    public function create(): View
    {
        return view('admin.dataMaster.jenisCuti.create');
    }

    public function store(JenisCutiRequest $request): RedirectResponse
    {
        $this->service->store($request->validated());

        return redirect()->route('jenis-cuti.index')
            ->with('success', 'Jenis cuti berhasil disimpan.');
    }

    public function edit(JenisCuti $jenisCuti): View
    {
        return view('admin.dataMaster.jenisCuti.edit', compact('jenisCuti'));
    }

    public function update(JenisCutiRequest $request, JenisCuti $jenisCuti): RedirectResponse
    {
        $this->service->update($jenisCuti, $request->validated());

        return redirect()->route('jenis-cuti.index')
            ->with('success', 'Jenis cuti berhasil diupdate.');
    }

    public function destroy(JenisCuti $jenisCuti): RedirectResponse
    {
        $this->service->delete($jenisCuti);

        return redirect()->route('jenis-cuti.index')
            ->with('success', 'Jenis cuti berhasil dihapus.');
    }
}
