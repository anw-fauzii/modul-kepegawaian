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
        min-height: 300px;
        background: #0f172a;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(0,0,0,0.25);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    #preview {
        width: 100%;
        height: 100%;
        min-height: 300px;
        max-height: 380px;
        object-fit: cover;
        display: block;
        background-color: #0f172a;
    }
    .scanner-overlay {
        position: absolute;
        inset: 0;
        pointer-events: none;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .scanner-target {
        width: 220px;
        height: 220px;
        border: 2px solid rgba(245, 158, 11, 0.85);
        border-radius: 16px;
        position: relative;
        box-shadow: 0 0 0 9999px rgba(15, 23, 42, 0.35);
    }
    .laser-line {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, transparent, #ea580c, #f59e0b, transparent);
        box-shadow: 0 0 8px #f59e0b;
        animation: scanMove 2s infinite ease-in-out;
    }
    @keyframes scanMove {
        0% { top: 5%; opacity: 0.3; }
        50% { top: 95%; opacity: 1; }
        100% { top: 5%; opacity: 0.3; }
    }
    .user-profile-badge {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        padding: 16px 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.03);
    }
    .btn-switch-cam {
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
        border: none;
        color: #ffffff;
        padding: 8px 16px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.2s ease;
    }
    .btn-switch-cam:hover {
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(2, 132, 199, 0.3);
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
                    <h6 class="mb-0 font-weight-bold text-dark">{{ auth()->user()?->name ?? 'User' }}</h6>
                    <div class="small text-muted">
                        NIPY: <code class="text-primary font-weight-bold">{{ auth()->user()?->no_induk ?? '-' }}</code>
                    </div>
                </div>
            </div>
            <a href="{{ route('home') }}" class="btn btn-sm btn-outline-secondary" title="Kembali ke Dashboard">
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

        {{-- Viewfinder Instascan --}}
        <div class="scanner-box" id="scanner-container">
            <video id="preview" playsinline autoplay muted></video>

            <!-- Overlay Target Kotak & Laser Animasi -->
            <div class="scanner-overlay">
                <div class="scanner-target">
                    <div class="laser-line"></div>
                </div>
            </div>
        </div>

        {{-- Kontrol Kamera (Ganti Kamera & Input Manual) --}}
        <div class="mt-3">
            <div id="switch-camera-container" class="mb-2" style="display: none;">
                <button type="button" class="btn btn-switch-cam btn-block" onclick="switchCamera()">
                    <i class="fa fa-refresh mr-1"></i> Ganti Kamera (Depan / Belakang)
                </button>
            </div>

            <div class="text-center">
                <button type="button" class="btn btn-sm btn-outline-info" onclick="toggleManualInput()" style="border-radius: 8px;">
                    <i class="fa fa-keyboard-o mr-1"></i> Input Token Manual
                </button>
            </div>
        </div>

        {{-- Form Input Manual (Collapsible) --}}
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

        {{-- Status Alert Box --}}
        <div id="scan-alert" class="alert alert-info text-center mt-3 py-2 small shadow-sm" style="border-radius: 10px;">
            <i class="fa fa-info-circle mr-1"></i> Menyiapkan pemindai kamera (Instascan)...
        </div>

        {{-- Modal Notifikasi Hasil Scan --}}
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

{{-- Library Instascan Lokal --}}
<script src="{{ asset('js/instascan.min.js') }}" type="text/javascript"></script>

<script type="text/javascript">
    let scanner = null;
    let availableCameras = [];
    let currentCameraIndex = 0;
    let isProcessing = false;

    document.addEventListener("DOMContentLoaded", function () {
        const previewElement = document.getElementById('preview');

        try {
            // Inisialisasi scanner Instascan sesuai dokumentasi resmi
            scanner = new Instascan.Scanner({
                video: previewElement,
                mirror: false,
                scanPeriod: 5
            });

            // Listener saat QR Code terdeteksi
            scanner.addListener('scan', function (content) {
                console.log('Instascan detected:', content);
                onScanSuccess(content);
            });

            // Deteksi daftar kamera yang tersedia
            Instascan.Camera.getCameras().then(function (cameras) {
                availableCameras = cameras;

                if (cameras.length > 0) {
                    // Otomatis prioritaskan kamera belakang (environment) jika ada
                    let selectedIndex = 0;
                    for (let i = 0; i < cameras.length; i++) {
                        const name = (cameras[i].name || '').toLowerCase();
                        if (name.includes('back') || name.includes('rear') || name.includes('belakang') || name.includes('environment')) {
                            selectedIndex = i;
                            break;
                        }
                    }
                    if (selectedIndex === 0 && cameras.length > 1) {
                        selectedIndex = cameras.length - 1;
                    }

                    currentCameraIndex = selectedIndex;
                    startSelectedCamera(cameras[selectedIndex]);

                    // Jika tersedia lebih dari 1 kamera, tampilkan tombol ganti kamera
                    if (cameras.length > 1) {
                        document.getElementById('switch-camera-container').style.display = 'block';
                    }
                } else {
                    updateAlert("warning", '<i class="fa fa-exclamation-triangle mr-1"></i> Tidak ada kamera yang ditemukan pada perangkat Anda.');
                }
            }).catch(function (e) {
                console.error('Error Instascan getCameras:', e);
                updateAlert("danger", '<i class="fa fa-times-circle mr-1"></i> Gagal mengakses kamera: ' + (e.message || e));
            });
        } catch (err) {
            console.error('Instascan init error:', err);
            updateAlert("danger", '<i class="fa fa-times-circle mr-1"></i> Terjadi kesalahan pada scanner: ' + err.message);
        }
    });

    function startSelectedCamera(camera) {
        if (!scanner || !camera) return;

        updateAlert("info", '<i class="fa fa-spinner fa-spin mr-1"></i> Membuka kamera...');

        scanner.start(camera).then(function () {
            const camLabel = camera.name ? camera.name : 'Kamera Aktif';
            updateAlert("success", '<i class="fa fa-video-camera mr-1"></i> ' + camLabel + ' aktif. Arahkan ke QR Code kegiatan.');
        }).catch(function (err) {
            console.error('Error starting camera:', err);
            updateAlert("danger", '<i class="fa fa-exclamation-triangle mr-1"></i> Gagal mengaktifkan kamera: ' + (err.message || err));
        });
    }

    function switchCamera() {
        if (!availableCameras || availableCameras.length <= 1) return;

        currentCameraIndex = (currentCameraIndex + 1) % availableCameras.length;
        const nextCamera = availableCameras[currentCameraIndex];
        startSelectedCamera(nextCamera);
    }

    function onScanSuccess(decodedText) {
        if (isProcessing) return;
        isProcessing = true;

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
        });
    }

    function continueScanning() {
        $('#scanResultModal').modal('hide');
        isProcessing = false;
        updateAlert("success", '<i class="fa fa-video-camera mr-1"></i> Kamera siap kembali. Arahkan ke QR Code.');
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
