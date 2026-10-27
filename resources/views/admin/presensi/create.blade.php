@extends('layouts.appui')

@section('title')
    <title>Input Presensi Massal (Bulk by Kategori)</title>
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
                        Input Presensi Massal (Bulk by Kategori)
                        <div class="page-title-subheading">
                            Input presensi seluruh pegawai dalam satu kategori sekaligus. Pegawai yang belum hadir dapat dikosongkan.
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

        <!-- FILTER & SELEKSI KATEGORI CARD -->
        <div class="main-card card mb-3 shadow-sm border-0">
            <div class="card-body">
                <form action="{{ route('presensi.create') }}" method="GET" id="filterCategoryForm">
                    <div class="row align-items-end">
                        <div class="col-md-3 mb-2">
                            <label class="form-label font-weight-bold">Tanggal Presensi <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control"
                                value="{{ $selectedTanggal }}" required onchange="document.getElementById('filterCategoryForm').submit();">
                        </div>

                        <div class="col-md-3 mb-2">
                            <label class="form-label font-weight-bold">Periode Bulan</label>
                            <select name="kepegawaian_master_bulan_id" class="form-control" onchange="document.getElementById('filterCategoryForm').submit();">
                                <option value="">-- Otomatis (Cut-off 28) --</option>
                                @foreach ($bulanList as $b)
                                    <option value="{{ $b->id }}" {{ $selectedBulanId == $b->id ? 'selected' : '' }}>
                                        {{ $b->nama_bulan }} {{ $b->tahunAjaran ? '(' . $b->tahunAjaran->nama_tahun_ajaran . ')' : '' }} {{ $b->status ? ' [Aktif]' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="form-label font-weight-bold">Pilih Kategori Karyawan <span class="text-danger">*</span></label>
                            <select name="kepegawaian_master_kategori_id" class="form-control" required onchange="document.getElementById('filterCategoryForm').submit();">
                                <option value="">-- Pilih Kategori (Contoh: SD, SMP, dll) --</option>
                                @foreach ($kategoriList as $kat)
                                    <option value="{{ $kat->id }}" {{ $selectedKategoriId == $kat->id ? 'selected' : '' }}>
                                        {{ $kat->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2 mb-2">
                            <button type="submit" class="btn btn-primary btn-block shadow-sm" style="border-radius: 6px;">
                                <i class="fa fa-users mr-1"></i> Muat Data
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if ($selectedKategoriId)
            <!-- BULK ATTENDANCE TABLE FORM -->
            <div class="main-card card mb-4 shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fa fa-list-alt mr-2 text-primary"></i>
                        Daftar Karyawan: <strong>{{ $kategoriList->firstWhere('id', $selectedKategoriId)?->nama_kategori }}</strong>
                        <span class="badge badge-primary ml-2">{{ count($karyawanList) }} Orang</span>
                    </div>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-success" onclick="setAllHadir()">
                            <i class="fa fa-check-double mr-1"></i> Set Semua Hadir
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="resetAll()">
                            <i class="fa fa-undo mr-1"></i> Kosongkan Semua
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="alert alert-info py-2 small mb-3">
                        <i class="fa fa-info-circle mr-1"></i>
                        <strong>Petunjuk:</strong> Pegawai yang belum hadir dapat dibiarkan dengan status <strong>-- Kosong (Belum Hadir) --</strong> dan tidak akan tersimpan. Jam masuk otomatis mendeteksi keterlambatan berdasarkan jam kerja pegawai.
                    </div>

                    <form action="{{ route('presensi.store-bulk') }}" method="POST">
                        @csrf
                        <input type="hidden" name="tanggal" value="{{ $selectedTanggal }}">
                        <input type="hidden" name="kepegawaian_master_kategori_id" value="{{ $selectedKategoriId }}">
                        <input type="hidden" name="kepegawaian_master_bulan_id" value="{{ $selectedBulanId }}">

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle mb-0">
                                <thead class="thead-light">
                                    <tr class="text-center">
                                        <th style="width: 4%;">No</th>
                                        <th style="text-align: left; width: 22%;">Nama & NIPY Pegawai</th>
                                        <th style="width: 15%;">Status Kehadiran</th>
                                        <th style="width: 11%;">Jam Masuk</th>
                                        <th style="width: 11%;">Jam Pulang</th>
                                        <th style="width: 14%;">Dispensasi / Izin</th>
                                        <th>Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($karyawanList as $index => $karyawan)
                                        @php
                                            $existing = $karyawan->presensi->first();
                                            $defaultJamMasuk = $existing ? substr($existing->jam_masuk, 0, 5) : '';
                                            $defaultJamPulang = $existing ? substr($existing->jam_pulang, 0, 5) : '';
                                            $defaultStatus = $existing ? $existing->status : '';
                                            $jadwalMasuk = $karyawan->jamKerja ? substr($karyawan->jamKerja->jam_masuk, 0, 5) : '07:15';
                                        @endphp
                                        <tr>
                                            <td class="text-center font-weight-bold">{{ $index + 1 }}</td>
                                            <td>
                                                <input type="hidden" name="items[{{ $index }}][karyawan_nipy]" value="{{ $karyawan->nipy }}">
                                                <div class="font-weight-bold text-dark">{{ $karyawan->nama_lengkap }}{{ $karyawan->gelar ? ', ' . $karyawan->gelar : '' }}</div>
                                                <div class="small text-muted">
                                                    NIPY: {{ $karyawan->nipy }}
                                                    @if ($karyawan->jamKerja)
                                                        &bull; <span class="badge badge-light border">Jadwal: {{ substr($karyawan->jamKerja->jam_masuk, 0, 5) }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <select name="items[{{ $index }}][status]" class="form-control form-control-sm status-select" data-index="{{ $index }}" data-jadwal="{{ $jadwalMasuk }}">
                                                    <option value="" {{ $defaultStatus == '' ? 'selected' : '' }}>-- Kosong (Belum Hadir) --</option>
                                                    <option value="hadir" {{ $defaultStatus == 'hadir' ? 'selected' : '' }}>Hadir</option>
                                                    <option value="terlambat" {{ $defaultStatus == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                                                    <option value="tugas" {{ $defaultStatus == 'tugas' ? 'selected' : '' }}>Tugas</option>
                                                    <option value="izin" {{ $defaultStatus == 'izin' ? 'selected' : '' }}>Izin</option>
                                                    <option value="sakit" {{ $defaultStatus == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                                    <option value="cuti" {{ $defaultStatus == 'cuti' ? 'selected' : '' }}>Cuti</option>
                                                    <option value="alpa" {{ $defaultStatus == 'alpa' ? 'selected' : '' }}>Alpa</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" name="items[{{ $index }}][jam_masuk]"
                                                    id="jam_masuk_{{ $index }}"
                                                    placeholder="JJ:MM"
                                                    class="form-control form-control-sm text-center jam-masuk-input"
                                                    value="{{ $defaultJamMasuk }}">
                                            </td>
                                            <td>
                                                <input type="text" name="items[{{ $index }}][jam_pulang]"
                                                    id="jam_pulang_{{ $index }}"
                                                    placeholder="JJ:MM"
                                                    class="form-control form-control-sm text-center jam-pulang-input"
                                                    value="{{ $defaultJamPulang }}">
                                            </td>
                                            <td>
                                                <div class="custom-control custom-checkbox small">
                                                    <input type="checkbox" class="custom-control-input"
                                                        id="izin_terlambat_{{ $index }}"
                                                        name="items[{{ $index }}][izin_terlambat]" value="1"
                                                        {{ ($existing && $existing->izin_terlambat) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="izin_terlambat_{{ $index }}">
                                                        Izin Terlambat
                                                    </label>
                                                </div>
                                                <div class="custom-control custom-checkbox small mt-1">
                                                    <input type="checkbox" class="custom-control-input"
                                                        id="izin_pulang_cepat_{{ $index }}"
                                                        name="items[{{ $index }}][izin_pulang_cepat]" value="1"
                                                        {{ ($existing && $existing->izin_pulang_cepat) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="izin_pulang_cepat_{{ $index }}">
                                                        Izin Pulang Cepat
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" name="items[{{ $index }}][catatan]"
                                                    placeholder="Keterangan..."
                                                    class="form-control form-control-sm"
                                                    value="{{ $existing ? $existing->catatan : '' }}">
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                <i class="fa fa-info-circle mr-1"></i> Tidak ada pegawai terdaftar pada kategori ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if (count($karyawanList) > 0)
                            <div class="text-right mt-4 pt-3 border-top">
                                <button type="submit" class="btn text-white px-4 shadow-sm"
                                    style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 8px;">
                                    <i class="fa fa-save mr-1"></i> SIMPAN SEMUA PRESENSI KATEGORI INI
                                </button>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        @else
            <div class="alert alert-warning text-center py-4 shadow-sm border-0">
                <i class="fa fa-hand-point-up fa-2x mb-2 text-warning"></i>
                <div class="font-weight-bold">Silakan Pilih Kategori Karyawan Terlebih Dahulu</div>
                <div class="small text-muted">Pilih kategori di atas untuk memuat daftar seluruh pegawai dalam kategori tersebut secara massal.</div>
            </div>
        @endif
    </div>

    <script>
        function setAllHadir() {
            document.querySelectorAll('.status-select').forEach(function(select) {
                select.value = 'hadir';
                var index = select.getAttribute('data-index');
                var defaultJam = select.getAttribute('data-jadwal') || '07:15';
                var jamInput = document.getElementById('jam_masuk_' + index);
                if (jamInput && !jamInput.value) {
                    jamInput.value = defaultJam;
                }
            });
        }

        function resetAll() {
            document.querySelectorAll('.status-select').forEach(function(select) {
                select.value = '';
            });
            document.querySelectorAll('.jam-masuk-input').forEach(function(input) {
                input.value = '';
            });
            document.querySelectorAll('.jam-pulang-input').forEach(function(input) {
                input.value = '';
            });
        }
    </script>
@endsection
