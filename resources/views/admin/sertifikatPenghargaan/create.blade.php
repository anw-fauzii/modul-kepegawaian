@extends('layouts.appui')

@section('title')
    <title>Tambah Sertifikat Penghargaan - SIPIT</title>
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
                    <strong>Pencatatan Sertifikat Penghargaan Baru</strong>
                    <div class="page-title-subheading">
                        Input data prestasi atau penghargaan karyawan dan unggah berkas sertifikat resmi.
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
            Formulir Sertifikat Penghargaan
        </div>
        <div class="card-body">
            <form action="{{ route('sertifikat-penghargaan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-row">
                    {{-- Karyawan (NIPY) --}}
                    <div class="form-group col-md-8">
                        <label class="font-weight-bold">Karyawan Penerima Penghargaan <span class="text-danger">*</span></label>
                        <select name="karyawan_nipy" class="form-control select2 @error('karyawan_nipy') is-invalid @enderror" required>
                            <option value="">-- Pilih Karyawan --</option>
                            @foreach($karyawanList as $karyawan)
                                <option value="{{ $karyawan->nipy }}" {{ old('karyawan_nipy', request('karyawan_nipy')) == $karyawan->nipy ? 'selected' : '' }}>
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
                               value="{{ old('tahun', date('Y')) }}" min="1990" max="{{ date('Y') + 1 }}" required>
                        @error('tahun')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Judul Penghargaan --}}
                <div class="form-group">
                    <label class="font-weight-bold">Judul / Nama Penghargaan <span class="text-danger">*</span></label>
                    <input type="text" name="judul_penghargaan" class="form-control @error('judul_penghargaan') is-invalid @enderror" 
                           placeholder="Contoh: Juara 1 Guru Berprestasi Tingkat Kabupaten, Penghargaan Pengabdian 10 Tahun" 
                           value="{{ old('judul_penghargaan') }}" required>
                    <small class="form-text text-muted">Tuliskan nama atau prestasi penghargaan secara lengkap.</small>
                    @error('judul_penghargaan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Upload Berkas --}}
                <div class="form-group">
                    <label class="font-weight-bold">Unggah Berkas Sertifikat <span class="text-danger">*</span></label>
                    <div class="custom-file">
                        <input type="file" name="berkas" class="custom-file-input @error('berkas') is-invalid @enderror" 
                               id="berkasInput" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required>
                        <label class="custom-file-label" for="berkasInput">Pilih file berkas sertifikat (PDF, JPG, PNG, DOCX)...</label>
                    </div>
                    <small class="form-text text-muted">
                        Format yang didukung: <strong>PDF, JPG, PNG, DOC, DOCX</strong>. Ukuran maksimum: <strong>5 MB</strong>.
                    </small>
                    @error('berkas')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Keterangan --}}
                <div class="form-group">
                    <label class="font-weight-bold">Keterangan / Catatan Tambahan</label>
                    <textarea name="keterangan" rows="3" class="form-control @error('keterangan') is-invalid @enderror" 
                              placeholder="Keterangan tambahan seperti penyelenggara, instansi pemberi, atau nomor piagam (opsional)...">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <hr>

                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('sertifikat-penghargaan.index') }}" class="btn btn-secondary">
                        <i class="fa fa-times mr-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary shadow-sm font-weight-bold">
                        <i class="fa fa-save mr-1"></i> Simpan & Unggah Sertifikat
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
                const fileName = e.target.files[0] ? e.target.files[0].name : 'Pilih file berkas sertifikat...';
                const label = e.target.nextElementSibling;
                if (label) {
                    label.innerText = fileName;
                }
            });
        }
    });
</script>
@endsection
