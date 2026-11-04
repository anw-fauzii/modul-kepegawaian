@extends('layouts.appui')

@section('title')
    <title>Slip Gaji Saya - {{ $karyawan->nama_lengkap ?? Auth::user()->name }} | SIPIT PION</title>
@endsection

@section('content')
@php
    $formatRp = fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
@endphp

<div class="user-presensi-wrapper user-penggajian-wrapper">

    {{-- =========================================================================
         1. MODERN HERO CARD & FILTER TAHUN PENGGAJIAN
         ========================================================================= --}}
    <div class="up-hero-card">
        <div class="up-hero-card__decor-1"></div>
        <div class="up-hero-card__decor-2"></div>
        <div class="up-hero-card__content">
            <div class="up-hero-card__left">
                <div class="up-hero-icon up-hero-icon--orange">
                    <i class="pe-7s-cash"></i>
                </div>
                <div>
                    <h1 class="up-hero-title">Slip &amp; Riwayat Gaji</h1>
                    <p class="up-hero-desc">
                        {{ $karyawan->nama_lengkap ?? Auth::user()->name }}
                        @if ($karyawan?->nipy)
                            &bull; <span class="up-hero-nipy">NIPY: {{ $karyawan->nipy }}</span>
                        @endif
                        @if ($karyawan?->unit)
                            &bull; <span class="up-hero-unit">{{ $karyawan->unit }}</span>
                        @endif
                    </p>

                    <div class="up-hero-metrics-bar">
                        <div class="up-metric-badge up-metric-badge--emerald" title="Total Gaji Bersih Tahun Ini">
                            <span class="up-metric-dot bg-emerald"></span>
                            <span class="up-metric-label">Total Bersih:</span>
                            <strong class="up-metric-value">{{ $formatRp($totalGajiBersih) }}</strong>
                        </div>
                        <div class="up-metric-badge up-metric-badge--sky" title="Total Dokumen Slip">
                            <span class="up-metric-dot bg-sky"></span>
                            <span class="up-metric-label">Jumlah Slip:</span>
                            <strong class="up-metric-value">{{ $totalSlip }} Dokumen</strong>
                        </div>
                        @if ($latestSlip)
                            <div class="up-metric-badge up-metric-badge--amber" title="Slip Terakhir yang Diterbitkan">
                                <span class="up-metric-dot bg-amber"></span>
                                <span class="up-metric-label">Terakhir:</span>
                                <strong class="up-metric-value">{{ $latestSlip->periode_teks }}</strong>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Form Filter Periode Tahun -->
            <form method="GET" action="{{ route('user.penggajian.index') }}" class="up-period-filter">
                @if (request('jenis'))
                    <input type="hidden" name="jenis" value="{{ request('jenis') }}">
                @endif

                <div class="up-select-wrapper" style="min-width: 140px;">
                    <select name="tahun" onchange="this.form.submit()" title="Pilih Tahun Penggajian">
                        <option value="semua" {{ $selectedYear === null ? 'selected' : '' }}>Semua Tahun</option>
                        @foreach ($tahunOptions as $year)
                            <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                Tahun {{ $year }}
                            </option>
                        @endforeach
                    </select>
                    <i class="fa fa-chevron-down up-select-icon"></i>
                </div>

                <a href="{{ route('user.penggajian.index') }}" class="up-btn-reset" title="Reset ke Tahun Berjalan">
                    <i class="fa fa-sync-alt"></i>
                </a>
            </form>
        </div>
    </div>

    {{-- =========================================================================
         2. REKAPITULASI STATISTIK PENGGAJIAN (4 SUMMARY CARDS MODERN)
         ========================================================================= --}}
    <div class="up-stats-grid">
        <!-- Card 1: Total Gaji Bersih (Take Home Pay - Emerald Green) -->
        <div class="up-stat-card up-stat-card--emerald">
            <div class="up-stat-card__inner">
                <div>
                    <span class="up-stat-label text-emerald">Gaji Bersih Diterima</span>
                    <div class="up-stat-num-row">
                        <span class="up-stat-number up-stat-number--currency">{{ $formatRp($totalGajiBersih) }}</span>
                    </div>
                </div>
                <div class="up-stat-icon-wrap bg-emerald-gradient">
                    <i class="fa fa-wallet"></i>
                </div>
            </div>
            <div class="up-stat-footer border-emerald-soft">
                <span class="up-stat-tag text-emerald">
                    <i class="fa fa-check-circle"></i> Take Home Pay {{ $selectedYear ? 'Tahun ' . $selectedYear : 'Seluruhnya' }}
                </span>
            </div>
        </div>

        <!-- Card 2: Total Penerimaan Bruto (Sunset Amber) -->
        <div class="up-stat-card up-stat-card--terlambat">
            <div class="up-stat-card__inner">
                <div>
                    <span class="up-stat-label text-amber">Total Penerimaan</span>
                    <div class="up-stat-num-row">
                        <span class="up-stat-number up-stat-number--currency">{{ $formatRp($totalPenerimaan) }}</span>
                    </div>
                </div>
                <div class="up-stat-icon-wrap bg-amber-gradient">
                    <i class="fa fa-arrow-circle-up"></i>
                </div>
            </div>
            <div class="up-stat-footer border-amber-soft">
                <span class="up-stat-tag text-amber">
                    <i class="fa fa-plus-circle"></i> Gaji Pokok &amp; Tunjangan
                </span>
            </div>
        </div>

        <!-- Card 3: Total Potongan (Rose Red) -->
        <div class="up-stat-card up-stat-card--alfa">
            <div class="up-stat-card__inner">
                <div>
                    <span class="up-stat-label text-rose">Total Potongan</span>
                    <div class="up-stat-num-row">
                        <span class="up-stat-number up-stat-number--currency">{{ $formatRp($totalPotongan) }}</span>
                    </div>
                </div>
                <div class="up-stat-icon-wrap bg-rose-gradient">
                    <i class="fa fa-arrow-circle-down"></i>
                </div>
            </div>
            <div class="up-stat-footer border-rose-soft">
                <span class="up-stat-tag text-rose">
                    <i class="fa fa-minus-circle"></i> BPJS, Infaq &amp; Lainnya
                </span>
            </div>
        </div>

        <!-- Card 4: Total Slip / Dokumen (Sky Blue) -->
        <div class="up-stat-card up-stat-card--izin">
            <div class="up-stat-card__inner">
                <div>
                    <span class="up-stat-label text-sky">Total Dokumen Slip</span>
                    <div class="up-stat-num-row">
                        <span class="up-stat-number">{{ $totalSlip }}</span>
                        <span class="up-stat-percentage up-stat-percentage--sky">Slip</span>
                    </div>
                </div>
                <div class="up-stat-icon-wrap bg-sky-gradient">
                    <i class="fa fa-file-invoice-dollar"></i>
                </div>
            </div>
            <div class="up-stat-footer border-sky-soft">
                <span class="up-stat-tag text-sky">
                    <i class="fa fa-receipt"></i> Slip Gaji, THR &amp; Bonus
                </span>
            </div>
        </div>
    </div>

    {{-- =========================================================================
         3. DAFTAR RIWAYAT SLIP GAJI (DUAL-VIEW RESPONSIVE: CARD DI HP, TABEL DI DESKTOP)
         ========================================================================= --}}
    <div class="up-main-card">
        <!-- Panel Header: Judul & Filter Kategori Jenis Pills -->
        <div class="up-main-card__head">
            <div>
                <div class="up-main-card__title-row">
                    <div class="up-calendar-badge up-calendar-badge--orange">
                        <i class="fa fa-receipt"></i>
                    </div>
                    <div>
                        <h2 class="up-main-card__title">Riwayat Slip Gaji</h2>
                        <p class="up-main-card__subtitle">
                            Periode: <strong>{{ $selectedYear ? 'Tahun ' . $selectedYear : 'Semua Tahun' }}</strong>
                            &bull; <strong>{{ $riwayatPenggajian->count() }}</strong> slip gaji terdata
                        </p>
                    </div>
                </div>
            </div>

            <!-- Segmented Quick Filter Jenis Gaji -->
            <div class="up-tabs-pills">
                <a href="{{ request()->fullUrlWithQuery(['jenis' => null]) }}"
                    class="up-tab-pill {{ !request('jenis') ? 'is-active' : '' }}">
                    Semua ({{ $riwayatPenggajian->count() }})
                </a>
                <a href="{{ request()->fullUrlWithQuery(['jenis' => 'gaji']) }}"
                    class="up-tab-pill {{ request('jenis') === 'gaji' ? 'is-active' : '' }}">
                    <span class="up-tab-dot bg-sky"></span> Gaji Bulanan
                </a>
                <a href="{{ request()->fullUrlWithQuery(['jenis' => 'thr']) }}"
                    class="up-tab-pill {{ request('jenis') === 'thr' ? 'is-active' : '' }}">
                    <span class="up-tab-dot bg-emerald"></span> THR
                </a>
                <a href="{{ request()->fullUrlWithQuery(['jenis' => 'bonus']) }}"
                    class="up-tab-pill {{ request('jenis') === 'bonus' ? 'is-active' : '' }}">
                    <span class="up-tab-dot bg-amber"></span> Bonus
                </a>
            </div>
        </div>

        {{-- 
            =======================================================================
            [KOMENTAR RESPONSIVE BEHAVIOR - TAMPILAN MOBILE (< 768px)]
            Breakpoint: < 768px (Layar Smartphone / HP)
            Class CSS: 'up-mobile-feed' (Aktif di layar sempit, disembunyikan di Tablet & Desktop)

            ALASAN DESAIN:
            Di layar HP, setiap slip gaji disajikan dalam card terpisah dengan
            informasi Gaji Bersih yang kontras dan tombol aksi satu-ketukan ("Lihat Slip").
            =======================================================================
        --}}
        <div class="up-mobile-feed">
            @forelse ($riwayatPenggajian as $item)
                @php
                    $isPaid = $item->status === 'dibayar';
                @endphp
                <div class="up-mobile-card up-mobile-slip-card">
                    <!-- Baris Atas: Periode & Badge Status -->
                    <div class="up-mobile-card__top">
                        <div class="up-mobile-date">
                            <span class="up-mobile-day-name">{{ $item->periode_teks }}</span>
                            <span class="up-mobile-full-date">
                                {{ $item->created_at ? $item->created_at->translatedFormat('d M Y') : '-' }}
                            </span>
                        </div>
                        <div class="up-mobile-badges">
                            <!-- Jenis Badge -->
                            <span class="up-status-badge up-status-badge--{{ $item->jenis }}">
                                {{ $item->jenis_label }}
                            </span>
                            <!-- Status Bayar Badge -->
                            <span class="up-status-badge up-status-badge--{{ $item->status }}">
                                {{ $item->status_label }}
                            </span>
                        </div>
                    </div>

                    <!-- Highlight Gaji Bersih Mobile -->
                    <div class="up-slip-mobile-highlight">
                        <span class="up-slip-mobile-highlight__label">Gaji Bersih Diterima</span>
                        <span class="up-slip-mobile-highlight__val">{{ $formatRp($item->gaji_bersih) }}</span>
                    </div>

                    <!-- Breakdown Bruto & Potongan -->
                    <div class="up-mobile-time-grid">
                        <div class="up-time-box">
                            <span class="up-time-label">Penerimaan</span>
                            <span class="up-time-val text-success font-weight-bold">
                                {{ $formatRp($item->penerimaan) }}
                            </span>
                        </div>
                        <div class="up-time-box">
                            <span class="up-time-label">Potongan</span>
                            <span class="up-time-val text-danger font-weight-bold">
                                {{ $formatRp($item->potongan) }}
                            </span>
                        </div>
                        <div class="up-time-box">
                            <span class="up-time-label">Status</span>
                            <span class="up-time-val" style="font-size: 0.78rem;">
                                {{ $item->status_label }}
                            </span>
                        </div>
                    </div>

                    <!-- Tombol Aksi Lihat / Cetak Slip Gaji -->
                    <div class="up-mobile-card__bottom up-slip-mobile-actions">
                        <span class="up-shift-name">
                            <i class="fa fa-briefcase"></i>
                            {{ $item->unit ?? ($karyawan->unit ?? 'Yayasan') }}
                        </span>

                        <a href="{{ route('user.penggajian.show', $item->id) }}" class="up-btn-slip-view">
                            <i class="fa fa-file-invoice"></i>
                            <span>Buka Slip Gaji</span>
                        </a>
                    </div>
                </div>
            @empty
                <div class="up-empty-state">
                    <div class="up-empty-icon up-empty-icon--orange">
                        <i class="pe-7s-cash"></i>
                    </div>
                    <h3>Belum Ada Dokumen Slip Gaji</h3>
                    <p>Tidak ditemukan rekaman slip gaji untuk periode {{ $selectedYear ? 'Tahun ' . $selectedYear : 'yang dipilih' }}.</p>
                </div>
            @endforelse
        </div>

        {{-- 
            =======================================================================
            [KOMENTAR RESPONSIVE BEHAVIOR - TAMPILAN TABLET & PC (>= 768px)]
            Breakpoint: >= 768px (Layar Tablet, Laptop, dan Desktop)
            Class CSS: 'up-desktop-table-wrapper' (Tampil di Tablet/PC, disembunyikan di HP)

            ALASAN DESAIN:
            Di layar PC/Tablet, seluruh riwayat disajikan dalam tabel SaaS elegan.
            Kolom Nomor dipersempit ringkas, nominal diformat rapi, dan tombol aksi
            terintegrasi dengan ikon cetak/lihat slip.
            =======================================================================
        --}}
        <div class="up-desktop-table-wrapper">
            <table class="up-table">
                <thead>
                    <tr>
                        <th class="up-th-no">No</th>
                        <th style="width: 25%;">Periode &amp; Jenis</th>
                        <th style="width: 14%;">Tanggal Terbit</th>
                        <th class="text-right" style="width: 15%;">Penerimaan</th>
                        <th class="text-right" style="width: 14%;">Potongan</th>
                        <th class="text-right" style="width: 17%;">Gaji Bersih</th>
                        <th class="text-center" style="width: 10%;">Status</th>
                        <th class="text-center" style="width: 5%; min-width: 70px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($riwayatPenggajian as $index => $item)
                        <tr>
                            <!-- Kolom 1: No Urut (Persempit Ringkas) -->
                            <td class="up-col-num">
                                {{ $index + 1 }}
                            </td>

                            <!-- Kolom 2: Periode & Jenis Slip -->
                            <td>
                                <div class="up-table-periode-cell">
                                    <div class="up-periode-title">{{ $item->periode_teks }}</div>
                                    <div class="up-periode-sub">
                                        <span class="up-status-badge up-status-badge--{{ $item->jenis }}">
                                            {{ $item->jenis_label }}
                                        </span>
                                        @if ($item->unit)
                                            <span class="up-unit-pill">{{ $item->unit }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Kolom 3: Tanggal Terbit Slip -->
                            <td>
                                <div class="up-table-date-cell">
                                    <span class="up-table-full-date">
                                        {{ $item->created_at ? $item->created_at->translatedFormat('d M Y') : '-' }}
                                    </span>
                                    <span class="up-table-day-name text-muted" style="font-size: 0.72rem;">
                                        {{ $item->created_at ? $item->created_at->translatedFormat('H:i') . ' WIB' : '' }}
                                    </span>
                                </div>
                            </td>

                            <!-- Kolom 4: Total Penerimaan Bruto -->
                            <td class="text-right">
                                <span class="up-val-penerimaan">
                                    +{{ $formatRp($item->penerimaan) }}
                                </span>
                            </td>

                            <!-- Kolom 5: Total Potongan -->
                            <td class="text-right">
                                <span class="up-val-potongan">
                                    -{{ $formatRp($item->potongan) }}
                                </span>
                            </td>

                            <!-- Kolom 6: Gaji Bersih / Take Home Pay -->
                            <td class="text-right">
                                <span class="up-val-gajibersih">
                                    {{ $formatRp($item->gaji_bersih) }}
                                </span>
                            </td>

                            <!-- Kolom 7: Status Slip Gaji -->
                            <td class="text-center">
                                <span class="up-status-badge up-status-badge--{{ $item->status }}">
                                    @if ($item->status === 'dibayar')
                                        <i class="fa fa-check-circle mr-1"></i>
                                    @endif
                                    {{ $item->status_label }}
                                </span>
                            </td>

                            <!-- Kolom 8: Tombol Aksi Buka / Cetak Slip Gaji -->
                            <td class="text-center">
                                <a href="{{ route('user.penggajian.show', $item->id) }}"
                                    class="up-btn-action"
                                    title="Buka &amp; Cetak Slip Gaji">
                                    <i class="fa fa-file-invoice"></i>
                                    <span>Slip</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">
                                <div class="up-empty-state">
                                    <div class="up-empty-icon up-empty-icon--orange">
                                        <i class="pe-7s-cash"></i>
                                    </div>
                                    <h3>Belum Ada Dokumen Slip Gaji</h3>
                                    <p>Tidak ditemukan rekaman slip gaji untuk periode {{ $selectedYear ? 'Tahun ' . $selectedYear : 'yang dipilih' }}.</p>
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
                <i class="fa fa-wallet text-emerald"></i>
                <span>Menampilkan seluruh <strong>{{ $riwayatPenggajian->count() }}</strong> rekaman slip gaji {{ $selectedYear ? 'Tahun ' . $selectedYear : 'keseluruhan' }}</span>
            </div>
            <div class="up-foot-legend">
                <span class="up-legend-item">
                    <span class="up-tab-dot bg-emerald"></span> Gaji Bersih: <strong>{{ $formatRp($totalGajiBersih) }}</strong>
                </span>
                <span class="up-legend-item">
                    <span class="up-tab-dot bg-amber"></span> Penerimaan: <strong>{{ $formatRp($totalPenerimaan) }}</strong>
                </span>
                <span class="up-legend-item">
                    <span class="up-tab-dot bg-rose"></span> Potongan: <strong>{{ $formatRp($totalPotongan) }}</strong>
                </span>
            </div>
        </div>
    </div>

</div>
@endsection

@push('css')
<style>
    /* =========================================================================
       GLOBAL CONTAINER & COLOR TOKENS
       ========================================================================= */
    .user-presensi-wrapper {
        margin: 0 auto;
        padding-bottom: 30px;
    }

    /* Color Tokens */
    .text-emerald { color: #059669 !important; }
    .text-amber   { color: #d97706 !important; }
    .text-sky     { color: #0284c7 !important; }
    .text-rose    { color: #e11d48 !important; }

    .bg-emerald { background-color: #10b981 !important; }
    .bg-amber   { background-color: #f59e0b !important; }
    .bg-sky     { background-color: #0ea5e9 !important; }
    .bg-rose    { background-color: #f43f5e !important; }

    .bg-emerald-gradient { background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important; color: #fff !important; }
    .bg-amber-gradient   { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important; color: #fff !important; }
    .bg-sky-gradient     { background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%) !important; color: #fff !important; }
    .bg-rose-gradient    { background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%) !important; color: #fff !important; }

    .border-emerald-soft { border-color: #a7f3d0 !important; background-color: #f0fdf4 !important; }
    .border-amber-soft   { border-color: #fde68a !important; background-color: #fffbeb !important; }
    .border-sky-soft     { border-color: #bae6fd !important; background-color: #f0f9ff !important; }
    .border-rose-soft    { border-color: #fecdd3 !important; background-color: #fff1f2 !important; }

    /* =========================================================================
       1. HERO HEADER CARD
       ========================================================================= */
    .up-hero-card {
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 55%, #c2410c 100%);
        border-radius: 20px;
        color: #ffffff;
        padding: 24px 28px;
        margin-bottom: 22px;
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.25), 0 8px 10px -6px rgba(194, 65, 12, 0.2);
    }
    .up-hero-card__decor-1 {
        position: absolute;
        width: 320px;
        height: 320px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(249, 115, 22, 0.25) 0%, rgba(249, 115, 22, 0) 70%);
        top: -120px;
        right: 80px;
        pointer-events: none;
    }
    .up-hero-card__decor-2 {
        position: absolute;
        width: 200px;
        height: 200px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(251, 146, 60, 0.15) 0%, rgba(251, 146, 60, 0) 70%);
        bottom: -80px;
        left: 20%;
        pointer-events: none;
    }
    .up-hero-card__content {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
    }
    .up-hero-card__left {
        display: flex;
        align-items: center;
        gap: 18px;
    }
    .up-hero-icon {
        width: 58px;
        height: 58px;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: #fb923c;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        flex-shrink: 0;
    }
    .up-hero-icon--orange {
        color: #f97316;
        background: rgba(249, 115, 22, 0.18);
        border: 1px solid rgba(249, 115, 22, 0.35);
    }
    .up-hero-title {
        font-size: 1.45rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        margin: 0 0 4px 0;
        color: #ffffff;
    }
    .up-hero-desc {
        margin: 0;
        font-size: 0.88rem;
        color: #cbd5e1;
        font-weight: 500;
    }
    .up-hero-nipy {
        color: #f97316;
        font-weight: 700;
    }
    .up-hero-unit {
        color: #fdba74;
        font-weight: 600;
    }
    .up-hero-metrics-bar {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 10px;
        flex-wrap: wrap;
    }
    .up-metric-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 20px;
        background: rgba(15, 23, 42, 0.45);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        font-size: 0.74rem;
        color: #e2e8f0;
    }
    .up-metric-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        display: inline-block;
    }
    .up-metric-value {
        color: #ffffff;
        font-weight: 700;
    }

    /* Period Filter Dropdown Form */
    .up-period-filter {
        display: flex;
        align-items: center;
        gap: 8px;
        background: rgba(15, 23, 42, 0.4);
        padding: 6px 8px;
        border-radius: 14px;
        border: 1px solid rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(8px);
    }
    .up-select-wrapper {
        position: relative;
    }
    .up-select-wrapper select {
        appearance: none;
        -webkit-appearance: none;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: #ffffff;
        border-radius: 10px;
        padding: 7px 32px 7px 12px;
        font-size: 0.82rem;
        font-weight: 600;
        cursor: pointer;
        outline: none;
        transition: all 0.2s ease;
        width: 100%;
    }
    .up-select-wrapper select:hover,
    .up-select-wrapper select:focus {
        background: rgba(255, 255, 255, 0.15);
        border-color: rgba(249, 115, 22, 0.6);
    }
    .up-select-wrapper select option {
        background: #1e293b;
        color: #ffffff;
    }
    .up-select-icon {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        font-size: 10px;
        color: #cbd5e1;
    }
    .up-btn-reset {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: #cbd5e1;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .up-btn-reset:hover {
        background: #ea580c;
        border-color: #ea580c;
        color: #ffffff;
        transform: rotate(180deg);
    }

    /* =========================================================================
       2. REKAPITULASI STATISTIK (4 SUMMARY CARDS)
       ========================================================================= --}}
    */
    .up-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 22px;
    }
    @media (max-width: 992px) {
        .up-stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
        }
    }
    @media (max-width: 576px) {
        .up-stats-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }
    }
    .up-stat-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04), 0 2px 4px -2px rgba(0, 0, 0, 0.03);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        overflow: hidden;
    }
    .up-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -4px rgba(0, 0, 0, 0.04);
    }
    .up-stat-card__inner {
        padding: 18px 20px 14px;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }
    .up-stat-label {
        display: block;
        font-size: 0.76rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 6px;
    }
    .up-stat-num-row {
        display: flex;
        align-items: baseline;
        gap: 8px;
        flex-wrap: wrap;
    }
    .up-stat-number {
        font-size: 1.65rem;
        font-weight: 800;
        line-height: 1.1;
        color: #0f172a;
        letter-spacing: -0.02em;
    }
    .up-stat-number--currency {
        font-size: 1.28rem;
    }
    .up-stat-percentage {
        font-size: 0.74rem;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 8px;
        background: #f1f5f9;
        color: #475569;
    }
    .up-stat-percentage--sky {
        background: #e0f2fe;
        color: #0369a1;
    }
    .up-stat-icon-wrap {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12);
    }
    .up-stat-footer {
        padding: 8px 20px;
        border-top: 1px solid transparent;
        font-size: 0.74rem;
    }
    .up-stat-tag {
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    /* =========================================================================
       3. MAIN CARD & SEGMENTED TABS
       ========================================================================= */
    .up-main-card {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 14px -2px rgba(15, 23, 42, 0.05);
        overflow: hidden;
    }
    .up-main-card__head {
        padding: 18px 22px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        background: #ffffff;
    }
    .up-main-card__title-row {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .up-calendar-badge {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: #fff7ed;
        color: #ea580c;
        border: 1px solid #fed7aa;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }
    .up-main-card__title {
        font-size: 1.05rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        letter-spacing: -0.01em;
    }
    .up-main-card__subtitle {
        font-size: 0.78rem;
        color: #64748b;
        margin: 2px 0 0 0;
    }

    /* Segmented Tab Pills */
    .up-tabs-pills {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px;
        border-radius: 12px;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        flex-wrap: wrap;
    }
    .up-tab-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 600;
        color: #475569;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .up-tab-pill:hover {
        color: #0f172a;
        background: rgba(255, 255, 255, 0.7);
    }
    .up-tab-pill.is-active {
        background: #ffffff;
        color: #ea580c;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        font-weight: 700;
    }
    .up-tab-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        display: inline-block;
    }

    /* =========================================================================
       4. MOBILE FEED CARD VIEW (< 768px)
       ========================================================================= */
    .up-mobile-feed {
        display: none;
        padding: 14px;
        gap: 12px;
        flex-direction: column;
    }
    @media (max-width: 767.98px) {
        .up-mobile-feed {
            display: flex;
        }
        .up-desktop-table-wrapper {
            display: none !important;
        }
    }
    .up-mobile-card {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        padding: 14px 16px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
        transition: transform 0.15s ease;
    }
    .up-mobile-card__top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 12px;
    }
    .up-mobile-date {
        display: flex;
        flex-direction: column;
    }
    .up-mobile-day-name {
        font-size: 0.92rem;
        font-weight: 800;
        color: #0f172a;
    }
    .up-mobile-full-date {
        font-size: 0.74rem;
        color: #64748b;
    }
    .up-mobile-badges {
        display: flex;
        gap: 4px;
        align-items: center;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    /* Mobile Highlight Gaji Bersih */
    .up-slip-mobile-highlight {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 10px;
        padding: 10px 14px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }
    .up-slip-mobile-highlight__label {
        font-size: 0.74rem;
        font-weight: 700;
        color: #166534;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }
    .up-slip-mobile-highlight__val {
        font-size: 1.15rem;
        font-weight: 800;
        color: #15803d;
    }

    /* Mobile Time/Stat Grid */
    .up-mobile-time-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
        padding: 10px 12px;
        border-radius: 10px;
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        margin-bottom: 12px;
    }
    .up-time-box {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    .up-time-label {
        font-size: 0.68rem;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 2px;
    }
    .up-time-val {
        font-size: 0.85rem;
        font-weight: 700;
        color: #1e293b;
    }
    .up-mobile-card__bottom {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 8px;
        border-top: 1px dashed #e2e8f0;
        font-size: 0.74rem;
        color: #64748b;
    }
    .up-btn-slip-view {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        background: linear-gradient(135deg, #ea580c, #c2410c);
        color: #ffffff !important;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.75rem;
        text-decoration: none;
        box-shadow: 0 3px 8px rgba(234, 88, 12, 0.25);
    }

    /* =========================================================================
       5. DESKTOP SAAS TABLE (>= 768px)
       ========================================================================= */
    .up-desktop-table-wrapper {
        overflow-x: auto;
    }
    .up-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }
    .up-table thead th {
        background: #f8fafc;
        padding: 12px 16px;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }
    .up-th-no,
    .up-table thead th.up-th-num,
    .up-col-num {
        width: 38px !important;
        max-width: 44px !important;
        min-width: 36px !important;
        padding-left: 6px !important;
        padding-right: 6px !important;
        text-align: center !important;
        white-space: nowrap !important;
        font-weight: 700;
        color: #94a3b8;
        font-size: 0.8rem;
    }
    .up-table tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: background-color 0.15s ease;
    }
    .up-table tbody tr:hover {
        background-color: #fafbfc;
    }
    .up-table tbody td {
        padding: 13px 16px;
        font-size: 0.84rem;
        color: #1e293b;
        vertical-align: middle;
    }

    /* Table Cell Specifics */
    .up-table-periode-cell {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }
    .up-periode-title {
        font-weight: 800;
        color: #0f172a;
        font-size: 0.88rem;
    }
    .up-periode-sub {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }
    .up-unit-pill {
        font-size: 0.7rem;
        color: #64748b;
        background: #f1f5f9;
        padding: 2px 6px;
        border-radius: 6px;
        font-weight: 600;
    }
    .up-table-date-cell {
        display: flex;
        flex-direction: column;
    }
    .up-table-full-date {
        font-weight: 600;
        color: #334155;
    }

    /* Currency Columns */
    .up-val-penerimaan {
        font-weight: 700;
        color: #059669;
        font-size: 0.86rem;
        white-space: nowrap !important;
        display: inline-block;
    }
    .up-val-potongan {
        font-weight: 700;
        color: #e11d48;
        font-size: 0.86rem;
        white-space: nowrap !important;
        display: inline-block;
    }
    .up-val-gajibersih {
        font-weight: 800;
        color: #0f172a;
        font-size: 0.94rem;
        background: #f0fdf4;
        padding: 4px 8px;
        border-radius: 6px;
        border: 1px solid #bbf7d0;
        display: inline-block;
        white-space: nowrap !important;
    }

    /* Status Badges */
    .up-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: capitalize;
    }
    .up-status-badge--gaji {
        background: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
    }
    .up-status-badge--thr {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }
    .up-status-badge--bonus {
        background: #fef3c7;
        color: #b45309;
        border: 1px solid #fde68a;
    }
    .up-status-badge--dibayar {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
    }
    .up-status-badge--final {
        background: #e0e7ff;
        color: #4338ca;
        border: 1px solid #c7d2fe;
    }
    .up-status-badge--draft {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    /* Action Button */
    .up-btn-action {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 12px;
        border-radius: 8px;
        background: #fff7ed;
        color: #ea580c !important;
        border: 1px solid #fdba74;
        font-weight: 700;
        font-size: 0.76rem;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .up-btn-action:hover {
        background: #ea580c;
        color: #ffffff !important;
        border-color: #ea580c;
        box-shadow: 0 4px 10px rgba(234, 88, 12, 0.25);
        transform: translateY(-1px);
    }

    /* =========================================================================
       6. EMPTY STATE & FOOTER
       ========================================================================= */
    .up-empty-state {
        padding: 48px 20px;
        text-align: center;
    }
    .up-empty-icon {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        background: #f1f5f9;
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        margin: 0 auto 16px;
    }
    .up-empty-icon--orange {
        background: #fff7ed;
        color: #f97316;
    }
    .up-empty-state h3 {
        font-size: 1.05rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 6px;
    }
    .up-empty-state p {
        font-size: 0.82rem;
        color: #64748b;
        max-width: 400px;
        margin: 0 auto;
    }

    .up-main-card__foot {
        padding: 14px 22px;
        background: #f8fafc;
        border-top: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        font-size: 0.78rem;
        color: #64748b;
    }
    .up-foot-info {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .up-foot-legend {
        display: flex;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
    }
    .up-legend-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
</style>
@endpush

