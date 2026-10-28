@extends('layouts.appui')

@section('title')
    <title>Penggajian Karyawan - SIPIT</title>
@endsection

@section('content')
@php
    $rp = fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
@endphp
<style>
    .stat-card { border-radius: 12px; border: 1px solid #e2e8f0; background: #fff; box-shadow: 0 2px 4px rgba(0,0,0,.02); transition: transform .2s, box-shadow .2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 6px 12px rgba(0,0,0,.05); }
    .table td, .table th { vertical-align: middle; }
</style>

<div class="app-main__inner">
    <div class="app-page-title mb-3">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon bg-success text-white shadow-sm" style="border-radius: 12px;">
                    <i class="pe-7s-cash"></i>
                </div>
                <div>
                    <strong>Penggajian Karyawan</strong>
                    <div class="page-title-subheading">Slip gaji bulanan: penerimaan, potongan, dan gaji bersih pegawai.</div>
                </div>
            </div>
            <div class="page-title-actions">
                <a href="{{ route('penggajian.create') }}" class="btn btn-primary font-weight-bold shadow-sm">
                    <i class="fa fa-plus-circle mr-1"></i> Tambah Slip Gaji
                </a>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3">
            <i class="fa fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3">
            <i class="fa fa-exclamation-triangle mr-2"></i>{{ session('error') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    {{-- Statistik --}}
    <div class="row mb-3">
        @foreach ([
            ['Total Slip', number_format($statistik->total_slip), '#0284c7', 'fa-file-invoice', 'text-primary'],
            ['Total Penerimaan', $rp($statistik->total_penerimaan), '#10b981', 'fa-arrow-down', 'text-success'],
            ['Total Potongan', $rp($statistik->total_potongan), '#ef4444', 'fa-arrow-up', 'text-danger'],
            ['Total Gaji Bersih', $rp($statistik->total_gaji_bersih), '#ea580c', 'fa-wallet', 'text-warning'],
        ] as [$label, $value, $color, $icon, $text])
            <div class="col-xl-3 col-md-6 mb-2">
                <div class="stat-card p-3 d-flex align-items-center" style="border-left: 4px solid {{ $color }};">
                    <div class="rounded-circle p-3 mr-3 bg-light {{ $text }}"><i class="fa {{ $icon }} fa-lg"></i></div>
                    <div>
                        <span class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: .5px;">{{ $label }}</span>
                        <h5 class="mb-0 font-weight-bold text-dark mt-1">{{ $value }}</h5>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Filter --}}
    <div class="main-card card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('penggajian.index') }}" class="row align-items-end">
                <div class="col-lg-4 mb-2 mb-lg-0">
                    <label class="font-weight-bold small text-muted mb-1">Pencarian</label>
                    <input type="text" name="search" class="form-control" placeholder="Cari NIPY, nama, unit, jabatan..." value="{{ $filters['search'] ?? '' }}">
                </div>
                <div class="col-lg-3 mb-2 mb-lg-0">
                    <label class="font-weight-bold small text-muted mb-1">Periode Bulan</label>
                    <select name="kepegawaian_master_bulan_id" class="form-control">
                        <option value="">-- Semua Bulan --</option>
                        @foreach ($bulanList as $b)
                            <option value="{{ $b->id }}" @selected(($filters['kepegawaian_master_bulan_id'] ?? '') == $b->id)>
                                {{ $b->nama_bulan }}{{ $b->tahunAjaran ? ' - ' . $b->tahunAjaran->nama_tahun_ajaran : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 mb-2 mb-lg-0">
                    <label class="font-weight-bold small text-muted mb-1">Status</label>
                    <select name="status" class="form-control">
                        <option value="">-- Semua --</option>
                        @foreach (\App\Models\Admin\Penggajian\Penggajian::STATUS as $key => $label)
                            <option value="{{ $key }}" @selected(($filters['status'] ?? '') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 d-flex justify-content-lg-end">
                    <button type="submit" class="btn btn-primary mr-2"><i class="fas fa-filter mr-1"></i> Filter</button>
                    <a href="{{ route('penggajian.index') }}" class="btn btn-outline-secondary"><i class="fas fa-redo mr-1"></i> Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="main-card card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-header bg-white border-bottom font-weight-bold d-flex justify-content-between align-items-center py-3 px-3">
            <span>Daftar Slip Gaji</span>
            <span class="badge badge-primary">{{ $penggajianList->total() }} Data</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>Karyawan</th>
                            <th>Periode</th>
                            <th class="text-right">Penerimaan</th>
                            <th class="text-right">Potongan</th>
                            <th class="text-right">Gaji Bersih</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($penggajianList as $i => $item)
                            <tr>
                                <td class="text-center text-muted font-weight-bold">{{ $penggajianList->firstItem() + $i }}</td>
                                <td>
                                    <div class="d-flex align-items-center" style="gap: 10px;">
                                        @if ($item->karyawan?->foto_url)
                                            <img src="{{ $item->karyawan->foto_url }}" alt="Foto" style="width: 36px; height: 36px; object-fit: cover; border-radius: 50%;">
                                        @else
                                            <span class="d-flex align-items-center justify-content-center text-white font-weight-bold"
                                                style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg,#ea580c,#d97706); font-size: .8rem;">
                                                {{ strtoupper(mb_substr($item->karyawan?->nama_lengkap ?? '?', 0, 1)) }}
                                            </span>
                                        @endif
                                        <div>
                                            <div class="font-weight-bold text-dark">{{ $item->karyawan?->nama_lengkap ?? 'Data terhapus' }}</div>
                                            <small class="text-muted"><code>{{ $item->karyawan_nipy }}</code> &bull; {{ $item->unit ?: '-' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-light border font-weight-bold">{{ $item->bulan?->nama_bulan ?? '-' }}</span>
                                    @if ($item->bulan?->tahunAjaran)
                                        <div class="small text-muted">{{ $item->bulan->tahunAjaran->nama_tahun_ajaran }}</div>
                                    @endif
                                </td>
                                <td class="text-right text-success">{{ $rp($item->penerimaan) }}</td>
                                <td class="text-right text-danger">{{ $rp($item->potongan) }}</td>
                                <td class="text-right font-weight-bold text-dark">{{ $rp($item->gaji_bersih) }}</td>
                                <td class="text-center"><span class="badge {{ $item->status_badge_class }} px-2 py-1">{{ $item->status_label }}</span></td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('penggajian.show', $item) }}" class="btn btn-sm btn-outline-info" title="Lihat Slip"><i class="fa fa-eye"></i></a>
                                        @if ($item->status !== 'dibayar')
                                            <a href="{{ route('penggajian.edit', $item) }}" class="btn btn-sm btn-outline-primary ml-1" title="Edit"><i class="fa fa-pen"></i></a>
                                            <form action="{{ route('penggajian.destroy', $item) }}" method="POST" class="d-inline ml-1" onsubmit="return confirm('Hapus slip gaji ini?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="fa fa-trash"></i></button>
                                            </form>
                                        @else
                                            <span class="btn btn-sm btn-light ml-1 disabled" title="Terkunci (sudah dibayar)"><i class="fa fa-lock"></i></span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fa fa-folder-open fa-3x mb-3 text-secondary"></i>
                                    <h5 class="font-weight-bold text-dark">Belum ada slip gaji</h5>
                                    <p class="small mb-3">Tidak ada data yang sesuai kriteria filter.</p>
                                    <a href="{{ route('penggajian.create') }}" class="btn btn-sm btn-primary font-weight-bold"><i class="fa fa-plus-circle mr-1"></i> Tambah Slip Gaji</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($penggajianList->hasPages())
            <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center py-2 px-3">
                <span class="small text-muted">Menampilkan {{ $penggajianList->firstItem() }} - {{ $penggajianList->lastItem() }} dari {{ $penggajianList->total() }} data</span>
                <div>{{ $penggajianList->links() }}</div>
            </div>
        @endif
    </div>
</div>
@endsection
