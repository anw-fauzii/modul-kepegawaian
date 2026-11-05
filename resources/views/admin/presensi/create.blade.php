@extends('layouts.appui')

@section('title')
    <title>Input Presensi Massal - SIPIT</title>
@endsection

@section('content')
<style>
    .filter-card {
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04), 0 2px 4px -2px rgba(0, 0, 0, 0.03);
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
    .quick-pill {
        display: inline-flex;
        align-items: center;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        background-color: #f8fafc;
        color: #475569;
        border: 1px solid #e2e8f0;
        text-decoration: none;
        transition: all 0.2s ease;
        margin-right: 6px;
        margin-bottom: 6px;
    }
    .quick-pill:hover,
    .quick-pill.active {
        background-color: #ea580c;
        color: #ffffff;
        border-color: #ea580c;
        text-decoration: none;
        box-shadow: 0 2px 6px rgba(234, 88, 12, 0.25);
    }

    /* Modern Bulk Table */
    .bulk-table {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
    }
    .bulk-table thead th {
        background-color: #f8fafc;
        color: #475569;
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 12px 14px;
        border-bottom: 2px solid #e2e8f0;
        border-top: none;
        white-space: nowrap;
        vertical-align: middle;
    }
    .bulk-table tbody td {
        padding: 10px 14px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13px;
        color: #1e293b;
    }
    .bulk-table tbody tr {
        transition: background-color 0.15s ease;
    }
    .bulk-table tbody tr:hover {
        background-color: #fff7ed;
    }
    .bulk-table tbody tr.row-filled {
        background-color: #f8fafc;
    }
    .bulk-table tbody tr.row-filled:hover {
        background-color: #fff7ed;
    }

    /* Inputs inside Bulk Table */
    .bulk-input {
        border-radius: 7px;
        border: 1px solid #cbd5e1;
        font-size: 12.5px;
        height: 34px;
        color: #0f172a;
        transition: all 0.15s ease;
    }
    .bulk-input:focus {
        border-color: #ea580c;
        box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.15);
    }
    .status-select.has-value {
        border-color: #ea580c;
        font-weight: 600;
        background-color: #fff7ed;
        color: #9a3412;
    }

    /* Check Pill Label */
    .option-check-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 8px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        font-size: 11px;
        font-weight: 600;
        color: #475569;
        cursor: pointer;
        user-select: none;
        margin: 0;
        transition: all 0.15s ease;
    }
    .option-check-pill:hover {
        border-color: #cbd5e1;
        background: #f1f5f9;
    }
    .option-check-pill input[type="checkbox"] {
        cursor: pointer;
        margin: 0;
    }
    .option-check-pill.pill-shaum:has(input:checked) {
        background: #ecfdf5;
        border-color: #a7f3d0;
        color: #065f46;
    }
    .option-check-pill.pill-izin:has(input:checked) {
        background: #f0f9ff;
        border-color: #bae6fd;
        color: #0369a1;
    }

    /* Guidance Box */
    .guidance-box {
        border-radius: 16px;
        border: 1.5px dashed #cbd5e1;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        padding: 42px 24px;
        text-align: center;
    }
    .guidance-icon-circle {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: #fff7ed;
        border: 2px solid #ffedd5;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
        color: #ea580c;
        font-size: 32px;
        box-shadow: 0 6px 16px rgba(234, 88, 12, 0.12);
    }
</style>

@php
    $isFriday = \Carbon\Carbon::parse($selectedTanggal)->isFriday();
    $savedCount = $karyawanList->filter(fn ($k) => $k->presensi->isNotEmpty())->count();
@endphp

