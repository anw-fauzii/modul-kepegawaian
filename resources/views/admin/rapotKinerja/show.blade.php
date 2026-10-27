@extends('layouts.appui')

@section('title')
    <title>Detail Rapot Kinerja #{{ $rapot->id }} - SIPIT</title>
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
                    <strong>Detail Rapot Kinerja</strong>
                    <div class="page-title-subheading">
                        Informasi lengkap dan pratinjau dokumen berkas rapot kinerja karyawan
                    </div>
                </div>
            </div>
            <div class="page-title-actions">
                <a href="{{ route('rapot-kinerja.index') }}" class="btn btn-secondary shadow-sm mr-1">
                    <i class="fa fa-arrow-left mr-1"></i> Kembali
                </a>
                <a href="{{ route('rapot-kinerja.edit', $rapot->id) }}" class="btn btn-warning text-white shadow-sm mr-1">
                    <i class="fa fa-pencil mr-1"></i> Edit Rapot
                </a>
                @if($rapot->berkas)
                    <a href="{{ route('rapot-kinerja.download', $rapot->id) }}" class="btn btn-success shadow-sm">
                        <i class="fa fa-download mr-1"></i> Unduh Dokumen
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Metadata Rapot --}}
        <div class="col-md-5 mb-3">
            <div class="main-card card shadow-sm mb-3">
                <div class="card-header bg-white font-weight-bold">
                    Informasi Rapot Kinerja
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <tr>
                            <th style="width: 40%;">Tahun Ajaran</th>
                            <td>
                                @if($rapot->tahunAjaran)
                                    <span class="badge badge-primary">
                                        {{ $rapot->tahunAjaran->nama_tahun_ajaran }} (Semester {{ $rapot->tahunAjaran->semester }})
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Nama Berkas</th>
                            <td>
                                @if($rapot->berkas)
                                    <code class="text-break">{{ $rapot->berkas_filename }}</code>
                                @else
                                    <span class="text-muted font-italic">Tidak ada berkas</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Diunggah Pada</th>
                            <td>{{ $rapot->created_at?->format('d M Y, H:i') }} WIB</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Profil Karyawan --}}
            <div class="main-card card shadow-sm">
                <div class="card-header bg-white font-weight-bold">
                    Profil Karyawan Penerima Rapot
                </div>
                <div class="card-body">
                    @if($rapot->karyawan)
                        <h5 class="card-title text-primary mb-1">
                            {{ $rapot->karyawan->nama_lengkap }}{{ $rapot->karyawan->gelar ? ', ' . $rapot->karyawan->gelar : '' }}
                        </h5>
                        <p class="text-muted small mb-3">
                            NIPY: <code>{{ $rapot->karyawan->nipy }}</code>
                        </p>
                        <p class="mb-1"><strong>Unit Kerja:</strong> {{ $rapot->karyawan->unit ?? '-' }}</p>
                        <p class="mb-1"><strong>Jabatan:</strong> {{ $rapot->karyawan->jabatan ?? '-' }}</p>
                        <p class="mb-1"><strong>Kategori:</strong> {{ $rapot->karyawan->kategori?->nama_kategori ?? '-' }}</p>
                        <p class="mb-0"><strong>WhatsApp:</strong> {{ $rapot->karyawan->whatsapp ?? '-' }}</p>
                    @else
                        <div class="text-danger font-italic">Data master karyawan untuk NIPY {{ $rapot->karyawan_nipy }} tidak ditemukan.</div>
                    @endif
                </div>
            </div>

            @if($rapot->keterangan)
                <div class="main-card card shadow-sm mt-3">
                    <div class="card-header bg-white font-weight-bold">
                        Catatan / Keterangan Evaluasi
                    </div>
                    <div class="card-body">
                        <p class="card-text">{{ $rapot->keterangan }}</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Preview Dokumen --}}
        <div class="col-md-7 mb-3">
            <div class="main-card card shadow-sm h-100">
                <div class="card-header bg-white font-weight-bold d-flex justify-content-between align-items-center">
                    <span>Pratinjau Dokumen Rapot</span>
                    @if($rapot->berkas_url)
                        <a href="{{ $rapot->berkas_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="fa fa-external-link mr-1"></i> Buka di Tab Baru
                        </a>
                    @endif
                </div>
                <div class="card-body p-2 d-flex flex-column align-items-center justify-content-center" style="min-height: 520px; background-color: #f8fafc;">
                    @if($rapot->berkas_url)
                        @php
                            $ext = strtolower(pathinfo($rapot->berkas, PATHINFO_EXTENSION));
                        @endphp

                        @if(in_array($ext, ['jpg', 'jpeg', 'png']))
                            <img src="{{ $rapot->berkas_url }}" alt="Dokumen Rapot Kinerja" class="img-fluid rounded shadow-sm" style="max-height: 600px;">
                        @elseif($ext === 'pdf')
                            <iframe src="{{ $rapot->berkas_url }}" width="100%" height="600px" style="border: none; border-radius: 8px;">
                                Dokumen PDF tidak dapat dimuat di browser ini. Silakan unduh dokumen melalui tombol di atas.
                            </iframe>
                        @else
                            <div class="text-center p-4">
                                <i class="fa fa-file-word-o fa-4x text-primary mb-3"></i>
                                <h5 class="text-dark">Dokumen Berformat {{ strtoupper($ext) }}</h5>
                                <p class="text-muted small">Pratinjau langsung tidak didukung untuk tipe file ini. Silakan unduh dokumen untuk membukanya.</p>
                                <a href="{{ route('rapot-kinerja.download', $rapot->id) }}" class="btn btn-primary shadow-sm">
                                    <i class="fa fa-download mr-1"></i> Unduh File ({{ $rapot->berkas_filename }})
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center text-muted p-4">
                            <i class="fa fa-file-o fa-4x mb-3 text-secondary"></i>
                            <p>Tidak ada berkas fisik dokumen rapot yang tersimpan.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
