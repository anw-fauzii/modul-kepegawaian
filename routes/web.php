<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SSOController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/sso/login', [SSOController::class, 'getLogin'])->name('sso.login');
Route::get('/callback', [SSOController::class, 'getCallback'])->name('sso.callback');
Route::get('/sso/authuser', [SSOController::class, 'connectUser'])->name('sso.authuser');

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/data-master/karyawan/{karyawan}/foto', [App\Http\Controllers\Admin\DataMaster\KaryawanController::class, 'foto'])->name('karyawan.foto');
});

Route::middleware(['auth', 'sso.check', 'nocache'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class,'index'])->name('home');
    Route::get('/dashboard/user', [App\Http\Controllers\HomeController::class, 'userDashboard'])->name('dashboard.user');
    Route::get('/user/presensi', [App\Http\Controllers\User\UserPresensiController::class, 'index'])->name('user.presensi.index');
    Route::get('/user/penggajian', [App\Http\Controllers\User\UserPenggajianController::class, 'index'])->name('user.penggajian.index');
    Route::get('/user/penggajian/{penggajian}', [App\Http\Controllers\User\UserPenggajianController::class, 'show'])->name('user.penggajian.show');
    Route::get('/profil', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profil/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::resource('/data-master/kategori', App\Http\Controllers\Admin\DataMaster\KategoriController::class);      
    Route::resource('/data-master/jam-kerja', App\Http\Controllers\Admin\DataMaster\JamKerjaController::class);
    Route::resource('/data-master/bulan', App\Http\Controllers\Admin\DataMaster\BulanController::class);
    Route::resource('/data-master/jenis-cuti', App\Http\Controllers\Admin\DataMaster\JenisCutiController::class);
    Route::post('/data-master/hari-libur/sync', [App\Http\Controllers\Admin\DataMaster\HariLiburController::class, 'sync'])->name('hari-libur.sync');
    Route::resource('/data-master/hari-libur', App\Http\Controllers\Admin\DataMaster\HariLiburController::class);
    Route::get('/data-master/karyawan/template', [App\Http\Controllers\Admin\DataMaster\KaryawanController::class, 'template'])->name('karyawan.template');
    Route::get('/data-master/karyawan/export', [App\Http\Controllers\Admin\DataMaster\KaryawanController::class, 'export'])->name('karyawan.export');
    Route::post('/data-master/karyawan/import', [App\Http\Controllers\Admin\DataMaster\KaryawanController::class, 'import'])->name('karyawan.import');
    Route::get('/data-master/karyawan/unit/{unit}', [App\Http\Controllers\Admin\DataMaster\KaryawanController::class, 'byUnit'])->name('karyawan.unit');
    Route::post('/data-master/karyawan/{karyawan}/riwayat-jabatan', [App\Http\Controllers\Admin\DataMaster\KaryawanController::class, 'storeRiwayatJabatan'])->name('karyawan.riwayat-jabatan.store');
    Route::delete('/data-master/karyawan/{karyawan}/riwayat-jabatan/{riwayatJabatan}', [App\Http\Controllers\Admin\DataMaster\KaryawanController::class, 'destroyRiwayatJabatan'])->name('karyawan.riwayat-jabatan.destroy');
    Route::resource('/data-master/karyawan', App\Http\Controllers\Admin\DataMaster\KaryawanController::class);

    // Modul Presensi Karyawan
    Route::get('/presensi/statistik-tahunan', [App\Http\Controllers\Admin\Presensi\PresensiController::class, 'statistikTahunan'])->name('presensi.statistik-tahunan');
    Route::get('/presensi/rekap', [App\Http\Controllers\Admin\Presensi\PresensiController::class, 'rekap'])->name('presensi.rekap');
    Route::get('/presensi/matriks/pdf', [App\Http\Controllers\Admin\Presensi\PresensiController::class, 'matriksPdf'])->name('presensi.matriks.pdf');
    Route::get('/presensi/matriks', [App\Http\Controllers\Admin\Presensi\PresensiController::class, 'matriks'])->name('presensi.matriks');
    Route::post('/presensi/bulk', [App\Http\Controllers\Admin\Presensi\PresensiController::class, 'storeBulk'])->name('presensi.store-bulk');
    Route::post('/presensi/sync-fingerspot', [App\Http\Controllers\Admin\Presensi\PresensiController::class, 'syncFingerspot'])->name('presensi.sync-fingerspot');
    Route::patch('/presensi/my-shaum', [App\Http\Controllers\Admin\Presensi\PresensiController::class, 'updateMyShaum'])->name('presensi.my-shaum');
    Route::post('/presensi/my-shaum/toggle', [App\Http\Controllers\Admin\Presensi\PresensiController::class, 'toggleMyShaum'])->name('presensi.my-shaum.toggle');
    Route::patch('/presensi/{presensi}/shaum', [App\Http\Controllers\Admin\Presensi\PresensiController::class, 'updateShaum'])->name('presensi.update-shaum');
    Route::post('/presensi/{presensi}/toggle-shaum', [App\Http\Controllers\Admin\Presensi\PresensiController::class, 'toggleShaum'])->name('presensi.toggle-shaum');
    Route::resource('/presensi', App\Http\Controllers\Admin\Presensi\PresensiController::class);

    // Modul Pengajuan & Form Cuti Karyawan
    Route::post('/cuti/{cuti}/toggle-acc', [App\Http\Controllers\Admin\Cuti\CutiController::class, 'toggleAcc'])->name('cuti.toggle-acc');
    Route::resource('/cuti', App\Http\Controllers\Admin\Cuti\CutiController::class);

    // Modul Kegiatan Lembaga & Presensi QR Code
    Route::get('/kegiatan-scanner', [App\Http\Controllers\Admin\Kegiatan\KegiatanLembagaController::class, 'scannerUser'])->name('kegiatan.scanner');
    Route::get('/kegiatan/presensi-qr/{token}', [App\Http\Controllers\Admin\Kegiatan\KegiatanLembagaController::class, 'presensiDirect'])->name('kegiatan.presensi-direct');
    Route::post('/kegiatan/scan', [App\Http\Controllers\Admin\Kegiatan\KegiatanLembagaController::class, 'scanQr'])->name('kegiatan.scan');
    Route::get('/kegiatan/{kegiatan}/qr', [App\Http\Controllers\Admin\Kegiatan\KegiatanLembagaController::class, 'showQr'])->name('kegiatan.qr');
    Route::post('/kegiatan/{kegiatan}/regenerate-qr', [App\Http\Controllers\Admin\Kegiatan\KegiatanLembagaController::class, 'regenerateQr'])->name('kegiatan.regenerate-qr');
    Route::post('/kegiatan/{kegiatan}/toggle-status', [App\Http\Controllers\Admin\Kegiatan\KegiatanLembagaController::class, 'toggleStatus'])->name('kegiatan.toggle-status');
    Route::post('/kegiatan/{kegiatan}/manual', [App\Http\Controllers\Admin\Kegiatan\KegiatanLembagaController::class, 'storeManual'])->name('kegiatan.store-manual');
    Route::resource('/kegiatan', App\Http\Controllers\Admin\Kegiatan\KegiatanLembagaController::class);

    // Modul Fasilitas Karyawan
    Route::post('/fasilitas/{fasilitas}/toggle-status', [App\Http\Controllers\Admin\Fasilitas\FasilitasController::class, 'toggleStatus'])->name('fasilitas.toggle-status');
    Route::resource('/fasilitas', App\Http\Controllers\Admin\Fasilitas\FasilitasController::class);

    // Modul Surat Keputusan (SK) Karyawan
    Route::get('/surat-keputusan/{surat_keputusan}/preview', [App\Http\Controllers\Admin\SuratKeputusan\SuratKeputusanController::class, 'preview'])->name('surat-keputusan.preview');
    Route::get('/surat-keputusan/{surat_keputusan}/download', [App\Http\Controllers\Admin\SuratKeputusan\SuratKeputusanController::class, 'download'])->name('surat-keputusan.download');
    Route::resource('/surat-keputusan', App\Http\Controllers\Admin\SuratKeputusan\SuratKeputusanController::class);

    // Modul Rapot Kinerja Karyawan
    Route::get('/rapot-kinerja/{rapot_kinerja}/preview', [App\Http\Controllers\Admin\RapotKinerja\RapotKinerjaController::class, 'preview'])->name('rapot-kinerja.preview');
    Route::get('/rapot-kinerja/{rapot_kinerja}/download', [App\Http\Controllers\Admin\RapotKinerja\RapotKinerjaController::class, 'download'])->name('rapot-kinerja.download');
    Route::resource('/rapot-kinerja', App\Http\Controllers\Admin\RapotKinerja\RapotKinerjaController::class);

    // Modul Tupoksi (Tugas Pokok dan Fungsi) Karyawan
    Route::get('/tupoksi/{tupoksi}/preview', [App\Http\Controllers\Admin\Tupoksi\TupoksiController::class, 'preview'])->name('tupoksi.preview');
    Route::get('/tupoksi/{tupoksi}/download', [App\Http\Controllers\Admin\Tupoksi\TupoksiController::class, 'download'])->name('tupoksi.download');
    Route::resource('/tupoksi', App\Http\Controllers\Admin\Tupoksi\TupoksiController::class);

    // Modul Diklat (Pendidikan dan Pelatihan) Karyawan
    Route::get('/diklat/kegiatan-detail/{id}', [App\Http\Controllers\Admin\Diklat\DiklatController::class, 'getKegiatanDetail'])->name('diklat.kegiatan-detail');
    Route::get('/diklat/{diklat}/preview', [App\Http\Controllers\Admin\Diklat\DiklatController::class, 'preview'])->name('diklat.preview');
    Route::get('/diklat/{diklat}/download', [App\Http\Controllers\Admin\Diklat\DiklatController::class, 'download'])->name('diklat.download');
    Route::resource('/diklat', App\Http\Controllers\Admin\Diklat\DiklatController::class);

    // Modul Sertifikat Penghargaan Karyawan
    Route::get('/sertifikat-penghargaan/{sertifikat_penghargaan}/preview', [App\Http\Controllers\Admin\SertifikatPenghargaan\SertifikatPenghargaanController::class, 'preview'])->name('sertifikat-penghargaan.preview');
    Route::get('/sertifikat-penghargaan/{sertifikat_penghargaan}/download', [App\Http\Controllers\Admin\SertifikatPenghargaan\SertifikatPenghargaanController::class, 'download'])->name('sertifikat-penghargaan.download');
    Route::resource('/sertifikat-penghargaan', App\Http\Controllers\Admin\SertifikatPenghargaan\SertifikatPenghargaanController::class);

    // Modul Penggajian (Keuangan) Karyawan
    Route::get('/penggajian/template', [App\Http\Controllers\Admin\Penggajian\PenggajianController::class, 'template'])->name('penggajian.template');
    Route::post('/penggajian/import', [App\Http\Controllers\Admin\Penggajian\PenggajianController::class, 'import'])->name('penggajian.import');
    Route::patch('/penggajian/{penggajian}/status', [App\Http\Controllers\Admin\Penggajian\PenggajianController::class, 'updateStatus'])->name('penggajian.update-status');
    Route::resource('/penggajian', App\Http\Controllers\Admin\Penggajian\PenggajianController::class);
});