<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Presensi Kegiatan - SIPIT</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .result-card {
            max-width: 460px;
            width: 100%;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            overflow: hidden;
            text-align: center;
        }
        .icon-circle {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 42px;
            margin-bottom: 20px;
        }
        .icon-success {
            background-color: #dcfce7;
            color: #16a34a;
        }
        .icon-info {
            background-color: #e0f2fe;
            color: #0284c7;
        }
        .icon-danger {
            background-color: #fee2e2;
            color: #dc2626;
        }
        .detail-box {
            background-color: #f8fafc;
            border-radius: 12px;
            padding: 16px;
            border: 1px solid #e2e8f0;
            text-align: left;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="result-card p-4">
    @if($success && ($status ?? '') === 'success')
        <div class="icon-circle icon-success">
            <i class="fa fa-check"></i>
        </div>
        <h4 class="font-weight-bold text-dark mb-1">Presensi Berhasil!</h4>
        <p class="text-muted small mb-3">Kehadiran Anda telah resmi tercatat di sistem Kepegawaian.</p>

        <div class="detail-box">
            <div class="mb-2">
                <small class="text-muted text-uppercase font-weight-bold">Kegiatan</small>
                <div class="font-weight-bold text-dark">{{ $kegiatan ?? 'Kegiatan Lembaga' }}</div>
            </div>
            <div class="mb-2">
                <small class="text-muted text-uppercase font-weight-bold">Nama Karyawan</small>
                <div class="font-weight-bold text-dark">
                    {{ $karyawan->nama_lengkap ?? auth()->user()->name }}
                    @if(!empty($karyawan->gelar)), {{ $karyawan->gelar }}@endif
                </div>
                <div class="small text-muted">NIPY: {{ $karyawan->nipy ?? auth()->user()->no_induk }}</div>
            </div>
            @if(isset($kehadiran))
                <div>
                    <small class="text-muted text-uppercase font-weight-bold">Waktu Presensi</small>
                    <div class="font-weight-bold text-success">
                        <i class="fa fa-clock mr-1"></i> {{ $kehadiran->waktu_scan->format('H:i:s') }} WIB ({{ $kehadiran->waktu_scan->format('d/m/Y') }})
                    </div>
                </div>
            @endif
        </div>

    @elseif($success && ($status ?? '') === 'already_recorded')
        <div class="icon-circle icon-info">
            <i class="fa fa-info"></i>
        </div>
        <h4 class="font-weight-bold text-dark mb-1">Sudah Tercatat</h4>
        <p class="text-muted small mb-3">{{ $message }}</p>

        <div class="detail-box">
            <div class="mb-2">
                <small class="text-muted text-uppercase font-weight-bold">Kegiatan</small>
                <div class="font-weight-bold text-dark">{{ $kegiatan ?? '-' }}</div>
            </div>
            <div class="mb-2">
                <small class="text-muted text-uppercase font-weight-bold">Nama Karyawan</small>
                <div class="font-weight-bold text-dark">
                    {{ $karyawan->nama_lengkap ?? auth()->user()->name }}
                </div>
                <div class="small text-muted">NIPY: {{ $karyawan->nipy ?? auth()->user()->no_induk }}</div>
            </div>
            @if(isset($kehadiran))
                <div>
                    <small class="text-muted text-uppercase font-weight-bold">Waktu Scan Sebelumnya</small>
                    <div class="font-weight-bold text-info">
                        <i class="fa fa-clock mr-1"></i> {{ $kehadiran->waktu_scan->format('H:i:s') }} WIB
                    </div>
                </div>
            @endif
        </div>

    @else
        <div class="icon-circle icon-danger">
            <i class="fa fa-times"></i>
        </div>
        <h4 class="font-weight-bold text-dark mb-1">Presensi Gagal</h4>
        <p class="text-danger small mb-3">{{ $message }}</p>

        <div class="alert alert-light border small text-muted text-left mb-0">
            <i class="fa fa-info-circle mr-1 text-primary"></i> 
            Pastikan gerbang presensi sedang dibuka oleh panitia dan akun Anda terhubung dengan NIPY Karyawan yang valid.
        </div>
    @endif

    <div class="mt-4">
        <a href="{{ route('home') }}" class="btn btn-primary btn-block font-weight-bold py-2" style="border-radius: 10px;">
            <i class="fa fa-home mr-1"></i> Kembali ke Dashboard
        </a>
        <a href="{{ route('kegiatan.scanner') }}" class="btn btn-outline-secondary btn-block font-weight-bold py-2 mt-2" style="border-radius: 10px;">
            <i class="fa fa-camera mr-1"></i> Buka Pemindai Kamera
        </a>
    </div>
</div>

</body>
</html>
