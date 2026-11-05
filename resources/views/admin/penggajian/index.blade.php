@extends('layouts.appui')

@section('title')
    <title>Penggajian & Kompensasi - SIPIT</title>
@endsection

@section('content')
@php
    $rp = fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
    $currentJenis = $filters['jenis'] ?? '';
    $tabs = [
        ''      => ['label' => 'Semua Slip', 'icon' => 'fa-layer-group'],
        'gaji'  => ['label' => 'Gaji Bulanan', 'icon' => 'fa-money-bill-wave'],
        'thr'   => ['label' => 'Tunjangan Hari Raya (THR)', 'icon' => 'fa-gift'],
        'bonus' => ['label' => 'Bonus Kinerja', 'icon' => 'fa-award'],
    ];
@endphp
<style>
    .stat-card { border-radius: 14px; border: 1px solid #e2e8f0; background: #fff; box-shadow: 0 2px 8px rgba(15, 23, 42, .03); transition: transform .2s, box-shadow .2s; height: 100%; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 18px rgba(15, 23, 42, .06); }
    .stat-icon-box { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; }
    .table td, .table th { vertical-align: middle !important; }
    .action-btn-group { display: inline-flex; align-items: center; justify-content: center; gap: 6px; flex-wrap: nowrap; white-space: nowrap; }
    .action-btn-group form { display: inline-flex; margin: 0; }
    .btn-action-icon { width: 34px; height: 34px; min-width: 34px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.82rem; border: 1px solid transparent; transition: all 0.18s ease; cursor: pointer; text-decoration: none !important; padding: 0; flex-shrink: 0; }
    .btn-action-icon:hover { transform: translateY(-1px); }
    .btn-action-view { background: #e0f2fe; color: #0284c7; border-color: #bae6fd; }
    .btn-action-view:hover { background: #0284c7; color: #fff; border-color: #0284c7; }
    .btn-action-edit { background: #eef2ff; color: #4f46e5; border-color: #c7d2fe; }
    .btn-action-edit:hover { background: #4f46e5; color: #fff; border-color: #4f46e5; }
    .btn-action-delete { background: #ffe4e6; color: #e11d48; border-color: #fecdd3; }
    .btn-action-delete:hover { background: #e11d48; color: #fff; border-color: #e11d48; }
    .btn-action-locked { background: #f1f5f9; color: #94a3b8; border-color: #e2e8f0; cursor: not-allowed; }
    @media (max-width: 767.98px) {
        .filter-actions-wrap { width: 100%; display: flex; gap: 8px; margin-top: 4px; }
        .filter-actions-wrap .btn { flex: 1; justify-content: center; }
    }

    /* Modal Import Gaji - Self-contained & Reliable */
    .modal-import-backdrop {
        position: fixed;
        inset: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(4px);
        z-index: 99999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        overflow-y: auto;
    }
    .modal-import-backdrop.show {
        display: flex !important;
    }
    .modal-import-dialog {
        position: relative;
        width: 100%;
        max-width: 540px;
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        overflow: hidden;
        animation: modalScaleIn 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        margin: auto;
    }
    @keyframes modalScaleIn {
        from { opacity: 0; transform: scale(0.96) translateY(-8px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
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
        width: 100%;
        transition: all 0.2s ease;
        color: #1e293b;
        background-color: #fff;
    }
    .form-filter-control:focus {
        border-color: #ea580c;
        box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.15);
        outline: none;
    }
</style>

<div class="app-main__inner">
    <div class="app-page-title mb-3">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon text-white shadow-sm"
                    style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 14px;">
                    <i class="pe-7s-cash" style="color: white;"></i>
                </div>
                <div>
                    Penggajian &amp; Kompensasi
                    <div class="page-title-subheading">Slip gaji bulanan, Tunjangan Hari Raya (THR), dan Bonus Kinerja karyawan.</div>
                </div>
            </div>
            <div class="page-title-actions d-flex flex-wrap align-items-center" style="gap: 8px;">
                <a href="{{ route('penggajian.template') }}" class="btn btn-outline-secondary bg-white font-weight-bold shadow-sm" style="border-radius: 8px; height: 38px; display: inline-flex; align-items: center;" title="Unduh Template Excel">
                    <i class="fa fa-download mr-1.5 text-success"></i> Format Excel
                </a>
                <button type="button" class="btn btn-outline-success bg-white font-weight-bold shadow-sm" style="border-radius: 8px; height: 38px; display: inline-flex; align-items: center;" id="btnOpenModalImport" onclick="openImportModal()">
                    <i class="fa fa-file-excel mr-1.5"></i> Upload Excel
                </button>
                <a href="{{ route('penggajian.create', ['jenis' => $currentJenis ?: 'gaji']) }}" class="btn text-white font-weight-bold shadow-sm"
                    style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 8px; height: 38px; display: inline-flex; align-items: center;">
                    <i class="fa fa-plus-circle mr-1.5"></i> Tambah Slip
                </a>
            </div>
        </div>
    </div>

    {{-- Tabs Jenis Pembayaran --}}
    <div class="d-flex flex-wrap align-items-center mb-3" style="gap: 8px;">
        @foreach ($tabs as $key => $tab)
            @php
                $tabParams = array_merge($filters, ['jenis' => $key]);
                if (empty($key)) unset($tabParams['jenis']);
                $isActive = $currentJenis === $key;
            @endphp
            <a href="{{ route('penggajian.index', $tabParams) }}"
                class="btn btn-sm font-weight-bold shadow-sm d-flex align-items-center {{ $isActive ? 'btn-primary' : 'btn-white text-secondary border' }}"
                style="border-radius: 9999px; padding: 7px 18px; font-size: 0.85rem;">
                <i class="fa {{ $tab['icon'] }} mr-2 {{ $isActive ? 'text-white' : 'text-primary' }}"></i>
                <span>{{ $tab['label'] }}</span>
            </a>
        @endforeach
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3">
            <i class="fa fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3">
            <i class="fa fa-exclamation-triangle mr-2"></i>{{ session('error') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    {{-- Statistik --}}
    <div class="row mb-3">
        @foreach ([
            ['Total Slip', number_format($statistik->total_slip), '#0284c7', 'fa-file-invoice', 'text-primary'],
            ['Total Penerimaan', $rp($statistik->total_penerimaan), '#10b981', 'fa-arrow-down', 'text-success'],
            ['Total Potongan', $rp($statistik->total_potongan), '#ef4444', 'fa-arrow-up', 'text-danger'],
            ['Total Bersih Dibayarkan', $rp($statistik->total_gaji_bersih), '#ea580c', 'fa-wallet', 'text-warning'],
        ] as [$label, $value, $color, $icon, $text])
            <div class="col-xl-3 col-md-6 mb-2">
                <div class="stat-card p-3 d-flex align-items-center" style="border-left: 4px solid {{ $color }};">
                    <div class="rounded-circle p-3 mr-3 bg-light {{ $text }}"><i class="fa {{ $icon }} fa-lg"></i></div>
                    <div>
                        <span class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: .5px;">{{ $label }}</span>
                        <h5 class="mb-0 font-weight-bold text-dark mt-1">{{ $value }}</h5>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- FILTER CARD -->
    <div class="main-card card mb-3 shadow-sm border-0" style="border-radius: 12px;">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('penggajian.index') }}" class="w-100 mb-0">
                <div class="row align-items-end">
                    <div class="col-xl-3 col-lg-3 col-md-6 col-12 mb-2 mb-md-0">
                        <label class="form-filter-label">
                            <i class="fa fa-search mr-1 text-warning"></i> Pencarian
                        </label>
                        <input type="text" name="search" class="form-control form-filter-control" placeholder="Cari NIPY, nama, unit, jabatan..." value="{{ $filters['search'] ?? '' }}">
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-6 col-12 mb-2 mb-md-0">
                        <label class="form-filter-label">
                            <i class="fa fa-calendar-alt mr-1 text-warning"></i>
                            @if ($currentJenis === 'thr')
                                Tahun THR
                            @elseif ($currentJenis === 'bonus')
                                Tahun Ajaran
                            @else
                                Periode Bulan
                            @endif
                        </label>
                        <select name="kepegawaian_master_bulan_id" class="form-control form-filter-control font-weight-bold" required>
                            @foreach ($bulanList as $b)
                                <option value="{{ $b->id }}" @selected(($filters['kepegawaian_master_bulan_id'] ?? '') == $b->id)>
                                    @if ($currentJenis === 'thr')
                                         Tahun {{ $b->created_at ? $b->created_at->format('Y') : date('Y') }}{{ $b->status ? ' [Aktif]' : '' }}
                                    @elseif ($currentJenis === 'bonus')
                                        TA {{ $b->tahunAjaran ? $b->tahunAjaran->nama_tahun_ajaran : date('Y') }}{{ $b->status ? ' [Aktif]' : '' }}
                                    @else
                                        {{ $b->nama_bulan }}{{ $b->tahunAjaran ? ' - ' . $b->tahunAjaran->nama_tahun_ajaran : '' }}{{ $b->status ? ' [Aktif]' : '' }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 mb-2 mb-md-0">
                        <label class="form-filter-label">
                            <i class="fa fa-tags mr-1 text-warning"></i> Jenis Pembayaran
                        </label>
                        <select name="jenis" class="form-control form-filter-control">
                            <option value="">-- Semua Jenis --</option>
                            @foreach ($jenisList as $key => $label)
                                <option value="{{ $key }}" @selected(($filters['jenis'] ?? '') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 mb-2 mb-md-0">
                        <label class="form-filter-label">
                            <i class="fa fa-check-circle mr-1 text-warning"></i> Status
                        </label>
                        <select name="status" class="form-control form-filter-control">
                            <option value="">-- Semua --</option>
                            @foreach ($statusList as $key => $label)
                                <option value="{{ $key }}" @selected(($filters['status'] ?? '') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xl-2 col-lg-2 col-md-4 col-12 mb-2 mb-md-0">
                        <div class="d-flex w-100" style="gap: 6px; width: 100%;">
                            <button type="submit" class="btn text-white font-weight-bold d-flex align-items-center justify-content-center"
                                style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 8px; height: 38px; border: none; flex: 1 1 auto; width: 100%; box-shadow: 0 2px 6px rgba(234, 88, 12, 0.2);">
                                <i class="fa fa-filter mr-1"></i> Filter
                            </button>
                            <a href="{{ route('penggajian.index') }}" class="btn btn-light border font-weight-bold shadow-sm d-flex align-items-center justify-content-center"
                                style="border-radius: 8px; height: 38px; width: 38px; min-width: 38px; flex-shrink: 0;" title="Reset Filter">
                                <i class="fa fa-undo"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="main-card card border-0 shadow-sm mb-4" style="border-radius: 14px; overflow: hidden;">
        <div class="card-header bg-white border-bottom font-weight-bold d-flex flex-wrap justify-content-between align-items-center py-3 px-3">
            <span class="text-dark">
                <i class="fa fa-file-invoice-dollar text-success mr-2"></i>Daftar Slip
                @if (!empty($currentJenis))
                    <span class="text-primary font-weight-normal">({{ $jenisList[$currentJenis] ?? '' }})</span>
                @endif
            </span>
            <span class="badge badge-primary px-2.5 py-1" style="border-radius: 999px;">{{ $penggajianList->total() }} Data</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>Karyawan</th>
                            <th class="text-center" style="width: 140px;">
                                @if ($currentJenis === 'thr')
                                    Tahun THR
                                @elseif ($currentJenis === 'bonus')
                                    Tahun Ajaran
                                @else
                                    Periode
                                @endif
                            </th>
                            <th class="text-center" style="width: 120px;">Jenis</th>
                            <th class="text-right">Penerimaan</th>
                            <th class="text-right">Potongan</th>
                            <th class="text-right">Gaji Bersih</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" style="width: 70px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($penggajianList as $i => $item)
                            <tr>
                                <td class="text-center text-muted font-weight-bold">{{ $penggajianList->firstItem() + $i }}</td>
                                <td>
                                    <div class="d-flex align-items-center" style="gap: 10px; min-width: 210px;">
                                        @if ($item->karyawan?->foto_url)
                                            <img src="{{ $item->karyawan->foto_url }}" alt="Foto" style="width: 38px; height: 38px; object-fit: cover; border-radius: 10px; flex-shrink: 0;">
                                        @else
                                            <span class="d-inline-flex align-items-center justify-content-center text-white font-weight-bold"
                                                style="width: 38px; height: 38px; border-radius: 10px; background: linear-gradient(135deg,#ea580c,#d97706); font-size: .82rem; flex-shrink: 0;">
                                                {{ strtoupper(mb_substr($item->karyawan?->nama_lengkap ?? '?', 0, 1)) }}
                                            </span>
                                        @endif
                                        <div>
                                            <div class="font-weight-bold text-dark" style="font-size: 0.88rem;">{{ $item->karyawan?->nama_lengkap ?? 'Data terhapus' }}</div>
                                            <small class="text-muted"><code>{{ $item->karyawan_nipy }}</code> &bull; {{ $item->unit ?: '-' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if ($item->jenis === 'thr')
                                        <div class="text-center">
                                            <span class="badge badge-success border font-weight-bold px-2.5 py-1" style="font-size: .8rem; border-radius: 6px;">
                                                Tahun {{ $item->tahun_thr }}
                                            </span>
                                        </div>
                                    @elseif ($item->jenis === 'bonus')
                                        <div class="text-center">
                                            <span class="badge badge-warning border text-dark font-weight-bold px-2.5 py-1" style="font-size: .8rem; border-radius: 6px;">
                                                TA {{ $item->bulan?->tahunAjaran?->nama_tahun_ajaran ?? date('Y') }}
                                            </span>
                                        </div>
                                    @else
                                        <div class="text-center">
                                            <span class="badge badge-light border font-weight-bold px-2.5 py-1" style="font-size: .8rem; border-radius: 6px;">
                                                {{ $item->bulan?->nama_bulan ?? '-' }}
                                            </span>
                                            @if ($item->bulan?->tahunAjaran)
                                                <div class="small text-muted" style="font-size: 11px;">{{ $item->bulan->tahunAjaran->nama_tahun_ajaran }}</div>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $item->jenis_badge_class }} px-2.5 py-1 font-weight-bold" style="border-radius: 6px;">
                                        {{ $item->jenis_short_label }}
                                    </span>
                                </td>
                                <td class="text-right text-success font-weight-semibold" style="white-space: nowrap;">{{ $rp($item->penerimaan) }}</td>
                                <td class="text-right text-danger font-weight-semibold" style="white-space: nowrap;">{{ $rp($item->potongan) }}</td>
                                <td class="text-right font-weight-bold text-dark" style="white-space: nowrap;">{{ $rp($item->gaji_bersih) }}</td>
                                <td class="text-center"><span class="badge {{ $item->status_badge_class }} px-2.5 py-1" style="border-radius: 6px;">{{ $item->status_label }}</span></td>
                                <td class="text-center">
                                    <div class="action-dropdown">
                                        <button type="button" class="btn-action-dots" title="Menu Aksi">
                                            <i class="fa fa-ellipsis-v"></i>
                                        </button>
                                        <div class="action-dropdown-menu">
                                            <a href="{{ route('penggajian.show', $item) }}" class="action-dropdown-item item-view">
                                                <span class="action-item-icon"><i class="fa fa-eye"></i></span>
                                                <span>Lihat Slip</span>
                                            </a>
                                            @if ($item->status !== 'dibayar')
                                                <a href="{{ route('penggajian.edit', $item) }}" class="action-dropdown-item item-edit">
                                                    <span class="action-item-icon"><i class="fa fa-edit"></i></span>
                                                    <span>Edit Slip</span>
                                                </a>
                                                <div class="action-dropdown-divider"></div>
                                                <form action="{{ route('penggajian.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus slip ini?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="action-dropdown-item item-delete">
                                                        <span class="action-item-icon"><i class="fa fa-trash"></i></span>
                                                        <span>Hapus Slip</span>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="action-dropdown-item item-locked" title="Terkunci (sudah dibayar)">
                                                    <span class="action-item-icon"><i class="fa fa-lock"></i></span>
                                                    <span>Terkunci (Dibayar)</span>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="fa fa-folder-open fa-3x mb-3 text-secondary" style="opacity: 0.45;"></i>
                                    <h6 class="font-weight-bold text-dark">Belum ada slip pembayaran</h6>
                                    <p class="small mb-3">Tidak ada data yang sesuai kriteria filter.</p>
                                    <a href="{{ route('penggajian.create', ['jenis' => $currentJenis ?: 'gaji']) }}" class="btn btn-sm btn-primary font-weight-bold" style="border-radius: 8px;">
                                        <i class="fa fa-plus-circle mr-1"></i> Tambah Slip
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($penggajianList->hasPages())
            <div class="card-footer bg-white border-top d-flex flex-wrap justify-content-between align-items-center py-2.5 px-3 gap-2">
                <span class="small text-muted">Menampilkan {{ $penggajianList->firstItem() }} - {{ $penggajianList->lastItem() }} dari {{ $penggajianList->total() }} data</span>
                <div>{{ $penggajianList->links() }}</div>
            </div>
        @endif
    </div>
</div>

{{-- MODAL UPLOAD EXCEL PENGGAJIAN --}}
<div id="modalImportGaji" class="modal-import-backdrop" onclick="handleBackdropClick(event)">
    <div class="modal-import-dialog" onclick="event.stopPropagation()">
        <form method="POST" action="{{ route('penggajian.import') }}" enctype="multipart/form-data">
            @csrf
            <div class="d-flex justify-content-between align-items-center p-3 px-4 border-bottom bg-light">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle p-2 bg-success text-white mr-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="fa fa-file-excel"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 font-weight-bold text-dark">Upload Excel Pembayaran</h6>
                        <small class="text-muted">Import massal Gaji Bulanan, THR, atau Bonus Kinerja</small>
                    </div>
                </div>
                <button type="button" class="close text-muted" onclick="closeImportModal()" style="font-size: 1.5rem; line-height: 1; outline: none; border: none; background: transparent;" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>

            <div class="p-4">
                <div class="alert alert-info border-0 shadow-sm small mb-3" style="border-radius: 10px; background: #eff6ff; color: #1e40af;">
                    <div class="d-flex align-items-start">
                        <i class="fa fa-info-circle fa-lg mr-2 mt-1 text-primary"></i>
                        <div>
                            <strong>Petunjuk:</strong> Unduh file template <code>.xlsx</code> yang sudah memuat NIPY dan nama karyawan, isi nominal komponen, lalu unggah di sini.
                            <div class="mt-2">
                                <a href="{{ route('penggajian.template') }}" class="btn btn-sm btn-outline-primary font-weight-bold">
                                    <i class="fa fa-download mr-1"></i> Unduh Format Excel Terbaru
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger small mb-3">{{ $errors->first() }}</div>
                @endif

                <div class="form-group mb-3">
                    <label class="font-weight-bold small text-dark mb-1">Jenis Pembayaran <span class="text-danger">*</span></label>
                    <select name="jenis" class="form-control" required style="border-radius: 8px;">
                        @foreach ($jenisList as $key => $label)
                            <option value="{{ $key }}" @selected(($filters['jenis'] ?? 'gaji') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Pilih apakah file Excel ini untuk Gaji Bulanan, THR, atau Bonus Kinerja.</small>
                </div>

                <div class="form-group mb-3">
                    <label class="font-weight-bold small text-dark mb-1" id="lblModalPeriode">
                        @if (($filters['jenis'] ?? 'gaji') === 'thr')
                            Tahun THR <span class="text-danger">*</span>
                        @elseif (($filters['jenis'] ?? 'gaji') === 'bonus')
                            Tahun Ajaran <span class="text-danger">*</span>
                        @else
                            Periode Bulan <span class="text-danger">*</span>
                        @endif
                    </label>
                    <select name="kepegawaian_master_bulan_id" id="modalBulanSelect" class="form-control font-weight-bold" required style="border-radius: 8px;">
                        @foreach ($bulanList as $b)
                            <option value="{{ $b->id }}"
                                data-gaji="{{ $b->nama_bulan }}{{ $b->tahunAjaran ? ' - ' . $b->tahunAjaran->nama_tahun_ajaran : '' }}{{ $b->status ? ' [Aktif]' : '' }}"
                                data-thr="Tahun {{ $b->created_at ? $b->created_at->format('Y') : date('Y') }}{{ $b->status ? ' [Aktif]' : '' }}"
                                data-bonus="TA {{ $b->tahunAjaran ? $b->tahunAjaran->nama_tahun_ajaran : date('Y') }}{{ $b->status ? ' [Aktif]' : '' }}"
                                @selected(($filters['kepegawaian_master_bulan_id'] ?? '') == $b->id)>
                                @if (($filters['jenis'] ?? 'gaji') === 'thr')
                                    Tahun {{ $b->created_at ? $b->created_at->format('Y') : date('Y') }}{{ $b->status ? ' [Aktif]' : '' }}
                                @elseif (($filters['jenis'] ?? 'gaji') === 'bonus')
                                    TA {{ $b->tahunAjaran ? $b->tahunAjaran->nama_tahun_ajaran : date('Y') }}{{ $b->status ? ' [Aktif]' : '' }}
                                @else
                                    {{ $b->nama_bulan }}{{ $b->tahunAjaran ? ' - ' . $b->tahunAjaran->nama_tahun_ajaran : '' }}{{ $b->status ? ' [Aktif]' : '' }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label class="font-weight-bold small text-dark mb-1">Status Slip Baru <span class="text-danger">*</span></label>
                    <select name="status" class="form-control" required style="border-radius: 8px;">
                        <option value="draft" selected>Draft (Dapat ditinjau / diedit kembali)</option>
                        <option value="final">Final (Slip siap diproses)</option>
                    </select>
                    <small class="text-muted">Catatan: Slip berstatus "Dibayar" akan otomatis dilewati agar aman.</small>
                </div>

                <div class="form-group mb-0">
                    <label class="font-weight-bold small text-dark mb-1">Pilih File Excel (.xlsx, .xls, .csv) <span class="text-danger">*</span></label>
                    <input type="file" name="file" class="form-control-file border p-2 w-100" accept=".xlsx,.xls,.csv" required style="border-radius: 8px; background: #f8fafc;">
                    <small class="text-muted d-block mt-1">Maksimal ukuran file: 5 MB.</small>
                </div>
            </div>

            <div class="modal-footer bg-light p-3 px-4 d-flex justify-content-end" style="border-top: 1px solid #f1f5f9;">
                <button type="button" class="btn btn-secondary font-weight-bold px-3" onclick="closeImportModal()">
                    Batal
                </button>
                <button type="submit" class="btn btn-success font-weight-bold px-4">
                    <i class="fa fa-upload mr-1"></i> Mulai Import
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<script>
    function openImportModal() {
        var modal = document.getElementById('modalImportGaji');
        if (modal) {
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeImportModal() {
        var modal = document.getElementById('modalImportGaji');
        if (modal) {
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }
    }

    function handleBackdropClick(event) {
        if (event.target === document.getElementById('modalImportGaji')) {
            closeImportModal();
        }
    }

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeImportModal();
        }
    });

    // Update label & opsi modal saat pilihan jenis pembayaran diganti
    document.querySelector('#modalImportGaji select[name="jenis"]')?.addEventListener('change', function() {
        var jenis = this.value;
        var lbl = document.getElementById('lblModalPeriode');
        var select = document.getElementById('modalBulanSelect');
        if (lbl) {
            if (jenis === 'thr') {
                lbl.innerHTML = 'Tahun THR <span class="text-danger">*</span>';
            } else if (jenis === 'bonus') {
                lbl.innerHTML = 'Tahun Ajaran <span class="text-danger">*</span>';
            } else {
                lbl.innerHTML = 'Periode Bulan <span class="text-danger">*</span>';
            }
        }
        if (select) {
            Array.from(select.options).forEach(function(opt) {
                if (jenis === 'thr') {
                    opt.textContent = opt.getAttribute('data-thr') || opt.textContent;
                } else if (jenis === 'bonus') {
                    opt.textContent = opt.getAttribute('data-bonus') || opt.textContent;
                } else {
                    opt.textContent = opt.getAttribute('data-gaji') || opt.textContent;
                }
            });
        }
    });

    if (typeof jQuery !== 'undefined') {
        jQuery(document).on('click', '[data-target="#modalImportGaji"], [data-toggle="modal"][data-target="#modalImportGaji"]', function(e) {
            e.preventDefault();
            openImportModal();
        });
    }
</script>
@endsection
