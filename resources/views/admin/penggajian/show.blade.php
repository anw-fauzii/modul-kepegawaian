@extends('layouts.appui')

@section('title')
    <title>Slip Gaji - {{ $penggajian->karyawan?->nama_lengkap ?? $penggajian->karyawan_nipy }}</title>
@endsection

@section('content')
@php
    $rp = fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
    $k = $penggajian->karyawan;
@endphp
<style>
    .slip { max-width: 860px; margin: 0 auto; background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden; box-shadow: 0 4px 14px rgba(15,23,42,.06); }
    .slip__head { background: linear-gradient(135deg, #0f172a, #1e293b); color: #fff; padding: 22px 28px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; }
    .slip__body { padding: 22px 28px; }
    .slip__meta dt { color: #94a3b8; font-size: .7rem; text-transform: uppercase; letter-spacing: .05em; font-weight: 600; }
    .slip__meta dd { color: #0f172a; font-weight: 600; margin-bottom: 10px; }
    .slip table td { padding: 6px 0; font-size: .86rem; border-bottom: 1px dashed #e2e8f0; }
    .slip table tr:last-child td { border-bottom: 0; }
    .slip__total { background: #fff7ed; border-top: 2px solid #ea580c; padding: 18px 28px; display: flex; justify-content: space-between; align-items: center; }
    @media print {
        .no-print, .app-sidebar, .app-header { display: none !important; }
        .slip { box-shadow: none; border: 1px solid #999; }
    }
</style>

<div class="app-main__inner">
    <div class="app-page-title mb-3 no-print">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon bg-success text-white"><i class="pe-7s-cash"></i></div>
                <div>
                    <strong>{{ $penggajian->slip_judul }}</strong>
                    <div class="page-title-subheading">{{ $k?->nama_lengkap ?? $penggajian->karyawan_nipy }} &bull; {{ $penggajian->periode_teks }}</div>
                </div>
            </div>
            <div class="page-title-actions d-flex align-items-center" style="gap: 8px;">
                <a href="{{ route('penggajian.index', ['jenis' => $penggajian->jenis]) }}" class="btn btn-outline-secondary font-weight-bold"><i class="fa fa-arrow-left mr-1"></i> Kembali</a>
                <button type="button" class="btn btn-outline-dark font-weight-bold" onclick="window.print()"><i class="fa fa-print mr-1"></i> Cetak</button>
                @if ($penggajian->status !== 'dibayar')
                    <a href="{{ route('penggajian.edit', $penggajian) }}" class="btn btn-primary font-weight-bold"><i class="fa fa-pen mr-1"></i> Edit</a>
                @endif
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success border-0 shadow-sm no-print"><i class="fa fa-check-circle mr-2"></i>{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger border-0 shadow-sm no-print"><i class="fa fa-exclamation-triangle mr-2"></i>{{ session('error') }}</div>
    @endif

    {{-- Ubah status --}}
    <form action="{{ route('penggajian.update-status', $penggajian) }}" method="POST" class="no-print d-flex align-items-center justify-content-end mb-3" style="gap: 8px;">
        @csrf
        @method('PATCH')
        <span class="small text-muted font-weight-bold">Status slip:</span>
        <select name="status" class="form-control form-control-sm" style="width: 140px;">
            @foreach ($statusList as $key => $label)
                <option value="{{ $key }}" @selected($penggajian->status === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-sm btn-success font-weight-bold">Ubah</button>
    </form>

    <div class="slip">
        <div class="slip__head">
            <div>
                <div class="small" style="color:#fb923c; letter-spacing:.08em; font-weight:700;">YAYASAN PRIMA INSANI</div>
                <h4 class="mb-0 font-weight-bold text-white">{{ $penggajian->slip_judul }}</h4>
                <div class="small" style="color:#cbd5e1;">
                    @if ($penggajian->jenis === 'thr')
                        Tahun {{ $penggajian->tahun_thr }}
                    @elseif ($penggajian->jenis === 'bonus')
                        Tahun Ajaran {{ $penggajian->bulan?->tahunAjaran?->nama_tahun_ajaran ?? '-' }}
                    @else
                        Periode {{ $penggajian->bulan?->nama_bulan ?? '-' }}
                        @if ($penggajian->bulan?->tahunAjaran) &bull; TA {{ $penggajian->bulan->tahunAjaran->nama_tahun_ajaran }} @endif
                    @endif
                </div>
            </div>
            <div>
                <span class="badge {{ $penggajian->jenis_badge_class }} px-3 py-2 mr-1" style="font-size: .8rem;">{{ $penggajian->jenis_label }}</span>
                <span class="badge {{ $penggajian->status_badge_class }} px-3 py-2" style="font-size: .8rem;">{{ $penggajian->status_label }}</span>
            </div>
        </div>

        <div class="slip__body">
            <dl class="row slip__meta mb-2">
                <div class="col-md-4"><dt>Nama</dt><dd>{{ $k?->nama_gelar ?? '-' }}</dd></div>
                <div class="col-md-4"><dt>NIPY</dt><dd>{{ $penggajian->karyawan_nipy }}</dd></div>
                <div class="col-md-4"><dt>Email</dt><dd>{{ $k?->email ?: '-' }}</dd></div>
                <div class="col-md-4"><dt>Unit</dt><dd>{{ $penggajian->unit ?: '-' }}</dd></div>
                <div class="col-md-4"><dt>Jabatan</dt><dd>{{ $penggajian->jabatan ?: '-' }}</dd></div>
                <div class="col-md-4"><dt>Golongan</dt><dd>{{ $penggajian->golongan ?: '-' }}</dd></div>
            </dl>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <h6 class="font-weight-bold text-success border-bottom pb-2"><i class="fa fa-plus-circle mr-1"></i> Penerimaan</h6>
                    <table class="w-100">
                        @foreach ($penerimaanList as $f => $label)
                            @if ((float) $penggajian->{$f} != 0)
                                <tr><td>{{ $label }}</td><td class="text-right">{{ $rp($penggajian->{$f}) }}</td></tr>
                            @endif
                        @endforeach
                        <tr><td class="font-weight-bold">Total Penerimaan</td><td class="text-right font-weight-bold text-success">{{ $rp($penggajian->penerimaan) }}</td></tr>
                    </table>
                </div>
                <div class="col-md-6 mb-3">
                    <h6 class="font-weight-bold text-danger border-bottom pb-2"><i class="fa fa-minus-circle mr-1"></i> Potongan</h6>
                    <table class="w-100">
                        @forelse ($potonganList as $f => $label)
                            @if ((float) $penggajian->{$f} != 0)
                                <tr><td>{{ $label }}</td><td class="text-right">{{ $rp($penggajian->{$f}) }}</td></tr>
                            @endif
                        @empty
                        @endforelse
                        <tr><td class="font-weight-bold">Total Potongan</td><td class="text-right font-weight-bold text-danger">{{ $rp($penggajian->potongan) }}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="slip__total">
            <span class="font-weight-bold text-uppercase" style="letter-spacing:.05em;">Gaji Bersih Diterima</span>
            <span class="font-weight-bold" style="font-size: 1.6rem; color:#ea580c;">{{ $rp($penggajian->gaji_bersih) }}</span>
        </div>
    </div>
</div>
@endsection
