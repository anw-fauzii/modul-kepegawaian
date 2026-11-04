<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Admin\SertifikatPenghargaan\SertifikatPenghargaan;
use App\Services\User\UserBerkasService;
use App\Services\User\UserPenghargaanService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserPenghargaanController extends Controller
{
    /**
     * Injeksi dependency UserPenghargaanService & UserBerkasService.
     * Proteksi rute di __construct(): Hanya izinkan role [user].
     */
    public function __construct(
        protected UserPenghargaanService $penghargaanService,
        protected UserBerkasService $berkasService
    ) {
        $this->middleware(['auth', 'role:user']);
    }

    /**
     * Menampilkan daftar sertifikat penghargaan pribadi.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $data = $this->penghargaanService->getUserPenghargaanData($request->user(), $request->all());

        return view('user.penghargaan.index', $data);
    }

    /**
     * Menampilkan preview berkas sertifikat penghargaan secara inline.
     *
     * @param Request $request
     * @param SertifikatPenghargaan $penghargaan
     * @return BinaryFileResponse|StreamedResponse
     */
    public function preview(Request $request, SertifikatPenghargaan $penghargaan): BinaryFileResponse|StreamedResponse
    {
        return $this->berkasService->preview($request->user(), $penghargaan, 'berkas');
    }

    /**
     * Mengunduh berkas fisik sertifikat penghargaan pribadi.
     *
     * @param Request $request
     * @param SertifikatPenghargaan $penghargaan
     * @return BinaryFileResponse|StreamedResponse
     */
    public function download(Request $request, SertifikatPenghargaan $penghargaan): BinaryFileResponse|StreamedResponse
    {
        return $this->berkasService->download($request->user(), $penghargaan, 'berkas');
    }
}

