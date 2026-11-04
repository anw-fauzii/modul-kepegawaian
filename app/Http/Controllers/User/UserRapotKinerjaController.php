<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Admin\RapotKinerja\RapotKinerja;
use App\Services\User\UserBerkasService;
use App\Services\User\UserRapotKinerjaService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserRapotKinerjaController extends Controller
{
    /**
     * Injeksi dependency UserRapotKinerjaService & UserBerkasService.
     * Proteksi rute di __construct(): Hanya izinkan role [user].
     */
    public function __construct(
        protected UserRapotKinerjaService $rapotService,
        protected UserBerkasService $berkasService
    ) {
        $this->middleware(['auth', 'role:user|super admin']);
    }

    /**
     * Menampilkan daftar dokumen rapot kinerja pribadi.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $data = $this->rapotService->getUserRapotKinerjaData($request->user(), $request->all());

        return view('user.rapot-kinerja.index', $data);
    }

    /**
     * Menampilkan preview berkas rapot kinerja secara inline.
     *
     * @param Request $request
     * @param RapotKinerja $rapot_kinerja
     * @return BinaryFileResponse|StreamedResponse
     */
    public function preview(Request $request, RapotKinerja $rapot_kinerja): BinaryFileResponse|StreamedResponse
    {
        return $this->berkasService->preview($request->user(), $rapot_kinerja, 'berkas');
    }

    /**
     * Mengunduh berkas fisik rapot kinerja.
     *
     * @param Request $request
     * @param RapotKinerja $rapot_kinerja
     * @return BinaryFileResponse|StreamedResponse
     */
    public function download(Request $request, RapotKinerja $rapot_kinerja): BinaryFileResponse|StreamedResponse
    {
        return $this->berkasService->download($request->user(), $rapot_kinerja, 'berkas');
    }
}

