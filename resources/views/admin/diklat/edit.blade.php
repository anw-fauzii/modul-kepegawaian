@extends('layouts.appui')

@section('title')
    <title>Edit Data Diklat #{{ $diklat->id }} - SIPIT</title>
@endsection

@section('content')
<div class="app-main__inner">
    {{-- Page Header --}}
    <div class="app-page-title mb-3">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon bg-warning text-white">
                    <i class="pe-7s-note2 icon-gradient bg-warm-flame"></i>
                </div>
                <div>
                    <strong>Edit Data Diklat #{{ $diklat->id }}</strong>
                    <div class="page-title-subheading">
                        Perbarui rincian riwayat keikutsertaan diklat / pelatihan karyawan atau ganti sertifikat.
                    </div>
                </div>
            </div>
            <div class="page-title-actions">
                <a href="{{ route('diklat.index') }}" class="btn btn-outline-secondary font-weight-bold mr-1">
                    <i class="fa fa-arrow-left mr-1"></i> Kembali ke Daftar
                </a>
                <a href="{{ route('diklat.show', $diklat->id) }}" class="btn btn-info font-weight-bold shadow-sm">
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
            <form action="{{ route('diklat.update', $diklat->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Karyawan & Kategori --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold text-dark">
                            Karyawan Peserta Diklat <span class="text-danger">*</span>
                        </label>
                        <select name="karyawan_nipy" class="form-control select2 @error('karyawan_nipy') is-invalid @enderror" required>
                            <option value="">-- Pilih Karyawan --</option>
                            @foreach($karyawanList as $karyawan)
                                <option value="{{ $karyawan->nipy }}" {{ old('karyawan_nipy', $diklat->karyawan_nipy) == $karyawan->nipy ? 'selected' : '' }}>
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

                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold text-dark">
                            Kategori Diklat <span class="text-danger">*</span>
                        </label>
                        <select name="kategori" id="kategoriSelect" class="form-control @error('kategori') is-invalid @enderror" required>
                            <option value="lembaga" {{ old('kategori', $diklat->kategori) === 'lembaga' ? 'selected' : '' }}>
                                Lembaga (Diselenggarakan oleh Yayasan / Lembaga)
                            </option>
                            <option value="mandiri" {{ old('kategori', $diklat->kategori) === 'mandiri' ? 'selected' : '' }}>
                                Mandiri (Inisiatif Karyawan / Lembaga Luar)
                            </option>
                        </select>
                        @error('kategori')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Khusus Kategori Lembaga: Hubungkan ke Kegiatan Lembaga (Hanya Pelatihan) --}}
                <div id="kegiatanLembagaWrapper" class="p-3 mb-3 bg-light rounded border">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="font-weight-bold text-primary mb-0">
                            <i class="fa fa-link mr-1"></i> Hubungkan ke Agenda Kegiatan Lembaga
                        </label>
                        <span class="badge badge-info">Khusus Jenis Pelatihan</span>
                    </div>
                    <select name="kepegawaian_kegiatan_lembaga_id" id="kegiatanLembagaSelect" class="form-control select2">
                        <option value="">-- Pilih Agenda Kegiatan Pelatihan Lembaga (Opsional) --</option>
                        @foreach($kegiatanPelatihanList as $keg)
                            <option value="{{ $keg->id }}" 
                                    data-nama="{{ $keg->nama_kegiatan }}"
                                    data-pengisi="{{ $keg->pengisi }}"
                                    data-tanggal="{{ $keg->tanggal->format('Y-m-d') }}"
                                    data-lokasi="{{ $keg->lokasi }}"
                                    data-materi="{{ $keg->deskripsi }}"
                                    {{ old('kepegawaian_kegiatan_lembaga_id', $diklat->kepegawaian_kegiatan_lembaga_id) == $keg->id ? 'selected' : '' }}>
                                {{ $keg->nama_kegiatan }} &bull; {{ $keg->tanggal->format('d/m/Y') }} &bull; Pengisi: {{ $keg->pengisi ?: '-' }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted mt-1">
                        <i class="fa fa-info-circle mr-1"></i> Agenda yang tercantum khusus berjenis <strong>Pelatihan / Diklat</strong>. Agenda rapat tidak disertakan ke diklat.
                    </small>
                </div>

                {{-- Detail Pelatihan --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold text-dark">
                            Nama Kegiatan Diklat / Pelatihan <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nama_kegiatan" id="namaKegiatanInput" class="form-control @error('nama_kegiatan') is-invalid @enderror" 
                               value="{{ old('nama_kegiatan', $diklat->nama_kegiatan) }}" placeholder="Contoh: Workshop Implementasi Kurikulum Merdeka..." required>
                        @error('nama_kegiatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold text-dark">
                            Penyelenggara / Pelaksana <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="pelaksana" id="pelaksanaInput" class="form-control @error('pelaksana') is-invalid @enderror" 
                               value="{{ old('pelaksana', $diklat->pelaksana) }}" placeholder="Contoh: Prima Insani / Kemendikbudristek..." required>
                        @error('pelaksana')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="font-weight-bold text-dark">
                            Tanggal Mulai <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="tanggal" id="tanggalInput" class="form-control @error('tanggal') is-invalid @enderror" 
                               value="{{ old('tanggal', $diklat->tanggal?->format('Y-m-d')) }}" required>
                        @error('tanggal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="font-weight-bold text-dark">
                            Tanggal Selesai <span class="text-muted small font-weight-normal">(Opsional)</span>
                        </label>
                        <input type="date" name="tanggal_selesai" class="form-control @error('tanggal_selesai') is-invalid @enderror" 
                               value="{{ old('tanggal_selesai', $diklat->tanggal_selesai?->format('Y-m-d')) }}">
                        @error('tanggal_selesai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="font-weight-bold text-dark">
                            Jumlah Jam (JP) <span class="text-muted small font-weight-normal">(Opsional)</span>
                        </label>
                        <input type="number" name="jumlah_jam" min="1" max="999" class="form-control @error('jumlah_jam') is-invalid @enderror" 
                               value="{{ old('jumlah_jam', $diklat->jumlah_jam) }}" placeholder="Contoh: 32">
                        @error('jumlah_jam')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold text-dark">
                            Tempat / Lokasi Pelaksanaan
                        </label>
                        <input type="text" name="tempat" id="tempatInput" class="form-control @error('tempat') is-invalid @enderror" 
                               value="{{ old('tempat', $diklat->tempat) }}" placeholder="Contoh: Aula Kampus 1 / Zoom Meeting...">
                        @error('tempat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold text-dark">
                            Ganti Berkas Sertifikat <span class="text-muted small font-weight-normal">(Kosongkan jika tidak ingin mengganti)</span>
                        </label>
                        <div class="custom-file">
                            <input type="file" name="berkas" class="custom-file-input @error('berkas') is-invalid @enderror" 
                                   id="berkasInput" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                            <label class="custom-file-label" for="berkasInput">Pilih file baru jika ingin mengganti...</label>
                        </div>
                        <small class="form-text text-muted">Maksimal: <strong>10 MB</strong>.</small>
                        @error('berkas')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror

                        @if($diklat->berkas)
                            <div class="mt-2 p-2 bg-light border rounded d-flex align-items-center justify-content-between">
                                <span class="small text-truncate" style="max-width: 80%;">
                                    <i class="fa fa-certificate text-primary mr-1"></i> File aktif saat ini: <code>{{ $diklat->berkas_filename }}</code>
                                </span>
                                <a href="{{ route('diklat.download', $diklat->id) }}" class="btn btn-xs btn-outline-primary" target="_blank">
                                    <i class="fa fa-download"></i> Unduh Sertifikat
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Materi Pelatihan --}}
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="font-weight-bold text-dark">
                            Materi / Silabus Pelatihan <span class="text-danger">*</span>
                        </label>
                        <textarea name="materi" id="materiTextarea" rows="3" class="form-control @error('materi') is-invalid @enderror" 
                                  placeholder="Rincian materi, topik pembahasan, atau capaian kompetensi diklat..." required>{{ old('materi', $diklat->materi) }}</textarea>
                        @error('materi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Keterangan --}}
                    <div class="col-md-12 mb-4">
                        <label class="font-weight-bold text-dark">
                            Keterangan / Catatan Tambahan
                        </label>
                        <textarea name="keterangan" rows="2" class="form-control @error('keterangan') is-invalid @enderror" 
                                  placeholder="Catatan hasil evaluasi, tindak lanjut, atau informasi lainnya...">{{ old('keterangan', $diklat->keterangan) }}</textarea>
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end border-top pt-3">
                    <a href="{{ route('diklat.index') }}" class="btn btn-outline-secondary mr-2 px-4">
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
        const kategoriSelect = document.getElementById('kategoriSelect');
        const wrapper = document.getElementById('kegiatanLembagaWrapper');
        const kegiatanSelect = document.getElementById('kegiatanLembagaSelect');
        const namaInput = document.getElementById('namaKegiatanInput');
        const pelaksanaInput = document.getElementById('pelaksanaInput');
        const tanggalInput = document.getElementById('tanggalInput');
        const tempatInput = document.getElementById('tempatInput');
        const materiTextarea = document.getElementById('materiTextarea');

        function toggleLembagaWrapper() {
            if (kategoriSelect.value === 'lembaga') {
                wrapper.style.display = 'block';
            } else {
                wrapper.style.display = 'none';
            }
        }

        if (kategoriSelect) {
            kategoriSelect.addEventListener('change', toggleLembagaWrapper);
            toggleLembagaWrapper();
        }

        if (kegiatanSelect) {
            kegiatanSelect.addEventListener('change', function () {
                const selected = this.options[this.selectedIndex];
                if (selected && selected.value) {
                    if (selected.dataset.nama) namaInput.value = selected.dataset.nama;
                    if (selected.dataset.pengisi) pelaksanaInput.value = selected.dataset.pengisi;
                    if (selected.dataset.tanggal) tanggalInput.value = selected.dataset.tanggal;
                    if (selected.dataset.lokasi) tempatInput.value = selected.dataset.lokasi;
                    if (selected.dataset.materi) materiTextarea.value = selected.dataset.materi;
                }
            });
        }

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
