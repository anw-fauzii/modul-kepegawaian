@extends('layouts.appui')

@section('title')
    <title>Manajemen Fasilitas Karyawan - SIPIT</title>
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
    .badge-soft-primary {
        background-color: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
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
                <div class="page-title-icon bg-success text-white shadow-sm" style="border-radius: 12px;">
                    <i class="pe-7s-box2 icon-gradient bg-malibu-beach"></i>
                </div>
                <div>
                    <strong>Fasilitas Karyawan</strong>
                    <div class="page-title-subheading">
                        Pencatatan inventaris sarana kerja yang dipinjamkan atau diserahkan kepada karyawan beserta status pengembaliannya.
                    </div>
                </div>
            </div>
            <div class="page-title-actions">
                <a href="{{ route('fasilitas.create') }}" class="btn btn-primary font-weight-bold shadow-sm">
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
                <div class="rounded-circle p-3 mr-3 bg-light text-primary">
                    <i class="fa fa-cubes fa-2x"></i>
                </div>
                <div>
                    <span class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Total Fasilitas</span>
                    <h3 class="mb-0 font-weight-bold text-dark">
                        {{ number_format($statistik->total_item ?? 0) }}
                        <small class="text-muted font-weight-normal" style="font-size: 13px;">Item ({{ number_format($statistik->total_unit ?? 0) }} Unit)</small>
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-2">
            <div class="stat-card p-3 d-flex align-items-center" style="border-left: 4px solid #f59e0b;">
                <div class="rounded-circle p-3 mr-3 bg-light text-warning">
                    <i class="fa fa-clock-o fa-2x"></i>
                </div>
                <div>
                    <span class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Belum Dikembalikan</span>
                    <h3 class="mb-0 font-weight-bold text-warning">
                        {{ number_format($statistik->total_belum_kembali ?? 0) }}
                        <small class="text-muted font-weight-normal" style="font-size: 13px;">Item ({{ number_format($statistik->unit_belum_kembali ?? 0) }} Unit)</small>
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-2">
            <div class="stat-card p-3 d-flex align-items-center" style="border-left: 4px solid #10b981;">
                <div class="rounded-circle p-3 mr-3 bg-light text-success">
                    <i class="fa fa-check-circle-o fa-2x"></i>
                </div>
                <div>
                    <span class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Sudah Dikembalikan</span>
                    <h3 class="mb-0 font-weight-bold text-success">
                        {{ number_format($statistik->total_sudah_kembali ?? 0) }}
                        <small class="text-muted font-weight-normal" style="font-size: 13px;">Item ({{ number_format($statistik->unit_sudah_kembali ?? 0) }} Unit)</small>
                    </h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="main-card card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body p-3">
            <form action="{{ route('fasilitas.index') }}" method="GET" class="row align-items-end">
                <div class="col-lg-4 col-md-4 col-sm-12 mb-2 mb-md-0">
                    <label class="font-weight-bold small text-muted mb-1">Pencarian</label>
                    <div class="input-group">
                        
                        <input type="text" name="search" class="form-control border-left-0" placeholder="Cari fasilitas, karyawan, NIPY..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-lg-3 col-md-3 col-sm-6 mb-2 mb-md-0">
                    <label class="font-weight-bold small text-muted mb-1">Status Pengembalian</label>
                    <select name="status" class="form-control">
                        <option value="">-- Semua Status --</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Belum Dikembalikan</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Sudah Dikembalikan</option>
                    </select>
                </div>

                <div class="col-lg-3 col-md-3 col-sm-6 mb-2 mb-md-0">
                    <label class="font-weight-bold small text-muted mb-1">Tahun Terima</label>
                    <input type="number" name="tahun_terima" class="form-control" placeholder="Contoh: {{ date('Y') }}" value="{{ request('tahun_terima') }}">
                </div>

                <div class="col-lg-2 col-md-2 col-sm-12 mb-2 mb-md-0 d-flex align-items-center justify-content-md-end">
                    <button type="submit" class="btn btn-primary mr-2" style="white-space: nowrap;">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    <a href="{{ route('fasilitas.index') }}" class="btn btn-outline-secondary" title="Reset Filter" style="white-space: nowrap;">
                        <i class="fas fa-redo mr-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Data Table Card --}}
    <div class="main-card card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-header bg-white border-bottom font-weight-bold d-flex justify-content-between align-items-center py-3 px-3">
            <span>Daftar Fasilitas Karyawan</span>
            <span class="badge badge-primary">{{ $fasilitas->total() }} Data Ditemukan</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>Karyawan (NIPY)</th>
                            <th>Nama Fasilitas</th>
                            <th class="text-center" style="width: 100px;">Jumlah</th>
                            <th class="text-center" style="width: 110px;">Tahun</th>
                            <th class="text-center" style="width: 170px;">Status</th>
                            <th>Catatan</th>
                            <th class="text-center" style="width: 150px;">Aksi</th>
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
                                        <div class="font-weight-bold text-dark">
                                            {{ $item->karyawan->nama_lengkap }}{{ $item->karyawan->gelar ? ', ' . $item->karyawan->gelar : '' }}
                                        </div>
                                        <small class="text-muted">
                                            <code>{{ $item->karyawan->nipy }}</code> &bull; {{ $item->karyawan->unit ?? 'Tanpa Unit' }}
                                        </small>
                                    @else
                                        <span class="text-danger font-italic">NIPY: {{ $item->karyawan_nipy }} (Data terhapus)</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="font-weight-bold text-dark">{{ $item->nama_fasilitas }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-light border font-weight-bold px-2 py-1">
                                        {{ $item->jumlah }} Unit
                                    </span>
                                </td>
                                <td class="text-center font-weight-bold text-secondary">
                                    {{ $item->tahun_terima }}
                                </td>
                                <td class="text-center">
                                    @if($item->status)
                                        <span class="badge badge-soft-success px-2 py-1 font-weight-bold">
                                            <i class="fa fa-check-circle mr-1"></i> Sudah Kembali
                                        </span>
                                        @if($item->tanggal_kembali)
                                            <div class="small text-muted mt-1">
                                                <i class="fa fa-calendar-check-o mr-1"></i>{{ \Carbon\Carbon::parse($item->tanggal_kembali)->format('d/m/Y') }}
                                            </div>
                                        @endif
                                    @else
                                        <span class="badge badge-soft-warning px-2 py-1 font-weight-bold">
                                            <i class="fa fa-clock-o mr-1"></i> Belum Kembali
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
                                    <div class="btn-group" role="group">
                                        {{-- Toggle Status Button --}}
                                        <form action="{{ route('fasilitas.toggle-status', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin mengubah status fasilitas ini?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $item->status ? 'btn-outline-warning' : 'btn-outline-success' }}" title="{{ $item->status ? 'Ubah jadi Belum Dikembalikan' : 'Tandai Sudah Dikembalikan' }}">
                                                <i class="fa {{ $item->status ? 'fa-undo' : 'fa-check' }}"></i>
                                            </button>
                                        </form>

                                        {{-- Detail Button --}}
                                        <a href="{{ route('fasilitas.show', $item->id) }}" class="btn btn-sm btn-outline-info ml-1" title="Lihat Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>

                                        {{-- Edit Button --}}
                                        <a href="{{ route('fasilitas.edit', $item->id) }}" class="btn btn-sm btn-outline-primary ml-1" title="Edit Fasilitas">
                                            <i class="fa fa-pencil"></i>
                                        </a>

                                        {{-- Delete Button --}}
                                        <form action="{{ route('fasilitas.destroy', $item->id) }}" method="POST" class="d-inline ml-1" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data fasilitas ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Fasilitas">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fa fa-folder-open-o fa-3x mb-3 text-secondary"></i>
                                        <h5 class="font-weight-bold text-dark">Belum ada data fasilitas</h5>
                                        <p class="small mb-3">Tidak ditemukan fasilitas yang sesuai dengan kriteria pencarian Anda.</p>
                                        <a href="{{ route('fasilitas.create') }}" class="btn btn-sm btn-primary shadow-sm font-weight-bold">
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
            <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center py-2 px-3">
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
