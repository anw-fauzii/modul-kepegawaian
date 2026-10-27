@extends('layouts.appui')

@section('title')
    <title>Master Data Karyawan - {{ isset($unitAktif) ? 'Unit ' . $unitAktif : (isset($kategoriAktif) ? $kategoriAktif->nama_kategori : 'Semua') }}</title>
@endsection

@php
    $countTotal = $karyawan->count();
    $countAktif = $karyawan->filter(fn($item) => (bool) ($item->user?->is_active ?? false))->count();
    $countNonAktif = $karyawan->filter(fn($item) => ! (bool) ($item->user?->is_active ?? false))->count();

    $countTetap = $karyawan->filter(fn($item) => $item->status_pegawai === 'Tetap' && (bool) ($item->user?->is_active ?? false))->count();
    $countKontrak = $karyawan->filter(fn($item) => $item->status_pegawai === 'Kontrak' && (bool) ($item->user?->is_active ?? false))->count();
    $countHonor = $karyawan->filter(fn($item) => $item->status_pegawai === 'Honor' && (bool) ($item->user?->is_active ?? false))->count();
@endphp

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
                        Karyawan{{ isset($unitAktif) ? ' - Unit ' . $unitAktif : (isset($kategoriAktif) ? ' - ' . $kategoriAktif->nama_kategori : '') }}
                        <div class="page-title-subheading">
                            {{ isset($unitAktif)
                                ? 'Daftar seluruh pegawai unit ' . $unitAktif . ' (Tetap, Kontrak, dan Honor).'
                                : (isset($kategoriAktif)
                                    ? 'Daftar karyawan kategori ' . $kategoriAktif->nama_kategori . '.'
                                    : 'Kelola data seluruh pegawai yayasan.') }}
                        </div>
                    </div>
                </div>

                <div class="page-title-actions">
                    <a href="{{ route('karyawan.create') }}" class="btn text-white shadow-sm font-weight-bold"
                        style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 8px;">
                        <i class="fa fa-plus-circle mr-1"></i> TAMBAH BARU
                    </a>
                </div>
            </div>
        </div>

        <!-- Baris 1: Status Keaktifan Akun User (2 di atas) -->
        <div class="row mb-3">
            <div class="col-md-4 mb-2">
                <div class="card stat-filter-card shadow-sm border-0 card-active" data-filter="all"
                    style="cursor: pointer; border-radius: 12px; transition: all 0.2s ease;">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-uppercase font-weight-bold"
                                style="font-size: 0.72rem; letter-spacing: 0.5px; color: #059669;">
                                Semua Karyawan Terdaftar
                            </div>
                            <div class="font-weight-bold my-1" style="font-size: 1.6rem; color: #059669; line-height: 1.2;">
                                {{ $countTotal }}
                            </div>
                            <span class="badge badge-success px-2 py-0 font-weight-bold" style="font-size: 0.7rem; border-radius: 4px;">
                                Karyawan Terdaftar
                            </span>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm text-white"
                            style="width: 44px; height: 44px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); flex-shrink: 0;">
                            <i class="pe-7s-users" style="font-size: 1.5rem; font-weight: bold;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <div class="card stat-filter-card shadow-sm border-0" data-filter="aktif"
                    style="cursor: pointer; border-radius: 12px; transition: all 0.2s ease;">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-uppercase font-weight-bold"
                                style="font-size: 0.72rem; letter-spacing: 0.5px; color: #059669;">
                                Pegawai Aktif
                            </div>
                            <div class="font-weight-bold my-1" style="font-size: 1.6rem; color: #059669; line-height: 1.2;">
                                {{ $countAktif }}
                            </div>
                            <span class="badge badge-success px-2 py-0 font-weight-bold" style="font-size: 0.7rem; border-radius: 4px;">
                                User is_active: True
                            </span>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm text-white"
                            style="width: 44px; height: 44px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); flex-shrink: 0;">
                            <i class="pe-7s-check" style="font-size: 1.5rem; font-weight: bold;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-2">
                <div class="card stat-filter-card shadow-sm border-0" data-filter="nonaktif"
                    style="cursor: pointer; border-radius: 12px; transition: all 0.2s ease;">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-uppercase font-weight-bold"
                                style="font-size: 0.72rem; letter-spacing: 0.5px; color: #dc2626;">
                                Pegawai Non-Aktif
                            </div>
                            <div class="font-weight-bold my-1" style="font-size: 1.6rem; color: #dc2626; line-height: 1.2;">
                                {{ $countNonAktif }}
                            </div>
                            <span class="badge badge-danger px-2 py-0 font-weight-bold" style="font-size: 0.7rem; border-radius: 4px;">
                                User is_active: False
                            </span>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm text-white"
                            style="width: 44px; height: 44px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); flex-shrink: 0;">
                            <i class="pe-7s-close-circle" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Baris 2: Kategori Kepegawaian (3 di bawah - Tetap, Kontrak, Honor Aktif) -->
        <div class="row mb-3">
            <div class="col-md-4 mb-2">
                <div class="card stat-filter-card shadow-sm border-0" data-filter="tetap"
                    style="cursor: pointer; border-radius: 12px; transition: all 0.2s ease;">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-uppercase font-weight-bold"
                                style="font-size: 0.72rem; letter-spacing: 0.5px; color: #047857;">
                                Pegawai Tetap
                            </div>
                            <div class="font-weight-bold my-1" style="font-size: 1.6rem; color: #047857; line-height: 1.2;">
                                {{ $countTetap }}
                            </div>
                            <span class="badge badge-success px-2 py-0 font-weight-bold"
                                style="font-size: 0.7rem; border-radius: 4px;">
                                Aktif • NIPY Akhiran -1
                            </span>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm text-white"
                            style="width: 44px; height: 44px; background: linear-gradient(135deg, #059669 0%, #047857 100%); flex-shrink: 0;">
                            <i class="pe-7s-medal" style="font-size: 1.4rem;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-2">
                <div class="card stat-filter-card shadow-sm border-0" data-filter="kontrak"
                    style="cursor: pointer; border-radius: 12px; transition: all 0.2s ease;">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-uppercase font-weight-bold"
                                style="font-size: 0.72rem; letter-spacing: 0.5px; color: #0284c7;">
                                Pegawai Kontrak
                            </div>
                            <div class="font-weight-bold my-1" style="font-size: 1.6rem; color: #0284c7; line-height: 1.2;">
                                {{ $countKontrak }}
                            </div>
                            <span class="badge badge-primary px-2 py-0 font-weight-bold"
                                style="font-size: 0.7rem; border-radius: 4px;">
                                Aktif • NIPY Akhiran -2
                            </span>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm text-white"
                            style="width: 44px; height: 44px; background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); flex-shrink: 0;">
                            <i class="pe-7s-note2" style="font-size: 1.4rem;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-2">
                <div class="card stat-filter-card shadow-sm border-0" data-filter="honor"
                    style="cursor: pointer; border-radius: 12px; transition: all 0.2s ease;">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-uppercase font-weight-bold"
                                style="font-size: 0.72rem; letter-spacing: 0.5px; color: #d97706;">
                                Pegawai Honor
                            </div>
                            <div class="font-weight-bold my-1" style="font-size: 1.6rem; color: #d97706; line-height: 1.2;">
                                {{ $countHonor }}
                            </div>
                            <span class="badge badge-warning text-white px-2 py-0 font-weight-bold"
                                style="font-size: 0.7rem; border-radius: 4px;">
                                Aktif • NIPY Akhiran -3
                            </span>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm text-white"
                            style="width: 44px; height: 44px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); flex-shrink: 0;">
                            <i class="pe-7s-star" style="font-size: 1.4rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="main-card card mb-4 shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="mb-0 table table-hover table-striped align-middle" id="myTable3" style="width: 100%;">
                        <thead>
                            <tr style="background: #f8fafc;">
                                <th style="width: 5%; text-align: center;">No</th>
                                <th>NIPY</th>
                                <th>Nama Lengkap</th>
                                <th>Jabatan</th>
                                <th>Golongan</th>
                                <th>Lama Kerja</th>
                                <th style="width: 16%; min-width: 130px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 1;
                            @endphp
                            @foreach ($karyawan as $item)
                                @php
                                    $statusPegawai = strtolower($item->status_pegawai);
                                    $badge = $item->status_pegawai_badge;
                                    $isActive = (bool) ($item->user?->is_active ?? false);
                                @endphp
                                <tr data-status="{{ $statusPegawai }}" data-active="{{ $isActive ? '1' : '0' }}">
                                    <td class="text-center font-weight-bold text-muted row-number">{{ $no++ }}</td>
                                    <td>
                                        <span class="font-weight-bold text-dark" style="font-family: monospace; font-size: 0.9rem;">
                                            {{ $item->nipy }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center flex-wrap" style="gap: 5px;">
                                            <span class="font-weight-bold text-dark" style="font-size: 0.92rem;">
                                                {{ $item->nama_lengkap }}{{ $item->gelar ? ', ' . $item->gelar : '' }}
                                            </span>
                                            <span class="badge {{ $badge['class'] }} px-2 py-0 shadow-sm font-weight-bold"
                                                style="border-radius: 4px; font-size: 0.7rem;">
                                                {{ $badge['label'] }}
                                            </span>
                                            @if ($isActive)
                                                <span class="badge badge-success px-2 py-0 shadow-sm font-weight-bold"
                                                    style="border-radius: 4px; font-size: 0.68rem;" title="Akun User Aktif">
                                                    <i class="fa fa-check-circle mr-1"></i> Aktif
                                                </span>
                                            @else
                                                <span class="badge badge-danger px-2 py-0 shadow-sm font-weight-bold"
                                                    style="border-radius: 4px; font-size: 0.68rem;" title="Akun User Non-Aktif">
                                                    <i class="fa fa-times-circle mr-1"></i> Non-Aktif
                                                </span>
                                            @endif
                                        </div>
                                        @if ($item->unit)
                                            <small class="text-muted">
                                                <i class="pe-7s-map-marker text-danger"></i> {{ $item->unit }}
                                                @if ($item->kategori)
                                                    &bull; {{ $item->kategori->nama_kategori }}
                                                @endif
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="font-weight-bold" style="color: #334155;">{{ $item->jabatan }}</span>
                                    </td>
                                    <td>{{ $item->golongan ?: '-' }}</td>
                                    <td>
                                        <span class="badge badge-light text-dark font-weight-bold px-2 py-1"
                                            style="border-radius: 6px; border: 1px solid #e2e8f0;">
                                            {{ $item->lama_kerja }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center align-items-center">
                                            <a href="{{ route('karyawan.show', $item->nipy) }}"
                                                class="btn btn-sm text-white shadow-sm mx-1"
                                                style="border-radius: 6px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; background: #0284c7; border: none;"
                                                data-toggle="tooltip" data-placement="top" title="Detail Data">
                                                <i class="fa fa-eye" style="font-size: 0.75rem;"></i>
                                            </a>

                                            <a href="{{ route('karyawan.edit', $item->nipy) }}"
                                                class="btn btn-sm btn-primary shadow-sm mx-1"
                                                style="border-radius: 6px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;"
                                                data-toggle="tooltip" data-placement="top" title="Edit Data">
                                                <i class="fa fa-edit" style="font-size: 0.75rem;"></i>
                                            </a>

                                            <form action="{{ route('karyawan.destroy', $item->nipy) }}" method="POST"
                                                class="delete-form d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    class="btn btn-sm btn-warning shadow-sm delete-button mx-1 p-0 text-white"
                                                    style="border-radius: 6px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;"
                                                    data-toggle="tooltip" data-placement="top" title="Hapus Data">
                                                    <i class="fa fa-trash" style="font-size: 0.75rem;"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
<style>
    .stat-filter-card {
        border: 2px solid transparent !important;
        background: #ffffff;
        height: 90px !important;
        max-height: 95px !important;
        overflow: hidden;
    }
    .stat-filter-card .card-body {
        padding: 12px 16px !important;
        height: 100% !important;
    }
    .stat-filter-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.08) !important;
    }
    .stat-filter-card.card-active {
        border: 2px solid #ea580c !important;
        background: #fffaf5 !important;
        box-shadow: 0 4px 12px rgba(234, 88, 12, 0.16) !important;
    }
    .status-filter-btn {
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        color: #475569;
        transition: all 0.2s ease;
    }
    .status-filter-btn:hover {
        background: #f1f5f9;
        color: #0f172a;
    }
    .status-filter-btn.active {
        background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important;
        color: #ffffff !important;
        border-color: #ea580c !important;
        box-shadow: 0 2px 6px rgba(234, 88, 12, 0.25);
    }
