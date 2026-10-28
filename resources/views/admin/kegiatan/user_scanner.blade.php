@extends('layouts.appui')

@section('title')
    <title>Pindai QR Kegiatan - SIPIT Kepegawaian</title>
@endsection

@section('content')
<style>
    .scanner-wrapper {
        max-width: 520px;
        margin: 0 auto;
    }
    .scanner-box {
        position: relative;
        width: 100%;
        min-height: 280px;
        background: #0f172a;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(0,0,0,0.25);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .user-profile-badge {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        padding: 16px 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.03);
    }
    .btn-camera-capture {
        background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%);
        border: none;
        color: #ffffff;
        padding: 14px 20px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 15px;
        box-shadow: 0 4px 12px rgba(234, 88, 12, 0.35);
        transition: all 0.2s ease;
    }
    .btn-camera-capture:hover, .btn-camera-capture:focus {
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(234, 88, 12, 0.45);
    }
</style>

<div class="app-main__inner">
    <div class="scanner-wrapper">
        {{-- Header Profil Karyawan --}}
        <div class="user-profile-badge d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <div class="avatar-icon-wrapper avatar-icon-md mr-3">
                    <div class="avatar-icon rounded-circle bg-warning text-dark font-weight-bold d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fa fa-user"></i>
                    </div>
                </div>
                <div>
                    <h6 class="mb-0 font-weight-bold text-dark">{{ auth()->user()->name ?? '-'}}</h6>
                    <div class="small text-muted">
                        NIPY: <code class="text-primary font-weight-bold">{{ auth()->user()->no_induk ?? '-' }}</code>
                    </div>
                </div>
            </div>
            <a href="{{ route('home') }}" class="btn btn-sm btn-outline-secondary" title="Kembali">
                <i class="fa fa-home"></i>
            </a>
        </div>

        {{-- Petunjuk --}}
        <div class="text-center mb-3">
            <h5 class="font-weight-bold text-dark mb-1">
                <i class="fa fa-qrcode text-warning mr-1"></i> Pindai QR Code Kegiatan
            </h5>
            <p class="text-muted small mb-0">
                Arahkan kamera ke QR Code kegiatan yang terpampang di layar proyektor aula.
            </p>
        </div>

        {{-- Hidden Native File Input untuk Kamera Smartphone (Bekerja 100% di HTTP & HTTPS) --}}
        <input type="file" id="qr-input-file" accept="image/*" capture="environment" style="display: none;">

        {{-- Viewfinder Kamera Live / Box Fallback --}}
        <div class="scanner-box" id="scanner-container">
            <div id="user-reader" style="width: 100%; min-height: 280px;"></div>
            <div id="fallback-box" class="p-4 text-center text-white" style="display: none;">
                <div class="mb-3">
                    <i class="fa fa-camera fa-3x text-warning"></i>
                </div>
                <h6 class="font-weight-bold mb-1">Mode Kamera Snapshot Aktif</h6>
                <p class="small text-white-50 mb-3">
                    Klik tombol di bawah untuk membuka kamera smartphone Anda dan memotret QR Code di proyektor.
                </p>
                <button type="button" class="btn btn-camera-capture btn-block" onclick="triggerNativeCamera()">
                    <i class="fa fa-camera mr-2"></i> Buka Kamera & Foto QR
                </button>
            </div>
        </div>

        <div class="mt-3">
            <div class="row no-gutters">
                <div class="col-6 pl-1">
                    <button type="button" class="btn btn-sm btn-outline-info btn-block" onclick="toggleManualInput()">
                        <i class="fa fa-keyboard mr-1"></i> Input Token Manual
                    </button>
                </div>
            </div>
        </div>

        <div id="manual-token-form" class="card mt-3 border-0 shadow-sm" style="border-radius: 12px; display: none;">
            <div class="card-body p-3">
                <label class="small font-weight-bold text-dark mb-1">Ketik / Tempel Token atau Link QR:</label>
                <div class="input-group">
                    <input type="text" id="manual_token_input" class="form-control form-control-sm" placeholder="Contoh: a1b2c3d4... atau tempel URL">
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-primary" type="button" onclick="submitManualToken()">
                            Submit
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div id="scan-alert" class="alert alert-info text-center mt-3 py-2 small shadow-sm" style="border-radius: 10px;">
            <i class="fa fa-info-circle mr-1"></i> Menyiapkan pemindai kamera...
        </div>

        <div class="modal fade" id="scanResultModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content text-center p-4" style="border-radius: 20px;">
                    <div id="modal-icon" class="mb-3" style="font-size: 52px;"></div>
                    <h5 id="modal-title" class="font-weight-bold mb-2"></h5>
                    <p id="modal-body" class="text-muted small mb-4"></p>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-primary px-4 font-weight-bold mr-2" onclick="continueScanning()">
                            <i class="fa fa-redo mr-1"></i> Scan Lagi
                        </button>
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary px-4 font-weight-bold">
                            Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    let html5Qr = null;
    let isProcessing = false;

    document.addEventListener("DOMContentLoaded", function () {
        html5Qr = new Html5Qrcode("user-reader");

        const isSecure = window.isSecureContext || window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
        const hasMediaDevices = navigator.mediaDevices && navigator.mediaDevices.getUserMedia;

        if (isSecure && hasMediaDevices) {
            initLiveCamera();
        } else {
            // Jika diakses via IP http://192.168.x.x (Insecure Context), alihkan ke mode Native Camera
            enableFallbackMode("Kamera live stream browser membutuhkan HTTPS. Mode Kamera Smartphone Native diaktifkan.");
        }

        // Event listener saat user selesai memotret QR via input camera
        document.getElementById('qr-input-file').addEventListener('change', handleFileInput);
    });

    function initLiveCamera() {
        const config = {
            fps: 12,
            qrbox: { width: 250, height: 250 },
            aspectRatio: 1.0
        };

        html5Qr.start(
            { facingMode: "environment" },
            config,
            onScanSuccess
        ).then(() => {
            updateAlert("success", '<i class="fa fa-camera mr-1"></i> Kamera aktif. Silakan bidik QR Code kegiatan.');
        }).catch(err => {
            enableFallbackMode("Tidak dapat membuka live stream kamera. Silakan gunakan tombol foto di bawah.");
        });
    }

    function enableFallbackMode(msg) {
        document.getElementById('user-reader').style.display = 'none';
        document.getElementById('fallback-box').style.display = 'block';
        updateAlert("warning", '<i class="fa fa-camera mr-1"></i> ' + msg);
    }

    function triggerNativeCamera() {
        const input = document.getElementById('qr-input-file');
        input.removeAttribute('multiple');
        input.setAttribute('capture', 'environment');
        input.click();
    }

    function triggerGallery() {
        const input = document.getElementById('qr-input-file');
        input.removeAttribute('capture');
        input.click();
    }

    function handleFileInput(e) {
        if (!e.target.files || e.target.files.length === 0) return;

        const file = e.target.files[0];
        updateAlert("info", '<i class="fa fa-spinner fa-spin mr-1"></i> Membaca QR Code dari foto...');

        html5Qr.scanFile(file, true)
            .then(decodedText => {
                onScanSuccess(decodedText);
            })
            .catch(err => {
                updateAlert("danger", '<i class="fa fa-exclamation-triangle mr-1"></i> QR Code tidak terdeteksi pada foto. Pastikan foto tegak lurus, fokus, dan cukup terang.');
            });

        // Reset input agar bisa memotret ulang
        e.target.value = '';
    }

    function onScanSuccess(decodedText) {
        if (isProcessing) return;
        isProcessing = true;

        if (html5Qr && html5Qr.isScanning) {
            html5Qr.pause();
        }

        if (navigator.vibrate) {
            navigator.vibrate(150);
        }

        updateAlert("info", '<i class="fa fa-spinner fa-spin mr-1"></i> Memproses presensi ke server...');

        fetch("{{ route('kegiatan.scan') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                qr_token: decodedText
            })
        })
        .then(res => res.json())
        .then(res => {
            const modalIcon = document.getElementById('modal-icon');
            const modalTitle = document.getElementById('modal-title');
            const modalBody = document.getElementById('modal-body');

            if (res.success && res.status === 'success') {
                modalIcon.innerHTML = '<i class="fa fa-check-circle text-success"></i>';
                modalTitle.className = "font-weight-bold mb-2 text-success";
                modalTitle.textContent = "Presensi Berhasil!";
                modalBody.textContent = res.message;
            } else if (res.success && res.status === 'already_recorded') {
                modalIcon.innerHTML = '<i class="fa fa-info-circle text-info"></i>';
                modalTitle.className = "font-weight-bold mb-2 text-info";
                modalTitle.textContent = "Sudah Tercatat";
                modalBody.textContent = res.message;
            } else {
                modalIcon.innerHTML = '<i class="fa fa-times-circle text-danger"></i>';
                modalTitle.className = "font-weight-bold mb-2 text-danger";
                modalTitle.textContent = "Presensi Gagal";
                modalBody.textContent = res.message || "Terjadi kendala saat memvalidasi QR Code.";
            }

            $('#scanResultModal').modal('show');
        })
        .catch(err => {
            alert('Terjadi kendala koneksi dengan server.');
            isProcessing = false;
            if (html5Qr && html5Qr.isScanning) html5Qr.resume();
        });
    }

    function continueScanning() {
        $('#scanResultModal').modal('hide');
        isProcessing = false;
        if (html5Qr && html5Qr.isScanning) {
            html5Qr.resume();
            updateAlert("success", '<i class="fa fa-camera mr-1"></i> Kamera siap kembali.');
        } else {
            updateAlert("info", '<i class="fa fa-camera mr-1"></i> Silakan foto QR Code kembali.');
        }
    }

    function toggleManualInput() {
        const form = document.getElementById('manual-token-form');
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
        if (form.style.display === 'block') {
            document.getElementById('manual_token_input').focus();
        }
    }

    function submitManualToken() {
        const val = document.getElementById('manual_token_input').value.trim();
        if (val) {
            onScanSuccess(val);
        }
    }

    function updateAlert(type, html) {
        const el = document.getElementById('scan-alert');
        el.className = `alert alert-${type} text-center mt-3 py-2 small shadow-sm`;
        el.innerHTML = html;
    }
</script>
@endsection
