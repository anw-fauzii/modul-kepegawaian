<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Admin\Penggajian\Penggajian;
use App\Services\User\UserPenggajianService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserPenggajianController extends Controller
{
    /**
     * Injeksi dependency UserPenggajianService untuk menerapkan pola Thin Controller.
     * Proteksi rute di __construct(): Hanya izinkan role [user].
     */
    public function __construct(
        protected UserPenggajianService $penggajianService
    ) {
        $this->middleware(['auth', 'role:user|super admin']);
    }

    /**
     * Menampilkan daftar riwayat slip gaji pribadi dan ringkasan penerimaan user yang sedang login.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $data = $this->penggajianService->getUserPenggajianData($request->user(), $request->all());

        return view('user.penggajian.index', $data);
    }

    /**
     * Menampilkan rincian slip gaji mandiri untuk dicetak (Digital & Print Slip).
     *
     * @param Request $request
     * @param Penggajian $penggajian
     * @return View
     */
    public function show(Request $request, Penggajian $penggajian): View
    {
        $data = $this->penggajianService->getSlipDetail($request->user(), $penggajian);

        return view('user.penggajian.show', $data);
    }
}

