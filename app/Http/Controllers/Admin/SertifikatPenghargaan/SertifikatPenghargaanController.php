<?php

namespace App\Http\Controllers\Admin\SertifikatPenghargaan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SertifikatPenghargaan\SertifikatPenghargaanFilterRequest;
use App\Http\Requests\Admin\SertifikatPenghargaan\SertifikatPenghargaanRequest;
use App\Models\Admin\DataMaster\Karyawan;
use App\Services\Admin\SertifikatPenghargaan\SertifikatPenghargaanService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class SertifikatPenghargaanController extends Controller
{
    /**
     * Dependency Injection pada constructor untuk arsitektur Decoupled & Thin Controller.
     */
    public function __construct(
        protected SertifikatPenghargaanService $service
    ) {}

    /**
     * Menampilkan daftar data Sertifikat Penghargaan dengan filter dan pagination.
     */
    public function index(SertifikatPenghargaanFilterRequest $request): View|JsonResponse
    {
        $filters = $request->validated();
        $perPage = (int) $request->input('per_page', 15);

        $penghargaanList = $this->service->getPaginated($filters, $perPage);
        $statistik = $this->service->getStatistik(
            isset($filters['tahun']) ? (int) $filters['tahun'] : null
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success'   => true,
                'data'      => $penghargaanList,
                'statistik' => $statistik,
            ]);
        }

        return view('admin.sertifikatPenghargaan.index', compact(
            'penghargaanList',
            'statistik',
            'filters'
        ));
    }

    /**
     * Menampilkan formulir penambahan data Sertifikat Penghargaan baru.
     */
    public function create(): View
    {
        $karyawanList = Karyawan::query()
            ->select('nipy', 'nama_lengkap', 'gelar', 'unit', 'jabatan')
            ->orderBy('nama_lengkap')
            ->get();

        return view('admin.sertifikatPenghargaan.create', compact('karyawanList'));
    }

    /**
     * Menyimpan data Sertifikat Penghargaan baru beserta berkas fisik ke storage.
     */
    public function store(SertifikatPenghargaanRequest $request): RedirectResponse|JsonResponse
    {
        try {
            $data = $request->validated();
            $file = $request->file('berkas');

            $penghargaan = $this->service->create($data, $file);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Sertifikat penghargaan '{$penghargaan->judul_penghargaan}' berhasil disimpan.",
                    'data'    => $penghargaan,
                ], Response::HTTP_CREATED);
            }

            return redirect()
                ->route('sertifikat-penghargaan.index')
                ->with('success', "Sertifikat penghargaan '{$penghargaan->judul_penghargaan}' berhasil disimpan.");
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan sertifikat penghargaan: ' . $e->getMessage(),
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan sertifikat penghargaan: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan detail data Sertifikat Penghargaan tertentu.
     */
    public function show(int $id, Request $request): View|JsonResponse
    {
        $penghargaan = $this->service->getById($id);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data'    => $penghargaan,
            ]);
        }

        return view('admin.sertifikatPenghargaan.show', compact('penghargaan'));
    }

    /**
     * Menampilkan formulir edit Sertifikat Penghargaan.
     */
    public function edit(int $id): View
    {
        $penghargaan = $this->service->getById($id);

        $karyawanList = Karyawan::query()
            ->select('nipy', 'nama_lengkap', 'gelar', 'unit', 'jabatan')
            ->orderBy('nama_lengkap')
            ->get();

        return view('admin.sertifikatPenghargaan.edit', compact('penghargaan', 'karyawanList'));
    }

    /**
     * Memperbarui data Sertifikat Penghargaan dan mengganti berkas jika disediakan.
     */
    public function update(SertifikatPenghargaanRequest $request, int $id): RedirectResponse|JsonResponse
    {
        try {
            $penghargaan = $this->service->getById($id);
            $data = $request->validated();
            $file = $request->file('berkas');

            $updated = $this->service->update($penghargaan, $data, $file);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sertifikat penghargaan berhasil diperbarui.',
                    'data'    => $updated,
                ]);
            }

            return redirect()
                ->route('sertifikat-penghargaan.index')
                ->with('success', 'Sertifikat penghargaan berhasil diperbarui.');
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui sertifikat penghargaan: ' . $e->getMessage(),
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui sertifikat penghargaan: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus data Sertifikat Penghargaan beserta berkas fisiknya.
     */
    public function destroy(int $id, Request $request): RedirectResponse|JsonResponse
    {
        try {
            $penghargaan = $this->service->getById($id);
            $this->service->delete($penghargaan);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sertifikat penghargaan berhasil dihapus.',
                ]);
            }

            return redirect()
                ->route('sertifikat-penghargaan.index')
                ->with('success', 'Sertifikat penghargaan berhasil dihapus.');
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus sertifikat penghargaan: ' . $e->getMessage(),
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return redirect()
                ->route('sertifikat-penghargaan.index')
                ->with('error', 'Gagal menghapus sertifikat penghargaan: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan preview berkas fisik sertifikat penghargaan secara inline.
     */
    public function preview(int $id): Response
    {
        try {
            $penghargaan = $this->service->getById($id);

            return $this->service->previewBerkas($penghargaan);
        } catch (Exception $e) {
            abort(404, $e->getMessage());
        }
    }

    /**
     * Mengunduh berkas fisik sertifikat penghargaan.
     */
    public function download(int $id, Request $request): Response
    {
        try {
            $penghargaan = $this->service->getById($id);

            return $this->service->downloadBerkas($penghargaan);
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
