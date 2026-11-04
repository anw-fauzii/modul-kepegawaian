@extends('layouts.appui')

@section('title')
    <title>{{ $penggajian->slip_judul }} - {{ $karyawan->nama_lengkap ?? $penggajian->karyawan_nipy }} | SIPIT PION</title>
@endsection

@section('content')
@php
    $rp = fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
    $k = $karyawan ?? $penggajian->karyawan;
@endphp

<div class="user-presensi-wrapper user-slip-container">

    {{-- Top Action Bar (Disembunyikan saat mencetak) --}}
    <div class="user-slip-actionbar no-print">
        <div class="user-slip-actionbar__left">
            <a href="{{ route('user.penggajian.index') }}" class="btn-slip-back">
                <i class="fa fa-arrow-left"></i>
                <span>Kembali ke Riwayat</span>
            </a>
            <div class="slip-doc-badge">
                <i class="fa fa-lock"></i>
                <span>Dokumen Resmi &amp; Rahasia</span>
            </div>
        </div>

        <div class="user-slip-actionbar__right">
            <button type="button" class="btn-slip-print" onclick="window.print()" title="Cetak atau Simpan sebagai PDF">
                <i class="fa fa-print"></i>
                <span>Cetak Slip (PDF)</span>
            </button>
        </div>
    </div>

    {{-- Printable Digital Salary Slip Document --}}
    <div class="digital-slip-card" id="printable-slip">
        
        {{-- Slip Header (Dark Slate + Garis Aksen Oranye) --}}
        <div class="digital-slip__header">
            <div class="digital-slip__header-left">
                <div class="digital-slip__logo">
                    <i class="pe-7s-cash"></i>
                </div>
                <div>
                    <div class="digital-slip__instansi">YAYASAN PRIMA INSANI</div>
                    <h1 class="digital-slip__title">{{ $penggajian->slip_judul }}</h1>
                    <div class="digital-slip__subtitle">
                        Sistem Informasi Prima Insani Terpadu (SIPIT) &bull; Keuangan &amp; Kepegawaian
                    </div>
                </div>
            </div>

            <div class="digital-slip__header-right">
                <div class="digital-slip__period-box">
                    <span class="digital-slip__period-label">PERIODE</span>
                    <span class="digital-slip__period-val">{{ $penggajian->periode_teks }}</span>
                </div>
                <div class="digital-slip__status-tags">
                    <span class="slip-tag slip-tag--{{ $penggajian->jenis }}">
                        {{ $penggajian->jenis_label }}
                    </span>
                    <span class="slip-tag slip-tag--{{ $penggajian->status }}">
                        <i class="fa fa-check-circle mr-1"></i> {{ $penggajian->status_label }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Bar Informasi Pegawai (Metadata Kompak 3-Kolom) --}}
        <div class="digital-slip__meta-wrap">
            <div class="meta-col">
                <div class="meta-item">
                    <span class="meta-label">Nama Pegawai</span>
                    <span class="meta-val meta-val--name">{{ $k?->nama_gelar ?? ($k?->nama_lengkap ?? '-') }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">NIPY</span>
                    <span class="meta-val font-mono">{{ $penggajian->karyawan_nipy }}</span>
                </div>
            </div>

            <div class="meta-col">
                <div class="meta-item">
                    <span class="meta-label">Unit Kerja</span>
                    <span class="meta-val">{{ $penggajian->unit ?: ($k?->unit ?? 'Yayasan Prima Insani') }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Jabatan / Amanah</span>
                    <span class="meta-val">{{ $penggajian->jabatan ?: ($k?->jabatan ?? '-') }}</span>
                </div>
            </div>

            <div class="meta-col">
                <div class="meta-item">
                    <span class="meta-label">Golongan</span>
                    <span class="meta-val">{{ $penggajian->golongan ?: '-' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Tanggal Terbit</span>
                    <span class="meta-val">{{ $penggajian->created_at ? $penggajian->created_at->translatedFormat('d F Y') : '-' }}</span>
                </div>
            </div>
        </div>

        {{-- Rincian Keuangan (Berdampingan: Penerimaan vs Potongan) --}}
        <div class="digital-slip__breakdown-body">
            <div class="breakdown-grid">
                
                {{-- Kolom Kiri: Penerimaan --}}
                <div class="breakdown-col breakdown-col--income">
                    <div class="breakdown-col__header text-emerald">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa fa-plus-circle"></i>
                            <span>A. PENERIMAAN</span>
                        </div>
                    </div>

                    <div class="breakdown-col__content">
                        <table class="breakdown-table">
                            <tbody>
                                @php $hasIncome = false; @endphp
                                @foreach ($penerimaanList as $field => $label)
                                    @if ((float) $penggajian->{$field} != 0)
                                        @php $hasIncome = true; @endphp
                                        <tr>
                                            <td class="col-item-label">{{ $label }}</td>
                                            <td class="col-item-val text-right">
                                                <span class="rp-val text-emerald font-weight-bold">{{ $rp($penggajian->{$field}) }}</span>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach

                                @if (!$hasIncome)
                                    <tr>
                                        <td colspan="2" class="text-center text-muted py-2">Tidak ada komponen penerimaan tambahan</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="breakdown-col__subtotal subtotal--income">
                        <span class="subtotal-title">Total Penerimaan (Bruto)</span>
                        <span class="subtotal-amount text-emerald rp-val">{{ $rp($penggajian->penerimaan) }}</span>
                    </div>
                </div>

                {{-- Kolom Kanan: Potongan --}}
                <div class="breakdown-col breakdown-col--deduction">
                    <div class="breakdown-col__header text-rose">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa fa-minus-circle"></i>
                            <span>B. POTONGAN</span>
                        </div>
                    </div>

                    <div class="breakdown-col__content">
                        <table class="breakdown-table">
                            <tbody>
                                @php $hasDeduction = false; @endphp
                                @foreach ($potonganList as $field => $label)
                                    @if ((float) $penggajian->{$field} != 0)
                                        @php $hasDeduction = true; @endphp
                                        <tr>
                                            <td class="col-item-label">{{ $label }}</td>
                                            <td class="col-item-val text-right">
                                                <span class="rp-val text-rose font-weight-bold">{{ $rp($penggajian->{$field}) }}</span>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach

                                @if (!$hasDeduction)
                                    <tr>
                                        <td colspan="2" class="text-center text-muted py-2">Tidak ada potongan pada slip ini</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="breakdown-col__subtotal subtotal--deduction">
                        <span class="subtotal-title">Total Potongan</span>
                        <span class="subtotal-amount text-rose rp-val">{{ $rp($penggajian->potongan) }}</span>
                    </div>
                </div>

            </div>

            {{-- Banner Highlight Take Home Pay (Gaji Bersih Diterima) --}}
            <div class="digital-slip__total-box">
                <div class="total-box__left">
                    <span class="total-box__tag">TAKE HOME PAY</span>
                    <h3 class="total-box__title">Gaji Bersih Diterima</h3>
                    <span class="total-box__formula">(Total Penerimaan Bruto &minus; Total Potongan)</span>
                </div>
                <div class="total-box__right">
                    <div class="total-box__amount rp-val">{{ $rp($penggajian->gaji_bersih) }}</div>
                    @if ($penggajian->status === 'dibayar')
                        <div class="total-box__status-pill">
                            <i class="fa fa-check-circle"></i> LUNAS / DITERIMA
                        </div>
                    @endif
                </div>
            </div>

            {{-- Area Pengesahan & Tanda Tangan --}}
            <div class="digital-slip__signatures">
                <div class="signature-block">
                    <div class="sig-role">Pegawai Penerima,</div>
                    <div class="sig-space"></div>
                    <div class="sig-name">{{ $k?->nama_gelar ?? ($k?->nama_lengkap ?? $penggajian->karyawan_nipy) }}</div>
                    <div class="sig-sub font-mono">NIPY: {{ $penggajian->karyawan_nipy }}</div>
                </div>

                <div class="signature-block text-right">
                    <div class="sig-date">Garut, {{ $penggajian->created_at ? $penggajian->created_at->translatedFormat('d F Y') : date('d F Y') }}</div>
                    <div class="sig-role">Kabag Keuangan,</div>
                    <div class="sig-space">
                        <div class="sig-seal">
                            <i class="fa fa-shield-alt mr-1"></i> SIPIT VERIFIED
                        </div>
                    </div>
                    <div class="sig-name">Tim Keuangan Prima Insani</div>
                    <div class="sig-sub">Yayasan Prima Insani</div>
                </div>
            </div>

            {{-- Catatan Keabsahan Dokumen --}}
            <div class="digital-slip__footer-note">
                <p>
                    <i class="fa fa-info-circle mr-1"></i> Dokumen ini merupakan slip gaji elektronik resmi yang diterbitkan secara sah oleh Sistem Informasi Prima Insani Terpadu (SIPIT) Yayasan Prima Insani.
                </p>
            </div>
        </div>

    </div>

</div>
@endsection

@push('css')
<style>
    /* =========================================================================
       PAGE TOP ACTION BAR
       ========================================================================= */
    .user-slip-container {
        margin: 0 auto;
        padding-bottom: 30px;
    }
    .user-slip-actionbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }
    .user-slip-actionbar__left {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .btn-slip-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 7px 15px;
        border-radius: 9px;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        color: #334155 !important;
        font-weight: 700;
        font-size: 0.8rem;
        text-decoration: none;
        transition: all 0.2s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }
    .btn-slip-back:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        color: #0f172a !important;
        transform: translateX(-2px);
    }
    .slip-doc-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.72rem;
        font-weight: 600;
        color: #64748b;
        background: #f1f5f9;
        padding: 4px 10px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }
    .btn-slip-print {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 18px;
        border-radius: 9px;
        background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%);
        border: none;
        color: #ffffff;
        font-weight: 700;
        font-size: 0.82rem;
        cursor: pointer;
        box-shadow: 0 4px 10px rgba(234, 88, 12, 0.25);
        transition: all 0.2s ease;
    }
    .btn-slip-print:hover {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        box-shadow: 0 5px 14px rgba(234, 88, 12, 0.35);
        transform: translateY(-1px);
    }

    /* =========================================================================
       DIGITAL SLIP CARD DOCUMENT
       ========================================================================= */
    .digital-slip-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 8px 20px -4px rgba(15, 23, 42, 0.08), 0 4px 6px -2px rgba(15, 23, 42, 0.03);
        overflow: hidden;
    }

    /* 1. Header (Dark Navy with Warm Orange Accent Line) */
    .digital-slip__header {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 65%, #292524 100%);
        border-top: 4px solid #ea580c;
        color: #ffffff;
        padding: 20px 28px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }
    .digital-slip__header-left {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .digital-slip__logo {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: rgba(234, 88, 12, 0.2);
        border: 1px solid rgba(251, 146, 60, 0.35);
        color: #fb923c;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
    }
    .digital-slip__instansi {
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        color: #fb923c;
        text-transform: uppercase;
    }
    .digital-slip__title {
        font-size: 1.25rem;
        font-weight: 900;
        color: #ffffff;
        margin: 2px 0;
        letter-spacing: -0.01em;
    }
    .digital-slip__subtitle {
        font-size: 0.76rem;
        color: #cbd5e1;
        font-weight: 500;
    }
    .digital-slip__header-right {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 6px;
    }
    .digital-slip__period-box {
        text-align: right;
    }
    .digital-slip__period-label {
        display: block;
        font-size: 0.62rem;
        font-weight: 700;
        color: #94a3b8;
        letter-spacing: 0.06em;
    }
    .digital-slip__period-val {
        font-size: 0.92rem;
        font-weight: 800;
        color: #ffffff;
    }
    .digital-slip__status-tags {
        display: flex;
        gap: 6px;
        align-items: center;
    }
    .slip-tag {
        font-size: 0.7rem;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 6px;
    }
    .slip-tag--gaji    { background: #0284c7; color: #ffffff; }
    .slip-tag--thr     { background: #059669; color: #ffffff; }
    .slip-tag--bonus   { background: #d97706; color: #ffffff; }
    .slip-tag--dibayar { background: #10b981; color: #ffffff; }
    .slip-tag--final   { background: #4f46e5; color: #ffffff; }
    .slip-tag--draft   { background: #64748b; color: #ffffff; }

    /* 2. Metadata Wrap (Kompak & Bersih) */
    .digital-slip__meta-wrap {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 14px 28px;
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }
    @media (max-width: 640px) {
        .digital-slip__meta-wrap {
            grid-template-columns: 1fr;
            gap: 10px;
            padding: 12px 18px;
        }
    }
    .meta-col {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .meta-item {
        display: flex;
        flex-direction: column;
    }
    .meta-label {
        font-size: 0.66rem;
        text-transform: uppercase;
        font-weight: 700;
        color: #94a3b8;
        letter-spacing: 0.04em;
        margin-bottom: 1px;
    }
    .meta-val {
        font-size: 0.85rem;
        font-weight: 700;
        color: #1e293b;
    }
    .meta-val--name {
        color: #0f172a;
        font-size: 0.92rem;
    }

    /* 3. Breakdown Body & Financial Grid */
    .digital-slip__breakdown-body {
        padding: 20px 28px 24px;
    }
    @media (max-width: 640px) {
        .digital-slip__breakdown-body {
            padding: 16px 16px 20px;
        }
    }
    .breakdown-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 18px;
    }
    @media (max-width: 768px) {
        .breakdown-grid {
            grid-template-columns: 1fr;
            gap: 14px;
        }
    }
    .breakdown-col {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .breakdown-col__header {
        padding: 10px 16px;
        font-size: 0.76rem;
        font-weight: 800;
        letter-spacing: 0.05em;
        background: #fafbfc;
        border-bottom: 1px solid #e2e8f0;
    }
    .breakdown-col__content {
        padding: 10px 16px;
        flex-grow: 1;
    }

    /* Tabel Rincian: Memastikan Rp dan Nominal Selalu Berdampingan Satu Baris */
    .breakdown-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: auto;
    }
    .breakdown-table td {
        padding: 5px 0;
        font-size: 0.82rem;
        border-bottom: 1px dashed #e2e8f0;
        vertical-align: middle;
    }
    .breakdown-table tr:last-child td {
        border-bottom: none;
    }
    .col-item-label {
        color: #475569;
        font-weight: 500;
        padding-right: 14px;
    }
    .col-item-val {
        text-align: right;
        width: 1%;
        white-space: nowrap !important;
    }

    /* Utilitas Khusus: Mencegah 'Rp' Terpotong / Di-Enter ke Baris Baru */
    .rp-val {
        display: inline-block;
        white-space: nowrap !important;
        word-break: keep-all !important;
        font-variant-numeric: tabular-nums;
        letter-spacing: -0.01em;
    }

    .breakdown-col__subtotal {
        padding: 10px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        border-top: 1px solid #e2e8f0;
    }
    .subtotal--income {
        background: #f0fdf4;
        border-top-color: #bbf7d0;
    }
    .subtotal--deduction {
        background: #fff1f2;
        border-top-color: #fecdd3;
    }
    .subtotal-title {
        font-size: 0.76rem;
        font-weight: 800;
        text-transform: uppercase;
        color: #334155;
    }
    .subtotal-amount {
        font-size: 0.94rem;
        font-weight: 900;
        white-space: nowrap !important;
    }

    /* 4. Total Gaji Bersih Box (Kompak & Elegan) */
    .digital-slip__total-box {
        background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
        border: 2px solid #ea580c;
        border-radius: 12px;
        padding: 14px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 14px;
    }
    .total-box__tag {
        display: inline-block;
        font-size: 0.65rem;
        font-weight: 800;
        color: #c2410c;
        letter-spacing: 0.08em;
    }
    .total-box__title {
        font-size: 1.15rem;
        font-weight: 900;
        color: #9a3412;
        margin: 1px 0;
    }
    .total-box__formula {
        font-size: 0.72rem;
        color: #7c2d12;
        font-weight: 500;
    }
    .total-box__right {
        text-align: right;
    }
    .total-box__amount {
        font-size: 1.65rem;
        font-weight: 900;
        color: #c2410c;
        letter-spacing: -0.01em;
        line-height: 1.1;
        white-space: nowrap !important;
    }
    .total-box__status-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.68rem;
        font-weight: 800;
        color: #166534;
        background: #dcfce7;
        padding: 2px 7px;
        border-radius: 5px;
        margin-top: 3px;
        white-space: nowrap !important;
    }

    /* 5. Tanda Tangan & Legalitas */
    .digital-slip__signatures {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 24px;
        margin-top: 18px;
        padding-top: 14px;
        border-top: 1px dashed #cbd5e1;
    }
    .signature-block {
        flex: 1;
    }
    .sig-date {
        font-size: 0.75rem;
        font-weight: 600;
        color: #475569;
        margin-bottom: 2px;
    }
    .sig-role {
        font-size: 0.75rem;
        font-weight: 600;
        color: #64748b;
    }
    .sig-space {
        height: 48px;
        display: flex;
        align-items: center;
    }
    .signature-block.text-right .sig-space {
        justify-content: flex-end;
    }
    .sig-seal {
        display: inline-flex;
        align-items: center;
        padding: 3px 8px;
        border-radius: 5px;
        font-size: 0.65rem;
        font-weight: 800;
        letter-spacing: 0.05em;
        color: #0369a1;
        background: #e0f2fe;
        border: 1px solid #bae6fd;
    }
    .sig-name {
        font-size: 0.84rem;
        font-weight: 800;
        color: #0f172a;
        text-decoration: underline;
    }
    .sig-sub {
        font-size: 0.7rem;
        color: #64748b;
    }

    /* 6. Catatan Footer */
    .digital-slip__footer-note {
        margin-top: 16px;
        padding-top: 10px;
        border-top: 1px solid #f1f5f9;
        text-align: center;
    }
    .digital-slip__footer-note p {
        font-size: 0.68rem;
        color: #94a3b8;
        margin: 0;
        font-style: italic;
    }

    /* =========================================================================
       PRINT STYLES (@media print)
       ========================================================================= */
    @media print {
        body, html {
            background: #ffffff !important;
            color: #000000 !important;
        }
        .no-print,
        .app-sidebar,
        .app-header,
        .app-footer,
        .app-page-title,
        .sidebar,
        .header {
            display: none !important;
        }
        .user-slip-container {
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .digital-slip-card {
            border: 1px solid #94a3b8 !important;
            box-shadow: none !important;
            border-radius: 0 !important;
        }
        .digital-slip__header {
            background: #0f172a !important;
            color: #ffffff !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .digital-slip__total-box {
            background: #fff7ed !important;
            border: 2px solid #ea580c !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .sig-space {
            height: 42px !important;
        }
    }
</style>
@endpush
