<?php

namespace App\Http\Controllers\Admin\Kegiatan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Kegiatan\KegiatanFilterRequest;
use App\Http\Requests\Admin\Kegiatan\KegiatanLembagaRequest;
use App\Http\Requests\Admin\Kegiatan\KehadiranManualRequest;
use App\Http\Requests\Admin\Kegiatan\ScanQrKegiatanRequest;
use App\Models\Admin\DataMaster\Karyawan;
use App\Models\Admin\DataMaster\TahunAjaran;
use App\Models\Admin\Kegiatan\KegiatanLembaga;
use App\Services\Admin\Kegiatan\KegiatanLembagaService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KegiatanLembagaController extends Controller
{
    /**
     * Dependency Injection pada constructor untuk memastikan Thin Controller & Decoupled Architecture.
     */
    public function __construct(
        protected KegiatanLembagaService $service
    ) {}

    /**
     * Menampilkan daftar kegiatan lembaga dengan pagination & filter performan tinggi.
     */
    public function index(KegiatanFilterRequest $request): View
    {
        $filters = $request->validated();
        $perPage = (int) $request->input('per_page', 15);

        $kegiatanList = $this->service->getPaginatedKegiatan($filters, $perPage);
        $tahunAjaranList = TahunAjaran::select('id', 'nama_tahun_ajaran', 'semester')->orderBy('id', 'desc')->get();

        return view('admin.kegiatan.index', compact('kegiatanList', 'tahunAjaranList'));
    }

    /**
     * Menampilkan formulir pembuatan kegiatan lembaga baru.
     */
    public function create(): View
    {
        $tahunAjaranList = TahunAjaran::select('id', 'nama_tahun_ajaran', 'semester')->orderBy('id', 'desc')->get();

        return view('admin.kegiatan.create', compact('tahunAjaranList'));
    }

    /**
     * Menyimpan data kegiatan baru ke database melalui Service Class.
     */
    public function store(KegiatanLembagaRequest $request): RedirectResponse
    {
        try {
            $kegiatan = $this->service->createKegiatan($request->validated());

            return redirect()
                ->route('kegiatan.show', $kegiatan->id)
                ->with('success', "Kegiatan '{$kegiatan->nama_kegiatan}' berhasil dibuat dan QR Code siap digunakan.");
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal membuat kegiatan: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan halaman rekapitulasi kehadiran dan monitoring real-time kegiatan.
     */
    public function show(KegiatanLembaga $kegiatan, Request $request): View
    {
        $data = $this->service->getRekapDetailKegiatan($kegiatan, $request->only(['status', 'search']));

        // Ambil daftar karyawan untuk modal presensi manual jika dibutuhkan
        $karyawanList = Karyawan::select('nipy', 'nama_lengkap', 'gelar', 'unit')
            ->orderBy('nama_lengkap')
            ->get();

        return view('admin.kegiatan.show', array_merge($data, compact('karyawanList')));
    }

    /**
     * Menampilkan formulir edit kegiatan lembaga.
     */
    public function edit(KegiatanLembaga $kegiatan): View
    {
        $tahunAjaranList = TahunAjaran::select('id', 'nama_tahun_ajaran', 'semester')->orderBy('id', 'desc')->get();

        return view('admin.kegiatan.edit', compact('kegiatan', 'tahunAjaranList'));
    }

    /**
     * Memperbarui informasi kegiatan lembaga.
     */
    public function update(KegiatanLembagaRequest $request, KegiatanLembaga $kegiatan): RedirectResponse
    {
        try {
            $this->service->updateKegiatan($kegiatan, $request->validated());

            return redirect()
                ->route('kegiatan.index')
                ->with('success', "Data kegiatan '{$kegiatan->nama_kegiatan}' berhasil diperbarui.");
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui kegiatan: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus kegiatan lembaga beserta seluruh data kehadirannya.
     */
    public function destroy(KegiatanLembaga $kegiatan): RedirectResponse
    {
        try {
            $nama = $kegiatan->nama_kegiatan;
            $this->service->deleteKegiatan($kegiatan);

            return redirect()
                ->route('kegiatan.index')
                ->with('success', "Kegiatan '{$nama}' berhasil dihapus.");
        } catch (Exception $e) {
            return redirect()
                ->route('kegiatan.index')
                ->with('error', 'Gagal menghapus kegiatan: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan layar QR Code Fullscreen untuk proyektor / monitor aula.
     */
    public function showQr(KegiatanLembaga $kegiatan): View
    {
        $kegiatan->loadMissing('tahunAjaran');

        return view('admin.kegiatan.qr_display', compact('kegiatan'));
    }

    /**
     * Me-refresh / meregenerasi token QR Code kegiatan jika terjadi kebocoran link.
     */
    public function regenerateQr(KegiatanLembaga $kegiatan): RedirectResponse
    {
        try {
            $this->service->regenerateQrToken($kegiatan);

            return redirect()
                ->back()
                ->with('success', 'Token QR Code berhasil diperbarui. QR Code lama sudah tidak berlaku.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal memperbarui token QR: ' . $e->getMessage());
        }
    }

    /**
     * Buka / Tutup gerbang presensi untuk kegiatan terkait.
     */
    public function toggleStatus(KegiatanLembaga $kegiatan): RedirectResponse
    {
        try {
            $updated = $this->service->toggleActiveStatus($kegiatan);
            $pesan = $updated->is_active ? 'Gerbang presensi telah dibuka.' : 'Gerbang presensi telah ditutup.';

            return redirect()->back()->with('success', $pesan);
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }

    /**
     * Endpoint API / AJAX untuk memproses Scan QR Code dari kamera smartphone atau scanner barcode.
     */
    public function scanQr(ScanQrKegiatanRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $result = $this->service->recordPresensiByQr(
                qrToken: $validated['qr_token'],
                karyawanNipy: $validated['karyawan_nipy'],
                catatan: $validated['catatan'] ?? null,
                deviceInfo: $validated['device_info'] ?? null,
                metode: 'qr_scan'
            );

            return response()->json([
                'success' => true,
                'status'  => $result['status'],
                'message' => $result['message'],
                'data'    => [
                    'karyawan'   => [
                        'nipy'         => $result['karyawan']->nipy,
                        'nama_lengkap' => $result['karyawan']->nama_lengkap,
                        'unit'         => $result['karyawan']->unit,
                        'jabatan'      => $result['karyawan']->jabatan,
                    ],
                    'waktu_scan' => $result['kehadiran']->waktu_scan->format('H:i:s d-m-Y'),
                ],
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 403);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Menangani input kehadiran manual secara single / massal dari admin.
     */
    public function storeManual(KehadiranManualRequest $request, KegiatanLembaga $kegiatan): RedirectResponse
    {
        try {
            $count = $this->service->recordPresensiManualBulk(
                kegiatan: $kegiatan,
                nipyList: $request->input('karyawan_nipy'),
                status: $request->input('status', 'hadir'),
                catatan: $request->input('catatan')
            );

            return redirect()
                ->back()
                ->with('success', "Berhasil mencatat kehadiran manual untuk {$count} karyawan.");
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal mencatat presensi manual: ' . $e->getMessage());
        }
    }

    /**
     * Halaman antarmuka pemindai QR Code khusus karyawan (Mobile Scanner).
     * Membuka kamera smartphone karyawan untuk memindai QR code kegiatan di layar proyektor.
     */
    public function scannerUser(): View
    {
        $user = auth()->user();
        return view('admin.kegiatan.user_scanner', compact('user'));
    }

    /**
     * Endpoint Direct URL jika karyawan memindai QR Code proyektor menggunakan kamera bawaan HP / Google Lens.
     * Mengarahkan langsung ke sistem untuk pencatatan presensi otomatis dan menampilkan hasil.
     */
    public function presensiDirect(string $token): View
    {
        $user = auth()->user();
        $nipy = $user->no_induk ?? $user->nipy ?? null;

        if (! $nipy) {
            return view('admin.kegiatan.presensi_result', [
                'success' => false,
                'status'  => 'error',
                'message' => 'Akun login Anda (' . ($user->email ?? $user->name ?? 'User') . ') belum terhubung dengan NIPY Karyawan di sistem SIPIT.',
                'kegiatan' => null,
                'karyawan' => null,
                'kehadiran' => null,
            ]);
        }

        try {
            $result = $this->service->recordPresensiByQr(
                qrToken: $token,
                karyawanNipy: $nipy,
                catatan: 'Presensi via Scan Kamera Langsung (Direct URL)',
                deviceInfo: request()->userAgent(),
                metode: 'qr_scan'
            );

            return view('admin.kegiatan.presensi_result', [
                'success'   => true,
                'status'    => $result['status'],
                'message'   => $result['message'],
                'kegiatan'  => $result['kegiatan'],
                'karyawan'  => $result['karyawan'],
                'kehadiran' => $result['kehadiran'],
            ]);
        } catch (\InvalidArgumentException $e) {
            return view('admin.kegiatan.presensi_result', [
                'success' => false,
                'status'  => 'invalid',
                'message' => $e->getMessage(),
                'kegiatan' => null,
                'karyawan' => null,
                'kehadiran' => null,
            ]);
        } catch (\RuntimeException $e) {
            return view('admin.kegiatan.presensi_result', [
                'success' => false,
                'status'  => 'closed',
                'message' => $e->getMessage(),
                'kegiatan' => null,
                'karyawan' => null,
                'kehadiran' => null,
            ]);
        } catch (Exception $e) {
            return view('admin.kegiatan.presensi_result', [
                'success' => false,
                'status'  => 'error',
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
                'kegiatan' => null,
                'karyawan' => null,
                'kehadiran' => null,
            ]);
        }
    }
}
