@extends('layouts.appui')

@section('title')
    <title>Detail Surat Keputusan (SK) #{{ $suratKeputusan->id }} - SIPIT</title>
@endsection

@section('content')
<div class="app-main__inner">
    <div class="app-page-title mb-3">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon bg-info text-white">
                    <i class="pe-7s-id icon-gradient bg-malibu-beach"></i>
                </div>
                <div>
                    <strong>Detail Surat Keputusan (SK)</strong>
                    <div class="page-title-subheading">
                        Informasi lengkap penetapan SK dan pratinjau berkas dokumen
                    </div>
                </div>
            </div>
            <div class="page-title-actions">
                <a href="{{ route('surat-keputusan.index') }}" class="btn btn-secondary shadow-sm mr-1">
                    <i class="fa fa-arrow-left mr-1"></i> Kembali
                </a>
                <a href="{{ route('surat-keputusan.edit', $suratKeputusan->id) }}" class="btn btn-warning text-white shadow-sm mr-1">
                    <i class="fa fa-pencil mr-1"></i> Edit SK
                </a>
                @if($suratKeputusan->berkas)
                    <a href="{{ route('surat-keputusan.download', $suratKeputusan->id) }}" class="btn btn-success shadow-sm">
                        <i class="fa fa-download mr-1"></i> Unduh Dokumen
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Metadata SK --}}
        <div class="col-md-5 mb-3">
            <div class="main-card card shadow-sm mb-3">
                <div class="card-header bg-white font-weight-bold">
                    Informasi Surat Keputusan
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <tr>
                            <th style="width: 40%;">Nomor SK</th>
                            <td>
                                <strong>{{ $suratKeputusan->nomor_sk ?: 'Belum diisi' }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <th>Jabatan Ditetapkan</th>
                            <td>
                                <span class="badge badge-info font-weight-bold px-2 py-1">
                                    {{ $suratKeputusan->jabatan }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Tahun Ajaran</th>
                            <td>
                                @if($suratKeputusan->tahunAjaran)
                                    {{ $suratKeputusan->tahunAjaran->nama_tahun_ajaran }} (Semester {{ $suratKeputusan->tahunAjaran->semester }})
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Tanggal Terbit SK</th>
                            <td>
                                <i class="fa fa-calendar text-muted mr-1"></i>
                                {{ $suratKeputusan->formatted_tanggal_sk ?: '-' }}
                            </td>
                        </tr>
                        <tr>
                            <th>Nama Berkas</th>
                            <td>
                                @if($suratKeputusan->berkas)
                                    <code class="text-break">{{ $suratKeputusan->berkas_filename }}</code>
                                @else
                                    <span class="text-muted font-italic">Tidak ada berkas</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Dicatat Pada</th>
                            <td>{{ $suratKeputusan->created_at?->format('d M Y, H:i') }} WIB</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Informasi Karyawan --}}
            <div class="main-card card shadow-sm">
                <div class="card-header bg-white font-weight-bold">
                    Profil Karyawan Penerima SK
                </div>
                <div class="card-body">
                    @if($suratKeputusan->karyawan)
                        <h5 class="card-title text-primary mb-1">
                            {{ $suratKeputusan->karyawan->nama_lengkap }}{{ $suratKeputusan->karyawan->gelar ? ', ' . $suratKeputusan->karyawan->gelar : '' }}
                        </h5>
                        <p class="text-muted small mb-3">
                            NIPY: <code>{{ $suratKeputusan->karyawan->nipy }}</code>
                        </p>
                        <p class="mb-1"><strong>Unit Kerja:</strong> {{ $suratKeputusan->karyawan->unit ?? '-' }}</p>
                        <p class="mb-1"><strong>Jabatan Utama:</strong> {{ $suratKeputusan->karyawan->jabatan ?? '-' }}</p>
                        <p class="mb-1"><strong>Kategori:</strong> {{ $suratKeputusan->karyawan->kategori?->nama_kategori ?? '-' }}</p>
                        <p class="mb-0"><strong>WhatsApp:</strong> {{ $suratKeputusan->karyawan->whatsapp ?? '-' }}</p>
                    @else
                        <div class="text-danger font-italic">Data master karyawan untuk NIPY {{ $suratKeputusan->karyawan_nipy }} tidak ditemukan.</div>
                    @endif
                </div>
            </div>

            @if($suratKeputusan->keterangan)
                <div class="main-card card shadow-sm mt-3">
                    <div class="card-header bg-white font-weight-bold">
                        Catatan / Keterangan
                    </div>
                    <div class="card-body">
                        <p class="card-text">{{ $suratKeputusan->keterangan }}</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Preview Dokumen --}}
        <div class="col-md-7 mb-3">
            <div class="main-card card shadow-sm h-100">
                <div class="card-header bg-white font-weight-bold d-flex justify-content-between align-items-center">
                    <span>Pratinjau Dokumen Berkas</span>
                    @if($suratKeputusan->berkas_url)
                        <a href="{{ $suratKeputusan->berkas_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="fa fa-external-link mr-1"></i> Buka di Tab Baru
                        </a>
                    @endif
                </div>
                <div class="card-body p-2 d-flex flex-column align-items-center justify-content-center" style="min-height: 520px; background-color: #f8fafc;">
                    @if($suratKeputusan->berkas_url)
                        @php
                            $ext = strtolower(pathinfo($suratKeputusan->berkas, PATHINFO_EXTENSION));
                        @endphp

                        @if(in_array($ext, ['jpg', 'jpeg', 'png']))
                            <img src="{{ $suratKeputusan->berkas_url }}" alt="Dokumen SK" class="img-fluid rounded shadow-sm" style="max-height: 600px;">
                        @elseif($ext === 'pdf')
                            <iframe src="{{ $suratKeputusan->berkas_url }}" width="100%" height="600px" style="border: none; border-radius: 8px;">
                                Dokumen PDF tidak dapat dimuat di browser ini. Silakan unduh dokumen melalui tombol di atas.
                            </iframe>
                        @else
                            <div class="text-center p-4">
                                <i class="fa fa-file-word-o fa-4x text-primary mb-3"></i>
                                <h5 class="text-dark">Dokumen Berformat {{ strtoupper($ext) }}</h5>
                                <p class="text-muted small">Pratinjau langsung tidak didukung untuk tipe dokumen ini. Silakan unduh untuk melihat isinya.</p>
                                <a href="{{ route('surat-keputusan.download', $suratKeputusan->id) }}" class="btn btn-primary shadow-sm">
                                    <i class="fa fa-download mr-1"></i> Unduh File ({{ $suratKeputusan->berkas_filename }})
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center text-muted p-4">
                            <i class="fa fa-file-o fa-4x mb-3 text-secondary"></i>
                            <p>Tidak ada berkas fisik dokumen SK yang tersimpan.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
