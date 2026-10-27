@extends('layouts.appui')

@section('title')
    <title>Edit Sertifikat Penghargaan - SIPIT</title>
@endsection

@section('content')
<div class="app-main__inner">
    <div class="app-page-title mb-3">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon bg-warning text-white shadow-sm" style="border-radius: 12px;">
                    <i class="pe-7s-pen icon-gradient bg-warm-flame"></i>
                </div>
                <div>
                    <strong>Edit Sertifikat Penghargaan #{{ $penghargaan->id }}</strong>
                    <div class="page-title-subheading">
                        Perbarui data penghargaan atau ganti berkas dokumen sertifikat.
                    </div>
                </div>
            </div>
            <div class="page-title-actions">
                <a href="{{ route('sertifikat-penghargaan.index') }}" class="btn btn-secondary shadow-sm">
                    <i class="fa fa-arrow-left mr-1"></i> Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3">
            <strong><i class="fa fa-exclamation-triangle mr-1"></i> Terdapat kesalahan input:</strong>
            <ul class="mb-0 mt-2 pl-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <div class="main-card mb-3 card shadow-sm">
        <div class="card-header bg-white font-weight-bold">
            Formulir Pembaruan Sertifikat Penghargaan
        </div>
        <div class="card-body">
            <form action="{{ route('sertifikat-penghargaan.update', $penghargaan->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-row">
                    {{-- Karyawan (NIPY) --}}
                    <div class="form-group col-md-8">
                        <label class="font-weight-bold">Karyawan Penerima Penghargaan <span class="text-danger">*</span></label>
                        <select name="karyawan_nipy" class="form-control select2 @error('karyawan_nipy') is-invalid @enderror" required>
                            <option value="">-- Pilih Karyawan --</option>
                            @foreach($karyawanList as $karyawan)
                                <option value="{{ $karyawan->nipy }}" {{ old('karyawan_nipy', $penghargaan->karyawan_nipy) == $karyawan->nipy ? 'selected' : '' }}>
                                    {{ $karyawan->nama_lengkap }}{{ $karyawan->gelar ? ', ' . $karyawan->gelar : '' }} (NIPY: {{ $karyawan->nipy }}) - {{ $karyawan->unit ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                        @error('karyawan_nipy')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tahun --}}
                    <div class="form-group col-md-4">
                        <label class="font-weight-bold">Tahun Penghargaan <span class="text-danger">*</span></label>
                        <input type="number" name="tahun" class="form-control @error('tahun') is-invalid @enderror" 
                               value="{{ old('tahun', $penghargaan->tahun) }}" min="1990" max="{{ date('Y') + 1 }}" required>
                        @error('tahun')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Judul Penghargaan --}}
                <div class="form-group">
                    <label class="font-weight-bold">Judul / Nama Penghargaan <span class="text-danger">*</span></label>
                    <input type="text" name="judul_penghargaan" class="form-control @error('judul_penghargaan') is-invalid @enderror" 
                           value="{{ old('judul_penghargaan', $penghargaan->judul_penghargaan) }}" required>
                    @error('judul_penghargaan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Berkas Saat Ini & Ganti Berkas --}}
                <div class="form-group bg-light p-3 rounded border">
                    <label class="font-weight-bold d-block mb-1">Berkas Sertifikat Saat Ini</label>
                    @if($penghargaan->berkas)
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge badge-success mr-2"><i class="fa fa-check"></i> File Terlampir</span>
                            <span class="text-dark small font-weight-bold mr-3">{{ $penghargaan->berkas_filename }}</span>
                            <a href="{{ route('sertifikat-penghargaan.download', $penghargaan->id) }}" class="btn btn-sm btn-outline-primary mr-2">
                                <i class="fa fa-download mr-1"></i> Unduh File
                            </a>
                            @if($penghargaan->berkas_url)
                                <a href="{{ $penghargaan->berkas_url }}" target="_blank" class="btn btn-sm btn-outline-info">
                                    <i class="fa fa-external-link mr-1"></i> Lihat
                                </a>
                            @endif
                        </div>
                    @else
                        <div class="text-muted small mb-2 font-italic">Belum ada berkas fisik terlampir.</div>
                    @endif

                    <label class="small text-muted font-weight-bold mt-2">Unggah Berkas Baru (Kosongkan jika tidak ingin mengganti):</label>
                    <div class="custom-file">
                        <input type="file" name="berkas" class="custom-file-input @error('berkas') is-invalid @enderror" 
                               id="berkasInput" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                        <label class="custom-file-label" for="berkasInput">Pilih file baru...</label>
                    </div>
                    <small class="form-text text-muted">Maksimal 5 MB (Format: PDF, DOC, DOCX, JPG, PNG).</small>
                    @error('berkas')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Keterangan --}}
                <div class="form-group">
                    <label class="font-weight-bold">Keterangan / Catatan Tambahan</label>
                    <textarea name="keterangan" rows="3" class="form-control @error('keterangan') is-invalid @enderror">{{ old('keterangan', $penghargaan->keterangan) }}</textarea>
                    @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <hr>

                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('sertifikat-penghargaan.index') }}" class="btn btn-secondary">
                        <i class="fa fa-times mr-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-warning text-white shadow-sm font-weight-bold">
                        <i class="fa fa-save mr-1"></i> Perbarui Sertifikat Penghargaan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fileInput = document.getElementById('berkasInput');
        if (fileInput) {
            fileInput.addEventListener('change', function (e) {
                const fileName = e.target.files[0] ? e.target.files[0].name : 'Pilih file baru...';
                const label = e.target.nextElementSibling;
                if (label) {
                    label.innerText = fileName;
                }
            });
        }
    });
</script>
@endsection
