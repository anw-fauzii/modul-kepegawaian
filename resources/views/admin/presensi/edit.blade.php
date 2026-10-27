@extends('layouts.appui')

@section('title')
    <title>Edit Data Presensi</title>
@endsection

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <div class="app-main__inner">
        <!-- PAGE TITLE & BREADCRUMB -->
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon text-white shadow-sm"
                        style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 12px;">
                        <i class="pe-7s-date" style="color: white;"></i>
                    </div>
                    <div>
                        Edit Data Presensi
                        <div class="page-title-subheading">
                            Perbarui status atau rincian jam kehadiran karyawan.
                        </div>
                    </div>
                </div>

                <div class="page-title-actions">
                    <a href="{{ route('presensi.index') }}" class="btn text-white shadow-sm"
                        style="background: linear-gradient(135deg, #64748b 0%, #475569 100%) !important; border-radius: 8px;">
                        <i class="fa fa-reply mr-1"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>

        <!-- FORM CARD -->
        <div class="main-card card mb-4 shadow-sm border-0">
            <div class="card-body">
                <form action="{{ route('presensi.update', $presensi->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('admin.presensi._form')
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr('.timepicker-24', {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                allowInput: true
            });
        });
    </script>
@endsection
