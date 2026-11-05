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
    .filter-panel {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
    }
    .form-filter-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: #475569;
        letter-spacing: 0.04em;
        margin-bottom: 5px;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .form-filter-control {
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        font-size: 13px;
        height: 38px;
        transition: all 0.2s ease;
        color: #1e293b;
        background-color: #ffffff;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
    }
    .form-filter-control:focus {
        border-color: #ea580c;
        box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.15);
        outline: none;
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

    /* Responsive Enhancements */
    @media (max-width: 991.98px) {
        .app-page-title .page-title-wrapper {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 12px !important;
        }
        .app-page-title .page-title-heading {
            width: 100% !important;
        }
        .app-page-title .page-title-actions {
            width: 100% !important;
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 8px !important;
            margin-left: 0 !important;
        }
        .app-page-title .page-title-actions > .btn,
        .app-page-title .page-title-actions > a {
            flex: 1 1 auto !important;
            width: 100% !important;
            justify-content: center !important;
        }
    }
    @media (min-width: 576px) and (max-width: 991.98px) {
        .app-page-title .page-title-actions > .btn,
        .app-page-title .page-title-actions > a {
            width: auto !important;
            flex: 0 1 auto !important;
        }
    }
</style>

<div class="app-main__inner">
    {{-- Page Header --}}
    <div class="app-page-title mb-3">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon text-white shadow-sm"
                    style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 14px; box-shadow: 0 4px 10px rgba(234, 88, 12, 0.25);">
                    <i class="pe-7s-id" style="color: white; font-size: 26px;"></i>
                </div>
                <div>
                    <span class="font-weight-bold" style="font-size: 18px; color: #1e293b;">Agenda Kegiatan Lembaga</span>
                    <div class="page-title-subheading text-muted mt-0.5" style="font-size: 12.5px;">
                        Manajemen agenda kegiatan lembaga, presensi real-time via QR Code, dan monitoring kehadiran karyawan.
                    </div>
                </div>
            </div>
            <div class="page-title-actions d-flex align-items-center" style="gap: 8px;">
                <a href="{{ route('kegiatan.create') }}" class="btn text-white shadow-sm font-weight-bold"
                    style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%); border-radius: 8px; height: 38px; display: inline-flex; align-items: center; border: none; box-shadow: 0 4px 10px rgba(234, 88, 12, 0.25);">
                    <i class="fa fa-plus mr-1.5"></i> Tambah Kegiatan
                </a>
            </div>
        </div>
    </div>

    <!-- FILTER CARD -->
    <div class="main-card card mb-3 shadow-sm border-0" style="border-radius: 12px;">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('kegiatan.index') }}" class="w-100 mb-0">
                <div class="row align-items-end">
                    <!-- 1. Pencarian Kata Kunci -->
                    <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                        <label class="form-filter-label">
                            <i class="fa fa-search mr-1 text-warning"></i> Cari Kata Kunci
                        </label>
                        <input type="text" name="search" class="form-control form-filter-control" placeholder="Kegiatan, pengisi, lokasi..." value="{{ request('search') }}">
                    </div>

                    <!-- 2. Tahun Ajaran -->
                    <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                        <label class="form-filter-label">
                            <i class="fa fa-graduation-cap mr-1 text-warning"></i> Tahun Ajaran
                        </label>
                        <select name="akademik_master_tahun_ajaran_id" class="form-control form-filter-control">
                            <option value="">Semua Tahun Ajaran</option>
                            @foreach($tahunAjaranList as $ta)
                                <option value="{{ $ta->id }}" {{ request('akademik_master_tahun_ajaran_id') == $ta->id ? 'selected' : '' }}>
                                    {{ $ta->nama_tahun_ajaran }} (Sem. {{ $ta->semester }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 3. Tanggal Mulai -->
                    <div class="col-md-2 col-sm-6 mb-2 mb-md-0">
                        <label class="form-filter-label">
                            <i class="fa fa-calendar-alt mr-1 text-warning"></i> Mulai Dari
                        </label>
                        <input type="date" name="tanggal_mulai" class="form-control form-filter-control" value="{{ request('tanggal_mulai') }}">
                    </div>

                    <!-- 4. Tanggal Selesai -->
                    <div class="col-md-2 col-sm-6 mb-2 mb-md-0">
                        <label class="form-filter-label">
                            <i class="fa fa-calendar-check mr-1 text-warning"></i> Sampai Tgl
                        </label>
                        <input type="date" name="tanggal_selesai" class="form-control form-filter-control" value="{{ request('tanggal_selesai') }}">
                    </div>

                    <!-- 5. Tombol Aksi Filter & Reset -->
                    <div class="col-md-2 col-sm-12 mb-2 mb-md-0">
                        <div class="d-flex w-100" style="gap: 6px; width: 100%;">
                            <button type="submit" class="btn text-white font-weight-bold d-flex align-items-center justify-content-center"
                                style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 8px; height: 38px; border: none; flex: 1 1 auto; width: 100%; box-shadow: 0 2px 6px rgba(234, 88, 12, 0.2);">
                                <i class="fa fa-filter mr-1"></i> Filter
                            </button>
                            <a href="{{ route('kegiatan.index') }}" class="btn btn-light border font-weight-bold shadow-sm d-flex align-items-center justify-content-center"
                                style="border-radius: 8px; height: 38px; width: 38px; min-width: 38px; flex-shrink: 0;" title="Reset Filter">
                                <i class="fa fa-undo"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Kegiatan List Table --}}
    <div class="main-card mb-4 card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
        <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center" style="gap: 10px;">
            <div class="d-flex align-items-center">
                <div class="d-inline-flex align-items-center justify-content-center text-white mr-2"
                    style="width: 28px; height: 28px; border-radius: 8px; background: linear-gradient(135deg, #ea580c 0%, #d97706 100%); box-shadow: 0 2px 5px rgba(234, 88, 12, 0.25); flex-shrink: 0;">
                    <i class="fa fa-calendar-alt" style="font-size: 12px;"></i>
                </div>
                <span class="font-weight-bold text-dark" style="font-size: 14.5px;">Daftar Agenda Kegiatan Lembaga</span>
            </div>
            <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                @if(request()->hasAny(['search', 'akademik_master_tahun_ajaran_id', 'tanggal_mulai', 'tanggal_selesai']))
                    <span class="badge px-2.5 py-1 text-white font-weight-bold" style="font-size: 11px; border-radius: 6px; background-color: #f59e0b;">
                        <i class="fa fa-filter mr-1"></i> Terfilter
                    </span>
                @endif
                <span class="badge badge-pill px-3 py-1.5 font-weight-bold"
                    style="background-color: #fff7ed; color: #ea580c; border: 1px solid #fed7aa; font-size: 11.5px;">
                    Total: {{ $kegiatanList->total() }} Kegiatan
                </span>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="min-width: 780px;">
                <thead class="thead-light">
                    <tr>
                        <th class="text-center" width="50">#</th>
                        <th>Nama Kegiatan & Pengisi</th>
                        <th>Jadwal & Lokasi</th>
                        <th>Tahun Ajaran</th>
                        <th class="text-center">Presensi Hadir</th>
                        <th class="text-center">Status Gerbang</th>
                        <th class="text-center" style="width: 70px;">Aksi</th>
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
                                <div class="action-dropdown">
                                    <button type="button" class="btn-action-dots" title="Menu Aksi">
                                        <i class="fa fa-ellipsis-v"></i>
                                    </button>
                                    <div class="action-dropdown-menu">
                                        <a href="{{ route('kegiatan.qr', $kegiatan->id) }}" target="_blank" class="action-dropdown-item item-warning">
                                            <span class="action-item-icon"><i class="fa fa-qrcode"></i></span>
                                            <span>Layar QR Proyektor</span>
                                        </a>
                                        <a href="{{ route('kegiatan.show', $kegiatan->id) }}" class="action-dropdown-item item-view">
                                            <span class="action-item-icon"><i class="fa fa-users"></i></span>
                                            <span>Rekap Kehadiran</span>
                                        </a>
                                        <a href="{{ route('kegiatan.edit', $kegiatan->id) }}" class="action-dropdown-item item-edit">
                                            <span class="action-item-icon"><i class="fa fa-edit"></i></span>
                                            <span>Edit Kegiatan</span>
                                        </a>
                                        <div class="action-dropdown-divider"></div>
                                        <form action="{{ route('kegiatan.destroy', $kegiatan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kegiatan ini beserta riwayat kehadirannya?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-dropdown-item item-delete">
                                                <span class="action-item-icon"><i class="fa fa-trash"></i></span>
                                                <span>Hapus Kegiatan</span>
                                            </button>
                                        </form>
                                    </div>
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
            <div class="card-footer bg-white border-top py-2.5 px-3 d-flex flex-column flex-sm-row justify-content-between align-items-center" style="gap: 10px;">
                <small class="text-muted text-center text-sm-left">
                    Menampilkan {{ $kegiatanList->firstItem() }} - {{ $kegiatanList->lastItem() }} dari {{ $kegiatanList->total() }} kegiatan
                </small>
                <div class="overflow-auto" style="max-width: 100%;">
                    {{ $kegiatanList->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
