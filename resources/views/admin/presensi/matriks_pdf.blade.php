<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Matriks Presensi Harian - {{ $selectedBulan?->nama_bulan ?? 'Laporan' }}</title>
    <style>
        /* Pengaturan Halaman Cetak DomPDF: Kertas A3 Landscape dengan Ukuran Font A4 */
        @page {
            size: a3 landscape;
            margin: 8mm 6mm 10mm 6mm;
        }

        body {
            font-family: 'DejaVu Sans', Helvetica, Arial, sans-serif;
            font-size: 7.5pt;
            color: #1e293b;
            line-height: 1.25;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }

        /* Kop / Header Laporan */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            border-bottom: 1.5pt solid #0f172a;
            padding-bottom: 5px;
        }

        .kop-table td {
            vertical-align: middle;
            padding: 0;
        }

        .kop-logo {
            width: 75px;
            text-align: left;
        }

        .kop-logo img {
            max-height: 44px;
            width: auto;
        }

        .kop-title {
            text-align: left;
            padding-left: 8px;
        }

        .institution-name {
            font-size: 13pt;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .report-title {
            font-size: 10pt;
            font-weight: bold;
            color: #ea580c;
            text-transform: uppercase;
            margin-top: 1px;
        }

        .system-sub {
            font-size: 7.5pt;
            color: #64748b;
        }

        .kop-meta {
            width: 270px;
            text-align: right;
            font-size: 7pt;
            color: #334155;
            line-height: 1.35;
        }

        /* Mini KPI Bar Table */
        .kpi-bar {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            background-color: #f8fafc;
            border: 0.5pt solid #cbd5e1;
        }

        .kpi-bar td {
            padding: 3px 6px;
            border-right: 0.5pt solid #cbd5e1;
            font-size: 7pt;
            vertical-align: middle;
        }

        .kpi-bar td:last-child {
            border-right: none;
        }

        .kpi-label {
            color: #64748b;
            text-transform: uppercase;
            font-size: 6pt;
            font-weight: bold;
        }

        .kpi-val {
            font-weight: bold;
            color: #0f172a;
            font-size: 7pt;
            margin-left: 3px;
        }

        /* Tabel Matriks Utama */
        .matrix-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 6.8pt;
        }

        .matrix-table thead {
            display: table-header-group;
        }

        .matrix-table tfoot {
            display: table-row-group;
        }

        .matrix-table tr {
            page-break-inside: avoid;
        }

        .matrix-table th,
        .matrix-table td {
            border: 0.5pt solid #000000;
            padding: 2.5px 2px;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
        }

        .matrix-table thead th {
            background-color: #f1f5f9;
            color: #000000;
            font-weight: bold;
            font-size: 6.5pt;
            padding: 3px 1px;
            border: 0.5pt solid #000000;
        }

        .matrix-table thead th.th-weekend {
            background-color: #e2e8f0;
            color: #475569;
        }

        .matrix-table thead th.th-today {
            background-color: #eff6ff;
            color: #1d4ed8;
            font-weight: bold;
        }

        .matrix-table thead th.th-rekap {
            background-color: #f8fafc;
            color: #0f172a;
        }

        .matrix-table thead th.th-ba {
            background-color: #fef2f2;
            color: #dc2626;
        }

        .matrix-table th.th-date {
            min-width: 15px;
            padding: 2px 1px;
        }

        /* Lebar Kolom */
        .col-no {
            width: 22px;
            font-weight: bold;
            font-size: 7pt;
        }

        .col-nama {
            min-width: 180px;
            max-width: 250px;
            text-align: left !important;
            padding-left: 6px !important;
            padding-right: 4px !important;
            white-space: normal !important;
        }

        .col-rekap {
            width: 18px;
        }

        .col-persen {
            width: 28px;
        }

        /* Styling Sel Data */
        .pegawai-nama {
            font-weight: normal;
            color: #000000;
            font-size: 7pt;
            line-height: 1.25;
            word-wrap: break-word;
        }

        .pegawai-nama strong {
            font-weight: bold;
        }

        /* Simbol Presensi */
        .sym-hadir {
            font-weight: bold;
            color: #0f172a;
            font-size: 7.2pt;
        }

        .sym-izin {
            font-weight: bold;
            color: #b91c1c;
            font-size: 7.2pt;
        }

        .sym-sakit {
            font-weight: bold;
            color: #b91c1c;
            font-size: 7.2pt;
        }

        .sym-cuti {
            font-weight: bold;
            color: #c2410c;
            font-size: 7.2pt;
        }

        .sym-tugas {
            font-weight: bold;
            color: #0369a1;
            font-size: 7.2pt;
        }

        .sym-belum {
            font-weight: bold;
            color: #dc2626;
            font-size: 7.5pt;
            background-color: #fee2e2;
            padding: 1px 3px;
            border-radius: 2px;
        }

        .sym-empty {
            color: #cbd5e1;
            font-size: 6pt;
        }

        .bg-weekend {
            background-color: #f8fafc;
        }

        .row-alt {
            background-color: #ddeded;
        }

        /* Warna Nomor Pegawai Berdasarkan Kedisiplinan (Sesuai Format User) */
        .no-kuning {
            background-color: #ffff00 !important;
            color: #000000 !important;
            font-weight: bold;
        }

        .no-merah {
            background-color: #ff0000 !important;
            color: #ffffff !important;
            font-weight: bold;
        }

        .no-hijau {
            background-color: #22c55e !important;
            color: #ffffff !important;
            font-weight: bold;
        }

        .no-biru {
            background-color: #3b82f6 !important;
            color: #ffffff !important;
            font-weight: bold;
        }

        .no-hitam {
            background-color: #000000 !important;
            color: #ffffff !important;
            font-weight: bold;
        }

        .no-putih {
            background-color: #ffffff !important;
            color: #1e293b !important;
        }

        /* Footer Tabel Total */
        .matrix-table tfoot td {
            font-weight: bold;
            background-color: #f8fafc;
            font-size: 6.5pt;
            padding: 3px 1px;
        }

        .tfoot-label {
            text-align: left !important;
            padding-left: 5px !important;
            color: #0f172a;
            font-size: 6.8pt;
        }

        .tfoot-belum-absen {
            background-color: #fff5f5 !important;
            color: #dc2626 !important;
        }

        /* Bottom Section: Legenda & Tanda Tangan */
        .bottom-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            page-break-inside: avoid;
        }

        .bottom-table td {
            vertical-align: top;
            padding: 0;
            border: none;
        }

        .legenda-box {
            border: 0.5pt solid #cbd5e1;
            background-color: #f8fafc;
            padding: 5px 8px;
            font-size: 6.8pt;
            border-radius: 4px;
            line-height: 1.35;
        }

        .legenda-title {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 6pt;
            color: #475569;
            margin-bottom: 2px;
        }

        .legenda-item {
            margin-right: 10px;
            display: inline;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
            font-size: 7.5pt;
        }

        .signature-table td {
            width: 50%;
            padding: 0 10px;
            vertical-align: top;
            border: none;
        }

        .signature-space {
            height: 38px;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            color: #0f172a;
        }
    </style>
