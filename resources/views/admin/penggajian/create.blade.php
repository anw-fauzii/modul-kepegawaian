@extends('layouts.appui')

@section('title')
    <title>Tambah Slip Gaji - SIPIT</title>
@endsection

@section('content')
<div class="app-main__inner">
    <div class="app-page-title mb-3">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon bg-success text-white"><i class="pe-7s-cash"></i></div>
                <div>
                    <strong>Tambah Slip Gaji</strong>
                    <div class="page-title-subheading">Input komponen penerimaan &amp; potongan gaji karyawan per bulan.</div>
                </div>
            </div>
            <div class="page-title-actions">
                <a href="{{ route('penggajian.index') }}" class="btn btn-outline-secondary font-weight-bold">
                    <i class="fa fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-danger border-0 shadow-sm"><i class="fa fa-exclamation-triangle mr-2"></i>{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm">
            <strong>Mohon periksa kembali isian:</strong>
            <ul class="mb-0 mt-1 pl-3">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('penggajian.store') }}" method="POST">
        @csrf
        @include('admin.penggajian._form')
    </form>
</div>
@endsection
