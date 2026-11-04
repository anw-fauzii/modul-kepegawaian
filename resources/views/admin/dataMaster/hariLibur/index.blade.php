@extends('layouts.appui')

@section('title')
    <title>Master Hari Libur Nasional - SIPIT</title>
@endsection

@section('content')
<div class="app-main__inner">
    <!-- PAGE TITLE -->
    <div class="app-page-title mb-3">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon text-white shadow-sm"
                    style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 12px;">
                    <i class="pe-7s-date" style="color: white;"></i>
                </div>
                <div>
                    Master Hari Libur Nasional & Yayasan
                    <div class="page-title-subheading">
                        Daftar tanggal libur nasional dan libur lembaga yang otomatis dilewati (tidak dihitung) saat pengajuan cuti pegawai.
                    </div>
                </div>
            </div>

            <div class="page-title-actions d-flex align-items-center">
                <!-- FORM SINKRONISASI API -->
                <form action="{{ route('hari-libur.sync') }}" method="POST" class="d-inline mr-2" onsubmit="return confirm('Apakah Anda yakin ingin menyinkronkan data hari libur nasional dari API sekarang?');">
                    @csrf
                    <div class="input-group">
                        <select name="year" class="custom-select custom-select-sm" style="border-radius: 8px 0 0 8px; width: 85px;">
                            @php $curYear = date('Y'); @endphp
                            @for ($y = $curYear - 1; $y <= $curYear + 2; $y++)
                                <option value="{{ $y }}" {{ $y == $curYear ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-sm btn-outline-primary shadow-sm font-weight-bold" style="border-radius: 0 8px 8px 0;">
                                <i class="fa fa-sync-alt mr-1"></i> Sinkron API
                            </button>
                        </div>
                    </div>
                </form>

                <a href="{{ route('hari-libur.create') }}" class="btn btn-sm text-white shadow-sm font-weight-bold py-2 px-3"
                    style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 8px;">
                    <i class="fa fa-plus-circle mr-1.5"></i> TAMBAH HARI LIBUR
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

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 10px;">
            <i class="fa fa-exclamation-triangle mr-1.5"></i> {{ session('error') }}
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
                            <th style="width: 20%;">Tanggal & Hari</th>
                            <th>Nama Hari Libur</th>
                            <th style="width: 18%; text-align: center;">Kategori</th>
                            <th>Keterangan</th>
                            <th style="width: 15%; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @forelse ($hariLibur as $item)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>
                                    <div class="font-weight-bold text-dark" style="font-size: 13px;">
                                        <i class="fa fa-calendar-alt text-danger mr-1"></i>
                                        {{ $item->tanggal ? $item->tanggal->isoFormat('dddd, D MMMM Y') : '-' }}
                                    </div>
                                    @if ($item->tanggal && $item->tanggal->isToday())
                                        <span class="badge badge-danger text-white py-0 px-1 font-weight-bold" style="font-size: 9.5px;">Hari Ini</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="font-weight-bold text-dark">{{ $item->nama }}</span>
                                </td>
                                <td class="text-center">
                                    @if ($item->is_cuti)
                                        <span class="badge font-weight-bold" style="background-color: #ffc107 !important; color: #212529 !important; border-radius: 6px; font-size: 11px; padding: 4px 10px;">
                                            Cuti Bersama
                                        </span>
                                    @elseif ($item->is_libur_nasional)
                                        <span class="badge font-weight-bold" style="background-color: #dc3545 !important; color: #ffffff !important; border-radius: 6px; font-size: 11px; padding: 4px 10px;">
                                            Libur Nasional
                                        </span>
                                    @else
                                        <span class="badge font-weight-bold" style="background-color: #0dcaf0 !important; color: #ffffff !important; border-radius: 6px; font-size: 11px; padding: 4px 10px;">
                                            Libur Yayasan
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted small">{{ $item->keterangan ?: '-' }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <a href="{{ route('hari-libur.edit', $item->id) }}"
                                            class="btn btn-sm btn-primary shadow-sm mx-1"
                                            style="border-radius: 6px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;"
                                            data-toggle="tooltip" data-placement="top" title="Edit Hari Libur">
                                            <i class="fa fa-edit" style="font-size: 11px;"></i>
                                        </a>

                                        <form action="{{ route('hari-libur.destroy', $item->id) }}" method="POST"
                                            class="delete-form d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                class="btn btn-sm btn-warning shadow-sm delete-button mx-1 p-0 text-white"
                                                style="border-radius: 6px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;"
                                                data-toggle="tooltip" data-placement="top" title="Hapus Hari Libur">
                                                <i class="fa fa-trash" style="font-size: 11px;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fa fa-info-circle mr-1"></i> Belum ada data hari libur tersimpan.
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
