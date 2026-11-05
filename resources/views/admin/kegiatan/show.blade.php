@extends('layouts.appui')

@section('title')
    <title>Monitoring & Rekap Kehadiran - {{ $kegiatan->nama_kegiatan }}</title>
@endsection

@section('content')
<style>
    /* ==========================================================================
       MODERN EVENT ATTENDANCE MONITORING UI
       ========================================================================== */
    .event-hero-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        padding: 16px 20px;
        margin-bottom: 20px;
    }

    .header-back-btn {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        color: #475569;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s ease;
        text-decoration: none !important;
        flex-shrink: 0;
    }
    .header-back-btn:hover {
        background: #f8fafc;
        color: #0f172a;
        border-color: #cbd5e1;
        transform: translateX(-2px);
    }

    .header-meta-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12.5px;
        color: #64748b;
        font-weight: 500;
    }
    .header-meta-item i {
        color: #94a3b8;
        font-size: 11.5px;
    }
    .header-meta-dot {
        color: #cbd5e1;
        font-size: 10px;
        user-select: none;
    }

    /* Action Toolbar Buttons */
    .btn-action-modern {
        border-radius: 10px;
        font-size: 12.5px;
        font-weight: 700;
        padding: 8px 14px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.15s ease;
    }
    .btn-action-modern:hover {
        transform: translateY(-1px);
    }

    /* KPI Cards */
    .kpi-card {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.02);
        padding: 18px 20px;
        width: 100%;
        min-height: 128px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        position: relative;
        overflow: hidden;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.06);
    }
    .kpi-icon-circle {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }
    .kpi-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin-bottom: 4px;
    }
    .kpi-value {
        font-size: 26px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
    }
    .mini-progress {
        height: 6px;
        background: #f1f5f9;
        border-radius: 999px;
        overflow: hidden;
        margin-top: 10px;
    }
    .mini-progress-bar {
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(90deg, #10b981, #059669);
        transition: width 0.4s ease;
    }

    /* Filter Pills */
    .status-filter-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 13px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        color: #475569;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        cursor: pointer;
        transition: all 0.15s ease;
        user-select: none;
    }
    .status-filter-pill:hover {
        background: #f1f5f9;
        color: #1e293b;
    }
    .status-filter-pill.active {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
        box-shadow: 0 2px 6px rgba(37, 99, 235, 0.22);
    }
    .status-filter-pill .pill-count {
        padding: 1px 7px;
        border-radius: 10px;
        font-size: 10.5px;
        background: rgba(15, 23, 42, 0.07);
        color: inherit;
    }
    .status-filter-pill.active .pill-count {
        background: rgba(255, 255, 255, 0.22);
        color: #ffffff;
    }

    /* Modern Table */
    .kegiatan-log-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .kegiatan-log-table thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        padding: 12px 16px;
        border-bottom: 2px solid #e2e8f0;
        white-space: nowrap;
    }
    .kegiatan-log-table tbody td {
        padding: 12px 16px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13px;
        color: #334155;
    }
    .kegiatan-log-table tbody tr:hover td {
        background-color: #f8fafc;
    }

    /* Avatar */
    .emp-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 13px;
        color: #fff;
        flex-shrink: 0;
        object-fit: cover;
        border: 2px solid #fff;
        box-shadow: 0 2px 5px rgba(0,0,0,0.08);
    }
    .nipy-chip {
        font-family: 'SFMono-Regular', Consolas, monospace;
        font-size: 10.5px;
        background: #f1f5f9;
        color: #475569;
        padding: 1px 6px;
        border-radius: 4px;
        border: 1px solid #e2e8f0;
    }

    /* Status Badges */
    .badge-status-hadir { background-color: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; font-weight: 700; }
    .badge-status-izin  { background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; font-weight: 700; }
    .badge-status-sakit { background-color: #fef3c7; color: #b45309; border: 1px solid #fde68a; font-weight: 700; }
    .badge-status-alpa  { background-color: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; font-weight: 700; }

    /* Manual Modal Employee Checklist */
    .manual-emp-list {
        max-height: 270px;
        overflow-y: auto;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        background: #f8fafc;
        padding: 6px;
    }
    .manual-emp-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 10px;
        border-radius: 8px;
        background: #ffffff;
        border: 1px solid #edf2f7;
        margin-bottom: 5px;
        cursor: pointer;
        transition: all 0.12s ease;
    }
    .manual-emp-item:hover {
        border-color: #cbd5e1;
        background: #f1f5f9;
    }
    .manual-emp-item.selected {
        border-color: #3b82f6;
        background: #eff6ff;
    }

    /* Status Radio Selector in Modal */
    .status-choice-group {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 8px;
    }
    .status-choice-label {
        margin: 0;
        cursor: pointer;
    }
    .status-choice-label input {
        display: none;
    }
    .status-choice-box {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 10px 6px;
        border-radius: 10px;
        border: 1.5px solid #e2e8f0;
        background: #ffffff;
        font-size: 12px;
        font-weight: 700;
        color: #475569;
        transition: all 0.15s ease;
    }
    .status-choice-label input:checked + .status-choice-box.box-hadir {
        border-color: #10b981;
        background: #ecfdf5;
        color: #047857;
    }
    .status-choice-label input:checked + .status-choice-box.box-izin {
        border-color: #0284c7;
        background: #f0f9ff;
        color: #0369a1;
    }
    .status-choice-label input:checked + .status-choice-box.box-sakit {
        border-color: #d97706;
        background: #fffbeb;
        color: #b45309;
    }
    .status-choice-label input:checked + .status-choice-box.box-alpa {
        border-color: #ef4444;
        background: #fef2f2;
        color: #b91c1c;
    }

    /* Fixed Centered Overlay Modal Styles (Tailwind Layout Adapter) */
    .modal {
        display: none;
        position: fixed;
        inset: 0;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        z-index: 99999 !important;
        overflow-x: hidden;
        overflow-y: auto;
        outline: 0;
        background-color: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(4px);
        padding: 1rem;
    }
    .modal.show,
    .modal.is-open {
        display: flex !important;
        align-items: center;
        justify-content: center;
    }
    .modal-dialog {
        position: relative;
        width: 100%;
        max-width: 520px;
        margin: auto;
        pointer-events: auto;
    }
    .modal-backdrop {
        display: none !important;
    }
    .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .modal-header .close {
        background: transparent;
        border: 0;
        font-size: 1.5rem;
        line-height: 1;
        color: #ffffff;
        opacity: 0.85;
        cursor: pointer;
        padding: 0 4px;
    }
    .modal-header .close:hover {
        opacity: 1;
    }
