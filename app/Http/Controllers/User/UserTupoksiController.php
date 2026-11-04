<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Admin\Tupoksi\Tupoksi;
use App\Services\User\UserBerkasService;
use App\Services\User\UserTupoksiService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserTupoksiController extends Controller
{
    /**
     * Injeksi dependency UserTupoksiService & UserBerkasService.
     * Proteksi rute di __construct(): Hanya izinkan role [user].
     */
    public function __construct(
        protected UserTupoksiService $tupoksiService,
        protected UserBerkasService $berkasService
    ) {
        $this->middleware(['auth', 'role:user']);
    }

    /**
     * Menampilkan daftar dokumen Tupoksi pribadi.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $data = $this->tupoksiService->getUserTupoksiData($request->user(), $request->all());

        return view('user.tupoksi.index', $data);
    }

    /**
     * Menampilkan preview dokumen Tupoksi secara inline.
     *
     * @param Request $request
     * @param Tupoksi $tupoksi
     * @return BinaryFileResponse|StreamedResponse
     */
    public function preview(Request $request, Tupoksi $tupoksi): BinaryFileResponse|StreamedResponse
    {
        return $this->berkasService->preview($request->user(), $tupoksi, 'berkas');
    }

    /**
     * Mengunduh berkas fisik Tupoksi.
     *
     * @param Request $request
     * @param Tupoksi $tupoksi
     * @return BinaryFileResponse|StreamedResponse
     */
    public function download(Request $request, Tupoksi $tupoksi): BinaryFileResponse|StreamedResponse
    {
        return $this->berkasService->download($request->user(), $tupoksi, 'berkas');
    }
}

