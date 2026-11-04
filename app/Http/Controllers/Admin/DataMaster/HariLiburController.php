<?php

namespace App\Http\Controllers\Admin\DataMaster;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DataMaster\HariLiburRequest;
use App\Models\Admin\DataMaster\HariLibur;
use App\Services\Admin\DataMaster\HariLiburService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;

class HariLiburController extends Controller
{
    public function __construct(
        protected HariLiburService $service
    ) {
        // Lapis 1: proteksi rute berbasis Role (Spatie)
        $this->middleware('role:super admin');
    }

    public function index(): View
    {
        return view('admin.dataMaster.hariLibur.index', [
            'hariLibur' => $this->service->getAll(),
        ]);
    }

    public function create(): View
    {
        return view('admin.dataMaster.hariLibur.create');
    }

    public function store(HariLiburRequest $request): RedirectResponse
    {
        $this->service->store($request->validated());

        return redirect()->route('hari-libur.index')
            ->with('success', 'Hari libur berhasil disimpan.');
    }

    public function edit(HariLibur $hariLibur): View
    {
        return view('admin.dataMaster.hariLibur.edit', compact('hariLibur'));
    }

    public function update(HariLiburRequest $request, HariLibur $hariLibur): RedirectResponse
    {
        $this->service->update($hariLibur, $request->validated());

        return redirect()->route('hari-libur.index')
            ->with('success', 'Hari libur berhasil diupdate.');
    }

    public function destroy(HariLibur $hariLibur): RedirectResponse
    {
        $this->service->delete($hariLibur);

        return redirect()->route('hari-libur.index')
            ->with('success', 'Hari libur berhasil dihapus.');
    }

    /**
     * Sinkronisasi data hari libur nasional langsung dari API melalui tombol web.
     */
    public function sync(Request $request): RedirectResponse
    {
        try {
            $year = $request->input('year') ?: date('Y');
            Artisan::call('import:hari-libur', ['year' => (string) $year]);

            return redirect()->route('hari-libur.index')
                ->with('success', "Sinkronisasi hari libur nasional tahun {$year} dari API berhasil dijalankan.");
        } catch (\Throwable $e) {
            return redirect()->route('hari-libur.index')
                ->with('error', 'Gagal melakukan sinkronisasi data dari API: ' . $e->getMessage());
        }
    }
}
