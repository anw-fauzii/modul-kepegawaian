@extends('layouts.appui')

@section('title')
    <title>Tambah Hari Libur - SIPIT</title>
@endsection

@section('content')
<div class="app-main__inner">
    <div class="app-page-title mb-3">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon text-white shadow-sm"
                    style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 12px;">
                    <i class="pe-7s-date" style="color: white;"></i>
                </div>
                <div>
                    Tambah Hari Libur
                    <div class="page-title-subheading">
                        Tambahkan tanggal hari libur nasional atau libur internal yayasan.
                    </div>
                </div>
            </div>

            <div class="page-title-actions">
                <a href="{{ route('hari-libur.index') }}" class="btn text-white shadow-sm font-weight-bold"
                    style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 8px;">
                    <i class="fa fa-reply mr-1.5"></i> Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>

    <div class="main-card card" style="border-radius: 12px; border: 1px solid #e2e8f0;">
        <div class="card-header bg-white py-3 border-bottom">
            <h6 class="font-weight-bold text-dark mb-0">
                <i class="fa fa-plus-circle text-warning mr-1.5"></i> Form Tambah Hari Libur
            </h6>
        </div>
        <div class="card-body p-4">
            <form method="POST" action="{{ route('hari-libur.store') }}" id="createForm">
                @csrf
                @include('admin.dataMaster.hariLibur._form')
            </form>
        </div>
    </div>
</div>
@endsection
