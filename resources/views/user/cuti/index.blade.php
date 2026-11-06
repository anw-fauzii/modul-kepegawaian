@extends('layouts.appui')

@section('title')
    <title>Pengajuan Cuti Saya - {{ $karyawan->nama_lengkap ?? Auth::user()->name }} | SIPIT PION</title>
@endsection

@section('content')
<div class="user-presensi-wrapper user-cuti-wrapper">

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
         1. MODERN HERO CARD & FILTER TAHUN
         ========================================================================= --}}
    <div class="up-hero-card">
        <div class="up-hero-card__decor-1"></div>
        <div class="up-hero-card__decor-2"></div>
        <div class="up-hero-card__content">
            <div class="up-hero-card__left">
                <div class="up-hero-icon up-hero-icon--orange">
                    <i class="pe-7s-date"></i>
                </div>
                <div>
                    <h1 class="up-hero-title">Pengajuan Cuti</h1>
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
                        <div class="up-metric-badge up-metric-badge--emerald" title="Total Hari Cuti Disetujui">
                            <span class="up-metric-dot bg-emerald"></span>
                            <span class="up-metric-label">Cuti Di-ACC:</span>
                            <strong class="up-metric-value">{{ $totalHariCuti }} Hari Kerja</strong>
                        </div>
                        <div class="up-metric-badge up-metric-badge--amber" title="Pengajuan Menunggu Persetujuan">
                            <span class="up-metric-dot bg-amber"></span>
                            <span class="up-metric-label">Menunggu ACC:</span>
                            <strong class="up-metric-value">{{ $totalMenunggu }} Pengajuan</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Tombol Ajukan & Form Filter Periode Tahun -->
            <div class="up-hero-card__actions">
                <a href="{{ route('user.cuti.create') }}" class="up-btn-primary-hero">
                    <i class="fa fa-plus-circle"></i>
                    <span>Ajukan Cuti Baru</span>
                </a>

                <form method="GET" action="{{ route('user.cuti.index') }}" class="up-period-filter">
                    @if (request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif

                    <div class="up-search-input-wrap">
                        <i class="fa fa-search up-search-icon"></i>
                        <input type="text" name="search" value="{{ $filters['search'] ?? request('search') ?? '' }}"
                            placeholder="Cari permohonan cuti..." class="up-search-input">
                    </div>

                    <div class="up-select-wrapper" style="min-width: 140px;">
                        <select name="tahun" onchange="this.form.submit()" title="Pilih Tahun">
                            <option value="semua" {{ $selectedYear === null ? 'selected' : '' }}>Semua Tahun</option>
                            @foreach ($tahunOptions as $year)
                                <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                    Tahun {{ $year }}
                                </option>
                            @endforeach
                        </select>
                        <i class="fa fa-chevron-down up-select-icon"></i>
                    </div>

                    @if (!empty($filters['search']) || request('search') || request('tahun') || request('status'))
                        <a href="{{ route('user.cuti.index') }}" class="up-btn-reset" title="Reset Filter">
                            <i class="fa fa-sync-alt"></i>
                        </a>
                    @endif
                </form>
            </div>
        </div>
    </div>

    {{-- =========================================================================
         2. REKAPITULASI STATISTIK CUTI (4 SUMMARY CARDS MODERN)
         ========================================================================= --}}
    <div class="up-stats-grid">
        <!-- Card 1: Total Pengajuan Cuti (Sky Blue) -->
        <div class="up-stat-card">
            <div class="up-stat-card__inner">
                <div>
                    <span class="up-stat-label text-sky">Total Pengajuan</span>
                    <div class="up-stat-num-row">
                        <span class="up-stat-number">{{ $totalPengajuan }}</span>
                        <span class="up-stat-percentage up-stat-percentage--sky">Permohonan</span>
                    </div>
                </div>
                <div class="up-stat-icon-wrap bg-sky-gradient">
                    <i class="fa fa-file-signature"></i>
                </div>
            </div>
            <div class="up-stat-footer border-sky-soft">
                <span class="up-stat-tag text-sky">
                    <i class="fa fa-list"></i> Seluruh Permohonan {{ $selectedYear ? 'Tahun ' . $selectedYear : '' }}
                </span>
            </div>
        </div>

        <!-- Card 2: Disetujui / ACC (Emerald Green) -->
        <div class="up-stat-card">
            <div class="up-stat-card__inner">
                <div>
                    <span class="up-stat-label text-emerald">Disetujui (ACC)</span>
                    <div class="up-stat-num-row">
                        <span class="up-stat-number">{{ $totalDisetujui }}</span>
                        <span class="up-stat-percentage up-stat-percentage--emerald">Disetujui</span>
                    </div>
                </div>
                <div class="up-stat-icon-wrap bg-emerald-gradient">
                    <i class="fa fa-check-double"></i>
                </div>
            </div>
            <div class="up-stat-footer border-emerald-soft">
                <span class="up-stat-tag text-emerald">
                    <i class="fa fa-check-circle"></i> Telah Disetujui Pimpinan
                </span>
            </div>
        </div>

        <!-- Card 3: Menunggu ACC (Sunset Amber) -->
        <div class="up-stat-card">
            <div class="up-stat-card__inner">
                <div>
                    <span class="up-stat-label text-amber">Menunggu ACC</span>
                    <div class="up-stat-num-row">
                        <span class="up-stat-number">{{ $totalMenunggu }}</span>
                        <span class="up-stat-percentage up-stat-percentage--amber">Pending</span>
                    </div>
                </div>
                <div class="up-stat-icon-wrap bg-amber-gradient">
                    <i class="fa fa-hourglass-half"></i>
                </div>
            </div>
            <div class="up-stat-footer border-amber-soft">
                <span class="up-stat-tag text-amber">
                    <i class="fa fa-clock"></i> Sedang Dalam Proses Peninjauan
                </span>
            </div>
        </div>

        <!-- Card 4: Total Hari Kerja Cuti (Rose Red) -->
        <div class="up-stat-card">
            <div class="up-stat-card__inner">
                <div>
                    <span class="up-stat-label text-rose">Total Hari Diambil</span>
                    <div class="up-stat-num-row">
                        <span class="up-stat-number">{{ $totalHariCuti }}</span>
                        <span class="up-stat-percentage up-stat-percentage--rose">Hari Kerja</span>
                    </div>
                </div>
                <div class="up-stat-icon-wrap bg-rose-gradient">
                    <i class="fa fa-calendar-check"></i>
                </div>
            </div>
            <div class="up-stat-footer border-rose-soft">
                <span class="up-stat-tag text-rose">
                    <i class="fa fa-umbrella-beach"></i> Hari Cuti Terhitung Efektif
                </span>
            </div>
        </div>
    </div>

    {{-- =========================================================================
         3. DAFTAR RIWAYAT CUTI (DUAL-VIEW: CARD DI MOBILE, TABEL DI DESKTOP)
         ========================================================================= --}}
    <div class="up-main-card">
        <!-- Panel Header: Judul & Segmented Tabs Status -->
        <div class="up-main-card__head">
            <div>
                <div class="up-main-card__title-row">
                    <div class="up-calendar-badge up-calendar-badge--orange">
                        <i class="fa fa-calendar-alt"></i>
                    </div>
                    <div>
                        <h2 class="up-main-card__title">Riwayat Permohonan Cuti</h2>
                        <p class="up-main-card__subtitle">
                            Periode: <strong>{{ $selectedYear ? 'Tahun ' . $selectedYear : 'Semua Tahun' }}</strong>
                            &bull; <strong>{{ $riwayatCuti->count() }}</strong> rekaman ditemukan
                        </p>
                    </div>
                </div>
            </div>

            <!-- Segmented Quick Filter Status Pills -->
            <div class="up-tabs-pills">
                <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}"
                    class="up-tab-pill {{ !request('status') ? 'is-active' : '' }}">
                    Semua ({{ $riwayatCuti->count() }})
                </a>
                <a href="{{ request()->fullUrlWithQuery(['status' => 'disetujui']) }}"
                    class="up-tab-pill {{ request('status') === 'disetujui' ? 'is-active' : '' }}">
                    <span class="up-tab-dot bg-emerald"></span> Disetujui
                </a>
                <a href="{{ request()->fullUrlWithQuery(['status' => 'menunggu']) }}"
                    class="up-tab-pill {{ request('status') === 'menunggu' ? 'is-active' : '' }}">
                    <span class="up-tab-dot bg-amber"></span> Menunggu ACC
                </a>
            </div>
        </div>

        {{-- 
            =======================================================================
            [KOMENTAR RESPONSIVE BEHAVIOR - TAMPILAN MOBILE (< 768px)]
            Breakpoint: < 768px (Layar Smartphone / HP)
            Class CSS: 'up-mobile-feed' (Tampil di HP, disembunyikan di Tablet & PC)
            =======================================================================
        --}}
        <div class="up-mobile-feed">
            @forelse ($riwayatCuti as $item)
                <div class="up-mobile-card">
                    <div class="up-mobile-card__top">
                        <div class="up-mobile-date">
                            <span class="up-mobile-day-name">{{ $item->jenisCuti?->nama ?? 'Cuti Pegawai' }}</span>
                            <span class="up-mobile-full-date">{{ $item->periode_formatted }}</span>
                        </div>
                        <div>
                            @if ($item->acc)
                                <span class="up-status-badge up-status-badge--dibayar">
                                    <i class="fa fa-check-circle mr-1"></i> Disetujui (ACC)
                                </span>
                            @else
                                <span class="up-status-badge up-status-badge--draft">
                                    <i class="fa fa-hourglass-half mr-1"></i> Menunggu ACC
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="up-mobile-time-grid">
                        <div class="up-time-box">
                            <span class="up-time-label">Lama Cuti</span>
                            <span class="up-time-val font-weight-bold text-orange">
                                {{ $item->jumlah_hari }} Hari Kerja
                            </span>
                        </div>
                        <div class="up-time-box">
                            <span class="up-time-label">Kontak HP</span>
                            <span class="up-time-val" style="font-size: 0.78rem;">
                                {{ $item->telepon ?: '-' }}
                            </span>
                        </div>
                        <div class="up-time-box">
                            <span class="up-time-label">Tgl Diajukan</span>
                            <span class="up-time-val" style="font-size: 0.78rem;">
                                {{ $item->created_at ? $item->created_at->format('d/m/Y') : '-' }}
                            </span>
                        </div>
                    </div>

                    @if ($item->alasan)
                        <div class="up-mobile-detail-row">
                            <span class="up-detail-label">Alasan:</span>
                            <span class="up-detail-text">{{ $item->alasan }}</span>
                        </div>
                    @endif

                    @if ($item->alamat)
                        <div class="up-mobile-detail-row">
                            <span class="up-detail-label">Alamat:</span>
                            <span class="up-detail-text">{{ $item->alamat }}</span>
                        </div>
                    @endif

                    @if ($item->acc && $item->catatan_acc)
                        <div class="up-mobile-detail-row up-mobile-detail-row--note">
                            <span class="up-detail-label text-emerald">Catatan ACC:</span>
                            <span class="up-detail-text">{{ $item->catatan_acc }}</span>
                        </div>
                    @endif

                    <!-- Baris Bawah: Status ACC & Tombol Batalkan -->
                    <div class="up-mobile-card__bottom">
                        <div class="up-mobile-foot-info">
                            @if ($item->acc)
                                <span class="text-success small font-weight-bold">
                                    <i class="fa fa-user-check mr-1"></i> ACC oleh: {{ $item->approver?->name ?? 'Pimpinan' }}
                                    @if ($item->tgl_acc) ({{ $item->tgl_acc->format('d M Y') }}) @endif
                                </span>
                            @else
                                <span class="text-muted small">
                                    <i class="fa fa-info-circle mr-1"></i> Belum disetujui admin
                                </span>
                            @endif
                        </div>

                        @if (!$item->acc)
                            <form action="{{ route('user.cuti.destroy', $item->id) }}" method="POST"
                                onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengajuan cuti ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="up-btn-cancel-cuti">
                                    <i class="fa fa-trash-alt mr-1"></i> Batalkan
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="up-empty-state">
                    <div class="up-empty-icon up-empty-icon--orange">
                        <i class="pe-7s-date"></i>
                    </div>
                    <h3>Belum Ada Permohonan Cuti</h3>
                    <p>Anda belum memiliki riwayat pengajuan cuti untuk periode {{ $selectedYear ? 'Tahun ' . $selectedYear : 'yang dipilih' }}.</p>
                    <div class="mt-3">
                        <a href="{{ route('user.cuti.create') }}" class="up-btn-primary-hero">
                            <i class="fa fa-plus-circle mr-1"></i> Ajukan Cuti Sekarang
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- 
            =======================================================================
            [KOMENTAR RESPONSIVE BEHAVIOR - TAMPILAN TABLET & PC (>= 768px)]
            Breakpoint: >= 768px (Layar Tablet, Laptop, dan Desktop)
            Class CSS: 'up-desktop-table-wrapper' (Tampil di Tablet/PC, disembunyikan di HP)
            =======================================================================
        --}}
        <div class="up-desktop-table-wrapper">
            <table class="up-table">
                <thead>
                    <tr>
                        <th class="up-th-no">No</th>
                        <th style="width: 17%;">Jenis Cuti</th>
                        <th style="width: 20%;">Rentang Tanggal</th>
                        <th class="text-center" style="width: 11%;">Lama Hari</th>
                        <th style="width: 22%;">Alasan &amp; Alamat</th>
                        <th class="text-center" style="width: 18%;">Status Persetujuan</th>
                        <th class="text-center" style="width: 12%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($riwayatCuti as $index => $item)
                        <tr>
                            <!-- Kolom 1: No Urut (Persempit Ringkas 38px) -->
                            <td class="up-col-num">
                                {{ $index + 1 }}
                            </td>

                            <!-- Kolom 2: Jenis Cuti -->
                            <td>
                                <div class="up-table-periode-cell">
                                    <div class="up-periode-title">{{ $item->jenisCuti?->nama ?? 'Cuti' }}</div>
                                    <div class="up-periode-sub">
                                        <span class="up-unit-pill">Kuota: {{ $item->jenisCuti?->lama_hari ?? '-' }} Hari</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Kolom 3: Rentang Tanggal Cuti -->
                            <td>
                                <div class="up-table-date-cell">
                                    <span class="up-table-full-date">{{ $item->periode_formatted }}</span>
                                    <span class="text-muted small">
                                        Diajukan: {{ $item->created_at ? $item->created_at->translatedFormat('d M Y') : '-' }}
                                    </span>
                                </div>
                            </td>

                            <!-- Kolom 4: Durasi Hari Kerja -->
                            <td class="text-center">
                                <span class="up-badge-duration">
                                    <i class="fa fa-calendar-day mr-1"></i>
                                    <strong>{{ $item->jumlah_hari }}</strong> Hari Kerja
                                </span>
                            </td>

                            <!-- Kolom 5: Alasan & Alamat Kontak -->
                            <td>
                                <div class="up-cell-notes">
                                    @if ($item->alasan)
                                        <div class="up-note-alasan">
                                            <strong>Alasan:</strong> {{ $item->alasan }}
                                        </div>
                                    @endif
                                    <div class="up-note-alamat text-muted">
                                        <i class="fa fa-map-marker-alt text-danger mr-1"></i> {{ $item->alamat ?: '-' }}
                                    </div>
                                    <div class="up-note-kontak text-muted">
                                        <i class="fa fa-phone text-success mr-1"></i> {{ $item->telepon ?: '-' }}
                                    </div>
                                </div>
                            </td>

                            <!-- Kolom 6: Status Persetujuan (ACC) -->
                            <td class="text-center">
                                @if ($item->acc)
                                    <div class="d-inline-flex flex-column align-items-center">
                                        <span class="up-status-badge up-status-badge--dibayar">
                                            <i class="fa fa-check-circle mr-1"></i> Disetujui (ACC)
                                        </span>
                                        <span class="text-muted mt-1" style="font-size: 0.72rem;">
                                            oleh {{ $item->approver?->name ?? 'Pimpinan' }}
                                        </span>
                                    </div>
                                    @if ($item->catatan_acc)
                                        <div class="up-tooltip-note" title="{{ $item->catatan_acc }}">
                                            <i class="fa fa-comment-dots text-info"></i> Catatan: {{ Str::limit($item->catatan_acc, 25) }}
                                        </div>
                                    @endif
                                @else
                                    <span class="up-status-badge up-status-badge--draft">
                                        <i class="fa fa-hourglass-half mr-1"></i> Menunggu ACC
                                    </span>
                                @endif
                            </td>

                            <!-- Kolom 7: Aksi -->
                            <td class="text-center">
                                @if (!$item->acc)
                                    <form action="{{ route('user.cuti.destroy', $item->id) }}" method="POST"
                                        onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengajuan cuti ini? Data akan dihapus.');"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="up-btn-action-cancel" title="Batalkan Pengajuan Cuti">
                                            <i class="fa fa-trash-alt mr-1"></i> Batalkan
                                        </button>
                                    </form>
                                @else
                                    <span class="up-badge-locked" title="Sudah disetujui, hubungi admin untuk pembatalan">
                                        <i class="fa fa-lock mr-1"></i> Terkunci
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">
                                <div class="up-empty-state">
                                    <div class="up-empty-icon up-empty-icon--orange">
                                        <i class="pe-7s-date"></i>
                                    </div>
                                    <h3>Belum Ada Permohonan Cuti</h3>
                                    <p>Anda belum memiliki riwayat pengajuan cuti untuk periode {{ $selectedYear ? 'Tahun ' . $selectedYear : 'yang dipilih' }}.</p>
                                    <div class="mt-3">
                                        <a href="{{ route('user.cuti.create') }}" class="up-btn-primary-hero">
                                            <i class="fa fa-plus-circle mr-1"></i> Ajukan Cuti Sekarang
                                        </a>
                                    </div>
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
                <i class="fa fa-calendar-check text-emerald"></i>
                <span>Menampilkan seluruh <strong>{{ $riwayatCuti->count() }}</strong> rekaman pengajuan cuti {{ $selectedYear ? 'Tahun ' . $selectedYear : 'keseluruhan' }}</span>
            </div>
            <div class="up-foot-legend">
                <span class="up-legend-item">
                    <span class="up-tab-dot bg-emerald"></span> Disetujui: <strong>{{ $totalDisetujui }}</strong>
                </span>
                <span class="up-legend-item">
                    <span class="up-tab-dot bg-amber"></span> Menunggu: <strong>{{ $totalMenunggu }}</strong>
                </span>
                <span class="up-legend-item">
                    <span class="up-tab-dot bg-rose"></span> Total Hari: <strong>{{ $totalHariCuti }} Hari Kerja</strong>
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

    /* Flash Alerts */
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

    /* Search Input Wrapper */
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

    /* =========================================================================
       1. HERO HEADER CARD
       ========================================================================= */
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
        background: radial-gradient(circle, rgba(249, 115, 22, 0.22) 0%, rgba(249, 115, 22, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .up-hero-card__decor-2 {
        position: absolute;
        bottom: -30px;
        left: 25%;
        width: 160px;
        height: 160px;
        background: radial-gradient(circle, rgba(234, 88, 12, 0.18) 0%, rgba(234, 88, 12, 0) 70%);
        border-radius: 50%;
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
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        flex-shrink: 0;
    }
    .up-hero-icon--orange {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        color: #ffffff;
        box-shadow: 0 6px 16px rgba(249, 115, 22, 0.35);
    }
    .up-hero-title {
        font-size: 1.45rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        margin: 0 0 4px 0;
        color: #ffffff;
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
    .up-btn-primary-hero {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 18px;
        border-radius: 12px;
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        color: #ffffff !important;
        font-weight: 700;
        font-size: 0.84rem;
        text-decoration: none;
        box-shadow: 0 4px 12px rgba(234, 88, 12, 0.4);
        transition: all 0.2s ease;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .up-btn-primary-hero:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(234, 88, 12, 0.5);
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
    .up-select-wrapper select option {
        background: #ffffff;
        color: #0f172a;
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

    @media (max-width: 767px) {
        .up-hero-card {
            padding: 20px 18px;
        }
        .up-hero-card__content {
            flex-direction: column;
            align-items: flex-start;
        }
        .up-hero-card__actions {
            width: 100%;
        }
        .up-period-filter {
            width: 100%;
        }
        .up-search-input-wrap {
            width: 100%;
        }
        .up-search-input {
            width: 100%;
        }
    }

    /* =========================================================================
       2. REKAPITULASI STATISTIK (4 SUMMARY CARDS)
       ========================================================================= */
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
    .up-stat-percentage--emerald {
        background: #dcfce7;
        color: #15803d;
    }
    .up-stat-percentage--amber {
        background: #fef3c7;
        color: #b45309;
    }
    .up-stat-percentage--rose {
        background: #ffe4e6;
        color: #be123c;
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
    }
    .up-mobile-card__top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 10px;
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
    .up-mobile-time-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
        padding: 8px 10px;
        border-radius: 10px;
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        margin-bottom: 10px;
    }
    .up-time-box {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    .up-time-label {
        font-size: 0.66rem;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 1px;
    }
    .up-time-val {
        font-size: 0.82rem;
        font-weight: 700;
        color: #1e293b;
    }
    .up-mobile-detail-row {
        font-size: 0.76rem;
        margin-bottom: 4px;
        color: #334155;
    }
    .up-detail-label {
        font-weight: 700;
        color: #64748b;
        margin-right: 4px;
    }
    .up-mobile-detail-row--note {
        background: #f0fdf4;
        padding: 6px 10px;
        border-radius: 6px;
        border: 1px solid #bbf7d0;
    }
    .up-mobile-card__bottom {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 10px;
        margin-top: 6px;
        border-top: 1px dashed #e2e8f0;
        font-size: 0.74rem;
    }
    .up-btn-cancel-cuti {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 5px 12px;
        background: #fff1f2;
        color: #e11d48;
        border: 1px solid #fecdd3;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.74rem;
        cursor: pointer;
    }
    .up-btn-cancel-cuti:hover {
        background: #e11d48;
        color: #ffffff;
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

    /* Kolom No Dipersempit Ringkas 38px */
    .up-th-no,
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
        padding: 12px 16px;
        font-size: 0.84rem;
        color: #1e293b;
        vertical-align: middle;
    }

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
        font-weight: 700;
        color: #0f172a;
    }

    .up-badge-duration {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 8px;
        background: #fff7ed;
        color: #ea580c;
        border: 1px solid #fed7aa;
        font-size: 0.78rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .up-cell-notes {
        display: flex;
        flex-direction: column;
        gap: 3px;
        font-size: 0.78rem;
    }
    .up-note-alasan {
        color: #1e293b;
        font-weight: 600;
    }
    .up-note-alamat, .up-note-kontak {
        font-size: 0.74rem;
    }

    /* Badges Status ACC */
    .up-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.72rem;
        font-weight: 700;
        white-space: nowrap;
    }
    .up-status-badge--dibayar {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
    }
    .up-status-badge--draft {
        background: #fef3c7;
        color: #b45309;
        border: 1px solid #fde68a;
    }
    .up-tooltip-note {
        font-size: 0.72rem;
        color: #0369a1;
        background: #f0f9ff;
        padding: 2px 6px;
        border-radius: 4px;
        margin-top: 3px;
        border: 1px solid #bae6fd;
    }

    .up-btn-action-cancel {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 5px 10px;
        border-radius: 6px;
        background: #fff1f2;
        color: #e11d48;
        border: 1px solid #fecdd3;
        font-weight: 700;
        font-size: 0.74rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .up-btn-action-cancel:hover {
        background: #e11d48;
        color: #ffffff;
        box-shadow: 0 2px 6px rgba(225, 29, 72, 0.25);
    }
    .up-badge-locked {
        display: inline-flex;
        align-items: center;
        padding: 4px 8px;
        border-radius: 6px;
        background: #f1f5f9;
        color: #64748b;
        font-size: 0.72rem;
        font-weight: 600;
    }

    /* Empty state & Footer */
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

