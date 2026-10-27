@extends('layouts.appui')

@section('title')
    <title>Matriks Presensi Harian Pegawai - SIPIT</title>
@endsection

@section('content')
<style>
    /* Styling Matriks Modern */
    .stat-card {
        border-radius: 14px;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(226, 232, 240, 0.8);
        background: #ffffff;
        position: relative;
        overflow: hidden;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.06);
    }
    .stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    /* Container Matriks dengan Scrollbar Halus */
    .matrix-wrapper {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04);
        position: relative;
        overflow-x: auto;
        overflow-y: visible;
        max-width: 100%;
    }
    .matrix-table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    /* Header Tabel */
    .matrix-table thead th {
        background-color: #f8fafc;
        color: #334155;
        font-weight: 700;
        font-size: 11px;
        border-top: none;
        border-bottom: 2px solid #cbd5e1;
        border-right: 1px solid #f1f5f9;
        padding: 8px 4px;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
    }
    .matrix-table thead th.col-today {
        background-color: #eff6ff !important;
        border-bottom: 2px solid #3b82f6 !important;
        color: #1d4ed8 !important;
    }
    .matrix-table thead th.col-weekend {
        background-color: #f1f5f9 !important;
        color: #94a3b8 !important;
    }

    /* Sticky Columns (Kolom No & Nama Pegawai Freeze di Kiri) */
    .sticky-col-no {
        position: sticky;
        left: 0;
        background-color: #ffffff;
        z-index: 10;
        width: 42px;
        min-width: 42px;
        max-width: 42px;
        text-align: center;
        border-right: 1px solid #e2e8f0 !important;
    }
    .sticky-col-name {
        position: sticky;
        left: 42px;
        background-color: #ffffff;
        z-index: 10;
        width: 220px;
        min-width: 220px;
        max-width: 220px;
        text-align: left !important;
        border-right: 2px solid #cbd5e1 !important;
        box-shadow: 3px 0 6px -2px rgba(0, 0, 0, 0.06);
    }
    .matrix-table thead th.sticky-col-no,
    .matrix-table thead th.sticky-col-name {
        background-color: #f8fafc;
        z-index: 20;
    }
    .matrix-table tfoot td.sticky-col-no,
    .matrix-table tfoot td.sticky-col-name {
        background-color: #f8fafc;
        z-index: 20;
    }

    /* Sel Body */
    .matrix-table tbody td {
        padding: 6px 3px;
        vertical-align: middle;
        font-size: 12px;
        border-bottom: 1px solid #f1f5f9;
        border-right: 1px solid #f1f5f9;
        text-align: center;
        height: 38px;
    }
    .matrix-table tbody tr:hover {
        background-color: #f8fafc;
    }
    .matrix-table tbody tr:hover td.sticky-col-no,
    .matrix-table tbody tr:hover td.sticky-col-name {
        background-color: #f8fafc;
    }

    /* Highlight Hari Ini di Seluruh Baris */
    .cell-today {
        background-color: #f0f7ff;
    }

    /* Tampilan Simbol Kehadiran Sesuai Desain */
    .matrix-symbol {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 12px;
        line-height: 1;
        transition: transform 0.1s ease;
        cursor: default;
    }
    .matrix-symbol:hover {
        transform: scale(1.15);
    }

    /* Hadir (v) */
    .symbol-hadir {
        color: #1e293b;
        font-size: 13px;
        font-weight: 600;
        font-family: system-ui, -apple-system, sans-serif;
    }

    /* Izin, Sakit, Cuti (Kotak Merah Solid Teks Putih Sesuai Screenshot) */
    .symbol-merah {
        width: 22px;
        height: 22px;
        border-radius: 4px;
        background-color: #dc2626 !important;
        color: #ffffff !important;
        font-weight: 700;
        font-size: 12px;
        text-transform: lowercase;
        box-shadow: 0 1px 2px rgba(220, 38, 38, 0.2);
    }

    /* Tugas Luar (Kotak Biru Solid) */
    .symbol-tugas {
        width: 22px;
        height: 22px;
        border-radius: 4px;
        background-color: #0284c7 !important;
        color: #ffffff !important;
        font-weight: 700;
        font-size: 12px;
        text-transform: lowercase;
    }

    /* Belum Absen (Kotak Merah Muda dengan Strip - Terlihat Mencolok) */
    .symbol-belum-absen {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 22px;
        height: 22px;
        border-radius: 4px;
        background-color: #fef2f2;
        border: 1px solid #fecaca;
        color: #ef4444;
        font-weight: 800;
        font-size: 13px;
    }

    /* Sel Kosong (Masa Depan / Hari Libur) */
    .symbol-empty {
        color: #cbd5e1;
        font-size: 11px;
    }

    /* Filter Pills */
    .filter-pill {
        display: inline-flex;
        align-items: center;
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        border: 1px solid #e2e8f0;
        background-color: #ffffff;
        color: #475569;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .filter-pill:hover {
        background-color: #f8fafc;
        color: #0f172a;
    }
    .filter-pill.active {
        background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important;
        color: #ffffff !important;
        border-color: #ea580c !important;
        box-shadow: 0 2px 6px rgba(234, 88, 12, 0.25);
    }

    /* Scrollbar */
    .matrix-wrapper::-webkit-scrollbar {
        height: 10px;
        width: 8px;
    }
    .matrix-wrapper::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 6px;
    }
    .matrix-wrapper::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 6px;
    }
    .matrix-wrapper::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Print View */
    @media print {
        .app-sidebar, .app-header, .no-print {
            display: none !important;
        }
        .app-main__inner {
            padding: 0 !important;
            margin: 0 !important;
        }
        .matrix-wrapper {
            border: none !important;
            box-shadow: none !important;
            overflow: visible !important;
        }
        .sticky-col-no, .sticky-col-name {
            position: static !important;
            box-shadow: none !important;
        }
    }
