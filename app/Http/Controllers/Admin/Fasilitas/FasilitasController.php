<?php

namespace App\Http\Controllers\Admin\Fasilitas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Fasilitas\FasilitasFilterRequest;
use App\Http\Requests\Admin\Fasilitas\FasilitasRequest;
use App\Models\Admin\DataMaster\Karyawan;
use App\Services\Admin\Fasilitas\FasilitasService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FasilitasController extends Controller
{
    /**
     * Dependency Injection pada constructor untuk arsitektur Decoupled & Thin Controller.
     */
    public function __construct(
        protected FasilitasService $service
    ) {}

    /**
     * Menampilkan daftar data fasilitas dengan filter dan pagination.
     */
    public function index(FasilitasFilterRequest $request): View|JsonResponse
    {
        $filters = $request->validated();
        $perPage = (int) $request->input('per_page', 15);

        $fasilitas = $this->service->getPaginated($filters, $perPage);
        $statistik = $this->service->getRekapStatistik();

        if ($request->wantsJson()) {
            return response()->json([
                'success'   => true,
                'data'      => $fasilitas,
                'statistik' => $statistik,
            ]);
        }

        return view('admin.fasilitas.index', compact('fasilitas', 'statistik', 'filters'));
    }

    /**
     * Menampilkan formulir penambahan data fasilitas baru.
     */
    public function create(): View
    {
        $karyawanList = Karyawan::query()
            ->select('nipy', 'nama_lengkap', 'gelar', 'unit', 'jabatan')
            ->orderBy('nama_lengkap')
            ->get();

        return view('admin.fasilitas.create', compact('karyawanList'));
    }

    /**
     * Menyimpan data fasilitas baru ke database via Service.
     */
    public function store(FasilitasRequest $request): RedirectResponse|JsonResponse
    {
        try {
            $fasilitas = $this->service->create($request->validated());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Fasilitas '{$fasilitas->nama_fasilitas}' berhasil dicatat.",
                    'data'    => $fasilitas,
                ], 201);
            }

            return redirect()
                ->route('fasilitas.index')
                ->with('success', "Fasilitas '{$fasilitas->nama_fasilitas}' berhasil dicatat.");
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan fasilitas: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan data fasilitas: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan detail fasilitas tertentu.
     */
    public function show(int $id, Request $request): View|JsonResponse
    {
        $fasilitas = $this->service->getById($id);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data'    => $fasilitas,
            ]);
        }

        return view('admin.fasilitas.show', compact('fasilitas'));
    }

    /**
     * Menampilkan formulir edit fasilitas.
     */
    public function edit(int $id): View
    {
        $fasilitas = $this->service->getById($id);
        $karyawanList = Karyawan::query()
            ->select('nipy', 'nama_lengkap', 'gelar', 'unit', 'jabatan')
            ->orderBy('nama_lengkap')
            ->get();

        return view('admin.fasilitas.edit', compact('fasilitas', 'karyawanList'));
    }

    /**
     * Memperbarui data fasilitas via Service.
     */
    public function update(FasilitasRequest $request, int $id): RedirectResponse|JsonResponse
    {
        try {
            $fasilitas = $this->service->getById($id);
            $updated = $this->service->update($fasilitas, $request->validated());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Data fasilitas '{$updated->nama_fasilitas}' berhasil diperbarui.",
                    'data'    => $updated,
                ]);
            }

            return redirect()
                ->route('fasilitas.index')
                ->with('success', "Data fasilitas '{$updated->nama_fasilitas}' berhasil diperbarui.");
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
                ->with('error', 'Gagal memperbarui data fasilitas: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus data fasilitas via Service.
     */
    public function destroy(int $id, Request $request): RedirectResponse|JsonResponse
    {
        try {
            $fasilitas = $this->service->getById($id);
            $namaFasilitas = $fasilitas->nama_fasilitas;
            $this->service->delete($fasilitas);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Fasilitas '{$namaFasilitas}' berhasil dihapus.",
                ]);
            }

            return redirect()
                ->route('fasilitas.index')
                ->with('success', "Fasilitas '{$namaFasilitas}' berhasil dihapus.");
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus data: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus data fasilitas: ' . $e->getMessage());
        }
    }

    /**
     * Toggle status pengembalian fasilitas (belum <-> sudah dikembalikan).
     */
    public function toggleStatus(Request $request, int $id): RedirectResponse|JsonResponse
    {
        try {
            $fasilitas = $this->service->getById($id);
            $catatan = $request->input('catatan');
            $updated = $this->service->toggleStatus($fasilitas, $catatan);

            $statusText = $updated->status ? 'sudah dikembalikan' : 'belum dikembalikan';

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Status fasilitas berhasil diubah menjadi: {$statusText}.",
                    'data'    => $updated,
                ]);
            }

            return redirect()
                ->back()
                ->with('success', "Status fasilitas '{$updated->nama_fasilitas}' berhasil diubah menjadi {$statusText}.");
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengubah status: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()
                ->back()
                ->with('error', 'Gagal mengubah status fasilitas: ' . $e->getMessage());
        }
    }
}
