@extends('layouts.appui')

@section('title')
    <title>Dashboard Pegawai - {{ $karyawan->nama_lengkap ?? Auth::user()->name }} | PION Prima Insani</title>
@endsection

@section('content')
<style>
    /* Hero Banner */
    .user-hero-card {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 55%, #334155 100%);
        border-radius: 16px;
        padding: 24px 28px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px -5px rgba(15, 23, 42, 0.25);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }
    .user-hero-decor-1 {
        position: absolute;
        right: -30px;
        top: -40px;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(16, 185, 129, 0.25) 0%, rgba(16, 185, 129, 0) 70%);
        pointer-events: none;
    }
    .user-hero-decor-2 {
        position: absolute;
        right: 120px;
        bottom: -50px;
        width: 170px;
        height: 170px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(234, 88, 12, 0.2) 0%, rgba(234, 88, 12, 0) 70%);
        pointer-events: none;
    }
    .user-hero-inner {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        width: 100%;
    }
    .user-hero-action {
        flex-shrink: 0;
        margin-left: auto;
        text-align: right;
    }

    /* Avatar */
    .user-avatar-box {
        width: 62px;
        height: 62px;
        border-radius: 14px;
        object-fit: cover;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
        border: 2.5px solid rgba(255, 255, 255, 0.3);
        flex-shrink: 0;
    }
    .user-avatar-initials {
        width: 62px;
        height: 62px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 22px;
        color: #ffffff;
        background: linear-gradient(135deg, #ea580c 0%, #d97706 100%);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
        border: 2.5px solid rgba(255, 255, 255, 0.3);
        flex-shrink: 0;
    }

    /* Modern Card */
    .modern-card {
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 4px 16px -2px rgba(15, 23, 42, 0.04);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }
    .modern-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 8px 24px -4px rgba(15, 23, 42, 0.08);
    }
    .modern-card-header {
        padding: 14px 18px;
        border-bottom: 1px solid #f1f5f9;
        background-color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* KPI Mini Cards */
    .kpi-stat-card {
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        padding: 16px 18px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .kpi-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.07);
        border-color: #cbd5e1;
    }
    .kpi-icon-square {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
        color: #ffffff;
        flex-shrink: 0;
    }
    .kpi-val-title {
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
    }

    /* Dokumen Count Quick Jump Cards */
    .doc-jump-card {
        display: block;
        padding: 14px 16px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        text-decoration: none !important;
        color: #1e293b;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
    }
    .doc-jump-card:hover {
        background: #ffffff;
        border-color: #ea580c;
        transform: translateY(-3px);
        box-shadow: 0 8px 18px rgba(234, 88, 12, 0.12);
        color: #ea580c;
    }
    .doc-jump-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: #ffffff;
        flex-shrink: 0;
    }
    .doc-jump-count {
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
    }
    .doc-jump-label {
        font-size: 12.5px;
        color: #64748b;
        font-weight: 600;
        margin-top: 2px;
    }
    .doc-jump-card:hover .doc-jump-count {
        color: #ea580c;
    }
    .doc-jump-card:hover .doc-jump-arrow {
        color: #ea580c;
        transform: translateX(3px);
    }
    .doc-jump-arrow {
        font-size: 11px;
        color: #94a3b8;
        transition: transform 0.2s ease, color 0.2s ease;
    }

    /* Badges */
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
    .badge-nipy-chip {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        background-color: rgba(255, 255, 255, 0.15);
        color: #f8fafc;
        font-size: 11px;
        padding: 3px 8px;
        border-radius: 6px;
        font-weight: 600;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    /* List Feed Row */
    .list-feed-row {
        padding: 13px 18px;
        border-bottom: 1px solid #f1f5f9;
        transition: background-color 0.15s ease;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .list-feed-row:last-child {
        border-bottom: none;
    }
    .list-feed-row:hover {
        background-color: #f8fafc;
    }

    /* Responsive Adjustments for Mobile Screens */
    @media (max-width: 767.98px) {
        .user-hero-card {
            padding: 18px 16px;
            border-radius: 14px;
        }
        .user-hero-inner {
            flex-direction: column;
            align-items: stretch;
            gap: 14px;
        }
        .user-hero-action {
            width: 100%;
            margin-left: 0;
            text-align: center;
        }
        .user-avatar-box,
        .user-avatar-initials {
            width: 52px;
            height: 52px;
            font-size: 18px;
            border-radius: 12px;
        }
        .kpi-val-title {
            font-size: 20px;
        }
        .kpi-stat-card {
            padding: 12px 14px;
        }
        .modern-card-header {
            padding: 12px 14px;
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }
        .list-feed-row {
            padding: 12px 14px;
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }
        .list-feed-row > div:last-child {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-mobile-block {
            width: 100%;
            text-align: center;
            justify-content: center;
        }
        .doc-jump-card {
            padding: 12px 14px;
        }
        .doc-jump-icon {
            width: 38px;
            height: 38px;
            font-size: 16px;
            margin-right: 12px !important;
        }
        .doc-jump-count {
            font-size: 18px;
        }
        .doc-jump-label {
            font-size: 12px;
        }
    }
</style>

@php
    $jam = (int) date('H');
    $salam = $jam < 11 ? 'Selamat Pagi' : ($jam < 15 ? 'Selamat Siang' : ($jam < 18 ? 'Selamat Sore' : 'Selamat Malam'));

    $namaPegawai = $karyawan->nama_lengkap ?? Auth::user()->name;
    $words = explode(' ', trim($namaPegawai));
    $initials = strtoupper(substr($words[0] ?? 'P', 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));

    // Modul Dokumen Cards dengan Link Detail
    $portofolioModuls = [
        [
            'key'   => 'gaji',
            'label' => 'Slip Gaji',
            'count' => $docCounts['gaji'] . ' Slip',
            'icon'  => 'fa fa-money-bill-wave',
            'route' => route('penggajian.index'),
            'grad'  => '#10b981, #047857',
            'badge' => null,
        ],
        [
            'key'   => 'sk',
            'label' => 'Surat Keputusan (SK)',
            'count' => $docCounts['sk'] . ' SK',
            'icon'  => 'pe-7s-news-paper',
            'route' => route('surat-keputusan.index'),
            'grad'  => '#3b82f6, #1d4ed8',
            'badge' => null,
        ],
        [
            'key'   => 'rapot',
            'label' => 'Rapor Kinerja',
            'count' => $docCounts['rapot'] . ' Rapor',
            'icon'  => 'pe-7s-graph1',
            'route' => route('rapot-kinerja.index'),
            'grad'  => '#06b6d4, #0e7490',
            'badge' => null,
        ],
        [
            'key'   => 'tupoksi',
            'label' => 'Tupoksi Jabatan',
            'count' => $docCounts['tupoksi'] . ' Uraian',
            'icon'  => 'pe-7s-note2',
            'route' => route('tupoksi.index'),
            'grad'  => '#8b5cf6, #6d28d9',
            'badge' => null,
        ],
        [
            'key'   => 'fasilitas',
            'label' => 'Inventaris & Fasilitas',
            'count' => $docCounts['fasilitas'] . ' Barang',
            'icon'  => 'pe-7s-box2',
            'route' => route('fasilitas.index'),
            'grad'  => '#f59e0b, #b45309',
            'badge' => $fasilitasDipinjamCount > 0 ? $fasilitasDipinjamCount . ' Dipinjam' : null,
        ],
        [
            'key'   => 'diklat',
            'label' => 'Diklat & Pelatihan',
            'count' => $docCounts['diklat'] . ' Diklat',
            'icon'  => 'pe-7s-culture',
            'route' => route('diklat.index'),
            'grad'  => '#6366f1, #4338ca',
            'badge' => null,
        ],
        [
            'key'   => 'penghargaan',
            'label' => 'Penghargaan',
            'count' => $docCounts['penghargaan'] . ' Piagam',
            'icon'  => 'pe-7s-medal',
            'route' => route('sertifikat-penghargaan.index'),
            'grad'  => '#ec4899, #be185d',
            'badge' => null,
        ],
        [
            'key'   => 'cuti',
            'label' => 'Permohonan Cuti',
            'count' => ($docCounts['cuti'] ?? 0) . ' Berkas',
            'icon'  => 'pe-7s-note2',
            'route' => route('cuti.index'),
            'grad'  => '#0ea5e9, #0284c7',
            'badge' => null,
        ],
    ];
@endphp

<div class="app-main__inner">
    <!-- MODE SWITCHER & REALTIME BAR -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 bg-white p-2.5 px-3 rounded-xl border border-slate-200 shadow-sm" style="border-radius: 12px;">
        <div class="d-flex align-items-center flex-wrap gap-2 mb-1 mb-sm-0">
            <span class="badge px-2.5 py-1 text-white font-weight-bold" style="background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%); border-radius: 7px; font-size: 11.5px;">
                <i class="fa fa-user mr-1"></i> Dashboard Pegawai
            </span>
            <a href="{{ route('home', ['view' => 'admin']) }}" class="btn btn-outline-secondary btn-sm font-weight-bold" style="border-radius: 7px; font-size: 11.5px; padding: 4px 10px;">
                <i class="fa fa-chart-pie mr-1"></i> Dashboard Admin
            </a>
            @if ($isPreview)
                <span class="badge badge-soft-warning font-weight-bold px-2 py-0.5" style="font-size: 10.5px;">
                    <i class="fa fa-eye mr-1"></i> Mode Pratinjau
                </span>
            @endif
        </div>
        <div class="text-muted small d-flex align-items-center flex-wrap">
            <span class="d-inline-flex align-items-center mr-2">
                <i class="fa fa-calendar-alt text-primary mr-1"></i> {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
            </span>
            <span class="d-none d-sm-inline text-slate-300 mr-2">|</span>
            <span class="d-inline-flex align-items-center font-weight-bold text-dark">
                <i class="fa fa-clock text-warning mr-1"></i> {{ \Carbon\Carbon::now()->format('H:i') }} WIB
            </span>
        </div>
    </div>

    <!-- MODERN USER HERO GREETING CARD (FOKUS OPERASIONAL & BEBAS BIODATA BERAT) -->
    <div class="user-hero-card mb-4">
        <div class="user-hero-decor-1"></div>
        <div class="user-hero-decor-2"></div>

        <div class="user-hero-inner">
            <!-- Sisi Kiri: Avatar, Salam & Identitas Cepat -->
            <div class="d-flex align-items-center min-w-0">
                <!-- Foto / Avatar Inisial -->
                <div class="mr-3 mr-sm-4">
                    @if ($karyawan?->foto_url)
                        <img src="{{ $karyawan->foto_url }}" alt="Foto Pegawai" class="user-avatar-box">
                    @else
                        <div class="user-avatar-initials">
                            {{ $initials }}
                        </div>
                    @endif
                </div>

                <!-- Salam & Identitas Cepat -->
                <div class="min-w-0">
                    <div class="d-flex align-items-center flex-wrap gap-1.5 mb-1">
                        <span class="badge-nipy-chip mr-1">
                            NIPY: {{ $karyawan->nipy ?? Auth::user()->no_induk ?? '-' }}
                        </span>
                        @if ($karyawan?->kategori)
                            <span class="badge px-2 py-0.5 font-weight-bold text-white mr-1"
                                style="background: rgba(59, 130, 246, 0.9); font-size: 10.5px; border-radius: 6px;">
                                {{ $karyawan->kategori->nama_kategori }}
                            </span>
                        @endif
                        @if ($karyawan?->status_pegawai)
                            <span class="badge px-2 py-0.5 font-weight-bold text-white"
                                style="background: rgba(234, 88, 12, 0.9); font-size: 10.5px; border-radius: 6px;">
                                {{ $karyawan->status_pegawai }}
                            </span>
                        @endif
                    </div>

                    <h4 class="font-weight-bold text-white mb-1 text-truncate" style="font-size: calc(1.1rem + 0.3vw);">
                        {{ $salam }}, {{ $karyawan?->nama_gelar ?? Auth::user()->name }}!
                    </h4>

                    <!-- Meta Baris Jam Kerja Hari Ini -->
                    <div class="d-flex flex-wrap align-items-center text-white-50 small mt-1">
                        <span class="mr-3 mb-1 d-inline-flex align-items-center">
                            <i class="fa fa-business-time text-warning mr-1"></i> Jam Masuk: <strong class="text-white ml-1">{{ $jamMasukNormal }} WIB</strong>
                        </span>
                        <span class="mb-1 d-inline-flex align-items-center">
                            <i class="fa fa-door-open text-success mr-1"></i> Pulang: <strong class="text-white ml-1">{{ $jamPulangNormal }} WIB</strong>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Sisi Kanan: Tombol Aksi Cepat Scan QR Kegiatan -->
            <div class="user-hero-action">
                <a href="{{ route('kegiatan.scanner') }}" class="btn font-weight-bold text-white shadow-sm btn-mobile-block"
                    style="background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important; border-radius: 10px; padding: 11px 22px; font-size: 13.5px; border: none;">
                    <i class="fa fa-qrcode mr-2"></i> Scan QR Presensi Kegiatan
                </a>
            </div>
        </div>
    </div>

    <!-- ROW 1: DUA WIDGET UTAMA (PRESENSI HARI INI & WIDGET SHAUM SUNNAH) -->
    <div class="row mb-4">
        <!-- CARD PRESENSI SAYA HARI INI -->
        <div class="col-lg-6 mb-3 mb-lg-0">
            <div class="modern-card h-100">
                <div class="modern-card-header">
                    <span class="text-uppercase text-muted font-weight-bold small">
                        <i class="fa fa-calendar-check text-primary mr-1"></i> Presensi Saya Hari Ini
                    </span>
                    <span class="badge badge-light border text-muted font-weight-normal small">
                        Standar: {{ $jamMasukNormal }} WIB
                    </span>
                </div>
                <div class="p-3.5 p-sm-4 d-flex flex-column justify-content-between" style="min-height: 165px;">
                    @if ($myPresensiToday && (!empty($myPresensiToday->jam_masuk) || in_array($myPresensiToday->status, ['hadir', 'terlambat', 'tugas'])))
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mr-3 shadow-sm text-white flex-shrink-0"
                                style="width: 48px; height: 48px; background: {{ $myPresensiToday->status === 'hadir' ? 'linear-gradient(135deg, #10b981 0%, #059669 100%)' : 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)' }}; font-size: 20px;">
                                <i class="{{ $myPresensiToday->status === 'hadir' ? 'fa fa-check' : 'fa fa-clock' }}"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="d-flex align-items-center flex-wrap gap-2">
                                    <h4 class="font-weight-bold text-dark mb-0">
                                        {{ $myPresensiToday->jam_masuk ? substr($myPresensiToday->jam_masuk, 0, 5) . ' WIB' : '-' }}
                                    </h4>
                                    @if ($myPresensiToday->status === 'hadir')
                                        <span class="badge badge-soft-success font-weight-bold px-2 py-0.5">Hadir Tepat Waktu</span>
                                    @elseif ($myPresensiToday->status === 'terlambat')
                                        <span class="badge badge-soft-warning font-weight-bold px-2 py-0.5">
                                            Telat +{{ $myPresensiToday->keterlambatan_menit }} Menit
                                        </span>
                                    @else
                                        <span class="badge badge-soft-primary font-weight-bold px-2 py-0.5">
                                            {{ ucfirst($myPresensiToday->status) }}
                                        </span>
                                    @endif
                                </div>
                                <div class="small text-muted mt-1">
                                    Jam Pulang: <strong class="text-dark">{{ $myPresensiToday->jam_pulang ? substr($myPresensiToday->jam_pulang, 0, 5) . ' WIB' : 'Belum Tap Pulang' }}</strong>
                                    @if ($myPresensiToday->durasi_kerja && $myPresensiToday->durasi_kerja !== '-')
                                        &bull; Durasi: {{ $myPresensiToday->durasi_kerja }}
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if ($myPresensiToday->status === 'terlambat' && $myPresensiToday->izin_terlambat)
                            <div class="p-2 px-3 rounded small mb-2" style="background-color: #f0fdf4; border: 1px solid #bbf7d0; color: #166534;">
                                <i class="fa fa-check-circle mr-1"></i> Ada Dispensasi/Izin Keterlambatan
                            </div>
                        @endif
                    @else
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mr-3 shadow-sm text-warning flex-shrink-0"
                                style="width: 48px; height: 48px; background: #fffbeb; border: 1px solid #fde68a; font-size: 20px;">
                                <i class="fa fa-exclamation-triangle"></i>
                            </div>
                            <div>
                                <h6 class="font-weight-bold text-dark mb-0">Belum Ada Presensi Masuk Hari Ini</h6>
                                <p class="small text-muted mb-0 mt-1">
                                    Silakan tap kartu presensi saat tiba di lembaga. Batas toleransi s/d <strong>{{ $jamToleransi }} WIB</strong>.
                                </p>
                            </div>
                        </div>
                    @endif

                    <div class="pt-2.5 border-top d-flex justify-content-between align-items-center small text-muted">
                        <span>Jadwal Pulang Hari Ini: <strong class="text-dark">{{ $jamPulangNormal }} WIB</strong></span>
                        <a href="{{ route('kegiatan.scanner') }}" class="font-weight-bold text-success text-decoration-none">
                            Scan QR Kegiatan <i class="fa fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- CARD STATUS SHAUM SUNNAH HARI INI (HANYA BISA DIKLIK JIKA SUDAH ABSEN MASUK) -->
        <div class="col-lg-6">
            <div class="modern-card h-100" style="background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%); border-color: #a7f3d0 !important;">
                <div class="modern-card-header" style="background: transparent; border-bottom-color: rgba(167, 243, 208, 0.7);">
                    <span class="text-uppercase font-weight-bold text-success small">
                        <i class="fa fa-moon mr-1"></i> Ibadah Shaum Sunnah Hari Ini
                    </span>
                    <span class="badge badge-soft-success font-weight-bold px-2 py-0.5" style="font-size: 11px;">
                        {{ $todayShaumCount }} Pegawai Shaum
                    </span>
                </div>
                <div class="p-3.5 p-sm-4 d-flex flex-column justify-content-between" style="min-height: 165px;">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mr-3 shadow-sm text-white flex-shrink-0"
                            style="width: 48px; height: 48px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); font-size: 20px;">
                            <i class="fa fa-moon"></i>
                        </div>
                        <div>
                            <h5 class="font-weight-bold text-dark mb-0">
                                @if ($myPresensiToday?->shaum)
                                    <span class="text-success"><i class="fa fa-check-circle mr-1"></i> Anda Sedang Shaum (Puasa)</span>
                                @elseif ($hasAbsenToday)
                                    <span class="text-secondary"><i class="fa fa-info-circle mr-1"></i> Tidak Sedang Shaum</span>
                                @else
                                    <span class="text-muted"><i class="fa fa-lock mr-1"></i> Belum Melakukan Presensi Masuk</span>
                                @endif
                            </h5>
                            <p class="small text-muted mb-0 mt-1">
                                @if ($hasAbsenToday)
                                    @if ($myPresensiToday?->shaum)
                                        Alhamdulillah, status shaum Anda tercatat aktif hari ini. Baarakallahu fiikum.
                                    @else
                                        Klik tombol di bawah jika Anda sedang menjalankan shaum sunnah.
                                    @endif
                                @else
                                    <span class="text-warning font-weight-semibold">
                                        Status shaum hanya dapat dikonfirmasi setelah Anda melakukan presensi masuk hari ini.
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="pt-2.5 border-top d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2" style="border-top-color: rgba(167, 243, 208, 0.7) !important;">
                        @if ($hasAbsenToday)
                            <form action="{{ route('presensi.my-shaum.toggle') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="karyawan_nipy" value="{{ $karyawan?->nipy }}">
                                <button type="submit" class="btn btn-sm font-weight-bold shadow-sm btn-mobile-block"
                                    style="border-radius: 8px; padding: 7px 18px; font-size: 12.5px; {{ $myPresensiToday->shaum ? 'background: #ffffff; color: #047857; border: 1.5px solid #10b981;' : 'background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff;' }}"
                                    title="Klik untuk mengubah status shaum Anda hari ini">
                                    @if ($myPresensiToday->shaum)
                                        <i class="fa fa-times text-danger mr-1"></i> Ubah ke Tidak Shaum
                                    @else
                                        <i class="fa fa-moon mr-1"></i> Konfirmasi Saya Sedang Shaum
                                    @endif
                                </button>
                            </form>
                        @else
                            <button type="button" class="btn btn-sm btn-light border text-muted btn-mobile-block" disabled
                                style="border-radius: 8px; font-size: 12px; cursor: not-allowed;"
                                title="Anda harus absen harian terlebih dahulu sebelum mengonfirmasi shaum">
                                <i class="fa fa-lock mr-1.5 text-secondary"></i> Wajib Absen Masuk Terlebih Dahulu
                            </button>
                        @endif
                        <span class="small text-muted mt-1 mt-sm-0">
                            Bulan Ini: <strong class="text-success">{{ $myShaumBulanIni ?? $myShaumDays }} Hari</strong>
                            &bull; Total: <strong class="text-success">{{ $myShaumDays }} Hari</strong>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 2: 4 KARTU STATISTIK KEHADIRAN (RESPONSIF 2x2 PADA MOBILE & 4 KOLOM PADA DESKTOP) -->
    <div class="row mb-4">
        <!-- KPI 1: Kehadiran -->
        <div class="col-6 col-lg-3 mb-3">
            <div class="kpi-stat-card">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase text-muted font-weight-bold" style="font-size: 10.5px; letter-spacing: 0.04em;">
                            Kehadiran
                        </span>
                        <div class="kpi-icon-square" style="background: linear-gradient(135deg, #10b981 0%, #047857 100%);">
                            <i class="fa fa-chart-line"></i>
                        </div>
                    </div>
                    <div class="kpi-val-title text-success">{{ $myAttendanceRate }}%</div>
                    <div class="progress my-2" style="height: 5px; border-radius: 3px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ min(100, $myAttendanceRate) }}%;" aria-valuenow="{{ $myAttendanceRate }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="small text-muted text-truncate">
                        {{ $totalMasuk }} dari {{ $totalTercatat }} hari kerja
                    </div>
                </div>
                <div class="pt-2 mt-2 border-top small text-success font-weight-bold">
                    <i class="fa fa-check mr-1"></i> Sangat Baik
                </div>
            </div>
        </div>

        <!-- KPI 2: Tepat Waktu -->
        <div class="col-6 col-lg-3 mb-3">
            <div class="kpi-stat-card">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase text-muted font-weight-bold" style="font-size: 10.5px; letter-spacing: 0.04em;">
                            Tepat Waktu
                        </span>
                        <div class="kpi-icon-square" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
                            <i class="fa fa-user-check"></i>
                        </div>
                    </div>
                    <div class="kpi-val-title text-primary">{{ $myHadir }} <span style="font-size: 13px; font-weight: 500;" class="text-muted">Hari</span></div>
                    <div class="small text-muted mt-2">
                        @if ($myTerlambat > 0)
                            <span class="badge badge-soft-warning font-weight-bold py-0 px-1" style="font-size: 10px;">
                                Telat {{ $myTerlambat }}x ({{ $myTerlambatMenit }}m)
                            </span>
                        @else
                            <span class="badge badge-soft-success font-weight-bold py-0 px-1" style="font-size: 10px;">
                                0 Terlambat
                            </span>
                        @endif
                    </div>
                </div>
                <div class="pt-2 mt-2 border-top small text-muted text-truncate">
                    Batas toleransi: 15 menit
                </div>
            </div>
        </div>

        <!-- KPI 3: Tugas & Izin -->
        <div class="col-6 col-lg-3 mb-3">
            <div class="kpi-stat-card">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase text-muted font-weight-bold" style="font-size: 10.5px; letter-spacing: 0.04em;">
                            Tugas & Izin
                        </span>
                        <div class="kpi-icon-square" style="background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);">
                            <i class="fa fa-clipboard-list"></i>
                        </div>
                    </div>
                    <div class="kpi-val-title text-dark">{{ $myTugas + $myIzin + $mySakit + $myCuti }} <span style="font-size: 13px; font-weight: 500;" class="text-muted">Hari</span></div>
                    <div class="d-flex flex-wrap gap-1 mt-2">
                        <span class="badge badge-light border" style="font-size: 10px;">Tugas: {{ $myTugas }}</span>
                        <span class="badge badge-light border" style="font-size: 10px;">Izin: {{ $myIzin }}</span>
                    </div>
                </div>
                <div class="pt-2 mt-2 border-top small text-muted text-truncate">
                    Sakit: {{ $mySakit }} &bull; Cuti: {{ $myCuti }}
                </div>
            </div>
        </div>

        <!-- KPI 4: Shaum Bulan Ini & Total Akumulasi -->
        <div class="col-6 col-lg-3 mb-3">
            <div class="kpi-stat-card">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase text-muted font-weight-bold" style="font-size: 10.5px; letter-spacing: 0.04em;">
                            Hari Shaum
                        </span>
                        <div class="kpi-icon-square" style="background: linear-gradient(135deg, #f59e0b 0%, #b45309 100%);">
                            <i class="fa fa-moon"></i>
                        </div>
                    </div>
                    <div class="kpi-val-title text-warning">{{ $myShaumDays }} <span style="font-size: 13px; font-weight: 500;" class="text-muted">Hari</span></div>
                    <div class="small text-muted mt-2">
                        <span class="badge badge-soft-success font-weight-bold py-0 px-1" style="font-size: 10px;">
                            <i class="fa fa-heart mr-0.5"></i> Bulan Ini: {{ $myShaumBulanIni ?? $myShaumDays }} Hari
                        </span>
                    </div>
                </div>
                <div class="pt-2 mt-2 border-top small text-warning font-weight-bold text-truncate">
                    Total Akumulasi Shaum
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 3: AGENDA KEGIATAN LEMBAGA TERDEKAT -->
    <div class="modern-card mb-4">
        <div class="modern-card-header">
            <div>
                <h6 class="font-weight-bold text-dark mb-0">
                    <i class="fa fa-calendar-alt text-primary mr-1.5"></i> Agenda Kegiatan Lembaga
                </h6>
                <small class="text-muted">Kegiatan, rapat, atau pelatihan yayasan yang wajib dihadiri</small>
            </div>
            <a href="{{ route('kegiatan.scanner') }}" class="btn btn-sm btn-outline-success font-weight-bold btn-mobile-block" style="border-radius: 7px; font-size: 11.5px;">
                <i class="fa fa-qrcode mr-1"></i> Buka Scanner QR
            </a>
        </div>
        <div class="p-0">
            @forelse ($upcomingKegiatan as $kg)
                @php
                    $isHadir = isset($myKehadiranKegiatan[$kg->id]);
                    $kehadiranRecord = $isHadir ? $myKehadiranKegiatan[$kg->id] : null;
                @endphp
                <div class="list-feed-row">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mr-3 text-white flex-shrink-0"
                            style="width: 38px; height: 38px; background: {{ $isHadir ? 'linear-gradient(135deg, #10b981 0%, #047857 100%)' : 'linear-gradient(135deg, #64748b 0%, #475569 100%)' }}; font-size: 15px;">
                            <i class="{{ $isHadir ? 'fa fa-check' : 'fa fa-calendar' }}"></i>
                        </div>
                        <div>
                            <div class="font-weight-bold text-dark" style="font-size: 13.5px;">
                                {{ $kg->nama_kegiatan }}
                                @if ($kg->jenis_kegiatan === 'pelatihan')
                                    <span class="badge badge-soft-primary py-0 px-1.5 ml-1" style="font-size: 10px;">Pelatihan</span>
                                @endif
                                @if ($kg->tanggal->isToday())
                                    <span class="badge badge-soft-warning py-0 px-1.5 ml-1" style="font-size: 10px;">Hari Ini</span>
                                @endif
                            </div>
                            <div class="small text-muted mt-0.5">
                                <i class="fa fa-calendar mr-1"></i> {{ $kg->tanggal->isoFormat('dddd, D MMMM Y') }}
                                &bull; <i class="fa fa-clock mr-1"></i> {{ $kg->jam_mulai ? substr($kg->jam_mulai, 0, 5) : '-' }} WIB
                                @if ($kg->lokasi)
                                    &bull; <i class="fa fa-map-marker-alt mr-1"></i> {{ $kg->lokasi }}
                                @endif
                            </div>
                        </div>
                    </div>

                    <div>
                        @if ($isHadir)
                            <span class="badge badge-soft-success font-weight-bold px-2.5 py-1" style="font-size: 11.5px;">
                                <i class="fa fa-check-circle mr-1"></i> Sudah Hadir ({{ \Carbon\Carbon::parse($kehadiranRecord->waktu_scan)->format('H:i') }})
                            </span>
                        @else
                            @if ($kg->is_active)
                                <a href="{{ route('kegiatan.scanner') }}" class="btn btn-sm btn-success font-weight-bold text-white shadow-sm" style="border-radius: 7px; font-size: 11.5px; padding: 5px 12px;">
                                    <i class="fa fa-qrcode mr-1"></i> Scan Hadir
                                </a>
                            @else
                                <span class="badge badge-soft-secondary px-2 py-1" style="font-size: 11px;">
                                    Presensi Belum Dibuka
                                </span>
                            @endif
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-4 text-muted small">
                    <i class="fa fa-calendar-check fa-2x opacity-4 mb-2 text-secondary" style="display: block;"></i>
                    Tidak ada kegiatan lembaga terjadwal dalam waktu dekat.
                </div>
            @endforelse
        </div>
    </div>

    <!-- ROW 4 & 5: DOKUMEN & PORTOFOLIO MANDIRI & RIWAYAT PRESENSI HARIAN (2 GRID) -->
    <div class="row">
        <!-- GRID 1: DOKUMEN & PORTOFOLIO MANDIRI -->
        <div class="col-12 col-lg-6 mb-4">
            <div class="modern-card h-100 d-flex flex-column">
                <div class="modern-card-header">
                    <div>
                        <h6 class="font-weight-bold text-dark mb-0.5">
                            <i class="fa fa-folder-open text-warning mr-1.5"></i> Dokumen & Portofolio Mandiri
                        </h6>
                        <small class="text-muted">Ringkasan berkas resmi (klik kartu untuk membuka)</small>
                    </div>
                    <span class="badge badge-light border text-muted small font-weight-normal">
                        {{ array_sum($docCounts) }} Total Berkas
                    </span>
                </div>
                <div class="p-3 p-sm-3.5 flex-grow-1">
                    <div class="row">
                        @foreach ($portofolioModuls as $modul)
                            <div class="col-12 col-sm-6 mb-2.5">
                                <a href="{{ $modul['route'] }}" class="doc-jump-card h-100" style="padding: 12px 14px;">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center min-w-0 flex-grow-1">
                                            <div class="doc-jump-icon mr-3" style="width: 40px; height: 40px; min-width: 40px; font-size: 16px; border-radius: 10px; background: linear-gradient(135deg, {{ $modul['grad'] }});">
                                                <i class="{{ $modul['icon'] }}"></i>
                                            </div>
                                            <div class="min-w-0 flex-grow-1">
                                                <div class="doc-jump-count" style="font-size: 16px; font-weight: 800;">{{ $modul['count'] }}</div>
                                                <div class="doc-jump-label text-truncate" style="font-size: 12px; margin-top: 1px;" title="{{ $modul['label'] }}">{{ $modul['label'] }}</div>
                                                @if ($modul['badge'])
                                                    <div class="mt-0.5">
                                                        <span class="badge badge-soft-warning py-0 px-1 font-weight-bold" style="font-size: 9.5px;">
                                                            {{ $modul['badge'] }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="pl-2 flex-shrink-0">
                                            <i class="fa fa-chevron-right doc-jump-arrow" style="font-size: 11px;"></i>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- GRID 2: RIWAYAT PRESENSI HARIAN (7 HARI TERAKHIR - PERSINGKAT) -->
        <div class="col-12 col-lg-6 mb-4">
            <div class="modern-card h-100 d-flex flex-column">
                <div class="modern-card-header">
                    <div>
                        <h6 class="font-weight-bold text-dark mb-0.5">
                            <i class="fa fa-history text-success mr-1.5"></i> Riwayat Presensi Harian
                        </h6>
                        <small class="text-muted">7 hari terakhir &bull; Catatan kehadiran & shaum</small>
                    </div>
                    <span class="badge badge-soft-primary small font-weight-bold">
                        7 Hari Terakhir
                    </span>
                </div>
                <div class="p-0 flex-grow-1">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">
                                <tr>
                                    <th class="py-2.5 px-3">Hari & Tanggal</th>
                                    <th class="py-2.5 px-2">Jam (In &rarr; Out)</th>
                                    <th class="py-2.5 px-2 text-center">Status</th>
                                    <th class="py-2.5 px-2 text-center">Shaum</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($myRecentPresensi as $rp)
                                    <tr>
                                        <td class="px-3 py-2">
                                            <div class="font-weight-bold text-dark" style="font-size: 12px;">
                                                {{ $rp->tanggal->isoFormat('dddd, D MMM') }}
                                            </div>
                                            @if ($rp->tanggal->isToday())
                                                <span class="badge badge-soft-success py-0 px-1 font-weight-bold" style="font-size: 9px;">Hari Ini</span>
                                            @endif
                                            @if ($rp->izin_terlambat)
                                                <span class="text-info font-weight-bold ml-1" style="font-size: 9.5px;" title="Dispensasi Keterlambatan"><i class="fa fa-check-circle"></i> Disp.</span>
                                            @endif
                                        </td>
                                        <td class="px-2 py-2">
                                            <div class="font-weight-bold text-dark font-monospace" style="font-size: 11.5px;">
                                                {{ $rp->jam_masuk ? substr($rp->jam_masuk, 0, 5) : '-' }}
                                                <span class="text-muted font-weight-normal">&rarr;</span>
                                                {{ $rp->jam_pulang ? substr($rp->jam_pulang, 0, 5) : ($rp->jam_masuk ? '...' : '-') }}
                                            </div>
                                            @if ($rp->durasi_kerja)
                                                <div class="text-muted" style="font-size: 10.5px;">
                                                    <i class="fa fa-clock mr-0.5 opacity-6"></i> {{ $rp->durasi_kerja }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-2 py-2 text-center">
                                            @if ($rp->status === 'hadir')
                                                <span class="badge badge-soft-success font-weight-bold px-2 py-0.5" style="font-size: 10.5px;">Hadir</span>
                                            @elseif ($rp->status === 'terlambat')
                                                <span class="badge badge-soft-warning font-weight-bold px-2 py-0.5" style="font-size: 10.5px;" title="Terlambat {{ $rp->keterlambatan_menit }} menit">
                                                    +{{ $rp->keterlambatan_menit }}m
                                                </span>
                                            @elseif ($rp->status === 'tugas')
                                                <span class="badge badge-soft-primary font-weight-bold px-2 py-0.5" style="font-size: 10.5px;">Tugas</span>
                                            @elseif ($rp->status === 'izin')
                                                <span class="badge badge-soft-info font-weight-bold px-2 py-0.5" style="font-size: 10.5px;">Izin</span>
                                            @elseif ($rp->status === 'sakit')
                                                <span class="badge badge-soft-secondary font-weight-bold px-2 py-0.5" style="font-size: 10.5px;">Sakit</span>
                                            @elseif ($rp->status === 'cuti')
                                                <span class="badge badge-soft-info font-weight-bold px-2 py-0.5" style="font-size: 10.5px;">Cuti</span>
                                            @else
                                                <span class="badge badge-soft-danger font-weight-bold px-2 py-0.5" style="font-size: 10.5px;">{{ ucfirst($rp->status) }}</span>
                                            @endif
                                        </td>
                                        <td class="px-2 py-2 text-center">
                                            @if ($rp->shaum)
                                                <span class="badge badge-soft-success font-weight-bold px-2 py-0.5" title="Shaum" style="font-size: 10.5px;">
                                                    <i class="fa fa-moon mr-0.5"></i> Ya
                                                </span>
                                            @else
                                                <span class="text-muted" style="font-size: 11px;">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted small">
                                            <i class="fa fa-calendar-times fa-2x opacity-4 mb-2 text-secondary" style="display: block;"></i>
                                            Belum ada data presensi 7 hari terakhir.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
