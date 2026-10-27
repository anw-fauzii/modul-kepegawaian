@extends('layouts.appui')

@section('title')
    <title>Detail Fasilitas #{{ $fasilitas->id }} - SIPIT</title>
@endsection

@section('content')
<style>
    .badge-soft-success {
        background-color: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }
    .badge-soft-warning {
        background-color: #fef3c7;
        color: #b45309;
        border: 1px solid #fde68a;
    }
    .detail-label {
        font-size: 12px;
        color: #64748b;
        text-transform: uppercase;
        font-weight: 700;
        margin-bottom: 2px;
    }
    .detail-value {
        font-size: 15px;
        color: #1e293b;
        font-weight: 600;
        margin-bottom: 16px;
    }
</style>

<div class="app-main__inner">
    {{-- Page Header --}}
    <div class="app-page-title mb-3">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon bg-info text-white">
                    <i class="pe-7s-info icon-gradient bg-sunny-morning"></i>
                </div>
                <div>
                    <strong>Detail Fasilitas Karyawan</strong>
                    <div class="page-title-subheading">
                        Informasi lengkap aset inventaris dan status peminjaman oleh karyawan.
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

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        {{-- Detail Fasilitas --}}
        <div class="col-md-7 mb-3">
            <div class="main-card card border-0 shadow-sm h-20" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 font-weight-bold text-dark">
                        <i class="fa fa-box mr-1 text-primary"></i> Data Fasilitas
                    </h5>
                    @if($fasilitas->status)
                        <span class="badge badge-soft-success font-weight-bold px-3 py-2">
                            <i class="fa fa-check-circle mr-1"></i> Sudah Dikembalikan
                        </span>
                    @else
                        <span class="badge badge-soft-warning font-weight-bold px-3 py-2">
                            <i class="fa fa-clock mr-1"></i> Belum Dikembalikan
                        </span>
                    @endif
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="detail-label">Nama Fasilitas</div>
                            <div class="detail-value text-primary font-weight-bold" style="font-size: 18px;">
                                {{ $fasilitas->nama_fasilitas }}
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="detail-label">Jumlah Unit</div>
                            <div class="detail-value">
                                <span class="badge badge-pill badge-secondary px-3 py-1 font-weight-bold" style="font-size: 13px;">
                                    {{ $fasilitas->jumlah }} Unit
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="detail-label">Tahun Terima</div>
                            <div class="detail-value text-dark font-weight-bold" style="font-size: 17px;">
                                {{ $fasilitas->tahun_terima }}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="detail-label">Status Pengembalian</div>
                            <div class="detail-value">
                                {{ $fasilitas->status ? 'Sudah Dikembalikan' : 'Masih Dipinjamkan (Belum Dikembalikan)' }}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-label">Tanggal Pengembalian</div>
                            <div class="detail-value">
                                @if($fasilitas->tanggal_kembali)
                                    <span class="font-weight-bold text-success">
                                        <i class="fa fa-calendar-check mr-1"></i>{{ \Carbon\Carbon::parse($fasilitas->tanggal_kembali)->isoFormat('dddd, D MMMM Y') }}
                                    </span>
                                @else
                                    <span class="text-muted font-italic">- Belum dikembalikan -</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="detail-label">Catatan / Keterangan Kondisi</div>
                    <div class="detail-value p-3 rounded" style="background-color: #f8fafc; border: 1px solid #e2e8f0; font-weight: 500;">
                        {{ $fasilitas->catatan ?: 'Tidak ada catatan khusus.' }}
                    </div>

                    <div class="row text-muted small mt-4 pt-3 border-top">
                        <div class="col-sm-6">
                            <i class="fa fa-calendar mr-1"></i> Dicatat: {{ $fasilitas->created_at?->format('d/m/Y H:i') }}
                        </div>
                        <div class="col-sm-6 text-sm-right">
                            <i class="fa fa-sync-alt mr-1"></i> Terakhir Diperbarui: {{ $fasilitas->updated_at?->format('d/m/Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Detail Karyawan Penerima --}}
        <div class="col-md-5 mb-3">
            <div class="main-card card border-0 shadow-sm h-20" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 font-weight-bold text-dark">
                        <i class="fa fa-user-tie mr-1 text-info"></i> Karyawan Penerima
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="text-center pb-3 border-bottom mb-3">
                        <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center text-primary mb-2 shadow-sm" style="width: 70px; height: 70px;">
                            <i class="fa fa-user fa-2x"></i>
                        </div>
                        <h5 class="font-weight-bold text-dark mb-0">
                            {{ $fasilitas->karyawan?->nama_lengkap ?? 'NIPY: ' . $fasilitas->karyawan_nipy }}
                            @if($fasilitas->karyawan?->gelar)
                                <small class="text-muted">, {{ $fasilitas->karyawan->gelar }}</small>
                            @endif
                        </h5>
                        <div class="badge badge-light border mt-1 font-weight-bold px-2 py-1">
                            NIPY: {{ $fasilitas->karyawan_nipy }}
                        </div>
                    </div>

                    <div class="detail-label">Unit Kerja</div>
                    <div class="detail-value">
                        <i class="fa fa-building text-secondary mr-1"></i>
                        {{ $fasilitas->karyawan?->unit ?: '-' }}
                    </div>

                    <div class="detail-label">Jabatan</div>
                    <div class="detail-value">
                        <i class="fa fa-briefcase text-secondary mr-1"></i>
                        {{ $fasilitas->karyawan?->jabatan ?: '-' }}
                    </div>

                    @if($fasilitas->karyawan?->whatsapp)
                        <div class="detail-label">Kontak WhatsApp</div>
                        <div class="detail-value">
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $fasilitas->karyawan->whatsapp) }}" target="_blank" class="text-success font-weight-bold">
                                <i class="fab fa-whatsapp mr-1"></i> {{ $fasilitas->karyawan->whatsapp }}
                            </a>
                        </div>
                    @endif
                </div>

                <div class="card-footer bg-light border-top p-3 d-flex justify-content-between align-items-center">
                    {{-- Toggle Status Button --}}
                    <form action="{{ route('fasilitas.toggle-status', $fasilitas->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengubah status fasilitas ini?')">
                        @csrf
                        <button type="submit" class="btn {{ $fasilitas->status ? 'btn-warning text-dark' : 'btn-success' }} font-weight-bold btn-sm">
                            <i class="fa {{ $fasilitas->status ? 'fa-undo' : 'fa-check' }} mr-1"></i>
                            {{ $fasilitas->status ? 'Tandai Belum Kembali' : 'Tandai Sudah Kembali' }}
                        </button>
                    </form>

                    <div>
                        <a href="{{ route('fasilitas.edit', $fasilitas->id) }}" class="btn btn-primary btn-sm font-weight-bold mr-1">
                            <i class="fa fa-edit mr-1"></i> Edit
                        </a>
                        <form action="{{ route('fasilitas.destroy', $fasilitas->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data fasilitas ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm delete-button" title="Hapus">
                                <i class="fa fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
