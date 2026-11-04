@extends('layouts.appui')

@section('title')
    <title>Tupoksi Saya - {{ $karyawan->nama_lengkap ?? Auth::user()->name }} | SIPIT PION</title>
@endsection

@section('content')
<div class="user-presensi-wrapper user-tupoksi-wrapper">

    {{-- Alert Notifikasi Success / Error --}}
    @if (session('success'))
        <div class="up-alert up-alert--success">
            <i class="fa fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div class="up-alert up-alert--error">
            <i class="fa fa-exclamation-triangle"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- =========================================================================
         1. MODERN HERO CARD & FILTER TAHUN AJARAN
         ========================================================================= --}}
    <div class="up-hero-card">
        <div class="up-hero-card__decor-1"></div>
        <div class="up-hero-card__decor-2"></div>
        <div class="up-hero-card__content">
            <div class="up-hero-card__left">
                <div class="up-hero-icon up-hero-icon--orange">
                    <i class="pe-7s-note2"></i>
                </div>
                <div>
                    <h1 class="up-hero-title">Tugas Pokok &amp; Fungsi (Tupoksi)</h1>
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
                        <div class="up-metric-badge up-metric-badge--sky" title="Total Dokumen Tupoksi Ditetapkan">
                            <span class="up-metric-dot bg-sky"></span>
                            <span class="up-metric-label">Total Tupoksi:</span>
                            <strong class="up-metric-value">{{ $kpi['total_tupoksi'] ?? $totalTupoksi ?? 0 }} Dokumen</strong>
                        </div>
                        <div class="up-metric-badge up-metric-badge--emerald" title="Ragam Posisi / Amanah Tugas">
                            <span class="up-metric-dot bg-emerald"></span>
                            <span class="up-metric-label">Ragam Jabatan:</span>
                            <strong class="up-metric-value">{{ $kpi['total_jabatan'] ?? $totalJabatan ?? 0 }} Posisi</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Pencarian & Filter Tahun Ajaran -->
            <div class="up-hero-card__actions">
                <form method="GET" action="{{ route('user.tupoksi.index') }}" class="up-period-filter">
                    <div class="up-search-input-wrap">
                        <i class="fa fa-search up-search-icon"></i>
                        <input type="text" name="search" value="{{ $filters['search'] ?? request('search') ?? '' }}"
                            placeholder="Cari uraian tupoksi / jabatan..." class="up-search-input">
                    </div>

                    <div class="up-select-wrapper" style="min-width: 160px;">
                        <select name="tahun_ajaran" onchange="this.form.submit()" title="Filter Tahun Ajaran">
                            <option value="semua">Semua Tahun Ajaran</option>
                            @foreach ($tahunAjaranOptions ?? $daftarTahunAjaran ?? [] as $taId => $taLabel)
                                @php
                                    $currTa = $selectedTa ?? ($filters['tahun_ajaran'] ?? ($filters['tahun_ajaran_id'] ?? request('tahun_ajaran')));
                                @endphp
                                <option value="{{ $taId }}" {{ (string)$currTa === (string)$taId ? 'selected' : '' }}>
                                    {{ is_object($taLabel) ? ($taLabel->nama_tahun_ajaran ?? $taLabel->tahun_ajaran) : $taLabel }}
                                </option>
                            @endforeach
                        </select>
                        <i class="fa fa-chevron-down up-select-icon"></i>
                    </div>

                    @if (!empty($filters['search']) || !empty($filters['tahun_ajaran']) || request('search') || (request('tahun_ajaran') && request('tahun_ajaran') !== 'semua'))
                        <a href="{{ route('user.tupoksi.index') }}" class="up-btn-reset" title="Reset Filter">
                            <i class="fa fa-sync-alt"></i>
                        </a>
                    @endif
                </form>
            </div>
        </div>
    </div>

    {{-- =========================================================================
         2. REKAPITULASI STATISTIK TUPOKSI (4 SUMMARY CARDS MODERN)
         ========================================================================= --}}
    <div class="up-stats-grid">
        <!-- Card 1: Total Dokumen Tupoksi (Sky Blue) -->
        <div class="up-stat-card">
            <div class="up-stat-card__inner">
                <div>
                    <span class="up-stat-label text-sky">Total Tupoksi</span>
                    <div class="up-stat-num-row">
                        <span class="up-stat-number">{{ $kpi['total_tupoksi'] ?? $totalTupoksi ?? 0 }}</span>
                        <span class="up-stat-percentage up-stat-percentage--sky">Dokumen</span>
                    </div>
                </div>
                <div class="up-stat-icon-wrap bg-sky-gradient">
                    <i class="fa fa-tasks"></i>
                </div>
            </div>
            <div class="up-stat-footer border-sky-soft">
                <span class="up-stat-tag text-sky">
                    <i class="fa fa-list-check"></i> Uraian Tugas Pokok Resmi
                </span>
            </div>
        </div>

        <!-- Card 2: Ragam Jabatan (Amber Orange) -->
        <div class="up-stat-card">
            <div class="up-stat-card__inner">
                <div>
                    <span class="up-stat-label text-amber">Ragam Jabatan</span>
                    <div class="up-stat-num-row">
                        <span class="up-stat-number">{{ $kpi['total_jabatan'] ?? $totalJabatan ?? 0 }}</span>
                        <span class="up-stat-percentage up-stat-percentage--amber">Posisi</span>
                    </div>
                </div>
                <div class="up-stat-icon-wrap bg-amber-gradient">
                    <i class="fa fa-user-tie"></i>
                </div>
            </div>
            <div class="up-stat-footer border-amber-soft">
                <span class="up-stat-tag text-amber">
                    <i class="fa fa-briefcase"></i> Tanggung Jawab Operasional
                </span>
            </div>
        </div>

        <!-- Card 3: Periode Tahun Ajaran (Emerald Green) -->
        <div class="up-stat-card">
            <div class="up-stat-card__inner">
                <div>
                    <span class="up-stat-label text-emerald">Tahun Ajaran</span>
                    <div class="up-stat-num-row">
                        <span class="up-stat-number">{{ $kpi['total_ta'] ?? $totalTa ?? 0 }}</span>
                        <span class="up-stat-percentage up-stat-percentage--emerald">Periode</span>
                    </div>
                </div>
                <div class="up-stat-icon-wrap bg-emerald-gradient">
                    <i class="fa fa-calendar-alt"></i>
                </div>
            </div>
            <div class="up-stat-footer border-emerald-soft">
                <span class="up-stat-tag text-emerald">
                    <i class="fa fa-history"></i> Tahun Ajaran Ditetapkan
                </span>
            </div>
        </div>

        <!-- Card 4: Berkas PDF (Rose Red) -->
        <div class="up-stat-card">
            <div class="up-stat-card__inner">
                <div>
                    <span class="up-stat-label text-rose">Berkas Panduan</span>
                    <div class="up-stat-num-row">
                        <span class="up-stat-number">{{ $kpi['berkas_tersedia'] ?? $totalBerkas ?? 0 }}</span>
                        <span class="up-stat-percentage up-stat-percentage--rose">Tersedia</span>
                    </div>
                </div>
                <div class="up-stat-icon-wrap bg-rose-gradient">
                    <i class="fa fa-file-pdf"></i>
                </div>
            </div>
            <div class="up-stat-footer border-rose-soft">
                <span class="up-stat-tag text-rose">
                    <i class="fa fa-download"></i> Dokumen PDF Panduan Kerja
                </span>
            </div>
        </div>
    </div>

    {{-- =========================================================================
         3. DAFTAR TUPOKSI (DUAL-VIEW: CARD DI MOBILE, TABEL DI DESKTOP)
         ========================================================================= --}}
    <div class="up-main-card">
        <!-- Panel Header -->
        <div class="up-main-card__head">
            <div>
                <div class="up-main-card__title-row">
                    <div class="up-calendar-badge up-calendar-badge--orange">
                        <i class="fa fa-clipboard-list"></i>
                    </div>
                    <div>
                        <h2 class="up-main-card__title">Daftar Dokumen Tugas Pokok &amp; Fungsi</h2>
                        <p class="up-main-card__subtitle">
                            Standar operasional tugas, tanggung jawab, dan wewenang sesuai posisi Anda
                            &bull; <strong>{{ count($tupoksiList ?? $daftarTupoksi) }}</strong> total dokumen
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- MOBILE FEED (< 768px) --}}
        <div class="up-mobile-feed">
            @forelse ($tupoksiList ?? $daftarTupoksi as $item)
                <div class="up-mobile-card">
                    <div class="up-mobile-card__top">
                        <div class="up-mobile-date">
                            <span class="up-mobile-day-name">{{ $item->jabatan ?: 'Tupoksi Pegawai' }}</span>
                            <span class="up-mobile-full-date">
                                TA: {{ $item->tahunAjaran?->nama_tahun_ajaran ?? ($item->tahunAjaran?->tahun_ajaran ?? '-') }}
                            </span>
                        </div>
                        <div>
                            @if ($item->berkas)
                                <span class="up-status-badge up-status-badge--dibayar">
                                    <i class="fa fa-check-circle mr-1"></i> File Siap
                                </span>
                            @else
                                <span class="up-status-badge up-status-badge--draft">
                                    <i class="fa fa-clock mr-1"></i> Belum Diunggah
                                </span>
                            @endif
                        </div>
                    </div>

                    @if ($item->keterangan)
                        <div class="up-mobile-detail-row">
                            <span class="up-detail-label">Uraian / Catatan:</span>
                            <span class="up-detail-text">{{ $item->keterangan }}</span>
                        </div>
                    @endif

                    <div class="up-mobile-card__bottom">
                        @if ($item->berkas)
                            <div class="d-flex gap-2 w-100">
                                <a href="{{ route('user.tupoksi.preview', $item->id) }}" target="_blank"
                                    class="up-btn-action-preview flex-1 text-center">
                                    <i class="fa fa-eye mr-1"></i> Buka Tupoksi
                                </a>
                                <a href="{{ route('user.tupoksi.download', $item->id) }}"
                                    class="up-btn-action-download">
                                    <i class="fa fa-download"></i>
                                </a>
                            </div>
                        @else
                            <span class="text-muted small">
                                <i class="fa fa-info-circle mr-1"></i> Panduan tertulis belum diunggah admin
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="up-empty-state">
                    <div class="up-empty-icon up-empty-icon--orange">
                        <i class="pe-7s-note2"></i>
                    </div>
                    <h3>Belum Ada Dokumen Tupoksi</h3>
                    <p>Uraian tugas pokok dan fungsi belum ditetapkan atau diunggah untuk posisi Anda.</p>
                </div>
            @endforelse
        </div>

        {{-- DESKTOP TABLE (>= 768px) --}}
        <div class="up-desktop-table-wrapper">
            <table class="up-table">
                <thead>
                    <tr>
                        <th class="up-th-no">No</th>
                        <th style="width: 25%;">Jabatan / Amanah Posisi</th>
                        <th class="text-center" style="width: 15%;">Tahun Ajaran</th>
                        <th style="width: 42%;">Uraian Ringkas &amp; Keterangan</th>
                        <th class="text-center" style="width: 18%;">Dokumen &amp; Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tupoksiList ?? $daftarTupoksi as $index => $item)
                        <tr>
                            <!-- Kolom 1: No Urut (38px narrow) -->
                            <td class="up-col-num">
                                {{ $index + 1 }}
                            </td>

                            <!-- Kolom 2: Jabatan -->
                            <td>
                                <div class="up-table-periode-cell">
                                    <div class="up-periode-title font-weight-bold text-dark">
                                        {{ $item->jabatan ?: 'Pegawai' }}
                                    </div>
                                    <div class="up-periode-sub">
                                        <span class="up-unit-pill">Tupoksi Resmi</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Kolom 3: Tahun Ajaran -->
                            <td class="text-center">
                                <span class="font-weight-bold text-dark small">
                                    {{ $item->tahunAjaran?->nama_tahun_ajaran ?? ($item->tahunAjaran?->tahun_ajaran ?? '-') }}
                                </span>
                            </td>

                            <!-- Kolom 4: Keterangan -->
                            <td>
                                <span class="text-muted small">
                                    {{ $item->keterangan ?: 'Panduan standar operasional tugas pokok dan fungsi.' }}
                                </span>
                            </td>

                            <!-- Kolom 5: Aksi Dokumen -->
                            <td class="text-center">
                                @if ($item->berkas)
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <a href="{{ route('user.tupoksi.preview', $item->id) }}" target="_blank"
                                            class="up-btn-action" title="Buka Dokumen Tupoksi">
                                            <i class="fa fa-eye"></i>
                                            <span>Lihat</span>
                                        </a>
                                        <a href="{{ route('user.tupoksi.download', $item->id) }}"
                                            class="up-btn-action up-btn-action--download" title="Unduh File Tupoksi">
                                            <i class="fa fa-download"></i>
                                        </a>
                                    </div>
                                @else
                                    <span class="text-muted small font-italic">
                                        <i class="fa fa-minus-circle mr-1"></i> Tidak Ada File
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">
                                <div class="up-empty-state">
                                    <div class="up-empty-icon up-empty-icon--orange">
                                        <i class="pe-7s-note2"></i>
                                    </div>
                                    <h3>Belum Ada Dokumen Tupoksi</h3>
                                    <p>Uraian tugas pokok dan fungsi belum ditetapkan atau diunggah untuk posisi Anda.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer Ringkasan -->
        <div class="up-main-card__foot">
            <div class="up-foot-info">
                <i class="fa fa-tasks text-emerald"></i>
                <span>Menampilkan seluruh <strong>{{ count($tupoksiList ?? $daftarTupoksi) }}</strong> dokumen tupoksi</span>
            </div>
            <div class="up-foot-legend">
                <span class="up-legend-item">
                    <span class="up-tab-dot bg-sky"></span> Total: <strong>{{ $kpi['total_tupoksi'] ?? $totalTupoksi ?? 0 }}</strong>
                </span>
                <span class="up-legend-item">
                    <span class="up-tab-dot bg-emerald"></span> Berkas: <strong>{{ $kpi['berkas_tersedia'] ?? $totalBerkas ?? 0 }}</strong>
                </span>
            </div>
        </div>
    </div>

