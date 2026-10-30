<?php

namespace App\Http\Controllers\Admin\Tupoksi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Tupoksi\TupoksiFilterRequest;
use App\Http\Requests\Admin\Tupoksi\TupoksiRequest;
use App\Models\Admin\DataMaster\Karyawan;
use App\Models\Admin\DataMaster\TahunAjaran;
use App\Services\Admin\Tupoksi\TupoksiService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TupoksiController extends Controller
{
    /**
     * Dependency Injection TupoksiService.
     */
    public function __construct(
        protected TupoksiService $service
    ) {}

    /**
     * Menampilkan daftar Tupoksi dengan filter dan pagination.
     */
    public function index(TupoksiFilterRequest $request): View|JsonResponse
    {
        $filters = $request->validated();
        $perPage = (int) $request->input('per_page', 15);

        $tupoksiList = $this->service->getPaginated($filters, $perPage);
        $statistik = $this->service->getStatistik(
            isset($filters['akademik_master_tahun_ajaran_id']) ? (int) $filters['akademik_master_tahun_ajaran_id'] : null
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success'   => true,
                'data'      => $tupoksiList,
                'statistik' => $statistik,
            ]);
        }

        $tahunAjaranList = TahunAjaran::query()
            ->select('id', 'nama_tahun_ajaran', 'semester', 'is_active')
            ->orderByDesc('id')
            ->get();

        return view('admin.tupoksi.index', compact(
            'tupoksiList',
            'statistik',
            'tahunAjaranList',
            'filters'
        ));
    }

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

        return view('admin.tupoksi.create', compact('karyawanList', 'tahunAjaranList'));
    }

    /**
     * Menyimpan data Tupoksi baru beserta berkas fisik.
     */
    public function store(TupoksiRequest $request): RedirectResponse|JsonResponse
    {
        try {
            $data = $request->validated();
            $file = $request->file('berkas');

            $tupoksi = $this->service->create($data, $file);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Tupoksi jabatan {$tupoksi->jabatan} berhasil disimpan.",
                    'data'    => $tupoksi,
                ], Response::HTTP_CREATED);
            }

            return redirect()
                ->route('tupoksi.index')
                ->with('success', "Tupoksi jabatan '{$tupoksi->jabatan}' untuk karyawan NIPY '{$tupoksi->karyawan_nipy}' berhasil disimpan.");
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Menampilkan rincian detail data Tupoksi.
     */
    public function show(int $id): View|JsonResponse
    {
        $tupoksi = $this->service->getById($id);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'data'    => $tupoksi,
            ]);
        }

        return view('admin.tupoksi.show', compact('tupoksi'));
    }

    /**
     * Menampilkan formulir edit data Tupoksi.
     */
    public function edit(int $id): View
    {
        $tupoksi = $this->service->getById($id);

        $karyawanList = Karyawan::query()
            ->select('nipy', 'nama_lengkap', 'gelar', 'unit', 'jabatan')
            ->orderBy('nama_lengkap')
            ->get();

        $tahunAjaranList = TahunAjaran::query()
            ->select('id', 'nama_tahun_ajaran', 'semester', 'is_active')
            ->orderByDesc('id')
            ->get();

        return view('admin.tupoksi.edit', compact('tupoksi', 'karyawanList', 'tahunAjaranList'));
    }

    /**
     * Memperbarui data Tupoksi dan mengganti berkas jika disediakan.
     */
    public function update(TupoksiRequest $request, int $id): RedirectResponse|JsonResponse
    {
        try {
            $data = $request->validated();
            $file = $request->file('berkas');

            $tupoksi = $this->service->update($id, $data, $file);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Tupoksi ID #{$id} berhasil diperbarui.",
                    'data'    => $tupoksi,
                ]);
            }

            return redirect()
                ->route('tupoksi.index')
                ->with('success', "Tupoksi jabatan '{$tupoksi->jabatan}' berhasil diperbarui.");
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Menghapus record Tupoksi beserta berkas fisiknya.
     */
    public function destroy(int $id): RedirectResponse|JsonResponse
    {
        try {
            $this->service->delete($id);

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Data Tupoksi berhasil dihapus dari sistem.",
                ]);
            }

            return redirect()
                ->route('tupoksi.index')
                ->with('success', 'Data Tupoksi dan berkas terkait berhasil dihapus.');
        } catch (Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Menampilkan preview berkas fisik Tupoksi secara inline.
     */
    public function preview(int $id): BinaryFileResponse|StreamedResponse
    {
        return $this->service->previewBerkas($id);
    }

    /**
     * Mengunduh berkas fisik Tupoksi.
     */
    public function download(int $id): BinaryFileResponse|StreamedResponse
    {
        return $this->service->downloadBerkas($id);
    }
}
