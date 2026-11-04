<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\User\UserFasilitasService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserFasilitasController extends Controller
{
    /**
     * Injeksi dependency UserFasilitasService.
     * Proteksi rute di __construct(): Hanya izinkan role [user].
     */
    public function __construct(
        protected UserFasilitasService $fasilitasService
    ) {
        $this->middleware(['auth', 'role:user']);
    }

    /**
     * Menampilkan daftar fasilitas inventaris pribadi dan kartu statistik KPI pegawai.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $data = $this->fasilitasService->getUserFasilitasData($request->user(), $request->all());

        return view('user.fasilitas.index', $data);
    }
}

