@extends('layouts.appui')

@section('title')
    <title>Data Presensi Karyawan - SIPIT</title>
@endsection

@section('content')
<style>
    /* Modern Card & Styling */
    .filter-card {
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04), 0 2px 4px -2px rgba(0, 0, 0, 0.03);
    }
    .table-card {
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04), 0 2px 4px -2px rgba(0, 0, 0, 0.03);
        overflow: hidden;
    }
    
    /* Form Filter Controls */
    .form-filter-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: #475569;
        letter-spacing: 0.04em;
        margin-bottom: 5px;
        display: block;
    }
    .form-filter-control {
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        font-size: 13px;
        height: 38px;
        transition: all 0.2s ease;
        color: #1e293b;
    }
    .form-filter-control:focus {
        border-color: #ea580c;
        box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.15);
    }

    /* Quick Shortcut Pills */
    .quick-pill {
        display: inline-flex;
        align-items: center;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        background-color: #f8fafc;
        color: #475569;
        border: 1px solid #e2e8f0;
        text-decoration: none;
        transition: all 0.2s ease;
        margin-right: 6px;
        margin-bottom: 6px;
    }
    .quick-pill:hover {
        background-color: #ea580c;
        color: #ffffff;
        border-color: #ea580c;
        text-decoration: none;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(234, 88, 12, 0.2);
    }
    .quick-pill.active {
        background-color: #ea580c;
        color: #ffffff;
        border-color: #ea580c;
        box-shadow: 0 2px 6px rgba(234, 88, 12, 0.25);
    }

    /* Avatar & Pill Badges */
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
        display: inline-block;
    }

    /* Modern Soft Badges */
    .badge-status {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11.5px;
        font-weight: 600;
        line-height: 1.2;
    }
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
    .badge-soft-dark {
        background-color: #faf5ff;
        color: #6b21a8;
        border: 1px solid #e9d5ff;
    }
    .badge-soft-danger {
        background-color: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    /* Table Styles */
    .modern-table {
        margin-bottom: 0;
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .modern-table thead th {
        background-color: #f8fafc;
        color: #334155;
        font-weight: 700;
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-top: none;
        border-bottom: 1px solid #e2e8f0;
        padding: 12px 10px;
        vertical-align: middle;
    }
    .modern-table tbody tr {
        transition: background-color 0.15s ease-in-out;
    }
    .modern-table tbody tr:hover {
        background-color: #f8fafc;
    }
    .modern-table tbody td {
        padding: 12px 10px;
        vertical-align: middle;
        font-size: 13px;
        border-bottom: 1px solid #f1f5f9;
    }

    /* Guidance Box (Empty State Sebelum Memilih Filter) */
    .guidance-box {
        border-radius: 16px;
        border: 1.5px dashed #cbd5e1;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        padding: 44px 24px;
        text-align: center;
    }
    .guidance-icon-circle {
        width: 78px;
        height: 78px;
        border-radius: 50%;
        background: #fff7ed;
        border: 2px solid #ffedd5;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 18px;
        color: #ea580c;
        font-size: 34px;
        box-shadow: 0 6px 16px rgba(234, 88, 12, 0.12);
    }
    .guidance-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px;
        text-align: left;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .guidance-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 20px -6px rgba(0, 0, 0, 0.08);
        border-color: #ea580c;
    }

    /* Active Filter Summary Bar */
    .active-filter-bar {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 16px;
    }
</style>

@php
    $bgGradients = [
        'linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%)',
        'linear-gradient(135deg, #10b981 0%, #047857 100%)',
        'linear-gradient(135deg, #f59e0b 0%, #b45309 100%)',
        'linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%)',
        'linear-gradient(135deg, #ec4899 0%, #be185d 100%)',
        'linear-gradient(135deg, #06b6d4 0%, #0e7490 100%)',
        'linear-gradient(135deg, #ea580c 0%, #c2410c 100%)',
    ];

    $todayDate = date('Y-m-d');
@endphp

<div class="app-main__inner">
    <!-- PAGE TITLE & ACTIONS -->
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon text-white shadow-sm"
                    style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 12px;">
                    <i class="pe-7s-date" style="color: white; font-size: 26px;"></i>
                </div>
                <div>
                    Presensi Karyawan
                    <div class="page-title-subheading">
                        Pencatatan riwayat presensi harian, filter dinamis per kategori, dan manajemen absensi pegawai.
                    </div>
                </div>
            </div>

            <div class="page-title-actions">
                <a href="{{ route('presensi.create') }}" class="btn text-white shadow-sm font-weight-bold"
                    style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 8px;">
                    <i class="fa fa-plus-circle mr-1"></i> Input Presensi
                </a>
            </div>
        </div>
    </div>

    <!-- FILTER CARD (Form & Quick Shortcuts) -->
    <div class="filter-card card mb-4">
        <div class="card-body p-4">
            <form action="{{ route('presensi.index') }}" method="GET" id="filterForm">
                <div class="row">
                    <!-- Periode Bulan -->
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="form-filter-label">
                            <i class="fa fa-calendar-alt mr-1 text-warning"></i> Periode Bulan
                        </label>
                        <select name="kepegawaian_master_bulan_id" class="form-control form-filter-control select2">
                            <option value="">Semua Bulan</option>
                            @foreach ($bulanList as $b)
                                <option value="{{ $b->id }}" {{ request('kepegawaian_master_bulan_id') == $b->id ? 'selected' : '' }}>
                                    {{ $b->nama_bulan }} {{ $b->tahunAjaran ? '(' . $b->tahunAjaran->nama_tahun_ajaran . ')' : '' }} {{ $b->status ? ' [Aktif]' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Kategori Karyawan -->
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="form-filter-label">
                            <i class="fa fa-layer-group mr-1 text-primary"></i> Kategori Pegawai
                        </label>
                        <select name="kepegawaian_master_kategori_id" class="form-control form-filter-control">
                            <option value="">Semua Kategori</option>
                            @foreach ($kategoriList as $k)
                                <option value="{{ $k->id }}" {{ request('kepegawaian_master_kategori_id') == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tanggal Mulai -->
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="form-filter-label">
                            <i class="fa fa-calendar-day mr-1 text-info"></i> Tanggal Mulai
                        </label>
                        <input type="date" name="tanggal_mulai" class="form-control form-filter-control"
                            value="{{ request('tanggal_mulai') }}">
                    </div>

                    <!-- Tanggal Selesai -->
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="form-filter-label">
                            <i class="fa fa-calendar-check mr-1 text-info"></i> Tanggal Selesai
                        </label>
                        <input type="date" name="tanggal_selesai" class="form-control form-filter-control"
                            value="{{ request('tanggal_selesai') }}">
                    </div>

                    <!-- Status Presensi -->
                    <div class="col-lg-3 col-md-4 mb-2">
                        <label class="form-filter-label">
                            <i class="fa fa-tag mr-1 text-success"></i> Status Kehadiran
                        </label>
                        <select name="status" class="form-control form-filter-control">
                            <option value="">Semua Status</option>
                            @foreach ([
                                'hadir'     => 'Hadir Tepat Waktu',
                                'terlambat' => 'Terlambat',
                                'tugas'     => 'Tugas (Dinas Luar)',
                                'izin'      => 'Izin',
                                'sakit'     => 'Sakit',
                                'cuti'      => 'Cuti',
                                'alpa'      => 'Alpa'
                            ] as $key => $val)
                                <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                    {{ $val }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Search Box (Nama / NIPY) -->
                    <div class="col-lg-6 col-md-5 mb-2">
                        <label class="form-filter-label">
                            <i class="fa fa-search mr-1 text-secondary"></i> Cari Nama atau NIPY
                        </label>
                        <div class="input-group">
                            <input type="text" name="search" class="form-control form-filter-control"
                                placeholder="Ketik nama lengkap atau NIPY karyawan..."
                                value="{{ request('search') }}">
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="col-lg-3 col-md-3 mb-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary font-weight-bold mr-2 shadow-sm flex-grow-1"
                            style="border-radius: 8px; height: 38px;">
                            <i class="fa fa-filter mr-1"></i> Terapkan Filter
                        </button>
                        <a href="{{ route('presensi.index') }}" class="btn btn-light shadow-sm"
                            style="border-radius: 8px; height: 38px; border: 1px solid #cbd5e1;"
                            title="Reset Semua Filter">
                            <i class="fa fa-undo"></i>
                        </a>
                    </div>
                </div>

                <!-- QUICK SHORTCUT FILTER CHIPS -->
                <div class="d-flex align-items-center flex-wrap pt-3 mt-2 border-top">
                    <span class="small font-weight-bold text-muted mr-3 mb-1">
                        <i class="fa fa-bolt text-warning mr-1"></i> Pintasan Cepat:
                    </span>

                    <a href="{{ route('presensi.index', ['tanggal_mulai' => $todayDate, 'tanggal_selesai' => $todayDate]) }}"
                        class="quick-pill {{ request('tanggal_mulai') == $todayDate && request('tanggal_selesai') == $todayDate ? 'active' : '' }}">
                        <i class="fa fa-calendar-day mr-1"></i> Presensi Hari Ini
                    </a>

                    @if ($activeBulan)
                        <a href="{{ route('presensi.index', ['kepegawaian_master_bulan_id' => $activeBulan->id]) }}"
                            class="quick-pill {{ request('kepegawaian_master_bulan_id') == $activeBulan->id ? 'active' : '' }}">
                            <i class="fa fa-calendar-alt mr-1"></i> Bulan Aktif ({{ $activeBulan->nama_bulan }})
                        </a>
                    @endif

                    <a href="{{ route('presensi.index', ['status' => 'terlambat']) }}"
                        class="quick-pill {{ request('status') == 'terlambat' ? 'active' : '' }}">
                        <i class="fa fa-clock mr-1 text-danger"></i> Khusus Terlambat
                    </a>

                    <a href="{{ route('presensi.index', ['status' => 'tugas']) }}"
                        class="quick-pill {{ request('status') == 'tugas' ? 'active' : '' }}">
                        <i class="fa fa-briefcase mr-1 text-primary"></i> Dinas / Tugas
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- MAIN CONTENT AREA -->
    @if (! $hasFilter)
        <!-- GUIDANCE STATE: Ditampilkan saat belum memilih menu/filter agar tidak query data berat -->
        <div class="guidance-box mb-4">
            <div class="guidance-icon-circle">
                <i class="pe-7s-filter"></i>
            </div>
            <h4 class="font-weight-bold text-dark mb-2">Tentukan Kriteria Presensi Terlebih Dahulu</h4>
            <p class="text-muted mx-auto mb-4" style="max-width: 650px; font-size: 13.5px; line-height: 1.6;">
                Untuk menjaga kinerja sistem tetap responsif dan cepat saat data presensi sudah sangat banyak, data riwayat absensi hanya akan dimuat setelah Anda memilih menu filter di atas atau menggunakan opsi pintasan berikut.
            </p>

            <div class="row text-left justify-content-center" style="max-width: 950px; margin: 0 auto;">
                <div class="col-md-4 mb-3">
                    <div class="guidance-card">
                        <div>
                            <div class="d-flex align-items-center mb-3">
                                <div class="badge-soft-success p-2 rounded mr-2" style="font-size: 16px;">
                                    <i class="fa fa-calendar-check"></i>
                                </div>
                                <span class="font-weight-bold text-dark">Presensi Hari Ini</span>
                            </div>
                            <p class="text-muted small mb-3">
                                Pantau kehadiran pegawai yang sudah melakukan presensi pada tanggal hari ini ({{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}).
                            </p>
                        </div>
                        <a href="{{ route('presensi.index', ['tanggal_mulai' => $todayDate, 'tanggal_selesai' => $todayDate]) }}"
                            class="btn btn-sm btn-outline-success btn-block font-weight-bold" style="border-radius: 8px;">
                            Lihat Presensi Hari Ini <i class="fa fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="guidance-card">
                        <div>
                            <div class="d-flex align-items-center mb-3">
                                <div class="badge-soft-warning p-2 rounded mr-2" style="font-size: 16px;">
                                    <i class="fa fa-calendar-alt"></i>
                                </div>
                                <span class="font-weight-bold text-dark">Bulan Berjalan</span>
                            </div>
                            <p class="text-muted small mb-3">
                                Tampilkan seluruh riwayat presensi pada periode bulan yang sedang aktif saat ini ({{ $activeBulan?->nama_bulan ?? 'Aktif' }}).
                            </p>
                        </div>
                        @if ($activeBulan)
                            <a href="{{ route('presensi.index', ['kepegawaian_master_bulan_id' => $activeBulan->id]) }}"
                                class="btn btn-sm btn-outline-warning btn-block font-weight-bold text-dark" style="border-radius: 8px;">
                                Buka Bulan Aktif <i class="fa fa-arrow-right ml-1"></i>
                            </a>
                        @else
                            <button disabled class="btn btn-sm btn-light btn-block text-muted" style="border-radius: 8px;">
                                Belum ada bulan aktif
                            </button>
                        @endif
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="guidance-card">
                        <div>
                            <div class="d-flex align-items-center mb-3">
                                <div class="badge-soft-primary p-2 rounded mr-2" style="font-size: 16px;">
                                    <i class="fa fa-user-plus"></i>
                                </div>
                                <span class="font-weight-bold text-dark">Input Presensi Baru</span>
                            </div>
                            <p class="text-muted small mb-3">
                                Ingin mencatat absensi baru? Buka form presensi massal (bulk) untuk memasukkan data per kategori kerja.
                            </p>
                        </div>
                        <a href="{{ route('presensi.create') }}"
                            class="btn btn-sm font-weight-bold text-white btn-block"
                            style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 8px;">
                            Buka Form Input <i class="fa fa-plus-circle ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- RESULT STATE: Ditampilkan saat filter aktif -->
        
        <!-- Active Filter Indicator Bar -->
        <div class="active-filter-bar mb-3 d-flex flex-wrap justify-content-between align-items-center">
            <div class="d-flex align-items-center flex-wrap">
                <span class="badge badge-light border text-dark py-1 px-2 font-weight-bold mr-2 mb-1" style="font-size: 12px;">
                    <i class="fa fa-check-circle text-success mr-1"></i> Filter Aktif:
                </span>

                @if (request('kepegawaian_master_bulan_id'))
                    @php $selBulan = $bulanList->firstWhere('id', request('kepegawaian_master_bulan_id')); @endphp
                    <span class="badge badge-soft-warning py-1 px-2 mr-2 mb-1" style="font-size: 11.5px;">
                        Bulan: {{ $selBulan?->nama_bulan ?? request('kepegawaian_master_bulan_id') }}
                    </span>
                @endif

                @if (request('kepegawaian_master_kategori_id'))
                    @php $selKat = $kategoriList->firstWhere('id', request('kepegawaian_master_kategori_id')); @endphp
                    <span class="badge badge-soft-primary py-1 px-2 mr-2 mb-1" style="font-size: 11.5px;">
                        Kategori: {{ $selKat?->nama_kategori ?? request('kepegawaian_master_kategori_id') }}
                    </span>
                @endif

                @if (request('tanggal_mulai') || request('tanggal_selesai'))
                    <span class="badge badge-soft-info py-1 px-2 mr-2 mb-1" style="font-size: 11.5px;">
                        Tgl: {{ request('tanggal_mulai') ?: '...' }} s/d {{ request('tanggal_selesai') ?: '...' }}
                    </span>
                @endif

                @if (request('status'))
                    <span class="badge badge-soft-secondary py-1 px-2 mr-2 mb-1" style="font-size: 11.5px;">
                        Status: {{ ucfirst(request('status')) }}
                    </span>
                @endif

                @if (request('search'))
                    <span class="badge badge-soft-dark py-1 px-2 mr-2 mb-1" style="font-size: 11.5px;">
                        Kata Kunci: "{{ request('search') }}"
                    </span>
                @endif
            </div>

            <div class="small text-muted font-weight-bold my-1">
                Ditemukan <span class="text-dark font-weight-bold">{{ $presensi->total() }}</span> data presensi
                <a href="{{ route('presensi.index') }}" class="ml-2 text-danger font-weight-bold" style="text-decoration: underline;">
                    <i class="fa fa-times-circle"></i> Bersihkan Filter
                </a>
            </div>
        </div>

        <!-- MAIN TABLE CARD -->
        <div class="table-card card mb-3">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="modern-table align-middle">
                        <thead>
                            <tr>
                                <th style="width: 4%; text-align: center;">No</th>
                                <th style="width: 12%;">Tanggal</th>
                                <th style="width: 23%;">Pegawai</th>
                                <th style="width: 11%;">Kategori</th>
                                <th style="width: 11%;">Periode Bulan</th>
                                <th style="width: 10%;">Jam Masuk</th>
                                <th style="width: 10%;">Jam Pulang</th>
                                <th style="width: 8%;">Durasi</th>
                                <th style="width: 11%;">Status</th>
                                <th style="width: 8%; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($presensi as $index => $item)
                                @php
                                    $namaClean = trim($item->karyawan?->nama_lengkap ?? 'Pegawai');
                                    $words = explode(' ', $namaClean);
                                    $initials = strtoupper(substr($words[0] ?? 'P', 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
                                    $bgGrad = $bgGradients[crc32($namaClean) % count($bgGradients)];
                                @endphp
                                <tr>
                                    <td class="text-center font-weight-bold text-muted" style="font-size: 12px;">
                                        {{ $presensi->firstItem() + $index }}
                                    </td>
                                    <td>
                                        <div class="font-weight-bold text-dark" style="font-size: 13px;">
                                            {{ $item->tanggal ? $item->tanggal->translatedFormat('d M Y') : '-' }}
                                        </div>
                                        <div class="small text-muted">
                                            {{ $item->tanggal ? $item->tanggal->translatedFormat('l') : '' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle mr-2" style="background: {{ $bgGrad }};">
                                                {{ $initials }}
                                            </div>
                                            <div style="min-width: 0;">
                                                <div class="font-weight-bold text-dark text-truncate" style="font-size: 13.5px;" title="{{ $item->karyawan?->nama_lengkap }}">
                                                    {{ $item->karyawan?->nama_lengkap ?? '-' }}{{ $item->karyawan?->gelar ? ', ' . $item->karyawan?->gelar : '' }}
                                                </div>
                                                <div class="mt-1">
                                                    <span class="badge-nipy">NIPY: {{ $item->karyawan_nipy }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-light border text-secondary font-weight-bold" style="font-size: 11px; padding: 4px 8px; border-radius: 6px;">
                                            {{ $item->karyawan?->kategori?->nama_kategori ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($item->bulan)
                                            <div class="font-weight-bold text-dark" style="font-size: 12px;">
                                                {{ $item->bulan->nama_bulan }}
                                            </div>
                                            <div class="small text-muted">
                                                {{ $item->bulan->tahunAjaran?->nama_tahun_ajaran ?? '' }}
                                            </div>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->jam_masuk)
                                            <span class="font-weight-bold text-dark" style="font-size: 13px;">
                                                {{ substr($item->jam_masuk, 0, 5) }}
                                            </span>
                                            @if ($item->keterlambatan_menit > 0)
                                                <div class="mt-1">
                                                    @if ($item->izin_terlambat)
                                                        <span class="badge badge-soft-info py-0 px-1" style="font-size: 10px;" title="Terlambat dengan izin/dispensasi">
                                                            +{{ $item->keterlambatan_menit }}m (Izin)
                                                        </span>
                                                    @else
                                                        <span class="badge badge-soft-danger py-0 px-1 font-weight-bold" style="font-size: 10px;">
                                                            +{{ $item->keterlambatan_menit }}m
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->jam_pulang)
                                            <span class="font-weight-bold text-dark" style="font-size: 13px;">
                                                {{ substr($item->jam_pulang, 0, 5) }}
                                            </span>
                                            @if ($item->pulang_cepat_menit > 0)
                                                <div class="mt-1">
                                                    @if ($item->izin_pulang_cepat)
                                                        <span class="badge badge-soft-info py-0 px-1" style="font-size: 10px;" title="Pulang cepat dengan izin">
                                                            -{{ $item->pulang_cepat_menit }}m (Izin)
                                                        </span>
                                                    @else
                                                        <span class="badge badge-soft-warning py-0 px-1 font-weight-bold" style="font-size: 10px;">
                                                            -{{ $item->pulang_cepat_menit }}m
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-secondary font-weight-bold" style="font-size: 12px;">
                                            {{ $item->durasi_kerja }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $badgeClass = match ($item->status) {
                                                'hadir'     => 'badge-soft-success',
                                                'terlambat' => 'badge-soft-warning',
                                                'tugas'     => 'badge-soft-primary',
                                                'izin'      => 'badge-soft-info',
                                                'sakit'     => 'badge-soft-secondary',
                                                'cuti'      => 'badge-soft-dark',
                                                'alpa'      => 'badge-soft-danger',
                                                default     => 'badge-soft-secondary',
                                            };
                                            $iconStatus = match ($item->status) {
                                                'hadir'     => 'fa fa-check-circle',
                                                'terlambat' => 'fa fa-clock',
                                                'tugas'     => 'fa fa-briefcase',
                                                'izin'      => 'fa fa-info-circle',
                                                'sakit'     => 'fa fa-medkit',
                                                'cuti'      => 'fa fa-calendar-minus',
                                                'alpa'      => 'fa fa-times-circle',
                                                default     => 'fa fa-circle',
                                            };
                                        @endphp
                                        <span class="badge-status {{ $badgeClass }}">
                                            <i class="{{ $iconStatus }} mr-1"></i>
                                            {{ ucfirst($item->status) }}
                                        </span>
                                        @if ($item->catatan)
                                            <div class="small text-muted mt-1 text-truncate" style="max-width: 140px;" title="{{ $item->catatan }}">
                                                <i class="fa fa-comment-alt mr-1"></i> {{ $item->catatan }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center align-items-center">
                                            <a href="{{ route('presensi.edit', $item->id) }}"
                                                class="btn btn-sm btn-outline-primary shadow-sm mx-1"
                                                style="border-radius: 6px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;"
                                                data-toggle="tooltip" data-placement="top" title="Edit Data Presensi">
                                                <i class="fa fa-edit"></i>
                                            </a>

                                            <form action="{{ route('presensi.destroy', $item->id) }}" method="POST"
                                                class="delete-form d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data presensi ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="btn btn-sm btn-outline-danger shadow-sm mx-1"
                                                    style="border-radius: 6px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;"
                                                    data-toggle="tooltip" data-placement="top" title="Hapus Data Presensi">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-5">
                                        <div class="text-muted mb-2">
                                            <i class="fa fa-search fa-3x text-secondary opacity-5 mb-3" style="display: block;"></i>
                                            <h6 class="font-weight-bold text-dark">Data Tidak Ditemukan</h6>
                                            <p class="small text-muted mb-3">Tidak ada riwayat presensi yang sesuai dengan kriteria filter yang Anda tentukan.</p>
                                            <a href="{{ route('presensi.index') }}" class="btn btn-sm btn-primary font-weight-bold" style="border-radius: 6px;">
                                                <i class="fa fa-undo mr-1"></i> Reset Filter
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION FOOTER -->
                @if ($presensi->total() > 0)
                    <div class="d-flex flex-wrap justify-content-between align-items-center p-3 border-top">
                        <div class="text-muted small mb-2 mb-md-0 font-weight-bold">
                            Menampilkan <span class="text-dark">{{ $presensi->firstItem() ?? 0 }}</span> - <span class="text-dark">{{ $presensi->lastItem() ?? 0 }}</span> dari total <span class="text-dark font-weight-bold">{{ $presensi->total() }}</span> data
                        </div>
                        <div>
                            {{ $presensi->appends(request()->query())->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
