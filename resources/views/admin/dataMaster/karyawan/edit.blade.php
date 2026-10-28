@extends('layouts.appui')

@section('title')
    <title>Karyawan</title>
@endsection

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon text-white shadow-sm"
                        style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 12px;">
                        <i class="pe-7s-users" style="color: white;"></i>
                    </div>
                    <div>
                        Perbarui Karyawan
                        <div class="page-title-subheading">
                            Perbarui data karyawan.
                        </div>
                    </div>
                </div>

                <div class="page-title-actions">
                    <a href="{{ route('karyawan.index') }}" class="btn text-white shadow-sm"
                        style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 8px;">
                        <i class="fa fa-reply mr-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="main-card card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-body p-4 sm:p-5">
                <form method="post" action="{{ route('karyawan.update', $karyawan->nipy) }}" id="editForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @include('admin.dataMaster.karyawan._form')
                </form>
            </div>
        </div>
    </div>
@endsection
