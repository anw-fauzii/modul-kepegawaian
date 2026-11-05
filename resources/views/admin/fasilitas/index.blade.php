@extends('layouts.appui')

@section('title')
    <title>Manajemen Fasilitas Karyawan - SIPIT</title>
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
    .badge-soft-success { background-color: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .badge-soft-warning { background-color: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
    .badge-soft-primary { background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
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
    .btn-action-success { background: #dcfce7; color: #15803d; border-color: #bbf7d0; }
    .btn-action-success:hover { background: #16a34a; color: #fff; border-color: #16a34a; }
    .btn-action-warning { background: #fef3c7; color: #b45309; border-color: #fde68a; }
    .btn-action-warning:hover { background: #d97706; color: #fff; border-color: #d97706; }
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
                    <i class="pe-7s-box2" style="color: white;"></i>
                </div>
                <div>
                    Fasilitas &amp; Aset Karyawan
                    <div class="page-title-subheading">
                        Pencatatan inventaris sarana kerja yang dipinjamkan atau diserahkan kepada karyawan beserta status pengembaliannya.
                    </div>
                </div>
            </div>
            <div class="page-title-actions d-flex align-items-center" style="gap: 8px;">
                <a href="{{ route('fasilitas.create') }}" class="btn text-white shadow-sm font-weight-bold"
                    style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 8px;">
                    <i class="fa fa-plus-circle mr-1"></i> Tambah Fasilitas
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
                    <i class="fa fa-cubes"></i>
                </div>
                <div>
                    <span class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Total Fasilitas</span>
                    <h4 class="mb-0 font-weight-bold text-dark mt-1">
                        {{ number_format($statistik->total_item ?? 0) }}
                        <small class="text-muted font-weight-normal" style="font-size: 12.5px;">Item ({{ number_format($statistik->total_unit ?? 0) }} Unit)</small>
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-2">
            <div class="stat-card p-3 d-flex align-items-center" style="border-left: 4px solid #f59e0b;">
                <div class="stat-icon-box mr-3" style="background: #fef3c7; color: #d97706;">
                    <i class="fa fa-clock"></i>
                </div>
                <div>
                    <span class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Belum Dikembalikan</span>
                    <h4 class="mb-0 font-weight-bold text-warning mt-1">
                        {{ number_format($statistik->total_belum_kembali ?? 0) }}
                        <small class="text-muted font-weight-normal" style="font-size: 12.5px;">Item ({{ number_format($statistik->unit_belum_kembali ?? 0) }} Unit)</small>
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-2">
            <div class="stat-card p-3 d-flex align-items-center" style="border-left: 4px solid #10b981;">
                <div class="stat-icon-box mr-3" style="background: #dcfce7; color: #16a34a;">
                    <i class="fa fa-check-circle"></i>
                </div>
                <div>
                    <span class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Sudah Dikembalikan</span>
                    <h4 class="mb-0 font-weight-bold text-success mt-1">
                        {{ number_format($statistik->total_sudah_kembali ?? 0) }}
                        <small class="text-muted font-weight-normal" style="font-size: 12.5px;">Item ({{ number_format($statistik->unit_sudah_kembali ?? 0) }} Unit)</small>
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <!-- FILTER CARD -->
    <div class="main-card card mb-3 shadow-sm border-0" style="border-radius: 12px;">
        <div class="card-body p-3">
            <form action="{{ route('fasilitas.index') }}" method="GET" class="w-100 mb-0">
                <div class="row align-items-end">
                    <div class="col-md-4 col-sm-12 mb-2 mb-md-0">
                        <label class="form-filter-label">
                            <i class="fa fa-search mr-1 text-warning"></i> Pencarian
                        </label>
                        <input type="text" name="search" class="form-control form-filter-control" placeholder="Cari fasilitas, karyawan, NIPY..." value="{{ request('search') }}">
                    </div>

                    <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                        <label class="form-filter-label">
                            <i class="fa fa-exchange-alt mr-1 text-warning"></i> Status Pengembalian
                        </label>
                        <select name="status" class="form-control form-filter-control">
                            <option value="">-- Semua Status --</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Belum Dikembalikan</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Sudah Dikembalikan</option>
                        </select>
                    </div>

                    <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                        <label class="form-filter-label">
                            <i class="fa fa-calendar-alt mr-1 text-warning"></i> Tahun Terima
                        </label>
                        <input type="number" name="tahun_terima" class="form-control form-filter-control" placeholder="Contoh: {{ date('Y') }}" value="{{ request('tahun_terima') }}">
                    </div>

                    <div class="col-md-2 col-sm-12 mb-2 mb-md-0">
                        <div class="d-flex w-100" style="gap: 6px; width: 100%;">
                            <button type="submit" class="btn text-white font-weight-bold d-flex align-items-center justify-content-center"
                                style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 8px; height: 38px; border: none; flex: 1 1 auto; width: 100%; box-shadow: 0 2px 6px rgba(234, 88, 12, 0.2);">
                                <i class="fa fa-filter mr-1"></i> Filter
                            </button>
                            <a href="{{ route('fasilitas.index') }}" class="btn btn-light border font-weight-bold shadow-sm d-flex align-items-center justify-content-center"
                                style="border-radius: 8px; height: 38px; width: 38px; min-width: 38px; flex-shrink: 0;" title="Reset Filter">
                                <i class="fa fa-undo"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Data Table Card --}}
    <div class="main-card card border-0 shadow-sm mb-4" style="border-radius: 14px; overflow: hidden;">
        <div class="card-header bg-white border-bottom font-weight-bold d-flex flex-wrap justify-content-between align-items-center py-3 px-3">
            <span class="text-dark"><i class="fa fa-list-ul text-primary mr-2"></i>Daftar Fasilitas Karyawan</span>
            <span class="badge badge-soft-primary px-2.5 py-1 font-weight-bold" style="border-radius: 999px;">{{ $fasilitas->total() }} Data Ditemukan</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>Karyawan</th>
                            <th>Nama Fasilitas</th>
                            <th class="text-center" style="width: 105px;">Jumlah</th>
                            <th class="text-center" style="width: 95px;">Tahun</th>
                            <th class="text-center" style="width: 175px;">Status</th>
                            <th>Catatan</th>
                            <th class="text-center" style="width: 70px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fasilitas as $index => $item)
                            <tr>
                                <td class="text-center text-muted font-weight-bold">
                                    {{ $fasilitas->firstItem() + $index }}
                                </td>
                                <td>
                                    @if($item->karyawan)
                                        <div class="emp-cell">
                                            <span class="emp-avatar">
                                                {{ strtoupper(mb_substr($item->karyawan->nama_lengkap, 0, 1)) }}
                                            </span>
                                            <div>
                                                <div class="font-weight-bold text-dark" style="font-size: 0.88rem;">
                                                    {{ $item->karyawan->nama_lengkap }}{{ $item->karyawan->gelar ? ', ' . $item->karyawan->gelar : '' }}
                                                </div>
                                                <small class="text-muted">
                                                    <code>{{ $item->karyawan->nipy }}</code> &bull; {{ $item->karyawan->unit ?? 'Tanpa Unit' }}
                                                </small>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-danger font-italic small">NIPY: {{ $item->karyawan_nipy }} (Data terhapus)</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="font-weight-bold text-dark"><i class="fa fa-cube text-warning mr-1.5"></i>{{ $item->nama_fasilitas }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-light border font-weight-bold px-2.5 py-1" style="border-radius: 6px;">
                                        {{ $item->jumlah }} Unit
                                    </span>
                                </td>
                                <td class="text-center font-weight-bold text-secondary">
                                    {{ $item->tahun_terima }}
                                </td>
                                <td class="text-center">
                                    @if($item->status)
                                        <span class="badge badge-soft-success px-2.5 py-1 font-weight-bold" style="border-radius: 6px;">
                                            <i class="fa fa-check-circle mr-1"></i> Sudah Kembali
                                        </span>
                                        @if($item->tanggal_kembali)
                                            <div class="small text-muted mt-1" style="font-size: 11px;">
                                                <i class="fa fa-calendar-check mr-1"></i>{{ \Carbon\Carbon::parse($item->tanggal_kembali)->format('d/m/Y') }}
                                            </div>
                                        @endif
                                    @else
                                        <span class="badge badge-soft-warning px-2.5 py-1 font-weight-bold" style="border-radius: 6px;">
                                            <i class="fa fa-clock mr-1"></i> Belum Kembali
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->catatan)
                                        <span class="small text-muted" title="{{ $item->catatan }}">
                                            {{ \Illuminate\Support\Str::limit($item->catatan, 40) }}
                                        </span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="action-dropdown">
                                        <button type="button" class="btn-action-dots" title="Menu Aksi">
                                            <i class="fa fa-ellipsis-v"></i>
                                        </button>
                                        <div class="action-dropdown-menu">
                                            <a href="{{ route('fasilitas.show', $item->id) }}" class="action-dropdown-item item-view">
                                                <span class="action-item-icon"><i class="fa fa-eye"></i></span>
                                                <span>Lihat Detail</span>
                                            </a>
                                            <a href="{{ route('fasilitas.edit', $item->id) }}" class="action-dropdown-item item-edit">
                                                <span class="action-item-icon"><i class="fa fa-edit"></i></span>
                                                <span>Edit Fasilitas</span>
                                            </a>
                                            <form action="{{ route('fasilitas.toggle-status', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengubah status pengembalian fasilitas ini?')">
                                                @csrf
                                                <button type="submit" class="action-dropdown-item {{ $item->status ? 'item-warning' : 'item-success' }}">
                                                    <span class="action-item-icon"><i class="fa {{ $item->status ? 'fa-undo' : 'fa-check' }}"></i></span>
                                                    <span>{{ $item->status ? 'Tandai Belum Kembali' : 'Tandai Sudah Kembali' }}</span>
                                                </button>
                                            </form>
                                            <div class="action-dropdown-divider"></div>
                                            <form action="{{ route('fasilitas.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data fasilitas ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-dropdown-item item-delete">
                                                    <span class="action-item-icon"><i class="fa fa-trash"></i></span>
                                                    <span>Hapus Fasilitas</span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fa fa-box-open fa-3x mb-3 text-secondary" style="opacity: 0.45;"></i>
                                        <h6 class="font-weight-bold text-dark">Belum ada data fasilitas</h6>
                                        <p class="small mb-3">Tidak ditemukan fasilitas yang sesuai dengan kriteria pencarian Anda.</p>
                                        <a href="{{ route('fasilitas.create') }}" class="btn btn-sm btn-primary shadow-sm font-weight-bold" style="border-radius: 8px;">
                                            <i class="fa fa-plus-circle mr-1"></i> Tambah Fasilitas Baru
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($fasilitas->hasPages())
            <div class="card-footer bg-white border-top d-flex flex-wrap justify-content-between align-items-center py-2.5 px-3 gap-2">
                <span class="small text-muted">
                    Menampilkan {{ $fasilitas->firstItem() }} - {{ $fasilitas->lastItem() }} dari {{ $fasilitas->total() }} data
                </span>
                <div>
                    {{ $fasilitas->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
