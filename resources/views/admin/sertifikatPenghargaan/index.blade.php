@extends('layouts.appui')

@section('title')
    <title>Sertifikat Penghargaan Karyawan - SIPIT</title>
@endsection

@section('content')
<style>
    .stat-card {
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        height: 100%;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.06);
    }
    .stat-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    .badge-soft-primary { background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
    .badge-soft-success { background-color: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .badge-soft-warning { background-color: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
    .badge-soft-info { background-color: #ede9fe; color: #6d28d9; border: 1px solid #ddd6fe; }
    .table td, .table th { vertical-align: middle !important; }
    .action-btn-group {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        flex-wrap: nowrap;
        white-space: nowrap;
    }
    .action-btn-group form { display: inline-flex; margin: 0; }
    .btn-action-icon {
        width: 34px;
        height: 34px;
        min-width: 34px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.82rem;
        border: 1px solid transparent;
        transition: all 0.18s ease;
        cursor: pointer;
        text-decoration: none !important;
        padding: 0;
        flex-shrink: 0;
    }
    .btn-action-icon:hover { transform: translateY(-1px); }
    .btn-action-view { background: #e0f2fe; color: #0284c7; border-color: #bae6fd; }
    .btn-action-view:hover { background: #0284c7; color: #fff; border-color: #0284c7; }
    .btn-action-edit { background: #eef2ff; color: #4f46e5; border-color: #c7d2fe; }
    .btn-action-edit:hover { background: #4f46e5; color: #fff; border-color: #4f46e5; }
    .btn-action-delete { background: #ffe4e6; color: #e11d48; border-color: #fecdd3; }
    .btn-action-delete:hover { background: #e11d48; color: #fff; border-color: #e11d48; }
    .emp-cell { display: flex; align-items: center; gap: 10px; min-width: 210px; }
    .emp-avatar {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.82rem;
        color: #fff;
        background: linear-gradient(135deg, #ea580c 0%, #d97706 100%);
        flex-shrink: 0;
    }
    .btn-file-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 0.76rem;
        font-weight: 700;
        background-color: #fff1f2;
        color: #e11d48;
        border: 1px solid #fecdd3;
        text-decoration: none !important;
        transition: all 0.18s ease;
        white-space: nowrap;
    }
    .btn-file-pill:hover { background-color: #e11d48; color: #fff; border-color: #e11d48; }
    @media (max-width: 767.98px) {
        .filter-actions-wrap { width: 100%; display: flex; gap: 8px; margin-top: 4px; }
        .filter-actions-wrap .btn { flex: 1; justify-content: center; }
    }
</style>

<div class="app-main__inner">
    {{-- Page Header --}}
    <div class="app-page-title mb-3">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon bg-warning text-white shadow-sm" style="border-radius: 12px;">
                    <i class="pe-7s-medal"></i>
                </div>
                <div>
                    <strong>Sertifikat Penghargaan Karyawan</strong>
                    <div class="page-title-subheading">
                        Pencatatan dan pengarsipan sertifikat penghargaan serta prestasi yang diraih oleh karyawan.
                    </div>
                </div>
            </div>
            <div class="page-title-actions">
                <a href="{{ route('sertifikat-penghargaan.create') }}" class="btn btn-primary shadow-sm font-weight-bold" style="border-radius: 9px;">
                    <i class="fa fa-plus-circle mr-1"></i> Tambah Penghargaan
                </a>
            </div>
        </div>
    </div>

    {{-- Alert Notifikasi --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
            <i class="fa fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
            <i class="fa fa-exclamation-triangle mr-2"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Statistik Ringkasan --}}
    <div class="row mb-3">
        <div class="col-md-3 col-sm-6 mb-2">
            <div class="stat-card p-3 d-flex align-items-center" style="border-left: 4px solid #0284c7;">
                <div class="stat-icon-box mr-3" style="background: #e0f2fe; color: #0284c7;">
                    <i class="fa fa-certificate"></i>
                </div>
                <div>
                    <span class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Total Sertifikat</span>
                    <h4 class="font-weight-bold text-dark mb-0 mt-1">{{ number_format($statistik->total_sertifikat ?? 0) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-2">
            <div class="stat-card p-3 d-flex align-items-center" style="border-left: 4px solid #10b981;">
                <div class="stat-icon-box mr-3" style="background: #dcfce7; color: #16a34a;">
                    <i class="fa fa-users"></i>
                </div>
                <div>
                    <span class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Karyawan Berprestasi</span>
                    <h4 class="font-weight-bold text-success mb-0 mt-1">{{ number_format($statistik->total_karyawan_berprestasi ?? 0) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-2">
            <div class="stat-card p-3 d-flex align-items-center" style="border-left: 4px solid #6366f1;">
                <div class="stat-icon-box mr-3" style="background: #ede9fe; color: #6d28d9;">
                    <i class="fa fa-calendar-alt"></i>
                </div>
                <div>
                    <span class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Tahun Terdata</span>
                    <h4 class="font-weight-bold text-info mb-0 mt-1">{{ number_format($statistik->total_tahun_tercatat ?? 0) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-2">
            <div class="stat-card p-3 d-flex align-items-center" style="border-left: 4px solid #f59e0b;">
                <div class="stat-icon-box mr-3" style="background: #fef3c7; color: #d97706;">
                    <i class="fa fa-cloud-upload-alt"></i>
                </div>
                <div>
                    <span class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Berkas Terunggah</span>
                    <h4 class="font-weight-bold text-warning mb-0 mt-1">{{ number_format($statistik->total_berkas_terunggah ?? 0) }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="main-card card border-0 shadow-sm mb-4" style="border-radius: 14px;">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('sertifikat-penghargaan.index') }}" class="row align-items-end">
                <div class="col-lg-6 col-md-6 col-sm-12 mb-2 mb-md-0">
                    <label class="small font-weight-bold text-muted mb-1"><i class="fa fa-search mr-1 text-primary"></i> Pencarian</label>
                    <input type="text" name="search" class="form-control" placeholder="Cari Judul Penghargaan, NIPY, Nama Karyawan, Unit..." value="{{ $filters['search'] ?? '' }}">
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6 mb-2 mb-md-0">
                    <label class="small font-weight-bold text-muted mb-1"><i class="fa fa-calendar-alt mr-1 text-primary"></i> Tahun Penghargaan</label>
                    <input type="number" name="tahun" class="form-control" placeholder="Contoh: {{ date('Y') }}" value="{{ $filters['tahun'] ?? '' }}">
                </div>
                <div class="col-lg-2 col-md-2 col-sm-6 d-flex align-items-center justify-content-md-end">
                    <div class="filter-actions-wrap d-flex align-items-center" style="gap: 6px;">
                        <button type="submit" class="btn btn-primary font-weight-bold" style="white-space: nowrap; border-radius: 8px;">
                            <i class="fas fa-filter mr-1"></i> Filter
                        </button>
                        <a href="{{ route('sertifikat-penghargaan.index') }}" class="btn btn-outline-secondary font-weight-bold" title="Reset Filter" style="white-space: nowrap; border-radius: 8px;">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Data --}}
    <div class="main-card card border-0 shadow-sm mb-4" style="border-radius: 14px; overflow: hidden;">
        <div class="card-header bg-white border-bottom font-weight-bold d-flex flex-wrap justify-content-between align-items-center py-3 px-3">
            <span class="text-dark"><i class="fa fa-award text-warning mr-2"></i>Daftar Sertifikat Penghargaan Karyawan</span>
            <span class="badge badge-soft-warning px-2.5 py-1 font-weight-bold" style="border-radius: 999px;">{{ $penghargaanList->total() }} Data Ditemukan</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="align-middle mb-0 table table-striped table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>Judul Penghargaan</th>
                            <th>Karyawan</th>
                            <th class="text-center" style="width: 110px;">Tahun</th>
                            <th class="text-center" style="width: 145px;">Berkas</th>
                            <th>Keterangan</th>
                            <th class="text-center" style="width: 70px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($penghargaanList as $index => $penghargaan)
                            <tr>
                                <td class="text-center text-muted font-weight-bold">
                                    {{ $penghargaanList->firstItem() + $index }}
                                </td>
                                <td>
                                    <div class="font-weight-bold text-dark" style="font-size: 0.88rem;">
                                        <i class="fa fa-trophy text-warning mr-1.5"></i>{{ $penghargaan->judul_penghargaan }}
                                    </div>
                                    <small class="text-muted">
                                        Dicatat: {{ $penghargaan->created_at?->format('d M Y') }}
                                    </small>
                                </td>
                                <td>
                                    @if($penghargaan->karyawan)
                                        <div class="emp-cell">
                                            <span class="emp-avatar">
                                                {{ strtoupper(mb_substr($penghargaan->karyawan->nama_lengkap, 0, 1)) }}
                                            </span>
                                            <div>
                                                <div class="font-weight-bold text-dark" style="font-size: 0.88rem;">
                                                    {{ $penghargaan->karyawan->nama_lengkap }}{{ $penghargaan->karyawan->gelar ? ', ' . $penghargaan->karyawan->gelar : '' }}
                                                </div>
                                                <small class="text-muted">
                                                    <code>{{ $penghargaan->karyawan->nipy }}</code> &bull; {{ $penghargaan->karyawan->unit ?? 'Tanpa Unit' }}
                                                </small>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-danger font-italic small">NIPY: {{ $penghargaan->karyawan_nipy }} (Data terhapus)</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-soft-primary font-weight-bold px-2.5 py-1" style="border-radius: 6px;">
                                        {{ $penghargaan->tahun }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($penghargaan->berkas)
                                        <a href="{{ route('sertifikat-penghargaan.download', $penghargaan->id) }}"
                                           class="btn-file-pill"
                                           title="Unduh Berkas: {{ $penghargaan->berkas_filename }}">
                                            <i class="fa fa-file-pdf"></i> Unduh Piagam
                                        </a>
                                    @else
                                        <span class="badge badge-light border text-muted px-2 py-1" style="border-radius: 6px;">Belum Ada</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted small">
                                        {{ $penghargaan->keterangan ? Str::limit($penghargaan->keterangan, 50) : '-' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="action-dropdown">
                                        <button type="button" class="btn-action-dots" title="Menu Aksi">
                                            <i class="fa fa-ellipsis-v"></i>
                                        </button>
                                        <div class="action-dropdown-menu">
                                            <a href="{{ route('sertifikat-penghargaan.show', $penghargaan->id) }}" class="action-dropdown-item item-view">
                                                <span class="action-item-icon"><i class="fa fa-eye"></i></span>
                                                <span>Lihat Detail</span>
                                            </a>
                                            <a href="{{ route('sertifikat-penghargaan.edit', $penghargaan->id) }}" class="action-dropdown-item item-edit">
                                                <span class="action-item-icon"><i class="fa fa-edit"></i></span>
                                                <span>Edit Penghargaan</span>
                                            </a>
                                            <div class="action-dropdown-divider"></div>
                                            <form action="{{ route('sertifikat-penghargaan.destroy', $penghargaan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus sertifikat penghargaan ini beserta berkasnya?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-dropdown-item item-delete">
                                                    <span class="action-item-icon"><i class="fa fa-trash"></i></span>
                                                    <span>Hapus Penghargaan</span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fa fa-trophy fa-3x d-block mb-3 text-secondary" style="opacity: 0.45;"></i>
                                    <h6 class="font-weight-bold text-dark">Belum ada data Sertifikat Penghargaan</h6>
                                    <p class="small mb-3">Tidak ditemukan data sertifikat penghargaan yang sesuai dengan filter.</p>
                                    <a href="{{ route('sertifikat-penghargaan.create') }}" class="btn btn-sm btn-primary shadow-sm font-weight-bold" style="border-radius: 8px;">
                                        <i class="fa fa-plus-circle mr-1"></i> Tambah Penghargaan
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($penghargaanList->hasPages())
            <div class="card-footer bg-white border-top d-flex flex-wrap justify-content-between align-items-center py-2.5 px-3 gap-2">
                <span class="small text-muted">
                    Menampilkan {{ $penghargaanList->firstItem() ?? 0 }} - {{ $penghargaanList->lastItem() ?? 0 }} dari {{ $penghargaanList->total() }} data
                </span>
                <div>
                    {{ $penghargaanList->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
