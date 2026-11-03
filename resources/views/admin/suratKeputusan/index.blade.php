@extends('layouts.appui')

@section('title')
    <title>Surat Keputusan (SK) Kepegawaian - SIPIT</title>
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
    .badge-soft-info { background-color: #ede9fe; color: #6d28d9; border: 1px solid #ddd6fe; }
    .badge-soft-warning { background-color: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
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
                <div class="page-title-icon bg-primary text-white shadow-sm" style="border-radius: 12px;">
                    <i class="pe-7s-news-paper"></i>
                </div>
                <div>
                    <strong>Surat Keputusan (SK) Karyawan</strong>
                    <div class="page-title-subheading">
                        Pengelolaan berkas dan administrasi penetapan SK jabatan karyawan per tahun ajaran.
                    </div>
                </div>
            </div>
            <div class="page-title-actions">
                <a href="{{ route('surat-keputusan.create') }}" class="btn btn-primary font-weight-bold shadow-sm" style="border-radius: 9px;">
                    <i class="fa fa-plus-circle mr-1"></i> Tambah SK Baru
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
            <div class="stat-card p-3 d-flex align-items-center" style="border-left: 4px solid #ea580c;">
                <div class="stat-icon-box mr-3" style="background: #ffedd5; color: #ea580c;">
                    <i class="fa fa-file-alt"></i>
                </div>
                <div>
                    <span class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Total SK Terbit</span>
                    <h4 class="mb-0 font-weight-bold text-dark mt-1">{{ number_format($statistik->total_sk ?? 0) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-2">
            <div class="stat-card p-3 d-flex align-items-center" style="border-left: 4px solid #10b981;">
                <div class="stat-icon-box mr-3" style="background: #dcfce7; color: #16a34a;">
                    <i class="fa fa-users"></i>
                </div>
                <div>
                    <span class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Karyawan Ber-SK</span>
                    <h4 class="mb-0 font-weight-bold text-success mt-1">{{ number_format($statistik->total_karyawan_ber_sk ?? 0) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-2">
            <div class="stat-card p-3 d-flex align-items-center" style="border-left: 4px solid #6366f1;">
                <div class="stat-icon-box mr-3" style="background: #ede9fe; color: #6d28d9;">
                    <i class="fa fa-briefcase"></i>
                </div>
                <div>
                    <span class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Variasi Jabatan</span>
                    <h4 class="mb-0 font-weight-bold text-info mt-1">{{ number_format($statistik->total_variasi_jabatan ?? 0) }}</h4>
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
                    <h4 class="mb-0 font-weight-bold text-warning mt-1">{{ number_format($statistik->total_berkas_terunggah ?? 0) }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="main-card card border-0 shadow-sm mb-4" style="border-radius: 14px;">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('surat-keputusan.index') }}" class="row align-items-end">
                <div class="col-lg-4 col-md-4 col-sm-12 mb-2 mb-lg-0">
                    <label class="font-weight-bold small text-muted mb-1"><i class="fa fa-search mr-1 text-primary"></i> Pencarian</label>
                    <input type="text" name="search" class="form-control" placeholder="Cari No. SK, NIPY, Nama, Jabatan..." value="{{ $filters['search'] ?? '' }}">
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 mb-2 mb-lg-0">
                    <label class="font-weight-bold small text-muted mb-1"><i class="fa fa-graduation-cap mr-1 text-primary"></i> Tahun Ajaran</label>
                    <select name="akademik_master_tahun_ajaran_id" class="form-control">
                        <option value="">-- Semua Tahun Ajaran --</option>
                        @foreach($tahunAjaranList as $ta)
                            <option value="{{ $ta->id }}" {{ (isset($filters['akademik_master_tahun_ajaran_id']) && $filters['akademik_master_tahun_ajaran_id'] == $ta->id) ? 'selected' : '' }}>
                                {{ $ta->nama_tahun_ajaran }} ({{ $ta->semester }}) {{ $ta->is_active ? '★ Aktif' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 mb-2 mb-lg-0">
                    <label class="font-weight-bold small text-muted mb-1"><i class="fa fa-briefcase mr-1 text-primary"></i> Jabatan</label>
                    <input type="text" name="jabatan" class="form-control" placeholder="Filter jabatan..." value="{{ $filters['jabatan'] ?? '' }}">
                </div>
                <div class="col-lg-2 col-md-2 col-sm-12 d-flex align-items-center justify-content-md-end">
                    <div class="filter-actions-wrap d-flex align-items-center" style="gap: 6px;">
                        <button type="submit" class="btn btn-primary font-weight-bold" style="white-space: nowrap; border-radius: 8px;">
                            <i class="fas fa-filter mr-1"></i> Filter
                        </button>
                        <a href="{{ route('surat-keputusan.index') }}" class="btn btn-outline-secondary font-weight-bold" title="Reset Filter" style="white-space: nowrap; border-radius: 8px;">
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
            <span class="text-dark"><i class="fa fa-file-signature text-primary mr-2"></i>Daftar Surat Keputusan (SK) Karyawan</span>
            <span class="badge badge-soft-primary px-2.5 py-1 font-weight-bold" style="border-radius: 999px;">{{ $suratKeputusanList->total() }} Data Ditemukan</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>Nomor SK &amp; Tanggal</th>
                            <th>Karyawan</th>
                            <th>Tahun Ajaran</th>
                            <th>Jabatan SK</th>
                            <th class="text-center" style="width: 140px;">Berkas SK</th>
                            <th class="text-center" style="width: 70px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suratKeputusanList as $index => $sk)
                            <tr>
                                <td class="text-center text-muted font-weight-bold">
                                    {{ $suratKeputusanList->firstItem() + $index }}
                                </td>
                                <td>
                                    <div class="font-weight-bold text-dark" style="font-size: 0.88rem;">
                                        {{ $sk->nomor_sk ?: 'Nomor SK belum dicatat' }}
                                    </div>
                                    <small class="text-muted">
                                        <i class="fa fa-calendar-alt mr-1 text-primary"></i>
                                        {{ $sk->formatted_tanggal_sk ?: '-' }}
                                    </small>
                                </td>
                                <td>
                                    @if($sk->karyawan)
                                        <div class="emp-cell">
                                            <span class="emp-avatar">
                                                {{ strtoupper(mb_substr($sk->karyawan->nama_lengkap, 0, 1)) }}
                                            </span>
                                            <div>
                                                <div class="font-weight-bold text-dark" style="font-size: 0.88rem;">
                                                    {{ $sk->karyawan->nama_lengkap }}{{ $sk->karyawan->gelar ? ', ' . $sk->karyawan->gelar : '' }}
                                                </div>
                                                <small class="text-muted">
                                                    <code>{{ $sk->karyawan->nipy }}</code> &bull; {{ $sk->karyawan->unit ?? 'Tanpa Unit' }}
                                                </small>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-danger font-italic small">NIPY: {{ $sk->karyawan_nipy }} (Data terhapus)</span>
                                    @endif
                                </td>
                                <td>
                                    @if($sk->tahunAjaran)
                                        <span class="badge {{ $sk->tahunAjaran->is_active ? 'badge-soft-success' : 'badge-soft-primary' }} font-weight-bold px-2.5 py-1" style="border-radius: 6px;">
                                            {{ $sk->tahunAjaran->nama_tahun_ajaran }} ({{ $sk->tahunAjaran->semester }})
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-soft-info font-weight-bold px-2.5 py-1" style="border-radius: 6px;">
                                        <i class="fa fa-user-tie mr-1"></i>{{ $sk->jabatan }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($sk->berkas)
                                        <a href="{{ route('surat-keputusan.download', $sk->id) }}"
                                           class="btn-file-pill"
                                           title="Unduh Berkas SK: {{ $sk->berkas_filename }}">
                                            <i class="fa fa-file-pdf"></i> Unduh SK
                                        </a>
                                    @else
                                        <span class="badge badge-light border text-muted px-2 py-1" style="border-radius: 6px;">Belum Ada</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="action-dropdown">
                                        <button type="button" class="btn-action-dots" title="Menu Aksi">
                                            <i class="fa fa-ellipsis-v"></i>
                                        </button>
                                        <div class="action-dropdown-menu">
                                            <a href="{{ route('surat-keputusan.show', $sk->id) }}" class="action-dropdown-item item-view">
                                                <span class="action-item-icon"><i class="fa fa-eye"></i></span>
                                                <span>Lihat Detail SK</span>
                                            </a>
                                            <a href="{{ route('surat-keputusan.edit', $sk->id) }}" class="action-dropdown-item item-edit">
                                                <span class="action-item-icon"><i class="fa fa-edit"></i></span>
                                                <span>Edit SK</span>
                                            </a>
                                            <div class="action-dropdown-divider"></div>
                                            <form action="{{ route('surat-keputusan.destroy', $sk->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus SK ini beserta berkas fisiknya?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-dropdown-item item-delete">
                                                    <span class="action-item-icon"><i class="fa fa-trash"></i></span>
                                                    <span>Hapus SK</span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fa fa-folder-open fa-3x mb-3 text-secondary" style="opacity: 0.45;"></i>
                                        <h6 class="font-weight-bold text-dark">Belum ada data Surat Keputusan</h6>
                                        <p class="small mb-3">Tidak ditemukan data yang sesuai dengan kriteria filter Anda.</p>
                                        <a href="{{ route('surat-keputusan.create') }}" class="btn btn-sm btn-primary shadow-sm font-weight-bold" style="border-radius: 8px;">
                                            <i class="fa fa-plus-circle mr-1"></i> Tambah SK Baru
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($suratKeputusanList->hasPages())
            <div class="card-footer bg-white border-top d-flex flex-wrap justify-content-between align-items-center py-2.5 px-3 gap-2">
                <span class="small text-muted">
                    Menampilkan {{ $suratKeputusanList->firstItem() ?? 0 }} - {{ $suratKeputusanList->lastItem() ?? 0 }} dari {{ $suratKeputusanList->total() }} data
                </span>
                <div>
                    {{ $suratKeputusanList->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
