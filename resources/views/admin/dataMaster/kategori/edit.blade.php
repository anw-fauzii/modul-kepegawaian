@extends('layouts.appui')

@section('title')
    <title>Kategori Karyawan</title>
@endsection

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon text-white shadow-sm"
                        style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 12px;">
                        <i class="pe-7s-plugin" style="color: white;"></i>
                    </div>
                    <div>
                        Perbarui Kategori Karyawan
                        <div class="page-title-subheading">
                            Kelola kategori karyawan untuk data pelengkap.
                        </div>
                    </div>
                </div>

                <div class="page-title-actions">
                    <a href="{{ route('kategori.index') }}" class="btn text-white shadow-sm"
                        style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 8px;">
                        <i class="fa fa-reply mr-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="main-card card">
            <div class="card-header">
                Update Data
            </div>
            <div class="card-body">
                <form method="post" action="{{ route('kategori.update', $kategori->id) }}" id="editForm">
                    @csrf
                    @method('PUT')
                    <div class="form-row">
                        <div class="col-md-12">
                            <div class="position-relative form-group">
                                <label for="nama_kategori" class="">Kategori Karyawan</label>
                                <input name="nama_kategori" id="nama_kategori" placeholder="Masukkan Kategori Karyawan"
                                    type="text" class="form-control @error('nama_kategori') is-invalid @enderror"
                                    value="{{ $kategori->nama_kategori ?? old('nama_kategori') }}">
                                @error('nama_kategori')
                                    <div class="invalid-feedback" style="font-style: italic; font-size: 0.7rem;">
                                        {{ strtolower($message) }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-xl text-white shadow-sm fw-bold px-4" id="submitBtn"
                            style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 8px; border: none;">
                            <i class="fa fa-save mr-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
