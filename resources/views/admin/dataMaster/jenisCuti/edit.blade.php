@extends('layouts.appui')

@section('title')
    <title>Edit Jenis Cuti</title>
@endsection

@section('content')
    <div class="app-main__inner">
        <!-- PAGE TITLE & BREADCRUMB -->
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon text-white shadow-sm"
                        style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 12px;">
                        <i class="pe-7s-id" style="color: white;"></i>
                    </div>
                    <div>
                        Edit Data Jenis Cuti
                        <div class="page-title-subheading">
                            Perbarui rincian kategori jenis cuti dan alokasi durasi hari kerja.
                        </div>
                    </div>
                </div>

                <div class="page-title-actions">
                    <a href="{{ route('jenis-cuti.index') }}" class="btn text-white shadow-sm font-weight-bold"
                        style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 8px;">
                        <i class="fa fa-reply mr-1.5"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>

        <div class="main-card card" style="border-radius: 12px; border: 1px solid #e2e8f0;">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="font-weight-bold text-dark mb-0">
                    <i class="fa fa-edit text-primary mr-1.5"></i> Form Perbarui Jenis Cuti
                </h6>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('jenis-cuti.update', $jenisCuti->id) }}" id="editForm">
                    @csrf
                    @method('PUT')
                    @include('admin.dataMaster.jenisCuti._form')
                </form>
            </div>
        </div>
    </div>
@endsection
