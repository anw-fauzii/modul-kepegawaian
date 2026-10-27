@extends('layouts.appui')

@section('title')
    <title>Detail Diklat #{{ $diklat->id }} - SIPIT</title>
@endsection

@section('content')
<div class="app-main__inner">
    <div class="app-page-title mb-3">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon bg-primary text-white">
                    <i class="pe-7s-study icon-gradient bg-plum-plate"></i>
                </div>
                <div>
                    <strong>Detail Data Diklat #{{ $diklat->id }}</strong>
                    <div class="page-title-subheading">
                        Informasi lengkap riwayat pendidikan dan pelatihan karyawan beserta berkas sertifikat
                    </div>
                </div>
            </div>
            <div class="page-title-actions">
                <a href="{{ route('diklat.index') }}" class="btn btn-secondary shadow-sm mr-1">
                    <i class="fa fa-arrow-left mr-1"></i> Kembali
                </a>
                <a href="{{ route('diklat.edit', $diklat->id) }}" class="btn btn-warning text-white shadow-sm mr-1">
                    <i class="fa fa-pencil mr-1"></i> Edit Diklat
                </a>
                @if($diklat->berkas)
                    <a href="{{ route('diklat.download', $diklat->id) }}" class="btn btn-success shadow-sm">
                        <i class="fa fa-download mr-1"></i> Unduh Sertifikat
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Metadata Diklat & Karyawan --}}
        <div class="col-md-5 mb-3">
            {{-- Info Diklat --}}
            <div class="main-card card shadow-sm mb-3">
                <div class="card-header bg-white font-weight-bold d-flex justify-content-between align-items-center">
                    <span>Informasi Pelatihan</span>
                    @if($diklat->kategori === 'lembaga')
                        <span class="badge badge-info"><i class="fa fa-building-o mr-1"></i> Lembaga</span>
                    @else
                        <span class="badge badge-success"><i class="fa fa-user mr-1"></i> Mandiri</span>
                    @endif
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <tr>
                            <th style="width: 40%;">Nama Kegiatan</th>
                            <td><strong class="text-dark">{{ $diklat->nama_kegiatan }}</strong></td>
                        </tr>
                        <tr>
                            <th>Penyelenggara</th>
                            <td>{{ $diklat->pelaksana }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Pelaksanaan</th>
                            <td>
                                {{ $diklat->tanggal?->format('d M Y') }}
                                @if($diklat->tanggal_selesai && $diklat->tanggal_selesai->ne($diklat->tanggal))
                                    s.d {{ $diklat->tanggal_selesai?->format('d M Y') }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Jumlah Jam (JP)</th>
                            <td>{{ $diklat->jumlah_jam ? $diklat->jumlah_jam . ' JP' : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Tempat / Lokasi</th>
                            <td>{{ $diklat->tempat ?: '-' }}</td>
                        </tr>
                        @if($diklat->kegiatanLembaga)
                            <tr>
                                <th>Agenda Lembaga</th>
                                <td>
                                    <span class="text-primary font-weight-bold">{{ $diklat->kegiatanLembaga->nama_kegiatan }}</span>
                                    <small class="d-block text-muted">
                                        Jenis: {{ ucfirst($diklat->kegiatanLembaga->jenis_kegiatan) }} &bull; {{ $diklat->kegiatanLembaga->tanggal?->format('d/m/Y') }}
                                    </small>
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <th>Dicatat Pada</th>
                            <td>{{ $diklat->created_at?->format('d M Y, H:i') }} WIB</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Profil Karyawan --}}
            <div class="main-card card shadow-sm">
                <div class="card-header bg-white font-weight-bold">
                    Profil Karyawan Peserta
                </div>
                <div class="card-body">
                    @if($diklat->karyawan)
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-3" style="width: 50px; height: 50px; font-size: 20px; font-weight: bold;">
                                {{ strtoupper(substr($diklat->karyawan->nama_lengkap, 0, 1)) }}
                            </div>
                            <div>
                                <h5 class="mb-0 font-weight-bold text-dark">
                                    {{ $diklat->karyawan->nama_lengkap }}{{ $diklat->karyawan->gelar ? ', ' . $diklat->karyawan->gelar : '' }}
                                </h5>
                                <div class="text-muted small">
                                    NIPY: <code>{{ $diklat->karyawan->nipy }}</code>
                                </div>
                            </div>
                        </div>

                        <ul class="list-group list-group-flush small">
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Unit Kerja</span>
                                <span class="font-weight-bold">{{ $diklat->karyawan->unit ?? '-' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Jabatan</span>
                                <span class="font-weight-bold">{{ $diklat->karyawan->jabatan ?? '-' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Kategori Pegawai</span>
                                <span class="font-weight-bold">{{ $diklat->karyawan->kategori->nama_kategori ?? '-' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Kontak WhatsApp</span>
                                <span>{{ $diklat->karyawan->whatsapp ?? '-' }}</span>
                            </li>
                        </ul>
                    @else
                        <div class="alert alert-warning mb-0">
                            <i class="fa fa-exclamation-triangle mr-1"></i> Data master karyawan NIPY <strong>{{ $diklat->karyawan_nipy }}</strong> telah dihapus.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Materi & Pratinjau Sertifikat --}}
        <div class="col-md-7 mb-3">
            {{-- Materi Pelatihan --}}
            <div class="main-card card shadow-sm mb-3">
                <div class="card-header bg-white font-weight-bold">
                    Materi / Silabus Pelatihan
                </div>
                <div class="card-body">
                    <div class="p-3 bg-light rounded border text-dark" style="white-space: pre-line; line-height: 1.6;">
                        {{ $diklat->materi }}
                    </div>

                    @if(!empty($diklat->keterangan))
                        <div class="mt-3">
                            <label class="font-weight-bold text-muted small text-uppercase">Catatan / Keterangan Tambahan</label>
                            <div class="p-2 bg-white rounded border text-secondary small" style="white-space: pre-line;">
                                {{ $diklat->keterangan }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Dokumen Berkas Sertifikat --}}
            <div class="main-card card shadow-sm">
                <div class="card-header bg-white font-weight-bold d-flex justify-content-between align-items-center">
                    <span>Berkas Sertifikat / Bukti Diklat</span>
                    @if($diklat->berkas)
                        <a href="{{ route('diklat.download', $diklat->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fa fa-download mr-1"></i> Unduh Sertifikat
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    @if($diklat->berkas)
                        @php
                            $ext = strtolower(pathinfo($diklat->berkas, PATHINFO_EXTENSION));
                        @endphp

                        @if(in_array($ext, ['jpg', 'jpeg', 'png']))
                            <div class="text-center p-2 bg-light rounded border">
                                <img src="{{ $diklat->berkas_url }}" alt="Sertifikat Diklat" class="img-fluid rounded shadow-sm" style="max-height: 500px;">
                            </div>
                        @elseif($ext === 'pdf')
                            <div class="embed-responsive embed-responsive-4by3 rounded border shadow-sm" style="min-height: 480px;">
                                <iframe class="embed-responsive-item" src="{{ $diklat->berkas_url }}" allowfullscreen></iframe>
                            </div>
                        @else
                            <div class="text-center py-5 bg-light rounded border">
                                <i class="fa fa-certificate fa-4x text-primary mb-3"></i>
                                <h6 class="font-weight-bold">{{ $diklat->berkas_filename }}</h6>
                                <p class="text-muted small mb-3">Format file sertifikat: .{{ strtoupper($ext) }}. Silakan klik tombol di bawah untuk mengunduh berkas.</p>
                                <a href="{{ route('diklat.download', $diklat->id) }}" class="btn btn-primary shadow-sm">
                                    <i class="fa fa-download mr-1"></i> Unduh Dokumen ({{ strtoupper($ext) }})
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fa fa-file-o fa-3x mb-2 text-secondary"></i>
                            <p class="mb-0">Tidak ada berkas fisik sertifikat yang diunggah untuk data Diklat ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
