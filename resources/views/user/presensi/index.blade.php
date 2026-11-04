@extends('layouts.appui')

@section('title')
    <title>Presensi Saya - {{ $karyawan->nama_lengkap ?? Auth::user()->name }} | SIPIT PION</title>
@endsection

@section('content')
<div class="user-presensi-wrapper">

    {{-- =========================================================================
         1. MODERN HEADER & FILTER PERIODE
         ========================================================================= --}}
    <div class="up-hero-card">
        <div class="up-hero-card__decor-1"></div>
        <div class="up-hero-card__decor-2"></div>
        <div class="up-hero-card__content">
            <div class="up-hero-card__left">
                <div class="up-hero-icon">
                    <i class="pe-7s-date"></i>
                </div>
                <div>
                    <h1 class="up-hero-title">Presensi Kehadiran</h1>
                    <p class="up-hero-desc">
                        {{ $karyawan->nama_lengkap ?? Auth::user()->name }}
                        @if ($karyawan?->unit)
                            &bull; <span class="up-hero-unit">{{ $karyawan->unit }}</span>
                        @endif
                    </p>

                    <div class="up-hero-metrics-bar">
                        <div class="up-metric-badge up-metric-badge--hadir" title="Persentase Hadir Tepat Waktu">
                            <span class="up-metric-dot bg-emerald"></span>
                            <span class="up-metric-label">Hadir:</span>
                            <strong class="up-metric-value">{{ $persentaseHadir }}%</strong>
                        </div>
                        <div class="up-metric-badge up-metric-badge--terlambat" title="Persentase Keterlambatan">
                            <span class="up-metric-dot bg-amber"></span>
                            <span class="up-metric-label">Terlambat:</span>
                            <strong class="up-metric-value">{{ $persentaseTerlambat }}%</strong>
                        </div>
                        <div class="up-metric-badge up-metric-badge--tidakhadir" title="Persentase Ketidakhadiran (Izin, Sakit, Cuti, & Alfa)">
                            <span class="up-metric-dot bg-rose"></span>
                            <span class="up-metric-label">Tidak Hadir:</span>
                            <strong class="up-metric-value">{{ $persentaseTidakHadir }}%</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Filter Periode (Bulan & Tahun) -->
            <form method="GET" action="{{ route('user.presensi.index') }}" class="up-period-filter">
                <div class="up-select-wrapper">
                    <select name="bulan" onchange="this.form.submit()" title="Pilih Bulan">
                        @foreach ($bulanOptions as $num => $namaBulan)
                            <option value="{{ $num }}" {{ $selectedMonth == $num ? 'selected' : '' }}>
                                {{ $namaBulan }}
                            </option>
                        @endforeach
                    </select>
                    <i class="fa fa-chevron-down up-select-icon"></i>
                </div>

                <div class="up-select-wrapper" style="min-width: 95px;">
                    <select name="tahun" onchange="this.form.submit()" title="Pilih Tahun">
                        @for ($y = Carbon\Carbon::now()->year; $y >= Carbon\Carbon::now()->year - 3; $y--)
                            <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                    <i class="fa fa-chevron-down up-select-icon"></i>
                </div>

                <a href="{{ route('user.presensi.index') }}" class="up-btn-reset" title="Kembali ke Bulan Berjalan">
                    <i class="fa fa-sync-alt"></i>
                </a>
            </form>
        </div>
    </div>

    {{-- =========================================================================
         2. REKAPITULASI STATISTIK BULAN INI (4 SUMMARY CARDS MODERN)
         ========================================================================= --}}
    <div class="up-stats-grid">
        <!-- Card 1: Total Hadir (Emerald Green) -->
        <div class="up-stat-card up-stat-card--hadir">
            <div class="up-stat-card__inner">
                <div>
                    <span class="up-stat-label text-emerald">Total Hadir</span>
                    <div class="up-stat-num-row">
                        <span class="up-stat-number">{{ $totalHadir }}</span>
                        <span class="up-stat-percentage up-stat-percentage--emerald" title="Persentase Kehadiran Tepat Waktu">
                            {{ $persentaseHadir }}%
                        </span>
                    </div>
                </div>
                <div class="up-stat-icon-wrap bg-emerald-gradient">
                    <i class="fa fa-user-check"></i>
                </div>
            </div>
            <div class="up-stat-footer border-emerald-soft">
                <span class="up-stat-tag text-emerald">
                    <i class="fa fa-check-circle"></i> Tepat Waktu &amp; Dinas
                </span>
            </div>
        </div>

        <!-- Card 2: Terlambat (Sunset Amber/Orange) -->
        <div class="up-stat-card up-stat-card--terlambat">
            <div class="up-stat-card__inner">
                <div>
                    <span class="up-stat-label text-amber">Terlambat</span>
                    <div class="up-stat-num-row">
                        <span class="up-stat-number">{{ $totalTerlambat }}</span>
                    </div>
                </div>
                <div class="up-stat-icon-wrap bg-amber-gradient">
                    <i class="fa fa-clock"></i>
                </div>
            </div>
            <div class="up-stat-footer border-amber-soft">
                <span class="up-stat-tag text-amber">
                    <i class="fa fa-exclamation-circle"></i> Lewat Jam Masuk
                </span>
            </div>
        </div>

        <!-- Card 3: Izin & Sakit (Sky Blue) -->
        <div class="up-stat-card up-stat-card--izin">
            <div class="up-stat-card__inner">
                <div>
                    <span class="up-stat-label text-sky">Izin &amp; Sakit</span>
                    <div class="up-stat-num-row">
                        <span class="up-stat-number">{{ $totalIzinSakit }}</span>
                        <span class="up-stat-percentage up-stat-percentage--sky" title="Izin, Sakit, & Cuti">
                            {{ $totalHariKerja > 0 ? round(($totalIzinSakit / $totalHariKerja) * 100, 1) : 0 }}%
                        </span>
                    </div>
                </div>
                <div class="up-stat-icon-wrap bg-sky-gradient">
                    <i class="fa fa-notes-medical"></i>
                </div>
            </div>
            <div class="up-stat-footer border-sky-soft">
                <span class="up-stat-tag text-sky">
                    <i class="fa fa-file-alt"></i> Izin, Sakit &amp; Cuti
                </span>
            </div>
        </div>

        <!-- Card 4: Alfa & Ketidakhadiran (Rose Red) -->
        <div class="up-stat-card up-stat-card--alfa">
            <div class="up-stat-card__inner">
                <div>
                    <span class="up-stat-label text-rose">Total Alfa</span>
                    <div class="up-stat-num-row">
                        <span class="up-stat-number">{{ $totalAlfa }}</span>
                        <span class="up-stat-percentage up-stat-percentage--rose" title="Persentase Ketidakhadiran Keseluruhan">
                            {{ $persentaseTidakHadir }}%
                        </span>
                    </div>
                </div>
                <div class="up-stat-icon-wrap bg-rose-gradient">
                    <i class="fa fa-user-times"></i>
                </div>
            </div>
            <div class="up-stat-footer border-rose-soft">
                <span class="up-stat-tag text-rose">
                    <i class="fa fa-times-circle"></i> Tanpa Keterangan
                </span>
            </div>
        </div>
    </div>

    {{-- =========================================================================
         3. DAFTAR RIWAYAT PRESENSI BULAN INI (LENGKAP TANPA PAGINASI)
         ========================================================================= --}}
    <div class="up-main-card">
        <!-- Panel Header: Judul & Filter Status Tabs -->
        <div class="up-main-card__head">
            <div>
                <div class="up-main-card__title-row">
                    <div class="up-calendar-badge">
                        <i class="fa fa-calendar-alt"></i>
                    </div>
                    <div>
                        <h2 class="up-main-card__title">Riwayat Presensi Harian</h2>
                        <p class="up-main-card__subtitle">
                            Periode: <strong>{{ $bulanOptions[$selectedMonth] }} {{ $selectedYear }}</strong>
                            &bull; <strong>{{ $riwayatPresensi->count() }}</strong> hari kerja tercatat
                        </p>
                    </div>
                </div>
            </div>

            <!-- Segmented Quick Filter Status Pills -->
            <div class="up-tabs-pills">
                <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}"
                    class="up-tab-pill {{ !request('status') ? 'is-active' : '' }}">
                    Semua ({{ $riwayatPresensi->count() }})
                </a>
                <a href="{{ request()->fullUrlWithQuery(['status' => 'hadir']) }}"
                    class="up-tab-pill {{ request('status') === 'hadir' ? 'is-active' : '' }}">
                    <span class="up-tab-dot bg-emerald"></span> Hadir
                </a>
                <a href="{{ request()->fullUrlWithQuery(['status' => 'terlambat']) }}"
                    class="up-tab-pill {{ request('status') === 'terlambat' ? 'is-active' : '' }}">
                    <span class="up-tab-dot bg-amber"></span> Terlambat
                </a>
                <a href="{{ request()->fullUrlWithQuery(['status' => 'alpa']) }}"
                    class="up-tab-pill {{ request('status') === 'alpa' ? 'is-active' : '' }}">
                    <span class="up-tab-dot bg-rose"></span> Alfa
                </a>
            </div>
        </div>

        {{-- 
            =======================================================================
            [KOMENTAR RESPONSIVE BEHAVIOR - TAMPILAN MOBILE (< 768px)]
            Breakpoint: < 768px (Layar Smartphone / HP)
            Class CSS: 'up-mobile-feed' (Aktif di layar sempit, disembunyikan di Tablet & Desktop)

            ALASAN DESAIN:
            Di layar HP, data disajikan sebagai Card List vertikal ergonomis sehingga
            mudah dibaca satu tangan dan tidak memerlukan horizontal scrolling.
            =======================================================================
        --}}
        <div class="up-mobile-feed">
            @forelse ($riwayatPresensi as $item)
                @php
                    $badge = $item->status_badge;
                    $isToday = $item->tanggal->isToday();
                @endphp
                <div class="up-mobile-card {{ $isToday ? 'is-today' : '' }}">
                    <!-- Baris Atas: Tanggal & Badge Status -->
                    <div class="up-mobile-card__top">
                        <div class="up-mobile-date">
                            <span class="up-mobile-day-name">{{ $item->tanggal->translatedFormat('l') }}</span>
                            <span class="up-mobile-full-date">{{ $item->tanggal->translatedFormat('d M Y') }}</span>
                            @if ($isToday)
                                <span class="up-today-pill">Hari Ini</span>
                            @endif
                        </div>
                        <div class="up-mobile-badges">
                            <span class="up-status-badge" style="--bg: {{ $badge['color'] }}15; --color: {{ $badge['color'] }}; --border: {{ $badge['color'] }}35;">
                                {{ $badge['label'] }}
                            </span>
                            @if ($item->shaum)
                                <span class="up-shaum-pill" title="Melaksanakan Ibadah Shaum">
                                    <i class="fa fa-moon"></i>
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Baris Tengah: Jam Masuk, Jam Pulang & Durasi -->
                    <div class="up-mobile-time-grid">
                        <div class="up-time-box">
                            <span class="up-time-label">Masuk</span>
                            <span class="up-time-val {{ $item->status === 'terlambat' ? 'text-late' : '' }}">
                                {{ $item->jam_masuk ? substr($item->jam_masuk, 0, 5) : '-' }}
                            </span>
                        </div>
                        <div class="up-time-box">
                            <span class="up-time-label">Pulang</span>
                            <span class="up-time-val">
                                {{ $item->jam_pulang ? substr($item->jam_pulang, 0, 5) : ($item->jam_masuk ? '...' : '-') }}
                            </span>
                        </div>
                        <div class="up-time-box">
                            <span class="up-time-label">Durasi</span>
                            <span class="up-time-val up-time-val--duration">
                                {{ $item->durasi_kerja ?: '-' }}
                            </span>
                        </div>
                    </div>

                    <!-- Baris Bawah: Shift Kerja & Catatan Tambahan -->
                    <div class="up-mobile-card__bottom">
                        <span class="up-shift-name">
                            <i class="fa fa-briefcase"></i>
                            {{ $item->jamKerja?->nama ?? 'Reguler' }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="up-empty-state">
                    <div class="up-empty-icon">
                        <i class="pe-7s-date"></i>
                    </div>
                    <h3>Belum Ada Rekaman Presensi</h3>
                    <p>Tidak ditemukan data kehadiran untuk periode {{ $bulanOptions[$selectedMonth] }} {{ $selectedYear }}.</p>
                </div>
            @endforelse
        </div>

        {{-- 
            =======================================================================
            [KOMENTAR RESPONSIVE BEHAVIOR - TAMPILAN TABLET & PC (>= 768px)]
            Breakpoint: >= 768px (Layar Tablet, Laptop, dan Desktop)
            Class CSS: 'up-desktop-table-wrapper' (Tampil di Tablet/PC, disembunyikan di HP)

            ALASAN DESAIN:
            Di layar lebar, data ditata dalam Modern SaaS Table yang leluasa, lengkap
            dengan highlight baris hari ini dan indikator status visual terpadu.
            =======================================================================
        --}}
        <div class="up-desktop-table-wrapper">
            <table class="up-table">
                <thead>
                    <tr>
                        <th class="width: 10%">No</th>
                        <th style="width: 28%;">Hari &amp; Tanggal</th>
                        <th style="width: 17%;">Jadwal Shift</th>
                        <th class="text-center" style="width: 13%;">Jam Masuk</th>
                        <th class="text-center" style="width: 13%;">Jam Pulang</th>
                        <th class="text-center" style="width: 13%;">Durasi Kerja</th>
                        <th style="width: 16%;">Status Kehadiran</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($riwayatPresensi as $index => $item)
                        @php
                            $badge = $item->status_badge;
                            $isToday = $item->tanggal->isToday();
                        @endphp
                        <tr class="{{ $isToday ? 'is-today' : '' }}">
                            <!-- Kolom 1: No Urut (Persempit Ringkas) -->
                            <td class="up-col-num">
                                {{ $index + 1 }}
                            </td>

                            <!-- Kolom 2: Hari & Tanggal -->
                            <td>
                                <div class="up-table-date-cell">
                                    <span class="up-table-day-name">{{ $item->tanggal->translatedFormat('l') }}</span>
                                    <span class="up-table-full-date">{{ $item->tanggal->translatedFormat('d F Y') }}</span>
                                    @if ($isToday)
                                        <span class="up-today-pill">Hari Ini</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Kolom 3: Jadwal Shift Kerja -->
                            <td>
                                <div class="up-table-shift-cell">
                                    <i class="fa fa-briefcase"></i>
                                    <span>{{ $item->jamKerja?->nama ?? 'Reguler' }}</span>
                                </div>
                            </td>

                            <!-- Kolom 4: Jam Masuk -->
                            <td class="text-center">
                                @if ($item->jam_masuk)
                                    <div class="up-time-badge {{ $item->status === 'terlambat' ? 'is-late' : 'is-ontime' }}">
                                        @if ($item->status === 'terlambat')
                                            <i class="fa fa-exclamation-triangle text-amber"></i>
                                        @else
                                            <span class="up-dot-ontime"></span>
                                        @endif
                                        <span>{{ substr($item->jam_masuk, 0, 5) }}</span>
                                    </div>
                                @else
                                    <span class="up-empty-dash">-</span>
                                @endif
                            </td>

                            <!-- Kolom 5: Jam Pulang -->
                            <td class="text-center">
                                @if ($item->jam_pulang)
                                    <div class="up-time-badge">
                                        <span>{{ substr($item->jam_pulang, 0, 5) }}</span>
                                    </div>
                                @elseif ($item->jam_masuk)
                                    <span class="up-badge-pending">
                                        <i class="fa fa-hourglass-half"></i> Belum Checkout
                                    </span>
                                @else
                                    <span class="up-empty-dash">-</span>
                                @endif
                            </td>

                            <!-- Kolom 6: Durasi Kerja Efektif -->
                            <td class="text-center">
                                @if ($item->durasi_kerja && $item->durasi_kerja !== '-')
                                    <span class="up-duration-pill">
                                        <i class="fa fa-stopwatch"></i> {{ $item->durasi_kerja }}
                                    </span>
                                @else
                                    <span class="up-empty-dash">-</span>
                                @endif
                            </td>

                            <!-- Kolom 7: Status Kehadiran Modern Badge -->
                            <td>
                                <div class="up-table-status-cell">
                                    <div class="up-status-badges-group">
                                        <span class="up-status-badge" style="--bg: {{ $badge['color'] }}15; --color: {{ $badge['color'] }}; --border: {{ $badge['color'] }}35;">
                                            {{ $badge['label'] }}
                                        </span>
                                        @if ($item->shaum)
                                            <span class="up-shaum-pill" title="Melaksanakan Ibadah Shaum">
                                                <i class="fa fa-moon"></i> Shaum
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">
                                <div class="up-empty-state">
                                    <div class="up-empty-icon">
                                        <i class="pe-7s-date"></i>
                                    </div>
                                    <h3>Belum Ada Rekaman Presensi</h3>
                                    <p>Tidak ditemukan data kehadiran untuk periode {{ $bulanOptions[$selectedMonth] }} {{ $selectedYear }}.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer Ringkasan (Tanpa Paginasi) -->
        <div class="up-main-card__foot">
            <div class="up-foot-info">
                <i class="fa fa-chart-pie text-emerald"></i>
                <span>Menampilkan seluruh <strong>{{ $riwayatPresensi->count() }}</strong> rekaman presensi periode <strong>{{ $bulanOptions[$selectedMonth] }} {{ $selectedYear }}</strong></span>
            </div>
            <div class="up-foot-legend">
                <span class="up-legend-item"><span class="up-tab-dot bg-emerald"></span> Hadir: <strong>{{ $persentaseHadir }}%</strong></span>
                <span class="up-legend-item"><span class="up-tab-dot bg-amber"></span> Terlambat: <strong>{{ $persentaseTerlambat }}%</strong></span>
                <span class="up-legend-item"><span class="up-tab-dot bg-rose"></span> Tidak Hadir: <strong>{{ $persentaseTidakHadir }}%</strong></span>
            </div>
        </div>
    </div>

</div>
@endsection

@push('css')
<style>
    /* =========================================================================
       GLOBAL CONTAINER & PAGE LAYOUT
       ========================================================================= */
    .user-presensi-wrapper {
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    /* =========================================================================
       1. HERO HEADER (ELEGANT WARM ORANGE & SLATE)
       ========================================================================= */
    .up-hero-card {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #c2410c 135%);
        border-radius: 20px;
        padding: 24px 28px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 12px 35px -8px rgba(15, 23, 42, 0.25);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .up-hero-card__decor-1 {
        position: absolute;
        right: -40px;
        top: -50px;
        width: 240px;
        height: 240px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(234, 88, 12, 0.35) 0%, rgba(234, 88, 12, 0) 70%);
        pointer-events: none;
    }
    .up-hero-card__decor-2 {
        position: absolute;
        right: 180px;
        bottom: -60px;
        width: 200px;
        height: 200px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(249, 115, 22, 0.25) 0%, rgba(249, 115, 22, 0) 70%);
        pointer-events: none;
    }
    .up-hero-card__content {
        position: relative;
        z-index: 1;
        display: flex;
        flex-direction: column;
        gap: 18px;
    }
    @media (min-width: 768px) {
        .up-hero-card__content {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }
    }

    .up-hero-card__left {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .up-hero-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #ea580c 0%, #f97316 100%);
        box-shadow: 0 8px 20px rgba(234, 88, 12, 0.35);
        border: 2px solid rgba(255, 255, 255, 0.25);
        font-size: 24px;
        color: #ffffff;
        flex-shrink: 0;
    }
    .up-hero-meta {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 4px;
    }
    .up-pill-indicator {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        background: rgba(234, 88, 12, 0.22);
        color: #fed7aa;
        border: 1px solid rgba(253, 186, 116, 0.35);
    }
    .up-pulse-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: #34d399;
        box-shadow: 0 0 8px #34d399;
    }
    .up-nipy-tag {
        font-family: ui-monospace, Menlo, Consolas, monospace;
        font-size: 11px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 6px;
        background: rgba(255, 255, 255, 0.12);
        color: #f1f5f9;
        border: 1px solid rgba(255, 255, 255, 0.18);
    }
    .up-hero-title {
        font-size: 22px;
        font-weight: 800;
        color: #ffffff;
        letter-spacing: -0.02em;
        margin: 0;
        line-height: 1.2;
    }
    .up-hero-desc {
        font-size: 13px;
        color: #cbd5e1;
        margin: 4px 0 0;
        font-weight: 500;
    }
    .up-hero-unit {
        color: #fed7aa;
        font-weight: 700;
    }

    /* Hero Metrics Bar */
    .up-hero-metrics-bar {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 10px;
    }
    .up-metric-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 11.5px;
        backdrop-filter: blur(8px);
        transition: all 0.2s ease;
    }
    .up-metric-badge:hover {
        transform: translateY(-1px);
    }
    .up-metric-badge--hadir {
        background: rgba(16, 185, 129, 0.2);
        color: #a7f3d0;
        border: 1px solid rgba(16, 185, 129, 0.35);
    }
    .up-metric-badge--terlambat {
        background: rgba(245, 158, 11, 0.2);
        color: #fde68a;
        border: 1px solid rgba(245, 158, 11, 0.35);
    }
    .up-metric-badge--tidakhadir {
        background: rgba(244, 63, 94, 0.2);
        color: #fecdd3;
        border: 1px solid rgba(244, 63, 94, 0.35);
    }
    .up-metric-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .up-metric-label {
        font-weight: 500;
        opacity: 0.9;
    }
    .up-metric-value {
        font-weight: 800;
    }

    /* Filter Periode Dropdowns */
    .up-period-filter {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(8px);
        padding: 6px;
        border-radius: 14px;
        border: 1px solid rgba(255, 255, 255, 0.18);
    }
    .up-select-wrapper {
        position: relative;
    }
    .up-select-wrapper select {
        background-color: rgba(255, 255, 255, 0.14);
        color: #ffffff;
        font-size: 12.5px;
        font-weight: 700;
        border-radius: 10px;
        border: 1px solid rgba(255, 255, 255, 0.22);
        padding: 7px 30px 7px 12px;
        appearance: none;
        cursor: pointer;
        outline: none;
        transition: all 0.2s ease;
        width: 100%;
    }
    .up-select-wrapper select:hover,
    .up-select-wrapper select:focus {
        background-color: rgba(255, 255, 255, 0.22);
        border-color: rgba(255, 255, 255, 0.4);
    }
    .up-select-wrapper select option {
        background-color: #1e293b;
        color: #ffffff;
    }
    .up-select-icon {
        position: absolute;
        right: 11px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 10px;
        color: rgba(255, 255, 255, 0.65);
        pointer-events: none;
    }
    .up-btn-reset {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(255, 255, 255, 0.12);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.22);
        font-size: 11.5px;
        text-decoration: none !important;
        transition: all 0.2s ease;
    }
    .up-btn-reset:hover {
        background-color: #ea580c;
        border-color: #ea580c;
        color: #ffffff;
    }

    /* =========================================================================
       2. REKAPITULASI SUMMARY CARDS (4 KOLOM)
       ========================================================================= */
    .up-stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
    @media (min-width: 992px) {
        .up-stats-grid {
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
        }
    }

    .up-stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px 18px;
        box-shadow: 0 4px 16px -2px rgba(15, 23, 42, 0.04);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .up-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 24px -4px rgba(15, 23, 42, 0.08);
    }
    .up-stat-card--hadir     { border-top: 4px solid #10b981; }
    .up-stat-card--terlambat { border-top: 4px solid #f59e0b; }
    .up-stat-card--izin      { border-top: 4px solid #0284c7; }
    .up-stat-card--alfa      { border-top: 4px solid #e11d48; }

    .up-stat-card__inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .up-stat-label {
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .up-stat-number {
        font-size: 28px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
    }
    .up-stat-num-row {
        display: flex;
        align-items: baseline;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 3px;
    }
    .up-stat-percentage {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        font-size: 11px;
        font-weight: 800;
        padding: 2px 7px;
        border-radius: 6px;
        line-height: 1.2;
    }
    .up-stat-percentage--emerald {
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
    }
    .up-stat-percentage--amber {
        background: #fffbeb;
        color: #d97706;
        border: 1px solid #fde68a;
    }
    .up-stat-percentage--sky {
        background: #f0f9ff;
        color: #0284c7;
        border: 1px solid #bae6fd;
    }
    .up-stat-percentage--rose {
        background: #fef2f2;
        color: #e11d48;
        border: 1px solid #fecaca;
    }
    .up-stat-icon-wrap {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
        color: #ffffff;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .up-stat-footer {
        margin-top: 12px;
        padding-top: 10px;
        border-top: 1px solid;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 11px;
    }
    .up-stat-tag {
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .up-stat-period {
        color: #94a3b8;
        font-weight: 500;
    }

    /* Helper Colors */
    .text-emerald { color: #059669; }
    .text-amber   { color: #d97706; }
    .text-sky     { color: #0284c7; }
    .text-rose    { color: #e11d48; }

    .border-emerald-soft { border-color: #ecfdf5; }
    .border-amber-soft   { border-color: #fffbeb; }
    .border-sky-soft     { border-color: #f0f9ff; }
    .border-rose-soft    { border-color: #fef2f2; }

    .bg-emerald-gradient { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .bg-amber-gradient   { background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%); }
    .bg-sky-gradient     { background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); }
    .bg-rose-gradient    { background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%); }

    /* =========================================================================
       3. MAIN CARD CONTAINER (TABLE & FEED)
       ========================================================================= */
    .up-main-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 4px 20px -3px rgba(15, 23, 42, 0.05);
    }
    .up-main-card__head {
        display: flex;
        flex-direction: column;
        gap: 12px;
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        background: #ffffff;
    }
    @media (min-width: 768px) {
        .up-main-card__head {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }
    }

    .up-main-card__title-row {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .up-calendar-badge {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fff7ed;
        color: #ea580c;
        border: 1px solid #ffedd5;
        font-size: 15px;
        flex-shrink: 0;
    }
    .up-main-card__title {
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        line-height: 1.2;
    }
    .up-main-card__subtitle {
        font-size: 12px;
        color: #64748b;
        margin: 2px 0 0;
    }

    /* Tabs Pills */
    .up-tabs-pills {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }
    .up-tab-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        background: #f8fafc;
        color: #475569;
        border: 1px solid #e2e8f0;
        text-decoration: none !important;
        transition: all 0.18s ease;
    }
    .up-tab-pill:hover {
        background: #f1f5f9;
        color: #0f172a;
    }
    .up-tab-pill.is-active {
        background: linear-gradient(135deg, #ea580c 0%, #f97316 100%) !important;
        color: #ffffff !important;
        border-color: #ea580c !important;
        box-shadow: 0 3px 10px rgba(234, 88, 12, 0.25);
    }
    .up-tab-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }
    .bg-emerald { background-color: #10b981; }
    .bg-amber   { background-color: #f59e0b; }
    .bg-rose    { background-color: #f43f5e; }

    /* =========================================================================
       4. MOBILE FEED (< 768px)
       ========================================================================= */
    .up-mobile-feed {
        display: block;
        padding: 14px;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .up-mobile-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 14px;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);
        transition: all 0.18s ease;
    }
    .up-mobile-card:hover {
        border-color: #fdba74;
    }
    .up-mobile-card.is-today {
        border-color: #fdba74;
        background: #fffaf5;
        box-shadow: 0 0 0 2px rgba(234, 88, 12, 0.15);
    }
    .up-mobile-card__top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding-bottom: 10px;
        border-bottom: 1px solid #f1f5f9;
    }
    .up-mobile-date {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }
    .up-mobile-day-name {
        font-size: 11px;
        font-weight: 700;
        color: #ea580c;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .up-mobile-full-date {
        font-size: 13px;
        font-weight: 800;
        color: #0f172a;
    }
    .up-today-pill {
        font-size: 10px;
        font-weight: 800;
        padding: 1px 7px;
        border-radius: 999px;
        background: #ea580c;
        color: #ffffff;
    }
    .up-mobile-badges {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .up-mobile-time-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
        margin: 10px 0;
        padding: 10px;
        background: #f8fafc;
        border-radius: 10px;
        border: 1px solid #f1f5f9;
        text-align: center;
    }
    .up-time-box:not(:last-child) {
        border-right: 1px solid #e2e8f0;
    }
    .up-time-label {
        display: block;
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        color: #94a3b8;
        letter-spacing: 0.3px;
    }
    .up-time-val {
        display: block;
        font-family: ui-monospace, Menlo, Consolas, monospace;
        font-size: 13px;
        font-weight: 800;
        color: #1e293b;
        margin-top: 2px;
    }
    .up-time-val--duration {
        font-family: inherit;
        font-size: 11.5px;
        font-weight: 600;
        color: #475569;
    }
    .text-late {
        color: #dc2626 !important;
    }

    .up-mobile-card__bottom {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 11px;
        color: #64748b;
    }
    .up-shift-name {
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .up-shift-name i {
        color: #ea580c;
        font-size: 10px;
    }
    .up-mobile-note {
        color: #0284c7;
        font-weight: 600;
        max-width: 160px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* =========================================================================
       5. DESKTOP SAAS TABLE (>= 768px)
       ========================================================================= */
    .up-desktop-table-wrapper {
        display: none;
        overflow-x: auto;
    }
    .up-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        text-align: left;
    }
    .up-table thead tr {
        background-color: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }
    .up-table th {
        padding: 13px 18px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        color: #64748b;
        white-space: nowrap;
    }
    .up-table td {
        padding: 13px 18px;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        white-space: nowrap;
    }
    .up-table tbody tr {
        transition: background-color 0.15s ease;
    }
    .up-table tbody tr:hover {
        background-color: #fffaf5;
    }
    .up-table tbody tr.is-today {
        background-color: #fff7ed;
    }

    .up-th-no,
    .up-col-num {
        width: 38px !important;
        max-width: 44px !important;
        min-width: 36px !important;
        padding-left: 6px !important;
        padding-right: 6px !important;
        text-align: center !important;
        font-weight: 700;
        color: #94a3b8;
        font-size: 12px;
    }
    .up-table-date-cell {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .up-table-day-name {
        font-size: 11px;
        font-weight: 700;
        color: #ea580c;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        background: #fff7ed;
        border: 1px solid #ffedd5;
        padding: 2px 7px;
        border-radius: 6px;
    }
    .up-table-full-date {
        font-weight: 800;
        color: #0f172a;
    }

    .up-table-shift-cell {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #475569;
        font-weight: 600;
        font-size: 12px;
    }
    .up-table-shift-cell i {
        color: #ea580c;
        font-size: 11px;
    }

    .up-time-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 8px;
        border-radius: 7px;
        font-family: ui-monospace, Menlo, Consolas, monospace;
        font-size: 12.5px;
        font-weight: 700;
        background: #f1f5f9;
        color: #1e293b;
        border: 1px solid #e2e8f0;
    }
    .up-time-badge.is-ontime {
        background: #ecfdf5;
        color: #065f46;
        border-color: #a7f3d0;
    }
    .up-time-badge.is-late {
        background: #fef2f2;
        color: #b91c1c;
        border-color: #fecaca;
    }
    .up-dot-ontime {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: #10b981;
    }

    .up-badge-pending {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 10.5px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 6px;
        background: #fffbeb;
        color: #b45309;
        border: 1px solid #fde68a;
    }
    .up-duration-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 11.5px;
        font-weight: 600;
        background: #f8fafc;
        color: #475569;
        border: 1px solid #e2e8f0;
    }
    .up-duration-pill i {
        color: #ea580c;
        font-size: 10px;
    }

    /* Badges */
    .up-status-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 11.5px;
        font-weight: 700;
        background-color: var(--bg);
        color: var(--color);
        border: 1px solid var(--border);
        line-height: 1.2;
    }
    .up-shaum-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 10.5px;
        font-weight: 700;
        background: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }
    .up-note-text {
        font-size: 12px;
        color: #475569;
        max-width: 220px;
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .up-empty-dash {
        color: #cbd5e1;
        font-weight: 600;
    }

    /* Responsive Breakpoint: Desktop */
    @media (min-width: 768px) {
        .up-mobile-feed {
            display: none;
        }
        .up-desktop-table-wrapper {
            display: block;
        }
    }

    /* =========================================================================
       6. MAIN CARD FOOTER & EMPTY STATE
       ========================================================================= */
    .up-main-card__foot {
        display: flex;
        flex-direction: column;
        gap: 8px;
        padding: 14px 20px;
        border-top: 1px solid #f1f5f9;
        background: #ffffff;
    }
    @media (min-width: 768px) {
        .up-main-card__foot {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }
    }
    .up-foot-info {
        display: flex;
        align-items: center;
        gap: 7px;
        font-size: 12.5px;
        color: #64748b;
    }
    .up-foot-info strong {
        color: #0f172a;
    }
    .up-foot-legend {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 11.5px;
        color: #64748b;
        flex-wrap: wrap;
    }
    .up-legend-item {
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .up-empty-state {
        text-align: center;
        padding: 40px 16px;
    }
    .up-empty-icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        background: #fff7ed;
        border: 1px solid #ffedd5;
        color: #ea580c;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        margin: 0 auto 12px;
    }
    .up-empty-state h3 {
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 4px;
    }
    .up-empty-state p {
        font-size: 12.5px;
        color: #94a3b8;
        margin: 0;
    }
</style>
@endpush
