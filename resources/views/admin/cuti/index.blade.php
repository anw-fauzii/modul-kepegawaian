@extends('layouts.appui')

@section('title')
    <title>Manajemen Cuti Pegawai - SIPIT</title>
@endsection

@section('content')
<style>
    .stat-card-cuti {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card-cuti:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.05);
    }
    .badge-soft-success {
        background-color: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }
    .badge-soft-warning {
        background-color: #fffbeb;
        color: #b45309;
        border: 1px solid #fde68a;
    }
    .badge-soft-info {
        background-color: #f0f9ff;
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
                <div class="page-title-icon text-white shadow-sm"
                    style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 12px;">
                    <i class="pe-7s-note2" style="color: white;"></i>
                </div>
                <div>
                    <strong>Cuti Pegawai</strong>
                    <div class="page-title-subheading">
                        Kelola formulir permohonan cuti, jenis cuti, rentang hari, alamat & kontak, serta persetujuan (ACC).
                    </div>
                </div>
            </div>
            <div class="page-title-actions">
                <a href="{{ route('cuti.create') }}" class="btn text-white font-weight-bold shadow-sm"
                    style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 8px;">
                    <i class="fa fa-plus-circle mr-1.5"></i> AJUKAN CUTI BARU
                </a>
            </div>
        </div>
    </div>

    {{-- Alerts Notifikasi --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3" role="alert" style="border-radius: 10px;">
            <i class="fa fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3" role="alert" style="border-radius: 10px;">
            <i class="fa fa-exclamation-triangle mr-2"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- 4 KARTU STATISTIK KPI CUTI TAHUN INI --}}
    <div class="row mb-3">
        <div class="col-md-3 col-sm-6 mb-2">
            <div class="stat-card-cuti p-3 d-flex align-items-center" style="border-left: 4px solid #0284c7;">
                <div class="rounded-circle p-3 mr-3 bg-light text-primary flex-shrink-0" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                    <i class="fa fa-folder-open fa-lg"></i>
                </div>
                <div class="min-w-0">
                    <span class="text-muted text-uppercase font-weight-bold" style="font-size: 10.5px; letter-spacing: 0.5px;">Total Pengajuan ({{ $statistik['tahun'] }})</span>
                    <h4 class="mb-0 font-weight-bold text-dark">{{ $statistik['total'] }} <small class="text-muted" style="font-size: 12px;">Berkas</small></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-2">
            <div class="stat-card-cuti p-3 d-flex align-items-center" style="border-left: 4px solid #10b981;">
                <div class="rounded-circle p-3 mr-3 bg-light text-success flex-shrink-0" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                    <i class="fa fa-check-double fa-lg"></i>
                </div>
                <div class="min-w-0">
                    <span class="text-muted text-uppercase font-weight-bold" style="font-size: 10.5px; letter-spacing: 0.5px;">Disetujui (Sudah ACC)</span>
                    <h4 class="mb-0 font-weight-bold text-success">{{ $statistik['disetujui'] }} <small class="text-muted" style="font-size: 12px;">Disetujui</small></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-2">
            <div class="stat-card-cuti p-3 d-flex align-items-center" style="border-left: 4px solid #f59e0b;">
                <div class="rounded-circle p-3 mr-3 bg-light text-warning flex-shrink-0" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                    <i class="fa fa-hourglass-half fa-lg"></i>
                </div>
                <div class="min-w-0">
                    <span class="text-muted text-uppercase font-weight-bold" style="font-size: 10.5px; letter-spacing: 0.5px;">Menunggu Persetujuan</span>
                    <h4 class="mb-0 font-weight-bold text-warning">{{ $statistik['menunggu'] }} <small class="text-muted" style="font-size: 12px;">Menunggu ACC</small></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-2">
            <div class="stat-card-cuti p-3 d-flex align-items-center" style="border-left: 4px solid #8b5cf6;">
                <div class="rounded-circle p-3 mr-3 bg-light text-purple flex-shrink-0" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; color: #8b5cf6;">
                    <i class="fa fa-calendar-check fa-lg"></i>
                </div>
                <div class="min-w-0">
                    <span class="text-muted text-uppercase font-weight-bold" style="font-size: 10.5px; letter-spacing: 0.5px;">Total Hari Cuti ACC</span>
                    <h4 class="mb-0 font-weight-bold text-dark">{{ $statistik['total_hari'] }} <small class="text-muted" style="font-size: 12px;">Hari Kerja</small></h4>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER DATA CARD --}}
    <div class="main-card card border-0 shadow-sm mb-3" style="border-radius: 12px;">
        <div class="card-body p-3">
            <form action="{{ route('cuti.index') }}" method="GET" class="form-row align-items-end">
                <div class="col-md-3 col-sm-6 mb-2">
                    <label class="small font-weight-bold text-muted mb-1">Pencarian</label>
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control" placeholder="NIPY, nama pegawai, alamat, telp..."
                            value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 mb-2">
                    <label class="small font-weight-bold text-muted mb-1">Kategori Jenis Cuti</label>
                    <select name="kepegawaian_master_jenis_cuti_id" class="form-control form-control-sm">
                        <option value="">-- Semua Jenis Cuti --</option>
                        @foreach ($jenisCutiOptions as $jopt)
                            <option value="{{ $jopt->id }}" {{ request('kepegawaian_master_jenis_cuti_id') == $jopt->id ? 'selected' : '' }}>
                                {{ $jopt->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 col-sm-6 mb-2">
                    <label class="small font-weight-bold text-muted mb-1">Status ACC</label>
                    <select name="acc" class="form-control form-control-sm">
                        <option value="">-- Semua Status --</option>
                        <option value="1" {{ request('acc') === '1' ? 'selected' : '' }}>Sudah ACC (Disetujui)</option>
                        <option value="0" {{ request('acc') === '0' ? 'selected' : '' }}>Menunggu ACC</option>
                    </select>
                </div>

                <div class="col-md-2 col-sm-6 mb-2">
                    <label class="small font-weight-bold text-muted mb-1">Tahun Cuti</label>
                    <input type="number" name="tahun" class="form-control form-control-sm" placeholder="Contoh: {{ date('Y') }}"
                        value="{{ request('tahun') }}">
                </div>

                <div class="col-md-2 mb-2 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary font-weight-bold flex-grow-1" style="border-radius: 7px;">
                        <i class="fa fa-filter mr-1"></i> Filter
                    </button>
                    @if(request()->anyFilled(['search', 'kepegawaian_master_jenis_cuti_id', 'acc', 'tahun']))
                        <a href="{{ route('cuti.index') }}" class="btn btn-sm btn-outline-secondary ml-1" style="border-radius: 7px;" title="Reset Filter">
                            <i class="fa fa-sync-alt"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- TABEL DATA CUTI --}}
    <div class="main-card card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase font-weight-bold">
                        <tr>
                            <th class="py-3 px-3" style="width: 5%;">No</th>
                            <th class="py-3 px-3">Pegawai Pemohon</th>
                            <th class="py-3 px-3">Jenis Cuti</th>
                            <th class="py-3 px-3">Rentang & Durasi</th>
                            <th class="py-3 px-3">Kontak & Alamat</th>
                            <th class="py-3 px-3 text-center">Status ACC</th>
                            <th class="py-3 px-3 text-center" style="width: 70px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cutiList as $cuti)
                            <tr>
                                <td class="px-3">{{ $cutiList->firstItem() + $loop->index }}</td>
                                <td class="px-3">
                                    <div class="d-flex align-items-center">
                                        <div class="mr-2.5">
                                            @if ($cuti->karyawan?->foto_url)
                                                <img src="{{ $cuti->karyawan->foto_url }}" alt="Foto"
                                                    class="rounded-circle border" style="width: 36px; height: 36px; object-fit: cover;">
                                            @else
                                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-primary font-weight-bold border"
                                                    style="width: 36px; height: 36px; font-size: 13px;">
                                                    {{ strtoupper(substr($cuti->karyawan?->nama_lengkap ?? 'P', 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-weight-bold text-dark text-truncate" style="font-size: 13px;">
                                                {{ $cuti->karyawan?->nama_gelar ?? $cuti->karyawan?->nama_lengkap ?? $cuti->karyawan_nipy }}
                                            </div>
                                            <div class="small text-muted">
                                                NIPY: <code class="text-secondary">{{ $cuti->karyawan_nipy }}</code>
                                                @if ($cuti->karyawan?->unit)
                                                    &bull; <span class="badge badge-light border">{{ $cuti->karyawan->unit }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3">
                                    <span class="badge badge-soft-info px-2 py-1 font-weight-bold" style="font-size: 11.5px; border-radius: 6px;">
                                        <i class="fa fa-tag mr-1"></i> {{ $cuti->jenisCuti?->nama ?? 'Cuti' }}
                                    </span>
                                </td>
                                <td class="px-3">
                                    <div class="font-weight-bold text-dark" style="font-size: 12.5px;">
                                        <i class="fa fa-calendar-alt text-primary mr-1"></i> {{ $cuti->periode_formatted }}
                                    </div>
                                    <div class="mt-1">
                                        <span class="badge badge-light border font-weight-bold px-2 py-0.5 text-muted" style="font-size: 11px;">
                                            <i class="fa fa-clock text-warning mr-1"></i> {{ $cuti->jumlah_hari }} Hari
                                        </span>
                                    </div>
                                </td>
                                <td class="px-3">
                                    <div class="small">
                                        <div class="text-dark font-weight-semibold">
                                            <i class="fa fa-phone text-muted mr-1"></i> {{ $cuti->telepon }}
                                        </div>
                                        <div class="text-muted text-truncate mt-0.5" style="max-width: 200px;" title="{{ $cuti->alamat }}">
                                            <i class="fa fa-map-marker-alt text-danger mr-1"></i> {{ $cuti->alamat }}
                                        </div>
                                        @if ($cuti->alasan)
                                            <div class="text-muted small font-italic mt-0.5 text-truncate" style="max-width: 200px;" title="{{ $cuti->alasan }}">
                                                Keperluan: "{{ $cuti->alasan }}"
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-3 text-center">
                                    @if ($cuti->acc)
                                        <span class="badge badge-soft-success px-2.5 py-1 font-weight-bold" style="font-size: 11px; border-radius: 6px;">
                                            <i class="fa fa-check-circle mr-1"></i> Disetujui (ACC)
                                        </span>
                                        @if ($cuti->tgl_acc)
                                            <div class="text-muted" style="font-size: 10px; margin-top: 2px;">
                                                {{ $cuti->tgl_acc->format('d/m/Y H:i') }}
                                            </div>
                                        @endif
                                    @else
                                        <span class="badge badge-soft-warning px-2.5 py-1 font-weight-bold" style="font-size: 11px; border-radius: 6px;">
                                            <i class="fa fa-hourglass-half mr-1"></i> Menunggu ACC
                                        </span>
                                    @endif
                                </td>
                                <td class="px-3 text-center">
                                    <div class="action-dropdown">
                                        <button type="button" class="btn-action-dots" title="Menu Aksi">
                                            <i class="fa fa-ellipsis-v"></i>
                                        </button>
                                        <div class="action-dropdown-menu">
                                            {{-- TOMBOL TOGGLE ACC --}}
                                            <form action="{{ route('cuti.toggle-acc', $cuti->id) }}" method="POST" onsubmit="return confirm('{{ $cuti->acc ? 'Batalkan ACC untuk cuti ini?' : 'Setujui (ACC) pengajuan cuti pegawai ini?' }}')">
                                                @csrf
                                                <button type="submit" class="action-dropdown-item {{ $cuti->acc ? 'item-warning' : 'item-success' }}">
                                                    <span class="action-item-icon"><i class="fa {{ $cuti->acc ? 'fa-undo' : 'fa-check' }}"></i></span>
                                                    <span>{{ $cuti->acc ? 'Batalkan ACC' : 'Setujui (ACC)' }}</span>
                                                </button>
                                            </form>

                                            {{-- TOMBOL EDIT --}}
                                            <a href="{{ route('cuti.edit', $cuti->id) }}" class="action-dropdown-item item-edit">
                                                <span class="action-item-icon"><i class="fa fa-edit"></i></span>
                                                <span>Edit Pengajuan</span>
                                            </a>

                                            <div class="action-dropdown-divider"></div>

                                            {{-- TOMBOL HAPUS --}}
                                            <form action="{{ route('cuti.destroy', $cuti->id) }}" method="POST" class="delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="action-dropdown-item item-delete delete-button">
                                                    <span class="action-item-icon"><i class="fa fa-trash"></i></span>
                                                    <span>Hapus Data Cuti</span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="fa fa-calendar-times fa-3x opacity-4 mb-2 text-secondary" style="display: block;"></i>
                                    Belum ada data permohonan cuti yang tercatat.
                                    <div class="mt-2">
                                        <a href="{{ route('cuti.create') }}" class="btn btn-sm btn-primary">
                                            <i class="fa fa-plus-circle mr-1"></i> Ajukan Cuti Sekarang
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION LINKS --}}
            @if ($cutiList->hasPages())
                <div class="p-3 border-top d-flex justify-content-between align-items-center">
                    <div class="small text-muted">
                        Menampilkan {{ $cutiList->firstItem() }} s/d {{ $cutiList->lastItem() }} dari {{ $cutiList->total() }} data cuti
                    </div>
                    <div>
                        {{ $cutiList->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
