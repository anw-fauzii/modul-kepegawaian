<?php

namespace App\Http\Controllers\Admin\Penggajian;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Penggajian\PenggajianFilterRequest;
use App\Http\Requests\Admin\Penggajian\PenggajianRequest;
use App\Models\Admin\DataMaster\Bulan;
use App\Models\Admin\DataMaster\Karyawan;
use App\Models\Admin\Penggajian\Penggajian;
use App\Services\Admin\Penggajian\PenggajianService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PenggajianController extends Controller
{
    public function __construct(
        protected PenggajianService $service
    ) {}

    public function index(PenggajianFilterRequest $request): View
    {
        $filters = $request->validated();
        $perPage = (int) ($filters['per_page'] ?? 15);

        $penggajianList = $this->service->getPaginated($filters, $perPage);
        $statistik = $this->service->getStatistik($filters);
        $bulanList = $this->bulanList();

        return view('admin.penggajian.index', compact('penggajianList', 'statistik', 'bulanList', 'filters'));
    }

    public function create(): View
    {
        return view('admin.penggajian.create', $this->formOptions());
    }

    public function store(PenggajianRequest $request): RedirectResponse
    {
        try {
            $penggajian = $this->service->create($request->validated());
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('penggajian.show', $penggajian)
            ->with('success', 'Slip gaji berhasil disimpan.');
    }

    public function show(Penggajian $penggajian): View
    {
        $penggajian = $this->service->getById($penggajian);

        return view('admin.penggajian.show', compact('penggajian'));
    }

    public function edit(Penggajian $penggajian): View|RedirectResponse
    {
        if ($penggajian->status === 'dibayar') {
            return redirect()
                ->route('penggajian.show', $penggajian)
                ->with('error', 'Slip gaji berstatus "Dibayar" terkunci dan tidak dapat diedit.');
        }

        return view('admin.penggajian.edit', array_merge(
            compact('penggajian'),
            $this->formOptions()
        ));
    }

    public function update(PenggajianRequest $request, Penggajian $penggajian): RedirectResponse
    {
        try {
            $this->service->update($penggajian, $request->validated());
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('penggajian.show', $penggajian)
            ->with('success', 'Slip gaji berhasil diperbarui.');
    }

    public function updateStatus(Request $request, Penggajian $penggajian): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Penggajian::STATUS))],
        ]);

        $this->service->updateStatus($penggajian, $data['status']);

        return back()->with('success', 'Status slip gaji diubah menjadi ' . Penggajian::STATUS[$data['status']] . '.');
    }

    public function destroy(Penggajian $penggajian): RedirectResponse
    {
        try {
            $this->service->delete($penggajian);
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('penggajian.index')
            ->with('success', 'Slip gaji berhasil dihapus.');
    }

    private function formOptions(): array
    {
        return [
            'karyawanList' => Karyawan::query()
                ->select('nipy', 'nama_lengkap', 'gelar', 'unit', 'jabatan', 'golongan')
                ->orderBy('nama_lengkap')
                ->get(),
            'bulanList' => $this->bulanList(),
        ];
    }

    private function bulanList()
    {
        return Bulan::with('tahunAjaran:id,nama_tahun_ajaran,semester')
            ->orderByDesc('id')
            ->get();
    }
}
