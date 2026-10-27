@extends('layouts.appui')

@section('title')
    <title>Sertifikat Penghargaan Karyawan - SIPIT</title>
@endsection

@section('content')
<style>
    .stat-card {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.05);
    }
    .badge-soft-primary {
        background-color: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
    }
    .badge-soft-success {
        background-color: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }
    .badge-soft-warning {
        background-color: #fef3c7;
        color: #b45309;
        border: 1px solid #fde68a;
    }
    .badge-soft-info {
        background-color: #ede9fe;
        color: #6d28d9;
        border: 1px solid #ddd6fe;
    }
    .table td, .table th {
        vertical-align: middle;
    }
</style>

<div class="app-main__inner">
    {{-- Page Header --}}
    <div class="app-page-title mb-3">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon bg-warning text-white shadow-sm" style="border-radius: 12px;">
                    <i class="pe-7s-medal icon-gradient bg-warm-flame"></i>
                </div>
                <div>
                    <strong>Sertifikat Penghargaan Karyawan</strong>
                    <div class="page-title-subheading">
                        Pencatatan dan pengarsipan sertifikat penghargaan serta prestasi yang diraih oleh karyawan.
                    </div>
                </div>
            </div>
            <div class="page-title-actions">
                <a href="{{ route('sertifikat-penghargaan.create') }}" class="btn btn-primary shadow-sm font-weight-bold">
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
            <div class="stat-card p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small text-uppercase font-weight-bold">Total Sertifikat</div>
                        <h4 class="font-weight-bold text-primary mb-0 mt-1">{{ number_format($statistik->total_sertifikat ?? 0) }}</h4>
                    </div>
                    <div class="rounded-circle bg-light p-3 text-primary">
                        <i class="fa fa-certificate fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-2">
            <div class="stat-card p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small text-uppercase font-weight-bold">Karyawan Berprestasi</div>
                        <h4 class="font-weight-bold text-success mb-0 mt-1">{{ number_format($statistik->total_karyawan_berprestasi ?? 0) }}</h4>
                    </div>
                    <div class="rounded-circle bg-light p-3 text-success">
                        <i class="fa fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-2">
            <div class="stat-card p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small text-uppercase font-weight-bold">Tahun Terdata</div>
                        <h4 class="font-weight-bold text-info mb-0 mt-1">{{ number_format($statistik->total_tahun_tercatat ?? 0) }}</h4>
                    </div>
                    <div class="rounded-circle bg-light p-3 text-info">
                        <i class="fa fa-calendar fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-2">
            <div class="stat-card p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small text-uppercase font-weight-bold">Berkas Terunggah</div>
                        <h4 class="font-weight-bold text-warning mb-0 mt-1">{{ number_format($statistik->total_berkas_terunggah ?? 0) }}</h4>
                    </div>
                    <div class="rounded-circle bg-light p-3 text-warning">
                        <i class="fa fa-cloud-upload fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="main-card mb-3 card shadow-sm">
        <div class="card-body">
            <form method="GET" action="{{ route('sertifikat-penghargaan.index') }}" class="form-row align-items-end">
                <div class="col-md-6 col-sm-6 mb-2">
                    <label class="small font-weight-bold">Pencarian</label>
                    <input type="text" name="search" class="form-control" placeholder="Cari Judul Penghargaan, NIPY, Nama Karyawan, Unit..." value="{{ $filters['search'] ?? '' }}">
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <label class="small font-weight-bold">Tahun Penghargaan</label>
                    <input type="number" name="tahun" class="form-control" placeholder="Contoh: {{ date('Y') }}" value="{{ $filters['tahun'] ?? '' }}">
                </div>
                <div class="col-md-3 col-sm-12 mb-2 text-right">
                    <button type="submit" class="btn btn-primary mr-1">
                        <i class="fa fa-filter mr-1"></i> Filter
                    </button>
                    <a href="{{ route('sertifikat-penghargaan.index') }}" class="btn btn-secondary" title="Reset Filter">
                        <i class="fa fa-refresh"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Data --}}
    <div class="main-card mb-3 card shadow-sm">
        <div class="card-header bg-white font-weight-bold d-flex justify-content-between align-items-center">
            <span>Daftar Sertifikat Penghargaan Karyawan</span>
            <span class="badge badge-warning text-dark font-weight-bold">{{ $penghargaanList->total() }} Data Ditemukan</span>
        </div>
        <div class="table-responsive">
            <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                <thead class="thead-light">
                    <tr>
                        <th class="text-center" style="width: 50px;">No</th>
                        <th>Judul Penghargaan</th>
                        <th>Karyawan (NIPY)</th>
                        <th class="text-center">Tahun</th>
                        <th class="text-center">Berkas</th>
                        <th>Keterangan</th>
                        <th class="text-center" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penghargaanList as $index => $penghargaan)
                        <tr>
                            <td class="text-center text-muted">
                                {{ $penghargaanList->firstItem() + $index }}
                            </td>
                            <td>
                                <div class="font-weight-bold text-dark">
                                    {{ $penghargaan->judul_penghargaan }}
                                </div>
                                <small class="text-muted">
                                    Dicatat: {{ $penghargaan->created_at?->format('d M Y') }}
                                </small>
                            </td>
                            <td>
                                @if($penghargaan->karyawan)
                                    <div class="font-weight-bold">
                                        {{ $penghargaan->karyawan->nama_lengkap }}{{ $penghargaan->karyawan->gelar ? ', ' . $penghargaan->karyawan->gelar : '' }}
                                    </div>
                                    <small class="text-muted">
                                        <code>{{ $penghargaan->karyawan->nipy }}</code> &bull; {{ $penghargaan->karyawan->unit ?? 'Tanpa Unit' }}
                                    </small>
                                @else
                                    <span class="text-danger font-italic">NIPY: {{ $penghargaan->karyawan_nipy }} (Data terhapus)</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge badge-soft-primary font-weight-bold px-2 py-1">
                                    {{ $penghargaan->tahun }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($penghargaan->berkas)
                                    <a href="{{ route('sertifikat-penghargaan.download', $penghargaan->id) }}" 
                                       class="btn btn-sm btn-outline-danger shadow-sm"
                                       title="Unduh Berkas: {{ $penghargaan->berkas_filename }}">
                                        <i class="fa fa-file-pdf-o mr-1"></i> Unduh
                                    </a>
                                @else
                                    <span class="badge badge-secondary">Tidak Ada Berkas</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-muted small">
                                    {{ $penghargaan->keterangan ? Str::limit($penghargaan->keterangan, 50) : '-' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('sertifikat-penghargaan.show', $penghargaan->id) }}" class="btn btn-sm btn-info" title="Detail">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <a href="{{ route('sertifikat-penghargaan.edit', $penghargaan->id) }}" class="btn btn-sm btn-warning text-white" title="Edit">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    <form action="{{ route('sertifikat-penghargaan.destroy', $penghargaan->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus sertifikat penghargaan ini beserta berkasnya?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fa fa-trophy fa-3x d-block mb-2 text-secondary"></i>
                                Belum ada data Sertifikat Penghargaan yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between align-items-center">
            <span class="small text-muted">
                Menampilkan {{ $penghargaanList->firstItem() ?? 0 }} - {{ $penghargaanList->lastItem() ?? 0 }} dari {{ $penghargaanList->total() }} data
            </span>
            <div>
                {{ $penghargaanList->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
