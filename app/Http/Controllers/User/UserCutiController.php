<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserCutiRequest;
use App\Models\Admin\Cuti\Cuti;
use App\Services\User\UserCutiService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserCutiController extends Controller
{
    /**
     * Injeksi dependency UserCutiService untuk menerapkan pola Thin Controller.
     * Proteksi rute di __construct(): Hanya izinkan role [user].
     */
    public function __construct(
        protected UserCutiService $cutiService
    ) {
        $this->middleware(['auth', 'role:user|super admin']);
    }

    /**
     * Menampilkan riwayat pengajuan cuti pribadi dan kartu statistik KPI pegawai.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $data = $this->cutiService->getUserCutiData($request->user(), $request->all());

        return view('user.cuti.index', $data);
    }

    /**
     * Menampilkan formulir permohonan pengajuan cuti baru.
     *
     * @param Request $request
     * @return View
     */
    public function create(Request $request): View
    {
        $data = $this->cutiService->getFormData($request->user());

        return view('user.cuti.create', $data);
    }

    /**
     * Menyimpan permohonan cuti baru atas nama pegawai yang login.
     *
     * @param UserCutiRequest $request
     * @return RedirectResponse
     */
    public function store(UserCutiRequest $request): RedirectResponse
    {
        try {
            $cuti = $this->cutiService->storeCuti($request->user(), $request->validated());

            return redirect()
                ->route('user.cuti.index')
                ->with('success', "Permohonan cuti ({$cuti->jenisCuti?->nama}) untuk {$cuti->jumlah_hari} hari kerja berhasil diajukan dan sedang menunggu persetujuan (ACC).");
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal mengajukan cuti: ' . $e->getMessage());
        }
    }

    /**
     * Membatalkan permohonan cuti yang belum disetujui (ACC) oleh admin.
     *
     * @param Request $request
     * @param Cuti $cuti
     * @return RedirectResponse
     */
    public function destroy(Request $request, Cuti $cuti): RedirectResponse
    {
        try {
            $this->cutiService->batalkanCuti($request->user(), $cuti);

            return redirect()
                ->route('user.cuti.index')
                ->with('success', 'Pengajuan cuti berhasil dibatalkan.');
        } catch (Exception $e) {
            return redirect()
                ->route('user.cuti.index')
                ->with('error', 'Gagal membatalkan pengajuan cuti: ' . $e->getMessage());
        }
    }
}

