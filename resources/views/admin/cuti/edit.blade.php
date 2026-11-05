@extends('layouts.appui')

@section('title')
    <title>Edit Pengajuan Cuti - SIPIT</title>
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
                    <strong>Edit Pengajuan Cuti</strong>
                    <div class="page-title-subheading">
                        Perbarui data permohonan cuti pegawai, penyesuaian rentang tanggal, kontak, atau status persetujuan (ACC).
                    </div>
                </div>
            </div>
            <div class="page-title-actions">
                <a href="{{ route('cuti.index') }}" class="btn btn-outline-secondary font-weight-bold" style="border-radius: 8px;">
                    <i class="fa fa-arrow-left mr-1.5"></i> Kembali
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
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="font-weight-bold text-dark mb-0">
                <i class="fa fa-edit text-primary mr-1.5"></i> Edit Data Cuti: {{ $cuti->karyawan?->nama_lengkap ?? $cuti->karyawan_nipy }}
            </h6>
            <div>
                {!! $cuti->acc ? '<span class="badge badge-success px-2.5 py-1"><i class="fa fa-check mr-1"></i> Disetujui (ACC)</span>' : '<span class="badge badge-warning px-2.5 py-1 text-white"><i class="fa fa-clock mr-1"></i> Menunggu ACC</span>' !!}
            </div>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('cuti.update', $cuti->id) }}" method="POST" id="editCutiForm">
                @csrf
                @method('PUT')
                @include('admin.cuti._form')
            </form>
        </div>
    </div>
</div>
@endsection