<div class="app-main__inner">
    <!-- PAGE TITLE & ACTIONS -->
    <div class="app-page-title mb-3">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon text-white shadow-sm"
                    style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 12px;">
                    <i class="pe-7s-date" style="color: white;"></i>
                </div>
                <div>
                    Input Presensi Massal (Bulk)
                    <div class="page-title-subheading">
                        Catat kehadiran seluruh pegawai per kategori kerja sekaligus. Baris yang dikosongkan tidak akan disimpan.
                    </div>
                </div>
            </div>

            <div class="page-title-actions">
                <a href="{{ route('presensi.index') }}" class="btn btn-light border shadow-sm font-weight-bold"
                    style="border-radius: 8px;">
                    <i class="fa fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- FILTER & SELEKSI KATEGORI CARD -->
    <div class="filter-card card mb-3">
        <div class="card-body p-3">
            <form action="{{ route('presensi.create') }}" method="GET" id="filterCategoryForm">
                <div class="row align-items-end">
                    <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                        <label class="form-filter-label">
                            <i class="fa fa-calendar-day mr-1 text-warning"></i> Tanggal Presensi <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="tanggal" class="form-control form-filter-control"
                            value="{{ $selectedTanggal }}" required onchange="document.getElementById('filterCategoryForm').submit();">
                    </div>

                    <div class="col-md-4 col-sm-6 mb-2 mb-md-0">
                        <label class="form-filter-label d-flex justify-content-between">
                            <span><i class="fa fa-calendar-alt mr-1 text-info"></i> Periode Bulan</span>
                            <span class="text-warning" style="text-transform: none;"><i class="fa fa-info-circle"></i> Cut-off tgl 28</span>
                        </label>
                        <select name="kepegawaian_master_bulan_id" class="form-control form-filter-control" onchange="document.getElementById('filterCategoryForm').submit();">
                            <option value="">-- Otomatis (Cut-off 28) --</option>
                            @foreach ($bulanList as $b)
                                <option value="{{ $b->id }}" {{ $selectedBulanId == $b->id ? 'selected' : '' }}>
                                    {{ $b->nama_bulan }} {{ $b->tahunAjaran ? '(' . $b->tahunAjaran->nama_tahun_ajaran . ')' : '' }} {{ $b->status ? ' [Aktif]' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-5 col-sm-12">
                        <label class="form-filter-label">
                            <i class="fa fa-layer-group mr-1 text-primary"></i> Kategori Pegawai <span class="text-danger">*</span>
                        </label>
                        <select name="kepegawaian_master_kategori_id" class="form-control form-filter-control" required onchange="document.getElementById('filterCategoryForm').submit();">
                            <option value="">-- Pilih Kategori Pegawai --</option>
                            @foreach ($kategoriList as $kat)
                                <option value="{{ $kat->id }}" {{ $selectedKategoriId == $kat->id ? 'selected' : '' }}>
                                    {{ $kat->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- PINTASAN KATEGORI CEPAT -->
                @if ($kategoriList->isNotEmpty())
                    <div class="d-flex align-items-center flex-wrap pt-3 mt-3 border-top">
                        <span class="small font-weight-bold text-muted mr-2 mb-1">
                            <i class="fa fa-bolt text-warning mr-1"></i> Pilih Cepat Kategori:
                        </span>
                        @foreach ($kategoriList as $kat)
                            <a href="{{ route('presensi.create', ['kepegawaian_master_kategori_id' => $kat->id, 'tanggal' => $selectedTanggal, 'kepegawaian_master_bulan_id' => $selectedBulanId]) }}"
                                class="quick-pill {{ (int) $selectedKategoriId === (int) $kat->id ? 'active' : '' }}">
                                {{ $kat->nama_kategori }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </form>
        </div>
    </div>

    @if ($selectedKategoriId)
        <!-- BULK ATTENDANCE TABLE CARD -->
        <div class="main-card card mb-4 shadow-sm border-0" style="border-radius: 14px;">
            <div class="card-body p-3">
                <!-- TOP TOOLBAR -->
                <div class="d-flex flex-wrap justify-content-between align-items-center pb-3 mb-3 border-bottom" style="gap: 10px;">
                    <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                        <span class="badge px-3 py-1.5 text-white font-weight-bold"
                            style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%); border-radius: 8px; font-size: 12.5px;">
                            <i class="fa fa-layer-group mr-1"></i> {{ $kategoriList->firstWhere('id', $selectedKategoriId)?->nama_kategori }}
                        </span>
                        <span class="badge badge-light border text-dark px-2.5 py-1 font-weight-bold" style="font-size: 12px;">
                            <i class="fa fa-calendar-day text-warning mr-1"></i> {{ \Carbon\Carbon::parse($selectedTanggal)->translatedFormat('l, d M Y') }}
                        </span>
                        <span class="badge badge-light border text-muted px-2.5 py-1 font-weight-bold" style="font-size: 12px;">
                            Total: <strong class="text-dark">{{ count($karyawanList) }}</strong> Pegawai
                            @if ($savedCount > 0)
                                &bull; <span class="text-success">{{ $savedCount }} Tersimpan</span>
                            @endif
                        </span>
                    </div>

                    <div class="d-flex align-items-center flex-wrap" style="gap: 6px;">
                        <div class=" position-relative mr-1" style="min-width: 210px;">
                            <input type="text" id="bulkSearchInput" class="form-control bulk-input"
                                placeholder="Cari nama atau NIPY..." onkeyup="filterBulkRows()">
                        </div>

                        <button type="button" class="btn btn-sm btn-outline-success font-weight-bold" style="border-radius: 7px;" onclick="setAllHadir()">
                            <i class="fa fa-check-double mr-1"></i> Set Semua Hadir
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-info font-weight-bold" style="border-radius: 7px;" onclick="setAllLibur()">
                            <i class="fa fa-calendar-times mr-1"></i> Set Semua Libur
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary font-weight-bold" style="border-radius: 7px;" onclick="resetAll()">
                            <i class="fa fa-undo mr-1"></i> Kosongkan
                        </button>
                    </div>
                </div>

                <form action="{{ route('presensi.store-bulk') }}" method="POST">
                    @csrf
                    <input type="hidden" name="tanggal" value="{{ $selectedTanggal }}">
                    <input type="hidden" name="kepegawaian_master_kategori_id" value="{{ $selectedKategoriId }}">
                    <input type="hidden" name="kepegawaian_master_bulan_id" value="{{ $selectedBulanId }}">

                    <div class="table-responsive">
                        <table class="bulk-table mb-0" id="bulkPresensiTable">
                            <thead>
                                <tr>
                                    <th style="width: 42px; text-align: center;">No</th>
                                    <th>Nama Lengkap</th>
                                    <th style="width: 175px;">Status Kehadiran</th>
                                    <th style="width: 205px; text-align: center;">Jam Kerja (Masuk &ndash; Pulang)</th>
                                    <th style="width: 235px;">Shaum & Dispensasi</th>
                                    <th style="width: 190px;">Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($karyawanList as $index => $karyawan)
                                    @php
                                        $existing = $karyawan->presensi->first();
                                        $defaultJamMasuk = $existing && $existing->jam_masuk ? substr($existing->jam_masuk, 0, 5) : '';
                                        $defaultJamPulang = $existing && $existing->jam_pulang ? substr($existing->jam_pulang, 0, 5) : '';
                                        $defaultStatus = $existing ? $existing->status : '';
                                        $jadwalMasuk = $karyawan->jamKerja ? substr($karyawan->jamKerja->jam_masuk, 0, 5) : '07:15';
                                        $jadwalPulangRaw = $karyawan->jamKerja
                                            ? ($isFriday ? $karyawan->jamKerja->jam_pulang_jumat : $karyawan->jamKerja->jam_pulang_senin_kamis)
                                            : null;
                                        $jadwalPulang = $jadwalPulangRaw ? substr($jadwalPulangRaw, 0, 5) : '15:30';
                                    @endphp
                                    <tr class="bulk-row {{ $defaultStatus !== '' ? 'row-filled' : '' }}"
                                        data-name="{{ strtolower($karyawan->nama_lengkap) }}"
                                        data-nipy="{{ strtolower($karyawan->nipy) }}">
                                        <td class="text-center font-weight-bold text-muted">{{ $index + 1 }}</td>

                                        <!-- Nama Lengkap + Foto + NIPY & Jadwal -->
                                        <td>
                                            <input type="hidden" name="items[{{ $index }}][karyawan_nipy]" value="{{ $karyawan->nipy }}">
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
                                                    <div class="d-flex align-items-center flex-wrap" style="gap: 5px;">
                                                        <span class="font-weight-bold text-dark text-truncate" style="font-size: 0.88rem; max-width: 220px;"
                                                            title="{{ $karyawan->nama_lengkap }}{{ $karyawan->gelar ? ', ' . $karyawan->gelar : '' }}">
                                                            {{ $karyawan->nama_lengkap }}{{ $karyawan->gelar ? ', ' . $karyawan->gelar : '' }}
                                                        </span>
                                                        @if ($existing)
                                                            <span class="badge px-1.5 py-0 font-weight-bold"
                                                                style="background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; font-size: 9.5px; border-radius: 4px;">
                                                                <i class="fa fa-check"></i> Tersimpan
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <small class="text-muted d-block text-truncate" style="max-width: 240px;">
                                                        {{ $karyawan->nipy }}
                                                        @if ($karyawan->jamKerja)
                                                            &nbsp;|&nbsp; <i class="fa fa-clock text-warning"></i> {{ $jadwalMasuk }}&ndash;{{ $jadwalPulang }}
                                                        @endif
                                                    </small>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Status Kehadiran -->
                                        <td>
                                            <select name="items[{{ $index }}][status]"
                                                class="form-control bulk-input status-select {{ $defaultStatus !== '' ? 'has-value' : '' }}"
                                                data-index="{{ $index }}"
                                                data-jadwal-masuk="{{ $jadwalMasuk }}"
                                                data-jadwal-pulang="{{ $jadwalPulang }}"
                                                onchange="handleStatusChange(this)">
                                                <option value="" {{ $defaultStatus == '' ? 'selected' : '' }}>-- Belum Diisi --</option>
                                                <option value="hadir" {{ $defaultStatus == 'hadir' ? 'selected' : '' }}>Hadir</option>
                                                <option value="terlambat" {{ $defaultStatus == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                                                <option value="tugas" {{ $defaultStatus == 'tugas' ? 'selected' : '' }}>Tugas / Dinas</option>
                                                <option value="izin" {{ $defaultStatus == 'izin' ? 'selected' : '' }}>Izin</option>
                                                <option value="sakit" {{ $defaultStatus == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                                <option value="cuti" {{ $defaultStatus == 'cuti' ? 'selected' : '' }}>Cuti</option>
                                                <option value="alpa" {{ $defaultStatus == 'alpa' ? 'selected' : '' }}>Alpa</option>
                                                <option value="libur" {{ $defaultStatus == 'libur' ? 'selected' : '' }}>Libur</option>
                                            </select>
                                        </td>

                                        <!-- Jam Kerja (Masuk - Pulang) -->
                                        <td>
                                            <div class="d-flex align-items-center justify-content-center" style="gap: 5px;">
                                                <input type="text" name="items[{{ $index }}][jam_masuk]"
                                                    id="jam_masuk_{{ $index }}"
                                                    placeholder="07:15"
                                                    maxlength="5"
                                                    class="form-control bulk-input text-center jam-masuk-input"
                                                    style="width: 78px;"
                                                    value="{{ $defaultJamMasuk }}">
                                                <span class="text-muted font-weight-bold">&ndash;</span>
                                                <input type="text" name="items[{{ $index }}][jam_pulang]"
                                                    id="jam_pulang_{{ $index }}"
                                                    placeholder="15:30"
                                                    maxlength="5"
                                                    class="form-control bulk-input text-center jam-pulang-input"
                                                    style="width: 78px;"
                                                    value="{{ $defaultJamPulang }}">
                                            </div>
                                        </td>

                                        <!-- Shaum & Dispensasi -->
                                        <td>
                                            <div class="d-flex align-items-center flex-wrap" style="gap: 5px;">
                                                <label class="option-check-pill pill-shaum" for="shaum_{{ $index }}" title="Tandai sedang Shaum / Puasa">
                                                    <input type="checkbox" class="shaum-checkbox"
                                                        id="shaum_{{ $index }}"
                                                        name="items[{{ $index }}][shaum]" value="1"
                                                        {{ ($existing && $existing->shaum) ? 'checked' : '' }}>
                                                    <span><i class="fa fa-moon text-success"></i> Shaum</span>
                                                </label>

                                                <label class="option-check-pill pill-izin" for="izin_terlambat_{{ $index }}" title="Dispensasi / Izin Terlambat">
                                                    <input type="checkbox"
                                                        id="izin_terlambat_{{ $index }}"
                                                        name="items[{{ $index }}][izin_terlambat]" value="1"
                                                        {{ ($existing && $existing->izin_terlambat) ? 'checked' : '' }}>
                                                    <span>Izin Telat</span>
                                                </label>

                                                <label class="option-check-pill pill-izin" for="izin_pulang_cepat_{{ $index }}" title="Dispensasi / Izin Pulang Cepat">
                                                    <input type="checkbox"
                                                        id="izin_pulang_cepat_{{ $index }}"
                                                        name="items[{{ $index }}][izin_pulang_cepat]" value="1"
                                                        {{ ($existing && $existing->izin_pulang_cepat) ? 'checked' : '' }}>
                                                    <span>Izin Plg Cepat</span>
                                                </label>
                                            </div>
                                        </td>

                                        <!-- Catatan -->
                                        <td>
                                            <input type="text" name="items[{{ $index }}][catatan]"
                                                placeholder="Catatan opsional..."
                                                class="form-control bulk-input"
                                                value="{{ $existing ? $existing->catatan : '' }}">
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-5">
                                            <i class="fa fa-users fa-2x text-secondary opacity-5 mb-2 d-block"></i>
                                            <div class="font-weight-bold text-dark">Tidak Ada Pegawai</div>
                                            <div class="small text-muted">Belum ada pegawai yang terdaftar pada kategori ini.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if (count($karyawanList) > 0)
                        <div class="d-flex flex-wrap justify-content-between align-items-center mt-3 pt-3 border-top">
                            <div class="small text-muted mb-2 mb-md-0">
                                <i class="fa fa-info-circle text-warning mr-1"></i>
                                Pegawai dengan status <strong>-- Belum Diisi --</strong> dan jam masuk kosong akan dilewati otomatis.
                            </div>
                            <button type="submit" class="btn text-white px-4 py-2 font-weight-bold shadow-sm"
                                style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 8px;">
                                <i class="fa fa-save mr-1"></i> Simpan Presensi Kategori Ini
                            </button>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    @else
        <!-- GUIDANCE STATE SEBELUM KATEGORI DIPILIH -->
        <div class="guidance-box mb-4">
            <div class="guidance-icon-circle">
                <i class="pe-7s-users"></i>
            </div>
            <h5 class="font-weight-bold text-dark mb-2">Pilih Kategori Pegawai Terlebih Dahulu</h5>
            <p class="text-muted mx-auto mb-3" style="max-width: 560px; font-size: 13.5px;">
                Silakan pilih kategori pegawai pada filter di atas atau klik salah satu tombol kategori di bawah ini untuk menampilkan daftar pegawai secara massal.
            </p>
            <div class="d-flex justify-content-center flex-wrap" style="gap: 6px;">
                @foreach ($kategoriList as $kat)
                    <a href="{{ route('presensi.create', ['kepegawaian_master_kategori_id' => $kat->id, 'tanggal' => $selectedTanggal, 'kepegawaian_master_bulan_id' => $selectedBulanId]) }}"
                        class="quick-pill">
                        <i class="fa fa-layer-group mr-1 text-warning"></i> {{ $kat->nama_kategori }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

@section('js')
<script>
    function handleStatusChange(select) {
        var index = select.getAttribute('data-index');
        var row = select.closest('tr');
        var jamMasuk = document.getElementById('jam_masuk_' + index);
        var jamPulang = document.getElementById('jam_pulang_' + index);

        if (select.value !== '') {
            select.classList.add('has-value');
            if (row) row.classList.add('row-filled');
        } else {
            select.classList.remove('has-value');
            if (row) row.classList.remove('row-filled');
        }

        if (select.value === 'hadir') {
            if (jamMasuk && !jamMasuk.value) {
                jamMasuk.value = select.getAttribute('data-jadwal-masuk') || '07:15';
            }
        } else if (['libur', 'izin', 'sakit', 'cuti', 'alpa'].indexOf(select.value) !== -1) {
            if (jamMasuk) jamMasuk.value = '';
            if (jamPulang) jamPulang.value = '';
        }
    }

    function setAllHadir() {
        document.querySelectorAll('.status-select').forEach(function(select) {
            var row = select.closest('tr');
            if (row && row.style.display === 'none') return;
            select.value = 'hadir';
            handleStatusChange(select);
        });
    }

    function setAllLibur() {
        document.querySelectorAll('.status-select').forEach(function(select) {
            var row = select.closest('tr');
            if (row && row.style.display === 'none') return;
            select.value = 'libur';
            handleStatusChange(select);
        });
    }

    function resetAll() {
        document.querySelectorAll('.status-select').forEach(function(select) {
            var row = select.closest('tr');
            if (row && row.style.display === 'none') return;
            select.value = '';
            handleStatusChange(select);
            var index = select.getAttribute('data-index');
            var jamMasuk = document.getElementById('jam_masuk_' + index);
            var jamPulang = document.getElementById('jam_pulang_' + index);
            if (jamMasuk) jamMasuk.value = '';
            if (jamPulang) jamPulang.value = '';
        });
    }

    function filterBulkRows() {
        var input = document.getElementById('bulkSearchInput');
        var filter = (input ? input.value : '').toLowerCase().trim();
        document.querySelectorAll('.bulk-row').forEach(function(row) {
            var name = row.getAttribute('data-name') || '';
            var nipy = row.getAttribute('data-nipy') || '';
            if (name.indexOf(filter) > -1 || nipy.indexOf(filter) > -1) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
</script>
@endsection
