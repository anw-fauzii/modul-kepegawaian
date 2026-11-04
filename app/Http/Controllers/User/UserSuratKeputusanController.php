<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Admin\SuratKeputusan\SuratKeputusan;
use App\Services\User\UserBerkasService;
use App\Services\User\UserSuratKeputusanService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserSuratKeputusanController extends Controller
{
    /**
     * Injeksi dependency UserSuratKeputusanService & UserBerkasService.
     * Proteksi rute di __construct(): Hanya izinkan role [user].
     */
    public function __construct(
        protected UserSuratKeputusanService $skService,
        protected UserBerkasService $berkasService
    ) {
        $this->middleware(['auth', 'role:user|super admin']);
    }

    /**
     * Menampilkan daftar arsip SK pribadi pegawai.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $data = $this->skService->getUserSuratKeputusanData($request->user(), $request->all());

        return view('user.surat-keputusan.index', $data);
    }

    /**
     * Menampilkan preview berkas SK secara inline.
     *
     * @param Request $request
     * @param SuratKeputusan $surat_keputusan
     * @return BinaryFileResponse|StreamedResponse
     */
    public function preview(Request $request, SuratKeputusan $surat_keputusan): BinaryFileResponse|StreamedResponse
    {
        return $this->berkasService->preview($request->user(), $surat_keputusan, 'berkas');
    }

    /**
     * Mengunduh berkas SK pribadi.
     *
     * @param Request $request
     * @param SuratKeputusan $surat_keputusan
     * @return BinaryFileResponse|StreamedResponse
     */
    public function download(Request $request, SuratKeputusan $surat_keputusan): BinaryFileResponse|StreamedResponse
    {
        return $this->berkasService->download($request->user(), $surat_keputusan, 'berkas');
    }
}

