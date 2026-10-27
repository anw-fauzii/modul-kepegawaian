<?php

namespace App\Http\Controllers\Admin\SuratKeputusan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SuratKeputusan\SuratKeputusanFilterRequest;
use App\Http\Requests\Admin\SuratKeputusan\SuratKeputusanRequest;
use App\Models\Admin\DataMaster\Karyawan;
use App\Models\Admin\DataMaster\TahunAjaran;
use App\Models\Admin\SuratKeputusan\SuratKeputusan;
use App\Services\Admin\SuratKeputusan\SuratKeputusanService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class SuratKeputusanController extends Controller
{
    /**
     * Dependency Injection pada constructor untuk arsitektur Decoupled & Thin Controller.
     */
    public function __construct(
        protected SuratKeputusanService $service
    ) {}

    /**
     * Menampilkan daftar data Surat Keputusan dengan filter dan pagination.
     */
    public function index(SuratKeputusanFilterRequest $request): View|JsonResponse
    {
        $filters = $request->validated();
        $perPage = (int) $request->input('per_page', 15);

        $suratKeputusanList = $this->service->getPaginated($filters, $perPage);
        $statistik = $this->service->getStatistik(
            isset($filters['akademik_master_tahun_ajaran_id']) ? (int) $filters['akademik_master_tahun_ajaran_id'] : null
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success'   => true,
                'data'      => $suratKeputusanList,
                'statistik' => $statistik,
            ]);
        }

        // Ambil data referensi filter secara efisien dengan selective columns
        $tahunAjaranList = TahunAjaran::query()
            ->select('id', 'nama_tahun_ajaran', 'semester', 'is_active')
            ->orderByDesc('id')
            ->get();

        return view('admin.suratKeputusan.index', compact(
            'suratKeputusanList',
            'statistik',
            'tahunAjaranList',
            'filters'
        ));
    }

    /**
     * Menampilkan formulir penambahan Surat Keputusan baru.
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

        return view('admin.suratKeputusan.create', compact('karyawanList', 'tahunAjaranList'));
    }

    /**
     * Menyimpan Surat Keputusan baru beserta unggahan berkas ke sistem.
     */
    public function store(SuratKeputusanRequest $request): RedirectResponse|JsonResponse
    {
        try {
            $data = $request->validated();
            $file = $request->file('berkas');

            $suratKeputusan = $this->service->create($data, $file);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Surat Keputusan untuk karyawan {$suratKeputusan->karyawan_nipy} berhasil disimpan.",
                    'data'    => $suratKeputusan,
                ], Response::HTTP_CREATED);
            }

            return redirect()
                ->route('surat-keputusan.index')
                ->with('success', "Surat Keputusan untuk jabatan '{$suratKeputusan->jabatan}' berhasil disimpan.");
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan Surat Keputusan: ' . $e->getMessage(),
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan Surat Keputusan: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan detail Surat Keputusan tertentu.
     */
    public function show(int $id, Request $request): View|JsonResponse
    {
        $suratKeputusan = $this->service->getById($id);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data'    => $suratKeputusan,
            ]);
        }

        return view('admin.suratKeputusan.show', compact('suratKeputusan'));
    }

    /**
     * Menampilkan formulir edit Surat Keputusan.
     */
    public function edit(int $id): View
    {
        $suratKeputusan = $this->service->getById($id);

        $karyawanList = Karyawan::query()
            ->select('nipy', 'nama_lengkap', 'gelar', 'unit', 'jabatan')
            ->orderBy('nama_lengkap')
            ->get();

        $tahunAjaranList = TahunAjaran::query()
            ->select('id', 'nama_tahun_ajaran', 'semester', 'is_active')
            ->orderByDesc('id')
            ->get();

        return view('admin.suratKeputusan.edit', compact('suratKeputusan', 'karyawanList', 'tahunAjaranList'));
    }

    /**
     * Memperbarui data Surat Keputusan dan mengganti berkas jika disediakan.
     */
    public function update(SuratKeputusanRequest $request, int $id): RedirectResponse|JsonResponse
    {
        try {
            $suratKeputusan = $this->service->getById($id);
            $data = $request->validated();
            $file = $request->file('berkas');

            $updated = $this->service->update($suratKeputusan, $data, $file);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Surat Keputusan berhasil diperbarui.',
                    'data'    => $updated,
                ]);
            }

            return redirect()
                ->route('surat-keputusan.index')
                ->with('success', 'Surat Keputusan berhasil diperbarui.');
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui Surat Keputusan: ' . $e->getMessage(),
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui Surat Keputusan: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus Surat Keputusan beserta berkas fisiknya.
     */
    public function destroy(int $id, Request $request): RedirectResponse|JsonResponse
    {
        try {
            $suratKeputusan = $this->service->getById($id);
            $this->service->delete($suratKeputusan);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Surat Keputusan berhasil dihapus beserta berkas dokumennya.',
                ]);
            }

            return redirect()
                ->route('surat-keputusan.index')
                ->with('success', 'Surat Keputusan berhasil dihapus.');
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus Surat Keputusan: ' . $e->getMessage(),
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return redirect()
                ->route('surat-keputusan.index')
                ->with('error', 'Gagal menghapus Surat Keputusan: ' . $e->getMessage());
        }
    }

    /**
     * Mengunduh berkas fisik Surat Keputusan.
     */
    public function download(int $id, Request $request): Response
    {
        try {
            $suratKeputusan = $this->service->getById($id);

            return $this->service->downloadBerkas($suratKeputusan);
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
