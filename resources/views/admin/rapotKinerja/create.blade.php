@extends('layouts.appui')

@section('title')
    <title>Unggah Rapot Kinerja - SIPIT</title>
@endsection

@section('content')
<div class="app-main__inner">
    {{-- Page Header --}}
    <div class="app-page-title mb-3">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon bg-info text-white">
                    <i class="pe-7s-medal icon-gradient bg-sunny-morning"></i>
                </div>
                <div>
                    <strong>Unggah Rapot Kinerja Baru</strong>
                    <div class="page-title-subheading">
                        Simpan dan arsipkan berkas hasil evaluasi / rapot kinerja karyawan per tahun ajaran.
                    </div>
                </div>
            </div>
            <div class="page-title-actions">
                <a href="{{ route('rapot-kinerja.index') }}" class="btn btn-outline-secondary font-weight-bold">
                    <i class="fa fa-arrow-left mr-1"></i> Kembali ke Daftar
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
            <form action="{{ route('rapot-kinerja.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    {{-- Karyawan (NIPY) --}}
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold text-dark">
                            Karyawan Penerima Rapot <span class="text-danger">*</span>
                        </label>
                        <select name="karyawan_nipy" class="form-control select2 @error('karyawan_nipy') is-invalid @enderror" required>
                            <option value="">-- Pilih Karyawan --</option>
                            @foreach($karyawanList as $karyawan)
                                <option value="{{ $karyawan->nipy }}" {{ old('karyawan_nipy', request('karyawan_nipy')) == $karyawan->nipy ? 'selected' : '' }}>
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
                                <option value="{{ $ta->id }}" {{ (old('akademik_master_tahun_ajaran_id') == $ta->id || (empty(old('akademik_master_tahun_ajaran_id')) && $ta->is_active)) ? 'selected' : '' }}>
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
                            Unggah Berkas Rapot Kinerja <span class="text-danger">*</span>
                        </label>
                        <div class="custom-file">
                            <input type="file" name="berkas" class="custom-file-input @error('berkas') is-invalid @enderror" 
                                   id="berkasInput" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required>
                            <label class="custom-file-label" for="berkasInput">Pilih file berkas rapot (PDF, DOC, DOCX, JPG, PNG)...</label>
                        </div>
                        <small class="form-text text-muted">
                            Format yang didukung: <strong>PDF, JPG, PNG, DOC, DOCX</strong>. Ukuran maksimum: <strong>5 MB</strong>.
                        </small>
                        @error('berkas')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Keterangan --}}
                    <div class="col-md-12 mb-4">
                        <label class="font-weight-bold text-dark">
                            Keterangan / Catatan Evaluasi Kinerja
                        </label>
                        <textarea name="keterangan" rows="3" class="form-control @error('keterangan') is-invalid @enderror" 
                                  placeholder="Catatan hasil penilaian, pencapaian kinerja, rekomendasi pengembangan, atau catatan khusus...">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end border-top pt-3">
                    <a href="{{ route('rapot-kinerja.index') }}" class="btn btn-outline-secondary mr-2 px-4">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary px-4 font-weight-bold shadow-sm">
                        <i class="fa fa-save mr-1"></i> Simpan Rapot Kinerja
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
                const fileName = e.target.files[0] ? e.target.files[0].name : 'Pilih file berkas rapot...';
                const nextSibling = e.target.nextElementSibling;
                if (nextSibling) {
                    nextSibling.innerText = fileName;
                }
            });
        }
    });
</script>
@endsection