</style>

@php
    $recordedNipys = $kehadiran_list->pluck('karyawan_nipy')->flip()->all();
    $recordedStatusMap = $kehadiran_list->pluck('status', 'karyawan_nipy')->all();
    $belumPresensiList = $karyawanList->filter(fn($k) => !isset($recordedNipys[$k->nipy]))->values();
    $totalAlpa = $statistik['total_alpa'] ?? 0;

    $avatarGradients = [
        'linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%)',
        'linear-gradient(135deg, #059669 0%, #047857 100%)',
        'linear-gradient(135deg, #d97706 0%, #b45309 100%)',
        'linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%)',
        'linear-gradient(135deg, #db2777 0%, #be185d 100%)',
        'linear-gradient(135deg, #0891b2 0%, #0e7490 100%)',
    ];
@endphp

<div class="app-main__inner">
    {{-- CLEAN & MODERN EVENT HEADER --}}
    <div class="event-hero-card">
        <div class="d-flex flex-wrap justify-content-between align-items-center" style="gap: 16px;">
            {{-- Left: Back Navigation + Event Title + Clean Meta Line --}}
            <div class="d-flex align-items-center" style="gap: 14px;">
                <a href="{{ route('kegiatan.index') }}" class="header-back-btn" title="Kembali Kegiatan">
                    <i class="fa fa-arrow-left"></i>
                </a>
                <div>
                    <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
                        <h4 class="font-weight-bold text-dark mb-0" style="font-size: 18px; letter-spacing: -0.2px;">
                            {{ $kegiatan->nama_kegiatan }}
                        </h4>
                        <span class="d-inline-flex align-items-center px-2 py-0.5" style="font-size: 11px; font-weight: 700; border-radius: 20px; gap: 5px; {{ $kegiatan->is_active ? 'background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0;' : 'background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca;' }}">
                            <span style="width: 6px; height: 6px; border-radius: 50%; background: {{ $kegiatan->is_active ? '#10b981' : '#ef4444' }};"></span>
                            {{ $kegiatan->is_active ? 'Gerbang Dibuka' : 'Gerbang Ditutup' }}
                        </span>
                    </div>

                    <div class="d-flex flex-wrap align-items-center mt-1" style="gap: 8px;">
                        <span class="header-meta-item">
                            <i class="fa fa-calendar-alt"></i> {{ $kegiatan->tanggal->isoFormat('dddd, D MMMM Y') }}
                        </span>
                        @if($kegiatan->jam_mulai)
                            <span class="header-meta-dot">•</span>
                            <span class="header-meta-item">
                                <i class="fa fa-clock"></i> {{ \Carbon\Carbon::parse($kegiatan->jam_mulai)->format('H:i') }}{{ $kegiatan->jam_selesai ? ' - ' . \Carbon\Carbon::parse($kegiatan->jam_selesai)->format('H:i') . ' WIB' : ' WIB' }}
                            </span>
                        @endif
                        @if($kegiatan->lokasi)
                            <span class="header-meta-dot">•</span>
                            <span class="header-meta-item">
                                <i class="fa fa-map-marker-alt"></i> {{ $kegiatan->lokasi }}
                            </span>
                        @endif
                        @if($kegiatan->pengisi)
                            <span class="header-meta-dot">•</span>
                            <span class="header-meta-item">
                                <i class="fa fa-user-tie"></i> {{ $kegiatan->pengisi }}
                            </span>
                        @endif
                        @if($kegiatan->tahunAjaran)
                            <span class="header-meta-dot">•</span>
                            <span class="header-meta-item">
                                <i class="fa fa-graduation-cap"></i> TA {{ $kegiatan->tahunAjaran->nama_tahun_ajaran }} ({{ ucfirst($kegiatan->tahunAjaran->semester) }})
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Right: Clean Grouped Actions --}}
            <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
                <button type="button" class="btn btn-light border btn-action-modern text-dark" onclick="openKegiatanModal('manualModal')">
                    <i class="fa fa-user-plus text-primary"></i> Presensi Manual
                </button>

                <button type="button" class="btn btn-light border btn-action-modern text-dark" onclick="openKegiatanModal('scannerModal')">
                    <i class="fa fa-camera text-success"></i> Scan Kamera
                </button>

                <a href="{{ route('kegiatan.qr', $kegiatan->id) }}" target="_blank" class="btn btn-primary btn-action-modern shadow-sm">
                    <i class="fa fa-qrcode"></i> Layar QR Proyektor
                </a>
            </div>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" style="border-radius: 12px;" role="alert">
            <i class="fa fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" style="border-radius: 12px;" role="alert">
            <i class="fa fa-exclamation-circle mr-1"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- 4 MODERN KPI CARDS --}}
    <div class="row mb-2">
        {{-- Card 1: Total Hadir --}}
        <div class="col-md-6 col-xl-3 d-flex mb-3">
            <div class="kpi-card" style="border-left: 4px solid #10b981;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-label">Total Hadir</div>
                        <div class="d-flex align-items-baseline" style="gap: 8px;">
                            <div class="kpi-value text-success">{{ $statistik['total_hadir'] }}</div>
                            <span class="badge badge-pill" style="background: #dcfce7; color: #15803d; font-size: 11px;">
                                {{ $statistik['persentase_hadir'] }}%
                            </span>
                        </div>
                    </div>
                    <div class="kpi-icon-circle" style="background: #ecfdf5; color: #059669;">
                        <i class="fa fa-user-check"></i>
                    </div>
                </div>
                <div>
                    <div class="mini-progress">
                        <div class="mini-progress-bar" style="width: {{ min(100, $statistik['persentase_hadir']) }}%;"></div>
                    </div>
                    <div class="small text-muted mt-2" style="font-size: 11.5px;">
                        Dari <strong>{{ $statistik['total_karyawan'] }}</strong> total pegawai aktif
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Izin / Sakit / Alpa --}}
        <div class="col-md-6 col-xl-3 d-flex mb-3">
            <div class="kpi-card" style="border-left: 4px solid #0284c7;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-label">Izin / Sakit / Alpa</div>
                        <div class="kpi-value" style="color: #0284c7;">
                            {{ $statistik['total_izin'] + $statistik['total_sakit'] + $totalAlpa }}
                        </div>
                    </div>
                    <div class="kpi-icon-circle" style="background: #f0f9ff; color: #0284c7;">
                        <i class="fa fa-envelope-open-text"></i>
                    </div>
                </div>
                <div class="d-flex flex-wrap align-items-center mt-3" style="gap: 6px;">
                    <span class="badge badge-status-izin px-2 py-1" style="border-radius: 6px; font-size: 11px;">
                        {{ $statistik['total_izin'] }} Izin
                    </span>
                    <span class="badge badge-status-sakit px-2 py-1" style="border-radius: 6px; font-size: 11px;">
                        {{ $statistik['total_sakit'] }} Sakit
                    </span>
                    @if($totalAlpa > 0)
                        <span class="badge badge-status-alpa px-2 py-1" style="border-radius: 6px; font-size: 11px;">
                            {{ $totalAlpa }} Alpa
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Card 3: Belum Presensi --}}
        <div class="col-md-6 col-xl-3 d-flex mb-3">
            <div class="kpi-card" style="border-left: 4px solid #f59e0b;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-label">Belum Presensi</div>
                        <div class="kpi-value" style="color: #d97706;">
                            {{ $belumPresensiList->count() }}
                        </div>
                    </div>
                    <div class="kpi-icon-circle" style="background: #fffbeb; color: #d97706;">
                        <i class="fa fa-user-clock"></i>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <span class="small text-muted" style="font-size: 11.5px;">
                        Tercatat: <strong>{{ $statistik['total_tercatat'] }}</strong> pegawai
                    </span>
                    <button type="button" class="btn btn-link btn-sm p-0 font-weight-bold" style="font-size: 11.5px; color: #d97706;"
                        onclick="switchLogView('belum', document.getElementById('pillBelumPresensi'))">
                        Lihat Daftar <i class="fa fa-arrow-right ml-1"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Card 4: Keamanan QR & Gerbang --}}
        <div class="col-md-6 col-xl-3 d-flex mb-3">
            <div class="kpi-card" style="border-left: 4px solid {{ $kegiatan->is_active ? '#10b981' : '#ef4444' }};">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-label">Status & Token QR</div>
                        <div class="font-weight-bold mt-1 {{ $kegiatan->is_active ? 'text-success' : 'text-danger' }}" style="font-size: 17px; line-height: 1.4;">
                            <i class="fa {{ $kegiatan->is_active ? 'fa-unlock-alt' : 'fa-lock' }} mr-1"></i>
                            {{ $kegiatan->is_active ? 'Gerbang Terbuka' : 'Gerbang Ditutup' }}
                        </div>
                    </div>
                    <form action="{{ route('kegiatan.regenerate-qr', $kegiatan->id) }}" method="POST" class="m-0"
                        onsubmit="return confirm('Regenerasi QR token akan membatalkan QR Code lama. Lanjutkan?');">
                        @csrf
                        <button type="submit" class="kpi-icon-circle border-0"
                            style="background: #fffbeb; color: #d97706; cursor: pointer;" title="Perbarui Token QR Code">
                            <i class="fa fa-sync-alt"></i>
                        </button>
                    </form>
                </div>
                <div class="d-flex align-items-center justify-content-between mt-3">
                    <span class="small text-muted" style="font-size: 11.5px;">Token Aktif:</span>
                    <span class="nipy-chip text-primary font-weight-bold">
                        {{ substr($kegiatan->qr_token, 0, 12) }}...
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN ATTENDANCE LOG & BELUM PRESENSI CARD --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; overflow: hidden;">
        {{-- Card Header: Filter Pills + Search Input --}}
        <div class="card-header bg-white py-3 px-4 border-bottom">
            <div class="d-flex flex-wrap justify-content-between align-items-center" style="gap: 12px;">
                {{-- Status Filter Pills --}}
                <div class="d-flex flex-wrap align-items-center" style="gap: 6px;">
                    <button type="button" class="status-filter-pill active" onclick="switchLogView('all', this)">
                        <i class="fa fa-list-ul"></i> Semua Tercatat
                        <span class="pill-count">{{ $kehadiran_list->count() }}</span>
                    </button>
                    <button type="button" class="status-filter-pill" onclick="switchLogView('hadir', this)">
                        <i class="fa fa-check-circle text-success"></i> Hadir
                        <span class="pill-count">{{ $statistik['total_hadir'] }}</span>
                    </button>
                    <button type="button" class="status-filter-pill" onclick="switchLogView('izin', this)">
                        <i class="fa fa-envelope text-info"></i> Izin
                        <span class="pill-count">{{ $statistik['total_izin'] }}</span>
                    </button>
                    <button type="button" class="status-filter-pill" onclick="switchLogView('sakit', this)">
                        <i class="fa fa-notes-medical text-warning"></i> Sakit
                        <span class="pill-count">{{ $statistik['total_sakit'] }}</span>
                    </button>
                    @if($totalAlpa > 0)
                        <button type="button" class="status-filter-pill" onclick="switchLogView('alpa', this)">
                            <i class="fa fa-times-circle text-danger"></i> Alpa
                            <span class="pill-count">{{ $totalAlpa }}</span>
                        </button>
                    @endif
                    <button type="button" id="pillBelumPresensi" class="status-filter-pill" onclick="switchLogView('belum', this)">
                        <i class="fa fa-user-clock text-warning"></i> Belum Presensi
                        <span class="pill-count">{{ $belumPresensiList->count() }}</span>
                    </button>
                </div>

                {{-- Live Search --}}
                <div class="d-flex align-items-center" style="gap: 10px;">
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light border-right-0" style="border-radius: 8px 0 0 8px;">
                                <i class="fa fa-search text-muted"></i>
                            </span>
                        </div>
                        <input type="text" id="logSearchInput" class="form-control border-left-0"
                            style="border-radius: 0 8px 8px 0;"
                            placeholder="Cari nama, NIPY, unit..." onkeyup="filterLogRows()">
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION A: TABEL PESERTA SUDAH TERCATAT --}}
        <div id="recordedTableContainer" class="table-responsive">
            <table class="kegiatan-log-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">#</th>
                        <th style="min-width: 240px;">Pegawai</th>
                        <th style="min-width: 140px;">Unit / Kategori</th>
                        <th style="min-width: 140px;">Waktu Presensi</th>
                        <th class="text-center" style="min-width: 150px;">Status & Metode</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody id="attendance-table-body">
                    @forelse($kehadiran_list as $index => $item)
                        @php
                            $kar = $item->karyawan;
                            $namaLengkap = $kar->nama_lengkap ?? '-';
                            $initial = strtoupper(substr($namaLengkap, 0, 1));
                            $gradient = $avatarGradients[$index % count($avatarGradients)];
                            $badgeClass = match($item->status) {
                                'hadir' => 'badge-status-hadir',
                                'izin'  => 'badge-status-izin',
                                'sakit' => 'badge-status-sakit',
                                default => 'badge-status-alpa',
                            };
                            $statusIcon = match($item->status) {
                                'hadir' => 'fa-check-circle',
                                'izin'  => 'fa-paper-plane',
                                'sakit' => 'fa-Briefcase-medical',
                                default => 'fa-times-circle',
                            };
                        @endphp
                        <tr class="recorded-row"
                            data-status="{{ $item->status }}"
                            data-search="{{ strtolower($namaLengkap . ' ' . $item->karyawan_nipy . ' ' . ($kar->unit ?? '') . ' ' . ($kar->jabatan ?? '')) }}">
                            <td class="text-center font-weight-bold text-muted row-index">{{ $index + 1 }}</td>

                            {{-- Pegawai --}}
                            <td>
                                <div class="d-flex align-items-center" style="gap: 11px;">
                                    @if(!empty($kar?->foto_url))
                                        <img src="{{ $kar->foto_url }}" alt="{{ $namaLengkap }}" class="emp-avatar"
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';">
                                        <div class="emp-avatar" style="background: {{ $gradient }}; display: none;">{{ $initial }}</div>
                                    @else
                                        <div class="emp-avatar" style="background: {{ $gradient }};">{{ $initial }}</div>
                                    @endif
                                    <div style="line-height: 1.3;">
                                        <div class="font-weight-bold text-dark">
                                            {{ $namaLengkap }}{{ !empty($kar?->gelar) ? ', ' . $kar->gelar : '' }}
                                        </div>
                                        <div class="d-flex flex-wrap align-items-center mt-1" style="gap: 6px;">
                                            <span class="nipy-chip">{{ $item->karyawan_nipy }}</span>
                                            @if(!empty($kar?->jabatan))
                                                <span class="text-muted" style="font-size: 11.5px;">• {{ $kar->jabatan }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Unit / Kategori --}}
                            <td>
                                <span class="badge badge-light border text-dark font-weight-bold px-2 py-1" style="border-radius: 6px;">
                                    {{ $kar->unit ?? '-' }}
                                </span>
                                @if(!empty($kar?->kategori?->nama_kategori))
                                    <div class="text-muted mt-1" style="font-size: 11px;">
                                        {{ $kar->kategori->nama_kategori }}
                                    </div>
                                @endif
                            </td>

                            {{-- Waktu Presensi --}}
                            <td>
                                <div class="font-weight-bold text-dark" style="font-family: monospace; font-size: 13px;">
                                    <i class="fa fa-clock text-primary mr-1"></i>{{ $item->waktu_scan->format('H:i:s') }}
                                </div>
                                <div class="text-muted" style="font-size: 11px;">
                                    {{ $item->waktu_scan->format('d M Y') }}
                                </div>
                            </td>

                            {{-- Status & Metode --}}
                            <td class="text-center">
                                <div class="d-inline-flex flex-column align-items-center" style="gap: 4px;">
                                    <span class="badge {{ $badgeClass }} px-2 py-1 text-uppercase" style="border-radius: 6px; font-size: 10.5px; letter-spacing: 0.4px;">
                                        {{ $item->status }}
                                    </span>
                                    <span class="text-muted" style="font-size: 11px;">
                                        @if($item->metode_presensi == 'qr_scan')
                                            <i class="fa fa-qrcode text-dark mr-1"></i>QR Scan
                                        @else
                                            <i class="fa fa-keyboard text-secondary mr-1"></i>Manual
                                        @endif
                                    </span>
                                </div>
                            </td>

                            {{-- Catatan --}}
                            <td>
                                @if(!empty($item->catatan))
                                    <span class="text-dark" style="font-size: 12.5px;">{{ $item->catatan }}</span>
                                @else
                                    <span class="text-muted" style="font-size: 12px;">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr id="emptyRecordedRow">
                            <td colspan="6" class="text-center py-5 text-muted">
                                <div class="mb-2">
                                    <i class="fa fa-qrcode fa-3x text-secondary" style="opacity: 0.35;"></i>
                                </div>
                                <h6 class="font-weight-bold text-dark mb-1">Belum Ada Kehadiran Tercatat</h6>
                                <p class="small mb-0">Gunakan tombol <strong>Scan Kamera</strong>, <strong>Layar QR Proyektor</strong>, atau <strong>Presensi Manual</strong> di atas untuk mulai mencatat kehadiran.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- SECTION B: TABEL PEGAWAI BELUM PRESENSI (Hidden by default) --}}
        <div id="unrecordedTableContainer" class="table-responsive" style="display: none;">
            <div class="bg-light px-4 py-2 border-bottom d-flex flex-wrap justify-content-between align-items-center">
                <span class="small text-muted">
                    Menampilkan daftar pegawai aktif yang <strong>belum tercatat</strong> pada kegiatan ini.
                </span>
                <button type="button" class="btn btn-sm btn-primary font-weight-bold" style="border-radius: 8px;"
                    onclick="openManualModalForUnrecorded()">
                    <i class="fa fa-check-double mr-1"></i> Catat Massal di Modal Manual
                </button>
            </div>
            <table class="kegiatan-log-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">#</th>
                        <th style="min-width: 250px;">Pegawai</th>
                        <th>Unit / Bagian</th>
                        <th>Jabatan</th>
                        <th class="text-center" style="width: 180px;">Aksi Cepat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($belumPresensiList as $idx => $emp)
                        @php
                            $empInitial = strtoupper(substr($emp->nama_lengkap ?? 'P', 0, 1));
                            $empGradient = $avatarGradients[$idx % count($avatarGradients)];
                        @endphp
                        <tr class="unrecorded-row"
                            data-search="{{ strtolower($emp->nama_lengkap . ' ' . $emp->nipy . ' ' . ($emp->unit ?? '') . ' ' . ($emp->jabatan ?? '')) }}">
                            <td class="text-center font-weight-bold text-muted row-index">{{ $idx + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center" style="gap: 11px;">
                                    @if(!empty($emp->foto_url))
                                        <img src="{{ $emp->foto_url }}" alt="{{ $emp->nama_lengkap }}" class="emp-avatar"
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';">
                                        <div class="emp-avatar" style="background: {{ $empGradient }}; display: none;">{{ $empInitial }}</div>
                                    @else
                                        <div class="emp-avatar" style="background: {{ $empGradient }};">{{ $empInitial }}</div>
                                    @endif
                                    <div>
                                        <div class="font-weight-bold text-dark">
                                            {{ $emp->nama_lengkap }}{{ !empty($emp->gelar) ? ', ' . $emp->gelar : '' }}
                                        </div>
                                        <span class="nipy-chip mt-1 d-inline-block">{{ $emp->nipy }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-light border text-dark px-2 py-1" style="border-radius: 6px;">
                                    {{ $emp->unit ?? '-' }}
                                </span>
                            </td>
                            <td class="text-muted">{{ $emp->jabatan ?? '-' }}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-primary font-weight-bold"
                                    style="border-radius: 8px; font-size: 11.5px;"
                                    onclick="quickSelectEmployeeManual('{{ $emp->nipy }}')">
                                    <i class="fa fa-user-check mr-1"></i> Catat Presensi
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fa fa-check-circle fa-3x text-success mb-2"></i>
                                <h6 class="font-weight-bold text-dark mb-0">Luar Biasa! Seluruh Pegawai Sudah Tercatat</h6>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL SCANNER KAMERA LIVE --}}
<div class="modal fade" id="scannerModal" tabindex="-1" role="dialog" aria-labelledby="scannerModalLabel" aria-hidden="true" onclick="if(event.target === this) closeKegiatanModal('scannerModal')">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-dark text-white border-0 py-3">
                <h5 class="modal-title font-weight-bold" id="scannerModalLabel" style="font-size: 16px;">
                    <i class="fa fa-camera text-warning mr-2"></i> Pemindai QR & Barcode Presensi
                </h5>
                <button type="button" class="close text-white" aria-label="Close" onclick="closeKegiatanModal('scannerModal')" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center p-4">
                <div id="reader" style="width: 100%; min-height: 280px; border-radius: 12px; overflow: hidden; background: #0f172a;"></div>
                <div id="scanner-status" class="mt-3 alert alert-info py-2 small border-0" style="border-radius: 8px;">
                    <i class="fa fa-info-circle mr-1"></i> Arahkan kamera ke QR Code kartu pegawai atau ketik NIPY di bawah.
                </div>
                <div class="form-group text-left mt-3 mb-0">
                    <label class="small font-weight-bold text-dark">Input NIPY Manual / Barcode Scanner USB</label>
                    <div class="input-group">
                        <input type="text" id="manual_nipy_input" class="form-control" style="border-radius: 8px 0 0 8px;"
                            placeholder="Scan barcode kartu atau ketik NIPY..." autofocus>
                        <div class="input-group-append">
                            <button class="btn btn-primary font-weight-bold px-3" style="border-radius: 0 8px 8px 0;" type="button" onclick="submitNipyScan()">
                                <i class="fa fa-check mr-1"></i> Submit
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light py-2 border-0">
                <button type="button" class="btn btn-secondary btn-sm px-3" style="border-radius: 8px;" onclick="closeKegiatanModal('scannerModal')" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL INPUT MANUAL MODERN --}}
