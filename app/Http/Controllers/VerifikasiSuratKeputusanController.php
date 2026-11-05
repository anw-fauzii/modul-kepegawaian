<?php

namespace App\Http\Controllers;

use App\Models\Admin\SuratKeputusan\SuratKeputusan;

class VerifikasiSuratKeputusanController extends Controller
{
    /**
     * Halaman publik verifikasi keaslian SK (diakses dari QR code pada dokumen).
     */
    public function show(string $kode)
    {
        $sk = SuratKeputusan::with([
            'karyawan:nipy,nama_lengkap,gelar,unit',
            'tahunAjaran:id,nama_tahun_ajaran,semester',
        ])->where('kode_verifikasi', strtoupper($kode))->first();

        return response()->view('verifikasi.surat-keputusan', ['sk' => $sk, 'kode' => strtoupper($kode)], $sk ? 200 : 404);
    }
}
