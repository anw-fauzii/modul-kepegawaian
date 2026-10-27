@extends('layouts.appui')

@section('title')
    <title>Edit Fasilitas Karyawan - SIPIT</title>
@endsection

@section('content')
<div class="app-main__inner">
    {{-- Page Header --}}
    <div class="app-page-title mb-3">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon bg-warning text-white">
                    <i class="pe-7s-note icon-gradient bg-warm-flame"></i>
                </div>
                <div>
                    <strong>Edit Data Fasilitas Karyawan</strong>
                    <div class="page-title-subheading">
                        Perbarui rincian inventaris, jumlah unit, maupun status pengembalian fasilitas.
                    </div>
                </div>
            </div>
            <div class="page-title-actions">
                <a href="{{ route('fasilitas.index') }}" class="btn btn-outline-secondary font-weight-bold">
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
            <form action="{{ route('fasilitas.update', $fasilitas->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    {{-- Karyawan (NIPY) --}}
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold text-dark">
                            Karyawan Penerima <span class="text-danger">*</span>
                        </label>
                        <select name="karyawan_nipy" class="form-control select2 @error('karyawan_nipy') is-invalid @enderror" required>
                            <option value="">-- Pilih Karyawan --</option>
                            @foreach($karyawanList as $karyawan)
                                <option value="{{ $karyawan->nipy }}" {{ old('karyawan_nipy', $fasilitas->karyawan_nipy) == $karyawan->nipy ? 'selected' : '' }}>
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

                    {{-- Nama Fasilitas --}}
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold text-dark">
                            Nama Fasilitas / Barang <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nama_fasilitas" class="form-control @error('nama_fasilitas') is-invalid @enderror" value="{{ old('nama_fasilitas', $fasilitas->nama_fasilitas) }}" required>
                        @error('nama_fasilitas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    {{-- Jumlah --}}
                    <div class="col-md-3 mb-3">
                        <label class="font-weight-bold text-dark">
                            Jumlah Unit <span class="text-danger">*</span>
                        </label>
                        <input type="number" name="jumlah" class="form-control @error('jumlah') is-invalid @enderror" value="{{ old('jumlah', $fasilitas->jumlah) }}" min="1" max="1000" required>
                        @error('jumlah')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tahun Terima --}}
                    <div class="col-md-3 mb-3">
                        <label class="font-weight-bold text-dark">
                            Tahun Terima <span class="text-danger">*</span>
                        </label>
                        <input type="number" name="tahun_terima" class="form-control @error('tahun_terima') is-invalid @enderror" value="{{ old('tahun_terima', $fasilitas->tahun_terima) }}" min="1990" max="{{ date('Y') }}" required>
                        @error('tahun_terima')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Status Pengembalian --}}
                    <div class="col-md-3 mb-3">
                        <label class="font-weight-bold text-dark d-block">
                            Status Pengembalian
                        </label>
                        <div class="custom-control custom-switch mt-2">
                            @php
                                $isChecked = old('status', $fasilitas->status) ? true : false;
                            @endphp
                            <input type="checkbox" name="status" class="custom-control-input" id="statusSwitch" value="1" {{ $isChecked ? 'checked' : '' }}>
                            <label class="custom-control-label font-weight-bold {{ $isChecked ? 'text-success' : '' }}" for="statusSwitch" id="statusLabel">
                                {{ $isChecked ? 'Sudah Dikembalikan' : 'Belum Dikembalikan' }}
                            </label>
                        </div>
                    </div>

                    {{-- Tanggal Kembali --}}
                    <div class="col-md-3 mb-3" id="wrapperTanggalKembali" style="{{ $isChecked ? '' : 'display: none;' }}">
                        <label class="font-weight-bold text-dark">
                            Tanggal Pengembalian
                        </label>
                        <input type="date" name="tanggal_kembali" id="tanggal_kembali" class="form-control @error('tanggal_kembali') is-invalid @enderror" value="{{ old('tanggal_kembali', $fasilitas->tanggal_kembali?->format('Y-m-d') ?? date('Y-m-d')) }}">
                        @error('tanggal_kembali')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    {{-- Catatan --}}
                    <div class="col-md-12 mb-4">
                        <label class="font-weight-bold text-dark">Catatan / Keterangan Kondisi</label>
                        <textarea name="catatan" class="form-control @error('catatan') is-invalid @enderror" rows="3" placeholder="Informasi kondisi fisik, nomor seri, kelengkapan aksesoris...">{{ old('catatan', $fasilitas->catatan) }}</textarea>
                        @error('catatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end border-top pt-3">
                    <a href="{{ route('fasilitas.index') }}" class="btn btn-outline-secondary mr-2 px-4">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-warning text-dark px-4 font-weight-bold shadow-sm">
                        <i class="fa fa-save mr-1"></i> Perbarui Fasilitas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const statusSwitch = document.getElementById('statusSwitch');
        const statusLabel = document.getElementById('statusLabel');
        const wrapperTanggalKembali = document.getElementById('wrapperTanggalKembali');

        if (statusSwitch) {
            statusSwitch.addEventListener('change', function () {
                if (this.checked) {
                    statusLabel.textContent = 'Sudah Dikembalikan';
                    statusLabel.classList.add('text-success');
                    wrapperTanggalKembali.style.display = 'block';
                } else {
                    statusLabel.textContent = 'Belum Dikembalikan';
                    statusLabel.classList.remove('text-success');
                    wrapperTanggalKembali.style.display = 'none';
                }
            });
        }
    });
</script>
@endsection
