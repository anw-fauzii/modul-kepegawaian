@extends('layouts.appui')

@section('title')
    <title>Profil Akun Saya - {{ $karyawan?->nama_lengkap ?? $user->name }}</title>
@endsection

@php
    $statusBadge = $karyawan?->status_pegawai_badge ?? [
        'label' => 'Aktif',
        'code'  => 'aktif',
        'class' => 'badge-success',
        'color' => '#10b981',
    ];

    $fullName = $karyawan?->nama_lengkap ?? $user->name ?? 'Pengguna SIPIT';
    $words = preg_split('/\s+/', trim($fullName));
    $initials = strtoupper(mb_substr($words[0] ?? 'U', 0, 1) . mb_substr($words[1] ?? '', 0, 1));

    $email = $user->email ?? $karyawan?->email;

    $cleanWa = null;
    if ($karyawan?->whatsapp) {
        $cleanWa = preg_replace('/[^0-9]/', '', $karyawan->whatsapp);
        if (str_starts_with($cleanWa, '0')) {
            $cleanWa = '62' . substr($cleanWa, 1);
        }
    }

    $activeTab = session('active_tab', ($errors->has('current_password') || $errors->has('password')) ? 'password' : 'profil');

    $tabs = [
        'profil'   => ['label' => 'Detail Profil & Kepegawaian', 'icon' => 'pe-7s-user',      'count' => null],
        'jabatan'  => ['label' => 'Histori Jabatan',             'icon' => 'pe-7s-portfolio', 'count' => $karyawan?->riwayat_jabatan_count ?? 0],
    ];
@endphp

