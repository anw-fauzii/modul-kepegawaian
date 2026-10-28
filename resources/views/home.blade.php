@extends('layouts.appui')

@section('title')
    <title>Dashboard Kepegawaian - SIPIT Yayasan Prima Insani</title>
@endsection

@section('content')
<style>
    /* Hero Banner */
    .dashboard-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
        border-radius: 16px;
        padding: 28px 32px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.25);
    }
    .hero-decor-circle {
        position: absolute;
        right: -30px;
        top: -40px;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(234, 88, 12, 0.25) 0%, rgba(234, 88, 12, 0) 70%);
        pointer-events: none;
    }
    .hero-decor-circle-2 {
        position: absolute;
        right: 120px;
        bottom: -60px;
        width: 160px;
        height: 160px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(217, 119, 6, 0.2) 0%, rgba(217, 119, 6, 0) 70%);
        pointer-events: none;
    }

    /* Modern KPI Cards */
    .kpi-card {
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        padding: 20px 22px;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03);
    }
    .kpi-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px -6px rgba(0, 0, 0, 0.08);
        border-color: #cbd5e1;
    }
    .kpi-icon-box {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        color: #ffffff;
        flex-shrink: 0;
    }

    /* Content Cards */
    .content-card {
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04);
        margin-bottom: 24px;
        overflow: hidden;
    }
    .content-card-header {
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        background-color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* Avatar & Badges */
    .avatar-circle {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 13px;
        color: #ffffff;
        flex-shrink: 0;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
    }
    .badge-nipy {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        background-color: #f1f5f9;
        color: #475569;
        font-size: 11px;
        padding: 2px 6px;
        border-radius: 4px;
        border: 1px solid #e2e8f0;
        font-weight: 600;
    }

    /* Soft Badges */
    .badge-soft-success {
        background-color: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }
    .badge-soft-warning {
        background-color: #fffbeb;
        color: #b45309;
        border: 1px solid #fde68a;
    }
    .badge-soft-primary {
        background-color: #eef2ff;
        color: #4338ca;
        border: 1px solid #c7d2fe;
    }
    .badge-soft-info {
        background-color: #f0f9ff;
        color: #0369a1;
        border: 1px solid #bae6fd;
    }
    .badge-soft-secondary {
        background-color: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
    }
    .badge-soft-danger {
        background-color: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    /* Quick Jump Buttons */
    .quick-jump-item {
        display: flex;
        align-items: center;
        padding: 12px 14px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        text-decoration: none !important;
        color: #1e293b;
        transition: all 0.2s ease;
        height: 100%;
    }
    .quick-jump-item:hover {
        background: #ffffff;
        border-color: #ea580c;
        color: #ea580c;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(234, 88, 12, 0.12);
    }
    .quick-jump-icon {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        margin-right: 12px;
        flex-shrink: 0;
    }

    /* List Feed Item */
    .feed-item {
        padding: 12px 20px;
        border-bottom: 1px solid #f1f5f9;
        transition: background-color 0.15s ease;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .feed-item:last-child {
        border-bottom: none;
    }
    .feed-item:hover {
        background-color: #f8fafc;
    }
</style>

@php
    $jam = (int) date('H');
    $salam = $jam < 11 ? 'Selamat Pagi' : ($jam < 15 ? 'Selamat Siang' : ($jam < 18 ? 'Selamat Sore' : 'Selamat Malam'));

    $bgGradients = [
        'linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%)',
        'linear-gradient(135deg, #10b981 0%, #047857 100%)',
        'linear-gradient(135deg, #f59e0b 0%, #b45309 100%)',
        'linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%)',
        'linear-gradient(135deg, #ec4899 0%, #be185d 100%)',
        'linear-gradient(135deg, #06b6d4 0%, #0e7490 100%)',
        'linear-gradient(135deg, #ea580c 0%, #c2410c 100%)',
    ];
@endphp

<div class="app-main__inner">
    <!-- HERO WELCOME BANNER -->
    <div class="dashboard-hero mb-4">
        <div class="hero-decor-circle"></div>
        <div class="hero-decor-circle-2"></div>
        
        <div class="row align-items-center position-relative" style="z-index: 1;">
            <div class="col-lg-8 mb-3 mb-lg-0">
                <div class="d-flex align-items-center mb-2">
                    <span class="badge text-white px-2 py-1 mr-2 font-weight-bold"
                        style="background: rgba(234, 88, 12, 0.9); font-size: 11px; border-radius: 6px;">
                        <i class="pe-7s-rocket mr-1"></i> MODUL KEPEGAWAIAN
                    </span>
                    <span class="text-white-50 small">
                        <i class="fa fa-calendar-alt mr-1"></i> {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
                    </span>
                </div>
                <h3 class="font-weight-bold text-white mb-2">
                    {{ $salam }}, {{ Auth::user()->name }}!
                </h3>
                <p class="text-white-50 mb-3" style="max-width: 620px; font-size: 13.5px; line-height: 1.5;">
                    Selamat datang di Dashboard Kepegawaian & Presensi Terpadu Yayasan Prima Insani. Pantau produktivitas, ketepatan waktu, dan data karyawan secara real-time.
                </p>

                <div class="d-flex flex-wrap align-items-center">
                    @if ($activeBulan)
                        <div class="d-inline-flex align-items-center bg-dark bg-opacity-50 px-3 py-1 rounded mr-2 mb-2"
                            style="background-color: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.15);">
                            <i class="fa fa-calendar-check text-warning mr-2"></i>
                            <span class="small text-white">
                                Bulan Aktif: <strong>{{ $activeBulan->nama_bulan }}</strong>
                            </span>
                        </div>
                    @endif

                    @if ($activeTahunAjaran)
                        <div class="d-inline-flex align-items-center bg-dark bg-opacity-50 px-3 py-1 rounded mr-2 mb-2"
                            style="background-color: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.15);">
                            <i class="fa fa-graduation-cap text-info mr-2"></i>
                            <span class="small text-white">
                                TA: <strong>{{ $activeTahunAjaran->nama_tahun_ajaran }}</strong>
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-4 text-lg-right">
                <div class="d-flex flex-column flex-sm-row flex-lg-column justify-content-lg-end gap-2">
                    <a href="{{ route('kegiatan.scanner') }}" class="btn font-weight-bold text-white shadow mb-2"
                        style="background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important; border-radius: 8px; padding: 10px 18px;">
                        <i class="fa fa-qrcode mr-1"></i> Scan QR Kegiatan
                    </a>
                    <a href="{{ route('presensi.create') }}" class="btn font-weight-bold text-white shadow mb-2"
                        style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 8px; padding: 10px 18px;">
                        <i class="fa fa-plus-circle mr-1"></i> Input Presensi Massal
                    </a>
                    <div class="d-flex justify-content-lg-end">
                        <a href="{{ route('presensi.statistik-tahunan') }}" class="btn btn-outline-light btn-sm mr-2 font-weight-bold"
                            style="border-radius: 8px;">
                            <i class="pe-7s-display1 mr-1"></i> Statistik
                        </a>
                        <a href="{{ route('presensi.rekap') }}" class="btn btn-outline-light btn-sm font-weight-bold"
                            style="border-radius: 8px;">
                            <i class="fa fa-chart-bar mr-1"></i> Rekap Bulanan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TOP ROW: KPI STATS CARDS -->
    <div class="row mb-4">
        <!-- KPI 1: Total Karyawan -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="kpi-card">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-uppercase text-muted font-weight-bold" style="font-size: 11px; letter-spacing: 0.05em;">
                            Total Pegawai
                        </span>
                        <div class="kpi-icon-box" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
                            <i class="pe-7s-users"></i>
                        </div>
                    </div>
                    <h2 class="font-weight-bold text-dark mb-1">{{ $totalKaryawan }}</h2>
                    <div class="small text-muted mb-2">
                        <i class="fa fa-mars text-primary mr-1"></i> {{ $totalLaki }} Laki-laki &nbsp;•&nbsp;
                        <i class="fa fa-venus text-danger mr-1"></i> {{ $totalPerempuan }} Perempuan
                    </div>
                    <div class="d-flex flex-wrap gap-1">
                        <span class="badge badge-light border" style="font-size: 10.5px;">Tetap: {{ $statusPegawaiStats['tetap'] }}</span>
                        <span class="badge badge-light border" style="font-size: 10.5px;">Kontrak: {{ $statusPegawaiStats['kontrak'] }}</span>
                        <span class="badge badge-light border" style="font-size: 10.5px;">Honor: {{ $statusPegawaiStats['honor'] }}</span>
                    </div>
                    <div class="small text-muted mt-2">
                        <i class="fa fa-certificate text-success mr-1"></i> {{ $totalSertifikasi }} bersertifikasi ({{ $sertifikasiPersen }}%)
                    </div>
                </div>
                <div class="pt-3 mt-3 border-top">
                    <a href="{{ route('karyawan.index') }}" class="small font-weight-bold text-primary">
                        Kelola Data Karyawan <i class="fa fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- KPI 2: Hadir Hari Ini -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="kpi-card">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-uppercase text-muted font-weight-bold" style="font-size: 11px; letter-spacing: 0.05em;">
                            Hadir Hari Ini
                        </span>
                        <div class="kpi-icon-box" style="background: linear-gradient(135deg, #10b981 0%, #047857 100%);">
                            <i class="pe-7s-check"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline mb-1">
                        <h2 class="font-weight-bold text-dark mb-0 mr-2">{{ $todayTotalHadirSemua }}</h2>
                        <span class="badge badge-soft-success font-weight-bold" style="font-size: 12px;">
                            {{ $todayTingkatKehadiran }}%
                        </span>
                    </div>
                    <div class="small text-muted mb-2">
                        Tercatat {{ $todayTotalTercatat }} dari total {{ $totalKaryawan }} pegawai
                    </div>
                    <div class="d-flex flex-wrap gap-1">
                        <span class="badge badge-soft-success" style="font-size: 10.5px;">
                            <i class="fa fa-check mr-1"></i> {{ $todayHadir }} Tepat Waktu
                        </span>
                        <span class="badge badge-soft-warning" style="font-size: 10.5px;">
                            <i class="fa fa-clock mr-1"></i> {{ $todayTerlambat }} Terlambat
                        </span>
                    </div>
                </div>
                <div class="pt-3 mt-3 border-top">
                    <a href="{{ route('presensi.index', ['tanggal_mulai' => $today, 'tanggal_selesai' => $today]) }}"
                        class="small font-weight-bold text-success">
                        Lihat Presensi Hari Ini <i class="fa fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- KPI 3: Keterlambatan Hari Ini -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="kpi-card">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-uppercase text-muted font-weight-bold" style="font-size: 11px; letter-spacing: 0.05em;">
                            Keterlambatan Hari Ini
                        </span>
                        <div class="kpi-icon-box" style="background: linear-gradient(135deg, #f59e0b 0%, #b45309 100%);">
                            <i class="pe-7s-alarm"></i>
                        </div>
                    </div>
                    <h2 class="font-weight-bold text-dark mb-1">{{ $todayTerlambat }}</h2>
                    <div class="small text-muted mb-2">
                        Pegawai tercatat hadir melewati batas jam masuk
                    </div>
                    <div class="d-flex flex-wrap gap-1">
                        <span class="badge badge-soft-info" style="font-size: 10.5px;">
                            {{ $todayTerlambatIzin }} Dispensasi / Izin
                        </span>
                        <span class="badge badge-soft-danger" style="font-size: 10.5px;">
                            {{ max(0, $todayTerlambat - $todayTerlambatIzin) }} Tanpa Izin
                        </span>
                    </div>
                </div>
                <div class="pt-3 mt-3 border-top">
                    <a href="{{ route('presensi.index', ['status' => 'terlambat', 'tanggal_mulai' => $today, 'tanggal_selesai' => $today]) }}"
                        class="small font-weight-bold text-warning">
                        Pantau Keterlambatan <i class="fa fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- KPI 4: Izin, Sakit & Tugas Luar -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="kpi-card">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-uppercase text-muted font-weight-bold" style="font-size: 11px; letter-spacing: 0.05em;">
                            Tugas, Izin & Cuti
                        </span>
                        <div class="kpi-icon-box" style="background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);">
                            <i class="pe-7s-info"></i>
                        </div>
                    </div>
                    <h2 class="font-weight-bold text-dark mb-1">
                        {{ $todayTugas + $todayIzin + $todaySakit + $todayCuti + $todayAlpa }}
                    </h2>
                    <div class="small text-muted mb-2">
                        Pegawai yang bertugas dinas luar atau tidak masuk
                    </div>
                    <div class="d-flex flex-wrap gap-1">
                        <span class="badge badge-soft-primary" style="font-size: 10.5px;">Tugas: {{ $todayTugas }}</span>
                        <span class="badge badge-soft-info" style="font-size: 10.5px;">Izin: {{ $todayIzin }}</span>
                        <span class="badge badge-soft-secondary" style="font-size: 10.5px;">Sakit: {{ $todaySakit }}</span>
                        <span class="badge badge-soft-danger" style="font-size: 10.5px;">Alpa: {{ $todayAlpa }}</span>
                    </div>
                </div>
                <div class="pt-3 mt-3 border-top">
                    <a href="{{ route('presensi.index') }}" class="small font-weight-bold text-indigo" style="color: #4f46e5;">
                        Riwayat Presensi Lengkap <i class="fa fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 1B: RINGKASAN MODUL ARSIP KEPEGAWAIAN -->
    <div class="row mb-3">
        @php
            $modulCards = [
                ['key' => 'kegiatan',    'label' => 'Kegiatan',    'icon' => 'fa fa-qrcode',     'route' => 'kegiatan.index',               'grad' => '#10b981, #047857'],
                ['key' => 'fasilitas',   'label' => 'Fasilitas',   'icon' => 'pe-7s-box2',       'route' => 'fasilitas.index',              'grad' => '#f59e0b, #b45309'],
                ['key' => 'diklat',      'label' => 'Diklat',      'icon' => 'pe-7s-culture',    'route' => 'diklat.index',                 'grad' => '#3b82f6, #1d4ed8'],
                ['key' => 'rapot',       'label' => 'Rapor Kinerja', 'icon' => 'pe-7s-graph1',   'route' => 'rapot-kinerja.index',          'grad' => '#06b6d4, #0e7490'],
                ['key' => 'sk',          'label' => 'Surat Keputusan', 'icon' => 'pe-7s-news-paper', 'route' => 'surat-keputusan.index',    'grad' => '#64748b, #334155'],
                ['key' => 'penghargaan', 'label' => 'Penghargaan', 'icon' => 'pe-7s-medal',      'route' => 'sertifikat-penghargaan.index', 'grad' => '#ec4899, #be185d'],
                ['key' => 'tupoksi',     'label' => 'Tupoksi',     'icon' => 'pe-7s-note2',      'route' => 'tupoksi.index',                'grad' => '#8b5cf6, #6d28d9'],
            ];
        @endphp
        @foreach ($modulCards as $mc)
            <div class="col-xl col-lg-3 col-md-4 col-6 mb-3">
                <a href="{{ route($mc['route']) }}" class="quick-jump-item" style="background: #ffffff;">
                    <div class="quick-jump-icon text-white" style="background: linear-gradient(135deg, {{ $mc['grad'] }});">
                        <i class="{{ $mc['icon'] }}"></i>
                    </div>
                    <div>
                        <div class="font-weight-bold text-dark" style="font-size: 18px; line-height: 1.1;">{{ $modulStats[$mc['key']] }}</div>
                        <div class="small text-muted">{{ $mc['label'] }}</div>
                        @if ($mc['key'] === 'fasilitas' && $fasilitasBelumKembali > 0)
                            <span class="badge badge-soft-warning py-0 px-1" style="font-size: 10px;">{{ $fasilitasBelumKembali }} dipinjam</span>
                        @endif
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <!-- ROW 2: CHARTS (Tren 7 Hari & Komposisi Kategori) -->
    <div class="row">
        <!-- Chart 1: Tren Presensi 7 Hari Terakhir -->
        <div class="col-lg-8">
            <div class="content-card">
                <div class="content-card-header">
                    <div>
                        <h6 class="font-weight-bold text-dark mb-0">
                            <i class="fa fa-chart-line text-primary mr-2"></i> Tren Presensi 7 Hari Terakhir
                        </h6>
                        <small class="text-muted">Perbandingan fluktuasi kehadiran harian seluruh pegawai</small>
                    </div>
                    <a href="{{ route('presensi.index') }}" class="btn btn-sm btn-light border" style="border-radius: 6px;">
                        Detail Presensi <i class="fa fa-angle-right ml-1"></i>
                    </a>
                </div>
                <div class="card-body p-3">
                    <div style="height: 280px; position: relative;">
                        <canvas id="chartTren7Hari"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart 2: Distribusi Pegawai per Kategori -->
        <div class="col-lg-4">
            <div class="content-card">
                <div class="content-card-header">
                    <div>
                        <h6 class="font-weight-bold text-dark mb-0">
                            <i class="fa fa-layer-group text-warning mr-2"></i> Komposisi Pegawai
                        </h6>
                        <small class="text-muted">Sebaran staf berdasarkan unit kerja</small>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div style="height: 200px; position: relative;">
                        <canvas id="chartKategori"></canvas>
                    </div>
                    <div class="mt-3 pt-2 border-top">
                        <div class="d-flex flex-wrap justify-content-between text-muted small">
                            <span>Total Unit: <strong>{{ $kategoriDistribution->count() }}</strong></span>
                            <span>Total Staf: <strong>{{ $totalKaryawan }}</strong></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 3: REAL-TIME OPERATIONAL FEEDS -->
    <div class="row">
        <!-- Feed Presensi Terbaru Hari Ini -->
        <div class="col-lg-6">
            <div class="content-card">
                <div class="content-card-header">
                    <div>
                        <h6 class="font-weight-bold text-dark mb-0">
                            <i class="fa fa-history text-success mr-2"></i> Presensi Terbaru Hari Ini
                        </h6>
                        <small class="text-muted">Pencatatan absensi terkini pada tanggal {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</small>
                    </div>
                    <a href="{{ route('presensi.index', ['tanggal_mulai' => $today, 'tanggal_selesai' => $today]) }}"
                        class="small font-weight-bold text-primary">
                        Semua ({{ $todayTotalTercatat }}) →
                    </a>
                </div>
                <div class="card-body p-0">
                    @forelse ($latestPresensiToday as $item)
                        @php
                            $namaClean = trim($item->karyawan?->nama_lengkap ?? 'Pegawai');
                            $words = explode(' ', $namaClean);
                            $initials = strtoupper(substr($words[0] ?? 'P', 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
                            $bgGrad = $bgGradients[crc32($namaClean) % count($bgGradients)];
                        @endphp
                        <div class="feed-item">
                            <div class="d-flex align-items-center">
                                @if ($item->karyawan?->foto_url)
                                    <img src="{{ $item->karyawan->foto_url }}" alt="Foto" class="avatar-circle mr-3" style="width: 36px; height: 36px; object-fit: cover;">
                                @else
                                <div class="avatar-circle mr-3" style="background: {{ $bgGrad }}; width: 36px; height: 36px; font-size: 12px;">
                                    {{ $initials }}
                                </div>
                                @endif
                                <div>
                                    <div class="font-weight-bold text-dark" style="font-size: 13px;">
                                        {{ $item->karyawan?->nama_lengkap ?? '-' }}
                                    </div>
                                    <div class="small text-muted">
                                        <span class="badge-nipy mr-1">NIPY: {{ $item->karyawan_nipy }}</span>
                                        {{ $item->karyawan?->kategori?->nama_kategori ?? '-' }}
                                    </div>
                                </div>
                            </div>

                            <div class="text-right">
                                <div class="font-weight-bold text-dark" style="font-size: 13px;">
                                    {{ $item->jam_masuk ? substr($item->jam_masuk, 0, 5) : '-' }}
                                </div>
                                <div>
                                    @if ($item->status === 'hadir')
                                        <span class="badge badge-soft-success py-0 px-2" style="font-size: 10px;">Hadir</span>
                                    @elseif ($item->status === 'terlambat')
                                        <span class="badge badge-soft-warning py-0 px-2 font-weight-bold" style="font-size: 10px;">
                                            +{{ $item->keterlambatan_menit }}m
                                        </span>
                                    @elseif ($item->status === 'tugas')
                                        <span class="badge badge-soft-primary py-0 px-2" style="font-size: 10px;">Tugas</span>
                                    @else
                                        <span class="badge badge-soft-secondary py-0 px-2" style="font-size: 10px;">{{ ucfirst($item->status) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="pe-7s-date fa-3x opacity-4 mb-2" style="display: block;"></i>
                            <h6 class="font-weight-bold text-dark">Belum Ada Presensi Masuk Hari Ini</h6>
                            <p class="small text-muted mb-3">Mulai lakukan pencatatan absensi hari ini dengan form massal.</p>
                            <a href="{{ route('presensi.create') }}" class="btn btn-sm btn-primary font-weight-bold" style="border-radius: 6px;">
                                <i class="fa fa-plus-circle mr-1"></i> Input Presensi Sekarang
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Feed Karyawan Terlambat Hari Ini -->
        <div class="col-lg-6">
            <div class="content-card">
                <div class="content-card-header">
                    <div>
                        <h6 class="font-weight-bold text-dark mb-0">
                            <i class="fa fa-exclamation-circle text-danger mr-2"></i> Keterlambatan Hari Ini
                        </h6>
                        <small class="text-muted">Daftar staf yang memerlukan perhatian ketepatan waktu</small>
                    </div>
                    @if ($todayTerlambat > 0)
                        <span class="badge badge-soft-danger px-2 py-1 font-weight-bold">
                            {{ $todayTerlambat }} Pegawai
                        </span>
                    @endif
                </div>
                <div class="card-body p-0">
                    @forelse ($latePresensiToday as $item)
                        @php
                            $namaClean = trim($item->karyawan?->nama_lengkap ?? 'Pegawai');
                            $words = explode(' ', $namaClean);
                            $initials = strtoupper(substr($words[0] ?? 'P', 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
                            $bgGrad = $bgGradients[crc32($namaClean) % count($bgGradients)];
                        @endphp
                        <div class="feed-item">
                            <div class="d-flex align-items-center">
                                @if ($item->karyawan?->foto_url)
                                    <img src="{{ $item->karyawan->foto_url }}" alt="Foto" class="avatar-circle mr-3" style="width: 36px; height: 36px; object-fit: cover;">
                                @else
                                <div class="avatar-circle mr-3" style="background: {{ $bgGrad }}; width: 36px; height: 36px; font-size: 12px;">
                                    {{ $initials }}
                                </div>
                                @endif
                                <div>
                                    <div class="font-weight-bold text-dark" style="font-size: 13px;">
                                        {{ $item->karyawan?->nama_lengkap ?? '-' }}
                                    </div>
                                    <div class="small text-muted">
                                        <span class="badge-nipy mr-1">{{ $item->karyawan_nipy }}</span>
                                        {{ $item->karyawan?->kategori?->nama_kategori ?? '-' }}
                                    </div>
                                </div>
                            </div>

                            <div class="text-right">
                                <span class="badge badge-soft-danger font-weight-bold py-1 px-2" style="font-size: 11px;">
                                    Telat {{ $item->keterlambatan_menit }} Menit
                                </span>
                                <div class="small mt-1">
                                    @if ($item->izin_terlambat)
                                        <span class="text-info font-weight-bold">
                                            <i class="fa fa-check-circle mr-1"></i> Ada Dispensasi
                                        </span>
                                    @else
                                        <span class="text-muted">Masuk: {{ substr($item->jam_masuk, 0, 5) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <div class="badge-soft-success p-3 rounded-circle d-inline-flex mb-2" style="width: 58px; height: 58px; align-items: center; justify-content: center; font-size: 24px;">
                                <i class="fa fa-medal"></i>
                            </div>
                            <h6 class="font-weight-bold text-success mb-1">Semua Hadir Tepat Waktu!</h6>
                            <p class="small text-muted mb-0">Tidak ada catatan pegawai terlambat pada tanggal hari ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 3B: KEGIATAN LEMBAGA HARI INI -->
    <div class="row">
        <div class="col-12">
            <div class="content-card">
                <div class="content-card-header">
                    <div>
                        <h6 class="font-weight-bold text-dark mb-0">
                            <i class="fa fa-qrcode text-success mr-2"></i> Kegiatan Lembaga Hari Ini
                        </h6>
                        <small class="text-muted">{{ $kegiatanAktifCount }} kegiatan dengan gerbang presensi QR terbuka</small>
                    </div>
                    <div>
                        <a href="{{ route('kegiatan.create') }}" class="btn btn-sm btn-light border mr-1" style="border-radius: 6px;">
                            <i class="fa fa-plus mr-1"></i> Buat Kegiatan
                        </a>
                        <a href="{{ route('kegiatan.index') }}" class="small font-weight-bold text-primary">Semua Kegiatan →</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @forelse ($kegiatanHariIni as $kg)
                        <div class="feed-item">
                            <div class="d-flex align-items-center">
                                <div class="quick-jump-icon {{ $kg->is_active ? 'badge-soft-success' : 'badge-soft-secondary' }}">
                                    <i class="fa fa-calendar-check"></i>
                                </div>
                                <div>
                                    <a href="{{ route('kegiatan.show', $kg->id) }}" class="font-weight-bold text-dark" style="font-size: 13px;">
                                        {{ $kg->nama_kegiatan }}
                                    </a>
                                    @if ($kg->jenis_kegiatan === 'pelatihan')
                                        <span class="badge badge-soft-primary py-0 px-2 ml-1" style="font-size: 10px;">Pelatihan</span>
                                    @endif
                                    <div class="small text-muted">
                                        <i class="fa fa-clock mr-1"></i>{{ $kg->jam_mulai ? substr($kg->jam_mulai, 0, 5) : '-' }}
                                        @if ($kg->jam_selesai) - {{ substr($kg->jam_selesai, 0, 5) }} @endif
                                        @if ($kg->lokasi) &bull; <i class="fa fa-map-marker-alt mr-1"></i>{{ $kg->lokasi }} @endif
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="badge badge-soft-success py-1 px-2 font-weight-bold" style="font-size: 11px;">
                                    {{ $kg->kehadiran_count }} / {{ $totalKaryawan }} hadir
                                </span>
                                <div class="small mt-1">
                                    @if ($kg->is_active)
                                        <a href="{{ route('kegiatan.qr', $kg->id) }}" class="text-success font-weight-bold">Tampilkan QR</a>
                                    @else
                                        <span class="text-muted">Ditutup</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted small">
                            Tidak ada kegiatan lembaga terjadwal hari ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 4: DATA MASTER OPERASIONAL & PINTASAN FITUR -->
    <div class="row">
        <!-- Jam Kerja & Ketentuan Operasional -->
        <div class="col-lg-5 mb-4">
            <div class="content-card h-100 mb-0">
                <div class="content-card-header">
                    <h6 class="font-weight-bold text-dark mb-0">
                        <i class="fa fa-clock text-warning mr-2"></i> Ketentuan Jam Kerja Operasional
                    </h6>
                    <a href="{{ route('jam-kerja.index') }}" class="small font-weight-bold text-primary">Kelola</a>
                </div>
                <div class="card-body p-3">
                    @forelse ($jamKerjaList as $jk)
                        <div class="p-3 mb-2 rounded border" style="background-color: #f8fafc;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="font-weight-bold text-dark" style="font-size: 13.5px;">{{ $jk->nama }}</span>
                                <span class="badge badge-soft-success py-1 px-2" style="font-size: 10.5px;">Aktif</span>
                            </div>
                            <div class="row small text-muted">
                                <div class="col-6 mb-1">
                                    <i class="fa fa-sign-in-alt text-success mr-1"></i> Jam Masuk:
                                    <strong class="text-dark">{{ substr($jk->jam_masuk, 0, 5) }}</strong>
                                </div>
                                <div class="col-6 mb-1">
                                    <i class="fa fa-sign-out-alt text-danger mr-1"></i> Pulang Sen-Kam:
                                    <strong class="text-dark">{{ substr($jk->jam_pulang_senin_kamis, 0, 5) }}</strong>
                                </div>
                                <div class="col-12 mt-1">
                                    <i class="fa fa-sign-out-alt text-info mr-1"></i> Pulang Khusus Jumat:
                                    <strong class="text-dark">{{ substr($jk->jam_pulang_jumat, 0, 5) }}</strong>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted small">
                            Belum ada master jam kerja yang dibuat.
                        </div>
                    @endforelse

                    <div class="mt-3 p-2 bg-light rounded text-muted small" style="font-size: 11.5px; border-left: 3px solid #ea580c;">
                        <i class="fa fa-info-circle text-warning mr-1"></i>
                        Tutup buku presensi bulanan dilakukan setiap <strong>tanggal 28</strong> tiap bulannya.
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Jump Shortcuts -->
        <div class="col-lg-7 mb-4">
            <div class="content-card h-100 mb-0">
                <div class="content-card-header">
                    <h6 class="font-weight-bold text-dark mb-0">
                        <i class="pe-7s-keypad text-primary mr-2"></i> Pintasan Fitur Lengkap
                    </h6>
                    <small class="text-muted">Akses navigasi cepat ke seluruh modul aplikasi</small>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <!-- Presensi Bulk -->
                        <div class="col-md-6 mb-2">
                            <a href="{{ route('presensi.create') }}" class="quick-jump-item">
                                <div class="quick-jump-icon badge-soft-warning">
                                    <i class="fa fa-user-check"></i>
                                </div>
                                <div>
                                    <div class="font-weight-bold" style="font-size: 13px;">Input Presensi Massal</div>
                                    <div class="small text-muted">Input absensi per kategori kerja</div>
                                </div>
                            </a>
                        </div>

                        <!-- Riwayat Presensi -->
                        <div class="col-md-6 mb-2">
                            <a href="{{ route('presensi.index') }}" class="quick-jump-item">
                                <div class="quick-jump-icon badge-soft-primary">
                                    <i class="fa fa-list-alt"></i>
                                </div>
                                <div>
                                    <div class="font-weight-bold" style="font-size: 13px;">Riwayat Presensi</div>
                                    <div class="small text-muted">Daftar presensi terfilter & cepat</div>
                                </div>
                            </a>
                        </div>

                        <!-- Rekap Bulanan -->
                        <div class="col-md-6 mb-2">
                            <a href="{{ route('presensi.rekap') }}" class="quick-jump-item">
                                <div class="quick-jump-icon badge-soft-info">
                                    <i class="fa fa-file-invoice"></i>
                                </div>
                                <div>
                                    <div class="font-weight-bold" style="font-size: 13px;">Rekap Presensi Bulanan</div>
                                    <div class="small text-muted">Tutup buku tanggal 28</div>
                                </div>
                            </a>
                        </div>

                        <!-- Statistik Tahunan -->
                        <div class="col-md-6 mb-2">
                            <a href="{{ route('presensi.statistik-tahunan') }}" class="quick-jump-item">
                                <div class="quick-jump-icon badge-soft-success">
                                    <i class="pe-7s-display1"></i>
                                </div>
                                <div>
                                    <div class="font-weight-bold" style="font-size: 13px;">Statistik Tahunan</div>
                                    <div class="small text-muted">Grafik tren & KPI kehadiran tahunan</div>
                                </div>
                            </a>
                        </div>

                        <!-- Data Karyawan -->
                        <div class="col-md-6 mb-2">
                            <a href="{{ route('karyawan.index') }}" class="quick-jump-item">
                                <div class="quick-jump-icon badge-soft-secondary">
                                    <i class="pe-7s-users"></i>
                                </div>
                                <div>
                                    <div class="font-weight-bold" style="font-size: 13px;">Manajemen Karyawan</div>
                                    <div class="small text-muted">Data identitas & profil pegawai</div>
                                </div>
                            </a>
                        </div>

                        <!-- Kategori Karyawan -->
                        <div class="col-md-6 mb-2">
                            <a href="{{ route('kategori.index') }}" class="quick-jump-item">
                                <div class="quick-jump-icon badge-soft-danger">
                                    <i class="fa fa-layer-group"></i>
                                </div>
                                <div>
                                    <div class="font-weight-bold" style="font-size: 13px;">Kategori Karyawan</div>
                                    <div class="small text-muted">Pengelompokan unit & jenjang</div>
                                </div>
                            </a>
                        </div>
                        <!-- Kegiatan Lembaga -->
                        <div class="col-md-6 mb-2">
                            <a href="{{ route('kegiatan.index') }}" class="quick-jump-item">
                                <div class="quick-jump-icon badge-soft-success"><i class="fa fa-qrcode"></i></div>
                                <div>
                                    <div class="font-weight-bold" style="font-size: 13px;">Kegiatan Lembaga</div>
                                    <div class="small text-muted">Agenda, QR & kehadiran kegiatan</div>
                                </div>
                            </a>
                        </div>

                        <!-- Fasilitas -->
                        <div class="col-md-6 mb-2">
                            <a href="{{ route('fasilitas.index') }}" class="quick-jump-item">
                                <div class="quick-jump-icon badge-soft-warning"><i class="pe-7s-box2"></i></div>
                                <div>
                                    <div class="font-weight-bold" style="font-size: 13px;">Fasilitas & Aset</div>
                                    <div class="small text-muted">Inventaris yang dipinjamkan</div>
                                </div>
                            </a>
                        </div>

                        <!-- Diklat -->
                        <div class="col-md-6 mb-2">
                            <a href="{{ route('diklat.index') }}" class="quick-jump-item">
                                <div class="quick-jump-icon badge-soft-primary"><i class="pe-7s-culture"></i></div>
                                <div>
                                    <div class="font-weight-bold" style="font-size: 13px;">Diklat & Pelatihan</div>
                                    <div class="small text-muted">Riwayat pelatihan pegawai</div>
                                </div>
                            </a>
                        </div>

                        <!-- Rapor Kinerja -->
                        <div class="col-md-6 mb-2">
                            <a href="{{ route('rapot-kinerja.index') }}" class="quick-jump-item">
                                <div class="quick-jump-icon badge-soft-info"><i class="pe-7s-graph1"></i></div>
                                <div>
                                    <div class="font-weight-bold" style="font-size: 13px;">Rapor Kinerja</div>
                                    <div class="small text-muted">Arsip penilaian per tahun ajaran</div>
                                </div>
                            </a>
                        </div>

                        <!-- Surat Keputusan -->
                        <div class="col-md-6 mb-2">
                            <a href="{{ route('surat-keputusan.index') }}" class="quick-jump-item">
                                <div class="quick-jump-icon badge-soft-secondary"><i class="pe-7s-news-paper"></i></div>
                                <div>
                                    <div class="font-weight-bold" style="font-size: 13px;">Surat Keputusan</div>
                                    <div class="small text-muted">SK pengangkatan & penugasan</div>
                                </div>
                            </a>
                        </div>

                        <!-- Penghargaan -->
                        <div class="col-md-6 mb-2">
                            <a href="{{ route('sertifikat-penghargaan.index') }}" class="quick-jump-item">
                                <div class="quick-jump-icon badge-soft-warning"><i class="pe-7s-medal"></i></div>
                                <div>
                                    <div class="font-weight-bold" style="font-size: 13px;">Penghargaan</div>
                                    <div class="small text-muted">Sertifikat & piagam prestasi</div>
                                </div>
                            </a>
                        </div>

                        <!-- Tupoksi -->
                        <div class="col-md-6 mb-2">
                            <a href="{{ route('tupoksi.index') }}" class="quick-jump-item">
                                <div class="quick-jump-icon badge-soft-primary"><i class="pe-7s-note2"></i></div>
                                <div>
                                    <div class="font-weight-bold" style="font-size: 13px;">Tupoksi</div>
                                    <div class="small text-muted">Tugas pokok & fungsi pegawai</div>
                                </div>
                            </a>
                        </div>

                        <!-- Bulan Presensi -->
                        <div class="col-md-6 mb-2">
                            <a href="{{ route('bulan.index') }}" class="quick-jump-item">
                                <div class="quick-jump-icon badge-soft-info"><i class="fa fa-calendar-alt"></i></div>
                                <div>
                                    <div class="font-weight-bold" style="font-size: 13px;">Bulan Presensi</div>
                                    <div class="small text-muted">Periode & tutup buku bulanan</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SCRIPT CHART.JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // 1. DATA TREN 7 HARI
        const trenLabels = @json($tren7Hari->pluck('label'));
        const trenHadir = @json($tren7Hari->pluck('hadir'));
        const trenTerlambat = @json($tren7Hari->pluck('terlambat'));
        const trenTugas = @json($tren7Hari->pluck('tugas'));
        const trenTidakHadir = @json($tren7Hari->pluck('tidakHadir'));

        const ctxTren = document.getElementById('chartTren7Hari');
        if (ctxTren) {
            new Chart(ctxTren.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: trenLabels,
                    datasets: [
                        {
                            label: 'Hadir Tepat Waktu',
                            data: trenHadir,
                            backgroundColor: '#10b981',
                            borderRadius: 4,
                        },
                        {
                            label: 'Terlambat',
                            data: trenTerlambat,
                            backgroundColor: '#f59e0b',
                            borderRadius: 4,
                        },
                        {
                            label: 'Tugas Luar',
                            data: trenTugas,
                            backgroundColor: '#6366f1',
                            borderRadius: 4,
                        },
                        {
                            label: 'Izin / Sakit / Alpa',
                            data: trenTidakHadir,
                            backgroundColor: '#ef4444',
                            borderRadius: 4,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            stacked: true,
                            grid: { display: false }
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                boxWidth: 12,
                                font: { size: 11 }
                            }
                        },
                        tooltip: {
                            padding: 10,
                            cornerRadius: 8
                        }
                    }
                }
            });
        }

        // 2. DATA KOMPOSISI KATEGORI
        const katLabels = @json($kategoriDistribution->pluck('nama_kategori'));
        const katData = @json($kategoriDistribution->pluck('karyawan_count'));

        const ctxKat = document.getElementById('chartKategori');
        if (ctxKat) {
            new Chart(ctxKat.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: katLabels,
                    datasets: [{
                        data: katData,
                        backgroundColor: [
                            '#ea580c', '#3b82f6', '#10b981', '#8b5cf6',
                            '#f59e0b', '#06b6d4', '#ec4899', '#64748b'
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return ' ' + context.label + ': ' + context.raw + ' Pegawai';
                                }
                            }
                        }
                    },
                    cutout: '68%'
                }
            });
        }
    });
</script>
@endsection
