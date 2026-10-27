@extends('layouts.appui')

@section('title')
    <title>Monitoring & Rekap Kehadiran - {{ $kegiatan->nama_kegiatan }}</title>
@endsection

@section('content')
<style>
    .stat-badge-card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        transition: transform 0.2s ease;
    }
    .stat-badge-card:hover {
        transform: translateY(-2px);
    }
    .badge-status-hadir { background-color: #dcfce7; color: #166534; font-weight: 600; }
    .badge-status-izin { background-color: #e0f2fe; color: #075985; font-weight: 600; }
    .badge-status-sakit { background-color: #fef3c7; color: #92400e; font-weight: 600; }
    .badge-status-alpa { background-color: #fee2e2; color: #991b1b; font-weight: 600; }
</style>

<div class="app-main__inner">
    {{-- Header --}}
    <div class="app-page-title mb-3">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon bg-warning text-white">
                    <i class="pe-7s-display1 icon-gradient bg-warm-flame"></i>
                </div>
                <div>
                    <strong>{{ $kegiatan->nama_kegiatan }}</strong>
                    <div class="page-title-subheading">
                        <i class="fa fa-calendar-day text-danger mr-1"></i> {{ $kegiatan->tanggal->isoFormat('dddd, D MMMM Y') }}
                        @if($kegiatan->pengisi)
                            | <i class="fa fa-user-tie text-primary mr-1"></i> Pengisi: {{ $kegiatan->pengisi }}
                        @endif
                        @if($kegiatan->lokasi)
                            | <i class="fa fa-map-marker-alt text-success mr-1"></i> {{ $kegiatan->lokasi }}
                        @endif
                    </div>
                </div>
            </div>
            <div class="page-title-actions d-flex flex-wrap gap-2">
                <a href="{{ route('kegiatan.qr', $kegiatan->id) }}" target="_blank" class="btn btn-warning text-dark font-weight-bold mr-1">
                    <i class="fa fa-qrcode mr-1"></i> Layar QR Proyektor
                </a>
                <button type="button" class="btn btn-success font-weight-bold mr-1" data-toggle="modal" data-target="#scannerModal">
                    <i class="fa fa-camera mr-1"></i> Scan Kamera
                </button>
                <button type="button" class="btn btn-primary font-weight-bold mr-1" data-toggle="modal" data-target="#manualModal">
                    <i class="fa fa-user-plus mr-1"></i> Presensi Manual
                </button>
                <form action="{{ route('kegiatan.toggle-status', $kegiatan->id) }}" method="POST" class="d-inline mr-1">
                    @csrf
                    @if($kegiatan->is_active)
                        <button type="submit" class="btn btn-outline-danger" title="Tutup Gerbang Presensi">
                            <i class="fa fa-door-closed mr-1"></i> Tutup Gerbang
                        </button>
                    @else
                        <button type="submit" class="btn btn-outline-success" title="Buka Gerbang Presensi">
                            <i class="fa fa-door-open mr-1"></i> Buka Gerbang
                        </button>
                    @endif
                </form>
                <a href="{{ route('kegiatan.index') }}" class="btn btn-outline-secondary">
                    <i class="fa fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa fa-exclamation-circle mr-1"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Summary Cards --}}
    <div class="row mb-4">
        <div class="col-md-3 mb-3 mb-md-0">
            <div class="card stat-badge-card bg-success text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-white-50 font-weight-bold text-uppercase">Total Hadir</div>
                            <div class="h3 font-weight-bold mb-0">{{ $statistik['total_hadir'] }}</div>
                        </div>
                        <i class="fa fa-user-check fa-2x opacity-7"></i>
                    </div>
                    <div class="small mt-2 text-white-50">
                        {{ $statistik['persentase_hadir'] }}% dari {{ $statistik['total_karyawan'] }} Karyawan
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3 mb-md-0">
            <div class="card stat-badge-card bg-info text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-white-50 font-weight-bold text-uppercase">Izin / Sakit</div>
                            <div class="h3 font-weight-bold mb-0">{{ $statistik['total_izin'] + $statistik['total_sakit'] }}</div>
                        </div>
                        <i class="fa fa-envelope-open-text fa-2x opacity-7"></i>
                    </div>
                    <div class="small mt-2 text-white-50">
                        {{ $statistik['total_izin'] }} Izin & {{ $statistik['total_sakit'] }} Sakit
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3 mb-md-0">
            <div class="card stat-badge-card bg-dark text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-white-50 font-weight-bold text-uppercase">Tercatat Hadir/Izin</div>
                            <div class="h3 font-weight-bold mb-0">{{ $statistik['total_tercatat'] }}</div>
                        </div>
                        <i class="fa fa-clipboard-check fa-2x opacity-7"></i>
                    </div>
                    <div class="small mt-2 text-white-50">
                        Dari {{ $statistik['total_karyawan'] }} Total Pegawai
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3 mb-md-0">
            <div class="card stat-badge-card bg-light border">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-muted font-weight-bold text-uppercase">Status Gerbang</div>
                            <div class="h5 font-weight-bold mb-0 mt-1 {{ $kegiatan->is_active ? 'text-success' : 'text-danger' }}">
                                <i class="fa {{ $kegiatan->is_active ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }} mr-1"></i>
                                {{ $kegiatan->is_active ? 'Gerbang Dibuka' : 'Gerbang Ditutup' }}
                            </div>
                        </div>
                        <form action="{{ route('kegiatan.regenerate-qr', $kegiatan->id) }}" method="POST" onsubmit="return confirm('Regenerasi QR token akan membatalkan QR Code lama. Lanjutkan?');">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-warning" title="Refresh QR Token">
                                <i class="fa fa-sync-alt"></i>
                            </button>
                        </form>
                    </div>
                    <div class="small mt-2 text-muted">
                        Token: <code class="text-primary">{{ substr($kegiatan->qr_token, 0, 10) }}...</code>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Detail Kehadiran Peserta Table --}}
    <div class="main-card card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-dark">
                <i class="fa fa-list-check text-primary mr-1"></i> Log Kehadiran Peserta Kegiatan (Real-time)
            </h6>
            <span class="badge badge-pill badge-primary">
                {{ $kehadiran_list->count() }} Orang
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="thead-light">
                    <tr>
                        <th class="text-center" width="50">#</th>
                        <th>NIPY</th>
                        <th>Nama Karyawan</th>
                        <th>Unit / Bagian</th>
                        <th>Waktu Scan / Masuk</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Metode</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody id="attendance-table-body">
                    @forelse($kehadiran_list as $index => $item)
                        <tr>
                            <td class="text-center font-weight-bold text-muted">{{ $index + 1 }}</td>
                            <td><code class="text-dark">{{ $item->karyawan_nipy }}</code></td>
                            <td>
                                <strong>{{ $item->karyawan->nama_lengkap ?? '-' }}</strong>
                                @if(!empty($item->karyawan->gelar))
                                    , {{ $item->karyawan->gelar }}
                                @endif
                                <div class="small text-muted">{{ $item->karyawan->jabatan ?? '-' }}</div>
                            </td>
                            <td>
                                <span class="badge badge-light border">
                                    {{ $item->karyawan->unit ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <i class="fa fa-clock text-muted mr-1"></i>
                                {{ $item->waktu_scan->format('H:i:s') }}
                                <div class="small text-muted">{{ $item->waktu_scan->format('d/m/Y') }}</div>
                            </td>
                            <td class="text-center">
                                @php
                                    $badgeClass = match($item->status) {
                                        'hadir' => 'badge-status-hadir',
                                        'izin'  => 'badge-status-izin',
                                        'sakit' => 'badge-status-sakit',
                                        default => 'badge-status-alpa',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }} px-2 py-1 text-uppercase">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-light border">
                                    {{ $item->metode_presensi == 'qr_scan' ? 'QR Code' : 'Manual' }}
                                </span>
                            </td>
                            <td class="small text-muted">{{ $item->catatan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="fa fa-users-slash fa-3x mb-2 text-secondary opacity-5"></i>
                                <p class="mb-0">Belum ada kehadiran yang tercatat untuk kegiatan ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL SCANNER KAMERA LIVE --}}
<div class="modal fade" id="scannerModal" tabindex="-1" role="dialog" aria-labelledby="scannerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 14px;">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="scannerModalLabel">
                    <i class="fa fa-camera mr-1"></i> Pemindai QR Presensi
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" onclick="stopScanner()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center p-4">
                <div id="reader" style="width: 100%; min-height: 280px; border-radius: 8px; overflow: hidden; background: #000;"></div>
                <div id="scanner-status" class="mt-3 alert alert-info py-2 small">
                    Arahkan kamera ke QR Code peserta atau kegiatan.
                </div>
                <div class="form-group text-left mt-2">
                    <label class="small font-weight-bold">Input NIPY Manual / Barcode Scanner USB</label>
                    <div class="input-group">
                        <input type="text" id="manual_nipy_input" class="form-control" placeholder="Scan barcode kartu atau ketik NIPY..." autofocus>
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button" onclick="submitNipyScan()">
                                <i class="fa fa-check"></i> Submit
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" onclick="stopScanner()">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL INPUT MANUAL --}}
<div class="modal fade" id="manualModal" tabindex="-1" role="dialog" aria-labelledby="manualModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 14px;">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="manualModalLabel">
                    <i class="fa fa-user-plus mr-1"></i> Catat Kehadiran Manual
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('kegiatan.store-manual', $kegiatan->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark">Pilih Karyawan <span class="text-danger">*</span></label>
                        <select name="karyawan_nipy[]" class="form-control" multiple size="7" required>
                            @foreach($karyawanList as $kar)
                                <option value="{{ $kar->nipy }}">
                                    {{ $kar->nipy }} - {{ $kar->nama_lengkap }} ({{ $kar->unit ?? 'Umum' }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Gunakan Ctrl/Cmd + Klik untuk memilih banyak karyawan sekaligus.</small>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark">Status Kehadiran <span class="text-danger">*</span></label>
                        <select name="status" class="form-control" required>
                            <option value="hadir" selected>Hadir</option>
                            <option value="izin">Izin</option>
                            <option value="sakit">Sakit</option>
                            <option value="alpa">Alpa</option>
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-dark">Catatan (Opsional)</label>
                        <input type="text" name="catatan" class="form-control" placeholder="Contoh: Dispensasi tugas luar / hadir terlambat">
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary font-weight-bold">Simpan Kehadiran</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- QR Code Reader Library (HTML5-QRCode via CDN) --}}
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    let html5QrCode = null;
    const qrToken = "{{ $kegiatan->qr_token }}";

    $('#scannerModal').on('shown.bs.modal', function () {
        startScanner();
        $('#manual_nipy_input').focus();
    });

    $('#scannerModal').on('hidden.bs.modal', function () {
        stopScanner();
    });

    function startScanner() {
        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode("reader");
        }
        const config = { fps: 10, qrbox: { width: 250, height: 250 } };
        html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess)
            .catch(err => {
                $('#scanner-status').removeClass('alert-info').addClass('alert-warning')
                    .html('Kamera tidak dapat diakses atau tidak ditemukan. Gunakan input manual di bawah.');
            });
    }

    function stopScanner() {
        if (html5QrCode && html5QrCode.isScanning) {
            html5QrCode.stop().catch(err => console.error(err));
        }
    }

    function onScanSuccess(decodedText) {
        // Hentikan sejenak scanner agar tidak memicu scan berulang
        if (html5QrCode) {
            html5QrCode.pause();
        }

        // decodedText bisa berupa NIPY karyawan atau format JSON
        processScan(decodedText);
    }

    function submitNipyScan() {
        const val = $('#manual_nipy_input').val().trim();
        if (val) {
            processScan(val);
            $('#manual_nipy_input').val('');
        }
    }

    $('#manual_nipy_input').on('keypress', function(e) {
        if (e.which === 13) {
            submitNipyScan();
        }
    });

    function processScan(scannedNipy) {
        $('#scanner-status').removeClass('alert-danger alert-success alert-warning')
            .addClass('alert-info').html('<i class="fa fa-spinner fa-spin mr-1"></i> Memproses presensi...');

        fetch("{{ route('kegiatan.scan') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                qr_token: qrToken,
                karyawan_nipy: scannedNipy
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                $('#scanner-status').removeClass('alert-info').addClass('alert-success')
                    .html('<i class="fa fa-check-circle mr-1"></i> ' + data.message);
                
                // Refresh halaman setelah jeda 1 detik agar data tabel update
                setTimeout(() => {
                    location.reload();
                }, 1200);
            } else {
                $('#scanner-status').removeClass('alert-info').addClass('alert-danger')
                    .html('<i class="fa fa-exclamation-circle mr-1"></i> ' + data.message);
                if (html5QrCode) {
                    setTimeout(() => html5QrCode.resume(), 1500);
                }
            }
        })
        .catch(err => {
            $('#scanner-status').removeClass('alert-info').addClass('alert-danger')
                .html('Terjadi kesalahan koneksi.');
            if (html5QrCode) {
                setTimeout(() => html5QrCode.resume(), 1500);
            }
        });
    }
</script>
@endsection
