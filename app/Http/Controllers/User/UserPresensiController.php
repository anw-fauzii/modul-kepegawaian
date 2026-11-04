<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\User\UserPresensiService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserPresensiController extends Controller
{
    /**
     * Injeksi dependency UserPresensiService untuk menerapkan pola Thin Controller.
     * Proteksi rute di __construct(): Hanya izinkan role [user].
     */
    public function __construct(
        protected UserPresensiService $presensiService
    ) {
        $this->middleware(['auth', 'role:user|super admin']);
    }

    /**
     * Menampilkan riwayat presensi mandiri dan rekapitulasi bulanan user yang sedang login.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $data = $this->presensiService->getUserPresensiData($request->user(), $request->all());

        return view('user.presensi.index', $data);
    }
}
