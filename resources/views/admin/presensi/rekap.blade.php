@extends('layouts.appui')

@section('title')
    <title>Rekapitulasi Presensi Bulanan - SIPIT</title>
@endsection

@section('content')
<style>
    .stat-card {
        border-radius: 14px;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(226, 232, 240, 0.8);
        background: #ffffff;
        position: relative;
        overflow: hidden;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 24px -6px rgba(0, 0, 0, 0.08), 0 4px 8px -4px rgba(0, 0, 0, 0.04);
    }
    .stat-icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 19px;
        flex-shrink: 0;
    }
    .badge-status {
        display: inline-flex;
        align-items: center;
        padding: 4px 9px;
        border-radius: 6px;
        font-size: 11.5px;
        font-weight: 600;
        line-height: 1.2;
    }
    .badge-soft-success   { background-color: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
    .badge-soft-warning   { background-color: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
    .badge-soft-info      { background-color: #f0f9ff; color: #0369a1; border: 1px solid #bae6fd; }
    .badge-soft-secondary { background-color: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
    .badge-soft-primary   { background-color: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe; }
    .badge-soft-dark      { background-color: #faf5ff; color: #6b21a8; border: 1px solid #e9d5ff; }
    .badge-soft-danger    { background-color: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

    .absen-pill {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        padding: 2px 7px;
        border-radius: 5px;
        font-size: 11px;
        font-weight: 600;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        color: #64748b;
    }
    .absen-pill.has-val-info    { background: #f0f9ff; color: #0369a1; border-color: #bae6fd; }
    .absen-pill.has-val-warning { background: #fffbeb; color: #b45309; border-color: #fde68a; }
    .absen-pill.has-val-primary { background: #eef2ff; color: #4338ca; border-color: #c7d2fe; }
    .absen-pill.has-val-danger  { background: #fef2f2; color: #b91c1c; border-color: #fecaca; font-weight: 700; }

    .rate-bar-container {
        width: 100%;
        max-width: 85px;
        background-color: #e2e8f0;
        height: 6px;
        border-radius: 4px;
        overflow: hidden;
        margin: 4px auto 0;
    }
    .rate-bar-fill {
        height: 100%;
        border-radius: 4px;
        transition: width 0.4s ease;
    }
    .form-filter-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: #475569;
        letter-spacing: 0.04em;
        margin-bottom: 5px;
        display: block;
    }
    .form-filter-control {
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        font-size: 13px;
        height: 38px;
        transition: all 0.2s ease;
        color: #1e293b;
    }
    .form-filter-control:focus {
        border-color: #ea580c;
        box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.15);
    }
</style>

@php
    $sumHadir = $rekapData->sum('total_hadir');
    $sumTerlambat = $rekapData->sum('total_terlambat');
    $sumTerlambatIzin = $rekapData->sum('total_terlambat_izin');
    $sumMenitTerlambat = $rekapData->sum('akumulasi_keterlambatan_menit');
    $sumTugas = $rekapData->sum('total_tugas');
    $sumIzin = $rekapData->sum('total_izin');
    $sumSakit = $rekapData->sum('total_sakit');
    $sumCuti = $rekapData->sum('total_cuti');
    $sumAlpa = $rekapData->sum('total_alpa');
    $sumPulangCepat = $rekapData->sum('total_pulang_cepat');
    $sumShaum = $rekapData->sum('total_shaum');
    $totalHariCatatan = $sumHadir + $sumTerlambat + $sumTugas + $sumIzin + $sumSakit + $sumCuti + $sumAlpa;
    $persenKehadiran = $totalHariCatatan > 0 ? round((($sumHadir + $sumTerlambat) / $totalHariCatatan) * 100, 1) : 0;
@endphp

<div class="app-main__inner">
    <!-- PAGE TITLE & ACTIONS -->
    <div class="app-page-title mb-3">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon text-white shadow-sm"
                    style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 14px;">
                    <i class="pe-7s-graph2" style="color: white;"></i>
                </div>
                <div>
                    Rekapitulasi Presensi Bulanan
                    <div class="page-title-subheading">
                        Ringkasan kehadiran seluruh pegawai dengan perhitungan otomatis <strong>Tutup Buku Tanggal 28</strong>.
                    </div>
                </div>
            </div>

            <div class="page-title-actions d-flex align-items-center" style="gap: 8px;">
                <a href="{{ route('presensi.matriks', request()->query()) }}"
                    class="btn btn-outline-secondary shadow-sm font-weight-bold" style="border-radius: 8px;">
                    <i class="fa fa-calendar-check-o mr-1"></i> Matriks Harian
                </a>
                <a href="{{ route('presensi.create') }}" class="btn text-white shadow-sm font-weight-bold"
                    style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 8px;">
                    <i class="fa fa-plus-circle mr-1"></i> Input Presensi
                </a>
            </div>
        </div>
    </div>

    <!-- EXECUTIVE KPI SUMMARY CARDS -->
    <div class="row mb-3">
        <div class="col-sm-6 col-lg-3 mb-2">
            <div class="stat-card p-3 shadow-sm">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small font-weight-bold text-uppercase">Tingkat Hadir</div>
                        <div class="h3 font-weight-bold text-success mb-0 mt-1">{{ $persenKehadiran }}%</div>
                        <div class="small text-muted mt-1">
                            <strong>{{ $sumHadir + $sumTerlambat }}</strong> hadir &bull; <strong>{{ $sumTugas }}</strong> tugas
                        </div>
                    </div>
                    <div class="stat-icon" style="background: #ecfdf5; color: #10b981;">
                        <i class="fa fa-user-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3 mb-2">
            <div class="stat-card p-3 shadow-sm">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small font-weight-bold text-uppercase">Terlambat</div>
                        <div class="h3 font-weight-bold text-warning mb-0 mt-1">{{ $sumTerlambat }} <span style="font-size: 13px;">hari</span></div>
                        <div class="small text-muted mt-1">
                            Total: <strong class="text-danger">{{ $sumMenitTerlambat }} mnt</strong>
                            @if ($sumTerlambatIzin > 0)
                                <span>({{ $sumTerlambatIzin }} izin)</span>
                            @endif
                        </div>
                    </div>
                    <div class="stat-icon" style="background: #fffbeb; color: #f59e0b;">
                        <i class="fa fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3 mb-2">
            <div class="stat-card p-3 shadow-sm">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small font-weight-bold text-uppercase">Izin, Sakit & Cuti</div>
                        <div class="h3 font-weight-bold text-primary mb-0 mt-1">{{ $sumIzin + $sumSakit + $sumCuti }} <span style="font-size: 13px;">hari</span></div>
                        <div class="small text-muted mt-1">
                            I: <strong>{{ $sumIzin }}</strong> &bull; S: <strong>{{ $sumSakit }}</strong> &bull; C: <strong>{{ $sumCuti }}</strong>
                        </div>
                    </div>
                    <div class="stat-icon" style="background: #eef2ff; color: #6366f1;">
                        <i class="fa fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3 mb-2">
            <div class="stat-card p-3 shadow-sm">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small font-weight-bold text-uppercase">Alpa & Shaum</div>
                        <div class="h3 font-weight-bold {{ $sumAlpa > 0 ? 'text-danger' : 'text-secondary' }} mb-0 mt-1">
                            {{ $sumAlpa }} <span style="font-size: 13px;">Alpa</span>
                        </div>
                        <div class="small text-muted mt-1">
                            Pulang Cepat: <strong>{{ $sumPulangCepat }}x</strong> &bull; Shaum: <strong class="text-success">{{ $sumShaum }}</strong>
                        </div>
                    </div>
                    <div class="stat-icon" style="background: #fef2f2; color: #ef4444;">
                        <i class="fa fa-user-times"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FILTER CARD -->
    <div class="main-card card mb-3 shadow-sm border-0" style="border-radius: 12px;">
        <div class="card-body p-3">
            <form action="{{ route('presensi.rekap') }}" method="GET" id="rekapFilterForm">
                <div class="row align-items-end">
                    <div class="col-md-5 col-sm-6 mb-2 mb-md-0">
                        <label class="form-filter-label d-flex justify-content-between">
                            <span><i class="fa fa-calendar-alt mr-1 text-warning"></i> Periode Master Bulan</span>
                            <span class="text-warning" style="text-transform: none;"><i class="fa fa-info-circle"></i> Cut-off tgl 28</span>
                        </label>
                        <select name="bulan_id" class="form-control form-filter-control" required onchange="document.getElementById('rekapFilterForm').submit();">
                            @foreach ($bulanList as $b)
                                <option value="{{ $b->id }}" {{ $selectedBulanId == $b->id ? 'selected' : '' }}>
                                    {{ $b->nama_bulan }} {{ $b->tahunAjaran ? '(' . $b->tahunAjaran->nama_tahun_ajaran . ')' : '' }} {{ $b->status ? ' [Aktif]' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-5 col-sm-6 mb-2 mb-md-0">
                        <label class="form-filter-label">
                            <i class="fa fa-layer-group mr-1 text-primary"></i> Kategori Pegawai
                        </label>
                        <select name="kepegawaian_master_kategori_id" class="form-control form-filter-control" onchange="document.getElementById('rekapFilterForm').submit();">
                            <option value="">Semua Kategori</option>
                            @foreach ($kategoriList as $k)
                                <option value="{{ $k->id }}" {{ $selectedKategoriId == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 col-sm-12">
                        <a href="{{ route('presensi.rekap') }}" class="btn btn-light border btn-block font-weight-bold shadow-sm d-flex align-items-center justify-content-center"
                            style="border-radius: 8px; height: 38px;" title="Reset Filter">
                            <i class="fa fa-undo mr-1"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- MAIN TABLE CARD (DataTables #myTable3) -->
    <div class="main-card card mb-4 shadow-sm border-0" style="border-radius: 12px;">
        <div class="card-body p-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-2 pb-2 border-bottom">
                <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                    <span class="text-muted small font-weight-bold">Periode:</span>
                    <span class="badge badge-soft-warning px-2.5 py-1 font-weight-bold" style="font-size: 12px;">
                        <i class="fa fa-calendar mr-1"></i> {{ $selectedBulan?->nama_bulan ?? 'Pilih Bulan' }}
                        {{ $selectedBulan?->tahunAjaran ? '(' . $selectedBulan->tahunAjaran->nama_tahun_ajaran . ')' : '' }}
                    </span>
                    @if ($selectedKategoriId && $kategoriList->firstWhere('id', $selectedKategoriId))
                        <span class="badge badge-soft-primary px-2.5 py-1 font-weight-bold" style="font-size: 12px;">
                            {{ $kategoriList->firstWhere('id', $selectedKategoriId)->nama_kategori }}
                        </span>
                    @endif
                </div>
                <div class="small text-muted font-weight-bold">
                    Total <strong class="text-dark">{{ $rekapData->count() }}</strong> pegawai
                </div>
            </div>

            <div class="table-responsive">
                <table class="mb-0 table table-hover table-striped align-middle" id="myTable3" style="width: 100%;">
                    <thead>
                        <tr style="background: #f8fafc;">
                            <th style="width: 5%; text-align: center;">No</th>
                            <th>Nama Lengkap</th>
                            <th style="text-align: center;">Hadir</th>
                            <th>Terlambat & Pulang Cepat</th>
                            <th style="text-align: center;">Izin / Sakit / Cuti / Alpa</th>
                            <th style="text-align: center;">Shaum</th>
                            <th style="text-align: center;">Performa</th>
                            <th style="width: 60px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rekapData as $index => $karyawan)
                            @php
                                $totalPegawaiHadir = $karyawan->total_hadir + $karyawan->total_terlambat;
                                $totalCatatan = $totalPegawaiHadir + $karyawan->total_izin + $karyawan->total_sakit + $karyawan->total_cuti + $karyawan->total_alpa;
                                $rate = $totalCatatan > 0 ? round(($totalPegawaiHadir / $totalCatatan) * 100) : 0;
                                $rateColor = $rate >= 90 ? '#10b981' : ($rate >= 75 ? '#f59e0b' : '#ef4444');
                            @endphp
                            <tr>
                                <td class="text-center font-weight-bold text-muted row-number">{{ $index + 1 }}</td>

                                <!-- Nama Lengkap + Foto + NIPY & Unit -->
                                <td>
                                    <div class="d-flex align-items-center" style="gap: 10px;">
                                        @if ($karyawan->foto_url)
                                            <img src="{{ $karyawan->foto_url }}" alt="Foto"
                                                style="width: 38px; height: 38px; object-fit: cover; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 1px 4px rgba(0,0,0,.2); flex-shrink: 0;">
                                        @else
                                            <span class="d-flex align-items-center justify-content-center text-white font-weight-bold"
                                                style="width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg, #ea580c, #d97706); font-size: 0.82rem; flex-shrink: 0;">
                                                {{ strtoupper(mb_substr($karyawan->nama_lengkap ?? 'P', 0, 1)) }}
                                            </span>
                                        @endif
                                        <div style="min-width: 0;">
                                            <div class="font-weight-bold text-dark text-truncate" style="font-size: 0.88rem; max-width: 230px;"
                                                title="{{ $karyawan->nama_lengkap }}{{ $karyawan->gelar ? ', ' . $karyawan->gelar : '' }}">
                                                {{ $karyawan->nama_lengkap }}{{ $karyawan->gelar ? ', ' . $karyawan->gelar : '' }}
                                            </div>
                                            <small class="text-muted d-block text-truncate" style="max-width: 230px;">
                                                {{ $karyawan->nipy }}
                                                @if ($karyawan->unit)
                                                    &nbsp;|&nbsp; <i class="pe-7s-map-marker text-danger"></i> {{ $karyawan->unit }}
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </td>

                                <!-- Hadir & Tugas -->
                                <td class="text-center" style="white-space: nowrap;">
                                    <span class="badge-status badge-soft-success font-weight-bold">
                                        <i class="fa fa-check-circle mr-1"></i> {{ $karyawan->total_hadir }} hari
                                    </span>
                                    @if (($karyawan->total_tugas ?? 0) > 0)
                                        <div class="mt-1">
                                            <span class="badge badge-soft-primary px-1.5 py-0 font-weight-bold" style="font-size: 10px;">
                                                <i class="fa fa-briefcase mr-1"></i> {{ $karyawan->total_tugas }} Tugas
                                            </span>
                                        </div>
                                    @endif
                                </td>

                                <!-- Terlambat & Pulang Cepat -->
                                <td style="white-space: nowrap;">
                                    @if ($karyawan->total_terlambat > 0 || $karyawan->total_pulang_cepat > 0)
                                        <div class="d-flex flex-column" style="gap: 3px;">
                                            @if ($karyawan->total_terlambat > 0)
                                                <div class="d-flex align-items-center" style="gap: 4px;">
                                                    <span class="badge-status badge-soft-warning py-0.5 px-2">
                                                        <i class="fa fa-clock mr-1"></i> {{ $karyawan->total_terlambat }}x Telat
                                                    </span>
                                                    @if (($karyawan->akumulasi_keterlambatan_menit ?? 0) > 0)
                                                        <span class="text-danger font-weight-bold" style="font-size: 11.5px;">
                                                            ({{ $karyawan->akumulasi_keterlambatan_menit }} mnt)
                                                        </span>
                                                    @endif
                                                    @if ($karyawan->total_terlambat_izin > 0)
                                                        <span class="badge badge-soft-info py-0 px-1" style="font-size: 9.5px;">
                                                            {{ $karyawan->total_terlambat_izin }} izin
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                            @if ($karyawan->total_pulang_cepat > 0)
                                                <div class="d-flex align-items-center" style="gap: 4px;">
                                                    <span class="badge badge-soft-secondary py-0.5 px-2" style="font-size: 10.5px;">
                                                        Pulang Cepat: {{ $karyawan->total_pulang_cepat }}x
                                                    </span>
                                                    @if (($karyawan->akumulasi_pulang_cepat_menit ?? 0) > 0)
                                                        <small class="text-muted">({{ $karyawan->akumulasi_pulang_cepat_menit }} mnt)</small>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted small"><i class="fa fa-check text-success mr-1"></i> Tepat Waktu</span>
                                    @endif
                                </td>

                                <!-- Ketidakhadiran (I / S / C / A) -->
                                <td class="text-center" style="white-space: nowrap;">
                                    <div class="d-inline-flex align-items-center flex-wrap justify-content-center" style="gap: 4px;">
                                        <span class="absen-pill {{ $karyawan->total_izin > 0 ? 'has-val-info' : '' }}" title="Izin">
                                            I: {{ $karyawan->total_izin }}
                                        </span>
                                        <span class="absen-pill {{ $karyawan->total_sakit > 0 ? 'has-val-warning' : '' }}" title="Sakit">
                                            S: {{ $karyawan->total_sakit }}
                                        </span>
                                        <span class="absen-pill {{ $karyawan->total_cuti > 0 ? 'has-val-primary' : '' }}" title="Cuti">
                                            C: {{ $karyawan->total_cuti }}
                                        </span>
                                        <span class="absen-pill {{ $karyawan->total_alpa > 0 ? 'has-val-danger' : '' }}" title="Alpa">
                                            A: {{ $karyawan->total_alpa }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Shaum -->
                                <td class="text-center" style="white-space: nowrap;">
                                    @if (($karyawan->total_shaum ?? 0) > 0)
                                        <span class="badge-status badge-soft-success font-weight-bold py-1 px-2">
                                            <i class="fa fa-moon mr-1"></i> {{ $karyawan->total_shaum }}
                                        </span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>

                                <!-- Performa -->
                                <td class="text-center" style="white-space: nowrap;">
                                    <div class="font-weight-bold text-dark" style="font-size: 11.5px;">{{ $rate }}%</div>
                                    <div class="rate-bar-container">
                                        <div class="rate-bar-fill" style="width: {{ $rate }}%; background-color: {{ $rateColor }};"></div>
                                    </div>
                                </td>

                                <!-- Aksi (3-Dots Dropdown) -->
                                <td class="text-center">
                                    <div class="action-dropdown">
                                        <button type="button" class="btn-action-dots" title="Menu Aksi">
                                            <i class="fa fa-ellipsis-v"></i>
                                        </button>
                                        <div class="action-dropdown-menu">
                                            <a href="{{ route('presensi.index', ['kepegawaian_master_bulan_id' => $selectedBulanId, 'search' => $karyawan->nipy]) }}"
                                                class="action-dropdown-item item-edit">
                                                <span class="action-item-icon"><i class="fa fa-list-alt"></i></span>
                                                <span>Riwayat Presensi</span>
                                            </a>
                                            <a href="{{ route('karyawan.show', $karyawan->nipy) }}"
                                                class="action-dropdown-item item-view">
                                                <span class="action-item-icon"><i class="fa fa-user"></i></span>
                                                <span>Profil Pegawai</span>
                                            </a>
                                        </div>
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

@section('js')
<script>
    $(document).ready(function() {
        if ($('#myTable3').length && $.fn.DataTable.isDataTable('#myTable3')) {
            var table = $('#myTable3').DataTable();
            table.settings()[0].oLanguage.sEmptyTable = "Belum ada catatan presensi untuk periode dan kategori yang dipilih.";
            table.settings()[0].oLanguage.sZeroRecords = "Tidak ditemukan pegawai yang cocok dengan kata kunci pencarian.";

            table.on('order.dt search.dt draw.dt', function() {
                var pageInfo = table.page.info();
                table.column(0, { search: 'applied', order: 'applied', page: 'current' }).nodes().each(function(cell, i) {
                    cell.innerHTML = pageInfo.start + i + 1;
                });
            }).draw();
        }
    });
</script>
@endsection