<div class="modal fade" id="manualModal" tabindex="-1" role="dialog" aria-labelledby="manualModalLabel" aria-hidden="true" onclick="if(event.target === this) closeKegiatanModal('manualModal')">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-primary text-white border-0 py-3">
                <h5 class="modal-title font-weight-bold" id="manualModalLabel" style="font-size: 16px;">
                    <i class="fa fa-user-plus mr-2"></i> Catat Kehadiran Manual
                </h5>
                <button type="button" class="close text-white" aria-label="Close" onclick="closeKegiatanModal('manualModal')" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('kegiatan.store-manual', $kegiatan->id) }}" method="POST" id="manualPresensiForm">
                @csrf
                <div class="modal-body p-4">
                    {{-- Search & Quick Select Header --}}
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="font-weight-bold text-dark mb-0" style="font-size: 13px;">
                            Pilih Pegawai <span class="text-danger">*</span>
                            <span class="badge badge-primary ml-1" id="selectedEmpCountBadge">0 dipilih</span>
                        </label>
                        <div class="d-flex" style="gap: 6px;">
                            <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2" style="font-size: 11px; border-radius: 6px;" onclick="selectAllUnrecordedManual()">
                                Pilih Belum Hadir
                            </button>
                            <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2" style="font-size: 11px; border-radius: 6px;" onclick="clearManualSelection()">
                                Reset
                            </button>
                        </div>
                    </div>

                    <div class="input-group input-group-sm mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white"><i class="fa fa-search text-muted"></i></span>
                        </div>
                        <input type="text" id="manualEmpSearch" class="form-control" placeholder="Cari nama atau NIPY pegawai..." onkeyup="filterManualEmpList()">
                    </div>

                    {{-- Checklist Pegawai --}}
                    <div class="manual-emp-list mb-3" id="manualEmpListContainer">
                        @foreach($karyawanList as $kar)
                            @php
                                $existingStatus = $recordedStatusMap[$kar->nipy] ?? null;
                            @endphp
                            <label class="manual-emp-item mb-1"
                                data-search="{{ strtolower($kar->nama_lengkap . ' ' . $kar->nipy . ' ' . ($kar->unit ?? '')) }}"
                                data-recorded="{{ $existingStatus ? '1' : '0' }}">
                                <div class="d-flex align-items-center" style="gap: 10px;">
                                    <input type="checkbox" name="karyawan_nipy[]" value="{{ $kar->nipy }}"
                                        class="manual-emp-checkbox" onchange="updateManualSelectedState(this)">
                                    <div style="line-height: 1.25;">
                                        <div class="font-weight-bold text-dark" style="font-size: 12.5px;">
                                            {{ $kar->nama_lengkap }}{{ !empty($kar->gelar) ? ', ' . $kar->gelar : '' }}
                                        </div>
                                        <div class="text-muted" style="font-size: 11px;">
                                            <span class="font-mono">{{ $kar->nipy }}</span> • {{ $kar->unit ?? 'Umum' }}
                                        </div>
                                    </div>
                                </div>
                                @if($existingStatus)
                                    <span class="badge badge-light border text-success" style="font-size: 10px;">
                                        Sudah: {{ strtoupper($existingStatus) }}
                                    </span>
                                @else
                                    <span class="badge badge-light border text-muted" style="font-size: 10px;">
                                        Belum
                                    </span>
                                @endif
                            </label>
                        @endforeach
                    </div>

                    {{-- Status Kehadiran Pills --}}
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark" style="font-size: 13px;">Status Kehadiran <span class="text-danger">*</span></label>
                        <div class="status-choice-group">
                            <label class="status-choice-label">
                                <input type="radio" name="status" value="hadir" checked>
                                <span class="status-choice-box box-hadir">
                                    <i class="fa fa-check-circle mb-1"></i> Hadir
                                </span>
                            </label>
                            <label class="status-choice-label">
                                <input type="radio" name="status" value="izin">
                                <span class="status-choice-box box-izin">
                                    <i class="fa fa-paper-plane mb-1"></i> Izin
                                </span>
                            </label>
                            <label class="status-choice-label">
                                <input type="radio" name="status" value="sakit">
                                <span class="status-choice-box box-sakit">
                                    <i class="fa fa-notes-medical mb-1"></i> Sakit
                                </span>
                            </label>
                            <label class="status-choice-label">
                                <input type="radio" name="status" value="alpa">
                                <span class="status-choice-box box-alpa">
                                    <i class="fa fa-times-circle mb-1"></i> Alpa
                                </span>
                            </label>
                        </div>
                    </div>

                    {{-- Catatan --}}
                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-dark" style="font-size: 13px;">Catatan (Opsional)</label>
                        <input type="text" name="catatan" class="form-control" style="border-radius: 8px;"
                            placeholder="Contoh: Dispensasi tugas luar / izin sakit">
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 border-0">
                    <button type="button" class="btn btn-secondary btn-sm px-3" style="border-radius: 8px;" onclick="closeKegiatanModal('manualModal')" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm font-weight-bold px-4" style="border-radius: 8px;">
                        <i class="fa fa-save mr-1"></i> Simpan Kehadiran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- QR Code Reader Library (HTML5-QRCode via CDN) --}}
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    let html5QrCode = null;
    let activeLogFilter = 'all';
    const qrToken = "{{ $kegiatan->qr_token }}";

    // Modal Open & Close Handler (Direct DOM & Stacking Context Guard)
    function openKegiatanModal(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        // Pindahkan modal langsung ke body agar bebas dari transform/overflow layout
        if (modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }

        modal.classList.add('show', 'is-open');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';

        if (modalId === 'scannerModal') {
            startScanner();
            setTimeout(function() {
                const inp = document.getElementById('manual_nipy_input');
                if (inp) inp.focus();
            }, 200);
        } else if (modalId === 'manualModal') {
            setTimeout(function() {
                const searchInp = document.getElementById('manualEmpSearch');
                if (searchInp) searchInp.focus();
            }, 200);
        }
    }

    function closeKegiatanModal(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        modal.classList.remove('show', 'is-open');
        modal.style.display = 'none';
        document.body.style.overflow = '';

        if (modalId === 'scannerModal') {
            stopScanner();
        }
    }

    // Tombol ESC menutup modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeKegiatanModal('scannerModal');
            closeKegiatanModal('manualModal');
        }
    });

    // Pindahkan kedua modal ke body saat DOM siap
    document.addEventListener('DOMContentLoaded', function() {
        ['scannerModal', 'manualModal'].forEach(function(id) {
            const el = document.getElementById(id);
            if (el && el.parentElement !== document.body) {
                document.body.appendChild(el);
            }
        });
    });

    // Switch Tab Filter (Semua, Hadir, Izin, Sakit, Alpa, Belum Presensi)
    function switchLogView(filterKey, btn) {
        activeLogFilter = filterKey;
        document.querySelectorAll('.status-filter-pill').forEach(p => p.classList.remove('active'));
        if (btn) btn.classList.add('active');

        const recordedContainer = document.getElementById('recordedTableContainer');
        const unrecordedContainer = document.getElementById('unrecordedTableContainer');

        if (filterKey === 'belum') {
            recordedContainer.style.display = 'none';
            unrecordedContainer.style.display = '';
        } else {
            recordedContainer.style.display = '';
            unrecordedContainer.style.display = 'none';
        }

        filterLogRows();
    }

    // Live Search + Status Filter
    function filterLogRows() {
        const query = (document.getElementById('logSearchInput')?.value || '').toLowerCase().trim();

        if (activeLogFilter === 'belum') {
            let num = 0;
            document.querySelectorAll('.unrecorded-row').forEach(row => {
                const text = row.getAttribute('data-search') || '';
                if (!query || text.indexOf(query) > -1) {
                    row.style.display = '';
                    num++;
                    const idxCell = row.querySelector('.row-index');
                    if (idxCell) idxCell.innerText = num;
                } else {
                    row.style.display = 'none';
                }
            });
        } else {
            let num = 0;
            document.querySelectorAll('.recorded-row').forEach(row => {
                const status = row.getAttribute('data-status') || '';
                const text = row.getAttribute('data-search') || '';
                const matchStatus = (activeLogFilter === 'all' || status === activeLogFilter);
                const matchQuery = (!query || text.indexOf(query) > -1);

                if (matchStatus && matchQuery) {
                    row.style.display = '';
                    num++;
                    const idxCell = row.querySelector('.row-index');
                    if (idxCell) idxCell.innerText = num;
                } else {
                    row.style.display = 'none';
                }
            });
        }
    }

    // Manual Modal Checklist Helpers
    function updateManualSelectedState(checkbox) {
        const item = checkbox.closest('.manual-emp-item');
        if (item) {
            item.classList.toggle('selected', checkbox.checked);
        }
        const totalChecked = document.querySelectorAll('.manual-emp-checkbox:checked').length;
        const badge = document.getElementById('selectedEmpCountBadge');
        if (badge) badge.innerText = totalChecked + ' dipilih';
    }

    function filterManualEmpList() {
        const q = (document.getElementById('manualEmpSearch')?.value || '').toLowerCase().trim();
        document.querySelectorAll('.manual-emp-item').forEach(item => {
            const text = item.getAttribute('data-search') || '';
            item.style.display = (!q || text.indexOf(q) > -1) ? 'flex' : 'none';
        });
    }

    function selectAllUnrecordedManual() {
        document.querySelectorAll('.manual-emp-item').forEach(item => {
            if (item.style.display !== 'none' && item.getAttribute('data-recorded') === '0') {
                const cb = item.querySelector('.manual-emp-checkbox');
                if (cb) {
                    cb.checked = true;
                    item.classList.add('selected');
                }
            }
        });
        const totalChecked = document.querySelectorAll('.manual-emp-checkbox:checked').length;
        document.getElementById('selectedEmpCountBadge').innerText = totalChecked + ' dipilih';
    }

    function clearManualSelection() {
        document.querySelectorAll('.manual-emp-checkbox').forEach(cb => {
            cb.checked = false;
            cb.closest('.manual-emp-item')?.classList.remove('selected');
        });
        document.getElementById('selectedEmpCountBadge').innerText = '0 dipilih';
    }

    function quickSelectEmployeeManual(nipy) {
        clearManualSelection();
        const cb = document.querySelector('.manual-emp-checkbox[value="' + nipy + '"]');
        if (cb) {
            cb.checked = true;
            updateManualSelectedState(cb);
            openKegiatanModal('manualModal');
            setTimeout(() => {
                cb.closest('.manual-emp-item')?.scrollIntoView({ block: 'center', behavior: 'smooth' });
            }, 250);
        }
    }

    function openManualModalForUnrecorded() {
        clearManualSelection();
        selectAllUnrecordedManual();
        openKegiatanModal('manualModal');
    }

    // Scanner Modal Lifecycle
    $('#scannerModal').on('shown.bs.modal', function () {
        startScanner();
        $('#manual_nipy_input').focus();
    });

    $('#scannerModal').on('hidden.bs.modal', function () {
        stopScanner();
    });

    function startScanner() {
        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode("reader");
        }
        const config = { fps: 10, qrbox: { width: 250, height: 250 } };
        html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess)
            .catch(err => {
                $('#scanner-status').removeClass('alert-info').addClass('alert-warning')
                    .html('Kamera tidak dapat diakses atau tidak ditemukan. Gunakan input manual di bawah.');
            });
    }

    function stopScanner() {
        if (html5QrCode && html5QrCode.isScanning) {
            html5QrCode.stop().catch(err => console.error(err));
        }
    }

    function onScanSuccess(decodedText) {
        if (html5QrCode) {
            html5QrCode.pause();
        }
        processScan(decodedText);
    }

    function submitNipyScan() {
        const val = $('#manual_nipy_input').val().trim();
        if (val) {
            processScan(val);
            $('#manual_nipy_input').val('');
        }
    }

    $('#manual_nipy_input').on('keypress', function(e) {
        if (e.which === 13) {
            submitNipyScan();
        }
    });

    function processScan(scannedNipy) {
        $('#scanner-status').removeClass('alert-danger alert-success alert-warning')
            .addClass('alert-info').html('<i class="fa fa-spinner fa-spin mr-1"></i> Memproses presensi...');

        fetch("{{ route('kegiatan.scan') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                qr_token: qrToken,
                karyawan_nipy: scannedNipy
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                $('#scanner-status').removeClass('alert-info').addClass('alert-success')
                    .html('<i class="fa fa-check-circle mr-1"></i> ' + data.message);
                
                setTimeout(() => {
                    location.reload();
                }, 1200);
            } else {
                $('#scanner-status').removeClass('alert-info').addClass('alert-danger')
                    .html('<i class="fa fa-exclamation-circle mr-1"></i> ' + data.message);
                if (html5QrCode) {
                    setTimeout(() => html5QrCode.resume(), 1500);
                }
            }
        })
        .catch(err => {
            $('#scanner-status').removeClass('alert-info').addClass('alert-danger')
                .html('Terjadi kesalahan koneksi.');
            if (html5QrCode) {
                setTimeout(() => html5QrCode.resume(), 1500);
            }
        });
    }
</script>
@endsection
