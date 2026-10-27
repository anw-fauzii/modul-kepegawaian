@extends('layouts.appui')

@section('title')
    <title>Statistik Presensi Tahunan - SIPIT</title>
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
        box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.08);
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

    /* Filter Panel */
    .filter-panel {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
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

    /* Tables */
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
    .modern-table tfoot td {
        background-color: #f8fafc;
        font-weight: 700;
        border-top: 2px solid #cbd5e1;
        padding: 12px 10px;
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

    /* Scrollbar */
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

    /* Print Styles */
    @media print {
        .app-sidebar, .app-header, .app-page-title .page-title-actions, .filter-section, .no-print {
            display: none !important;
        }
        .app-main__inner {
            padding: 0 !important;
        }
        .stat-card, .filter-panel, .card {
            border: 1px solid #ddd !important;
            box-shadow: none !important;
            break-inside: avoid;
        }
        .print-header {
            display: block !important;
        }
    }
    .print-header {
        display: none;
    }
</style>

<div class="app-main__inner">
    <!-- PRINT HEADER -->
    <div class="print-header text-center mb-4">
        <h3 class="font-weight-bold mb-1">LAPORAN STATISTIK PRESENSI TAHUNAN</h3>
        <h5 class="text-muted mb-0">
            Tahun Ajaran: {{ $tahunAjaran?->nama_tahun_ajaran ?? '-' }} {{ $tahunAjaran?->semester ? '(' . $tahunAjaran->semester . ')' : '' }}
            &bull; Agregasi Master Bulan (Tutup Buku Tanggal 28)
        </h5>
        @if ($selectedKategoriId && $kategoriList->firstWhere('id', $selectedKategoriId))
            <div class="badge badge-primary px-3 py-1 mt-1 font-weight-normal">
                Kategori Pegawai: {{ $kategoriList->firstWhere('id', $selectedKategoriId)->nama_kategori }}
            </div>
        @endif
        <hr>
    </div>

    <!-- PAGE TITLE & BREADCRUMB -->
    <div class="app-page-title mb-3">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon text-white shadow-sm"
                    style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important; border-radius: 14px;">
                    <i class="pe-7s-display1" style="color: white;"></i>
                </div>
                <div>
                    Statistik Presensi Tahunan
                    <div class="page-title-subheading">
                        Analisis kehadiran pegawai berbasis <strong>Tahun Ajaran</strong> & akumulasi Master Bulan (Cut-off tgl 28).
                    </div>
                </div>
            </div>

            <div class="page-title-actions d-flex align-items-center">
                <a href="{{ route('presensi.rekap') }}" class="btn btn-outline-primary bg-white shadow-sm mr-2"
                    style="border-radius: 8px;">
                    <i class="pe-7s-graph2 mr-1"></i> Rekap Bulanan
                </a>
            </div>
        </div>
    </div>

    <!-- FILTER SECTION (TAHUN AJARAN & KATEGORI) -->
    <div class="filter-panel p-3 mb-4 filter-section">
        <form action="{{ route('presensi.statistik-tahunan') }}" method="GET" class="form-row align-items-end">
            <!-- Pilihan Tahun Ajaran (Wajib / Utama) -->
            <div class="col-md-5 mb-2">
                <label class="form-label font-weight-bold text-dark small text-uppercase mb-1">
                    <i class="fa fa-calendar-alt text-primary mr-1"></i> Tahun Ajaran (Akademik Master)
                </label>
                <select name="akademik_master_tahun_ajaran_id" class="form-control shadow-sm" style="border-radius: 8px;" onchange="this.form.submit()">
                    @forelse ($tahunAjaranList as $ta)
                        <option value="{{ $ta->id }}" {{ $selectedTahunAjaranId == $ta->id ? 'selected' : '' }}>
                            {{ $ta->nama_tahun_ajaran }} {{ $ta->semester ? '(' . $ta->semester . ')' : '' }} {{ $ta->is_active ? ' [AKTIF]' : '' }}
                        </option>
                    @empty
                        <option value="">-- Belum ada Tahun Ajaran --</option>
                    @endforelse
                </select>
            </div>

            <!-- Pilihan Kategori Pegawai (Opsional) -->
            <div class="col-md-4 mb-2">
                <label class="form-label font-weight-bold text-dark small text-uppercase mb-1">
                    <i class="fa fa-layer-group text-primary mr-1"></i> Kategori Pegawai
                </label>
                <select name="kepegawaian_master_kategori_id" class="form-control shadow-sm" style="border-radius: 8px;" onchange="this.form.submit()">
                    <option value="">-- Semua Kategori Pegawai --</option>
                    @foreach ($kategoriList as $kat)
                        <option value="{{ $kat->id }}" {{ $selectedKategoriId == $kat->id ? 'selected' : '' }}>
                            {{ $kat->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="col-md-3 mb-2 d-flex">
                <button type="submit" class="btn btn-primary btn-block shadow-sm mr-2" style="border-radius: 8px; font-weight: 600;">
                    <i class="fa fa-filter mr-1"></i> Terapkan
                </button>
                <a href="{{ route('presensi.statistik-tahunan') }}" class="btn btn-light shadow-sm" style="border-radius: 8px;" title="Reset Filter">
                    <i class="fa fa-undo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- EXECUTIVE KPI SUMMARY CARDS -->
    <div class="row mb-4 no-print">
        <!-- Card 1: Rata-rata Tingkat Kehadiran Tahunan -->
        <div class="col-sm-6 col-lg-3 mb-3">
            <div class="stat-card p-3 shadow-sm">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small font-weight-bold text-uppercase">Tingkat Hadir Tahunan</div>
                        <div class="h3 font-weight-bold mb-0 mt-1 {{ ($kpiTahunan['persen_kehadiran'] ?? 0) >= 90 ? 'text-success' : 'text-warning' }}">
                            {{ $kpiTahunan['persen_kehadiran'] ?? 0 }}%
                        </div>
                        <div class="small text-muted mt-1">
                            <strong>{{ number_format($kpiTahunan['total_hadir_efektif'] ?? 0) }}</strong> hadir & terlambat
                        </div>
                    </div>
                    <div class="stat-icon" style="background: #ecfdf5; color: #10b981;">
                        <i class="fa fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Hadir Tepat Waktu -->
        <div class="col-sm-6 col-lg-3 mb-3">
            <div class="stat-card p-3 shadow-sm">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small font-weight-bold text-uppercase">Hadir Tepat Waktu</div>
                        <div class="h3 font-weight-bold text-success mb-0 mt-1">
                            {{ number_format($kpiTahunan['total_hadir'] ?? 0) }}
                        </div>
                        <div class="small text-muted mt-1">
                            <span class="text-warning font-weight-bold">{{ number_format($kpiTahunan['total_terlambat'] ?? 0) }}</span> terlambat
                        </div>
                    </div>
                    <div class="stat-icon" style="background: #ecfdf5; color: #059669;">
                        <i class="fa fa-user-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Keterlambatan & Menit -->
        <div class="col-sm-6 col-lg-3 mb-3">
            <div class="stat-card p-3 shadow-sm">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small font-weight-bold text-uppercase">Keterlambatan</div>
                        <div class="h3 font-weight-bold text-warning mb-0 mt-1">
                            {{ number_format($kpiTahunan['total_terlambat'] ?? 0) }} <small class="text-muted" style="font-size: 14px;">kali</small>
                        </div>
                        <div class="small text-muted mt-1">
                            <strong class="text-danger">{{ number_format($kpiTahunan['total_menit_terlambat'] ?? 0) }}</strong> menit telat
                            @if (($kpiTahunan['total_terlambat_izin'] ?? 0) > 0)
                                &bull; <span class="text-info">{{ $kpiTahunan['total_terlambat_izin'] }} izin</span>
                            @endif
                        </div>
                    </div>
                    <div class="stat-icon" style="background: #fffbeb; color: #f59e0b;">
                        <i class="fa fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4: Total Ketidakhadiran (Izin, Sakit, Cuti, Alpa) -->
        <div class="col-sm-6 col-lg-3 mb-3">
            <div class="stat-card p-3 shadow-sm">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small font-weight-bold text-uppercase">Ketidakhadiran</div>
                        <div class="h3 font-weight-bold text-danger mb-0 mt-1">
                            {{ number_format($kpiTahunan['total_tidak_hadir'] ?? 0) }} <small class="text-muted" style="font-size: 14px;">hari</small>
                        </div>
                        <div class="small text-muted mt-1">
                            {{ $kpiTahunan['total_izin'] ?? 0 }} izin &bull; 
                            {{ $kpiTahunan['total_sakit'] ?? 0 }} sakit &bull; 
                            <span class="text-danger font-weight-bold">{{ $kpiTahunan['total_alpa'] ?? 0 }} alpa</span>
                        </div>
                    </div>
                    <div class="stat-icon" style="background: #fef2f2; color: #ef4444;">
                        <i class="fa fa-user-times"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- VISUAL CHARTS ROW -->
    <div class="row mb-4 no-print">
        <!-- Chart 1: Tren Presensi Bulanan (Bar/Line Combo) -->
        <div class="col-lg-8 mb-3">
            <div class="card shadow-sm border-0" style="border-radius: 14px;">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between border-bottom">
                    <div>
                        <h6 class="font-weight-bold text-dark mb-0">
                            <i class="fa fa-chart-line text-primary mr-2"></i> Tren Kehadiran per Master Bulan
                        </h6>
                        <small class="text-muted">Perbandingan kehadiran tepat waktu, tugas dinas, terlambat, dan ketidakhadiran</small>
                    </div>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 310px; width: 100%;">
                        <canvas id="monthlyTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart 2: Proporsi Status Presensi Tahunan (Donut) -->
        <div class="col-lg-4 mb-3">
            <div class="card shadow-sm border-0" style="border-radius: 14px;">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="font-weight-bold text-dark mb-0">
                        <i class="fa fa-chart-pie text-info mr-2"></i> Distribusi Status Presensi
                    </h6>
                    <small class="text-muted">Komposisi seluruh kehadiran selama tahun ajaran</small>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 260px; width: 100%;">
                        <canvas id="statusDonutChart"></canvas>
                    </div>
                    <div class="mt-2 text-center small text-muted">
                        Total Log Terakumulasi: <strong>{{ number_format($kpiTahunan['total_log'] ?? 0) }}</strong> entri
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- HRD EARLY WARNING & INSIGHTS (3 CARDS) -->
    <div class="row mb-4 no-print">
        <!-- Top Disiplin / Teladan -->
        <div class="col-lg-4 mb-3">
            <div class="card shadow-sm border-0" style="border-radius: 14px;">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="font-weight-bold text-success mb-0">
                            <i class="fa fa-medal mr-1"></i> Top 5 Pegawai Paling Disiplin
                        </h6>
                        <small class="text-muted">Tingkat kehadiran terbaik & tanpa alpa</small>
                    </div>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse ($topDisiplin as $p)
                            <li class="list-group-item d-flex align-items-center justify-content-between px-3 py-2">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle mr-2 bg-success text-white" style="width: 32px; height: 32px; font-size: 12px;">
                                        {{ strtoupper(substr($p->nama_lengkap, 0, 1)) }}
                                    </div>
                                    <div style="line-height: 1.2;">
                                        <div class="font-weight-bold text-dark small text-truncate" style="max-width: 170px;">
                                            {{ $p->nama_lengkap }}
                                        </div>
                                        <small class="text-muted">{{ $p->kategori?->nama_kategori ?: $p->nipy }}</small>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="badge badge-soft-success font-weight-bold">{{ $p->persentase_kehadiran }}%</span>
                                    <div class="text-muted" style="font-size: 10px;">{{ $p->total_hadir_efektif }} hadir</div>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted py-4 small">
                                Belum ada data presensi yang memenuhi kriteria.
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <!-- Top Keterlambatan -->
        <div class="col-lg-4 mb-3">
            <div class="card shadow-sm border-0" style="border-radius: 14px;">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="font-weight-bold text-warning mb-0">
                            <i class="fa fa-exclamation-triangle mr-1"></i> Menit Terlambat Tertinggi
                        </h6>
                        <small class="text-muted">Perlu evaluasi ketepatan waktu HRD</small>
                    </div>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse ($topTerlambat as $p)
                            <li class="list-group-item d-flex align-items-center justify-content-between px-3 py-2">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle mr-2 bg-warning text-dark font-weight-bold" style="width: 32px; height: 32px; font-size: 12px;">
                                        {{ strtoupper(substr($p->nama_lengkap, 0, 1)) }}
                                    </div>
                                    <div style="line-height: 1.2;">
                                        <div class="font-weight-bold text-dark small text-truncate" style="max-width: 170px;">
                                            {{ $p->nama_lengkap }}
                                        </div>
                                        <small class="text-muted">{{ $p->kategori?->nama_kategori ?: $p->nipy }}</small>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="badge badge-soft-danger font-weight-bold">{{ $p->akumulasi_keterlambatan_menit }} mnt</span>
                                    <div class="text-muted" style="font-size: 10px;">{{ $p->total_terlambat }}x telat</div>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted py-4 small">
                                Tidak ada pegawai dengan catatan terlambat.
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <!-- Top Alpa / Ketidakhadiran -->
        <div class="col-lg-4 mb-3">
            <div class="card shadow-sm border-0" style="border-radius: 14px;">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="font-weight-bold text-danger mb-0">
                            <i class="fa fa-user-slash mr-1"></i> Alpa Terbanyak (Early Warning)
                        </h6>
                        <small class="text-muted">Tidak hadir tanpa keterangan resmi</small>
                    </div>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse ($topAlpa as $p)
                            <li class="list-group-item d-flex align-items-center justify-content-between px-3 py-2">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle mr-2 bg-danger text-white font-weight-bold" style="width: 32px; height: 32px; font-size: 12px;">
                                        {{ strtoupper(substr($p->nama_lengkap, 0, 1)) }}
                                    </div>
                                    <div style="line-height: 1.2;">
                                        <div class="font-weight-bold text-dark small text-truncate" style="max-width: 170px;">
                                            {{ $p->nama_lengkap }}
                                        </div>
                                        <small class="text-muted">{{ $p->kategori?->nama_kategori ?: $p->nipy }}</small>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="badge badge-soft-danger font-weight-bold">{{ $p->total_alpa }} Hari</span>
                                    <div class="text-muted" style="font-size: 10px;">Rate: {{ $p->persentase_kehadiran }}%</div>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted py-4 small">
                                <i class="fa fa-check text-success mr-1"></i> Tidak ada catatan alpa pada periode ini.
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- BREAKDOWN TABLE PER MASTER BULAN -->
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 14px; overflow: hidden;">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between border-bottom">
            <div>
                <h6 class="font-weight-bold text-dark mb-0">
                    <i class="fa fa-calendar-check text-primary mr-2"></i> Rekapitulasi Kehadiran per Master Bulan
                </h6>
                <small class="text-muted">Rangkuman kehadiran bulanan pada Tahun Ajaran <strong>{{ $tahunAjaran?->nama_tahun_ajaran ?? '-' }}</strong> (Cut-off tgl 28)</small>
            </div>
        </div>

        <div class="table-responsive">
            <table class="modern-table text-center">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th style="text-align: left; min-width: 150px;">Bulan Periode</th>
                        <th style="min-width: 80px;">Pegawai</th>
                        <th style="min-width: 75px;">Hadir</th>
                        <th style="min-width: 90px;">Terlambat</th>
                        <th style="min-width: 65px;">Izin</th>
                        <th style="min-width: 65px;">Sakit</th>
                        <th style="min-width: 65px;">Cuti</th>
                        <th style="min-width: 65px;">Alpa</th>
                        <th style="min-width: 90px;">Plg Cepat</th>
                        <th style="min-width: 95px;">Menit Telat</th>
                        <th style="min-width: 80px;">% Hadir</th>
                        <th style="min-width: 100px;" class="no-print">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($breakdownBulanan as $idx => $b)
                        <tr>
                            <td class="text-muted font-weight-bold">{{ $idx + 1 }}</td>
                            <td class="text-left font-weight-bold text-dark">
                                {{ $b->nama_bulan }}
                                @if ($b->status_bulan)
                                    <span class="badge badge-success ml-1 font-weight-normal" style="font-size: 10px;">Aktif</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-light border">{{ $b->total_pegawai }}</span>
                            </td>
                            <td>
                                <span class="badge badge-soft-success font-weight-bold px-2 py-1">{{ $b->sum_hadir }}</span>
                            </td>
                            <td>
                                <span class="badge badge-soft-warning font-weight-bold px-2 py-1">{{ $b->sum_terlambat }}</span>
                                @if ($b->sum_terlambat_izin > 0)
                                    <div class="text-muted" style="font-size: 10px;">{{ $b->sum_terlambat_izin }} izin</div>
                                @endif
                            </td>
                            <td><span class="badge badge-soft-info px-2 py-1">{{ $b->sum_izin }}</span></td>
                            <td><span class="badge badge-soft-secondary px-2 py-1">{{ $b->sum_sakit }}</span></td>
                            <td><span class="badge badge-soft-primary px-2 py-1">{{ $b->sum_cuti }}</span></td>
                            <td>
                                @if ($b->sum_alpa > 0)
                                    <span class="badge badge-soft-danger font-weight-bold px-2 py-1">{{ $b->sum_alpa }}</span>
                                @else
                                    <span class="text-muted" style="font-size: 11px;">0</span>
                                @endif
                            </td>
                            <td>
                                @if ($b->sum_pulang_cepat > 0)
                                    <span class="badge badge-soft-warning px-2 py-1">{{ $b->sum_pulang_cepat }}x</span>
                                @else
                                    <span class="text-muted" style="font-size: 11px;">-</span>
                                @endif
                            </td>
                            <td>
                                @if ($b->sum_menit_terlambat > 0)
                                    <span class="text-danger font-weight-bold">{{ $b->sum_menit_terlambat }} <small>mnt</small></span>
                                @else
                                    <span class="text-muted" style="font-size: 11px;">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="font-weight-bold {{ $b->rate_kehadiran >= 90 ? 'text-success' : 'text-warning' }}">
                                    {{ $b->rate_kehadiran }}%
                                </div>
                            </td>
                            <td class="no-print">
                                <a href="{{ route('presensi.rekap', ['bulan_id' => $b->bulan_id, 'kepegawaian_master_kategori_id' => $selectedKategoriId]) }}"
                                    class="btn btn-sm btn-outline-primary" style="border-radius: 6px; font-size: 11px;">
                                    <i class="fa fa-eye mr-1"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="text-center text-muted py-5">
                                Belum ada Master Bulan yang terdaftar untuk Tahun Ajaran ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if (count($breakdownBulanan) > 0)
                    <tfoot>
                        <tr>
                            <td colspan="2" class="text-left font-weight-bold text-dark">TOTAL AKUMULASI</td>
                            <td>{{ ($kpiTahunan['total_log'] ?? 0) > 0 ? count($rekapPegawai) : 0 }}</td>
                            <td class="text-success">{{ number_format($kpiTahunan['total_hadir'] ?? 0) }}</td>
                            <td class="text-warning">{{ number_format($kpiTahunan['total_terlambat'] ?? 0) }}</td>
                            <td>{{ number_format($kpiTahunan['total_izin'] ?? 0) }}</td>
                            <td>{{ number_format($kpiTahunan['total_sakit'] ?? 0) }}</td>
                            <td>{{ number_format($kpiTahunan['total_cuti'] ?? 0) }}</td>
                            <td class="text-danger">{{ number_format($kpiTahunan['total_alpa'] ?? 0) }}</td>
                            <td>{{ number_format($kpiTahunan['total_pulang_cepat'] ?? 0) }}x</td>
                            <td class="text-danger">{{ number_format($kpiTahunan['total_menit_terlambat'] ?? 0) }} mnt</td>
                            <td class="text-success font-weight-bold">{{ $kpiTahunan['persen_kehadiran'] ?? 0 }}%</td>
                            <td class="no-print">-</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    <!-- COMPREHENSIVE EMPLOYEE ANNUAL RECAP TABLE -->
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 14px; overflow: hidden;">
        <div class="card-header bg-white py-3 d-flex flex-wrap align-items-center justify-content-between border-bottom">
            <div>
                <h6 class="font-weight-bold text-dark mb-0">
                    <i class="fa fa-users text-primary mr-2"></i> Rekapitulasi Tahunan Seluruh Pegawai
                </h6>
                <small class="text-muted">Total kehadiran individual sepanjang periode Tahun Ajaran ini</small>
            </div>
            <div class="d-flex align-items-center mt-2 mt-sm-0">
                <div class="input-group input-group-sm mr-2" style="width: 250px;">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-light border-right-0"><i class="fa fa-search text-muted"></i></span>
                    </div>
                    <input type="text" id="pegawaiSearchInput" class="form-control border-left-0"
                        placeholder="Cari Nama / NIPY..." onkeyup="filterPegawaiTable()">
                </div>
                <div class="small text-muted">
                    Menampilkan <strong id="visiblePegawaiCount">{{ count($rekapPegawai) }}</strong> pegawai
                </div>
            </div>
        </div>

        <div class="table-responsive custom-scrollbar" style="max-height: 700px;">
            <table class="modern-table text-center" id="pegawaiAnnualTable">
                <thead>
                    <tr>
                        <th style="width: 45px;">No</th>
                        <th style="text-align: left; min-width: 220px;">Pegawai</th>
                        <th style="min-width: 120px;">Kategori</th>
                        <th style="min-width: 70px;">Hadir</th>
                        <th style="min-width: 90px;">Terlambat</th>
                        <th style="min-width: 65px;">Izin</th>
                        <th style="min-width: 65px;">Sakit</th>
                        <th style="min-width: 65px;">Cuti</th>
                        <th style="min-width: 65px;">Alpa</th>
                        <th style="min-width: 95px;">Menit Telat</th>
                        <th style="min-width: 90px;">Plg Cepat</th>
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

                    @forelse ($rekapPegawai as $idx => $pegawai)
                        @php
                            $initial = strtoupper(substr($pegawai->nama_lengkap ?? 'P', 0, 1));
                            $gradientBg = $avatarGradients[$idx % count($avatarGradients)];
                            $rate = $pegawai->persentase_kehadiran;
                            $rateColor = $rate >= 90 ? '#10b981' : ($rate >= 75 ? '#f59e0b' : '#ef4444');
                        @endphp
                        <tr class="pegawai-row" data-name="{{ strtolower($pegawai->nama_lengkap) }}" data-nipy="{{ strtolower($pegawai->nipy) }}">
                            <td class="text-muted font-weight-bold">{{ $idx + 1 }}</td>
                            
                            <!-- Profile -->
                            <td class="text-left">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle mr-2 shadow-sm" style="background: {{ $gradientBg }};">
                                        {{ $initial }}
                                    </div>
                                    <div style="line-height: 1.25;">
                                        <div class="font-weight-bold text-dark text-truncate" style="max-width: 220px;" title="{{ $pegawai->nama_lengkap }}">
                                            {{ $pegawai->nama_lengkap }}{{ $pegawai->gelar ? ', ' . $pegawai->gelar : '' }}
                                        </div>
                                        <span class="badge badge-light border text-muted px-2 py-0 mt-1 font-mono" style="font-size: 10.5px;">
                                            {{ $pegawai->nipy }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Kategori -->
                            <td>
                                <span class="badge badge-soft-secondary font-weight-normal px-2 py-1" style="border-radius: 6px;">
                                    {{ $pegawai->kategori?->nama_kategori ?: '-' }}
                                </span>
                            </td>

                            <!-- Hadir -->
                            <td>
                                <span class="badge badge-soft-success font-weight-bold px-2 py-1" style="min-width: 32px; border-radius: 6px;">
                                    {{ $pegawai->total_hadir }}
                                </span>
                            </td>

                            <!-- Terlambat -->
                            <td>
                                <span class="badge badge-soft-warning font-weight-bold px-2 py-1" style="min-width: 32px; border-radius: 6px;">
                                    {{ $pegawai->total_terlambat }}
                                </span>
                                @if ($pegawai->total_terlambat_izin > 0)
                                    <div class="text-muted mt-1" style="font-size: 10px;">
                                        <i class="fa fa-check text-info"></i> {{ $pegawai->total_terlambat_izin }} izin
                                    </div>
                                @endif
                            </td>

                            <!-- Izin -->
                            <td>
                                <span class="badge badge-soft-info font-weight-bold px-2 py-1" style="min-width: 28px; border-radius: 6px;">
                                    {{ $pegawai->total_izin }}
                                </span>
                            </td>

                            <!-- Sakit -->
                            <td>
                                <span class="badge badge-soft-secondary font-weight-bold px-2 py-1" style="min-width: 28px; border-radius: 6px;">
                                    {{ $pegawai->total_sakit }}
                                </span>
                            </td>

                            <!-- Cuti -->
                            <td>
                                <span class="badge badge-soft-primary font-weight-bold px-2 py-1" style="min-width: 28px; border-radius: 6px;">
                                    {{ $pegawai->total_cuti }}
                                </span>
                            </td>

                            <!-- Alpa -->
                            <td>
                                @if ($pegawai->total_alpa > 0)
                                    <span class="badge badge-soft-danger font-weight-bold px-2 py-1" style="min-width: 28px; border-radius: 6px;">
                                        {{ $pegawai->total_alpa }}
                                    </span>
                                @else
                                    <span class="text-muted" style="font-size: 11px;">0</span>
                                @endif
                            </td>

                            <!-- Menit Terlambat Murni -->
                            <td>
                                @if (($pegawai->akumulasi_keterlambatan_menit ?? 0) > 0)
                                    <span class="font-weight-bold text-danger">
                                        {{ $pegawai->akumulasi_keterlambatan_menit }} <small>mnt</small>
                                    </span>
                                @else
                                    <span class="text-muted" style="font-size: 11px;">-</span>
                                @endif
                            </td>

                            <!-- Pulang Cepat -->
                            <td>
                                @if ($pegawai->total_pulang_cepat > 0)
                                    <span class="badge badge-soft-warning font-weight-bold px-2 py-1" style="border-radius: 6px;">
                                        {{ $pegawai->total_pulang_cepat }}x
                                    </span>
                                    @if ($pegawai->akumulasi_pulang_cepat_menit > 0)
                                        <div class="text-muted mt-1" style="font-size: 10px;">
                                            {{ $pegawai->akumulasi_pulang_cepat_menit }} mnt
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
                            <td colspan="12" class="text-center text-muted py-5">
                                Belum ada data presensi pegawai untuk Tahun Ajaran ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Include Chart.js (CDN) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
    // Live Search Table Pegawai
    function filterPegawaiTable() {
        var input = document.getElementById('pegawaiSearchInput');
        var filter = input.value.toLowerCase().trim();
        var rows = document.querySelectorAll('.pegawai-row');
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

        var countElem = document.getElementById('visiblePegawaiCount');
        if (countElem) {
            countElem.innerText = visibleCount;
        }
    }

    // Chart.js Rendering
    document.addEventListener('DOMContentLoaded', function () {
        const chartLabels = {{ Illuminate\Support\Js::from($chartData['labels'] ?? []) }};
        const hadirData = {{ Illuminate\Support\Js::from($chartData['hadir'] ?? []) }};
        const terlambatData = {{ Illuminate\Support\Js::from($chartData['terlambat'] ?? []) }};
        const tidakHadirData = {{ Illuminate\Support\Js::from($chartData['tidakHadir'] ?? []) }};
        const donutData = {{ Illuminate\Support\Js::from($chartData['donutData'] ?? [0, 0, 0, 0, 0, 0]) }};

        // 1. Monthly Trend Bar/Line Combo Chart
        var trendCtx = document.getElementById('monthlyTrendChart');
        if (trendCtx && chartLabels.length > 0) {
            new Chart(trendCtx, {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [
                        {
                            label: 'Hadir Tepat Waktu',
                            data: hadirData,
                            backgroundColor: '#10b981',
                            borderRadius: 6,
                            stack: 'kehadiran'
                        },
                        {
                            label: 'Terlambat',
                            data: terlambatData,
                            backgroundColor: '#f59e0b',
                            borderRadius: 6,
                            stack: 'kehadiran'
                        },
                        {
                            label: 'Tidak Hadir (Izin/Sakit/Cuti/Alpa)',
                            data: tidakHadirData,
                            backgroundColor: '#ef4444',
                            borderRadius: 6,
                            stack: 'kehadiran'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
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
                            boxPadding: 4
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11 } }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f1f5f9' },
                            ticks: { font: { size: 11 } }
                        }
                    }
                }
            });
        }

        // 2. Status Donut Chart (Hadir, Terlambat, Izin, Sakit, Cuti, Alpa)
        var donutCtx = document.getElementById('statusDonutChart');
        if (donutCtx) {
            new Chart(donutCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Hadir Tepat Waktu', 'Terlambat', 'Izin', 'Sakit', 'Cuti', 'Alpa'],
                    datasets: [{
                        data: donutData,
                        backgroundColor: [
                            '#10b981', // Hadir (Emerald)
                            '#f59e0b', // Terlambat (Amber)
                            '#0ea5e9', // Izin (Sky)
                            '#64748b', // Sakit (Slate)
                            '#8b5cf6', // Cuti (Purple)
                            '#ef4444'  // Alpa (Red)
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
                            position: 'bottom',
                            labels: {
                                boxWidth: 10,
                                font: { size: 10.5 },
                                padding: 10
                            }
                        }
                    },
                    cutout: '65%'
                }
            });
        }
    });
</script>
@endsection
