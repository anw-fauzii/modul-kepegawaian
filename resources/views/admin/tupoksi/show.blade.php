@extends('layouts.appui')

@section('title')
    <title>Detail Tupoksi #{{ $tupoksi->id }} - SIPIT</title>
@endsection

@section('content')
<div class="app-main__inner">
    <div class="app-page-title mb-3">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon bg-info text-white">
                    <i class="pe-7s-id icon-gradient bg-sunny-morning"></i>
                </div>
                <div>
                    <strong>Detail Tupoksi #{{ $tupoksi->id }}</strong>
                    <div class="page-title-subheading">
                        Rincian penetapan tugas pokok & fungsi karyawan beserta dokumen berkas
                    </div>
                </div>
            </div>
            <div class="page-title-actions">
                <a href="{{ route('tupoksi.index') }}" class="btn btn-secondary shadow-sm mr-1">
                    <i class="fa fa-arrow-left mr-1"></i> Kembali
                </a>
                <a href="{{ route('tupoksi.edit', $tupoksi->id) }}" class="btn btn-warning text-white shadow-sm mr-1">
                    <i class="fa fa-pencil mr-1"></i> Edit Tupoksi
                </a>
                @if($tupoksi->berkas)
                    <a href="{{ route('tupoksi.download', $tupoksi->id) }}" class="btn btn-success shadow-sm">
                        <i class="fa fa-download mr-1"></i> Unduh Berkas
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Metadata Tupoksi & Karyawan --}}
        <div class="col-md-5 mb-3">
            <div class="main-card card shadow-sm mb-3">
                <div class="card-header bg-white font-weight-bold">
                    Informasi Tugas Pokok & Fungsi
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <tr>
                            <th style="width: 40%;">Jabatan / Tugas</th>
                            <td>
                                <strong class="text-primary">{{ $tupoksi->jabatan }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <th>Tahun Ajaran</th>
                            <td>
                                @if($tupoksi->tahunAjaran)
                                    <span class="badge {{ $tupoksi->tahunAjaran->is_active ? 'badge-success' : 'badge-primary' }}">
                                        {{ $tupoksi->tahunAjaran->nama_tahun_ajaran }} (Semester {{ $tupoksi->tahunAjaran->semester }})
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Nama Berkas</th>
                            <td>
                                @if($tupoksi->berkas)
                                    <code class="text-break">{{ $tupoksi->berkas_filename }}</code>
                                @else
                                    <span class="text-muted font-italic">Tidak ada berkas fisik</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Dibuat Pada</th>
                            <td>{{ $tupoksi->created_at?->format('d M Y, H:i') }} WIB</td>
                        </tr>
                        <tr>
                            <th>Terakhir Diperbarui</th>
                            <td>{{ $tupoksi->updated_at?->format('d M Y, H:i') }} WIB</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Profil Karyawan --}}
            <div class="main-card card shadow-sm">
                <div class="card-header bg-white font-weight-bold">
                    Profil Karyawan yang Ditugaskan
                </div>
                <div class="card-body">
                    @if($tupoksi->karyawan)
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-3" style="width: 50px; height: 50px; font-size: 20px; font-weight: bold;">
                                {{ strtoupper(substr($tupoksi->karyawan->nama_lengkap, 0, 1)) }}
                            </div>
                            <div>
                                <h5 class="mb-0 font-weight-bold text-dark">
                                    {{ $tupoksi->karyawan->nama_lengkap }}{{ $tupoksi->karyawan->gelar ? ', ' . $tupoksi->karyawan->gelar : '' }}
                                </h5>
                                <div class="text-muted small">
                                    NIPY: <code>{{ $tupoksi->karyawan->nipy }}</code>
                                </div>
                            </div>
                        </div>

                        <ul class="list-group list-group-flush small">
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Unit Kerja</span>
                                <span class="font-weight-bold">{{ $tupoksi->karyawan->unit ?? '-' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Jabatan Pokok Master</span>
                                <span class="font-weight-bold">{{ $tupoksi->karyawan->jabatan ?? '-' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Kategori Pegawai</span>
                                <span class="font-weight-bold">{{ $tupoksi->karyawan->kategori->nama_kategori ?? '-' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Kontak WhatsApp</span>
                                <span>{{ $tupoksi->karyawan->whatsapp ?? '-' }}</span>
                            </li>
                        </ul>
                    @else
                        <div class="alert alert-warning mb-0">
                            <i class="fa fa-exclamation-triangle mr-1"></i> Data master karyawan dengan NIPY <strong>{{ $tupoksi->karyawan_nipy }}</strong> telah dihapus atau tidak ditemukan.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Dokumen & Uraian Tugas --}}
        <div class="col-md-7 mb-3">
            {{-- Dokumen Berkas --}}
            <div class="main-card card shadow-sm mb-3">
                <div class="card-header bg-white font-weight-bold d-flex justify-content-between align-items-center">
                    <span>Berkas Dokumen Tupoksi</span>
                    @if($tupoksi->berkas)
                        <a href="{{ route('tupoksi.download', $tupoksi->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fa fa-download mr-1"></i> Unduh Berkas
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    @if($tupoksi->berkas)
                        @php
                            $ext = strtolower(pathinfo($tupoksi->berkas, PATHINFO_EXTENSION));
                        @endphp

                        @if(in_array($ext, ['jpg', 'jpeg', 'png']))
                            <div class="text-center p-2 bg-light rounded border">
                                <img src="{{ $tupoksi->berkas_url }}" alt="Dokumen Tupoksi" class="img-fluid rounded shadow-sm" style="max-height: 500px;">
                            </div>
                        @elseif($ext === 'pdf')
                            <div class="embed-responsive embed-responsive-4by3 rounded border shadow-sm" style="min-height: 480px;">
                                <iframe class="embed-responsive-item" src="{{ $tupoksi->berkas_url }}" allowfullscreen></iframe>
                            </div>
                        @else
                            <div class="text-center py-5 bg-light rounded border">
                                <i class="fa fa-file-text-o fa-4x text-primary mb-3"></i>
                                <h6 class="font-weight-bold">{{ $tupoksi->berkas_filename }}</h6>
                                <p class="text-muted small mb-3">Format berkas: .{{ strtoupper($ext) }}. Silakan unduh dokumen untuk melihat isi berkas.</p>
                                <a href="{{ route('tupoksi.download', $tupoksi->id) }}" class="btn btn-primary shadow-sm">
                                    <i class="fa fa-download mr-1"></i> Unduh Dokumen ({{ strtoupper($ext) }})
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fa fa-file-o fa-3x mb-2 text-secondary"></i>
                            <p class="mb-0">Tidak ada berkas fisik dokumen yang diunggah untuk Tupoksi ini.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Uraian Tugas Pokok --}}
            <div class="main-card card shadow-sm">
                <div class="card-header bg-white font-weight-bold">
                    Uraian Tugas Pokok dan Fungsi / Keterangan
                </div>
                <div class="card-body">
                    @if(!empty($tupoksi->keterangan))
                        <div class="p-3 bg-light rounded border text-dark" style="white-space: pre-line; line-height: 1.6;">
                            {{ $tupoksi->keterangan }}
                        </div>
                    @else
                        <div class="text-muted font-italic">
                            Tidak ada uraian tugas tambahan atau keterangan khusus untuk Tupoksi ini.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
