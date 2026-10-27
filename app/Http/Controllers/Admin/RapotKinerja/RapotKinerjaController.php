<?php

namespace App\Http\Controllers\Admin\RapotKinerja;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RapotKinerja\RapotKinerjaFilterRequest;
use App\Http\Requests\Admin\RapotKinerja\RapotKinerjaRequest;
use App\Models\Admin\DataMaster\Karyawan;
use App\Models\Admin\DataMaster\TahunAjaran;
use App\Services\Admin\RapotKinerja\RapotKinerjaService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class RapotKinerjaController extends Controller
{
    /**
     * Dependency Injection pada constructor untuk arsitektur Decoupled & Thin Controller.
     */
    public function __construct(
        protected RapotKinerjaService $service
    ) {}

    /**
     * Menampilkan daftar Rapot Kinerja dengan filter dan pagination.
     */
    public function index(RapotKinerjaFilterRequest $request): View|JsonResponse
    {
        $filters = $request->validated();
        $perPage = (int) $request->input('per_page', 15);

        $rapotList = $this->service->getPaginated($filters, $perPage);
        $statistik = $this->service->getStatistik(
            isset($filters['akademik_master_tahun_ajaran_id']) ? (int) $filters['akademik_master_tahun_ajaran_id'] : null
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success'   => true,
                'data'      => $rapotList,
                'statistik' => $statistik,
            ]);
        }

        $tahunAjaranList = TahunAjaran::query()
            ->select('id', 'nama_tahun_ajaran', 'semester', 'is_active')
            ->orderByDesc('id')
            ->get();

        return view('admin.rapotKinerja.index', compact(
            'rapotList',
            'statistik',
            'tahunAjaranList',
            'filters'
        ));
    }

    /**
     * Menampilkan formulir unggah Rapot Kinerja baru.
     */
    public function create(): View
    {
        $karyawanList = Karyawan::query()
            ->select('nipy', 'nama_lengkap', 'gelar', 'unit', 'jabatan')
            ->orderBy('nama_lengkap')
            ->get();

        $tahunAjaranList = TahunAjaran::query()
            ->select('id', 'nama_tahun_ajaran', 'semester', 'is_active')
            ->orderByDesc('id')
            ->get();

        return view('admin.rapotKinerja.create', compact('karyawanList', 'tahunAjaranList'));
    }

    /**
     * Menyimpan data Rapot Kinerja baru beserta berkas fisik.
     */
    public function store(RapotKinerjaRequest $request): RedirectResponse|JsonResponse
    {
        try {
            $data = $request->validated();
            $file = $request->file('berkas');

            $rapot = $this->service->create($data, $file);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Rapot Kinerja untuk karyawan {$rapot->karyawan_nipy} berhasil disimpan.",
                    'data'    => $rapot,
                ], Response::HTTP_CREATED);
            }

            return redirect()
                ->route('rapot-kinerja.index')
                ->with('success', "Rapot Kinerja untuk karyawan NIPY '{$rapot->karyawan_nipy}' berhasil disimpan.");
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan Rapot Kinerja: ' . $e->getMessage(),
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan Rapot Kinerja: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan detail Rapot Kinerja tertentu.
     */
    public function show(int $id, Request $request): View|JsonResponse
    {
        $rapot = $this->service->getById($id);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data'    => $rapot,
            ]);
        }

        return view('admin.rapotKinerja.show', compact('rapot'));
    }

    /**
     * Menampilkan formulir edit Rapot Kinerja.
     */
    public function edit(int $id): View
    {
        $rapot = $this->service->getById($id);

        $karyawanList = Karyawan::query()
            ->select('nipy', 'nama_lengkap', 'gelar', 'unit', 'jabatan')
            ->orderBy('nama_lengkap')
            ->get();

        $tahunAjaranList = TahunAjaran::query()
            ->select('id', 'nama_tahun_ajaran', 'semester', 'is_active')
            ->orderByDesc('id')
            ->get();

        return view('admin.rapotKinerja.edit', compact('rapot', 'karyawanList', 'tahunAjaranList'));
    }

    /**
     * Memperbarui data Rapot Kinerja dan mengganti berkas jika disediakan.
     */
    public function update(RapotKinerjaRequest $request, int $id): RedirectResponse|JsonResponse
    {
        try {
            $rapot = $this->service->getById($id);
            $data = $request->validated();
            $file = $request->file('berkas');

            $updated = $this->service->update($rapot, $data, $file);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Rapot Kinerja berhasil diperbarui.',
                    'data'    => $updated,
                ]);
            }

            return redirect()
                ->route('rapot-kinerja.index')
                ->with('success', 'Rapot Kinerja berhasil diperbarui.');
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui Rapot Kinerja: ' . $e->getMessage(),
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui Rapot Kinerja: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus data Rapot Kinerja beserta berkas fisiknya.
     */
    public function destroy(int $id, Request $request): RedirectResponse|JsonResponse
    {
        try {
            $rapot = $this->service->getById($id);
            $this->service->delete($rapot);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Rapot Kinerja berhasil dihapus.',
                ]);
            }

            return redirect()
                ->route('rapot-kinerja.index')
                ->with('success', 'Rapot Kinerja berhasil dihapus.');
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus Rapot Kinerja: ' . $e->getMessage(),
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return redirect()
                ->route('rapot-kinerja.index')
                ->with('error', 'Gagal menghapus Rapot Kinerja: ' . $e->getMessage());
        }
    }

    /**
     * Mengunduh berkas fisik Rapot Kinerja.
     */
    public function download(int $id, Request $request): Response
    {
        try {
            $rapot = $this->service->getById($id);

            return $this->service->downloadBerkas($rapot);
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], Response::HTTP_NOT_FOUND);
            }

            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }
}
