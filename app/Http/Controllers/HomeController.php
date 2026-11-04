<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Injeksi dependency DashboardService untuk arsitektur Thin Controller.
     */
    public function __construct(
        protected DashboardService $dashboardService
    ) {
        $this->middleware('auth');
    }

    /**
     * Menampilkan dashboard utama kepegawaian dengan ringkasan analitik real-time.
     */
    public function index(Request $request): Renderable
    {
        if ($request->query('view') === 'user') {
            return $this->userDashboard($request);
        }

        $data = $this->dashboardService->getAdminDashboardData();

        return view('home', $data);
    }

    /**
     * Menampilkan dashboard mandiri personal untuk user / pegawai / guru.
     */
    public function userDashboard(Request $request): Renderable
    {
        $data = $this->dashboardService->getUserDashboardData($request->user());

        return view('home_user', $data);
    }
}
