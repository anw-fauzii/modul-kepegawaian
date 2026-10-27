<?php

namespace App\Http\Controllers\Admin\DataMaster;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DataMaster\BulanRequest;
use App\Models\Admin\DataMaster\Bulan;
use App\Models\Admin\DataMaster\TahunAjaran;
use App\Services\Admin\DataMaster\BulanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BulanController extends Controller
{
    public function __construct(
        protected BulanService $service
    ) {}

    public function index(): View
    {
        return view('admin.dataMaster.bulan.index', [
            'bulan' => $this->service->getAll()
        ]);
    }

    public function create(): View
    {
        return view('admin.dataMaster.bulan.create', $this->formOptions());
    }

    public function store(BulanRequest $request): RedirectResponse
    {
        $this->service->store($request->validated());

        return redirect()->route('bulan.index')
            ->with('success', 'Bulan berhasil disimpan');
    }

    public function edit(Bulan $bulan): View
    {
        return view('admin.dataMaster.bulan.edit', array_merge(
            compact('bulan'),
            $this->formOptions()
        ));
    }

    public function update(BulanRequest $request, Bulan $bulan): RedirectResponse
    {
        $this->service->update($bulan, $request->validated());

        return redirect()->route('bulan.index')
            ->with('success', 'Bulan berhasil diupdate');
    }

    public function destroy(Bulan $bulan): RedirectResponse
    {
        $this->service->delete($bulan);

        return redirect()->route('bulan.index')
            ->with('success', 'Bulan berhasil dihapus');
    }

    private function formOptions(): array
    {
        return [
            'tahunAjaran' => TahunAjaran::orderByDesc('id')->get(),
        ];
    }
}
