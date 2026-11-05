@extends('layouts.appui')

@section('title')
    <title>Tupoksi Karyawan - SIPIT</title>
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
    {{-- Page Header --}}
    <div class="app-page-title mb-3">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon text-white shadow-sm"
                    style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 14px;">
                    <i class="pe-7s-note2" style="color: white;"></i>
                </div>
                <div>
                    Tugas Pokok dan Fungsi (Tupoksi) Karyawan
                    <div class="page-title-subheading">
                        Pengelolaan dan pengarsipan berkas dokumen penetapan tugas pokok &amp; fungsi karyawan per tahun ajaran.
                    </div>
                </div>
            </div>
            <div class="page-title-actions d-flex align-items-center" style="gap: 8px;">
                <a href="{{ route('tupoksi.create') }}" class="btn text-white shadow-sm font-weight-bold"
                    style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 8px;">
                    <i class="fa fa-plus-circle mr-1"></i> Tambah Tupoksi Baru
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
        <div class="col-md-4 mb-2">
            <div class="stat-card p-3 d-flex align-items-center" style="border-left: 4px solid #0284c7;">
                <div class="stat-icon-box mr-3" style="background: #e0f2fe; color: #0284c7;">
                    <i class="fa fa-id-badge"></i>
                </div>
                <div>
                    <span class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Total Tupoksi</span>
                    <h4 class="mb-0 font-weight-bold text-dark mt-1">{{ number_format($statistik->total_tupoksi ?? 0) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-2">
            <div class="stat-card p-3 d-flex align-items-center" style="border-left: 4px solid #10b981;">
                <div class="stat-icon-box mr-3" style="background: #dcfce7; color: #16a34a;">
                    <i class="fa fa-users"></i>
                </div>
                <div>
                    <span class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Karyawan Memiliki Tupoksi</span>
                    <h4 class="mb-0 font-weight-bold text-success mt-1">{{ number_format($statistik->total_karyawan_memiliki_tupoksi ?? 0) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-2">
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

    <!-- FILTER CARD -->
    <div class="main-card card mb-3 shadow-sm border-0" style="border-radius: 12px;">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('tupoksi.index') }}" class="w-100 mb-0">
                <div class="row align-items-end">
                    <div class="col-md-4 col-sm-12 mb-2 mb-md-0">
                        <label class="form-filter-label">
                            <i class="fa fa-search mr-1 text-warning"></i> Pencarian
                        </label>
                        <input type="text" name="search" class="form-control form-filter-control" placeholder="Cari NIPY, Nama, Jabatan, Unit..." value="{{ $filters['search'] ?? '' }}">
                    </div>
                    <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                        <label class="form-filter-label">
                            <i class="fa fa-graduation-cap mr-1 text-warning"></i> Tahun Ajaran
                        </label>
                        <select name="akademik_master_tahun_ajaran_id" class="form-control form-filter-control">
                            <option value="">-- Semua Tahun Ajaran --</option>
                            @foreach($tahunAjaranList as $ta)
                                <option value="{{ $ta->id }}" {{ (isset($filters['akademik_master_tahun_ajaran_id']) && $filters['akademik_master_tahun_ajaran_id'] == $ta->id) ? 'selected' : '' }}>
                                    {{ $ta->nama_tahun_ajaran }} ({{ $ta->semester }}) {{ $ta->is_active ? '★ Aktif' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                        <label class="form-filter-label">
                            <i class="fa fa-briefcase mr-1 text-warning"></i> Jabatan
                        </label>
                        <input type="text" name="jabatan" class="form-control form-filter-control" placeholder="Filter nama jabatan..." value="{{ $filters['jabatan'] ?? '' }}">
                    </div>
                    <div class="col-md-2 col-sm-12 mb-2 mb-md-0">
                        <div class="d-flex w-100" style="gap: 6px; width: 100%;">
                            <button type="submit" class="btn text-white font-weight-bold d-flex align-items-center justify-content-center"
                                style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 8px; height: 38px; border: none; flex: 1 1 auto; width: 100%; box-shadow: 0 2px 6px rgba(234, 88, 12, 0.2);">
                                <i class="fa fa-filter mr-1"></i> Filter
                            </button>
                            <a href="{{ route('tupoksi.index') }}" class="btn btn-light border font-weight-bold shadow-sm d-flex align-items-center justify-content-center"
                                style="border-radius: 8px; height: 38px; width: 38px; min-width: 38px; flex-shrink: 0;" title="Reset Filter">
                                <i class="fa fa-undo"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Data --}}
    <div class="main-card card border-0 shadow-sm mb-4" style="border-radius: 14px; overflow: hidden;">
        <div class="card-header bg-white border-bottom font-weight-bold d-flex flex-wrap justify-content-between align-items-center py-3 px-3">
            <span class="text-dark"><i class="fa fa-tasks text-primary mr-2"></i>Daftar Tupoksi Karyawan</span>
            <span class="badge badge-soft-primary px-2.5 py-1 font-weight-bold" style="border-radius: 999px;">{{ $tupoksiList->total() }} Data Ditemukan</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>Karyawan</th>
                            <th>Jabatan / Tugas Pokok</th>
                            <th>Tahun Ajaran</th>
                            <th>Keterangan</th>
                            <th class="text-center" style="width: 145px;">Berkas Tupoksi</th>
                            <th class="text-center" style="width: 70px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tupoksiList as $index => $tupoksi)
                            <tr>
                                <td class="text-center text-muted font-weight-bold">
                                    {{ $tupoksiList->firstItem() + $index }}
                                </td>
                                <td>
                                    @if($tupoksi->karyawan)
                                        <div class="emp-cell">
                                            <span class="emp-avatar">
                                                {{ strtoupper(mb_substr($tupoksi->karyawan->nama_lengkap, 0, 1)) }}
                                            </span>
                                            <div>
                                                <div class="font-weight-bold text-dark" style="font-size: 0.88rem;">
                                                    {{ $tupoksi->karyawan->nama_lengkap }}{{ $tupoksi->karyawan->gelar ? ', ' . $tupoksi->karyawan->gelar : '' }}
                                                </div>
                                                <small class="text-muted">
                                                    <code>{{ $tupoksi->karyawan->nipy }}</code> &bull; {{ $tupoksi->karyawan->unit ?? 'Tanpa Unit' }}
                                                </small>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-danger font-italic small">NIPY: {{ $tupoksi->karyawan_nipy }} (Data terhapus)</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-soft-info font-weight-bold px-2.5 py-1" style="border-radius: 6px;">
                                        <i class="fa fa-user-tie mr-1"></i>{{ $tupoksi->jabatan }}
                                    </span>
                                </td>
                                <td>
                                    @if($tupoksi->tahunAjaran)
                                        <span class="badge {{ $tupoksi->tahunAjaran->is_active ? 'badge-soft-success' : 'badge-soft-primary' }} font-weight-bold px-2.5 py-1" style="border-radius: 6px;">
                                            {{ $tupoksi->tahunAjaran->nama_tahun_ajaran }} ({{ $tupoksi->tahunAjaran->semester }})
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted small">
                                        {{ $tupoksi->keterangan ? Str::limit($tupoksi->keterangan, 45) : '-' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($tupoksi->berkas)
                                        <a href="{{ route('tupoksi.download', $tupoksi->id) }}"
                                           class="btn-file-pill"
                                           title="Unduh Berkas: {{ $tupoksi->berkas_filename }}">
                                            <i class="fa fa-file-pdf"></i> Unduh Berkas
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
                                            <a href="{{ route('tupoksi.show', $tupoksi->id) }}" class="action-dropdown-item item-view">
                                                <span class="action-item-icon"><i class="fa fa-eye"></i></span>
                                                <span>Lihat Detail</span>
                                            </a>
                                            <a href="{{ route('tupoksi.edit', $tupoksi->id) }}" class="action-dropdown-item item-edit">
                                                <span class="action-item-icon"><i class="fa fa-edit"></i></span>
                                                <span>Edit Tupoksi</span>
                                            </a>
                                            <div class="action-dropdown-divider"></div>
                                            <form action="{{ route('tupoksi.destroy', $tupoksi->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Tupoksi ini beserta berkasnya?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-dropdown-item item-delete">
                                                    <span class="action-item-icon"><i class="fa fa-trash"></i></span>
                                                    <span>Hapus Tupoksi</span>
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
                                        <h6 class="font-weight-bold text-dark">Belum ada data Tupoksi</h6>
                                        <p class="small mb-3">Tidak ditemukan data yang sesuai dengan kriteria filter Anda.</p>
                                        <a href="{{ route('tupoksi.create') }}" class="btn btn-sm btn-primary shadow-sm font-weight-bold" style="border-radius: 8px;">
                                            <i class="fa fa-plus-circle mr-1"></i> Tambah Tupoksi Baru
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($tupoksiList->hasPages())
            <div class="card-footer bg-white border-top d-flex flex-wrap justify-content-between align-items-center py-2.5 px-3 gap-2">
                <span class="small text-muted">
                    Menampilkan {{ $tupoksiList->firstItem() ?? 0 }} - {{ $tupoksiList->lastItem() ?? 0 }} dari {{ $tupoksiList->total() }} data
                </span>
                <div>
                    {{ $tupoksiList->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