</style>
@endpush

@section('js')
<script>
    $(document).ready(function() {
        var table = $('#myTable3').DataTable();
        var currentFilter = 'all';

        // Sesuaikan teks bawaan DataTables
        if (table.settings() && table.settings()[0]) {
            table.settings()[0].oLanguage.sZeroRecords = "Tidak ada data karyawan untuk kategori ini.";
            table.settings()[0].oLanguage.sEmptyTable = "Belum ada data karyawan.";
        }

        var labels = {
            'all': 'Semua Karyawan (Total: {{ $countTotal }})',
            'aktif': 'Pegawai Aktif (is_active: True)',
            'nonaktif': 'Pegawai Non-Aktif (is_active: False)',
            'tetap': 'Pegawai Tetap Aktif (is_active: True, Akhiran -1)',
            'kontrak': 'Pegawai Kontrak Aktif (is_active: True, Akhiran -2)',
            'honor': 'Pegawai Honor Aktif (is_active: True, Akhiran -3)'
        };

        // Filter pencarian kustom DataTables
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            if (settings.nTable.id !== 'myTable3') return true;
            if (currentFilter === 'all') return true;
            var rowNode = settings.aoData[dataIndex].nTr;
            var rowStatus = $(rowNode).attr('data-status');
            var rowActive = $(rowNode).attr('data-active');

            if (currentFilter === 'aktif') {
                return rowActive === '1';
            }
            if (currentFilter === 'nonaktif') {
                return rowActive === '0';
            }
            if (currentFilter === 'tetap') {
                return rowStatus === 'tetap' && rowActive === '1';
            }
            if (currentFilter === 'kontrak') {
                return rowStatus === 'kontrak' && rowActive === '1';
            }
            if (currentFilter === 'honor') {
                return rowStatus === 'honor' && rowActive === '1';
            }
            return true;
        });

        // Penomoran otomatis kolom No saat pagination / filter / draw
        table.on('order.dt search.dt draw.dt', function() {
            var info = table.page.info();
            table.column(0, { search: 'applied', order: 'applied', page: 'current' }).nodes().each(function(cell, i) {
                cell.innerHTML = (info.start + i + 1);
            });
        });

        function applyFilter(filter) {
            currentFilter = filter;

            // Sinkronkan tab pills
            $('.status-filter-btn').removeClass('active');
            $('.status-filter-btn[data-filter="' + filter + '"]').addClass('active');

            // Sinkronkan stat cards
            $('.stat-filter-card').removeClass('card-active');
            $('.stat-filter-card[data-filter="' + filter + '"]').addClass('card-active');

            // Update keterangan filter aktif
            $('#filterStatusLabel').text(labels[filter] || 'Semua Karyawan (Total: {{ $countTotal }})');

            // Redraw DataTable murni tanpa manipulasi DOM manual
            table.draw();
        }

        // Event klik tab
        $('.status-filter-btn').on('click', function(e) {
            e.preventDefault();
            applyFilter($(this).data('filter'));
        });

        // Event klik stat card
        $('.stat-filter-card').on('click', function() {
            applyFilter($(this).data('filter'));
        });

        // Ambil query param ?status= jika ada
        var urlParams = new URLSearchParams(window.location.search);
        var statusParam = urlParams.get('status');
        if (statusParam && ['all', 'aktif', 'nonaktif', 'tetap', 'kontrak', 'honor'].includes(statusParam.toLowerCase())) {
            applyFilter(statusParam.toLowerCase());
        }
    });
</script>
@endsection