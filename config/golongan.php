<?php

/**
 * Aturan Penggolongan Otomatis (berdasarkan lembar "Penghitungan Penggolongan").
 *
 * - Setiap `tahun_per_tingkat` tahun masa kerja, golongan naik 1 tingkat ruang
 *   (tahun 1-4 = ruang awal, 5-8 = ruang berikutnya, dst).
 * - `urutan` adalah urutan tingkat dari yang terendah ke tertinggi.
 * - `awal` menentukan golongan awal (tahun ke-1) berdasarkan nama jenjang pendidikan
 *   (sesuai tabel akademik_pelengkap_jenjang_pendidikan). Kunci dicocokkan tanpa
 *   membedakan huruf besar/kecil.
 */
return [
    'tahun_per_tingkat' => 4,

    'urutan' => [
        'I/A', 'I/B', 'I/C', 'I/D',
        'II/A', 'II/B', 'II/C', 'II/D',
        'III/A', 'III/B', 'III/C', 'III/D',
        'IV/A', 'IV/B', 'IV/C', 'IV/D', 'IV/E',
    ],

    'awal' => [
        'SD/MI'      => 'I/A',
        'SMP/MTs'    => 'I/C',
        'SMA/SMK/MA' => 'II/A',
        'D1/D2'      => 'II/B',
        'D3'         => 'II/C',
        'D4'         => 'III/A',
        'S1'         => 'III/A',
        'S2'         => 'III/B',
        'S3'         => 'III/B',
    ],

    // Jendela tampilan notifikasi di dashboard admin (hari)
    'notifikasi_hari_lalu'   => 30,
    'notifikasi_hari_depan'  => 60,
];
