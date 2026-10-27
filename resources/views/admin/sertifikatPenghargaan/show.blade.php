@extends('layouts.appui')

@section('title')
    <title>Detail Sertifikat Penghargaan #{{ $penghargaan->id }} - SIPIT</title>
@endsection

@section('content')
<div class="app-main__inner">
    <div class="app-page-title mb-3">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon bg-warning text-white shadow-sm" style="border-radius: 12px;">
                    <i class="pe-7s-medal icon-gradient bg-warm-flame"></i>
                </div>
                <div>
                    <strong>Detail Sertifikat Penghargaan</strong>
                    <div class="page-title-subheading">
                        Informasi lengkap dan pratinjau dokumen berkas sertifikat penghargaan karyawan.
                    </div>
                </div>
            </div>
            <div class="page-title-actions">
                <a href="{{ route('sertifikat-penghargaan.index') }}" class="btn btn-secondary shadow-sm mr-1">
                    <i class="fa fa-arrow-left mr-1"></i> Kembali
                </a>
                <a href="{{ route('sertifikat-penghargaan.edit', $penghargaan->id) }}" class="btn btn-warning text-white shadow-sm mr-1">
                    <i class="fa fa-pencil mr-1"></i> Edit Penghargaan
                </a>
                @if($penghargaan->berkas)
                    <a href="{{ route('sertifikat-penghargaan.download', $penghargaan->id) }}" class="btn btn-success shadow-sm">
                        <i class="fa fa-download mr-1"></i> Unduh Berkas
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Metadata Penghargaan --}}
        <div class="col-md-5 mb-3">
            <div class="main-card card shadow-sm mb-3">
                <div class="card-header bg-white font-weight-bold">
                    Informasi Penghargaan
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <tr>
                            <th style="width: 40%;">Judul Penghargaan</th>
                            <td>
                                <strong>{{ $penghargaan->judul_penghargaan }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <th>Tahun Diterima</th>
                            <td>
                                <span class="badge badge-primary font-weight-bold px-2 py-1">
                                    {{ $penghargaan->tahun }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Nama Berkas</th>
                            <td>
                                @if($penghargaan->berkas)
                                    <code class="text-break">{{ $penghargaan->berkas_filename }}</code>
                                @else
                                    <span class="text-muted font-italic">Tidak ada berkas</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Dicatat Pada</th>
                            <td>{{ $penghargaan->created_at?->format('d M Y, H:i') }} WIB</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Profil Karyawan --}}
            <div class="main-card card shadow-sm">
                <div class="card-header bg-white font-weight-bold">
                    Profil Karyawan Penerima
                </div>
                <div class="card-body">
                    @if($penghargaan->karyawan)
                        <h5 class="card-title text-primary mb-1">
                            {{ $penghargaan->karyawan->nama_lengkap }}{{ $penghargaan->karyawan->gelar ? ', ' . $penghargaan->karyawan->gelar : '' }}
                        </h5>
                        <p class="text-muted small mb-3">
                            NIPY: <code>{{ $penghargaan->karyawan->nipy }}</code>
                        </p>
                        <p class="mb-1"><strong>Unit Kerja:</strong> {{ $penghargaan->karyawan->unit ?? '-' }}</p>
                        <p class="mb-1"><strong>Jabatan:</strong> {{ $penghargaan->karyawan->jabatan ?? '-' }}</p>
                        <p class="mb-1"><strong>Kategori:</strong> {{ $penghargaan->karyawan->kategori?->nama_kategori ?? '-' }}</p>
                        <p class="mb-0"><strong>WhatsApp:</strong> {{ $penghargaan->karyawan->whatsapp ?? '-' }}</p>
                    @else
                        <div class="text-danger font-italic">Data master karyawan untuk NIPY {{ $penghargaan->karyawan_nipy }} tidak ditemukan.</div>
                    @endif
                </div>
            </div>

            @if($penghargaan->keterangan)
                <div class="main-card card shadow-sm mt-3">
                    <div class="card-header bg-white font-weight-bold">
                        Catatan / Keterangan
                    </div>
                    <div class="card-body">
                        <p class="card-text">{{ $penghargaan->keterangan }}</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Preview Dokumen --}}
        <div class="col-md-7 mb-3">
            <div class="main-card card shadow-sm h-100">
                <div class="card-header bg-white font-weight-bold d-flex justify-content-between align-items-center">
                    <span>Pratinjau Dokumen Sertifikat</span>
                    @if($penghargaan->berkas_url)
                        <a href="{{ $penghargaan->berkas_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="fa fa-external-link mr-1"></i> Buka di Tab Baru
                        </a>
                    @endif
                </div>
                <div class="card-body p-2 d-flex flex-column align-items-center justify-content-center" style="min-height: 520px; background-color: #f8fafc;">
                    @if($penghargaan->berkas_url)
                        @php
                            $ext = strtolower(pathinfo($penghargaan->berkas, PATHINFO_EXTENSION));
                        @endphp

                        @if(in_array($ext, ['jpg', 'jpeg', 'png']))
                            <img src="{{ $penghargaan->berkas_url }}" alt="Dokumen Sertifikat" class="img-fluid rounded shadow-sm" style="max-height: 600px;">
                        @elseif($ext === 'pdf')
                            <iframe src="{{ $penghargaan->berkas_url }}" width="100%" height="600px" style="border: none; border-radius: 8px;">
                                Dokumen PDF tidak dapat dimuat langsung di browser ini. Silakan unduh dokumen melalui tombol di atas.
                            </iframe>
                        @else
                            <div class="text-center p-4">
                                <i class="fa fa-file-word-o fa-4x text-primary mb-3"></i>
                                <h5 class="text-dark">Dokumen Berformat {{ strtoupper($ext) }}</h5>
                                <p class="text-muted small">Pratinjau langsung tidak didukung untuk tipe dokumen ini. Silakan unduh untuk melihat isinya.</p>
                                <a href="{{ route('sertifikat-penghargaan.download', $penghargaan->id) }}" class="btn btn-primary shadow-sm">
                                    <i class="fa fa-download mr-1"></i> Unduh File ({{ $penghargaan->berkas_filename }})
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center text-muted p-4">
                            <i class="fa fa-certificate fa-4x mb-3 text-secondary"></i>
                            <p>Tidak ada berkas fisik dokumen sertifikat yang tersimpan.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
