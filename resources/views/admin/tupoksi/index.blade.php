@extends('layouts.appui')

@section('title')
    <title>Tupoksi Karyawan - SIPIT</title>
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
                <div class="page-title-icon bg-info text-white shadow-sm" style="border-radius: 12px;">
                    <i class="pe-7s-id icon-gradient bg-sunny-morning"></i>
                </div>
                <div>
                    <strong>Tugas Pokok dan Fungsi (Tupoksi) Karyawan</strong>
                    <div class="page-title-subheading">
                        Pengelolaan dan pengarsipan berkas dokumen penetapan tugas pokok & fungsi karyawan per tahun ajaran.
                    </div>
                </div>
            </div>
            <div class="page-title-actions">
                <a href="{{ route('tupoksi.create') }}" class="btn btn-primary font-weight-bold shadow-sm">
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
                <div class="rounded-circle p-3 mr-3 bg-light text-primary">
                    <i class="fa fa-id-badge fa-2x"></i>
                </div>
                <div>
                    <span class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Total Tupoksi</span>
                    <h3 class="mb-0 font-weight-bold text-dark mt-1">{{ number_format($statistik->total_tupoksi ?? 0) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-2">
            <div class="stat-card p-3 d-flex align-items-center" style="border-left: 4px solid #10b981;">
                <div class="rounded-circle p-3 mr-3 bg-light text-success">
                    <i class="fa fa-users fa-2x"></i>
                </div>
                <div>
                    <span class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Karyawan Memiliki Tupoksi</span>
                    <h3 class="mb-0 font-weight-bold text-success mt-1">{{ number_format($statistik->total_karyawan_memiliki_tupoksi ?? 0) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-2">
            <div class="stat-card p-3 d-flex align-items-center" style="border-left: 4px solid #f59e0b;">
                <div class="rounded-circle p-3 mr-3 bg-light text-warning">
                    <i class="fa fa-cloud-upload fa-2x"></i>
                </div>
                <div>
                    <span class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Berkas Terunggah</span>
                    <h3 class="mb-0 font-weight-bold text-warning mt-1">{{ number_format($statistik->total_berkas_terunggah ?? 0) }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="main-card card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('tupoksi.index') }}" class="row align-items-end">
                <div class="col-lg-4 col-md-4 col-sm-12 mb-2 mb-md-0">
                    <label class="font-weight-bold small text-muted mb-1">Pencarian</label>
                    <div class="input-group">
                        
                        <input type="text" name="search" class="form-control border-left-0" placeholder="Cari NIPY, Nama, Jabatan, Unit..." value="{{ $filters['search'] ?? '' }}">
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 mb-2 mb-md-0">
                    <label class="font-weight-bold small text-muted mb-1">Tahun Ajaran</label>
                    <select name="akademik_master_tahun_ajaran_id" class="form-control">
                        <option value="">-- Semua Tahun Ajaran --</option>
                        @foreach($tahunAjaranList as $ta)
                            <option value="{{ $ta->id }}" {{ (isset($filters['akademik_master_tahun_ajaran_id']) && $filters['akademik_master_tahun_ajaran_id'] == $ta->id) ? 'selected' : '' }}>
                                {{ $ta->nama_tahun_ajaran }} ({{ $ta->semester }}) {{ $ta->is_active ? '★ Aktif' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 mb-2 mb-md-0">
                    <label class="font-weight-bold small text-muted mb-1">Jabatan</label>
                    <input type="text" name="jabatan" class="form-control" placeholder="Filter nama jabatan..." value="{{ $filters['jabatan'] ?? '' }}">
                </div>
                <div class="col-lg-2 col-md-2 col-sm-12 mb-2 mb-md-0 d-flex align-items-center justify-content-md-end">
                    <button type="submit" class="btn btn-primary mr-2" style="white-space: nowrap;">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    <a href="{{ route('tupoksi.index') }}" class="btn btn-outline-secondary" title="Reset Filter" style="white-space: nowrap;">
                        <i class="fas fa-redo mr-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Data --}}
    <div class="main-card card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-header bg-white border-bottom font-weight-bold d-flex justify-content-between align-items-center py-3 px-3">
            <span>Daftar Tupoksi Karyawan</span>
            <span class="badge badge-primary">{{ $tupoksiList->total() }} Data Ditemukan</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>Karyawan (NIPY)</th>
                            <th>Jabatan / Tugas Pokok</th>
                            <th>Tahun Ajaran</th>
                            <th>Keterangan</th>
                            <th class="text-center" style="width: 140px;">Berkas Tupoksi</th>
                            <th class="text-center" style="width: 150px;">Aksi</th>
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
                                        <div class="font-weight-bold text-dark">
                                            {{ $tupoksi->karyawan->nama_lengkap }}{{ $tupoksi->karyawan->gelar ? ', ' . $tupoksi->karyawan->gelar : '' }}
                                        </div>
                                        <small class="text-muted">
                                            <code>{{ $tupoksi->karyawan->nipy }}</code> &bull; {{ $tupoksi->karyawan->unit ?? 'Tanpa Unit' }}
                                        </small>
                                    @else
                                        <span class="text-danger font-italic">NIPY: {{ $tupoksi->karyawan_nipy }} (Data terhapus)</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-soft-info font-weight-bold px-2 py-1">
                                        {{ $tupoksi->jabatan }}
                                    </span>
                                </td>
                                <td>
                                    @if($tupoksi->tahunAjaran)
                                        <span class="badge {{ $tupoksi->tahunAjaran->is_active ? 'badge-soft-success' : 'badge-soft-primary' }} font-weight-bold px-2 py-1">
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
                                           class="btn btn-sm btn-outline-danger shadow-sm font-weight-bold"
                                           title="Unduh Berkas: {{ $tupoksi->berkas_filename }}">
                                            <i class="fa fa-file-pdf-o mr-1"></i> Unduh
                                        </a>
                                    @else
                                        <span class="badge badge-secondary">Tidak Ada Berkas</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        {{-- Detail Button --}}
                                        <a href="{{ route('tupoksi.show', $tupoksi->id) }}" class="btn btn-sm btn-outline-info" title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>

                                        {{-- Edit Button --}}
                                        <a href="{{ route('tupoksi.edit', $tupoksi->id) }}" class="btn btn-sm btn-outline-primary ml-1" title="Edit">
                                            <i class="fa fa-pencil"></i>
                                        </a>

                                        {{-- Delete Button --}}
                                        <form action="{{ route('tupoksi.destroy', $tupoksi->id) }}" method="POST" class="d-inline ml-1" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Tupoksi ini beserta berkasnya?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fa fa-folder-open-o fa-3x mb-3 text-secondary"></i>
                                        <h5 class="font-weight-bold text-dark">Belum ada data Tupoksi</h5>
                                        <p class="small mb-3">Tidak ditemukan data yang sesuai dengan kriteria filter Anda.</p>
                                        <a href="{{ route('tupoksi.create') }}" class="btn btn-sm btn-primary shadow-sm font-weight-bold">
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
            <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center py-2 px-3">
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
