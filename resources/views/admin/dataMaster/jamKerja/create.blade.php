@extends('layouts.appui')

@section('title')
    <title>Jam Kerja</title>
@endsection

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon text-white shadow-sm"
                        style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 12px;">
                        <i class="pe-7s-plugin" style="color: white;"></i>
                    </div>
                    <div>
                        Create Jam Kerja
                        <div class="page-title-subheading">
                            Tambah pengaturan jam kerja baru.
                        </div>
                    </div>
                </div>

                <div class="page-title-actions">
                    <a href="{{ route('jam-kerja.index') }}" class="btn text-white shadow-sm"
                        style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 8px;">
                        <i class="fa fa-reply mr-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="main-card card">
            <div class="card-body">
                <form method="post" action="{{ route('jam-kerja.store') }}" id="createForm">
                    @csrf
                    @include('admin.dataMaster.jamKerja._form')
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




