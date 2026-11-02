@extends('layouts.appui')

@section('title')
    <title>Master Data Jenis Cuti</title>
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
                        Master Jenis Cuti
                        <div class="page-title-subheading">
                            Kelola kategori jenis cuti, alokasi kuota lama hari, dan status pengajuan.
                        </div>
                    </div>
                </div>

                <div class="page-title-actions">
                    <a href="{{ route('jenis-cuti.create') }}" class="btn text-white shadow-sm font-weight-bold"
                        style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 8px;">
                        <i class="fa fa-plus-circle mr-1.5"></i> TAMBAH JENIS CUTI
                    </a>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 10px;">
                <i class="fa fa-check-circle mr-1.5"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <!-- MAIN CARD -->
        <div class="main-card card mb-3" style="border-radius: 12px; border: 1px solid #e2e8f0;">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="mb-0 table table-hover table-striped align-middle" id="myTable3">
                        <thead>
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th>Nama Jenis Cuti</th>
                                <th style="width: 15%; text-align: center;">Lama Hari</th>
                                <th>Keterangan</th>
                                <th style="width: 12%; text-align: center;">Status</th>
                                <th style="width: 15%; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 1;
                            @endphp
                            @forelse ($jenisCuti as $item)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>
                                        <div class="font-weight-bold text-dark" style="font-size: 13.5px;">
                                            {{ $item->nama }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-info px-2.5 py-1 text-white font-weight-bold"
                                            style="border-radius: 6px; font-size: 12px; background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
                                            <i class="fa fa-clock mr-1"></i> {{ $item->lama_hari }} Hari
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted small">
                                            {{ $item->keterangan ?: '-' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if ($item->is_active)
                                            <span class="badge badge-success px-2 py-1 font-weight-bold" style="border-radius: 6px; font-size: 11px;">
                                                <i class="fa fa-check-circle mr-1"></i> Aktif
                                            </span>
                                        @else
                                            <span class="badge badge-secondary px-2 py-1 font-weight-bold" style="border-radius: 6px; font-size: 11px;">
                                                <i class="fa fa-times-circle mr-1"></i> Non-Aktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center align-items-center">
                                            <a href="{{ route('jenis-cuti.edit', $item->id) }}"
                                                class="btn btn-sm btn-primary shadow-sm mx-1"
                                                style="border-radius: 6px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;"
                                                data-toggle="tooltip" data-placement="top" title="Edit Jenis Cuti">
                                                <i class="fa fa-edit" style="font-size: 11px;"></i>
                                            </a>

                                            <form action="{{ route('jenis-cuti.destroy', $item->id) }}" method="POST"
                                                class="delete-form d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    class="btn btn-sm btn-warning shadow-sm delete-button mx-1 p-0 text-white"
                                                    style="border-radius: 6px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;"
                                                    data-toggle="tooltip" data-placement="top" title="Hapus Jenis Cuti">
                                                    <i class="fa fa-trash" style="font-size: 11px;"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="fa fa-info-circle mr-1"></i> Belum ada data jenis cuti tersimpan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
