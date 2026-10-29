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
use App\Exports\PenggajianTemplateExport;
use App\Imports\PenggajianImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\View\View;

class PenggajianController extends Controller
{
    public function __construct(
        protected PenggajianService $service
    ) {}

    public function index(PenggajianFilterRequest $request): View
    {
        $bulanList = $this->allBulanList();
        $defaultBulan = $bulanList->firstWhere('status', true) ?: $bulanList->first();

        $filters = $request->validated();
        if (empty($filters['kepegawaian_master_bulan_id']) && $defaultBulan) {
            $filters['kepegawaian_master_bulan_id'] = $defaultBulan->id;
        }

        $perPage = (int) ($filters['per_page'] ?? 15);

        $penggajianList = $this->service->getPaginated($filters, $perPage);
        $statistik = $this->service->getStatistik($filters);
        $jenisList = $this->service->getJenisList();
        $statusList = $this->service->getStatusList();

        return view('admin.penggajian.index', compact(
            'penggajianList',
            'statistik',
            'bulanList',
            'filters',
            'jenisList',
            'statusList'
        ));
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
        $statusList = $this->service->getStatusList();
        $penerimaanList = $this->service->getPenerimaanList();
        $potonganList = $this->service->getPotonganList();

        return view('admin.penggajian.show', compact('penggajian', 'statusList', 'penerimaanList', 'potonganList'));
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
            $this->formOptions($penggajian)
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
        $statusList = $this->service->getStatusList();

        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys($statusList))],
        ]);

        $this->service->updateStatus($penggajian, $data['status']);

        return back()->with('success', 'Status slip gaji diubah menjadi ' . ($statusList[$data['status']] ?? $data['status']) . '.');
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

    public function template()
    {
        return Excel::download(new PenggajianTemplateExport, 'template-penggajian.xlsx');
    }

    public function import(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kepegawaian_master_bulan_id' => ['required', 'exists:' . (new Bulan)->getTable() . ',id'],
            'status'                      => ['required', Rule::in(['draft', 'final'])],
            'jenis'                       => ['required', Rule::in(array_keys($this->service->getJenisList()))],
            'file'                        => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        $import = new PenggajianImport(
            (int) $data['kepegawaian_master_bulan_id'],
            $data['status'],
            $data['jenis']
        );

        try {
            Excel::import($import, $request->file('file'));
        } catch (Exception $e) {
            return back()->with('error', 'Import gagal: ' . $e->getMessage());
        }

        $msg = "Import selesai ({$import->created} slip baru, {$import->updated} diperbarui).";
        if ($import->errors) {
            $msg .= ' Catatan: ' . implode(' | ', array_slice($import->errors, 0, 5))
                . (count($import->errors) > 5 ? ' ...(+' . (count($import->errors) - 5) . ' lainnya)' : '');
        }

        return redirect()->route('penggajian.index', ['jenis' => $data['jenis']])->with($import->errors ? 'error' : 'success', $msg);
    }

    private function formOptions(?Penggajian $penggajian = null): array
    {
        return [
            'karyawanList'   => Karyawan::query()
                ->select('nipy', 'nama_lengkap', 'gelar', 'unit', 'jabatan', 'golongan')
                ->orderBy('nama_lengkap')
                ->get(),
            'bulanList'      => $this->allBulanList(),
            'jenisList'      => $this->service->getJenisList(),
            'statusList'     => $this->service->getStatusList(),
            'penerimaanList' => $this->service->getPenerimaanList(),
            'potonganList'   => $this->service->getPotonganList(),
        ];
    }

    private function allBulanList()
    {
        return Bulan::with('tahunAjaran:id,nama_tahun_ajaran,semester')
            ->orderByDesc('status')
            ->orderByDesc('id')
            ->get();
    }
}
