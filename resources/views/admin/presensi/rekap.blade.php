@extends('layouts.appui')

@section('title')
    <title>Rekapitulasi Presensi Bulanan - SIPIT</title>
@endsection

@section('content')
<style>
    /* Modern Dashboard Styling */
    .stat-card {
        border-radius: 14px;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(226, 232, 240, 0.8);
        background: #ffffff;
        position: relative;
        overflow: hidden;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 24px -6px rgba(0, 0, 0, 0.08), 0 4px 8px -4px rgba(0, 0, 0, 0.04);
    }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    
    /* Modern Badges & Pills */
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
    .badge-soft-primary {
        background-color: #eef2ff;
        color: #4338ca;
        border: 1px solid #c7d2fe;
    }
    .badge-soft-danger {
        background-color: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    /* Custom Responsive Table */
    .rekap-table-wrapper {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04), 0 2px 4px -2px rgba(0, 0, 0, 0.03);
    }
    .rekap-table {
        margin-bottom: 0;
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .rekap-table thead th {
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
    .rekap-table tbody tr {
        transition: background-color 0.15s ease-in-out;
    }
    .rekap-table tbody tr:hover {
        background-color: #f1f5f9;
    }
    .rekap-table tbody td {
        padding: 12px 10px;
        vertical-align: middle;
        font-size: 13px;
        border-bottom: 1px solid #f1f5f9;
    }
    .rekap-table tfoot td {
        background-color: #f8fafc;
        font-weight: 700;
        border-top: 2px solid #cbd5e1;
        padding: 14px 10px;
    }

    /* Avatar Initial */
    .avatar-circle {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 13px;
        color: #ffffff;
        flex-shrink: 0;
    }

    /* Mini Progress Bar */
    .rate-bar-container {
        width: 100%;
        max-width: 90px;
        background-color: #e2e8f0;
        height: 6px;
        border-radius: 4px;
        overflow: hidden;
        margin: 4px auto 0;
    }
    .rate-bar-fill {
        height: 100%;
        border-radius: 4px;
        transition: width 0.4s ease;
    }

    /* Scrollbar Styling */
    .custom-scrollbar::-webkit-scrollbar {
        height: 8px;
        width: 8px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Print Styles */
    @media print {
        .app-sidebar, .app-header, .app-page-title .page-title-actions, .filter-section, .no-print {
            display: none !important;
        }
        .app-main__inner {
            padding: 0 !important;
            margin: 0 !important;
        }
        .rekap-table-wrapper {
            border: none !important;
            box-shadow: none !important;
        }
        .rekap-table {
            font-size: 10px !important;
        }
        .print-header {
            display: block !important;
        }
    }
    .print-header {
        display: none;
    }
</style>

@php
    $sumHadir = $rekapData->sum('total_hadir');
    $sumTerlambat = $rekapData->sum('total_terlambat');
    $sumTerlambatIzin = $rekapData->sum('total_terlambat_izin');
    $sumMenitTerlambat = $rekapData->sum('akumulasi_keterlambatan_menit');
    $sumTugas = $rekapData->sum('total_tugas');
    $sumIzin = $rekapData->sum('total_izin');
    $sumSakit = $rekapData->sum('total_sakit');
    $sumCuti = $rekapData->sum('total_cuti');
    $sumAlpa = $rekapData->sum('total_alpa');
    $sumPulangCepat = $rekapData->sum('total_pulang_cepat');
    $totalPegawai = count($rekapData);
    $totalHariCatatan = $sumHadir + $sumTerlambat + $sumTugas + $sumIzin + $sumSakit + $sumCuti + $sumAlpa;
    $persenKehadiran = $totalHariCatatan > 0 ? round((($sumHadir + $sumTerlambat) / $totalHariCatatan) * 100, 1) : 0;
@endphp

<div class="app-main__inner">
    <!-- PRINT HEADER (Hanya Tampil saat Cetak) -->
    <div class="print-header text-center mb-4">
        <h4 class="font-weight-bold mb-1">REKAPITULASI PRESENSI PEGAWAI</h4>
        <h6 class="text-muted mb-0">
            Periode: {{ $selectedBulan?->nama_bulan ?? 'Semua Bulan' }} 
            {{ $selectedBulan?->tahunAjaran ? '(' . $selectedBulan->tahunAjaran->nama_tahun_ajaran . ')' : '' }} &bull; Tutup Buku Tanggal 28
        </h6>
        @if ($selectedKategoriId && $kategoriList->firstWhere('id', $selectedKategoriId))
            <div class="badge badge-primary px-3 py-1 mt-1">Kategori: {{ $kategoriList->firstWhere('id', $selectedKategoriId)->nama_kategori }}</div>
        @endif
        <hr>
    </div>

    <!-- PAGE TITLE & BREADCRUMB -->
    <div class="app-page-title mb-3">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon text-white shadow-sm"
                    style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 14px;">
                    <i class="pe-7s-graph2" style="color: white;"></i>
                </div>
                <div>
                    Rekapitulasi Presensi Bulanan
                    <div class="page-title-subheading">
                        Ringkasan kehadiran seluruh pegawai dengan perhitungan otomatis <strong>Tutup Buku Tanggal 28</strong>.
                    </div>
                </div>
            </div>

            <div class="page-title-actions d-flex align-items-center">
                <a href="{{ route('presensi.create') }}" class="btn text-white shadow-sm mr-2"
                    style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 8px;">
                    <i class="fa fa-plus-circle mr-1"></i> Input Presensi
                </a>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs: Rekap Akumulasi vs Matriks Harian -->
    <div class="mb-3 d-flex align-items-center flex-wrap" style="gap: 8px;">
        <a href="{{ route('presensi.rekap', request()->query()) }}"
            class="btn btn-sm font-weight-bold shadow-sm"
            style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%); color: white; border-radius: 20px; padding: 6px 16px;">
            <i class="fa fa-table mr-1"></i> Rekap Akumulasi Bulanan
        </a>
        <a href="{{ route('presensi.matriks', request()->query()) }}"
            class="btn btn-sm font-weight-bold btn-light border text-muted shadow-sm hover:text-dark"
            style="border-radius: 20px; padding: 6px 16px;">
            <i class="fa fa-calendar-check-o mr-1"></i> Matriks Presensi Harian
        </a>
    </div>

    <!-- EXECUTIVE KPI SUMMARY CARDS -->
    <div class="row mb-3 no-print">
        <!-- Card 1: Total Pegawai & Tingkat Hadir -->
        <div class="col-sm-6 col-lg-3 mb-2">
            <div class="stat-card p-3 shadow-sm">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small font-weight-bold text-uppercase">Tingkat Hadir</div>
                        <div class="h3 font-weight-bold text-success mb-0 mt-1">{{ $persenKehadiran }}%</div>
                        <div class="small text-muted mt-1">
                            <strong>{{ $sumHadir + $sumTerlambat }}</strong> hadir & terlambat &bull; <strong>{{ $sumTugas }}</strong> tugas
                        </div>
                    </div>
                    <div class="stat-icon" style="background: #ecfdf5; color: #10b981;">
                        <i class="fa fa-user-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Keterlambatan -->
        <div class="col-sm-6 col-lg-3 mb-2">
            <div class="stat-card p-3 shadow-sm">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small font-weight-bold text-uppercase">Terlambat</div>
                        <div class="h3 font-weight-bold text-warning mb-0 mt-1">{{ $sumTerlambat }} <span style="font-size: 14px;">hari</span></div>
                        <div class="small text-muted mt-1">
                            Akumulasi: <strong class="text-danger">{{ $sumMenitTerlambat }} menit</strong>
                            @if ($sumTerlambatIzin > 0)
                                <span class="badge badge-light border">({{ $sumTerlambatIzin }} izin)</span>
                            @endif
                        </div>
                    </div>
                    <div class="stat-icon" style="background: #fffbeb; color: #f59e0b;">
                        <i class="fa fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Izin / Sakit / Cuti -->
        <div class="col-sm-6 col-lg-3 mb-2">
            <div class="stat-card p-3 shadow-sm">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small font-weight-bold text-uppercase">Izin, Sakit & Cuti</div>
                        <div class="h3 font-weight-bold text-primary mb-0 mt-1">{{ $sumIzin + $sumSakit + $sumCuti }} <span style="font-size: 14px;">hari</span></div>
                        <div class="small text-muted mt-1">
                            I: <strong>{{ $sumIzin }}</strong> &bull; S: <strong>{{ $sumSakit }}</strong> &bull; C: <strong>{{ $sumCuti }}</strong>
                        </div>
                    </div>
                    <div class="stat-icon" style="background: #eef2ff; color: #6366f1;">
                        <i class="fa fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4: Alpa & Pulang Cepat -->
        <div class="col-sm-6 col-lg-3 mb-2">
            <div class="stat-card p-3 shadow-sm">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small font-weight-bold text-uppercase">Alpa & Pulang Cepat</div>
                        <div class="h3 font-weight-bold {{ $sumAlpa > 0 ? 'text-danger' : 'text-secondary' }} mb-0 mt-1">
                            {{ $sumAlpa }} <span style="font-size: 14px;">Alpa</span>
                        </div>
                        <div class="small text-muted mt-1">
                            Pulang Cepat: <strong>{{ $sumPulangCepat }}x</strong>
                        </div>
                    </div>
                    <div class="stat-icon" style="background: #fef2f2; color: #ef4444;">
                        <i class="fa fa-user-times"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FILTER & SEARCH BAR -->
    <div class="main-card card mb-3 shadow-sm border-0 filter-section no-print">
        <div class="card-body p-3">
            <form action="{{ route('presensi.rekap') }}" method="GET" id="rekapFilterForm">
                <div class="row align-items-center">
                    <!-- Bulan Filter -->
                    <div class="col-md-4 col-sm-6 mb-2 mb-md-0">
                        <label class="small font-weight-bold text-muted mb-1 d-flex justify-content-between">
                            <span><i class="fa fa-calendar mr-1 text-primary"></i> Periode Master Bulan</span>
                            <span class="text-warning small"><i class="fa fa-info-circle"></i> Cut-off tgl 28</span>
                        </label>
                        <select name="bulan_id" class="form-control form-control-sm select-modern" required onchange="document.getElementById('rekapFilterForm').submit();">
                            @foreach ($bulanList as $b)
                                <option value="{{ $b->id }}" {{ $selectedBulanId == $b->id ? 'selected' : '' }}>
                                    {{ $b->nama_bulan }} {{ $b->tahunAjaran ? '(' . $b->tahunAjaran->nama_tahun_ajaran . ')' : '' }} {{ $b->status ? ' [Bulan Aktif]' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Kategori Filter -->
                    <div class="col-md-4 col-sm-6 mb-2 mb-md-0">
                        <label class="small font-weight-bold text-muted mb-1">
                            <i class="fa fa-layer-group mr-1 text-primary"></i> Kategori Karyawan
                        </label>
                        <select name="kepegawaian_master_kategori_id" class="form-control form-control-sm select-modern" onchange="document.getElementById('rekapFilterForm').submit();">
                            <option value="">-- Semua Kategori --</option>
                            @foreach ($kategoriList as $k)
                                <option value="{{ $k->id }}" {{ $selectedKategoriId == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Instant Live Search Client-side -->
                    <div class="col-md-3 col-sm-8 mb-2 mb-md-0">
                        <label class="small font-weight-bold text-muted mb-1">
                            <i class="fa fa-search mr-1 text-primary"></i> Cari Nama / NIPY
                        </label>
                        <input type="text" id="liveSearchInput" class="form-control form-control-sm"
                            placeholder="Ketik untuk memfilter..." onkeyup="filterRekapTable()">
                    </div>

                    <!-- Reset Button -->
                    <div class="col-md-1 col-sm-4 text-right pt-md-3">
                        <a href="{{ route('presensi.rekap') }}" class="btn btn-sm btn-light border btn-block shadow-sm" title="Reset Filter">
                            <i class="fa fa-undo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- MAIN REKAP DATA TABLE -->
    <div class="rekap-table-wrapper mb-4">
        <!-- Card Header Info -->
        <div class="p-3 bg-white d-flex flex-wrap justify-content-between align-items-center border-bottom">
            <div class="d-flex align-items-center mb-1 mb-md-0">
                <div class="mr-3">
                    <span class="text-muted small">Periode Terpilih:</span>
                    <span class="h6 font-weight-bold text-dark ml-1 mb-0">
                        {{ $selectedBulan?->nama_bulan ?? 'Pilih Bulan' }}
                    </span>
                    @if ($selectedBulan?->tahunAjaran)
                        <span class="badge badge-light border ml-1">{{ $selectedBulan->tahunAjaran->nama_tahun_ajaran }}</span>
                    @endif
                </div>
                @if ($selectedKategoriId && $kategoriList->firstWhere('id', $selectedKategoriId))
                    <span class="badge badge-soft-primary px-3 py-1 font-weight-bold">
                        Kategori: {{ $kategoriList->firstWhere('id', $selectedKategoriId)->nama_kategori }}
                    </span>
                @else
                    <span class="badge badge-soft-secondary px-3 py-1 font-weight-bold">
                        Semua Kategori
                    </span>
                @endif
            </div>

            <div class="small text-muted">
                Menampilkan <strong id="visibleRowCount" class="text-dark">{{ count($rekapData) }}</strong> pegawai
            </div>
        </div>

        <!-- Table Container with Horizontal Scroll -->
        <div class="table-responsive custom-scrollbar" style="max-height: 650px;">
            <table class="rekap-table text-center" id="rekapTable">
                <thead>
                    <tr>
                        <th style="width: 45px;">No</th>
                        <th style="text-align: left; min-width: 220px;">Pegawai</th>
                        <th style="min-width: 130px;">Kategori</th>
                        <th style="min-width: 70px;">Hadir</th>
                        <th style="min-width: 95px;">Terlambat</th>
                        <th style="min-width: 65px;">Tugas</th>
                        <th style="min-width: 65px;">Izin</th>
                        <th style="min-width: 65px;">Sakit</th>
                        <th style="min-width: 65px;">Cuti</th>
                        <th style="min-width: 65px;">Alpa</th>
                        <th style="min-width: 105px;">Menit Telat</th>
                        <th style="min-width: 95px;">Pulang Cepat</th>
                        <th style="min-width: 110px;">Performa</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $avatarGradients = [
                            'linear-gradient(135deg, #0284c7 0%, #0369a1 100%)',
                            'linear-gradient(135deg, #ea580c 0%, #c2410c 100%)',
                            'linear-gradient(135deg, #059669 0%, #047857 100%)',
                            'linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%)',
                            'linear-gradient(135deg, #db2777 0%, #be185d 100%)',
                            'linear-gradient(135deg, #475569 0%, #334155 100%)',
                        ];
                    @endphp

                    @forelse ($rekapData as $index => $karyawan)
                        @php
                            $initial = strtoupper(substr($karyawan->nama_lengkap ?? 'P', 0, 1));
                            $gradientBg = $avatarGradients[$index % count($avatarGradients)];
                            
                            $totalPegawaiHadir = $karyawan->total_hadir + $karyawan->total_terlambat;
                            $totalCatatan = $totalPegawaiHadir + $karyawan->total_izin + $karyawan->total_sakit + $karyawan->total_cuti + $karyawan->total_alpa;
                            $rate = $totalCatatan > 0 ? round(($totalPegawaiHadir / $totalCatatan) * 100) : 0;
                            $rateColor = $rate >= 90 ? '#10b981' : ($rate >= 75 ? '#f59e0b' : '#ef4444');
                        @endphp
                        <tr class="rekap-row" data-name="{{ strtolower($karyawan->nama_lengkap) }}" data-nipy="{{ strtolower($karyawan->nipy) }}">
                            <td class="text-muted font-weight-bold">{{ $index + 1 }}</td>
                            
                            <!-- Employee Profile -->
                            <td class="text-left">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle mr-2 shadow-sm" style="background: {{ $gradientBg }};">
                                        {{ $initial }}
                                    </div>
                                    <div style="line-height: 1.25;">
                                        <div class="font-weight-bold text-dark text-truncate" style="max-width: 220px;" title="{{ $karyawan->nama_lengkap }}">
                                            {{ $karyawan->nama_lengkap }}{{ $karyawan->gelar ? ', ' . $karyawan->gelar : '' }}
                                        </div>
                                        <span class="badge badge-light border text-muted px-2 py-0 mt-1 font-mono" style="font-size: 10.5px;">
                                            {{ $karyawan->nipy }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Category -->
                            <td>
                                <span class="badge badge-soft-secondary font-weight-normal px-2 py-1" style="border-radius: 6px;">
                                    {{ $karyawan->kategori?->nama_kategori ?: '-' }}
                                </span>
                            </td>

                            <!-- Hadir -->
                            <td>
                                <span class="badge badge-soft-success font-weight-bold px-2 py-1" style="min-width: 32px; border-radius: 6px;">
                                    {{ $karyawan->total_hadir }}
                                </span>
                            </td>

                            <!-- Terlambat -->
                            <td>
                                <span class="badge badge-soft-warning font-weight-bold px-2 py-1" style="min-width: 32px; border-radius: 6px;">
                                    {{ $karyawan->total_terlambat }}
                                </span>
                                @if ($karyawan->total_terlambat_izin > 0)
                                    <div class="text-muted mt-1" style="font-size: 10px;">
                                        <i class="fa fa-check text-info"></i> {{ $karyawan->total_terlambat_izin }} izin
                                    </div>
                                @endif
                            </td>

                            <!-- Tugas -->
                            <td>
                                @if (($karyawan->total_tugas ?? 0) > 0)
                                    <span class="badge badge-soft-primary font-weight-bold px-2 py-1" style="min-width: 28px; border-radius: 6px;">
                                        {{ $karyawan->total_tugas }}
                                    </span>
                                @else
                                    <span class="text-muted" style="font-size: 11px;">0</span>
                                @endif
                            </td>

                            <!-- Izin -->
                            <td>
                                <span class="badge badge-soft-info font-weight-bold px-2 py-1" style="min-width: 28px; border-radius: 6px;">
                                    {{ $karyawan->total_izin }}
                                </span>
                            </td>

                            <!-- Sakit -->
                            <td>
                                <span class="badge badge-soft-secondary font-weight-bold px-2 py-1" style="min-width: 28px; border-radius: 6px;">
                                    {{ $karyawan->total_sakit }}
                                </span>
                            </td>

                            <!-- Cuti -->
                            <td>
                                <span class="badge badge-soft-primary font-weight-bold px-2 py-1" style="min-width: 28px; border-radius: 6px;">
                                    {{ $karyawan->total_cuti }}
                                </span>
                            </td>

                            <!-- Alpa -->
                            <td>
                                @if ($karyawan->total_alpa > 0)
                                    <span class="badge badge-soft-danger font-weight-bold px-2 py-1" style="min-width: 28px; border-radius: 6px;">
                                        {{ $karyawan->total_alpa }}
                                    </span>
                                @else
                                    <span class="text-muted" style="font-size: 11px;">0</span>
                                @endif
                            </td>

                            <!-- Akumulasi Menit Terlambat Murni -->
                            <td>
                                @if (($karyawan->akumulasi_keterlambatan_menit ?? 0) > 0)
                                    <span class="font-weight-bold text-danger">
                                        {{ $karyawan->akumulasi_keterlambatan_menit }} <small>mnt</small>
                                    </span>
                                @else
                                    <span class="text-muted" style="font-size: 11px;">-</span>
                                @endif
                            </td>

                            <!-- Pulang Cepat -->
                            <td>
                                @if ($karyawan->total_pulang_cepat > 0)
                                    <span class="badge badge-soft-warning font-weight-bold px-2 py-1" style="border-radius: 6px;">
                                        {{ $karyawan->total_pulang_cepat }}x
                                    </span>
                                    @if ($karyawan->akumulasi_pulang_cepat_menit > 0)
                                        <div class="text-muted mt-1" style="font-size: 10px;">
                                            {{ $karyawan->akumulasi_pulang_cepat_menit }} mnt
                                        </div>
                                    @endif
                                @else
                                    <span class="text-muted" style="font-size: 11px;">-</span>
                                @endif
                            </td>

                            <!-- Kehadiran Rate / Mini Progress -->
                            <td>
                                <div class="font-weight-bold text-dark" style="font-size: 11px;">{{ $rate }}%</div>
                                <div class="rate-bar-container">
                                    <div class="rate-bar-fill" style="width: {{ $rate }}%; background-color: {{ $rateColor }};"></div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="text-center text-muted py-5">
                                <div class="py-4">
                                    <i class="fa fa-folder-open fa-3x text-light mb-3"></i>
                                    <div class="h6 font-weight-bold text-secondary">Data Tidak Ditemukan</div>
                                    <div class="small text-muted">Belum ada catatan presensi untuk periode dan kategori yang dipilih.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Real-time Filter Script -->
<script>
    function filterRekapTable() {
        var input = document.getElementById('liveSearchInput');
        var filter = input.value.toLowerCase().trim();
        var rows = document.querySelectorAll('.rekap-row');
        var visibleCount = 0;

        rows.forEach(function(row) {
            var name = row.getAttribute('data-name') || '';
            var nipy = row.getAttribute('data-nipy') || '';

            if (name.indexOf(filter) > -1 || nipy.indexOf(filter) > -1) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        var countElem = document.getElementById('visibleRowCount');
        if (countElem) {
            countElem.innerText = visibleCount;
        }
    }
</script>
@endsection
