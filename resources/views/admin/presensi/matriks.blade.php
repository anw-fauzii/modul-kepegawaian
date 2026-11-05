@extends('layouts.appui')

@section('title')
    <title>Matriks Presensi Harian Pegawai - SIPIT</title>
@endsection

@section('content')
<style>
    /* Modern KPI & Filter Cards */
    .stat-card {
        border-radius: 14px;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(226, 232, 240, 0.85);
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
        flex-shrink: 0;
    }
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

    /* Filter Pills */
    .filter-pill {
        display: inline-flex;
        align-items: center;
        padding: 5px 13px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        border: 1px solid #e2e8f0;
        background-color: #f8fafc;
        color: #475569;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .filter-pill:hover {
        background-color: #fff7ed;
        border-color: #fed7aa;
        color: #ea580c;
        text-decoration: none;
    }
    .filter-pill.active {
        background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important;
        color: #ffffff !important;
        border-color: #ea580c !important;
        box-shadow: 0 2px 6px rgba(234, 88, 12, 0.25);
    }

    /* Legend Chips */
    .legend-card {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
    }
    .legend-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 9px;
        border-radius: 6px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        font-size: 11.5px;
        font-weight: 600;
        color: #334155;
    }

    /* Container Matriks dengan Scrollbar Halus */
    .matrix-wrapper {
        border-radius: 14px;
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
        color: #475569;
        font-weight: 700;
        font-size: 11px;
        border-top: none;
        border-bottom: 2px solid #e2e8f0;
        border-right: 1px solid #f1f5f9;
        padding: 8px 4px;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
    }
    .matrix-table thead th.col-today {
        background-color: #fff7ed !important;
        border-bottom: 2px solid #ea580c !important;
        color: #c2410c !important;
    }
    .matrix-table thead th.col-weekend {
        background-color: #f1f5f9 !important;
        color: #94a3b8 !important;
    }

    /* Tombol Klik Tanggal pada Header Tabel Matriks */
    .btn-matrix-date {
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: 4px 3px;
        border-radius: 7px;
        border: 1px solid #e2e8f0;
        background-color: #ffffff;
        color: #334155;
        text-decoration: none !important;
        transition: all 0.18s ease;
        cursor: pointer;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
    }
    .col-today .btn-matrix-date {
        background-color: #ffedd5;
        border-color: #fdba74;
        color: #c2410c;
    }
    .col-weekend .btn-matrix-date {
        background-color: #f8fafc;
        border-color: #e2e8f0;
        color: #94a3b8;
    }
    .btn-matrix-date:hover {
        background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important;
        color: #ffffff !important;
        border-color: #ea580c !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(234, 88, 12, 0.25);
    }

    /* Sticky Columns (Kolom No & Nama Pegawai Freeze di Kiri) */
    .sticky-col-no {
        position: sticky;
        left: 0;
        background-color: #ffffff;
        z-index: 10;
        width: 40px;
        min-width: 40px;
        max-width: 40px;
        text-align: center;
        border-right: 1px solid #e2e8f0 !important;
    }
    .sticky-col-name {
        position: sticky;
        left: 40px;
        background-color: #ffffff;
        z-index: 10;
        width: 245px;
        min-width: 245px;
        max-width: 245px;
        text-align: left !important;
        padding-left: 10px !important;
        padding-right: 8px !important;
        border-right: 2px solid #cbd5e1 !important;
        box-shadow: 3px 0 6px -2px rgba(0, 0, 0, 0.05);
    }
    .matrix-table thead th.sticky-col-no,
    .matrix-table thead th.sticky-col-name,
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
        height: 42px;
    }
    .matrix-table tbody tr:hover td {
        background-color: #fff7ed;
    }
    .matrix-table tbody tr:hover td.sticky-col-name {
        background-color: #fff7ed;
    }

    /* Highlight Hari Ini di Seluruh Baris */
    .cell-today {
        background-color: #fffaf5;
    }

    /* Simbol Kehadiran */
    .matrix-symbol {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 12px;
        line-height: 1;
        transition: transform 0.12s ease;
        cursor: default;
    }
    .matrix-symbol:hover {
        transform: scale(1.18);
    }

    /* Hadir (v) */
    .symbol-hadir {
        width: 22px;
        height: 22px;
        border-radius: 5px;
        background-color: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #059669;
        font-size: 12px;
        font-weight: 800;
    }
    .symbol-terlambat {
        width: 22px;
        height: 22px;
        border-radius: 5px;
        background-color: #fffbeb;
        border: 1px solid #fde68a;
        color: #d97706;
        font-size: 12px;
        font-weight: 800;
    }

    /* Izin, Sakit, Cuti */
    .symbol-merah {
        width: 22px;
        height: 22px;
        border-radius: 5px;
        background-color: #dc2626 !important;
        color: #ffffff !important;
        font-weight: 700;
        font-size: 11.5px;
        text-transform: lowercase;
        box-shadow: 0 1px 2px rgba(220, 38, 38, 0.2);
    }

    /* Tugas Luar */
    .symbol-tugas {
        width: 22px;
        height: 22px;
        border-radius: 5px;
        background-color: #0284c7 !important;
        color: #ffffff !important;
        font-weight: 700;
        font-size: 11.5px;
        text-transform: lowercase;
    }

    /* Libur */
    .symbol-libur {
        width: 22px;
        height: 22px;
        border-radius: 5px;
        background-color: #64748b !important;
        color: #ffffff !important;
        font-weight: 700;
        font-size: 11.5px;
        text-transform: lowercase;
    }

    /* Belum Absen */
    .symbol-belum-absen {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 22px;
        height: 22px;
        border-radius: 5px;
        background-color: #fef2f2;
        border: 1px solid #fecaca;
        color: #ef4444;
        font-weight: 800;
        font-size: 13px;
    }

    /* Sel Kosong */
    .symbol-empty {
        color: #cbd5e1;
        font-size: 12px;
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
    <!-- PAGE TITLE & ACTIONS -->
    <div class="app-page-title mb-3">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon text-white shadow-sm"
                    style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 14px;">
                    <i class="pe-7s-keypad" style="color: white;"></i>
                </div>
                <div>
                    Matriks Presensi Harian
                    <div class="page-title-subheading">
                        Pantau kehadiran seluruh pegawai per tanggal secara visual.
                    </div>
                </div>
            </div>

            <div class="page-title-actions d-flex align-items-center flex-wrap no-print" style="gap: 8px;">
                <a href="{{ route('presensi.rekap', request()->query()) }}"
                    class="btn btn-outline-secondary shadow-sm font-weight-bold" style="border-radius: 8px;">
                    <i class="fa fa-table mr-1"></i> Rekap Bulanan
                </a>

                <a href="{{ route('presensi.matriks.pdf', request()->query()) }}"
                    target="_blank"
                    class="btn text-white font-weight-bold btn-cetak-pdf"
                    style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important; border-radius: 8px; border: none; font-size: 13px;"
                    title="Buka Dokumen PDF Matriks Presensi (Kertas A3 Landscape)">
                    <i class="fa fa-download"></i> Download
                </a>

                <a href="{{ route('presensi.create', array_filter([
                    'kepegawaian_master_bulan_id'    => $selectedBulanId,
                    'kepegawaian_master_kategori_id' => $selectedKategoriId,
                ])) }}" class="btn text-white shadow-sm font-weight-bold"
                    style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 8px; border: none; font-size: 13px;">
                    <i class="fa fa-plus-circle mr-1"></i> Input Presensi
                </a>
            </div>
        </div>
    </div>

    <!-- KPI SUMMARY CARDS -->
    <div class="row mb-3 no-print">
        <div class="col-md-3 col-sm-6 mb-2">
            <div class="stat-card p-3 shadow-sm">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-uppercase text-muted font-weight-bold" style="font-size: 11px;">Total Pegawai</span>
                        <h4 class="font-weight-bold my-1 text-dark" style="font-size: 1.45rem;">
                            {{ $summary['total_pegawai'] }}
                        </h4>
                        <small class="text-success font-weight-bold">
                            <i class="fa fa-check-circle mr-1"></i> {{ $summary['total_pegawai_aktif'] }} Akun Aktif
                        </small>
                    </div>
                    <div class="stat-icon" style="background-color: #ecfdf5; color: #10b981;">
                        <i class="pe-7s-users font-weight-bold"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-2">
            <div class="stat-card p-3 shadow-sm">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-uppercase text-muted font-weight-bold" style="font-size: 11px;">Periode Presensi</span>
                        <h6 class="font-weight-bold my-1 text-dark" style="font-size: 1rem; line-height: 1.35;">
                            {{ $bulan?->nama_bulan ?? 'Pilih Bulan' }}
                        </h6>
                        <small class="text-muted font-weight-bold">
                            @if ($summary['start_date'] && $summary['end_date'])
                                {{ $summary['start_date']->format('d/m/Y') }} &ndash; {{ $summary['end_date']->format('d/m/Y') }}
                            @else
                                Cut-off Tanggal 28
                            @endif
                        </small>
                    </div>
                    <div class="stat-icon" style="background-color: #fff7ed; color: #ea580c;">
                        <i class="pe-7s-date"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-2">
            <div class="stat-card p-3 shadow-sm">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-uppercase text-muted font-weight-bold" style="font-size: 11px;">Hari Ditampilkan</span>
                        <h4 class="font-weight-bold my-1 text-dark" style="font-size: 1.45rem;">
                            {{ $summary['total_hari_tampil'] }} <span style="font-size: 0.85rem; font-weight: normal; color: #64748b;">Hari</span>
                        </h4>
                        <small class="text-muted">
                            {{ $onlyWorkdays ? 'Hari Kerja (Senin - Jumat)' : 'Semua Hari' }}
                        </small>
                    </div>
                    <div class="stat-icon" style="background-color: #eef2ff; color: #6366f1;">
                        <i class="pe-7s-clock"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-2">
            <div class="stat-card p-3 shadow-sm" style="{{ $summary['total_pegawai_belum_absen_hari_ini'] > 0 ? 'border: 1.5px solid #fca5a5; background: #fff5f5;' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-uppercase font-weight-bold {{ $summary['total_pegawai_belum_absen_hari_ini'] > 0 ? 'text-danger' : 'text-muted' }}" style="font-size: 11px;">
                            Belum Absen Hari Ini
                        </span>
                        <h4 class="font-weight-bold my-1 {{ $summary['total_pegawai_belum_absen_hari_ini'] > 0 ? 'text-danger' : 'text-success' }}" style="font-size: 1.45rem;">
                            {{ $summary['total_pegawai_belum_absen_hari_ini'] }} <span style="font-size: 0.85rem; font-weight: normal;">Orang</span>
                        </h4>
                        <small class="{{ $summary['total_pegawai_belum_absen_hari_ini'] > 0 ? 'text-danger font-weight-bold' : 'text-muted' }}">
                            {{ $summary['total_pegawai_belum_absen_hari_ini'] > 0 ? 'Perlu konfirmasi kehadiran' : 'Semua sudah tercatat' }}
                        </small>
                    </div>
                    <div class="stat-icon" style="{{ $summary['total_pegawai_belum_absen_hari_ini'] > 0 ? 'background-color: #fee2e2; color: #dc2626;' : 'background-color: #ecfdf5; color: #10b981;' }}">
                        <i class="{{ $summary['total_pegawai_belum_absen_hari_ini'] > 0 ? 'pe-7s-attention font-weight-bold' : 'pe-7s-check' }}"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FILTER & QUICK TOOLBAR CARD -->
    <div class="filter-card card mb-3 no-print">
        <div class="card-body p-3">
            <form action="{{ route('presensi.matriks') }}" method="GET" id="filterMatriksForm">
                <div class="row align-items-end">
                    <div class="col-md-3 mb-2 mb-md-0">
                        <label class="form-filter-label d-flex justify-content-between">
                            <span><i class="fa fa-calendar-alt mr-1 text-warning"></i> Periode Master Bulan</span>
                        </label>
                        <select name="bulan_id" class="form-control form-filter-control" onchange="document.getElementById('filterMatriksForm').submit();">
                            @foreach ($bulanList as $b)
                                <option value="{{ $b->id }}" {{ $selectedBulanId == $b->id ? 'selected' : '' }}>
                                    {{ $b->nama_bulan }} {{ $b->tahunAjaran ? '(' . $b->tahunAjaran->nama_tahun_ajaran . ')' : '' }} {{ $b->status ? ' [Aktif]' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-2 mb-md-0">
                        <label class="form-filter-label">
                            <i class="fa fa-layer-group mr-1 text-primary"></i> Kategori Pegawai
                        </label>
                        <select name="kepegawaian_master_kategori_id" class="form-control form-filter-control" onchange="document.getElementById('filterMatriksForm').submit();">
                            <option value="">Semua Kategori Pegawai</option>
                            @foreach ($kategoriList as $kat)
                                <option value="{{ $kat->id }}" {{ $selectedKategoriId == $kat->id ? 'selected' : '' }}>
                                    {{ $kat->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 mb-2 mb-md-0">
                        <label class="form-filter-label">
                            <i class="fa fa-search mr-1 text-secondary"></i> Cari Nama Pegawai
                        </label>
                        <input type="text" id="searchKaryawanInput" class="form-control form-filter-control"
                            placeholder="Ketik nama pegawai...">
                    </div>

                    <div class="col-md-2 col-sm-12 mb-2 mb-md-0">
                        <a href="{{ route('presensi.matriks') }}" class="btn btn-light border btn-block shadow-sm d-flex align-items-center justify-content-center"
                            style="border-radius: 8px; height: 38px;" title="Reset Filter">
                            <i class="fa fa-undo"></i>
                        </a>
                    </div>
                </div>
            </form>

            <!-- FILTER PILLS BARIS -->
            <div class="d-flex align-items-center justify-content-between flex-wrap pt-3 mt-3 border-top" style="gap: 8px;">
                <div class="d-flex align-items-center flex-wrap" style="gap: 6px;">
                    <span class="text-muted small font-weight-bold mr-1">
                        <i class="fa fa-bolt text-warning mr-1"></i> Tampilkan:
                    </span>
                    <button type="button" class="filter-pill active" data-filter="all">
                        Semua Pegawai ({{ $summary['total_pegawai'] }})
                    </button>
                    <button type="button" class="filter-pill" data-filter="belum_absen_hari_ini">
                        <i class="fa fa-user-clock text-danger mr-1"></i> Belum Absen Hari Ini ({{ $summary['total_pegawai_belum_absen_hari_ini'] }})
                    </button>
                    <button type="button" class="filter-pill" data-filter="ada_belum_absen">
                        Ada Belum Absen Bulan Ini
                    </button>
                </div>
                <div class="small text-muted">
                    <i class="fa fa-hand-pointer-o mr-1 text-warning"></i> Klik tanggal di header tabel untuk input massal pada tanggal tersebut
                </div>
            </div>
        </div>
    </div>

    <!-- LEGENDA STATUS PRESENSI & WARNA NOMOR -->
    <div class="legend-card mb-3 p-2.5 px-3 shadow-sm no-print">
        <div class="d-flex align-items-center justify-content-between flex-wrap pb-2 mb-2 border-bottom" style="gap: 8px;">
            <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                <span class="font-weight-bold text-muted small text-uppercase mr-1" style="font-size: 10.5px;">Simbol:</span>
                <span class="legend-chip">
                    <span class="matrix-symbol symbol-hadir">v</span> Hadir
                </span>
                <span class="legend-chip">
                    <span class="matrix-symbol symbol-terlambat">v</span> Terlambat
                </span>
                <span class="legend-chip">
                    <span class="matrix-symbol symbol-merah">i</span> Izin
                </span>
                <span class="legend-chip">
                    <span class="matrix-symbol symbol-merah">s</span> Sakit
                </span>
                <span class="legend-chip">
                    <span class="matrix-symbol symbol-merah">c</span> Cuti
                </span>
                <span class="legend-chip">
                    <span class="matrix-symbol symbol-tugas">t</span> Tugas
                </span>
                <span class="legend-chip">
                    <span class="matrix-symbol symbol-libur">l</span> Libur
                </span>
                <span class="legend-chip">
                    <span class="symbol-belum-absen">-</span> <span class="text-danger">Belum Absen</span>
                </span>
            </div>
        </div>

        <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
            <span class="font-weight-bold text-muted small text-uppercase mr-1" style="font-size: 10.5px;">Indikator Nomor:</span>
            <span class="legend-chip" title="Tidak ada keterlambatan, semua nol kecuali cuti">
                <span class="badge border font-weight-bold" style="background-color: #fef08a; color: #854d0e; border-color: #ca8a04 !important;">No</span>
                Reward (Tepat Waktu)
            </span>
            <span class="legend-chip" title="3 Kali Terlambat dalam 7 hari">
                <span class="badge font-weight-bold text-white" style="background-color: #3b82f6;">No</span>
                3x Telat (7 Hari)
            </span>
            <span class="legend-chip" title="5 Kali Terlambat dalam 1 bulan">
                <span class="badge font-weight-bold text-white" style="background-color: #22c55e;">No</span>
                5x Telat (1 Bulan)
            </span>
            <span class="legend-chip" title="5 Kali Terlambat dalam 2 bulan berturut-turut">
                <span class="badge font-weight-bold text-white" style="background-color: #ef4444;">No</span>
                5x Telat (2 Bulan Berturut)
            </span>
            <span class="legend-chip" title="Tidak ada data presensi">
                <span class="badge font-weight-bold text-white" style="background-color: #0f172a;">No</span>
                Kosong
            </span>
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
                            style="min-width: 38px; width: 40px; padding: 4px 2px;">
                            <a href="{{ route('presensi.create', array_filter([
                                'tanggal'                        => $tgl['date_string'],
                                'kepegawaian_master_bulan_id'    => $selectedBulanId,
                                'kepegawaian_master_kategori_id' => $selectedKategoriId,
                            ])) }}"
                                class="btn-matrix-date"
                                data-toggle="tooltip"
                                data-placement="top"
                                title="Klik untuk input presensi {{ $tgl['day_name'] }}, {{ $tgl['date']->format('d/m/Y') }}">
                                <span style="font-size: 9.5px; opacity: 0.85; line-height: 1.1;">{{ $tgl['day_short'] }}</span>
                                <span style="font-size: 11px; font-weight: 700; line-height: 1.2;">{{ $tgl['formatted'] }}</span>
                            </a>
                        </th>
                    @endforeach
                    <!-- Kolom Rekap Akumulasi di Ujung Kanan -->
                    <th style="min-width: 32px; background-color: #ecfdf5; color: #065f46;" title="Total Hadir">H</th>
                    <th style="min-width: 30px; background-color: #f8fafc;" title="Total Izin">I</th>
                    <th style="min-width: 30px; background-color: #f8fafc;" title="Total Sakit">S</th>
                    <th style="min-width: 30px; background-color: #f8fafc;" title="Total Cuti">C</th>
                    <th style="min-width: 30px; background-color: #f8fafc;" title="Total Tugas">T</th>
                    <th style="min-width: 34px; background-color: #fef2f2; color: #dc2626;" title="Total Belum Absen">BA</th>
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
                        $statusNomor = $row['status_nomor'] ?? [
                            'code'       => 'putih',
                            'bg_color'   => '#ffffff',
                            'text_color' => '#1e293b',
                            'desc'       => 'Normal',
                        ];
                    @endphp
                    <tr class="pegawai-row"
                        data-name="{{ strtolower($karyawan->nama_lengkap) }}"
                        data-active="{{ $isActive ? '1' : '0' }}"
                        data-belum-hari-ini="{{ $belumAbsenHariIni ? '1' : '0' }}"
                        data-ada-belum="{{ $hasBelumAbsen ? '1' : '0' }}">
                        <td class="sticky-col-no font-weight-bold row-number"
                            style="background-color: {{ $statusNomor['bg_color'] }} !important; color: {{ $statusNomor['text_color'] }} !important;"
                            data-toggle="tooltip" data-placement="right" title="{{ $statusNomor['desc'] }}">
                            {{ $index + 1 }}
                        </td>
                        <td class="sticky-col-name">
                            <div class="d-flex align-items-center justify-content-between" style="gap: 6px;">
                                <div class="d-flex align-items-center" style="gap: 8px; min-width: 0;">
                                    @if ($karyawan->foto_url)
                                        <img src="{{ $karyawan->foto_url }}" alt="Foto"
                                            style="width: 28px; height: 28px; object-fit: cover; border-radius: 50%; border: 1.5px solid #fff; box-shadow: 0 1px 3px rgba(0,0,0,.15); flex-shrink: 0;">
                                    @else
                                        <span class="d-flex align-items-center justify-content-center text-white font-weight-bold"
                                            style="width: 28px; height: 28px; border-radius: 50%; background: linear-gradient(135deg, #ea580c, #d97706); font-size: 0.72rem; flex-shrink: 0;">
                                            {{ strtoupper(mb_substr($karyawan->nama_lengkap ?? 'P', 0, 1)) }}
                                        </span>
                                    @endif
                                    <div style="min-width: 0; line-height: 1.2;">
                                        <div class="font-weight-bold text-dark text-truncate" style="font-size: 12px; max-width: 170px;"
                                            title="{{ $karyawan->nama_lengkap }}{{ $karyawan->gelar ? ', ' . $karyawan->gelar : '' }}">
                                            {{ $karyawan->nama_lengkap }}{{ $karyawan->gelar ? ', ' . $karyawan->gelar : '' }}
                                        </div>
                                        <small class="text-muted d-block text-truncate" style="font-size: 10px; max-width: 170px;">
                                            {{ $karyawan->nipy }}{{ $karyawan->golongan ? ' • Gol. ' . $karyawan->golongan : '' }}
                                        </small>
                                    </div>
                                </div>
                                @if (! $isActive)
                                    <span class="badge badge-danger px-1 py-0" style="font-size: 9px;" title="Akun Non-Aktif">OFF</span>
                                @elseif ($belumAbsenHariIni)
                                    <span class="badge badge-warning px-1.5 py-0 text-white" style="font-size: 9px;" title="Belum Absen Hari Ini">!</span>
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
                                @if ($status === 'hadir')
                                    <span class="matrix-symbol symbol-hadir"
                                        data-toggle="tooltip" data-placement="top"
                                        title="{{ $karyawan->nama_lengkap }} ({{ $tgl['formatted'] }}): Hadir {{ $rec['jam_masuk'] ? 'Jam ' . $rec['jam_masuk'] : '' }}">
                                        v
                                    </span>
                                @elseif ($status === 'terlambat')
                                    <span class="matrix-symbol symbol-terlambat"
                                        data-toggle="tooltip" data-placement="top"
                                        title="{{ $karyawan->nama_lengkap }} ({{ $tgl['formatted'] }}): Terlambat {{ $rec['jam_masuk'] ? 'Jam ' . $rec['jam_masuk'] : '' }} {{ $rec['keterlambatan_menit'] > 0 ? '(+' . $rec['keterlambatan_menit'] . ' mnt)' : '' }}">
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
                                @elseif ($status === 'libur')
                                    <span class="matrix-symbol symbol-libur"
                                        data-toggle="tooltip" data-placement="top"
                                        title="{{ $karyawan->nama_lengkap }} ({{ $tgl['formatted'] }}): Libur {{ $rec['catatan'] ? '- ' . $rec['catatan'] : '' }}">
                                        l
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
                        <td class="font-weight-bold text-success" style="background-color: #f8fafc;">{{ $totals['total_hadir_semua'] }}</td>
                        <td class="font-weight-bold {{ $totals['izin'] > 0 ? 'text-danger' : 'text-muted' }}">{{ $totals['izin'] ?: '-' }}</td>
                        <td class="font-weight-bold {{ $totals['sakit'] > 0 ? 'text-danger' : 'text-muted' }}">{{ $totals['sakit'] ?: '-' }}</td>
                        <td class="font-weight-bold {{ $totals['cuti'] > 0 ? 'text-danger' : 'text-muted' }}">{{ $totals['cuti'] ?: '-' }}</td>
                        <td class="font-weight-bold {{ $totals['tugas'] > 0 ? 'text-info' : 'text-muted' }}">{{ $totals['tugas'] ?: '-' }}</td>
                        <td class="font-weight-bold {{ $totals['belum_absen'] > 0 ? 'text-danger' : 'text-muted' }}" style="{{ $totals['belum_absen'] > 0 ? 'background-color: #fef2f2;' : '' }}">
                            {{ $totals['belum_absen'] ?: '-' }}
                        </td>
                        <td class="font-weight-bold text-dark" style="font-size: 11px; background-color: #f8fafc;">
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
                        <td class="sticky-col-no text-center font-weight-bold text-success">&Sigma;</td>
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
                        <td class="sticky-col-no text-center font-weight-bold text-danger" style="background-color: #fff5f5 !important;">!</td>
                        <td class="sticky-col-name text-danger font-weight-bold" style="background-color: #fff5f5 !important;">
                            Belum Absen Harian
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

            updatePdfLinks();
        }

        // Fungsi Sinkronisasi Link Cetak PDF dengan Filter & Pencarian Aktif
        function updatePdfLinks() {
            $('.btn-cetak-pdf').each(function() {
                var el = $(this);
                var baseHref = el.data('base-href');
                if (!baseHref) {
                    baseHref = el.attr('href');
                    el.data('base-href', baseHref);
                }

                try {
                    var parsed = new URL(baseHref, window.location.origin);
                    if (currentPillFilter && currentPillFilter !== 'all') {
                        parsed.searchParams.set('filter_status', currentPillFilter);
                    } else {
                        parsed.searchParams.delete('filter_status');
                    }
                    if (searchKeyword && searchKeyword.trim() !== '') {
                        parsed.searchParams.set('search', searchKeyword.trim());
                    } else {
                        parsed.searchParams.delete('search');
                    }
                    el.attr('href', parsed.pathname + parsed.search);
                } catch (err) {
                    // Ignore fallback
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