</div>
@endsection

@push('css')
<style>
    /* =========================================================================
       GLOBAL CONTAINER & TOKENS
       ========================================================================= */
    .user-presensi-wrapper {
        margin: 0 auto;
        padding-bottom: 30px;
    }

    .text-emerald { color: #059669 !important; }
    .text-amber   { color: #d97706 !important; }
    .text-sky     { color: #0284c7 !important; }
    .text-rose    { color: #e11d48 !important; }
    .text-orange  { color: #ea580c !important; }

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

    .up-alert {
        padding: 12px 18px;
        border-radius: 12px;
        margin-bottom: 18px;
        font-size: 0.85rem;
        font-weight: 600;
        display: flex;
        gap: 10px;
        align-items: center;
    }
    .up-alert--success {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #166534;
    }
    .up-alert--error {
        background: #fff1f2;
        border: 1px solid #fecdd3;
        color: #be123c;
    }

    .up-search-input-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }
    .up-search-icon {
        position: absolute;
        left: 12px;
        color: #94a3b8;
        font-size: 0.82rem;
        pointer-events: none;
    }
    .up-search-input {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 8px 12px 8px 34px;
        font-size: 0.82rem;
        color: #0f172a;
        font-weight: 500;
        outline: none;
        transition: all 0.2s ease;
        min-width: 180px;
    }
    .up-search-input:focus {
        border-color: #f97316;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.12);
    }

    .up-hero-card {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #c2410c 100%);
        border-radius: 20px;
        padding: 26px 30px;
        color: #fff;
        margin-bottom: 22px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.22);
    }
    .up-hero-card__decor-1 {
        position: absolute;
        top: -40px;
        right: -40px;
        width: 220px;
        height: 220px;
        background: radial-gradient(circle, rgba(249, 115, 22, 0.35) 0%, rgba(249, 115, 22, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .up-hero-card__decor-2 {
        position: absolute;
        bottom: -50px;
        right: 180px;
        width: 160px;
        height: 160px;
        background: radial-gradient(circle, rgba(234, 88, 12, 0.2) 0%, rgba(234, 88, 12, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .up-hero-card__content {
        position: relative;
        z-index: 2;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
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
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        flex-shrink: 0;
    }
    .up-hero-icon--orange {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        color: #fff;
        box-shadow: 0 8px 18px rgba(234, 88, 12, 0.4);
    }
    .up-hero-title {
        font-size: 1.35rem;
        font-weight: 800;
        margin: 0 0 3px 0;
        letter-spacing: -0.02em;
        color: #fff;
    }
    .up-hero-desc {
        font-size: 0.85rem;
        color: #cbd5e1;
        margin: 0;
    }
    .up-hero-nipy, .up-hero-unit {
        color: #fed7aa;
        font-weight: 600;
    }
    .up-hero-metrics-bar {
        display: flex;
        gap: 10px;
        margin-top: 10px;
        flex-wrap: wrap;
    }
    .up-metric-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 4px 11px;
        border-radius: 9999px;
        font-size: 0.74rem;
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(8px);
        color: #f1f5f9;
    }
    .up-metric-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
    }
    .up-metric-value {
        color: #fff;
        font-weight: 700;
    }
    .up-hero-card__actions {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    .up-period-filter {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #fff;
        padding: 5px 7px 5px 12px;
        border-radius: 14px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12);
        flex-wrap: wrap;
    }
    .up-select-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }
    .up-select-wrapper select {
        appearance: none;
        -webkit-appearance: none;
        background: transparent;
        border: none;
        padding: 6px 26px 6px 6px;
        font-size: 0.82rem;
        font-weight: 700;
        color: #0f172a;
        cursor: pointer;
        outline: none;
        width: 100%;
    }
    .up-select-icon {
        position: absolute;
        right: 8px;
        font-size: 0.7rem;
        color: #f97316;
        pointer-events: none;
    }
    .up-btn-reset {
        width: 32px;
        height: 32px;
        border-radius: 9px;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
        transition: all 0.2s ease;
        text-decoration: none;
        border: none;
    }
    .up-btn-reset:hover {
        background: #f97316;
        color: #fff;
    }

    .up-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 22px;
    }
    .up-stat-card {
        background: #fff;
        border-radius: 16px;
        padding: 18px 20px 14px 20px;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.05);
        border: 1px solid #f1f5f9;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .up-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.09);
    }
    .up-stat-card__inner {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 12px;
    }
    .up-stat-label {
        font-size: 0.74rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 4px;
        display: block;
    }
    .up-stat-num-row {
        display: flex;
        align-items: baseline;
        gap: 6px;
    }
    .up-stat-number {
        font-size: 1.65rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
    }
    .up-stat-percentage {
        font-size: 0.72rem;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 6px;
    }
    .up-stat-percentage--emerald { background: #d1fae5; color: #065f46; }
    .up-stat-percentage--amber   { background: #fef3c7; color: #92400e; }
    .up-stat-percentage--sky     { background: #e0f2fe; color: #0369a1; }
    .up-stat-percentage--rose    { background: #ffe4e6; color: #9f1239; }

    .up-stat-icon-wrap {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.05rem;
        flex-shrink: 0;
    }
    .up-stat-footer {
        padding-top: 8px;
        border-top: 1px solid #f1f5f9;
        font-size: 0.72rem;
        font-weight: 600;
    }
    .up-stat-tag {
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .up-main-card {
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05);
        border: 1px solid #f1f5f9;
        overflow: hidden;
    }
    .up-main-card__head {
        padding: 20px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #f1f5f9;
        flex-wrap: wrap;
        gap: 14px;
    }
    .up-main-card__title-row {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .up-calendar-badge {
        width: 40px;
        height: 40px;
        border-radius: 11px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        color: #fff;
    }
    .up-calendar-badge--orange {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
    }
    .up-main-card__title {
        font-size: 1.05rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }
    .up-main-card__subtitle {
        font-size: 0.78rem;
        color: #64748b;
        margin: 2px 0 0 0;
    }

    .up-desktop-table-wrapper {
        display: block;
        overflow-x: auto;
    }
    .up-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.82rem;
    }
    .up-table th {
        background: #f8fafc;
        padding: 12px 16px;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 0.04em;
        border-bottom: 1px solid #e2e8f0;
    }
    .up-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        color: #1e293b;
    }
    .up-table tbody tr:hover {
        background: #fdfaf6;
    }

    /* Kolom No (38px width narrow) */
    .up-th-no, .up-col-num {
        width: 38px !important;
        max-width: 38px !important;
        min-width: 38px !important;
        text-align: center !important;
        font-weight: 700;
        color: #94a3b8;
        padding-left: 6px !important;
        padding-right: 6px !important;
        white-space: nowrap !important;
    }

    .up-periode-title {
        font-weight: 700;
        color: #0f172a;
        font-size: 0.85rem;
    }
    .up-unit-pill {
        display: inline-flex;
        align-items: center;
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
        padding: 3px 9px;
        border-radius: 9999px;
        font-size: 0.72rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .up-status-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 9999px;
        font-size: 0.74rem;
        font-weight: 700;
        white-space: nowrap;
    }
    .up-status-badge--dibayar {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }
    .up-status-badge--draft {
        background: #fef3c7;
        color: #b45309;
        border: 1px solid #fde68a;
    }

    /* Action Buttons */
    .up-btn-action {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #fff;
        color: #ea580c;
        border: 1.5px solid #fed7aa;
        padding: 5px 11px;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .up-btn-action:hover {
        background: #ea580c;
        color: #fff;
        border-color: #ea580c;
    }
    .up-btn-action--download {
        background: #f8fafc;
        color: #0284c7;
        border-color: #bae6fd;
    }
    .up-btn-action--download:hover {
        background: #0284c7;
        color: #fff;
        border-color: #0284c7;
    }

    .up-btn-action-preview {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #ea580c;
        color: #fff;
        padding: 7px 12px;
        border-radius: 8px;
        font-size: 0.78rem;
        font-weight: 700;
        text-decoration: none;
    }
    .up-btn-action-download {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
        color: #0284c7;
        border: 1px solid #bae6fd;
        padding: 7px 12px;
        border-radius: 8px;
        font-size: 0.82rem;
        text-decoration: none;
    }

    .up-empty-state {
        padding: 44px 20px;
        text-align: center;
    }
    .up-empty-icon {
        width: 64px;
        height: 64px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 14px auto;
        font-size: 30px;
    }
    .up-empty-icon--orange {
        background: #ffedd5;
        color: #f97316;
    }
    .up-empty-state h3 {
        font-size: 1rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 4px 0;
    }
    .up-empty-state p {
        font-size: 0.82rem;
        color: #64748b;
        margin: 0;
    }

    .up-main-card__foot {
        padding: 14px 24px;
        background: #f8fafc;
        border-top: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.78rem;
        color: #64748b;
        flex-wrap: wrap;
        gap: 10px;
    }
    .up-foot-info {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .up-mobile-feed {
        display: none;
        padding: 14px;
        gap: 12px;
        flex-direction: column;
    }
    .up-mobile-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }
    .up-mobile-card__top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 12px;
        gap: 10px;
    }
    .up-mobile-day-name {
        font-weight: 800;
        color: #0f172a;
        font-size: 0.92rem;
        display: block;
    }
    .up-mobile-full-date {
        font-size: 0.75rem;
        color: #64748b;
    }
    .up-mobile-detail-row {
        font-size: 0.78rem;
        color: #334155;
        margin-top: 6px;
        margin-bottom: 10px;
    }
    .up-detail-label {
        font-weight: 700;
        color: #64748b;
        margin-right: 4px;
    }
    .up-mobile-card__bottom {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 10px;
        border-top: 1px solid #f1f5f9;
    }

    @media (max-width: 991px) {
        .up-stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 767px) {
        .up-desktop-table-wrapper {
            display: none !important;
        }
        .up-mobile-feed {
            display: flex !important;
        }
        .up-stats-grid {
            grid-template-columns: 1fr;
        }
        .up-hero-card {
            padding: 20px 18px;
        }
        .up-hero-title {
            font-size: 1.15rem;
        }
    }
</style>
@endpush