@section('content')
<div class="kd">

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3" role="alert" style="border-radius: 10px;">
            <i class="fa fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3" role="alert" style="border-radius: 10px;">
            <div class="font-weight-bold mb-1"><i class="fa fa-exclamation-triangle mr-1"></i> Mohon periksa kembali input Anda:</div>
            <ul class="mb-0 pl-4 small">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- ============ HERO SECTION (MODERN & CLEAN) ============ --}}
    <section class="kd-hero">
        <div class="kd-hero__cover">
            <a href="{{ route('dashboard.user') }}" class="kd-hero__back">
                <i class="fa fa-arrow-left"></i>
                <span>Dashboard Pegawai</span>
            </a>
            <div class="kd-hero__badge-top">
                <i class="pe-7s-portfolio mr-1 text-orange-600"></i>
                <span>{{ $karyawan?->kategori?->nama_kategori ?? 'Akun Pengguna SIPIT' }}</span>
            </div>
        </div>

        <div class="kd-hero__body">
            <div class="kd-avatar {{ $karyawan?->foto_url ? 'kd-avatar--photo' : '' }}">
                @if ($karyawan?->foto_url)
                    <img class="kd-avatar__img" src="{{ $karyawan->foto_url }}" alt="Foto {{ $fullName }}" title="Klik untuk memperbesar">
                @else
                    {{ $initials }}
                @endif
                @if ($karyawan?->jk === 'L')
                    <span class="kd-avatar__sex kd-avatar__sex--l" title="Laki-laki"><i class="fa fa-mars"></i></span>
                @elseif ($karyawan?->jk === 'P')
                    <span class="kd-avatar__sex kd-avatar__sex--p" title="Perempuan"><i class="fa fa-venus"></i></span>
                @endif
            </div>

            <div class="kd-hero__info">
                <div class="kd-hero__title-row">
                    <h1 class="kd-hero__name">
                        {{ $fullName }}@if ($karyawan?->gelar)<span class="kd-hero__gelar">, {{ $karyawan->gelar }}</span>@endif
                    </h1>
                    @if($karyawan)
                        <span class="kd-status-pill" style="--status-color: {{ $statusBadge['color'] }};">
                            <span class="kd-status-pill__dot"></span>
                            <span>Pegawai {{ $statusBadge['label'] }}</span>
                        </span>
                    @else
                        <span class="kd-status-pill" style="--status-color: #10b981;">
                            <span class="kd-status-pill__dot"></span>
                            <span>Akun Sistem Aktif</span>
                        </span>
                    @endif
                </div>

                <div class="kd-hero__subline">
                    <span class="kd-subline-item font-weight-bold text-dark">
                        <i class="pe-7s-portfolio text-orange-600 mr-1"></i>
                        {{ $karyawan?->jabatan ?: 'Administrator / Pengguna' }}
                    </span>
                    <span class="kd-subline-dot">&bull;</span>
                    <span class="kd-subline-item text-muted">
                        <i class="pe-7s-culture text-orange-600 mr-1"></i>
                        {{ $karyawan?->unit ?: 'Yayasan Prima Insani' }}
                    </span>
                    @if ($karyawan?->lama_kerja)
                        <span class="kd-subline-dot">&bull;</span>
                        <span class="kd-subline-item text-orange-700">
                            <i class="fa fa-business-time text-orange-500 mr-1"></i>
                            {{ $karyawan->lama_kerja }}
                        </span>
                    @endif
                </div>

                <div class="kd-hero__pills">
                    <span class="kd-pill-meta">
                        <i class="pe-7s-id text-orange-600"></i>
                        <span>NIPY: <strong class="ml-1 text-dark">{{ $karyawan?->nipy ?? $user->no_induk ?? '-' }}</strong></span>
                        @if($karyawan?->nipy || $user->no_induk)
                            <button type="button" class="kd-copy-btn" data-clipboard="{{ $karyawan?->nipy ?? $user->no_induk }}" title="Salin NIPY">
                                <i class="fa fa-copy"></i>
                            </button>
                        @endif
                    </span>
                    @if ($email)
                        <a href="mailto:{{ $email }}" class="kd-pill-meta kd-pill-meta--link" title="Kirim Email">
                            <i class="pe-7s-mail text-orange-600"></i>
                            <span>{{ $email }}</span>
                        </a>
                    @endif
                    @if ($cleanWa)
                        <a href="https://wa.me/{{ $cleanWa }}" target="_blank" rel="noopener" class="kd-pill-meta kd-pill-meta--wa" title="WhatsApp">
                            <i class="fab fa-whatsapp text-success"></i>
                            <span>{{ $karyawan->whatsapp }}</span>
                        </a>
                    @endif
                </div>
            </div>

            <div class="kd-hero__actions">
                <button type="button" class="kd-btn-modern kd-btn-modern--ghost" onclick="window.print()" title="Cetak Profil Pegawai">
                    <i class="fa fa-print"></i>
                    <span>Cetak</span>
                </button>
                <button type="button" class="kd-btn-modern kd-btn-modern--primary" data-kd-tab="password" title="Ubah Kata Sandi Akun">
                    <i class="fa fa-key"></i>
                    <span>Ubah Password</span>
                </button>
            </div>
        </div>

        {{-- Stat Strip Ringkasan Portofolio (Ramping & Modern) --}}
        @if($karyawan)
            <div class="kd-stats-bar">
                <div class="kd-stat-card">
                    <div class="kd-stat-card__icon bg-orange-soft text-orange">
                        <i class="pe-7s-box2"></i>
                    </div>
                    <div class="kd-stat-card__info">
                        <span class="kd-stat-card__num">{{ $karyawan->fasilitas_count ?? 0 }}</span>
                        <span class="kd-stat-card__lbl">Fasilitas</span>
                    </div>
                </div>
                <div class="kd-stat-card">
                    <div class="kd-stat-card__icon bg-blue-soft text-blue">
                        <i class="pe-7s-culture"></i>
                    </div>
                    <div class="kd-stat-card__info">
                        <span class="kd-stat-card__num">{{ $karyawan->diklat_count ?? 0 }}</span>
                        <span class="kd-stat-card__lbl">Diklat</span>
                    </div>
                </div>
                <div class="kd-stat-card">
                    <div class="kd-stat-card__icon bg-emerald-soft text-emerald">
                        <i class="pe-7s-graph1"></i>
                    </div>
                    <div class="kd-stat-card__info">
                        <span class="kd-stat-card__num">{{ $karyawan->rapot_kinerja_count ?? 0 }}</span>
                        <span class="kd-stat-card__lbl">Rapor</span>
                    </div>
                </div>
                <div class="kd-stat-card">
                    <div class="kd-stat-card__icon bg-purple-soft text-purple">
                        <i class="pe-7s-news-paper"></i>
                    </div>
                    <div class="kd-stat-card__info">
                        <span class="kd-stat-card__num">{{ $karyawan->surat_keputusan_count ?? 0 }}</span>
                        <span class="kd-stat-card__lbl">SK</span>
                    </div>
                </div>
                <div class="kd-stat-card">
                    <div class="kd-stat-card__icon bg-amber-soft text-amber">
                        <i class="pe-7s-medal"></i>
                    </div>
                    <div class="kd-stat-card__info">
                        <span class="kd-stat-card__num">{{ $karyawan->sertifikat_penghargaan_count ?? 0 }}</span>
                        <span class="kd-stat-card__lbl">Penghargaan</span>
                    </div>
                </div>
                <div class="kd-stat-card">
                    <div class="kd-stat-card__icon bg-slate-soft text-slate">
                        <i class="pe-7s-note2"></i>
                    </div>
                    <div class="kd-stat-card__info">
                        <span class="kd-stat-card__num">{{ $karyawan->tupoksi_count ?? 0 }}</span>
                        <span class="kd-stat-card__lbl">Tupoksi</span>
                    </div>
                </div>
            </div>
        @endif
    </section>

    {{-- ============ STICKY NAV TABS ============ --}}
    <nav class="kd-tabs" role="tablist">
        @foreach ($tabs as $key => $tab)
            <button type="button" class="kd-tab {{ $activeTab === $key ? 'is-active' : '' }}" data-kd-tab="{{ $key }}" role="tab">
                <i class="{{ $tab['icon'] }}"></i>
                <span>{{ $tab['label'] }}</span>
                @if ($tab['count'] !== null && $tab['count'] > 0)
                    <span class="kd-tab__count">{{ $tab['count'] }}</span>
                @endif
            </button>
        @endforeach
    </nav>

    {{-- ============ PANE 1: DETAIL PROFIL & KEPEGAWAIAN ============ --}}
    <div class="kd-pane {{ $activeTab === 'profil' ? 'is-active' : '' }}" data-kd-pane="profil">
        <div class="kd-grid">
            <div class="kd-stack">
                {{-- Row 1: Identitas Pribadi & Kepegawaian --}}
                <div class="kd-subgrid-2">
                    {{-- 1. Identitas Pribadi --}}
                    <div class="kd-card">
                        <div class="kd-card__head">
                            <span class="kd-card__icon"><i class="pe-7s-id"></i></span>
                            <h2 class="kd-card__title">1. Identitas Pribadi</h2>
                        </div>
                        <dl class="kd-dl kd-dl--compact">
                            <div>
                                <dt>NIPY</dt>
                                <dd class="text-primary font-weight-bold">{{ $karyawan?->nipy ?? $user->no_induk ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt>NIK</dt>
                                <dd>{{ $karyawan?->nik ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt>Nama</dt>
                                <dd class="kd-strong">{{ $fullName }}</dd>
                            </div>
                            <div>
                                <dt>Gelar</dt>
                                <dd>{{ $karyawan?->gelar ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt>Jenis Kelamin</dt>
                                <dd>
                                    @if ($karyawan?->jk === 'L')
                                        <span class="kd-chip kd-chip--blue"><i class="fa fa-mars"></i> Laki-laki</span>
                                    @elseif ($karyawan?->jk === 'P')
                                        <span class="kd-chip kd-chip--red"><i class="fa fa-venus"></i> Perempuan</span>
                                    @else
                                        -
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt>TTL</dt>
                                <dd>
                                    {{ $karyawan?->tempat_lahir ?: '-' }},
                                    {{ $karyawan?->tanggal_lahir?->translatedFormat('d M Y') ?? '-' }}
                                    @if ($karyawan?->tanggal_lahir)
                                        <span class="kd-muted">({{ $karyawan->tanggal_lahir->age }} thn)</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>

                    {{-- 2. Kepegawaian & Tugas --}}
                    <div class="kd-card">
                        <div class="kd-card__head">
                            <span class="kd-card__icon"><i class="pe-7s-portfolio"></i></span>
                            <h2 class="kd-card__title">2. Kepegawaian &amp; Tugas</h2>
                        </div>
                        <dl class="kd-dl kd-dl--compact">
                            <div>
                                <dt>Kategori</dt>
                                <dd>
                                    <span class="kd-chip kd-chip--orange">
                                        {{ $karyawan?->kategori?->nama_kategori ?? '-' }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt>Jabatan</dt>
                                <dd class="kd-strong">{{ $karyawan?->jabatan ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt>Golongan</dt>
                                <dd>{{ $karyawan?->golongan ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt>Unit</dt>
                                <dd class="kd-strong">{{ $karyawan?->unit ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt>Mulai Tugas</dt>
                                <dd>{{ $karyawan?->mulai_tugas?->translatedFormat('d M Y') ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt>Lama Kerja</dt>
                                <dd>
                                    <span class="kd-chip kd-chip--orange">
                                        <i class="fa fa-business-time"></i> {{ $karyawan?->lama_kerja ?? '-' }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt>Status NIPY</dt>
                                <dd>
                                    @if($karyawan)
                                        <span class="kd-chip" style="--chip: {{ $statusBadge['color'] }};">
                                            <span class="kd-chip__dot"></span> {{ $statusBadge['label'] }} ({{ substr($karyawan->nipy, -2) }})
                                        </span>
                                    @else
                                        <span class="kd-chip kd-chip--green"><span class="kd-chip__dot"></span> Aktif</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                {{-- Row 2: Pendidikan Terakhir & Alamat Domisili --}}
                <div class="kd-subgrid-2">
                    {{-- 3. Pendidikan Terakhir --}}
                    <div class="kd-card">
                        <div class="kd-card__head">
                            <span class="kd-card__icon"><i class="pe-7s-study"></i></span>
                            <h2 class="kd-card__title">3. Pendidikan Terakhir</h2>
                        </div>
                        <dl class="kd-dl kd-dl--compact">
                            <div>
                                <dt>Jenjang</dt>
                                <dd class="kd-strong">{{ $karyawan?->jenjangPendidikan?->nama_jenjang_pendidikan ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt>Perguruan</dt>
                                <dd>{{ $karyawan?->perguruan ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt>Prodi</dt>
                                <dd>{{ $karyawan?->prodi ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt>Tahun Lulus</dt>
                                <dd>{{ $karyawan?->tahun_lulus ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt>NUPTK</dt>
                                <dd>{{ $karyawan?->nuptk ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt>Sertifikasi</dt>
                                <dd>
                                    @if ($karyawan?->sertifikasi)
                                        <span class="kd-chip kd-chip--green"><i class="fa fa-check"></i> Sudah</span>
                                    @else
                                        <span class="kd-chip kd-chip--neutral"><i class="fa fa-minus"></i> Belum</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>

                    {{-- 4. Alamat & Domisili --}}
                    <div class="kd-card">
                        <div class="kd-card__head">
                            <span class="kd-card__icon"><i class="pe-7s-map-marker"></i></span>
                            <h2 class="kd-card__title">4. Alamat &amp; Domisili</h2>
                        </div>
                        <dl class="kd-dl kd-dl--compact">
                            <div>
                                <dt>Alamat</dt>
                                <dd class="kd-strong" title="{{ $karyawan?->alamat }}">{{ $karyawan?->alamat ?: 'Belum diisi' }}</dd>
                            </div>
                            <div>
                                <dt>RT / RW</dt>
                                <dd>{{ $karyawan?->rt ?: '-' }} / {{ $karyawan?->rw ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt>Kelurahan</dt>
                                <dd>{{ $karyawan?->kelurahan ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt>Kecamatan</dt>
                                <dd>{{ $karyawan?->kecamatan ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt>Kota/Kab.</dt>
                                <dd>{{ $karyawan?->kabupaten ?: '-' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            {{-- Sidebar Column on Profil Tab --}}
            <aside class="kd-stack">
                {{-- Kontak Pegawai --}}
                <div class="kd-card">
                    <div class="kd-card__head">
                        <span class="kd-card__icon"><i class="pe-7s-call"></i></span>
                        <h2 class="kd-card__title">Kontak Saya</h2>
                    </div>
                    <div class="kd-contact">
                        <a class="kd-contact__item {{ $email ? '' : 'is-empty' }}" href="{{ $email ? 'mailto:' . $email : '#' }}">
                            <span class="kd-contact__icon"><i class="pe-7s-mail"></i></span>
                            <span>
                                <small>Email Akun</small>
                                <strong>{{ $email ?: '-' }}</strong>
                            </span>
                        </a>
                        <a class="kd-contact__item {{ $cleanWa ? '' : 'is-empty' }}" href="{{ $cleanWa ? 'https://wa.me/' . $cleanWa : '#' }}" target="_blank" rel="noopener">
                            <span class="kd-contact__icon kd-contact__icon--wa"><i class="fab fa-whatsapp"></i></span>
                            <span>
                                <small>No. WhatsApp</small>
                                <strong>{{ $karyawan?->whatsapp ?: '-' }}</strong>
                            </span>
                        </a>
                    </div>
                </div>

                {{-- Jadwal Jam Kerja --}}
                <div class="kd-card">
                    <div class="kd-card__head">
                        <span class="kd-card__icon"><i class="pe-7s-clock"></i></span>
                        <div class="d-flex align-items-center justify-content-between flex-grow-1">
                            <h2 class="kd-card__title">Jam Kerja</h2>
                            @if ($karyawan?->jamKerja)
                                <span class="kd-badge-ghost">{{ $karyawan->jamKerja->nama }}</span>
                            @endif
                        </div>
                    </div>
                    @if ($karyawan?->jamKerja)
                        <div class="kd-hours">
                            <div>
                                <small>Masuk</small>
                                <strong>{{ substr($karyawan->jamKerja->jam_masuk, 0, 5) }}</strong>
                                <span class="kd-xs kd-muted">WIB</span>
                            </div>
                            <div>
                                <small>Senin – Kamis</small>
                                <strong>{{ substr($karyawan->jamKerja->jam_pulang_senin_kamis, 0, 5) }}</strong>
                                <span class="kd-xs kd-muted">WIB</span>
                            </div>
                            <div>
                                <small>Jumat</small>
                                <strong>{{ substr($karyawan->jamKerja->jam_pulang_jumat, 0, 5) }}</strong>
                                <span class="kd-xs kd-muted">WIB</span>
                            </div>
                        </div>
                    @else
                        <p class="kd-muted kd-pad text-center" style="padding: 10px; font-size: 0.78rem;">Jadwal jam kerja standar (07:00 - 15:00).</p>
                    @endif
                </div>

                {{-- Akun Sistem & SSO --}}
                <div class="kd-card">
                    <div class="kd-card__head">
                        <span class="kd-card__icon"><i class="pe-7s-key"></i></span>
                        <h2 class="kd-card__title">Akun Sistem (SSO)</h2>
                    </div>
                    <dl class="kd-dl kd-dl--compact">
                        <div>
                            <dt>Username</dt>
                            <dd class="kd-strong">{{ $user->no_induk ?? $karyawan?->nipy ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt>Nama Akun</dt>
                            <dd>{{ $user->name }}</dd>
                        </div>
                        <div>
                            <dt>Email Login</dt>
                            <dd class="kd-clip" title="{{ $user->email }}">{{ $user->email }}</dd>
                        </div>
                        <div>
                            <dt>Status SSO</dt>
                            <dd>
                                <span class="kd-chip kd-chip--green"><span class="kd-chip__dot"></span> Terhubung</span>
                            </dd>
                        </div>
                    </dl>
                </div>
            </aside>
        </div>
    </div>

    {{-- ============ PANE 2: HISTORI JABATAN ============ --}}
    <div class="kd-pane {{ $activeTab === 'jabatan' ? 'is-active' : '' }}" data-kd-pane="jabatan">
        <div class="kd-card">
            <div class="kd-card__head">
                <span class="kd-card__icon"><i class="pe-7s-portfolio"></i></span>
                <div>
                    <h2 class="kd-card__title">Histori &amp; Riwayat Jabatan Pegawai</h2>
                    <p class="kd-card__sub">Catatan kronologis perjalanan jabatan, unit kerja, dan kategori kepegawaian Anda</p>
                </div>
            </div>

            @if ($karyawan && $karyawan->riwayatJabatan && $karyawan->riwayatJabatan->isNotEmpty())
                <div class="kd-table-wrap">
                    <table class="kd-table">
                        <thead>
                            <tr>
                                <th style="width: 48px;">No</th>
                                <th>Jabatan</th>
                                <th>Unit Kerja</th>
                                <th>Kategori</th>
                                <th>Nomor SK</th>
                                <th>Periode Menjabat</th>
                                <th class="kd-center">Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($karyawan->riwayatJabatan as $rj)
                                <tr>
                                    <td class="kd-muted">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="kd-strong">{{ $rj->jabatan }}</div>
                                    </td>
                                    <td>{{ $rj->unit ?: '-' }}</td>
                                    <td>
                                        @if ($rj->kategori)
                                            <span class="kd-chip kd-chip--orange">{{ $rj->kategori->nama_kategori }}</span>
                                        @else
                                            <span class="kd-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($rj->nomor_sk)
                                            <span class="kd-code">{{ $rj->nomor_sk }}</span>
                                        @else
                                            <span class="kd-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="kd-nowrap">
                                        {{ $rj->tanggal_mulai ? $rj->tanggal_mulai->translatedFormat('d M Y') : '-' }}
                                        <span class="kd-muted mx-1">&ndash;</span>
                                        @if ($rj->is_aktif)
                                            <span class="text-success font-weight-bold">Sekarang</span>
                                        @else
                                            {{ $rj->tanggal_selesai ? $rj->tanggal_selesai->translatedFormat('d M Y') : '-' }}
                                        @endif
                                    </td>
                                    <td class="kd-center">
                                        @if ($rj->is_aktif)
                                            <span class="kd-chip kd-chip--green"><span class="kd-chip__dot"></span> Jabatan Aktif</span>
                                        @else
                                            <span class="kd-chip kd-chip--neutral">Riwayat</span>
                                        @endif
                                    </td>
                                    <td class="kd-muted">{{ $rj->keterangan ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="kd-empty">
                    <div class="kd-empty__icon"><i class="pe-7s-portfolio"></i></div>
                    <h3>Belum Ada Histori Jabatan</h3>
                    <p>Catatan riwayat perubahan jabatan Anda akan tampil secara otomatis di sini.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ============ PANE 3: KEAMANAN & UBAH PASSWORD ============ --}}
    <div class="kd-pane {{ $activeTab === 'password' ? 'is-active' : '' }}" data-kd-pane="password">
        <div class="kd-grid">
            <div class="kd-card">
                <div class="kd-card__head">
                    <span class="kd-card__icon"><i class="pe-7s-lock"></i></span>
                    <div>
                        <h2 class="kd-card__title">Ubah Password Akun</h2>
                        <p class="kd-card__sub">Perbarui kata sandi masuk (login) untuk keamanan akun PION Anda</p>
                    </div>
                </div>
                <div class="p-4">
                    <form action="{{ route('profile.password') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            @if(!empty($user->password))
                                <div class="col-md-12 form-group mb-3">
                                    <label class="font-weight-bold small text-dark mb-1">Password Saat Ini <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" name="current_password" id="current_password" class="form-control" placeholder="Masukkan password saat ini" required>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary toggle-pwd" type="button" data-target="current_password">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="col-md-6 form-group mb-3">
                                <label class="font-weight-bold small text-dark mb-1">Password Baru <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" name="password" id="new_password" class="form-control" placeholder="Minimal 6 karakter" required minlength="6">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary toggle-pwd" type="button" data-target="new_password">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 form-group mb-3">
                                <label class="font-weight-bold small text-dark mb-1">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Ulangi password baru" required minlength="6">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary toggle-pwd" type="button" data-target="password_confirmation">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-2 pt-3 border-top">
                            <button type="submit" class="kd-btn kd-btn--primary">
                                <i class="fa fa-key"></i> Simpan Password Baru
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <aside class="kd-stack">
                <div class="kd-card">
                    <div class="kd-card__head">
                        <span class="kd-card__icon"><i class="pe-7s-shield"></i></span>
                        <h2 class="kd-card__title">Tips Keamanan Akun</h2>
                    </div>
                    <div class="p-3 small text-muted" style="line-height: 1.6;">
                        <div class="mb-2 d-flex align-items-start gap-2">
                            <i class="fa fa-check-circle text-success mt-1"></i>
                            <span>Gunakan minimal <strong>6 karakter</strong> dengan kombinasi huruf besar, huruf kecil, dan angka.</span>
                        </div>
                        <div class="mb-2 d-flex align-items-start gap-2">
                            <i class="fa fa-check-circle text-success mt-1"></i>
                            <span>Jangan gunakan NIPY atau tanggal lahir sebagai password akun Anda.</span>
                        </div>
                        <div class="d-flex align-items-start gap-2">
                            <i class="fa fa-info-circle text-primary mt-1"></i>
                            <span>Jika terdapat ketidaksesuaian pada biodata kepegawaian, silakan hubungi <strong>Admin Kepegawaian</strong>.</span>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>

</div>
@endsection

@push('css')
<style>
    .kd {
        --kd-primary: #ea580c;
        --kd-primary-dark: #c2410c;
        --kd-primary-2: #d97706;
        --kd-grad: linear-gradient(135deg, #ea580c 0%, #d97706 100%);
        --kd-soft: #fff7ed;
        --kd-soft-2: #ffedd5;
        --kd-ink: #0f172a;
        --kd-text: #334155;
        --kd-muted: #64748b;
        --kd-line: #e2e8f0;
        --kd-radius: 12px;
        color: var(--kd-text);
        font-size: 0.85rem;
    }
    .kd *, .kd *::before, .kd *::after { box-sizing: border-box; }
    .kd h1, .kd h2, .kd h3, .kd p, .kd dl, .kd dd { margin: 0; }
    .kd a { text-decoration: none; }

    /* ---------- MODERN HERO SECTION ---------- */
    .kd-hero {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        overflow: hidden;
        margin-bottom: 14px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04), 0 6px 16px -4px rgba(234, 88, 12, 0.04);
    }
    .kd-hero__cover {
        height: 74px;
        background:
            radial-gradient(circle at 10% 20%, rgba(255,255,255,.2) 0%, transparent 40%),
            linear-gradient(135deg, #ea580c 0%, #f97316 55%, #fb923c 100%);
        padding: 10px 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
    }
    .kd-hero__back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #ffffff !important;
        font-size: 0.74rem;
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.28);
        transition: all 0.2s ease;
    }
    .kd-hero__back:hover {
        background: rgba(255, 255, 255, 0.32);
        transform: translateX(-2px);
    }
    .kd-hero__badge-top {
        display: inline-flex;
        align-items: center;
        color: #9a3412;
        background: #ffffff;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 999px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid #ffedd5;
    }
    .kd-hero__body {
        display: flex;
        align-items: flex-end;
        gap: 16px;
        padding: 0 20px 16px;
        flex-wrap: wrap;
    }
    .kd-avatar {
        position: relative;
        width: 78px;
        height: 78px;
        flex-shrink: 0;
        margin-top: -39px;
        border-radius: 18px;
        background: linear-gradient(135deg, #ea580c 0%, #d97706 100%);
        color: #ffffff;
        font-size: 1.6rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 4px solid #ffffff;
        box-shadow: 0 8px 20px -4px rgba(234, 88, 12, 0.35);
    }
    .kd-avatar--photo {
        width: 84px;
        height: 104px;
        margin-top: -52px;
        background: #f8fafc;
        border-radius: 16px;
        border: 4px solid #ffffff;
    }
    .kd-avatar__img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center top;
        border-radius: 12px;
        cursor: zoom-in;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .kd-avatar__img:hover {
        transform: scale(1.03);
    }
    .kd-lightbox {
        position: fixed;
        inset: 0;
        z-index: 2000;
        display: none;
        align-items: center;
        justify-content: center;
        background: rgba(15, 23, 42, 0.82);
        backdrop-filter: blur(4px);
        cursor: zoom-out;
    }
    .kd-lightbox.is-open { display: flex; }
    .kd-lightbox img {
        max-width: 90vw;
        max-height: 88vh;
        border-radius: 14px;
        border: 4px solid #ffffff;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
    }
    .kd-lightbox button {
        position: absolute;
        top: 18px;
        right: 24px;
        background: none;
        border: 0;
        color: #ffffff;
        font-size: 2.2rem;
        line-height: 1;
        cursor: pointer;
    }
    .kd-avatar__sex {
        position: absolute;
        right: -3px;
        bottom: -3px;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        border: 2px solid #ffffff;
        color: #ffffff;
        font-size: 0.62rem;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .kd-avatar__sex--l { background: #0284c7; }
    .kd-avatar__sex--p { background: #ec4899; }

    .kd-hero__info {
        flex: 1;
        min-width: 260px;
        padding-top: 8px;
    }
    .kd-hero__title-row {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px 12px;
        margin-bottom: 4px;
    }
    .kd-hero__name {
        font-size: 1.35rem;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.02em;
        line-height: 1.25;
    }
    .kd-hero__gelar {
        font-weight: 600;
        color: #64748b;
    }
    .kd-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 0.68rem;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 999px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #334155;
    }
    .kd-status-pill__dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background-color: var(--status-color, #10b981);
    }
    .kd-hero__subline {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 6px 8px;
        font-size: 0.8125rem;
        margin-bottom: 8px;
    }
    .kd-subline-item {
        display: inline-flex;
        align-items: center;
    }
    .kd-subline-dot {
        color: #cbd5e1;
        font-size: 0.75rem;
    }
    .kd-hero__pills {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 6px 10px;
    }
    .kd-pill-meta {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 4px 10px;
        font-size: 0.76rem;
        color: #475569;
        font-weight: 500;
        transition: all 0.15s ease;
    }
    .kd-pill-meta--link:hover {
        background: #fff7ed;
        border-color: #fdba74;
        color: #ea580c !important;
    }
    .kd-pill-meta--wa:hover {
        background: #f0fdf4;
        border-color: #86efac;
        color: #16a34a !important;
    }
    .kd-copy-btn {
        border: 0;
        background: transparent;
        color: #94a3b8;
        padding: 0 2px;
        cursor: pointer;
        font-size: 0.72rem;
        transition: color 0.15s ease;
    }
    .kd-copy-btn:hover {
        color: #ea580c;
    }
    .kd-hero__actions {
        display: flex;
        align-items: center;
        gap: 8px;
        padding-top: 8px;
    }
    .kd-btn-modern {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        font-size: 0.78rem;
        font-weight: 700;
        padding: 7px 14px;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none !important;
    }
    .kd-btn-modern--ghost {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        color: #475569;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
    }
    .kd-btn-modern--ghost:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #0f172a;
        transform: translateY(-1px);
    }
    .kd-btn-modern--primary {
        background: linear-gradient(135deg, #ea580c 0%, #f97316 100%);
        border: 0;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(234, 88, 12, 0.28);
    }
    .kd-btn-modern--primary:hover {
        background: linear-gradient(135deg, #c2410c 0%, #ea580c 100%);
        box-shadow: 0 6px 16px rgba(234, 88, 12, 0.38);
        transform: translateY(-1px);
    }

    /* ---------- MODERN STATS BAR ---------- */
    .kd-stats-bar {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        border-top: 1px solid #f1f5f9;
        background: #fafbfc;
        padding: 8px 12px;
        gap: 8px;
    }
    .kd-stat-card {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 6px 10px;
        border-radius: 10px;
        background: #ffffff;
        border: 1px solid #f1f5f9;
        transition: all 0.15s ease;
    }
    .kd-stat-card:hover {
        border-color: #fed7aa;
        background: #fffdfb;
        transform: translateY(-1px);
    }
    .kd-stat-card__icon {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
    }
    .bg-orange-soft { background: #fff7ed; color: #ea580c; }
    .bg-blue-soft   { background: #eff6ff; color: #2563eb; }
    .bg-emerald-soft{ background: #f0fdf4; color: #16a34a; }
    .bg-purple-soft { background: #faf5ff; color: #9333ea; }
    .bg-amber-soft  { background: #fffbeb; color: #d97706; }
    .bg-slate-soft  { background: #f1f5f9; color: #475569; }
    .kd-stat-card__info {
        display: flex;
        flex-direction: column;
        line-height: 1.15;
        min-width: 0;
    }
    .kd-stat-card__num {
        font-size: 0.98rem;
        font-weight: 800;
        color: #0f172a;
    }
    .kd-stat-card__lbl {
        font-size: 0.67rem;
        color: #64748b;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* ---------- STICKY TABS ---------- */
    .kd-tabs {
        position: sticky;
        top: 8px;
        z-index: 40;
        display: flex;
        gap: 4px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(12px);
        border: 1px solid var(--kd-line);
        border-radius: var(--kd-radius);
        padding: 5px;
        margin-bottom: 12px;
        box-shadow: 0 3px 12px -2px rgba(15, 23, 42, 0.05);
        overflow-x: auto;
    }
    .kd-tab {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        border: 0;
        border-radius: 8px;
        background: transparent;
        color: #64748b;
        font-size: 0.81rem;
        font-weight: 700;
        white-space: nowrap;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .kd-tab i { font-size: 0.95rem; }
    .kd-tab:hover {
        background: #f8fafc;
        color: var(--kd-ink);
    }
    .kd-tab.is-active {
        background: var(--kd-soft);
        color: var(--kd-primary);
        box-shadow: inset 0 0 0 1px #fed7aa;
    }
    .kd-tab__count {
        min-width: 18px;
        padding: 0 5px;
        height: 18px;
        line-height: 18px;
        border-radius: 999px;
        background: #f1f5f9;
        color: #64748b;
        font-size: 0.65rem;
        font-weight: 700;
        text-align: center;
    }
    .kd-tab.is-active .kd-tab__count {
        background: var(--kd-primary);
        color: #ffffff;
    }

    /* ---------- TAB PANES & GRID ---------- */
    .kd-pane { display: none; }
    .kd-pane.is-active {
        display: block;
        animation: kdFadeIn 0.2s ease;
    }
    @keyframes kdFadeIn {
        from { opacity: 0; transform: translateY(4px); }
        to { opacity: 1; transform: none; }
    }
    .kd-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 290px;
        gap: 12px;
        align-items: start;
    }
    .kd-stack {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .kd-subgrid-2 {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }

    /* ---------- CARD & DEFINITION LIST ---------- */
    .kd-card {
        background: #ffffff;
        border: 1px solid var(--kd-line);
        border-radius: var(--kd-radius);
        overflow: hidden;
        box-shadow: 0 1px 2px 0 rgba(15, 23, 42, 0.03);
    }
    .kd-card__head {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        border-bottom: 1px solid var(--kd-line);
        background: #ffffff;
    }
    .kd-card__icon {
        width: 28px;
        height: 28px;
        border-radius: 7px;
        flex-shrink: 0;
        background: var(--kd-soft);
        color: var(--kd-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
    }
    .kd-card__title {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--kd-ink);
    }
    .kd-card__sub {
        font-size: 0.72rem;
        color: var(--kd-muted);
        margin-top: 1px;
    }
    .kd-badge-ghost {
        font-size: 0.68rem;
        color: #64748b;
        background: #f1f5f9;
        padding: 2px 7px;
        border-radius: 4px;
        font-weight: 600;
    }

    .kd-dl > div {
        display: grid;
        grid-template-columns: 110px 1fr;
        gap: 8px;
        padding: 7px 14px;
        border-bottom: 1px solid #f6f7f9;
        align-items: center;
    }
    .kd-dl > div:last-child { border-bottom: 0; }
    .kd-dl dt {
        color: #64748b;
        font-weight: 600;
        font-size: 0.75rem;
    }
    .kd-dl dd {
        color: var(--kd-ink);
        font-weight: 500;
        word-break: break-word;
        font-size: 0.81rem;
    }

    /* ---------- CONTACT & HOURS ---------- */
    .kd-contact { padding: 6px; }
    .kd-contact__item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 7px 10px;
        border-radius: 7px;
        color: var(--kd-ink) !important;
        transition: background 0.15s;
    }
    .kd-contact__item:hover { background: #f8fafc; }
    .kd-contact__item.is-empty { pointer-events: none; }
    .kd-contact__item small {
        display: block;
        font-size: 0.65rem;
        color: var(--kd-muted);
        font-weight: 600;
        text-transform: uppercase;
    }
    .kd-contact__item strong {
        display: block;
        font-weight: 700;
        font-size: 0.82rem;
        word-break: break-all;
    }
    .kd-contact__icon {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        flex-shrink: 0;
        background: var(--kd-soft);
        color: var(--kd-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
    }
    .kd-contact__icon--wa {
        background: #dcfce7;
        color: #16a34a;
    }

    .kd-hours {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        padding: 8px;
        gap: 6px;
    }
    .kd-hours > div {
        background: #f8fafc;
        border-radius: 7px;
        padding: 6px 4px;
        text-align: center;
        border: 1px solid var(--kd-line);
    }
    .kd-hours small {
        display: block;
        font-size: 0.64rem;
        color: var(--kd-muted);
        font-weight: 600;
    }
    .kd-hours strong {
        display: block;
        font-size: 0.9rem;
        color: var(--kd-ink);
        margin: 2px 0 1px;
    }
    .kd-hours > div:first-child {
        background: var(--kd-soft);
        border-color: #fed7aa;
    }
    .kd-hours > div:first-child strong { color: var(--kd-primary); }

    /* ---------- CHIPS, BUTTONS & TABLES ---------- */
    .kd-chip {
        --chip: #64748b;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 2px 8px;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 600;
        line-height: 1.4;
        color: var(--chip);
        background: color-mix(in srgb, var(--chip) 12%, #ffffff);
        white-space: nowrap;
    }
    .kd-chip__dot {
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: currentColor;
    }
    .kd-chip--orange  { --chip: #ea580c; background: #fff7ed; border: 1px solid #ffedd5; }
    .kd-chip--green   { --chip: #059669; background: #ecfdf5; border: 1px solid #d1fae5; }
    .kd-chip--blue    { --chip: #0284c7; background: #f0f9ff; border: 1px solid #e0f2fe; }
    .kd-chip--red     { --chip: #dc2626; background: #fef2f2; border: 1px solid #fee2e2; }
    .kd-chip--neutral { --chip: #64748b; background: #f8fafc; border: 1px solid #e2e8f0; }

    .kd-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 600;
        line-height: 1;
        border: 1px solid transparent;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.2s ease;
    }
    .kd-btn--sm { padding: 6px 12px; font-size: 0.76rem; }
    .kd-btn--primary {
        background: var(--kd-grad);
        color: #ffffff !important;
        box-shadow: 0 3px 10px -2px rgba(234, 88, 12, 0.4);
    }
    .kd-btn--primary:hover {
        box-shadow: 0 5px 14px -2px rgba(234, 88, 12, 0.55);
        transform: translateY(-1px);
    }
    .kd-btn--ghost {
        background: #ffffff;
        border-color: #cbd5e1;
        color: var(--kd-text) !important;
    }
    .kd-btn--ghost:hover {
        border-color: #fdba74;
        color: var(--kd-primary) !important;
        background: #fffaf5;
    }

    .kd-table-wrap { overflow-x: auto; }
    .kd-table { width: 100%; border-collapse: collapse; }
    .kd-table th {
        text-align: left;
        padding: 10px 14px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
        background: #f8fafc;
        border-bottom: 1px solid var(--kd-line);
        white-space: nowrap;
    }
    .kd-table td {
        padding: 10px 14px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        font-size: 0.83rem;
    }
    .kd-table tbody tr:last-child td { border-bottom: 0; }
    .kd-table tbody tr:hover { background: #fffbf7; }
    .kd-code {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 0.76rem;
        background: #f8fafc;
        border: 1px solid var(--kd-line);
        padding: 2px 6px;
        border-radius: 4px;
        color: var(--kd-ink);
    }

    .kd-empty { text-align: center; padding: 36px 16px; }
    .kd-empty__icon {
        width: 56px;
        height: 56px;
        margin: 0 auto 12px;
        border-radius: 14px;
        background: var(--kd-soft);
        color: var(--kd-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
    }
    .kd-empty h3 { font-size: 0.9rem; font-weight: 700; color: var(--kd-ink); margin: 0 0 3px; }
    .kd-empty p { color: var(--kd-muted); font-size: 0.78rem; margin: 0; }

    .kd-muted { color: var(--kd-muted); }
    .kd-strong { font-weight: 700; color: var(--kd-ink); }
    .kd-xs { font-size: 0.72rem; margin-top: 1px; }
    .kd-center { text-align: center !important; }
    .kd-nowrap { white-space: nowrap; }
    .kd-clip { max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .kd-pad { padding: 16px; }

    @media (max-width: 1199px) {
        .kd-stats-bar { grid-template-columns: repeat(3, 1fr); }
        .kd-grid { grid-template-columns: minmax(0, 1fr) 260px; }
    }
    @media (max-width: 991px) {
        .kd-grid { grid-template-columns: 1fr; }
        .kd-subgrid-2 { grid-template-columns: 1fr; }
        .kd-stats-bar { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 767px) {
        .kd-hero__cover { height: 64px; padding: 8px 12px; }
        .kd-hero__body { padding: 0 14px 14px; gap: 12px; }
        .kd-hero__info { min-width: 100%; }
        .kd-hero__actions { width: 100%; }
        .kd-hero__actions .kd-btn-modern { flex: 1; justify-content: center; }
        .kd-stats-bar { grid-template-columns: repeat(2, 1fr); }
        .kd-dl > div { grid-template-columns: 1fr; gap: 3px; padding: 6px 10px; }
    }
</style>
@endpush

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var root = document.querySelector('.kd');
        if (!root) return;

        var tabs  = root.querySelectorAll('.kd-tab');
        var panes = root.querySelectorAll('.kd-pane');

        function activate(name) {
            var pane = root.querySelector('[data-kd-pane="' + name + '"]');
            if (!pane) return;

            tabs.forEach(function (t) {
                t.classList.toggle('is-active', t.dataset.kdTab === name);
            });
            panes.forEach(function (p) {
                p.classList.toggle('is-active', p === pane);
            });
        }

        root.querySelectorAll('[data-kd-tab]').forEach(function (el) {
            el.addEventListener('click', function () {
                var tabKey = this.dataset.kdTab;
                activate(tabKey);
            });
        });

        // Copy NIPY
        root.querySelectorAll('.kd-copy, .kd-copy-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var text = this.dataset.clipboard;
                var icon = this.querySelector('i');
                if (!navigator.clipboard) return;
                navigator.clipboard.writeText(text).then(function () {
                    if (icon) {
                        icon.className = 'fa fa-check text-success';
                        setTimeout(function () {
                            icon.className = 'fa fa-copy';
                        }, 1500);
                    }
                    if (typeof toastr !== 'undefined') {
                        toastr.success('NIPY berhasil disalin!');
                    }
                });
            });
        });

        // Toggle password visibility
        document.querySelectorAll('.toggle-pwd').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var input = document.getElementById(this.getAttribute('data-target'));
                var icon = this.querySelector('i');
                if (!input) return;
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });
    });

    (function () {
        var img = document.querySelector('.kd-avatar__img');
        if (!img) return;
        var box = document.createElement('div');
        box.className = 'kd-lightbox';
        box.innerHTML = '<img alt="' + (img.alt || 'Foto') + '"><button type="button" aria-label="Tutup">&times;</button>';
        document.body.appendChild(box);
        var big = box.querySelector('img');
        var close = function () { box.classList.remove('is-open'); };
        img.addEventListener('click', function () { big.src = img.src; box.classList.add('is-open'); });
        box.addEventListener('click', close);
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); });
    })();
</script>
@endsection
