<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layar Presensi QR - {{ $kegiatan->nama_kegiatan }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%);
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
            overflow-x: hidden;
        }
        .display-container {
            max-width: 900px;
            width: 100%;
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            padding: 40px;
            text-align: center;
        }
        .qr-box {
            background: #ffffff;
            padding: 24px;
            border-radius: 20px;
            display: inline-block;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            margin: 20px auto;
        }
        .live-clock {
            font-size: 28px;
            font-weight: 700;
            color: #38bdf8;
            letter-spacing: 2px;
        }
        .badge-pill-gate {
            padding: 8px 18px;
            font-size: 14px;
            border-radius: 50px;
            font-weight: 700;
        }
        .instruction-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 14px;
            padding: 15px 20px;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
    </style>
</head>
<body>

<div class="display-container">
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="text-left">
            <span class="badge badge-warning text-dark font-weight-bold px-3 py-1 mb-2">
                <i class="fa fa-qrcode mr-1"></i> PRESENSI RESMI KEGIATAN
            </span>
            <div class="text-white-50 small">
                {{ $kegiatan->tahunAjaran->nama_tahun_ajaran ?? 'SIPIT Kepegawaian' }}
            </div>
        </div>
        <div class="text-right">
            <div class="live-clock" id="liveClock">00:00:00</div>
            <div class="small text-white-50">{{ $kegiatan->tanggal->isoFormat('dddd, D MMMM Y') }}</div>
        </div>
    </div>

    <hr style="border-color: rgba(255,255,255,0.1);">

    {{-- Title & Speaker --}}
    <h2 class="font-weight-bold text-white mb-2" style="font-size: 32px; letter-spacing: -0.5px;">
        {{ $kegiatan->nama_kegiatan }}
    </h2>
    <div class="h5 text-info mb-3">
        @if($kegiatan->pengisi)
            <i class="fa fa-user-tie mr-1"></i> {{ $kegiatan->pengisi }}
        @endif
        @if($kegiatan->lokasi)
            <span class="mx-2 text-white-50">•</span>
            <i class="fa fa-map-marker-alt mr-1 text-danger"></i> {{ $kegiatan->lokasi }}
        @endif
    </div>

    {{-- Gate Status --}}
    <div class="mb-3">
        @if($kegiatan->is_active)
            <span class="badge-pill-gate bg-success text-white">
                <i class="fa fa-door-open mr-1"></i> Gerbang Presensi Sedang DIBUKA
            </span>
        @else
            <span class="badge-pill-gate bg-danger text-white">
                <i class="fa fa-door-closed mr-1"></i> Gerbang Presensi Telah DITUTUP
            </span>
        @endif
    </div>

    {{-- QR Box --}}
    <div class="qr-box">
        <div id="qrcode"></div>
    </div>

    {{-- Instructions --}}
    <div class="row justify-content-center mt-2">
        <div class="col-md-10">
            <div class="instruction-card text-white-50">
                <div class="row text-left align-items-center">
                    <div class="col-md-4 mb-2 mb-md-0">
                        <strong class="text-white d-block"><i class="fa fa-mobile-alt mr-1 text-primary"></i> Langkah 1:</strong>
                        Buka kamera HP atau menu scan di aplikasi SIPIT.
                    </div>
                    <div class="col-md-4 mb-2 mb-md-0">
                        <strong class="text-white d-block"><i class="fa fa-expand mr-1 text-warning"></i> Langkah 2:</strong>
                        Arahkan lensa kamera ke QR Code di atas.
                    </div>
                    <div class="col-md-4">
                        <strong class="text-white d-block"><i class="fa fa-check-circle mr-1 text-success"></i> Langkah 3:</strong>
                        Kehadiran Anda otomatis tercatat dalam sistem.
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Controls --}}
    <div class="mt-4 pt-2">
        <button onclick="toggleFullScreen()" class="btn btn-outline-light btn-sm mr-2 font-weight-bold">
            <i class="fa fa-expand mr-1"></i> Mode Layar Penuh (F11)
        </button>
        <a href="{{ route('kegiatan.show', $kegiatan->id) }}" class="btn btn-primary btn-sm font-weight-bold">
            <i class="fa fa-chart-bar mr-1"></i> Rekap Kehadiran
        </a>
    </div>
</div>

{{-- QR Code Generator (Client-Side Vanilla JS) --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    // Live Clock
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        document.getElementById('liveClock').textContent = `${hours}:${minutes}:${seconds}`;
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Generate QR Code with Payload URL (Kompatibel dengan kamera bawaan HP & scanner web SIPIT)
    const qrPayload = "{{ route('kegiatan.presensi-direct', $kegiatan->qr_token) }}";
    new QRCode(document.getElementById("qrcode"), {
        text: qrPayload,
        width: 320,
        height: 320,
        colorDark : "#0f172a",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });

    // Fullscreen Toggle
    function toggleFullScreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen();
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            }
        }
    }
</script>
</body>
</html>
