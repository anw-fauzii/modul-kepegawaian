<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $sk ? 'Verifikasi SK: ' . ($sk->nomor_sk ?: 'Resmi') : 'Verifikasi Gagal' }} - Yayasan Prima Insani</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #ea580c;
            --primary-dark: #c2410c;
            --amber: #f59e0b;
            --emerald: #059669;
            --rose: #e11d48;
            --dark: #0f172a;
            --slate-800: #1e293b;
            --slate-600: #475569;
            --slate-400: #94a3b8;
            --slate-100: #f1f5f9;
            --slate-50: #f8fafc;
            --line: #e2e8f0;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            color: var(--dark);
            background:
                radial-gradient(40rem 25rem at 50% 0%, rgba(234, 88, 12, 0.14), transparent 80%),
                linear-gradient(180deg, #fef8f4 0%, #f1f5f9 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px 14px;
            -webkit-font-smoothing: antialiased;
        }

        .container {
            width: 100%;
            max-width: 440px;
        }

        /* Brand Header */
        .brand-bar {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 14px;
        }
        .brand-bar img {
            height: 38px;
            width: auto;
            object-fit: contain;
        }

        /* Main Certificate Card */
        .card {
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.05), 0 20px 30px -10px rgba(15, 23, 42, 0.08);
            overflow: hidden;
            position: relative;
        }

        /* Top Accent Banner */
        .card-top-accent {
            height: 4px;
            background: linear-gradient(90deg, #ea580c 0%, #f59e0b 50%, #10b981 100%);
        }
        .card-top-accent.invalid {
            background: linear-gradient(90deg, #e11d48, #f43f5e);
        }

        /* Header Info Section */
        .card-header {
            padding: 20px 22px 14px;
            text-align: center;
            background: linear-gradient(180deg, #ffffff 0%, #fafafa 100%);
            border-bottom: 1px solid var(--line);
        }

        .verified-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 9999px;
            background: #ecfdf5;
            color: var(--emerald);
            border: 1px solid #a7f3d0;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.03em;
            margin-bottom: 10px;
        }
        .verified-badge svg {
            width: 14px;
            height: 14px;
        }

        .verified-badge.invalid {
            background: #fff1f2;
            color: var(--rose);
            border-color: #fecdd3;
        }

        .recipient-name {
            font-size: 17px;
            font-weight: 800;
            color: var(--dark);
            line-height: 1.35;
            letter-spacing: -0.01em;
            margin-bottom: 5px;
        }

        .position-pill {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 6px;
            background: #fff7ed;
            color: #c2410c;
            border: 1px solid #ffedd5;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.01em;
        }

        /* Detail List Rows (Ringkas & Padat) */
        .data-list {
            padding: 6px 18px;
        }

        .data-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 7.5px 4px;
            border-bottom: 1px dashed #e9eef4;
            font-size: 12px;
            line-height: 1.4;
        }
        .data-row:last-child {
            border-bottom: none;
        }

        .data-label {
            color: var(--slate-600);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }
        .data-label svg {
            width: 13px;
            height: 13px;
            color: var(--primary);
            opacity: 0.85;
        }

        .data-val {
            font-weight: 700;
            color: var(--dark);
            text-align: right;
            word-break: break-word;
        }

        .mono {
            font-family: 'JetBrains Mono', ui-monospace, Menlo, Consolas, monospace;
            font-size: 11.5px;
        }

        /* Security Token Bar */
        .token-box {
            margin: 10px 18px 16px;
            padding: 8px 12px;
            border-radius: 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .token-info {
            display: flex;
            flex-direction: column;
            text-align: left;
        }
        .token-caption {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--slate-400);
            font-weight: 700;
        }
        .token-code {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12.5px;
            font-weight: 800;
            color: var(--primary-dark);
            letter-spacing: 0.06em;
        }

        .btn-copy {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            padding: 4px 9px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            color: var(--slate-600);
            cursor: pointer;
            transition: all 0.15s ease;
            font-family: inherit;
        }
        .btn-copy:hover {
            background: #fff7ed;
            color: var(--primary);
            border-color: #fdba74;
        }

        /* Action Buttons */
        .card-actions {
            padding: 0 18px 18px;
        }

        .btn-portal {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 9px 14px;
            border-radius: 10px;
            background: linear-gradient(135deg, #ea580c 0%, #f59e0b 100%);
            color: #ffffff;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 3px 8px -1px rgba(234, 88, 12, 0.35);
            transition: transform 0.15s, opacity 0.15s;
        }
        .btn-portal:hover {
            opacity: 0.95;
            transform: translateY(-1px);
        }
        .btn-portal svg {
            width: 14px;
            height: 14px;
        }

        /* Invalid / Error View */
        .error-body {
            padding: 24px 20px 20px;
            text-align: center;
        }
        .error-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #fff1f2;
            color: var(--rose);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            box-shadow: 0 0 0 6px #ffe4e6;
        }
        .error-icon svg {
            width: 24px;
            height: 24px;
        }
        .error-title {
            font-size: 16px;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 6px;
        }
        .error-desc {
            font-size: 11.5px;
            color: var(--slate-600);
            line-height: 1.5;
            margin-bottom: 16px;
        }

        /* Footer */
        .footer {
            margin-top: 14px;
            text-align: center;
            font-size: 10.5px;
            color: #94a3b8;
            line-height: 1.5;
        }

        @media (max-width: 360px) {
            .data-label, .data-val { font-size: 11px; }
            .recipient-name { font-size: 15px; }
        }
    </style>
</head>
<body>

<div class="container">

    <!-- Header Brand Logo Yayasan -->
    <div class="brand-bar">
        <img src="{{ asset('storage/logo/samping.png') }}" 
             alt="Logo Yayasan Prima Insani" 
             onerror="this.src='{{ asset('logo.png') }}'; this.onerror=null;">
    </div>

    <!-- Main Card -->
    <div class="card">
        @if ($sk)
            {{-- Garis Aksen Atas --}}
            <div class="card-top-accent"></div>

            {{-- Header Status & Penerima SK --}}
            <div class="card-header">
                <div class="verified-badge">
                    <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Dokumen Resmi Terverifikasi</span>
                </div>

                <h1 class="recipient-name">
                    {{ $sk->karyawan?->nama_gelar ?? $sk->karyawan?->nama_lengkap ?? $sk->karyawan_nipy }}
                </h1>

                <div>
                    <span class="position-pill">{{ $sk->jabatan }}</span>
                </div>
            </div>

            {{-- Daftar Rincian Rapi & Padat (Jarak Antar Baris Ringkas) --}}
            <div class="data-list">
                <!-- Nomor SK -->
                <div class="data-row">
                    <span class="data-label">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Nomor SK
                    </span>
                    <span class="data-val mono">{{ $sk->nomor_sk ?: '-' }}</span>
                </div>

                <!-- NIPY -->
                @if ($sk->karyawan_nipy)
                <div class="data-row">
                    <span class="data-label">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        NIPY
                    </span>
                    <span class="data-val mono">{{ $sk->karyawan_nipy }}</span>
                </div>
                @endif

                <!-- Unit Kerja -->
                <div class="data-row">
                    <span class="data-label">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Unit Kerja
                    </span>
                    <span class="data-val">{{ $sk->karyawan?->unit ?: '-' }}</span>
                </div>

                <!-- Tahun Ajaran -->
                <div class="data-row">
                    <span class="data-label">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        Tahun Ajaran
                    </span>
                    <span class="data-val">{{ $sk->tahunAjaran?->nama_tahun_ajaran ?? '-' }}</span>
                </div>

                <!-- Tanggal Penetapan SK -->
                <div class="data-row">
                    <span class="data-label">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Tanggal SK
                    </span>
                    <span class="data-val">
                        {{ $sk->tanggal_sk ? $sk->tanggal_sk->translatedFormat('d F Y') : '-' }}
                    </span>
                </div>
            </div>

            {{-- Kotak Token Kode Verifikasi --}}
            <div class="token-box">
                <div class="token-info">
                    <span class="token-caption">Kode Verifikasi</span>
                    <span class="token-code" id="kodeSk">{{ $kode }}</span>
                </div>
                <button type="button" class="btn-copy" id="btnSalin" onclick="salinKode()">
                    Salin
                </button>
            </div>

        @else
            {{-- ==========================================
                 KONDISI: DOKUMEN TIDAK TERDAFTAR / GAGAL
                 ========================================== --}}
            <div class="card-top-accent invalid"></div>

            <div class="error-body">
                <div class="error-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>

                <div class="verified-badge invalid">
                    Verifikasi Gagal
                </div>

                <h2 class="error-title">Dokumen Tidak Ditemukan</h2>
                
                <p class="error-desc">
                    Kode <span class="mono" style="font-weight:700; color:var(--dark); background:#f1f5f9; padding:1px 5px; border-radius:4px;">{{ $kode }}</span> 
                    tidak terdaftar sebagai Surat Keputusan sah Yayasan Prima Insani.
                </p>

            </div>
        @endif
    </div>


</div>

<script>
    function salinKode() {
        const kode = document.getElementById('kodeSk')?.innerText?.trim();
        const btn = document.getElementById('btnSalin');
        if (!kode) return;

        navigator.clipboard.writeText(kode).then(() => {
            btn.innerText = 'Tersalin!';
            btn.style.color = '#059669';
            btn.style.borderColor = '#a7f3d0';
            setTimeout(() => {
                btn.innerText = 'Salin';
                btn.style.color = '';
                btn.style.borderColor = '';
            }, 1800);
        }).catch(() => {
            alert('Kode: ' + kode);
        });
    }
</script>

</body>
</html>
