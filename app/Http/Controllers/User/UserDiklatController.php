<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Admin\Diklat\Diklat;
use App\Services\User\UserBerkasService;
use App\Services\User\UserDiklatService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserDiklatController extends Controller
{
    /**
     * Injeksi dependency UserDiklatService & UserBerkasService.
     * Proteksi rute di __construct(): Hanya izinkan role [user].
     */
    public function __construct(
        protected UserDiklatService $diklatService,
        protected UserBerkasService $berkasService
    ) {
        $this->middleware(['auth', 'role:user']);
    }

    /**
     * Menampilkan riwayat keikutsertaan diklat & pelatihan pribadi.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $data = $this->diklatService->getUserDiklatData($request->user(), $request->all());

        return view('user.diklat.index', $data);
    }

    /**
     * Menampilkan preview berkas sertifikat diklat secara inline.
     *
     * @param Request $request
     * @param Diklat $diklat
     * @return BinaryFileResponse|StreamedResponse
     */
    public function preview(Request $request, Diklat $diklat): BinaryFileResponse|StreamedResponse
    {
        return $this->berkasService->preview($request->user(), $diklat, 'berkas');
    }

    /**
     * Mengunduh berkas sertifikat diklat pribadi.
     *
     * @param Request $request
     * @param Diklat $diklat
     * @return BinaryFileResponse|StreamedResponse
     */
    public function download(Request $request, Diklat $diklat): BinaryFileResponse|StreamedResponse
    {
        return $this->berkasService->download($request->user(), $diklat, 'berkas');
    }
}

