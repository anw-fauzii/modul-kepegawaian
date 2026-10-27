@extends('layouts.appui')

@section('title')
    <title>Edit Rapot Kinerja - SIPIT</title>
@endsection

@section('content')
<div class="app-main__inner">
    {{-- Page Header --}}
    <div class="app-page-title mb-3">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon bg-warning text-white">
                    <i class="pe-7s-pen icon-gradient bg-warm-flame"></i>
                </div>
                <div>
                    <strong>Edit Rapot Kinerja #{{ $rapot->id }}</strong>
                    <div class="page-title-subheading">
                        Perbarui data evaluasi rapot kinerja karyawan atau ganti berkas dokumen.
                    </div>
                </div>
            </div>
            <div class="page-title-actions">
                <a href="{{ route('rapot-kinerja.index') }}" class="btn btn-outline-secondary font-weight-bold mr-1">
                    <i class="fa fa-arrow-left mr-1"></i> Kembali ke Daftar
                </a>
                <a href="{{ route('rapot-kinerja.show', $rapot->id) }}" class="btn btn-info font-weight-bold shadow-sm">
                    <i class="fa fa-eye mr-1"></i> Lihat Detail
                </a>
            </div>
        </div>
    </div>

    {{-- Error validation alerts --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong><i class="fa fa-exclamation-triangle mr-1"></i> Mohon periksa kembali form isian:</strong>
            <ul class="mb-0 mt-1 pl-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Form Card --}}
    <div class="main-card card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body p-4">
            <form action="{{ route('rapot-kinerja.update', $rapot->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    {{-- Karyawan (NIPY) --}}
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold text-dark">
                            Karyawan Penerima Rapot <span class="text-danger">*</span>
                        </label>
                        <select name="karyawan_nipy" class="form-control select2 @error('karyawan_nipy') is-invalid @enderror" required>
                            <option value="">-- Pilih Karyawan --</option>
                            @foreach($karyawanList as $karyawan)
                                <option value="{{ $karyawan->nipy }}" {{ old('karyawan_nipy', $rapot->karyawan_nipy) == $karyawan->nipy ? 'selected' : '' }}>
                                    {{ $karyawan->nipy }} - {{ $karyawan->nama_lengkap }}{{ $karyawan->gelar ? ', ' . $karyawan->gelar : '' }}
                                    @if($karyawan->unit)
                                        ({{ $karyawan->unit }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('karyawan_nipy')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tahun Ajaran --}}
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold text-dark">
                            Tahun Ajaran <span class="text-danger">*</span>
                        </label>
                        <select name="akademik_master_tahun_ajaran_id" class="form-control @error('akademik_master_tahun_ajaran_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Tahun Ajaran --</option>
                            @foreach($tahunAjaranList as $ta)
                                <option value="{{ $ta->id }}" {{ old('akademik_master_tahun_ajaran_id', $rapot->akademik_master_tahun_ajaran_id) == $ta->id ? 'selected' : '' }}>
                                    {{ $ta->nama_tahun_ajaran }} - Semester {{ $ta->semester }} {{ $ta->is_active ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('akademik_master_tahun_ajaran_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    {{-- Upload Berkas Rapot --}}
                    <div class="col-md-12 mb-3">
                        <label class="font-weight-bold text-dark">
                            Ganti Berkas Dokumen Rapot <span class="text-muted small font-weight-normal">(Kosongkan jika tidak ingin mengganti)</span>
                        </label>
                        <div class="custom-file">
                            <input type="file" name="berkas" class="custom-file-input @error('berkas') is-invalid @enderror" 
                                   id="berkasInput" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                            <label class="custom-file-label" for="berkasInput">Pilih file baru jika ingin mengganti...</label>
                        </div>
                        <small class="form-text text-muted">
                            Format yang didukung: <strong>PDF, JPG, PNG, DOC, DOCX</strong>. Ukuran maksimum: <strong>5 MB</strong>.
                        </small>
                        @error('berkas')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror

                        {{-- Preview File Saat Ini --}}
                        @if($rapot->berkas)
                            <div class="mt-2 p-2 bg-light border rounded d-flex align-items-center justify-content-between">
                                <span class="small text-truncate" style="max-width: 80%;">
                                    <i class="fa fa-file-pdf-o text-danger mr-1"></i> Berkas aktif saat ini: <code>{{ $rapot->berkas_filename }}</code>
                                </span>
                                <a href="{{ route('rapot-kinerja.download', $rapot->id) }}" class="btn btn-xs btn-outline-primary" target="_blank">
                                    <i class="fa fa-download"></i> Unduh Berkas
                                </a>
                            </div>
                        @endif
                    </div>

                    {{-- Keterangan --}}
                    <div class="col-md-12 mb-4">
                        <label class="font-weight-bold text-dark">
                            Keterangan / Catatan Evaluasi Kinerja
                        </label>
                        <textarea name="keterangan" rows="3" class="form-control @error('keterangan') is-invalid @enderror" 
                                  placeholder="Catatan hasil penilaian, pencapaian kinerja, rekomendasi pengembangan, atau catatan khusus...">{{ old('keterangan', $rapot->keterangan) }}</textarea>
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end border-top pt-3">
                    <a href="{{ route('rapot-kinerja.index') }}" class="btn btn-outline-secondary mr-2 px-4">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-warning text-white px-4 font-weight-bold shadow-sm">
                        <i class="fa fa-save mr-1"></i> Simpan Perubahan
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
                const fileName = e.target.files[0] ? e.target.files[0].name : 'Pilih file baru jika ingin mengganti...';
                const nextSibling = e.target.nextElementSibling;
                if (nextSibling) {
                    nextSibling.innerText = fileName;
                }
            });
        }
    });
</script>
@endsection