</head>
<body>

    <!-- KOP / HEADER LAPORAN -->
    <table class="kop-table">
        <tr>
            @if ($logoBase64)
                <td class="kop-logo">
                    <img src="{{ $logoBase64 }}" alt="Logo">
                </td>
            @endif
            <td class="kop-title">
                <div class="institution-name">YAYASAN PRIMA INSANI GARUT</div>
                <div class="report-title">MATRIKS PRESENSI HARIAN PEGAWAI</div>
                <div class="system-sub">Sistem Informasi Prima Insani Terpadu (SIPIT) &bull; Modul Kepegawaian</div>
            </td>
            <td class="kop-meta">
                <strong>Periode:</strong> {{ $selectedBulan?->nama_bulan ?? 'Semua Bulan' }}
                {{ $selectedBulan?->tahunAjaran ? '(' . $selectedBulan->tahunAjaran->nama_tahun_ajaran . ')' : '' }}<br>
                <strong>Kategori:</strong> {{ $selectedKategori?->nama_kategori ?? 'Semua Kategori Pegawai' }}<br>
                <strong>Rentang:</strong>
                @if ($summary['start_date'] && $summary['end_date'])
                    {{ $summary['start_date']->format('d/m/Y') }} - {{ $summary['end_date']->format('d/m/Y') }} (Cut-off 28)
                @else
                    Cut-off Tanggal 28
                @endif
                <br>
                <strong>Filter:</strong> {{ $onlyWorkdays ? 'Hanya Hari Kerja' : 'Semua Hari' }}
                @if ($filterStatus && $filterStatus !== 'all')
                    &bull; [{{ ucwords(str_replace('_', ' ', $filterStatus)) }}]
                @endif
                <br>
                <strong>Format:</strong> Kertas A3 Landscape &bull; <strong>Waktu Cetak:</strong> {{ $waktuCetak->translatedFormat('d F Y, H:i') }} WIB
            </td>
        </tr>
    </table>

    <!-- MINI SUMMARY BAR -->
    <table class="kpi-bar">
        <tr>
            <td>
                <span class="kpi-label">TOTAL PEGAWAI:</span>
                <span class="kpi-val">{{ $summary['total_pegawai'] }} Orang</span>
            </td>
            <td>
                <span class="kpi-label">PEGAWAI AKTIF:</span>
                <span class="kpi-val">{{ $summary['total_pegawai_aktif'] }} Orang</span>
            </td>
            <td>
                <span class="kpi-label">HARI DITAMPILKAN:</span>
                <span class="kpi-val">{{ $summary['total_hari_tampil'] }} Hari</span>
            </td>
            <td>
                <span class="kpi-label">BELUM ABSEN HARI INI:</span>
                <span class="kpi-val" style="color: {{ $summary['total_pegawai_belum_absen_hari_ini'] > 0 ? '#dc2626' : '#16a34a' }};">
                    {{ $summary['total_pegawai_belum_absen_hari_ini'] }} Orang
                </span>
            </td>
            <td>
                <span class="kpi-label">PEGAWAI DI LAPORAN:</span>
                <span class="kpi-val">{{ $pegawaiRows->count() }} Orang</span>
            </td>
        </tr>
    </table>

    <!-- TABEL MATRIKS PRESENSI HARIAN -->
    <table class="matrix-table">
        <thead>
            <tr>
                <th class="col-no" rowspan="2">No</th>
                <th class="col-nama" rowspan="2">Nama</th>
                @foreach ($daftarTanggal as $tgl)
                    <th class="th-date {{ $tgl['is_today'] ? 'th-today' : ($tgl['is_weekend'] ? 'th-weekend' : '') }}">
                        <div style="font-size: 5pt; opacity: 0.85;">{{ $tgl['day_short'] }}</div>
                        <div style="font-size: 6.5pt; font-weight: bold;">{{ $tgl['day'] }}</div>
                    </th>
                @endforeach
                <!-- Header Rekapitulasi Akumulasi -->
                <th class="th-rekap col-rekap" rowspan="2" title="Total Hadir (Hadir + Terlambat)">H</th>
                <th class="th-rekap col-rekap" rowspan="2" title="Total Izin">I</th>
                <th class="th-rekap col-rekap" rowspan="2" title="Total Sakit">S</th>
                <th class="th-rekap col-rekap" rowspan="2" title="Total Cuti">C</th>
                <th class="th-rekap col-rekap" rowspan="2" title="Total Tugas Luar">T</th>
                <th class="th-ba col-rekap" rowspan="2" title="Total Belum Absen">BA</th>
                <th class="th-rekap col-persen" rowspan="2" title="Persentase Kehadiran">%</th>
            </tr>
            <tr>
                @foreach ($daftarTanggal as $tgl)
                    <th class="th-date {{ $tgl['is_today'] ? 'th-today' : ($tgl['is_weekend'] ? 'th-weekend' : '') }}" style="font-size: 5pt; padding: 1px 0;">
                        {{ $tgl['month'] }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($pegawaiRows as $idx => $row)
                @php
                    $karyawan = $row['karyawan'];
                    $totals = $row['totals'];
                    $hasBelumAbsen = ($totals['belum_absen'] ?? 0) > 0;
                    $statusNomor = $row['status_nomor'] ?? [
                        'code'       => 'putih',
                        'bg_color'   => '#ffffff',
                        'text_color' => '#1e293b',
                    ];
                @endphp
                <tr class="{{ $idx % 2 === 0 ? 'row-alt' : '' }}">
                    <td class="col-no no-{{ $statusNomor['code'] }}" style="background-color: {{ $statusNomor['bg_color'] }} !important; color: {{ $statusNomor['text_color'] }} !important;">
                        {{ $idx + 1 }}
                    </td>
                    <td class="col-nama">
                        <div class="pegawai-nama">
                            @if ($karyawan->golongan)
                                <strong>({{ $karyawan->golongan }})</strong>
                            @endif
                            {{ $karyawan->nama_lengkap }}{{ $karyawan->gelar ? ', ' . $karyawan->gelar : '' }}
                        </div>
                    </td>

                    <!-- Sel Tanggal -->
                    @foreach ($daftarTanggal as $tgl)
                        @php
                            $dateStr = $tgl['date_string'];
                            $rec = $row['records'][$dateStr] ?? null;
                            $status = $rec['status'] ?? 'empty';
                            $symbol = $rec['symbol'] ?? '';
                        @endphp
                        <td class="{{ $tgl['is_weekend'] ? 'bg-weekend' : '' }}">
                            @if ($status === 'hadir' || $status === 'terlambat')
                                <span class="sym-hadir">v</span>
                            @elseif ($status === 'izin')
                                <span class="sym-izin">i</span>
                            @elseif ($status === 'sakit')
                                <span class="sym-sakit">s</span>
                            @elseif ($status === 'cuti')
                                <span class="sym-cuti">c</span>
                            @elseif ($status === 'tugas')
                                <span class="sym-tugas">t</span>
                            @elseif ($status === 'belum_absen')
                                <span class="sym-belum">-</span>
                            @else
                                <span class="sym-empty">&middot;</span>
                            @endif
                        </td>
                    @endforeach

                    <!-- Kolom Rekapitulasi -->
                    <td style="font-weight: bold; color: #0f172a;">{{ $totals['total_hadir_semua'] }}</td>
                    <td style="color: {{ $totals['izin'] > 0 ? '#b91c1c; font-weight: bold;' : '#64748b' }};">
                        {{ $totals['izin'] ?: '-' }}
                    </td>
                    <td style="color: {{ $totals['sakit'] > 0 ? '#b91c1c; font-weight: bold;' : '#64748b' }};">
                        {{ $totals['sakit'] ?: '-' }}
                    </td>
                    <td style="color: {{ $totals['cuti'] > 0 ? '#c2410c; font-weight: bold;' : '#64748b' }};">
                        {{ $totals['cuti'] ?: '-' }}
                    </td>
                    <td style="color: {{ $totals['tugas'] > 0 ? '#0369a1; font-weight: bold;' : '#64748b' }};">
                        {{ $totals['tugas'] ?: '-' }}
                    </td>
                    <td style="{{ $hasBelumAbsen ? 'background-color: #fee2e2; color: #dc2626; font-weight: bold;' : 'color: #94a3b8;' }}">
                        {{ $totals['belum_absen'] ?: '-' }}
                    </td>
                    <td style="font-weight: bold; font-size: 6.5pt; color: #0f172a;">
                        {{ $totals['persen_kehadiran'] }}%
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $daftarTanggal->count() + 9 }}" style="padding: 16px; text-align: center; color: #64748b;">
                        Tidak ada data pegawai yang sesuai dengan kriteria filter.
                    </td>
                </tr>
            @endforelse
        </tbody>

        <!-- FOOTER TOTAL HARIAN -->
        @if ($pegawaiRows->isNotEmpty())
            <tfoot>
                <tr>
                    <td class="col-no">&Sigma;</td>
                    <td class="tfoot-label">Total Hadir Harian</td>
                    @php
                        $grandTotalHadir = 0;
                    @endphp
                    @foreach ($daftarTanggal as $tgl)
                        @php
                            $dTot = $columnTotals[$tgl['date_string']] ?? null;
                            $totHadir = ($dTot['hadir'] ?? 0) + ($dTot['terlambat'] ?? 0);
                            $grandTotalHadir += $totHadir;
                        @endphp
                        <td style="color: #0f172a;">{{ $totHadir ?: '-' }}</td>
                    @endforeach
                    <td style="font-weight: bold; color: #0f172a;">{{ $grandTotalHadir }}</td>
                    <td colspan="6" style="background-color: #f1f5f9;"></td>
                </tr>
                <tr>
                    <td class="col-no tfoot-belum-absen">!</td>
                    <td class="tfoot-label tfoot-belum-absen">Total Belum Absen</td>
                    @php
                        $grandTotalBelum = 0;
                    @endphp
                    @foreach ($daftarTanggal as $tgl)
                        @php
                            $dTot = $columnTotals[$tgl['date_string']] ?? null;
                            $totBelum = $dTot['belum_absen'] ?? 0;
                            $grandTotalBelum += $totBelum;
                        @endphp
                        <td class="{{ $totBelum > 0 ? 'tfoot-belum-absen' : '' }}">
                            {{ $totBelum > 0 ? $totBelum : '-' }}
                        </td>
                    @endforeach
                    <td class="{{ $grandTotalBelum > 0 ? 'tfoot-belum-absen' : '' }}">{{ $grandTotalBelum ?: '-' }}</td>
                    <td colspan="6" style="background-color: #f1f5f9;"></td>
                </tr>
            </tfoot>
        @endif
    </table>

    <!-- LEGENDA & TANDA TANGAN (BOTTOM SECTION) -->
    <table class="bottom-table">
        <tr>
            <!-- Kolom Kiri: Keterangan & Legenda Simbol -->
            <td style="width: 58%; padding-right: 15px; vertical-align: top;">
                <div class="legenda-box">
                    <!-- Keterangan Warna Nomor Pegawai Sesuai Screenshot User -->
                    <div style="margin-bottom: 6px;">
                        <div style="font-weight: bold; font-size: 8.5pt; color: #000000; margin-bottom: 3px;">Keterangan:</div>
                        <table style="border-collapse: collapse; border: none; font-size: 6.8pt; line-height: 1.45;">
                            <tr>
                                <td style="width: 14px; vertical-align: middle; padding: 1px 0; border: none;">
                                    <span style="display: inline-block; width: 9px; height: 9px; background-color: #ffff00; border: 0.5pt solid #000000;"></span>
                                </td>
                                <td style="vertical-align: middle; padding: 1px 4px; border: none; color: #000000;">
                                    <strong>Kuning</strong> = Reward (Tidak ada keterlambatan) semua nol kecuali cuti tidak dihitung
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 14px; vertical-align: middle; padding: 1px 0; border: none;">
                                    <span style="display: inline-block; width: 9px; height: 9px; background-color: #ff0000; border: 0.5pt solid #000000;"></span>
                                </td>
                                <td style="vertical-align: middle; padding: 1px 4px; border: none; color: #000000;">
                                    <strong>Merah</strong> = 5 Kali Terlambat dalam 2 bulan berturut-turut
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 14px; vertical-align: middle; padding: 1px 0; border: none;">
                                    <span style="display: inline-block; width: 9px; height: 9px; background-color: #22c55e; border: 0.5pt solid #000000;"></span>
                                </td>
                                <td style="vertical-align: middle; padding: 1px 4px; border: none; color: #000000;">
                                    <strong>Hijau</strong> = 5 Kali Terlambat dalam 1 bulan
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 14px; vertical-align: middle; padding: 1px 0; border: none;">
                                    <span style="display: inline-block; width: 9px; height: 9px; background-color: #3b82f6; border: 0.5pt solid #000000;"></span>
                                </td>
                                <td style="vertical-align: middle; padding: 1px 4px; border: none; color: #000000;">
                                    <strong>Biru</strong> = 3 Kali Terlambat dalam 7 hari
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 14px; vertical-align: middle; padding: 1px 0; border: none;">
                                    <span style="display: inline-block; width: 9px; height: 9px; background-color: #000000; border: 0.5pt solid #000000;"></span>
                                </td>
                                <td style="vertical-align: middle; padding: 1px 4px; border: none; color: #000000;">
                                    <strong>Hitam</strong> = Tidak ada data presensi
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 14px; vertical-align: middle; padding: 1px 0; border: none;">
                                    <span style="display: inline-block; width: 9px; height: 9px; background-color: #ffffff; border: 0.5pt solid #000000;"></span>
                                </td>
                                <td style="vertical-align: middle; padding: 1px 4px; border: none; color: #000000;">
                                    <strong>Putih</strong> = Normal
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div style="border-top: 0.5pt dashed #94a3b8; padding-top: 4px; margin-top: 4px;">
                        <div class="legenda-title">Legenda Simbol Presensi:</div>
                        <div class="legenda-item"><strong>v</strong> = Hadir / Terlambat</div>
                        <div class="legenda-item"><strong style="color: #b91c1c;">i</strong> = Izin</div>
                        <div class="legenda-item"><strong style="color: #b91c1c;">s</strong> = Sakit</div>
                        <div class="legenda-item"><strong style="color: #c2410c;">c</strong> = Cuti</div>
                        <div class="legenda-item"><strong style="color: #0369a1;">t</strong> = Tugas Luar</div>
                        <div class="legenda-item"><strong style="color: #dc2626; background: #fee2e2; padding: 0 2px;">-</strong> = Belum Absen (Hari Kerja)</div>
                        <div class="legenda-item"><strong style="color: #64748b;">&middot;</strong> = Hari Libur / Belum Berjalan</div>
                    </div>

                    <div style="margin-top: 4px; font-size: 5.6pt; color: #64748b; font-style: italic;">
                        * Catatan: Penghitungan rekapitulasi presensi bulanan menggunakan cut-off tanggal 28. Hadir dihitung dari status Hadir dan Terlambat.
                    </div>
                </div>
            </td>

            <!-- Kolom Kanan: Lembar Pengesahan / Tanda Tangan -->
            <td style="width: 42%;">
                <table class="signature-table">
                    <tr>
                        <td>
                            Mengetahui,<br>
                            <strong>Pimpinan Lembaga / Kepala Sekolah</strong>
                            <div class="signature-space"></div>
                            <div class="signature-name">( _________________________ )</div>
                            <div style="font-size: 6.5pt; color: #64748b;">NIPY.</div>
                        </td>
                        <td>
                            Garut, {{ $waktuCetak->translatedFormat('d F Y') }}<br>
                            <strong>Petugas Presensi / Kepegawaian</strong>
                            <div class="signature-space"></div>
                            <div class="signature-name">( {{ auth()->user()->name ?? 'Administrator' }} )</div>
                            <div style="font-size: 6.5pt; color: #64748b;">SIPIT Yayasan Prima Insani</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- SKRIP PHP DOMPDF: NOMOR HALAMAN & WATERMARK FOOTER -->
    <script type="text/php">
    if (isset($pdf)) {
        $font = $fontMetrics->get_font("DejaVu Sans, Helvetica, sans-serif", "normal");
        $size = 6.5;
        $color = array(0.45, 0.45, 0.45);
        $w = $pdf->get_width();
        $h = $pdf->get_height();

        // Footer Kiri: Informasi Dokumen
        $pdf->page_text(18, $h - 20, "SIPIT - Sistem Informasi Prima Insani Terpadu | Laporan Matriks Presensi Harian (Kertas A3 Landscape)", $font, $size, $color);

        // Footer Kanan: Nomor Halaman
        $pageText = "Halaman {PAGE_NUM} dari {PAGE_COUNT}";
        $pdf->page_text($w - 85, $h - 20, $pageText, $font, $size, $color);
    }
    </script>

</body>
</html>
