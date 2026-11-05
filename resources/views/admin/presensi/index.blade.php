@extends('layouts.appui')

@section('title')
    <title>Data Presensi Karyawan - SIPIT</title>
@endsection

@section('content')
<style>
    .filter-card {
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04), 0 2px 4px -2px rgba(0, 0, 0, 0.03);
    }
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
    .quick-pill:hover,
    .quick-pill.active {
        background-color: #ea580c;
        color: #ffffff;
        border-color: #ea580c;
        text-decoration: none;
        box-shadow: 0 2px 6px rgba(234, 88, 12, 0.25);
    }
    .badge-status {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11.5px;
        font-weight: 600;
        line-height: 1.2;
    }
    .badge-soft-success { background-color: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
    .badge-soft-warning { background-color: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
    .badge-soft-primary { background-color: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe; }
    .badge-soft-info    { background-color: #f0f9ff; color: #0369a1; border: 1px solid #bae6fd; }
    .badge-soft-secondary { background-color: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
    .badge-soft-dark    { background-color: #faf5ff; color: #6b21a8; border: 1px solid #e9d5ff; }
    .badge-soft-danger  { background-color: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
    .guidance-box {
        border-radius: 16px;
        border: 1.5px dashed #cbd5e1;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        padding: 40px 24px;
        text-align: center;
    }
    .guidance-icon-circle {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: #fff7ed;
        border: 2px solid #ffedd5;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
        color: #ea580c;
        font-size: 32px;
        box-shadow: 0 6px 16px rgba(234, 88, 12, 0.12);
    }
    .guidance-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 18px;
        text-align: left;
        transition: all 0.25s ease;
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
    .active-filter-bar {
        background: #fff7ed;
        border: 1px solid #fed7aa;
        border-radius: 10px;
        padding: 10px 16px;
    }
</style>

@php
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

            <div class="page-title-actions d-flex align-items-center">
                <form action="{{ route('presensi.sync-fingerspot') }}" method="POST" class="mr-2" id="syncFingerspotForm" onsubmit="return handleSyncFingerspot(this);">
                    @csrf
                    <input type="hidden" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}">
                    <input type="hidden" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}">
                    <button type="submit" class="btn btn-outline-primary shadow-sm font-weight-bold" style="border-radius: 8px;">
                        <i class="fa fa-sync-alt mr-1"></i> Tarik Fingerspot
                    </button>
                </form>
                <a href="{{ route('presensi.create') }}" class="btn text-white shadow-sm font-weight-bold"
                    style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 8px;">
                    <i class="fa fa-plus-circle mr-1"></i> Input Presensi
                </a>
            </div>
        </div>
    </div>

    @if (session('sukses'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 10px;">
            <i class="fa fa-check-circle mr-1.5"></i> {{ session('sukses') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 10px;">
            <i class="fa fa-exclamation-triangle mr-1.5"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

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
                    <div class="col-lg-2 col-md-3 mb-2">
                        <label class="form-filter-label">
                            <i class="fa fa-tag mr-1 text-success"></i> Status Kehadiran
                        </label>
                        <select name="status" class="form-control form-filter-control">
                            <option value="">Semua Status</option>
                            @foreach ([
                                'hadir'       => 'Hadir Tepat Waktu',
                                'terlambat'   => 'Terlambat',
                                'belum_absen' => 'Belum Absen',
                                'tugas'       => 'Tugas (Dinas Luar)',
                                'izin'        => 'Izin',
                                'sakit'       => 'Sakit',
                                'cuti'        => 'Cuti',
                                'alpa'        => 'Alpa',
                                'libur'       => 'Libur'
                            ] as $key => $val)
                                <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                    {{ $val }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Shaum -->
                    <div class="col-lg-2 col-md-3 mb-2">
                        <label class="form-filter-label">
                            <i class="fa fa-moon mr-1 text-success"></i> Status Shaum
                        </label>
                        <select name="shaum" class="form-control form-filter-control">
                            <option value="">Semua</option>
                            <option value="1" {{ request('shaum') === '1' ? 'selected' : '' }}>Shaum</option>
                            <option value="0" {{ request('shaum') === '0' ? 'selected' : '' }}>Tidak Shaum</option>
                        </select>
                    </div>

                    <!-- Search Box (Nama / NIPY) -->
                    <div class="col-lg-5 col-md-4 mb-2">
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
                    <div class="col-lg-3 col-md-2 mb-2 d-flex align-items-end">
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
                        class="quick-pill {{ request('tanggal_mulai') == $todayDate && request('tanggal_selesai') == $todayDate && !request('shaum') && !request('status') ? 'active' : '' }}">
                        <i class="fa fa-calendar-day mr-1"></i> Presensi Hari Ini
                    </a>

                    <a href="{{ route('presensi.index', ['status' => 'belum_absen', 'tanggal_mulai' => $todayDate, 'tanggal_selesai' => $todayDate]) }}"
                        class="quick-pill {{ request('status') == 'belum_absen' ? 'active' : '' }}">
                        <i class="fa fa-user-clock mr-1 text-danger"></i> Belum Absen Hari Ini
                    </a>

                    <a href="{{ route('presensi.index', ['shaum' => '1', 'tanggal_mulai' => $todayDate, 'tanggal_selesai' => $todayDate]) }}"
                        class="quick-pill {{ request('shaum') === '1' ? 'active' : '' }}">
                        <i class="fa fa-moon mr-1 text-success"></i> Shaum Hari Ini
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
                        Status: {{ request('status') === 'belum_absen' ? 'Belum Absen' : ucfirst(request('status')) }}
                    </span>
                @endif

                @if (request()->filled('shaum'))
                    <span class="badge badge-soft-success py-1 px-2 mr-2 mb-1" style="font-size: 11.5px;">
                        <i class="fa fa-moon mr-1"></i> Shaum: {{ request('shaum') == '1' ? 'Ya' : 'Tidak' }}
                    </span>
                @endif

                @if (request('search'))
                    <span class="badge badge-soft-dark py-1 px-2 mr-2 mb-1" style="font-size: 11.5px;">
                        Kata Kunci: "{{ request('search') }}"
                    </span>
                @endif
            </div>

            <div class="small text-muted font-weight-bold my-1">
                Ditemukan <span class="text-dark font-weight-bold">{{ $presensi->count() }}</span> data presensi
                <a href="{{ route('presensi.index') }}" class="ml-2 text-danger font-weight-bold" style="text-decoration: underline;">
                    <i class="fa fa-times-circle"></i> Bersihkan Filter
                </a>
            </div>
        </div>

        <!-- MAIN TABLE CARD -->
        <div class="main-card card mb-4 shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="mb-0 table table-hover table-striped align-middle" id="myTable6" style="width: 100%;">
                        <thead>
                            <tr style="background: #f8fafc;">
                                <th style="width: 5%; text-align: center;">No</th>
                                <th>Nama Lengkap</th>
                                <th>Tanggal</th>
                                <th>Jam Kerja</th>
                                <th>Status</th>
                                <th style="width: 60px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($presensi as $index => $item)
                                <tr>
                                    <td class="text-center font-weight-bold text-muted row-number">
                                        {{ $index + 1 }}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center" style="gap: 10px;">
                                            @if ($item->karyawan?->foto_url)
                                                <img src="{{ $item->karyawan->foto_url }}" alt="Foto"
                                                    style="width: 38px; height: 38px; object-fit: cover; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 1px 4px rgba(0,0,0,.2); flex-shrink: 0;">
                                            @else
                                                <span class="d-flex align-items-center justify-content-center text-white font-weight-bold"
                                                    style="width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg, #ea580c, #d97706); font-size: 0.82rem; flex-shrink: 0;">
                                                    {{ strtoupper(mb_substr($item->karyawan?->nama_lengkap ?? 'P', 0, 1)) }}
                                                </span>
                                            @endif
                                            <div style="min-width: 0;">
                                                <div class="font-weight-bold text-dark text-truncate" style="font-size: 0.88rem; max-width: 240px;"
                                                    title="{{ $item->karyawan?->nama_lengkap }}{{ $item->karyawan?->gelar ? ', ' . $item->karyawan->gelar : '' }}">
                                                    {{ $item->karyawan?->nama_lengkap ?? '-' }}{{ $item->karyawan?->gelar ? ', ' . $item->karyawan->gelar : '' }}
                                                </div>
                                                <small class="text-muted d-block text-truncate" style="max-width: 240px;">
                                                    {{ $item->karyawan_nipy }}
                                                    @if ($item->karyawan?->unit)
                                                        &nbsp;|&nbsp; <i class="pe-7s-map-marker text-danger"></i> {{ $item->karyawan->unit }}
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="white-space: nowrap;">
                                        <div class="font-weight-bold text-dark" style="font-size: 0.84rem;">
                                            {{ $item->tanggal ? $item->tanggal->translatedFormat('d M Y') : '-' }}
                                        </div>
                                        <small class="text-muted">
                                            {{ $item->tanggal ? $item->tanggal->translatedFormat('l') : '' }}
                                        </small>
                                    </td>
                                    <td style="white-space: nowrap;">
                                        <div class="d-flex align-items-center" style="gap: 6px;">
                                            <span class="font-weight-bold text-dark" style="font-size: 0.84rem;">
                                                {{ $item->jam_masuk ? substr($item->jam_masuk, 0, 5) : '--:--' }} &ndash; {{ $item->jam_pulang ? substr($item->jam_pulang, 0, 5) : '--:--' }}
                                            </span>
                                            @if ($item->durasi_kerja && $item->durasi_kerja !== '-')
                                                <span class="badge badge-light text-muted font-weight-bold px-1.5 py-0"
                                                    style="border-radius: 4px; border: 1px solid #e2e8f0; font-size: 0.7rem;">
                                                    {{ $item->durasi_kerja }}
                                                </span>
                                            @endif
                                        </div>
                                        @if ($item->keterlambatan_menit > 0 || $item->pulang_cepat_menit > 0)
                                            <div class="mt-0.5 d-flex flex-wrap" style="gap: 3px;">
                                                @if ($item->keterlambatan_menit > 0)
                                                    <span class="badge {{ $item->izin_terlambat ? 'badge-soft-info' : 'badge-soft-danger font-weight-bold' }} py-0 px-1" style="font-size: 9.5px;">
                                                        +{{ $item->keterlambatan_menit }}m{{ $item->izin_terlambat ? ' (Izin)' : '' }}
                                                    </span>
                                                @endif
                                                @if ($item->pulang_cepat_menit > 0)
                                                    <span class="badge {{ $item->izin_pulang_cepat ? 'badge-soft-info' : 'badge-soft-warning font-weight-bold' }} py-0 px-1" style="font-size: 9.5px;">
                                                        -{{ $item->pulang_cepat_menit }}m{{ $item->izin_pulang_cepat ? ' (Izin)' : '' }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td style="white-space: nowrap;">
                                        @php
                                            $badgeClass = match ($item->status) {
                                                'hadir'       => 'badge-soft-success',
                                                'terlambat'   => 'badge-soft-warning',
                                                'belum_absen' => 'badge-soft-danger',
                                                'tugas'       => 'badge-soft-primary',
                                                'izin'        => 'badge-soft-info',
                                                'sakit'       => 'badge-soft-secondary',
                                                'cuti'        => 'badge-soft-dark',
                                                'alpa'        => 'badge-soft-danger',
                                                'libur'       => 'badge-soft-secondary',
                                                default       => 'badge-soft-secondary',
                                            };
                                            $iconStatus = match ($item->status) {
                                                'hadir'       => 'fa fa-check-circle',
                                                'terlambat'   => 'fa fa-clock',
                                                'belum_absen' => 'fa fa-user-clock',
                                                'tugas'       => 'fa fa-briefcase',
                                                'izin'        => 'fa fa-info-circle',
                                                'sakit'       => 'fa fa-medkit',
                                                'cuti'        => 'fa fa-calendar-minus',
                                                'alpa'        => 'fa fa-times-circle',
                                                'libur'       => 'fa fa-calendar-times',
                                                default       => 'fa fa-circle',
                                            };
                                            $labelStatus = $item->status === 'belum_absen' ? 'Belum Absen' : ucfirst($item->status);
                                        @endphp
                                        <div class="d-flex align-items-center flex-wrap" style="gap: 4px;">
                                            <span class="badge-status {{ $badgeClass }}">
                                                <i class="{{ $iconStatus }} mr-1"></i>
                                                {{ $labelStatus }}
                                            </span>
                                            @if ($item->shaum && $item->id)
                                                <form action="{{ route('presensi.toggle-shaum', $item->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm p-0 border-0 bg-transparent" title="Klik untuk batalkan shaum">
                                                        <span class="badge-status badge-soft-success py-1 px-2 font-weight-bold" style="cursor: pointer; font-size: 10.5px;">
                                                            <i class="fa fa-moon mr-1"></i> Shaum
                                                        </span>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="action-dropdown">
                                            <button type="button" class="btn-action-dots" title="Menu Aksi">
                                                <i class="fa fa-ellipsis-v"></i>
                                            </button>
                                            <div class="action-dropdown-menu">
                                                @if ($item->id)
                                                    <form action="{{ route('presensi.toggle-shaum', $item->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="action-dropdown-item {{ $item->shaum ? 'item-warning' : 'item-success' }}">
                                                            <span class="action-item-icon">
                                                                <i class="fa {{ $item->shaum ? 'fa-utensils' : 'fa-moon' }}"></i>
                                                            </span>
                                                            <span>{{ $item->shaum ? 'Batalkan Shaum' : 'Tandai Shaum' }}</span>
                                                        </button>
                                                    </form>

                                                    <a href="{{ route('presensi.edit', $item->id) }}" class="action-dropdown-item item-edit">
                                                        <span class="action-item-icon"><i class="fa fa-edit"></i></span>
                                                        <span>Edit Presensi</span>
                                                    </a>

                                                    @if ($item->karyawan_nipy)
                                                        <a href="{{ route('karyawan.show', $item->karyawan_nipy) }}" class="action-dropdown-item item-view">
                                                            <span class="action-item-icon"><i class="fa fa-user"></i></span>
                                                            <span>Profil Pegawai</span>
                                                        </a>
                                                    @endif

                                                    <div class="action-dropdown-divider"></div>

                                                    <form action="{{ route('presensi.destroy', $item->id) }}" method="POST" class="delete-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="action-dropdown-item item-delete delete-button">
                                                            <span class="action-item-icon"><i class="fa fa-trash"></i></span>
                                                            <span>Hapus Presensi</span>
                                                        </button>
                                                    </form>
                                                @else
                                                    <a href="{{ route('presensi.create', ['kepegawaian_master_kategori_id' => $item->karyawan?->kepegawaian_master_kategori_id, 'tanggal' => $item->tanggal ? $item->tanggal->format('Y-m-d') : $todayDate]) }}"
                                                        class="action-dropdown-item item-edit">
                                                        <span class="action-item-icon"><i class="fa fa-plus-circle"></i></span>
                                                        <span>Input Presensi</span>
                                                    </a>

                                                    @if ($item->karyawan_nipy)
                                                        <a href="{{ route('karyawan.show', $item->karyawan_nipy) }}" class="action-dropdown-item item-view">
                                                            <span class="action-item-icon"><i class="fa fa-user"></i></span>
                                                            <span>Profil Pegawai</span>
                                                        </a>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@section('js')
<script>
    function handleSyncFingerspot(form) {
        const startInput = document.querySelector('#filterForm input[name="tanggal_mulai"]');
        const endInput = document.querySelector('#filterForm input[name="tanggal_selesai"]');

        const startVal = startInput ? startInput.value : '';
        const endVal = endInput ? endInput.value : '';

        form.querySelector('input[name="tanggal_mulai"]').value = startVal;
        form.querySelector('input[name="tanggal_selesai"]').value = endVal;

        let periodeText = 'hari ini';
        if (startVal && endVal) {
            periodeText = startVal === endVal ? `tanggal ${startVal}` : `periode ${startVal} s/d ${endVal}`;
        } else if (startVal) {
            periodeText = `tanggal ${startVal}`;
        } else if (endVal) {
            periodeText = `tanggal ${endVal}`;
        }

        return confirm(`Tarik dan sinkronkan data presensi dari mesin Fingerspot untuk ${periodeText}?`);
    }

    $(document).ready(function() {
        if ($('#myTable6').length && $.fn.DataTable.isDataTable('#myTable6')) {
            var table = $('#myTable6').DataTable();
            table.settings()[0].oLanguage.sEmptyTable = "Tidak ada data presensi yang sesuai dengan filter.";
            table.settings()[0].oLanguage.sZeroRecords = "Tidak ditemukan data presensi yang cocok dengan kata kunci pencarian.";

            table.on('order.dt search.dt draw.dt', function() {
                var pageInfo = table.page.info();
                table.column(0, { search: 'applied', order: 'applied', page: 'current' }).nodes().each(function(cell, i) {
                    cell.innerHTML = pageInfo.start + i + 1;
                });
            }).draw();
        }
    });
</script>
@endsection
