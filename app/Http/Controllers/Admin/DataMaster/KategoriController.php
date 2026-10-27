<?php

namespace App\Http\Controllers\Admin\DataMaster;

use App\Http\Controllers\Controller;
use App\Models\Admin\DataMaster\Kategori;
use App\Http\Requests\Admin\DataMaster\KategoriRequest;
use App\Services\Admin\DataMaster\KategoriService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class KategoriController extends Controller
{
    public function __construct(
        protected KategoriService $service
    ) {}

    public function index(): View
    {
        return view('admin.dataMaster.kategori.index', [
            'kategori' => $this->service->getAll()
        ]);
    }

    public function create(): View
    {
        return view('admin.dataMaster.kategori.create');
    }

    public function store(KategoriRequest $request): RedirectResponse
    {
        $this->service->store($request->validated());

        return redirect()->route('kategori.index')
            ->with('success', 'Kategori berhasil disimpan');
    }

    public function edit(Kategori $kategori): View
    {
        return view('admin.dataMaster.kategori.edit', compact('kategori'));
    }

    public function update(KategoriRequest $request, Kategori $kategori): RedirectResponse
    {
        $this->service->update($kategori, $request->validated());

        return redirect()->route('kategori.index')
            ->with('success', 'Kategori berhasil diupdate');
    }

    public function destroy(Kategori $kategori): RedirectResponse
    {
        $this->service->delete($kategori);

        return redirect()->route('kategori.index')
            ->with('success', 'Kategori berhasil dihapus');
    }
}