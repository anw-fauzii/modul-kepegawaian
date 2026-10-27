@extends('layouts.appui')

@section('title')
    <title>Absen Kegiatan Lembaga (QR Code) - SIPIT</title>
@endsection

@section('content')
<style>
    .kegiatan-card {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .kegiatan-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.06);
    }
    .badge-soft-success {
        background-color: #dcfce7;
        color: #15803d;
    }
    .badge-soft-danger {
        background-color: #fee2e2;
        color: #b91c1c;
    }
    .badge-soft-primary {
        background-color: #e0f2fe;
        color: #0369a1;
    }
</style>

<div class="app-main__inner">
    {{-- Page Header --}}
    <div class="app-page-title mb-3">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon bg-warning text-white">
                    <i class="pe-7s-id icon-gradient bg-warm-flame"></i>
                </div>
                <div>
                    <strong>Absen Kegiatan Lembaga (QR Code)</strong>
                    <div class="page-title-subheading">
                        Manajemen kegiatan lembaga, presensi real-time via QR Code, dan monitoring kehadiran karyawan.
                    </div>
                </div>
            </div>
            <div class="page-title-actions">
                <a href="{{ route('kegiatan.create') }}" class="btn btn-primary btn-shadow font-weight-bold">
                    <i class="fa fa-plus mr-1"></i> Tambah Kegiatan
                </a>
            </div>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa fa-exclamation-circle mr-1"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Filter Card --}}
    <div class="card mb-4 border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('kegiatan.index') }}" class="row align-items-end">
                <div class="col-md-3 mb-2 mb-md-0">
                    <label class="small text-muted font-weight-bold">Cari Nama / Pengisi / Lokasi</label>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Ketik kata kunci..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2 mb-2 mb-md-0">
                    <label class="small text-muted font-weight-bold">Tahun Ajaran</label>
                    <select name="akademik_master_tahun_ajaran_id" class="form-control form-control-sm">
                        <option value="">Semua Tahun Ajaran</option>
                        @foreach($tahunAjaranList as $ta)
                            <option value="{{ $ta->id }}" {{ request('akademik_master_tahun_ajaran_id') == $ta->id ? 'selected' : '' }}>
                                {{ $ta->nama_tahun_ajaran }} (Sem. {{ $ta->semester }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2 mb-md-0">
                    <label class="small text-muted font-weight-bold">Dari Tanggal</label>
                    <input type="date" name="tanggal_mulai" class="form-control form-control-sm" value="{{ request('tanggal_mulai') }}">
                </div>
                <div class="col-md-2 mb-2 mb-md-0">
                    <label class="small text-muted font-weight-bold">Sampai Tanggal</label>
                    <input type="date" name="tanggal_selesai" class="form-control form-control-sm" value="{{ request('tanggal_selesai') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-sm btn-dark mr-1">
                        <i class="fa fa-filter mr-1"></i> Filter
                    </button>
                    <a href="{{ route('kegiatan.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fa fa-redo mr-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Kegiatan List Table --}}
    <div class="main-card mb-4 card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-dark">
                <i class="fa fa-calendar-alt text-warning mr-1"></i> Daftar Agenda Kegiatan Lembaga
            </h6>
            <span class="badge badge-pill badge-light border">
                Total: {{ $kegiatanList->total() }} Kegiatan
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="thead-light">
                    <tr>
                        <th class="text-center" width="50">#</th>
                        <th>Nama Kegiatan & Pengisi</th>
                        <th>Jadwal & Lokasi</th>
                        <th>Tahun Ajaran</th>
                        <th class="text-center">Presensi Hadir</th>
                        <th class="text-center">Status Gerbang</th>
                        <th class="text-center" width="180">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kegiatanList as $index => $kegiatan)
                        <tr>
                            <td class="text-center font-weight-bold text-muted">
                                {{ $kegiatanList->firstItem() + $index }}
                            </td>
                            <td>
                                <div class="font-weight-bold text-dark" style="font-size: 14px;">
                                    <a href="{{ route('kegiatan.show', $kegiatan->id) }}" class="text-dark text-decoration-none">
                                        {{ $kegiatan->nama_kegiatan }}
                                    </a>
                                </div>
                                <div class="small text-muted">
                                    <i class="fa fa-user-tie mr-1 text-primary"></i> Pengisi: <strong>{{ $kegiatan->pengisi ?? '-' }}</strong>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <i class="fa fa-calendar-day mr-1 text-danger"></i> {{ $kegiatan->tanggal->isoFormat('dddd, D MMMM Y') }}
                                </div>
                                <div class="small text-muted">
                                    <i class="fa fa-clock mr-1"></i> {{ $kegiatan->jam_mulai ? \Carbon\Carbon::parse($kegiatan->jam_mulai)->format('H:i') : '--:--' }} 
                                    s/d {{ $kegiatan->jam_selesai ? \Carbon\Carbon::parse($kegiatan->jam_selesai)->format('H:i') : 'Selesai' }}
                                    @if($kegiatan->lokasi)
                                        | <i class="fa fa-map-marker-alt text-info mr-1"></i> {{ $kegiatan->lokasi }}
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-soft-primary px-2 py-1">
                                    {{ $kegiatan->tahunAjaran->nama_tahun_ajaran ?? '-' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-soft-success font-weight-bold px-2 py-1" style="font-size: 13px;">
                                    <i class="fa fa-check-circle mr-1"></i> {{ $kegiatan->total_hadir }} Hadir
                                </span>
                                @if($kegiatan->total_izin > 0 || $kegiatan->total_sakit > 0)
                                    <div class="small text-muted mt-1">
                                        {{ $kegiatan->total_izin }} Izin / {{ $kegiatan->total_sakit }} Sakit
                                    </div>
                                @endif
                            </td>
                            <td class="text-center">
                                <form action="{{ route('kegiatan.toggle-status', $kegiatan->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @if($kegiatan->is_active)
                                        <button type="submit" class="btn btn-sm badge-soft-success border-0 px-2 py-1" title="Klik untuk menutup gerbang presensi">
                                            <i class="fa fa-door-open mr-1"></i> Dibuka
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-sm badge-soft-danger border-0 px-2 py-1" title="Klik untuk membuka gerbang presensi">
                                            <i class="fa fa-door-closed mr-1"></i> Ditutup
                                        </button>
                                    @endif
                                </form>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('kegiatan.qr', $kegiatan->id) }}" target="_blank" class="btn btn-sm btn-warning text-dark font-weight-bold" title="Layar QR Proyektor">
                                        <i class="fa fa-qrcode"></i> QR
                                    </a>
                                    <a href="{{ route('kegiatan.show', $kegiatan->id) }}" class="btn btn-sm btn-info" title="Rekap Kehadiran">
                                        <i class="fa fa-users"></i>
                                    </a>
                                    <a href="{{ route('kegiatan.edit', $kegiatan->id) }}" class="btn btn-sm btn-outline-secondary" title="Edit Kegiatan">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <form action="{{ route('kegiatan.destroy', $kegiatan->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kegiatan ini beserta riwayat kehadirannya?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Kegiatan">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fa fa-calendar-times fa-3x mb-2 text-secondary opacity-5"></i>
                                <p class="mb-0">Belum ada agenda kegiatan lembaga yang ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($kegiatanList->hasPages())
            <div class="card-footer bg-white border-top py-2 d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Menampilkan {{ $kegiatanList->firstItem() }} - {{ $kegiatanList->lastItem() }} dari {{ $kegiatanList->total() }} kegiatan
                </small>
                <div>
                    {{ $kegiatanList->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
