@extends('layouts.appui')

@section('title')
    <title>Form Pengajuan Cuti - SIPIT</title>
@endsection

@section('content')
<div class="app-main__inner">
    {{-- Page Header --}}
    <div class="app-page-title mb-3">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon text-white shadow-sm"
                    style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 12px;">
                    <i class="pe-7s-note2" style="color: white;"></i>
                </div>
                <div>
                    <strong>Form Pengajuan Cuti</strong>
                    <div class="page-title-subheading">
                        Input data permohonan cuti pegawai, jenis cuti, rentang tanggal, alamat, telepon, dan status ACC.
                    </div>
                </div>
            </div>
            <div class="page-title-actions">
                <a href="{{ route('cuti.index') }}" class="btn btn-outline-secondary font-weight-bold" style="border-radius: 8px;">
                    <i class="fa fa-arrow-left mr-1.5"></i> Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>

    {{-- Error Validation Alert --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 10px;">
            <strong><i class="fa fa-exclamation-triangle mr-1"></i> Mohon periksa kembali isian formulir:</strong>
            <ul class="mb-0 mt-1 pl-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Form Card --}}
    <div class="main-card card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-header bg-white py-3 border-bottom">
            <h6 class="font-weight-bold text-dark mb-0">
                <i class="fa fa-file-signature text-warning mr-1.5"></i> Formulir Permohonan Cuti Pegawai
            </h6>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('cuti.store') }}" method="POST" id="createCutiForm">
                @csrf
                @include('admin.cuti._form')
            </form>
        </div>
    </div>
</div>
@endsection
