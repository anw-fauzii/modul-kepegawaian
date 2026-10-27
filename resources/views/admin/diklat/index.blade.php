@extends('layouts.appui')

@section('title')
    <title>Pendidikan & Pelatihan (Diklat) Karyawan - SIPIT</title>
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
    .badge-soft-info {
        background-color: #ede9fe;
        color: #6d28d9;
        border: 1px solid #ddd6fe;
    }
    .badge-soft-warning {
        background-color: #fef3c7;
        color: #b45309;
        border: 1px solid #fde68a;
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
                <div class="page-title-icon bg-primary text-white shadow-sm" style="border-radius: 12px;">
                    <i class="pe-7s-study icon-gradient bg-plum-plate"></i>
                </div>
                <div>
                    <strong>Pendidikan & Pelatihan (Diklat) Karyawan</strong>
                    <div class="page-title-subheading">
                        Pengarsipan riwayat pengembangan kompetensi karyawan melalui diklat lembaga maupun mandiri.
                    </div>
                </div>
            </div>
            <div class="page-title-actions">
                <a href="{{ route('diklat.create') }}" class="btn btn-primary font-weight-bold shadow-sm">
                    <i class="fa fa-plus-circle mr-1"></i> Tambah Diklat Baru
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
                <div class="rounded-circle p-3 mr-3 bg-light text-primary">
                    <i class="fa fa-graduation-cap fa-2x"></i>
                </div>
                <div>
                    <span class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Total Diklat</span>
                    <h3 class="mb-0 font-weight-bold text-dark mt-1">{{ number_format($statistik->total_diklat ?? 0) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-2">
            <div class="stat-card p-3 d-flex align-items-center" style="border-left: 4px solid #0284c7;">
                <div class="rounded-circle p-3 mr-3 bg-light text-info">
                    <i class="fa fa-building-o fa-2x"></i>
                </div>
                <div>
                    <span class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Diklat Lembaga</span>
                    <h3 class="mb-0 font-weight-bold text-info mt-1">{{ number_format($statistik->total_lembaga ?? 0) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-2">
            <div class="stat-card p-3 d-flex align-items-center" style="border-left: 4px solid #10b981;">
                <div class="rounded-circle p-3 mr-3 bg-light text-success">
                    <i class="fa fa-user fa-2x"></i>
                </div>
                <div>
                    <span class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Diklat Mandiri</span>
                    <h3 class="mb-0 font-weight-bold text-success mt-1">{{ number_format($statistik->total_mandiri ?? 0) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-2">
            <div class="stat-card p-3 d-flex align-items-center" style="border-left: 4px solid #f59e0b;">
                <div class="rounded-circle p-3 mr-3 bg-light text-warning">
                    <i class="fa fa-users fa-2x"></i>
                </div>
                <div>
                    <span class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Peserta Terdaftar</span>
                    <h3 class="mb-0 font-weight-bold text-warning mt-1">{{ number_format($statistik->total_karyawan_mengikuti ?? 0) }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="main-card card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('diklat.index') }}" class="row align-items-end">
                <div class="col-lg-3 col-md-3 col-sm-12 mb-2 mb-md-0">
                    <label class="font-weight-bold small text-muted mb-1">Pencarian</label>
                    <div class="input-group">
                        
                        <input type="text" name="search" class="form-control border-left-0" placeholder="Cari kegiatan, materi, pelaksana..." value="{{ $filters['search'] ?? '' }}">
                    </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-6 mb-2 mb-md-0">
                    <label class="font-weight-bold small text-muted mb-1">Kategori Diklat</label>
                    <select name="kategori" class="form-control">
                        <option value="">-- Semua Kategori --</option>
                        <option value="lembaga" {{ (isset($filters['kategori']) && $filters['kategori'] === 'lembaga') ? 'selected' : '' }}>Lembaga</option>
                        <option value="mandiri" {{ (isset($filters['kategori']) && $filters['kategori'] === 'mandiri') ? 'selected' : '' }}>Mandiri</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-6 mb-2 mb-md-0">
                    <label class="font-weight-bold small text-muted mb-1">Dari Tanggal</label>
                    <input type="date" name="tanggal_mulai" class="form-control" value="{{ $filters['tanggal_mulai'] ?? '' }}">
                </div>
                <div class="col-lg-2 col-md-2 col-sm-6 mb-2 mb-md-0">
                    <label class="font-weight-bold small text-muted mb-1">Sampai Tanggal</label>
                    <input type="date" name="tanggal_selesai" class="form-control" value="{{ $filters['tanggal_selesai'] ?? '' }}">
                </div>
                <div class="col-lg-3 col-md-3 col-sm-12 mb-2 mb-md-0 d-flex align-items-center justify-content-md-end">
                    <button type="submit" class="btn btn-primary mr-2" style="white-space: nowrap;">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    <a href="{{ route('diklat.index') }}" class="btn btn-outline-secondary" title="Reset Filter" style="white-space: nowrap;">
                        <i class="fas fa-redo mr-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Data --}}
    <div class="main-card card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-header bg-white border-bottom font-weight-bold d-flex justify-content-between align-items-center py-3 px-3">
            <span>Daftar Riwayat Diklat Karyawan</span>
            <span class="badge badge-primary">{{ $diklatList->total() }} Data Ditemukan</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>Karyawan (NIPY)</th>
                            <th class="text-center" style="width: 120px;">Kategori</th>
                            <th>Kegiatan & Pelaksana</th>
                            <th>Materi Pelatihan</th>
                            <th>Tanggal Pelaksanaan</th>
                            <th class="text-center" style="width: 140px;">Sertifikat</th>
                            <th class="text-center" style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($diklatList as $index => $diklat)
                            <tr>
                                <td class="text-center text-muted font-weight-bold">
                                    {{ $diklatList->firstItem() + $index }}
                                </td>
                                <td>
                                    @if($diklat->karyawan)
                                        <div class="font-weight-bold text-dark">
                                            {{ $diklat->karyawan->nama_lengkap }}{{ $diklat->karyawan->gelar ? ', ' . $diklat->karyawan->gelar : '' }}
                                        </div>
                                        <small class="text-muted">
                                            <code>{{ $diklat->karyawan->nipy }}</code> &bull; {{ $diklat->karyawan->unit ?? 'Tanpa Unit' }}
                                        </small>
                                    @else
                                        <span class="text-danger font-italic">NIPY: {{ $diklat->karyawan_nipy }} (Data terhapus)</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($diklat->kategori === 'lembaga')
                                        <span class="badge badge-soft-info font-weight-bold px-2 py-1">
                                            <i class="fa fa-building-o mr-1"></i> Lembaga
                                        </span>
                                    @else
                                        <span class="badge badge-soft-success font-weight-bold px-2 py-1">
                                            <i class="fa fa-user mr-1"></i> Mandiri
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="font-weight-bold text-dark">
                                        {{ $diklat->nama_kegiatan }}
                                    </div>
                                    <small class="text-muted">
                                        Pelaksana: <strong>{{ $diklat->pelaksana }}</strong>
                                        @if($diklat->tempat)
                                            &bull; Lokasi: {{ $diklat->tempat }}
                                        @endif
                                    </small>
                                </td>
                                <td>
                                    <span class="small text-muted" title="{{ $diklat->materi }}">
                                        {{ Str::limit($diklat->materi, 50) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="font-weight-bold text-dark small">
                                        {{ $diklat->tanggal?->format('d M Y') }}
                                        @if($diklat->tanggal_selesai && $diklat->tanggal_selesai->ne($diklat->tanggal))
                                            - {{ $diklat->tanggal_selesai?->format('d M Y') }}
                                        @endif
                                    </div>
                                    @if($diklat->jumlah_jam)
                                        <small class="badge badge-light border">{{ $diklat->jumlah_jam }} JP</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($diklat->berkas)
                                        <a href="{{ route('diklat.download', $diklat->id) }}" 
                                           class="btn btn-sm btn-outline-danger shadow-sm font-weight-bold"
                                           title="Unduh Sertifikat: {{ $diklat->berkas_filename }}">
                                            <i class="fa fa-certificate mr-1"></i> Unduh
                                        </a>
                                    @else
                                        <span class="badge badge-secondary">Belum Diunggah</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        {{-- Detail Button --}}
                                        <a href="{{ route('diklat.show', $diklat->id) }}" class="btn btn-sm btn-outline-info" title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>

                                        {{-- Edit Button --}}
                                        <a href="{{ route('diklat.edit', $diklat->id) }}" class="btn btn-sm btn-outline-primary ml-1" title="Edit">
                                            <i class="fa fa-pencil"></i>
                                        </a>

                                        {{-- Delete Button --}}
                                        <form action="{{ route('diklat.destroy', $diklat->id) }}" method="POST" class="d-inline ml-1" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data Diklat ini beserta berkasnya?');">
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
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fa fa-folder-open-o fa-3x mb-3 text-secondary"></i>
                                        <h5 class="font-weight-bold text-dark">Belum ada data Diklat</h5>
                                        <p class="small mb-3">Tidak ditemukan riwayat diklat yang sesuai dengan filter Anda.</p>
                                        <a href="{{ route('diklat.create') }}" class="btn btn-sm btn-primary shadow-sm font-weight-bold">
                                            <i class="fa fa-plus-circle mr-1"></i> Tambah Diklat Baru
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($diklatList->hasPages())
            <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center py-2 px-3">
                <span class="small text-muted">
                    Menampilkan {{ $diklatList->firstItem() ?? 0 }} - {{ $diklatList->lastItem() ?? 0 }} dari {{ $diklatList->total() }} data
                </span>
                <div>
                    {{ $diklatList->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