</style>

<div class="app-main__inner">
    <!-- PAGE TITLE & BREADCRUMB -->
    <div class="app-page-title mb-3">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon text-white shadow-sm"
                    style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 14px;">
                    <i class="pe-7s-keypad" style="color: white;"></i>
                </div>
                <div>
                    Matriks Presensi Harian (Grid View)
                    <div class="page-title-subheading">
                        Pantau absensi seluruh pegawai per tanggal secara langsung untuk mendeteksi siapa saja yang belum absen.
                    </div>
                </div>
            </div>

            <div class="page-title-actions d-flex align-items-center flex-wrap no-print" style="gap: 8px;">
                <a href="{{ route('presensi.create') }}" class="btn text-white shadow-sm"
                    style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 8px;">
                    <i class="fa fa-plus-circle mr-1"></i> Input Presensi
                </a>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs: Rekap Akumulasi vs Matriks Harian -->
    <div class="mb-3 d-flex align-items-center flex-wrap no-print" style="gap: 8px;">
        <a href="{{ route('presensi.rekap', request()->query()) }}"
            class="btn btn-sm font-weight-bold btn-light border text-muted shadow-sm hover:text-dark"
            style="border-radius: 20px; padding: 6px 16px;">
            <i class="fa fa-table mr-1"></i> Rekap Akumulasi Bulanan
        </a>
        <a href="{{ route('presensi.matriks', request()->query()) }}"
            class="btn btn-sm font-weight-bold shadow-sm"
            style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%); color: white; border-radius: 20px; padding: 6px 16px;">
            <i class="fa fa-calendar-check-o mr-1"></i> Matriks Presensi Harian
        </a>
    </div>

    <!-- KPI SUMMARY CARDS -->
    <div class="row mb-3 no-print">
        <!-- Total Pegawai -->
        <div class="col-md-3 col-sm-6 mb-2">
            <div class="stat-card p-3 shadow-sm">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-uppercase text-muted font-weight-bold" style="font-size: 11px;">Total Pegawai</span>
                        <h4 class="font-weight-bold my-1 text-dark" style="font-size: 1.5rem;">
                            {{ $summary['total_pegawai'] }}
                        </h4>
                        <small class="text-success font-weight-bold">
                            <i class="fa fa-check-circle mr-1"></i> {{ $summary['total_pegawai_aktif'] }} Akun Aktif
                        </small>
                    </div>
                    <div class="stat-icon" style="background-color: #f0fdf4; color: #16a34a;">
                        <i class="pe-7s-users font-weight-bold"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Periode Tutup Buku Tanggal 28 -->
        <div class="col-md-3 col-sm-6 mb-2">
            <div class="stat-card p-3 shadow-sm">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-uppercase text-muted font-weight-bold" style="font-size: 11px;">Periode Presensi</span>
                        <h6 class="font-weight-bold my-1 text-primary" style="font-size: 1rem; line-height: 1.3;">
                            {{ $bulan?->nama_bulan ?? 'Pilih Bulan' }}
                        </h6>
                        <small class="text-muted font-weight-bold">
                            @if ($summary['start_date'] && $summary['end_date'])
                                {{ $summary['start_date']->format('d/m/Y') }} - {{ $summary['end_date']->format('d/m/Y') }}
                            @else
                                Cut-off Tanggal 28
                            @endif
                        </small>
                    </div>
                    <div class="stat-icon" style="background-color: #eff6ff; color: #2563eb;">
                        <i class="pe-7s-date"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Tanggal Tampil -->
        <div class="col-md-3 col-sm-6 mb-2">
            <div class="stat-card p-3 shadow-sm">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-uppercase text-muted font-weight-bold" style="font-size: 11px;">Hari Ditampilkan</span>
                        <h4 class="font-weight-bold my-1 text-dark" style="font-size: 1.5rem;">
                            {{ $summary['total_hari_tampil'] }} <span style="font-size: 0.9rem; font-weight: normal; color: #64748b;">Hari</span>
                        </h4>
                        <small class="text-muted">
                            {{ $onlyWorkdays ? 'Hanya Hari Kerja (Senin - Jumat)' : 'Semua Hari' }}
                        </small>
                    </div>
                    <div class="stat-icon" style="background-color: #faf5ff; color: #9333ea;">
                        <i class="pe-7s-clock"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pegawai Belum Absen Hari Ini (ALERT) -->
        <div class="col-md-3 col-sm-6 mb-2">
            <div class="stat-card p-3 shadow-sm" style="{{ $summary['total_pegawai_belum_absen_hari_ini'] > 0 ? 'border: 1.5px solid #fca5a5; background: #fff5f5;' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-uppercase font-weight-bold {{ $summary['total_pegawai_belum_absen_hari_ini'] > 0 ? 'text-danger' : 'text-muted' }}" style="font-size: 11px;">
                            Belum Absen Hari Ini
                        </span>
                        <h4 class="font-weight-bold my-1 {{ $summary['total_pegawai_belum_absen_hari_ini'] > 0 ? 'text-danger' : 'text-success' }}" style="font-size: 1.5rem;">
                            {{ $summary['total_pegawai_belum_absen_hari_ini'] }} <span style="font-size: 0.85rem; font-weight: normal;">Orang</span>
                        </h4>
                        <small class="{{ $summary['total_pegawai_belum_absen_hari_ini'] > 0 ? 'text-danger font-weight-bold' : 'text-muted' }}">
                            {{ $summary['total_pegawai_belum_absen_hari_ini'] > 0 ? 'Perlu konfirmasi kehadiran' : 'Semua sudah tercatat' }}
                        </small>
                    </div>
                    <div class="stat-icon" style="{{ $summary['total_pegawai_belum_absen_hari_ini'] > 0 ? 'background-color: #fee2e2; color: #dc2626;' : 'background-color: #f0fdf4; color: #16a34a;' }}">
                        <i class="{{ $summary['total_pegawai_belum_absen_hari_ini'] > 0 ? 'pe-7s-attention font-weight-bold' : 'pe-7s-check' }}"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FILTER FORM CARD -->
    <div class="main-card card mb-3 shadow-sm border-0 no-print" style="border-radius: 12px;">
        <div class="card-body p-3">
            <form action="{{ route('presensi.matriks') }}" method="GET" id="filterMatriksForm">
                <div class="row align-items-end">
                    <!-- Pilihan Bulan -->
                    <div class="col-md-4 col-sm-6 mb-2">
                        <label class="form-label font-weight-bold small text-uppercase text-muted">Periode Master Bulan</label>
                        <select name="bulan_id" class="form-control form-control-sm" style="border-radius: 8px;" onchange="document.getElementById('filterMatriksForm').submit();">
                            @foreach ($bulanList as $b)
                                <option value="{{ $b->id }}" {{ $selectedBulanId == $b->id ? 'selected' : '' }}>
                                    {{ $b->nama_bulan }} {{ $b->tahunAjaran ? '(' . $b->tahunAjaran->nama_tahun_ajaran . ')' : '' }} {{ $b->status ? ' • [Aktif]' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Pilihan Kategori Karyawan -->
                    <div class="col-md-4 col-sm-6 mb-2">
                        <label class="form-label font-weight-bold small text-uppercase text-muted">Kategori Pegawai</label>
                        <select name="kepegawaian_master_kategori_id" class="form-control form-control-sm" style="border-radius: 8px;" onchange="document.getElementById('filterMatriksForm').submit();">
                            <option value="">-- Semua Kategori Pegawai --</option>
                            @foreach ($kategoriList as $kat)
                                <option value="{{ $kat->id }}" {{ $selectedKategoriId == $kat->id ? 'selected' : '' }}>
                                    {{ $kat->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Toggle Hari Kerja -->
                    <div class="col-md-2 col-sm-6 mb-2">
                        <div class="custom-control custom-switch pt-2">
                            <input type="checkbox" class="custom-control-input" id="onlyWorkdaysSwitch" name="only_workdays" value="1" {{ $onlyWorkdays ? 'checked' : '' }} onchange="document.getElementById('filterMatriksForm').submit();">
                            <label class="custom-control-label font-weight-bold small" for="onlyWorkdaysSwitch" style="cursor: pointer;">
                                Hanya Hari Kerja
                            </label>
                        </div>
                        <small class="text-muted" style="font-size: 10px;">Senin - Jumat</small>
                    </div>

                    <!-- Tombol Reset / Submit -->
                    <div class="col-md-2 col-sm-6 mb-2 text-right">
                        <a href="{{ route('presensi.matriks') }}" class="btn btn-sm btn-outline-secondary" style="border-radius: 8px;">
                            <i class="fa fa-refresh mr-1"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- FILTER BARIS CEPAT & LEGENDA SIMBOL -->
    <div class="d-flex align-items-center justify-content-between flex-wrap mb-3 px-1 no-print" style="gap: 12px;">
        <!-- Filter Pills Baris -->
        <div class="d-flex align-items-center flex-wrap" style="gap: 6px;">
            <span class="text-muted small font-weight-bold mr-1">Filter Tampilan:</span>
            <button type="button" class="filter-pill active" data-filter="all">
                Semua Pegawai ({{ $summary['total_pegawai'] }})
            </button>
            <button type="button" class="filter-pill" data-filter="belum_absen_hari_ini">
                <i class="fa fa-exclamation-circle text-danger mr-1"></i> Belum Absen Hari Ini ({{ $summary['total_pegawai_belum_absen_hari_ini'] }})
            </button>
            <button type="button" class="filter-pill" data-filter="ada_belum_absen">
                Ada Belum Absen Bulan Ini
            </button>
            <button type="button" class="filter-pill" data-filter="aktif_only">
                Pegawai Aktif Saja ({{ $summary['total_pegawai_aktif'] }})
            </button>
        </div>

        <!-- Pencarian Cepat Nama Pegawai -->
        <div style="min-width: 220px;">
            <input type="text" id="searchKaryawanInput" class="form-control form-control-sm"
                placeholder="Cari nama pegawai..." style="border-radius: 20px; font-size: 12px; padding: 6px 14px;">
        </div>
    </div>

    <!-- LEGENDA STATUS PRESENSI (Sesuai Screenshot User) -->
    <div class="card mb-3 shadow-sm border-0 no-print" style="border-radius: 10px; background-color: #ffffff;">
        <div class="card-body py-2 px-3 d-flex align-items-center justify-content-between flex-wrap" style="gap: 12px;">
            <div class="d-flex align-items-center flex-wrap" style="gap: 16px; font-size: 12px;">
                <span class="font-weight-bold text-muted small text-uppercase">Legenda Simbol:</span>
                <span class="d-inline-flex align-items-center" style="gap: 5px;">
                    <span class="matrix-symbol symbol-hadir">v</span>
                    <span class="text-dark font-weight-bold">Hadir / Terlambat</span>
                </span>
                <span class="d-inline-flex align-items-center" style="gap: 5px;">
                    <span class="matrix-symbol symbol-merah">i</span>
                    <span class="text-dark font-weight-bold">Izin</span>
                </span>
                <span class="d-inline-flex align-items-center" style="gap: 5px;">
                    <span class="matrix-symbol symbol-merah">s</span>
                    <span class="text-dark font-weight-bold">Sakit</span>
                </span>
                <span class="d-inline-flex align-items-center" style="gap: 5px;">
                    <span class="matrix-symbol symbol-merah">c</span>
                    <span class="text-dark font-weight-bold">Cuti</span>
                </span>
                <span class="d-inline-flex align-items-center" style="gap: 5px;">
                    <span class="matrix-symbol symbol-tugas">t</span>
                    <span class="text-dark font-weight-bold">Tugas Luar</span>
                </span>
                <span class="d-inline-flex align-items-center" style="gap: 5px;">
                    <span class="symbol-belum-absen">-</span>
                    <span class="text-danger font-weight-bold">Belum Absen (Hari Kerja)</span>
                </span>
            </div>
            <div class="text-muted small">
                Hover pada simbol untuk melihat detail jam masuk / pulang
            </div>
        </div>
    </div>

    <!-- MATRIKS PRESENSI TABEL UTAMA -->
    <div class="matrix-wrapper mb-4">
        <table class="table matrix-table" id="matriksTable">
            <thead>
                <tr>
                    <th class="sticky-col-no" rowspan="2">No</th>
                    <th class="sticky-col-name" rowspan="2">Nama Pegawai</th>
                    @foreach ($daftarTanggal as $tgl)
                        <th class="{{ $tgl['is_today'] ? 'col-today' : ($tgl['is_weekend'] ? 'col-weekend' : '') }}"
                            style="min-width: 32px; width: 34px;">
                            <div style="font-size: 9.5px; opacity: 0.85;">{{ $tgl['day_short'] }}</div>
                            <div style="font-size: 11px; font-weight: 700;">{{ $tgl['formatted'] }}</div>
                        </th>
                    @endforeach
                    <!-- Kolom Rekap Akumulasi di Ujung Kanan -->
                    <th style="min-width: 30px; background-color: #f8fafc;" title="Total Hadir">H</th>
                    <th style="min-width: 30px; background-color: #f8fafc;" title="Total Izin">I</th>
                    <th style="min-width: 30px; background-color: #f8fafc;" title="Total Sakit">S</th>
                    <th style="min-width: 30px; background-color: #f8fafc;" title="Total Cuti">C</th>
                    <th style="min-width: 30px; background-color: #f8fafc;" title="Total Tugas">T</th>
                    <th style="min-width: 32px; background-color: #fef2f2; color: #dc2626;" title="Total Belum Absen">BA</th>
                    <th style="min-width: 48px; background-color: #f8fafc;" title="Persentase Kehadiran">%</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pegawaiRows as $index => $row)
                    @php
                        $karyawan = $row['karyawan'];
                        $totals = $row['totals'];
                        $isActive = $row['user_is_active'];
                        $belumAbsenHariIni = $row['belum_absen_hari_ini'];
                        $hasBelumAbsen = $totals['belum_absen'] > 0;
                    @endphp
                    <tr class="pegawai-row"
                        data-name="{{ strtolower($karyawan->nama_lengkap) }}"
                        data-active="{{ $isActive ? '1' : '0' }}"
                        data-belum-hari-ini="{{ $belumAbsenHariIni ? '1' : '0' }}"
                        data-ada-belum="{{ $hasBelumAbsen ? '1' : '0' }}">
                        <td class="sticky-col-no font-weight-bold text-muted row-number">{{ $index + 1 }}</td>
                        <td class="sticky-col-name">
                            <div class="d-flex align-items-center justify-content-between" style="gap: 4px;">
                                <div class="text-truncate" style="max-width: 175px;" title="{{ $karyawan->nama_lengkap }}{{ $karyawan->gelar ? ', ' . $karyawan->gelar : '' }}">
                                    <span class="font-weight-bold text-dark" style="font-size: 12.5px;">
                                        {{ $karyawan->nama_lengkap }}{{ $karyawan->gelar ? ', ' . $karyawan->gelar : '' }}
                                    </span>
                                    <div class="text-muted" style="font-size: 10px; font-family: monospace;">
                                        {{ $karyawan->nipy }}
                                        @if ($karyawan->unit)
                                            &bull; {{ $karyawan->unit }}
                                        @endif
                                    </div>
                                </div>
                                @if (! $isActive)
                                    <span class="badge badge-danger px-1 py-0" style="font-size: 9px;" title="Akun Non-Aktif">OFF</span>
                                @elseif ($belumAbsenHariIni)
                                    <span class="badge badge-warning px-1 py-0 text-white" style="font-size: 9px;" title="Belum Absen Hari Ini">!</span>
                                @endif
                            </div>
                        </td>

                        <!-- Loop Sel Tanggal Matriks -->
                        @foreach ($daftarTanggal as $tgl)
                            @php
                                $dateStr = $tgl['date_string'];
                                $rec = $row['records'][$dateStr] ?? null;
                                $status = $rec['status'] ?? 'empty';
                                $symbol = $rec['symbol'] ?? '';
                            @endphp
                            <td class="{{ $tgl['is_today'] ? 'cell-today' : '' }}">
                                @if ($status === 'hadir' || $status === 'terlambat')
                                    <span class="matrix-symbol symbol-hadir"
                                        data-toggle="tooltip" data-placement="top"
                                        title="{{ $karyawan->nama_lengkap }} ({{ $tgl['formatted'] }}): Hadir {{ $rec['jam_masuk'] ? 'Jam ' . $rec['jam_masuk'] : '' }} {{ $rec['keterlambatan_menit'] > 0 ? '[Terlambat ' . $rec['keterlambatan_menit'] . ' mnt]' : '' }}">
                                        v
                                    </span>
                                @elseif (in_array($status, ['izin', 'sakit', 'cuti']))
                                    <span class="matrix-symbol symbol-merah"
                                        data-toggle="tooltip" data-placement="top"
                                        title="{{ $karyawan->nama_lengkap }} ({{ $tgl['formatted'] }}): {{ ucfirst($status) }} {{ $rec['catatan'] ? '- ' . $rec['catatan'] : '' }}">
                                        {{ $symbol }}
                                    </span>
                                @elseif ($status === 'tugas')
                                    <span class="matrix-symbol symbol-tugas"
                                        data-toggle="tooltip" data-placement="top"
                                        title="{{ $karyawan->nama_lengkap }} ({{ $tgl['formatted'] }}): Tugas Luar {{ $rec['catatan'] ? '- ' . $rec['catatan'] : '' }}">
                                        t
                                    </span>
                                @elseif ($status === 'belum_absen')
                                    <span class="symbol-belum-absen"
                                        data-toggle="tooltip" data-placement="top"
                                        title="{{ $karyawan->nama_lengkap }} ({{ $tgl['formatted'] }}): Belum Absen!">
                                        -
                                    </span>
                                @else
                                    <span class="symbol-empty">&middot;</span>
                                @endif
                            </td>
                        @endforeach

                        <!-- Kolom Rekap Akumulasi di Ujung Kanan -->
                        <td class="font-weight-bold text-dark">{{ $totals['total_hadir_semua'] }}</td>
                        <td class="font-weight-bold {{ $totals['izin'] > 0 ? 'text-danger' : 'text-muted' }}">{{ $totals['izin'] ?: '-' }}</td>
                        <td class="font-weight-bold {{ $totals['sakit'] > 0 ? 'text-danger' : 'text-muted' }}">{{ $totals['sakit'] ?: '-' }}</td>
                        <td class="font-weight-bold {{ $totals['cuti'] > 0 ? 'text-danger' : 'text-muted' }}">{{ $totals['cuti'] ?: '-' }}</td>
                        <td class="font-weight-bold {{ $totals['tugas'] > 0 ? 'text-info' : 'text-muted' }}">{{ $totals['tugas'] ?: '-' }}</td>
                        <td class="font-weight-bold {{ $totals['belum_absen'] > 0 ? 'text-danger bg-danger-soft' : 'text-muted' }}" style="{{ $totals['belum_absen'] > 0 ? 'background-color: #fee2e2;' : '' }}">
                            {{ $totals['belum_absen'] ?: '-' }}
                        </td>
                        <td class="font-weight-bold" style="font-size: 11px;">
                            {{ $totals['persen_kehadiran'] }}%
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $daftarTanggal->count() + 9 }}" class="text-center py-5 text-muted">
                            <i class="pe-7s-info text-warning d-block mb-2" style="font-size: 2.5rem;"></i>
                            Tidak ada data pegawai untuk kategori atau periode yang dipilih.
                        </td>
                    </tr>
                @endforelse
            </tbody>

            <!-- FOOTER TABEL (RINGKASAN TOTAL HARIAN) -->
            @if ($pegawaiRows->isNotEmpty())
                <tfoot>
                    <tr style="background-color: #f8fafc; font-weight: 700; border-top: 2px solid #cbd5e1;">
                        <td class="sticky-col-no text-center font-weight-bold">&Sigma;</td>
                        <td class="sticky-col-name text-dark font-weight-bold">
                            Total Hadir Harian
                        </td>
                        @foreach ($daftarTanggal as $tgl)
                            @php
                                $dTot = $columnTotals[$tgl['date_string']] ?? null;
                                $totHadir = ($dTot['hadir'] ?? 0) + ($dTot['terlambat'] ?? 0);
                            @endphp
                            <td class="{{ $tgl['is_today'] ? 'cell-today' : '' }} text-center" style="font-size: 11px; color: #0f172a;">
                                {{ $totHadir ?: '-' }}
                            </td>
                        @endforeach
                        <td colspan="7"></td>
                    </tr>
                    <tr style="background-color: #fff5f5; font-weight: 700;">
                        <td class="sticky-col-no text-center font-weight-bold text-danger">!</td>
                        <td class="sticky-col-name text-danger font-weight-bold" style="background-color: #fff5f5 !important;">
                            Belum Absen
                        </td>
                        @foreach ($daftarTanggal as $tgl)
                            @php
                                $dTot = $columnTotals[$tgl['date_string']] ?? null;
                                $totBelum = $dTot['belum_absen'] ?? 0;
                            @endphp
                            <td class="{{ $tgl['is_today'] ? 'cell-today' : '' }} text-center font-weight-bold"
                                style="font-size: 11px; color: {{ $totBelum > 0 ? '#dc2626' : '#94a3b8' }};">
                                {{ $totBelum > 0 ? $totBelum : '-' }}
                            </td>
                        @endforeach
                        <td colspan="7"></td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        // Inisialisasi tooltip bootstrap
        $('[data-toggle="tooltip"]').tooltip({
            boundary: 'window',
            container: 'body'
        });

        var currentPillFilter = 'all';
        var searchKeyword = '';

        function filterRows() {
            var visibleCount = 0;

            $('.pegawai-row').each(function() {
                var row = $(this);
                var name = row.attr('data-name') || '';
                var isActive = row.attr('data-active') === '1';
                var belumHariIni = row.attr('data-belum-hari-ini') === '1';
                var adaBelum = row.attr('data-ada-belum') === '1';

                var matchesPill = true;
                if (currentPillFilter === 'belum_absen_hari_ini') {
                    matchesPill = belumHariIni;
                } else if (currentPillFilter === 'ada_belum_absen') {
                    matchesPill = adaBelum;
                } else if (currentPillFilter === 'aktif_only') {
                    matchesPill = isActive;
                }

                var matchesSearch = true;
                if (searchKeyword.trim() !== '') {
                    matchesSearch = name.indexOf(searchKeyword.toLowerCase()) !== -1;
                }

                if (matchesPill && matchesSearch) {
                    row.show();
                    visibleCount++;
                    row.find('.row-number').text(visibleCount);
                } else {
                    row.hide();
                }
            });
        }

        // Event Klik Filter Pills
        $('.filter-pill').on('click', function(e) {
            e.preventDefault();
            $('.filter-pill').removeClass('active');
            $(this).addClass('active');

            currentPillFilter = $(this).data('filter');
            filterRows();
        });

        // Event Pencarian Real-time
        $('#searchKaryawanInput').on('keyup input', function() {
            searchKeyword = $(this).val();
            filterRows();
        });
    });
</script>
@endsection
