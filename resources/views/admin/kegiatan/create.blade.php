@extends('layouts.appui')

@section('title')
    <title>Tambah Kegiatan Lembaga - SIPIT</title>
@endsection

@section('content')
<div class="app-main__inner">
    <div class="app-page-title mb-3">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon bg-primary text-white">
                    <i class="pe-7s-note2 icon-gradient bg-plum-plate"></i>
                </div>
                <div>
                    <strong>Tambah Kegiatan Lembaga Baru</strong>
                    <div class="page-title-subheading">
                        Buat agenda kegiatan lembaga dan sistem akan otomatis menghasilkan Token QR Code presensi unik.
                    </div>
                </div>
            </div>
            <div class="page-title-actions">
                <a href="{{ route('kegiatan.index') }}" class="btn btn-outline-secondary font-weight-bold">
                    <i class="fa fa-arrow-left mr-1"></i> Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong><i class="fa fa-exclamation-triangle mr-1"></i> Terjadi kesalahan input:</strong>
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

    <div class="main-card card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body p-4">
            <form action="{{ route('kegiatan.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-5 mb-3">
                        <label class="font-weight-bold text-dark">Nama Kegiatan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kegiatan" class="form-control @error('nama_kegiatan') is-invalid @enderror" placeholder="Contoh: Pelatihan Kurikulum / Rapat Kerja..." value="{{ old('nama_kegiatan') }}" required>
                        @error('nama_kegiatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="font-weight-bold text-dark">Jenis Kegiatan <span class="text-danger">*</span></label>
                        <select name="jenis_kegiatan" class="form-control @error('jenis_kegiatan') is-invalid @enderror" required>
                            <option value="pelatihan" {{ old('jenis_kegiatan') == 'pelatihan' ? 'selected' : '' }}>Pelatihan / Diklat</option>
                            <option value="rapat" {{ old('jenis_kegiatan', 'rapat') == 'rapat' ? 'selected' : '' }}>Rapat</option>
                            <option value="seminar" {{ old('jenis_kegiatan') == 'seminar' ? 'selected' : '' }}>Seminar / Workshop</option>
                            <option value="pembinaan" {{ old('jenis_kegiatan') == 'pembinaan' ? 'selected' : '' }}>Pembinaan</option>
                            <option value="lainnya" {{ old('jenis_kegiatan') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        <small class="text-muted">Kegiatan berjenis <strong>Pelatihan</strong> otomatis dapat dijadikan sumber Diklat Lembaga.</small>
                        @error('jenis_kegiatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="font-weight-bold text-dark">Tahun Ajaran <span class="text-danger">*</span></label>
                        <select name="akademik_master_tahun_ajaran_id" class="form-control @error('akademik_master_tahun_ajaran_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Tahun Ajaran --</option>
                            @foreach($tahunAjaranList as $ta)
                                <option value="{{ $ta->id }}" {{ old('akademik_master_tahun_ajaran_id') == $ta->id ? 'selected' : '' }}>
                                    {{ $ta->nama_tahun_ajaran }} (Semester {{ $ta->semester }})
                                </option>
                            @endforeach
                        </select>
                        @error('akademik_master_tahun_ajaran_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold text-dark">Pengisi / Narasumber / Pembina</label>
                        <input type="text" name="pengisi" class="form-control @error('pengisi') is-invalid @enderror" placeholder="Contoh: Ustadz Dr. Fulan, M.Pd / Ketua Yayasan" value="{{ old('pengisi') }}">
                        @error('pengisi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold text-dark">Lokasi / Tempat Pelaksanaan</label>
                        <input type="text" name="lokasi" class="form-control @error('lokasi') is-invalid @enderror" placeholder="Contoh: Aula Utama Kampus 1 / Masjid Insani" value="{{ old('lokasi') }}">
                        @error('lokasi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="font-weight-bold text-dark">Tanggal Kegiatan <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                        @error('tanggal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="font-weight-bold text-dark">Jam Mulai</label>
                        <input type="time" name="jam_mulai" class="form-control @error('jam_mulai') is-invalid @enderror" value="{{ old('jam_mulai', '08:00') }}">
                        @error('jam_mulai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="font-weight-bold text-dark">Jam Selesai</label>
                        <input type="time" name="jam_selesai" class="form-control @error('jam_selesai') is-invalid @enderror" value="{{ old('jam_selesai') }}">
                        @error('jam_selesai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="font-weight-bold text-dark">Deskripsi / Catatan Agenda</label>
                        <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="3" placeholder="Informasi tambahan mengenai kegiatan...">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12 mb-4">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="custom-control-label font-weight-bold text-dark" for="is_active">
                                Buka Gerbang Presensi Segera (Karyawan dapat langsung scan QR saat kegiatan berlangsung)
                            </label>
                        </div>
                    </div>
                </div>

                <div class="border-top pt-3 d-flex justify-content-end">
                    <a href="{{ route('kegiatan.index') }}" class="btn btn-secondary mr-2">Batal</a>
                    <button type="submit" class="btn btn-primary px-4 font-weight-bold">
                        <i class="fa fa-save mr-1"></i> Simpan & Buat QR Code
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
