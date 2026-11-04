<?php

namespace App\Http\Controllers\Admin\Cuti;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Cuti\CutiFilterRequest;
use App\Http\Requests\Admin\Cuti\CutiRequest;
use App\Models\Admin\Cuti\Cuti;
use App\Models\Admin\DataMaster\HariLibur;
use App\Models\Admin\DataMaster\JenisCuti;
use App\Models\Admin\DataMaster\Karyawan;
use App\Services\Admin\Cuti\CutiService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CutiController extends Controller
{
    public function __construct(
        protected CutiService $service
    ) {
        $this->middleware('role:super admin');
    }

    /**
     * Menampilkan daftar data pengajuan cuti dengan pagination, filter, dan rekap KPI.
     */
    public function index(CutiFilterRequest $request): View|JsonResponse
    {
        $filters = $request->validated();
        $perPage = (int) $request->input('per_page', 15);

        $cutiList = $this->service->getPaginated($filters, $perPage);
        $statistik = $this->service->getRekapStatistik();
        $jenisCutiOptions = JenisCuti::orderBy('nama')->get();

        if ($request->wantsJson()) {
            return response()->json([
                'success'   => true,
                'data'      => $cutiList,
                'statistik' => $statistik,
            ]);
        }

        return view('admin.cuti.index', compact('cutiList', 'statistik', 'filters', 'jenisCutiOptions'));
    }

    /**
     * Menampilkan formulir pengajuan cuti baru.
     */
    public function create(): View
    {
        $karyawanList = Karyawan::query()
            ->select('nipy', 'nama_lengkap', 'gelar', 'unit', 'jabatan', 'whatsapp')
            ->orderBy('nama_lengkap')
            ->get();

        $jenisCutiList = JenisCuti::query()
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();

        $hariLiburMap = HariLibur::getHolidayMap();

        return view('admin.cuti.create', compact('karyawanList', 'jenisCutiList', 'hariLiburMap'));
    }

    /**
     * Menyimpan data pengajuan cuti baru ke database.
     */
    public function store(CutiRequest $request): RedirectResponse|JsonResponse
    {
        try {
            $created = $this->service->store($request->validated());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pengajuan cuti berhasil disimpan.',
                    'data'    => $created,
                ], 201);
            }

            return redirect()
                ->route('cuti.index')
                ->with('success', "Pengajuan cuti untuk pegawai '{$created->karyawan?->nama_lengkap}' berhasil disimpan.");
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan pengajuan cuti: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan data cuti: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan formulir perbarui data cuti.
     */
    public function edit(int $id): View
    {
        $cuti = $this->service->getById($id);

        $karyawanList = Karyawan::query()
            ->select('nipy', 'nama_lengkap', 'gelar', 'unit', 'jabatan', 'whatsapp')
            ->orderBy('nama_lengkap')
            ->get();

        $jenisCutiList = JenisCuti::query()
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();

        $hariLiburMap = HariLibur::getHolidayMap();

        return view('admin.cuti.edit', compact('cuti', 'karyawanList', 'jenisCutiList', 'hariLiburMap'));
    }

    /**
     * Memperbarui data pengajuan cuti.
     */
    public function update(CutiRequest $request, int $id): RedirectResponse|JsonResponse
    {
        try {
            $cuti = $this->service->getById($id);
            $updated = $this->service->update($cuti, $request->validated());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data cuti berhasil diperbarui.',
                    'data'    => $updated,
                ]);
            }

            return redirect()
                ->route('cuti.index')
                ->with('success', "Data cuti '{$updated->karyawan?->nama_lengkap}' berhasil diperbarui.");
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui data: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui data cuti: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus data cuti.
     */
    public function destroy(int $id, Request $request): RedirectResponse|JsonResponse
    {
        try {
            $cuti = $this->service->getById($id);
            $nama = $cuti->karyawan?->nama_lengkap ?? $cuti->karyawan_nipy;
            $this->service->delete($cuti);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Data cuti pegawai '{$nama}' berhasil dihapus.",
                ]);
            }

            return redirect()
                ->route('cuti.index')
                ->with('success', "Data cuti pegawai '{$nama}' berhasil dihapus.");
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus data: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus data cuti: ' . $e->getMessage());
        }
    }

    /**
     * Toggle persetujuan cuti (ACC <-> Batal ACC).
     */
    public function toggleAcc(Request $request, int $id): RedirectResponse|JsonResponse
    {
        try {
            $cuti = $this->service->getById($id);
            $catatan = $request->input('catatan_acc');
            $updated = $this->service->toggleAcc($cuti, $catatan, auth()->id());

            $statusText = $updated->acc ? 'disetujui (ACC)' : 'dibatalkan (Menunggu ACC)';

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Status cuti berhasil diubah menjadi: {$statusText}.",
                    'data'    => $updated,
                ]);
            }

            return redirect()
                ->back()
                ->with('success', "Status cuti '{$updated->karyawan?->nama_lengkap}' berhasil diubah menjadi: {$statusText}.");
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengubah status ACC: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()
                ->back()
                ->with('error', 'Gagal mengubah status ACC cuti: ' . $e->getMessage());
        }
    }
}
