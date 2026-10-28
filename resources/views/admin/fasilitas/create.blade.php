@extends('layouts.appui')

@section('title')
    <title>Tambah Fasilitas Karyawan - SIPIT</title>
@endsection

@section('content')
<div class="app-main__inner">
    {{-- Page Header --}}
    <div class="app-page-title mb-3">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon bg-success text-white">
                    <i class="pe-7s-plus icon-gradient bg-malibu-beach"></i>
                </div>
                <div>
                    <strong>Pencatatan Fasilitas Karyawan</strong>
                    <div class="page-title-subheading">
                        Input data inventaris atau sarana kerja yang diserahkan/dipinjamkan kepada karyawan.
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
            <form action="{{ route('fasilitas.store') }}" method="POST">
                @csrf

                <div class="row">
                    {{-- Karyawan (NIPY) --}}
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold text-dark">
                            Karyawan Penerima <span class="text-danger">*</span>
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

                    {{-- Nama Fasilitas --}}
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold text-dark">
                            Nama Fasilitas / Barang <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nama_fasilitas" class="form-control @error('nama_fasilitas') is-invalid @enderror" placeholder="Contoh: Laptop Lenovo ThinkPad / Kunci Ruangan / Kendaraan Operasional" value="{{ old('nama_fasilitas') }}" required>
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
                        <input type="number" name="jumlah" class="form-control @error('jumlah') is-invalid @enderror" value="{{ old('jumlah', 1) }}" min="1" max="1000" required>
                        @error('jumlah')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tahun Terima --}}
                    <div class="col-md-3 mb-3">
                        <label class="font-weight-bold text-dark">
                            Tahun Terima <span class="text-danger">*</span>
                        </label>
                        <input type="number" name="tahun_terima" class="form-control @error('tahun_terima') is-invalid @enderror" value="{{ old('tahun_terima', date('Y')) }}" min="1990" max="{{ date('Y') }}" required>
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
                            <input type="checkbox" name="status" class="custom-control-input" id="statusSwitch" value="1" {{ old('status') ? 'checked' : '' }}>
                            <label class="custom-control-label font-weight-bold" for="statusSwitch" id="statusLabel">
                                {{ old('status') ? 'Sudah Dikembalikan' : 'Belum Dikembalikan' }}
                            </label>
                        </div>
                    </div>

                    {{-- Tanggal Kembali --}}
                    <div class="col-md-3 mb-3" id="wrapperTanggalKembali" style="{{ old('status') ? '' : 'display: none;' }}">
                        <label class="font-weight-bold text-dark">
                            Tanggal Pengembalian
                        </label>
                        <input type="date" name="tanggal_kembali" id="tanggal_kembali" class="form-control @error('tanggal_kembali') is-invalid @enderror" value="{{ old('tanggal_kembali', date('Y-m-d')) }}">
                        @error('tanggal_kembali')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    {{-- Catatan --}}
                    <div class="col-md-12 mb-4">
                        <label class="font-weight-bold text-dark">Catatan / Keterangan Kondisi</label>
                        <textarea name="catatan" class="form-control @error('catatan') is-invalid @enderror" rows="3" placeholder="Informasi tambahan seperti nomor seri (SN), kondisi fisik barang saat diserahkan/dikembalikan, atau spesifikasi...">{{ old('catatan') }}</textarea>
                        @error('catatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end border-top pt-3">
                    <a href="{{ route('fasilitas.index') }}" class="btn btn-outline-secondary mr-2 px-4">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary px-4 font-weight-bold shadow-sm">
                        <i class="fa fa-save mr-1"></i> Simpan Fasilitas
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
