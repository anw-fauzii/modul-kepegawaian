<?php

namespace App\Http\Controllers;

use App\Models\Admin\DataMaster\Karyawan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Resolve profil Karyawan yang tertaut dengan akun User yang sedang login.
     */
    protected function resolveKaryawan($user): ?Karyawan
    {
        if (! $user) {
            return null;
        }

        $userNipy = trim((string) ($user->no_induk ?? ''));

        $query = Karyawan::with([
            'user',
            'kategori',
            'jenjangPendidikan',
            'jamKerja',
            'riwayatJabatan' => fn ($q) => $q->with('kategori')
                ->orderByDesc('is_aktif')
                ->orderByDesc('tanggal_mulai')
                ->orderByDesc('id'),
        ])->withCount([
            'riwayatJabatan',
            'suratKeputusan',
            'diklat',
            'sertifikatPenghargaan',
            'fasilitas',
            'rapotKinerja',
            'tupoksi',
            'cuti',
        ]);

        if ($userNipy !== '') {
            $karyawan = (clone $query)
                ->where(function ($q) use ($userNipy) {
                    $q->where('nipy', $userNipy)
                        ->orWhere('nipy', 'like', $userNipy . '-%');
                })
                ->first();

            if ($karyawan) {
                return $karyawan;
            }
        }

        return null;
    }

    /**
     * Tampilkan halaman Detail Profil & Kepegawaian Akun Saya.
     */
    public function index(): View
    {
        $user = auth()->user();
        $karyawan = $this->resolveKaryawan($user);

        return view('profile.index', compact(
            'user',
            'karyawan'
        ));
    }

    /**
     * Perbarui kata sandi (password) akun yang sedang login.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $rules = [
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ];

        if (! empty($user->password)) {
            $rules['current_password'] = ['required', 'string'];
        }

        $validated = $request->validate($rules, [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'password.required'         => 'Password baru wajib diisi.',
            'password.min'              => 'Password baru minimal terdiri dari 6 karakter.',
            'password.confirmed'        => 'Konfirmasi password baru tidak cocok.',
        ]);

        if (! empty($user->password) && ! Hash::check($validated['current_password'], $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Password saat ini yang Anda masukkan tidak sesuai.'])
                ->with('active_tab', 'password');
        }

        $user->update([
            'password' => $validated['password'],
        ]);

        return redirect()
            ->route('profile.index')
            ->with('success', 'Password akun Anda berhasil diperbarui.')
            ->with('active_tab', 'password');
    }
}
