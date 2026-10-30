<?php

namespace App\Http\Controllers\Admin\Diklat;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Diklat\DiklatFilterRequest;
use App\Http\Requests\Admin\Diklat\DiklatRequest;
use App\Models\Admin\DataMaster\Karyawan;
use App\Models\Admin\Kegiatan\KegiatanLembaga;
use App\Services\Admin\Diklat\DiklatService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DiklatController extends Controller
{
    /**
     * Dependency Injection DiklatService.
     */
    public function __construct(
        protected DiklatService $service
    ) {}

    /**
     * Menampilkan daftar data Diklat dengan filter dan pagination.
     */
    public function index(DiklatFilterRequest $request): View|JsonResponse
    {
        $filters = $request->validated();
        $perPage = (int) $request->input('per_page', 15);

        $diklatList = $this->service->getPaginated($filters, $perPage);
        $statistik = $this->service->getStatistik();

        if ($request->wantsJson()) {
            return response()->json([
                'success'   => true,
                'data'      => $diklatList,
                'statistik' => $statistik,
            ]);
        }

        $karyawanList = Karyawan::query()
            ->select('nipy', 'nama_lengkap', 'gelar')
            ->orderBy('nama_lengkap')
            ->get();

        return view('admin.diklat.index', compact(
            'diklatList',
            'statistik',
            'karyawanList',
            'filters'
        ));
    }

    /**
     * Menampilkan formulir tambah data Diklat baru.
     */
    public function create(): View
    {
        $karyawanList = Karyawan::query()
            ->select('nipy', 'nama_lengkap', 'gelar', 'unit', 'jabatan')
            ->orderBy('nama_lengkap')
            ->get();

        // Hanya mengambil kegiatan lembaga yang bertipe pelatihan (bukan rapat)
        $kegiatanPelatihanList = $this->service->getKegiatanPelatihanList();

        return view('admin.diklat.create', compact('karyawanList', 'kegiatanPelatihanList'));
    }

    /**
     * Menyimpan data Diklat baru ke database.
     */
    public function store(DiklatRequest $request): RedirectResponse|JsonResponse
    {
        try {
            $data = $request->validated();
            $file = $request->file('berkas');

            $diklat = $this->service->create($data, $file);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Data Diklat '{$diklat->nama_kegiatan}' berhasil disimpan.",
                    'data'    => $diklat,
                ], Response::HTTP_CREATED);
            }

            return redirect()
                ->route('diklat.index')
                ->with('success', "Data Diklat '{$diklat->nama_kegiatan}' untuk karyawan NIPY '{$diklat->karyawan_nipy}' berhasil disimpan.");
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
     * Menampilkan rincian detail data Diklat.
     */
    public function show(int $id): View|JsonResponse
    {
        $diklat = $this->service->getById($id);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'data'    => $diklat,
            ]);
        }

        return view('admin.diklat.show', compact('diklat'));
    }

    /**
     * Menampilkan formulir edit data Diklat.
     */
    public function edit(int $id): View
    {
        $diklat = $this->service->getById($id);

        $karyawanList = Karyawan::query()
            ->select('nipy', 'nama_lengkap', 'gelar', 'unit', 'jabatan')
            ->orderBy('nama_lengkap')
            ->get();

        $kegiatanPelatihanList = $this->service->getKegiatanPelatihanList();

        return view('admin.diklat.edit', compact('diklat', 'karyawanList', 'kegiatanPelatihanList'));
    }

    /**
     * Memperbarui data Diklat dan mengelola file sertifikat.
     */
    public function update(DiklatRequest $request, int $id): RedirectResponse|JsonResponse
    {
        try {
            $data = $request->validated();
            $file = $request->file('berkas');

            $diklat = $this->service->update($id, $data, $file);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Data Diklat ID #{$id} berhasil diperbarui.",
                    'data'    => $diklat,
                ]);
            }

            return redirect()
                ->route('diklat.index')
                ->with('success', "Data Diklat '{$diklat->nama_kegiatan}' berhasil diperbarui.");
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
     * Menghapus record Diklat beserta berkas fisiknya.
     */
    public function destroy(int $id): RedirectResponse|JsonResponse
    {
        try {
            $this->service->delete($id);

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data Diklat berhasil dihapus dari sistem.',
                ]);
            }

            return redirect()
                ->route('diklat.index')
                ->with('success', 'Data Diklat dan berkas sertifikat berhasil dihapus.');
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
     * Menampilkan preview berkas sertifikat Diklat secara inline.
     */
    public function preview(int $id): BinaryFileResponse|StreamedResponse
    {
        return $this->service->previewBerkas($id);
    }

    /**
     * Mengunduh berkas fisik sertifikat / bukti diklat.
     */
    public function download(int $id): BinaryFileResponse|StreamedResponse
    {
        return $this->service->downloadBerkas($id);
    }

    /**
     * Mengambil detail kegiatan lembaga (khusus pelatihan) untuk auto-fill pada form input.
     */
    public function getKegiatanDetail(int $id): JsonResponse
    {
        $kegiatan = KegiatanLembaga::where('jenis_kegiatan', 'pelatihan')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'nama_kegiatan' => $kegiatan->nama_kegiatan,
                'pelaksana'     => $kegiatan->pengisi ?: 'Prima Insani',
                'tanggal'       => $kegiatan->tanggal->format('Y-m-d'),
                'lokasi'        => $kegiatan->lokasi,
                'materi'        => $kegiatan->deskripsi,
            ],
        ]);
    }
}
