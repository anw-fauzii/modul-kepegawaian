@extends('layouts.appui')

@section('title')
    <title>Detail Karyawan - {{ $karyawan->nama_lengkap }}</title>
@endsection

@php
    $statusBadge = $karyawan->status_pegawai_badge;

    $words = preg_split('/\s+/', trim($karyawan->nama_lengkap));
    $initials = strtoupper(mb_substr($words[0] ?? '', 0, 1) . mb_substr($words[1] ?? '', 0, 1));

    $email = $karyawan->email ?? $karyawan->user?->email;

    $cleanWa = null;
    if ($karyawan->whatsapp) {
        $cleanWa = preg_replace('/[^0-9]/', '', $karyawan->whatsapp);
        if (str_starts_with($cleanWa, '0')) {
            $cleanWa = '62' . substr($cleanWa, 1);
        }
    }

    $unreturnedCount = $karyawan->fasilitas->where('status', false)->count();

    $tabs = [
        'profil'      => ['label' => 'Profil Pegawai',   'icon' => 'pe-7s-user',        'count' => null],
        'fasilitas'   => ['label' => 'Fasilitas & Aset', 'icon' => 'pe-7s-box2',        'count' => $karyawan->fasilitas_count],
        'diklat'      => ['label' => 'Diklat',           'icon' => 'pe-7s-culture',     'count' => $karyawan->diklat_count],
        'rapot'       => ['label' => 'Rapor Kinerja',    'icon' => 'pe-7s-graph1',      'count' => $karyawan->rapot_kinerja_count],
        'sk'          => ['label' => 'Surat Keputusan',  'icon' => 'pe-7s-news-paper',  'count' => $karyawan->surat_keputusan_count],
        'penghargaan' => ['label' => 'Penghargaan',      'icon' => 'pe-7s-medal',       'count' => $karyawan->sertifikat_penghargaan_count],
        'tupoksi'     => ['label' => 'Tupoksi',          'icon' => 'pe-7s-note2',       'count' => $karyawan->tupoksi_count],
        'presensi'    => ['label' => 'Presensi Harian',  'icon' => 'pe-7s-clock',       'count' => null],
    ];
@endphp

@section('content')
<div class="kd">

    {{-- ============ HERO SECTION ============ --}}
    <section class="kd-hero">
        <div class="kd-hero__cover">
            <a href="{{ route('karyawan.index') }}" class="kd-hero__back">
                <i class="fa fa-arrow-left"></i> Data Karyawan
            </a>
            <div class="kd-hero__badge-top">
                <i class="pe-7s-portfolio mr-1"></i> {{ $karyawan->kategori?->nama_kategori ?? 'Karyawan' }}
            </div>
        </div>

        <div class="kd-hero__body">
            <div class="kd-avatar {{ $karyawan->foto_url ? 'kd-avatar--photo' : '' }}">
                @if ($karyawan->foto_url)
                    <img class="kd-avatar__img" src="{{ $karyawan->foto_url }}" alt="Foto {{ $karyawan->nama_lengkap }}" title="Klik untuk memperbesar">
                @else
                    {{ $initials }}
                @endif
                @if ($karyawan->jk === 'L')
                    <span class="kd-avatar__sex kd-avatar__sex--l" title="Laki-laki"><i class="fa fa-mars"></i></span>
                @elseif ($karyawan->jk === 'P')
                    <span class="kd-avatar__sex kd-avatar__sex--p" title="Perempuan"><i class="fa fa-venus"></i></span>
                @endif
            </div>

            <div class="kd-hero__info">
                <h1 class="kd-hero__name">
                    {{ $karyawan->nama_lengkap }}@if ($karyawan->gelar)<span class="kd-hero__gelar">, {{ $karyawan->gelar }}</span>@endif
                </h1>

                <div class="kd-hero__meta">
                    <span><i class="pe-7s-portfolio"></i> {{ $karyawan->jabatan ?: '-' }}</span>
                    <span><i class="pe-7s-culture"></i> {{ $karyawan->unit ?: '-' }}</span>
                    <span class="kd-meta-id">
                        <i class="pe-7s-id"></i> NIPY: <strong class="ml-1">{{ $karyawan->nipy }}</strong>
                        <button type="button" class="kd-copy" data-clipboard="{{ $karyawan->nipy }}" title="Salin NIPY">
                            <i class="fa fa-copy"></i>
                        </button>
                    </span>
                    @if ($cleanWa)
                        <a href="https://wa.me/{{ $cleanWa }}" target="_blank" rel="noopener" class="kd-hero-link kd-hero-link--wa" title="WhatsApp">
                            <i class="fab fa-whatsapp"></i> {{ $karyawan->whatsapp }}
                        </a>
                    @endif
                    @if ($email)
                        <a href="mailto:{{ $email }}" class="kd-hero-link" title="Kirim Email">
                            <i class="pe-7s-mail"></i> {{ $email }}
                        </a>
                    @endif
                </div>

                <div class="kd-hero__chips">
                    <span class="kd-chip" style="--chip: {{ $statusBadge['color'] }};">
                        <span class="kd-chip__dot"></span> Pegawai {{ $statusBadge['label'] }}
                    </span>
                    <span class="kd-chip kd-chip--orange">
                        <i class="fa fa-business-time"></i> {{ $karyawan->lama_kerja }}
                    </span>
                    @if ($karyawan->golongan)
                        <span class="kd-chip kd-chip--neutral">
                            <i class="pe-7s-ribbon"></i> Gol. {{ $karyawan->golongan }}
                        </span>
                    @endif
                    @if ($karyawan->sertifikasi)
                        <span class="kd-chip kd-chip--green"><i class="fa fa-check-circle"></i> Bersertifikasi</span>
                    @else
                        <span class="kd-chip kd-chip--neutral"><i class="fa fa-minus-circle"></i> Belum Sertifikasi</span>
                    @endif
                </div>
            </div>

            <div class="kd-hero__actions">
                <button type="button" class="kd-btn kd-btn--ghost kd-btn--sm" onclick="window.print()">
                    <i class="fa fa-print"></i> Cetak
                </button>
                <a href="{{ route('karyawan.edit', $karyawan->nipy) }}" class="kd-btn kd-btn--primary kd-btn--sm">
                    <i class="fa fa-edit"></i> Edit Data
                </a>
            </div>
        </div>

        {{-- Stat Strip / Quick Summary Cards --}}
        <div class="kd-stats">
            @foreach (['fasilitas', 'diklat', 'rapot', 'sk', 'penghargaan', 'tupoksi'] as $key)
                <button type="button" class="kd-stat" data-kd-tab="{{ $key }}" title="Buka tab {{ $tabs[$key]['label'] }}">
                    <span class="kd-stat__icon"><i class="{{ $tabs[$key]['icon'] }}"></i></span>
                    <span class="kd-stat__text">
                        <span class="kd-stat__num">{{ $tabs[$key]['count'] ?? 0 }}</span>
                        <span class="kd-stat__label">{{ $tabs[$key]['label'] }}</span>
                    </span>
                    @if ($key === 'fasilitas' && $unreturnedCount > 0)
                        <span class="kd-stat__flag" title="{{ $unreturnedCount }} item belum dikembalikan">
                            {{ $unreturnedCount }} dipinjam
                        </span>
                    @endif
                </button>
            @endforeach
        </div>
    </section>

    {{-- ============ STICKY NAV TABS ============ --}}
    <nav class="kd-tabs" role="tablist">
        @foreach ($tabs as $key => $tab)
            <button type="button" class="kd-tab {{ $loop->first ? 'is-active' : '' }}" data-kd-tab="{{ $key }}" role="tab">
                <i class="{{ $tab['icon'] }}"></i>
                <span>{{ $tab['label'] }}</span>
                @if ($tab['count'] !== null && $tab['count'] > 0)
                    <span class="kd-tab__count">{{ $tab['count'] }}</span>
                @endif
            </button>
        @endforeach
    </nav>

    {{-- ============ PANE 1: PROFIL PEGAWAI (SUPER COMPACT & RAPAT) ============ --}}
    <div class="kd-pane is-active" data-kd-pane="profil">
        <div class="kd-grid">
            <div class="kd-stack">
                {{-- Row 1: Identitas & Kepegawaian (Side by Side) --}}
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
                                <dd class="text-primary font-weight-bold">{{ $karyawan->nipy }}</dd>
                            </div>
                            <div>
                                <dt>NIK</dt>
                                <dd>{{ $karyawan->nik ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt>Nama</dt>
                                <dd class="kd-strong">{{ $karyawan->nama_lengkap }}</dd>
                            </div>
                            <div>
                                <dt>Gelar</dt>
                                <dd>{{ $karyawan->gelar ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt>Jenis Kelamin</dt>
                                <dd>
                                    @if ($karyawan->jk === 'L')
                                        <span class="kd-chip kd-chip--blue"><i class="fa fa-mars"></i> Laki-laki</span>
                                    @elseif ($karyawan->jk === 'P')
                                        <span class="kd-chip kd-chip--red"><i class="fa fa-venus"></i> Perempuan</span>
                                    @else
                                        -
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt>TTL</dt>
                                <dd>
                                    {{ $karyawan->tempat_lahir ?: '-' }},
                                    {{ $karyawan->tanggal_lahir?->translatedFormat('d M Y') ?? '-' }}
                                    @if ($karyawan->tanggal_lahir)
                                        <span class="kd-muted">({{ $karyawan->tanggal_lahir->age }} thn)</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>

                    {{-- 2. Kepegawaian & Penugasan --}}
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
                                        {{ $karyawan->kategori?->nama_kategori ?? '-' }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt>Jabatan</dt>
                                <dd class="kd-strong">{{ $karyawan->jabatan ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt>Golongan</dt>
                                <dd>{{ $karyawan->golongan ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt>Unit</dt>
                                <dd class="kd-strong">{{ $karyawan->unit ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt>Mulai Tugas</dt>
                                <dd>{{ $karyawan->mulai_tugas?->translatedFormat('d M Y') ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt>Lama Kerja</dt>
                                <dd>
                                    <span class="kd-chip kd-chip--orange">
                                        <i class="fa fa-business-time"></i> {{ $karyawan->lama_kerja }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt>Status NIPY</dt>
                                <dd>
                                    <span class="kd-chip" style="--chip: {{ $statusBadge['color'] }};">
                                        <span class="kd-chip__dot"></span> {{ $statusBadge['label'] }} ({{ substr($karyawan->nipy, -2) }})
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                {{-- Row 2: Pendidikan & Alamat (Side by Side) --}}
                <div class="kd-subgrid-2">
                    {{-- 3. Pendidikan Terakhir & Kualifikasi --}}
                    <div class="kd-card">
                        <div class="kd-card__head">
                            <span class="kd-card__icon"><i class="pe-7s-study"></i></span>
                            <h2 class="kd-card__title">3. Pendidikan Terakhir</h2>
                        </div>
                        <dl class="kd-dl kd-dl--compact">
                            <div>
                                <dt>Jenjang</dt>
                                <dd class="kd-strong">{{ $karyawan->jenjangPendidikan?->nama_jenjang_pendidikan ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt>Prodi</dt>
                                <dd>{{ $karyawan->prodi ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt>Tahun Lulus</dt>
                                <dd>{{ $karyawan->tahun_lulus ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt>NUPTK</dt>
                                <dd>{{ $karyawan->nuptk ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt>Sertifikasi</dt>
                                <dd>
                                    @if ($karyawan->sertifikasi)
                                        <span class="kd-chip kd-chip--green"><i class="fa fa-check"></i> Sudah</span>
                                    @else
                                        <span class="kd-chip kd-chip--neutral"><i class="fa fa-minus"></i> Belum</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>

                    {{-- 4. Alamat Lengkap & Domisili --}}
                    <div class="kd-card">
                        <div class="kd-card__head">
                            <span class="kd-card__icon"><i class="pe-7s-map-marker"></i></span>
                            <h2 class="kd-card__title">4. Alamat &amp; Domisili</h2>
                        </div>
                        <dl class="kd-dl kd-dl--compact">
                            <div>
                                <dt>Alamat</dt>
                                <dd class="kd-strong kd-clip" title="{{ $karyawan->alamat }}">{{ $karyawan->alamat ?: 'Belum diisi' }}</dd>
                            </div>
                            <div>
                                <dt>RT / RW</dt>
                                <dd>{{ $karyawan->rt ?: '-' }} / {{ $karyawan->rw ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt>Kelurahan</dt>
                                <dd>{{ $karyawan->kelurahan ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt>Kecamatan</dt>
                                <dd>{{ $karyawan->kecamatan ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt>Kota/Kab.</dt>
                                <dd>{{ $karyawan->kabupaten ?: '-' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            {{-- Sidebar Column on Profil Tab --}}
            <aside class="kd-stack">
                {{-- Kontak Cepat --}}
                <div class="kd-card">
                    <div class="kd-card__head">
                        <span class="kd-card__icon"><i class="pe-7s-call"></i></span>
                        <h2 class="kd-card__title">Kontak Pegawai</h2>
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
                                <strong>{{ $karyawan->whatsapp ?: '-' }}</strong>
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
                            @if ($karyawan->jamKerja)
                                <span class="kd-badge-ghost">{{ $karyawan->jamKerja->nama }}</span>
                            @endif
                        </div>
                    </div>
                    @if ($karyawan->jamKerja)
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
                        <p class="kd-muted kd-pad text-center" style="padding: 10px; font-size: 0.78rem;">Jadwal belum diatur.</p>
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
                            <dd class="kd-strong">{{ $karyawan->user?->no_induk ?? $karyawan->nipy }}</dd>
                        </div>
                        <div>
                            <dt>Nama Akun</dt>
                            <dd>{{ $karyawan->user?->name ?? $karyawan->nama_lengkap }}</dd>
                        </div>
                        <div>
                            <dt>Email Login</dt>
                            <dd class="kd-clip">{{ $karyawan->user?->email ?? $email ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt>Status SSO</dt>
                            <dd>
                                @if ($karyawan->user)
                                    <span class="kd-chip kd-chip--green"><span class="kd-chip__dot"></span> Terhubung</span>
                                @else
                                    <span class="kd-chip kd-chip--amber"><span class="kd-chip__dot"></span> Belum ada</span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </aside>
        </div>
    </div>

    {{-- ============ PANE 2: FASILITAS & ASET ============ --}}
    <div class="kd-pane" data-kd-pane="fasilitas">
        <div class="kd-card">
            <div class="kd-card__head">
                <span class="kd-card__icon"><i class="pe-7s-box2"></i></span>
                <div>
                    <h2 class="kd-card__title">Fasilitas &amp; Inventaris Karyawan</h2>
                    <p class="kd-card__sub">Daftar sarana kerja atau aset yayasan yang diserahkan kepada pegawai</p>
                </div>
                <div class="kd-ml-auto d-flex align-items-center" style="gap: 8px;">
                    <a href="{{ route('fasilitas.create', ['karyawan_nipy' => $karyawan->nipy]) }}" class="kd-btn kd-btn--primary kd-btn--sm">
                        <i class="fa fa-plus"></i> Tambah Fasilitas
                    </a>
                </div>
            </div>

            @if ($unreturnedCount > 0)
                <div class="kd-notice">
                    <i class="fa fa-exclamation-triangle"></i>
                    <span>Terdapat <strong>{{ $unreturnedCount }} fasilitas</strong> yang masih dipinjam / belum dikembalikan ke yayasan.</span>
                </div>
            @endif

            @forelse ($karyawan->fasilitas as $item)
                @if ($loop->first)
                    <div class="kd-table-wrap">
                        <table class="kd-table">
                            <thead>
                                <tr>
                                    <th class="kd-center" style="width: 48px;">#</th>
                                    <th>Nama Fasilitas</th>
                                    <th>Jumlah</th>
                                    <th>Tahun Terima</th>
                                    <th>Status Pengembalian</th>
                                    <th>Catatan</th>
                                    <th class="kd-right" style="width: 130px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                @endif
                <tr class="{{ ! $item->status ? 'kd-row-warning' : '' }}">
                    <td class="kd-center kd-muted kd-strong">{{ $loop->iteration }}</td>
                    <td class="kd-strong">
                        <i class="fa fa-cube text-warning mr-2"></i>
                        {{ $item->nama_fasilitas }}
                    </td>
                    <td>
                        <span class="kd-chip kd-chip--neutral">
                            {{ $item->jumlah }} Unit
                        </span>
                    </td>
                    <td class="kd-muted font-weight-bold">{{ $item->tahun_terima ?? '-' }}</td>
                    <td>
                        @if ($item->status)
                            <span class="kd-chip kd-chip--green"><span class="kd-chip__dot"></span> Sudah Dikembalikan</span>
                            @if ($item->tanggal_kembali)
                                <div class="kd-muted kd-xs"><i class="fa fa-calendar-alt mr-1"></i> {{ $item->tanggal_kembali->translatedFormat('d M Y') }}</div>
                            @endif
                        @else
                            <span class="kd-chip kd-chip--amber"><span class="kd-chip__dot"></span> Belum Dikembalikan</span>
                        @endif
                    </td>
                    <td class="kd-muted kd-clip">{{ $item->catatan ?: '-' }}</td>
                    <td class="kd-right kd-nowrap">
                        <a href="{{ route('fasilitas.show', $item->id) }}" class="kd-icon-btn" title="Lihat Detail"><i class="fa fa-eye"></i></a>
                        <a href="{{ route('fasilitas.edit', $item->id) }}" class="kd-icon-btn" title="Edit Fasilitas"><i class="fa fa-pen"></i></a>
                        <form action="{{ route('fasilitas.toggle-status', $item->id) }}" method="POST" class="kd-inline"
                            onsubmit="return confirm('Ubah status pengembalian fasilitas ini?')">
                            @csrf
                            <button type="submit" class="kd-icon-btn" title="{{ $item->status ? 'Tandai Belum Kembali' : 'Tandai Sudah Kembali' }}">
                                <i class="fa fa-exchange-alt"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @if ($loop->last)
                            </tbody>
                        </table>
                    </div>
                @endif
            @empty
                @include('admin.dataMaster.karyawan.partials.empty', [
                    'icon' => 'pe-7s-box2',
                    'title' => 'Belum ada fasilitas inventaris',
                    'text' => 'Pegawai ini belum memiliki catatan fasilitas kerja yang dipinjamkan.',
                    'url' => route('fasilitas.create', ['karyawan_nipy' => $karyawan->nipy]),
                ])
            @endforelse
        </div>
    </div>

    {{-- ============ PANE 3: DIKLAT & PELATIHAN ============ --}}
    <div class="kd-pane" data-kd-pane="diklat">
        <div class="kd-card">
            <div class="kd-card__head">
                <span class="kd-card__icon"><i class="pe-7s-culture"></i></span>
                <div>
                    <h2 class="kd-card__title">Riwayat Diklat &amp; Pelatihan</h2>
                    <p class="kd-card__sub">Pelatihan mandiri maupun kedinasan / lembaga yang diikuti pegawai</p>
                </div>
                <div class="kd-ml-auto">
                    <a href="{{ route('diklat.create', ['karyawan_nipy' => $karyawan->nipy]) }}" class="kd-btn kd-btn--primary kd-btn--sm">
                        <i class="fa fa-plus"></i> Tambah Data Diklat
                    </a>
                </div>
            </div>

            @forelse ($karyawan->diklat as $item)
                @if ($loop->first)
                    <div class="kd-table-wrap">
                        <table class="kd-table">
                            <thead>
                                <tr>
                                    <th class="kd-center" style="width: 48px;">#</th>
                                    <th>Nama Kegiatan &amp; Pelaksana</th>
                                    <th>Kategori</th>
                                    <th>Waktu &amp; Durasi</th>
                                    <th>Tempat</th>
                                    <th>Materi</th>
                                    <th class="kd-center">Sertifikat</th>
                                    <th class="kd-right" style="width: 90px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                @endif
                <tr>
                    <td class="kd-center kd-muted kd-strong">{{ $loop->iteration }}</td>
                    <td>
                        <div class="kd-strong">{{ $item->nama_kegiatan }}</div>
                        <div class="kd-muted kd-xs"><i class="fa fa-building mr-1"></i> {{ $item->pelaksana }}</div>
                    </td>
                    <td>
                        <span class="kd-chip {{ $item->kategori === 'lembaga' ? 'kd-chip--orange' : 'kd-chip--neutral' }}">
                            {{ ucfirst($item->kategori) }}
                        </span>
                    </td>
                    <td class="kd-nowrap">
                        <div class="font-weight-bold">
                            {{ $item->tanggal?->translatedFormat('d M Y') ?? '-' }}
                            @if ($item->tanggal_selesai && $item->tanggal && $item->tanggal_selesai->ne($item->tanggal))
                                <span class="text-muted font-weight-normal">s/d</span> {{ $item->tanggal_selesai->translatedFormat('d M Y') }}
                            @endif
                        </div>
                        @if ($item->jumlah_jam)
                            <div class="kd-muted kd-xs"><i class="fa fa-clock mr-1"></i> {{ $item->jumlah_jam }} JP</div>
                        @endif
                    </td>
                    <td class="kd-muted">{{ $item->tempat ?: '-' }}</td>
                    <td class="kd-muted kd-clip">{{ $item->materi ?: '-' }}</td>
                    <td class="kd-center">
                        @if ($item->berkas)
                            <a href="{{ route('diklat.download', $item->id) }}" class="kd-file-btn" title="Unduh Sertifikat">
                                <i class="fa fa-file-pdf text-danger"></i> Unduh
                            </a>
                        @else
                            <span class="kd-muted">-</span>
                        @endif
                    </td>
                    <td class="kd-right kd-nowrap">
                        <a href="{{ route('diklat.show', $item->id) }}" class="kd-icon-btn" title="Lihat Detail"><i class="fa fa-eye"></i></a>
                        <a href="{{ route('diklat.edit', $item->id) }}" class="kd-icon-btn" title="Edit Diklat"><i class="fa fa-pen"></i></a>
                    </td>
                </tr>
                @if ($loop->last)
                            </tbody>
                        </table>
                    </div>
                @endif
            @empty
                @include('admin.dataMaster.karyawan.partials.empty', [
                    'icon' => 'pe-7s-culture',
                    'title' => 'Belum ada riwayat diklat',
                    'text' => 'Belum ada pelatihan atau kegiatan diklat yang tercatat untuk pegawai ini.',
                    'url' => route('diklat.create', ['karyawan_nipy' => $karyawan->nipy]),
                ])
            @endforelse
        </div>
    </div>

    {{-- ============ PANE 4: RAPOR KINERJA ============ --}}
    <div class="kd-pane" data-kd-pane="rapot">
        <div class="kd-card">
            <div class="kd-card__head">
                <span class="kd-card__icon"><i class="pe-7s-graph1"></i></span>
                <div>
                    <h2 class="kd-card__title">Arsip Rapor Kinerja Pegawai</h2>
                    <p class="kd-card__sub">Dokumen evaluasi dan rekapitulasi penilaian kinerja per tahun pelajaran</p>
                </div>
                <div class="kd-ml-auto">
                    <a href="{{ route('rapot-kinerja.create', ['karyawan_nipy' => $karyawan->nipy]) }}" class="kd-btn kd-btn--primary kd-btn--sm">
                        <i class="fa fa-plus"></i> Unggah Rapor
                    </a>
                </div>
            </div>

            @forelse ($karyawan->rapotKinerja as $item)
                @if ($loop->first)
                    <div class="kd-table-wrap">
                        <table class="kd-table">
                            <thead>
                                <tr>
                                    <th class="kd-center" style="width: 48px;">#</th>
                                    <th>Tahun Pelajaran</th>
                                    <th>Keterangan / Evaluasi</th>
                                    <th>Tanggal Unggah</th>
                                    <th class="kd-center">Berkas Dokumen</th>
                                    <th class="kd-right" style="width: 90px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                @endif
                <tr>
                    <td class="kd-center kd-muted kd-strong">{{ $loop->iteration }}</td>
                    <td class="kd-strong kd-nowrap">
                        <span class="kd-chip kd-chip--blue">
                            <i class="fa fa-graduation-cap"></i>
                            {{ $item->tahunAjaran?->nama_tahun_ajaran ?? '-' }}
                            @if ($item->tahunAjaran?->semester)
                                (Sem. {{ $item->tahunAjaran->semester }})
                            @endif
                        </span>
                    </td>
                    <td class="kd-muted kd-clip">{{ $item->keterangan ?: '-' }}</td>
                    <td class="kd-nowrap kd-muted">
                        <i class="fa fa-calendar-alt mr-1"></i>
                        {{ $item->created_at?->translatedFormat('d F Y') ?? '-' }}
                    </td>
                    <td class="kd-center">
                        @if ($item->berkas)
                            <a href="{{ route('rapot-kinerja.download', $item->id) }}" class="kd-file-btn" title="Unduh Berkas Rapor">
                                <i class="fa fa-file-pdf text-danger"></i> Unduh Rapor
                            </a>
                        @else
                            <span class="kd-muted">-</span>
                        @endif
                    </td>
                    <td class="kd-right kd-nowrap">
                        <a href="{{ route('rapot-kinerja.show', $item->id) }}" class="kd-icon-btn" title="Lihat Detail"><i class="fa fa-eye"></i></a>
                        <a href="{{ route('rapot-kinerja.edit', $item->id) }}" class="kd-icon-btn" title="Edit Rapor"><i class="fa fa-pen"></i></a>
                    </td>
                </tr>
                @if ($loop->last)
                            </tbody>
                        </table>
                    </div>
                @endif
            @empty
                @include('admin.dataMaster.karyawan.partials.empty', [
                    'icon' => 'pe-7s-graph1',
                    'title' => 'Belum ada dokumen rapor kinerja',
                    'text' => 'Belum ada berkas penilaian rapor kinerja yang diunggah untuk pegawai ini.',
                    'url' => route('rapot-kinerja.create', ['karyawan_nipy' => $karyawan->nipy]),
                ])
            @endforelse
        </div>
    </div>

    {{-- ============ PANE 5: SURAT KEPUTUS (SK) ============ --}}
    <div class="kd-pane" data-kd-pane="sk">
        <div class="kd-card">
            <div class="kd-card__head">
                <span class="kd-card__icon"><i class="pe-7s-news-paper"></i></span>
                <div>
                    <h2 class="kd-card__title">Surat Keputusan (SK) Pengangkatan &amp; Penugasan</h2>
                    <p class="kd-card__sub">Dokumen resmi penetapan tugas, wewenang, atau jabatan pegawai</p>
                </div>
                <div class="kd-ml-auto">
                    <a href="{{ route('surat-keputusan.create', ['karyawan_nipy' => $karyawan->nipy]) }}" class="kd-btn kd-btn--primary kd-btn--sm">
                        <i class="fa fa-plus"></i> Tambah SK
                    </a>
                </div>
            </div>

            @forelse ($karyawan->suratKeputusan as $item)
                @if ($loop->first)
                    <div class="kd-table-wrap">
                        <table class="kd-table">
                            <thead>
                                <tr>
                                    <th class="kd-center" style="width: 48px;">#</th>
                                    <th>Nomor SK</th>
                                    <th>Jabatan Ditetapkan</th>
                                    <th>Tahun Pelajaran</th>
                                    <th>Tanggal Penetapan</th>
                                    <th>Keterangan</th>
                                    <th class="kd-center">Berkas SK</th>
                                    <th class="kd-right" style="width: 90px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                @endif
                <tr>
                    <td class="kd-center kd-muted kd-strong">{{ $loop->iteration }}</td>
                    <td><span class="kd-code">{{ $item->nomor_sk }}</span></td>
                    <td class="kd-strong">{{ $item->jabatan }}</td>
                    <td>
                        <span class="kd-chip kd-chip--neutral">
                            {{ $item->tahunAjaran?->nama_tahun_ajaran ?? '-' }}
                        </span>
                    </td>
                    <td class="kd-nowrap">
                        {{ $item->formatted_tanggal_sk ?? ($item->tanggal_sk ? $item->tanggal_sk->translatedFormat('d F Y') : '-') }}
                    </td>
                    <td class="kd-muted kd-clip">{{ $item->keterangan ?: '-' }}</td>
                    <td class="kd-center">
                        @if ($item->berkas)
                            <a href="{{ route('surat-keputusan.download', $item->id) }}" class="kd-file-btn" title="Unduh Berkas SK">
                                <i class="fa fa-file-pdf text-danger"></i> Unduh SK
                            </a>
                        @else
                            <span class="kd-muted">-</span>
                        @endif
                    </td>
                    <td class="kd-right kd-nowrap">
                        <a href="{{ route('surat-keputusan.show', $item->id) }}" class="kd-icon-btn" title="Lihat Detail"><i class="fa fa-eye"></i></a>
                        <a href="{{ route('surat-keputusan.edit', $item->id) }}" class="kd-icon-btn" title="Edit SK"><i class="fa fa-pen"></i></a>
                    </td>
                </tr>
                @if ($loop->last)
                            </tbody>
                        </table>
                    </div>
                @endif
            @empty
                @include('admin.dataMaster.karyawan.partials.empty', [
                    'icon' => 'pe-7s-news-paper',
                    'title' => 'Belum ada Surat Keputusan (SK)',
                    'text' => 'Belum ada SK penugasan atau pengangkatan yang dicatat di sistem.',
                    'url' => route('surat-keputusan.create', ['karyawan_nipy' => $karyawan->nipy]),
                ])
            @endforelse
        </div>
    </div>

    {{-- ============ PANE 6: PENGHARGAAN ============ --}}
    <div class="kd-pane" data-kd-pane="penghargaan">
        <div class="kd-card">
            <div class="kd-card__head">
                <span class="kd-card__icon"><i class="pe-7s-medal"></i></span>
                <div>
                    <h2 class="kd-card__title">Sertifikat &amp; Piagam Penghargaan</h2>
                    <p class="kd-card__sub">Apresiasi, piagam kejuaraan, atau penghargaan prestasi pegawai</p>
                </div>
                <div class="kd-ml-auto">
                    <a href="{{ route('sertifikat-penghargaan.create', ['karyawan_nipy' => $karyawan->nipy]) }}" class="kd-btn kd-btn--primary kd-btn--sm">
                        <i class="fa fa-plus"></i> Tambah Penghargaan
                    </a>
                </div>
            </div>

            @forelse ($karyawan->sertifikatPenghargaan as $item)
                @if ($loop->first)
                    <div class="kd-table-wrap">
                        <table class="kd-table">
                            <thead>
                                <tr>
                                    <th class="kd-center" style="width: 48px;">#</th>
                                    <th>Nama Penghargaan / Prestasi</th>
                                    <th>Tahun Perolehan</th>
                                    <th>Keterangan</th>
                                    <th class="kd-center">Piagam / Berkas</th>
                                    <th class="kd-right" style="width: 90px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                @endif
                <tr>
                    <td class="kd-center kd-muted kd-strong">{{ $loop->iteration }}</td>
                    <td class="kd-strong">
                        <i class="fa fa-award text-warning mr-2"></i>
                        {{ $item->judul_penghargaan }}
                    </td>
                    <td>
                        <span class="kd-chip kd-chip--amber">
                            <i class="fa fa-calendar-alt"></i> {{ $item->tahun }}
                        </span>
                    </td>
                    <td class="kd-muted kd-clip">{{ $item->keterangan ?: '-' }}</td>
                    <td class="kd-center">
                        @if ($item->berkas)
                            <a href="{{ route('sertifikat-penghargaan.download', $item->id) }}" class="kd-file-btn" title="Unduh Piagam">
                                <i class="fa fa-download text-success"></i> Unduh Piagam
                            </a>
                        @else
                            <span class="kd-muted">-</span>
                        @endif
                    </td>
                    <td class="kd-right kd-nowrap">
                        <a href="{{ route('sertifikat-penghargaan.show', $item->id) }}" class="kd-icon-btn" title="Lihat Detail"><i class="fa fa-eye"></i></a>
                        <a href="{{ route('sertifikat-penghargaan.edit', $item->id) }}" class="kd-icon-btn" title="Edit Data"><i class="fa fa-pen"></i></a>
                    </td>
                </tr>
                @if ($loop->last)
                            </tbody>
                        </table>
                    </div>
                @endif
            @empty
                @include('admin.dataMaster.karyawan.partials.empty', [
                    'icon' => 'pe-7s-medal',
                    'title' => 'Belum ada sertifikat penghargaan',
                    'text' => 'Belum ada penghargaan atau piagam prestasi yang diinputkan.',
                    'url' => route('sertifikat-penghargaan.create', ['karyawan_nipy' => $karyawan->nipy]),
                ])
            @endforelse
        </div>
    </div>

    {{-- ============ PANE 7: TUPOKSI ============ --}}
    <div class="kd-pane" data-kd-pane="tupoksi">
        <div class="kd-card">
            <div class="kd-card__head">
                <span class="kd-card__icon"><i class="pe-7s-note2"></i></span>
                <div>
                    <h2 class="kd-card__title">Tugas Pokok &amp; Fungsi (Tupoksi)</h2>
                    <p class="kd-card__sub">Dokumen rincian wewenang, uraian kerja, dan kewajiban kedinasan pegawai</p>
                </div>
                <div class="kd-ml-auto">
                    <a href="{{ route('tupoksi.create', ['karyawan_nipy' => $karyawan->nipy]) }}" class="kd-btn kd-btn--primary kd-btn--sm">
                        <i class="fa fa-plus"></i> Tambah Tupoksi
                    </a>
                </div>
            </div>

            @forelse ($karyawan->tupoksi as $item)
                @if ($loop->first)
                    <div class="kd-table-wrap">
                        <table class="kd-table">
                            <thead>
                                <tr>
                                    <th class="kd-center" style="width: 48px;">#</th>
                                    <th>Jabatan Tupoksi</th>
                                    <th>Tahun Pelajaran</th>
                                    <th>Keterangan</th>
                                    <th class="kd-center">Dokumen Berkas</th>
                                    <th class="kd-right" style="width: 90px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                @endif
                <tr>
                    <td class="kd-center kd-muted kd-strong">{{ $loop->iteration }}</td>
                    <td class="kd-strong">{{ $item->jabatan }}</td>
                    <td>
                        <span class="kd-chip kd-chip--neutral">
                            {{ $item->tahunAjaran?->nama_tahun_ajaran ?? '-' }}
                        </span>
                    </td>
                    <td class="kd-muted kd-clip">{{ $item->keterangan ?: '-' }}</td>
                    <td class="kd-center">
                        @if ($item->berkas)
                            <a href="{{ route('tupoksi.download', $item->id) }}" class="kd-file-btn" title="Unduh Berkas Tupoksi">
                                <i class="fa fa-file-pdf text-danger"></i> Unduh Tupoksi
                            </a>
                        @else
                            <span class="kd-muted">-</span>
                        @endif
                    </td>
                    <td class="kd-right kd-nowrap">
                        <a href="{{ route('tupoksi.show', $item->id) }}" class="kd-icon-btn" title="Lihat Detail"><i class="fa fa-eye"></i></a>
                        <a href="{{ route('tupoksi.edit', $item->id) }}" class="kd-icon-btn" title="Edit Tupoksi"><i class="fa fa-pen"></i></a>
                    </td>
                </tr>
                @if ($loop->last)
                            </tbody>
                        </table>
                    </div>
                @endif
            @empty
                @include('admin.dataMaster.karyawan.partials.empty', [
                    'icon' => 'pe-7s-note2',
                    'title' => 'Belum ada dokumen tupoksi',
                    'text' => 'Belum ada dokumen uraian tugas pokok dan fungsi yang diunggah untuk pegawai ini.',
                    'url' => route('tupoksi.create', ['karyawan_nipy' => $karyawan->nipy]),
                ])
            @endforelse
        </div>
    </div>

    {{-- ============ PANE 8: PRESENSI & KEGIATAN ============ --}}
    <div class="kd-pane" data-kd-pane="presensi">
        <div class="kd-stack">
            {{-- Presensi Harian --}}
            <div class="kd-card">
                <div class="kd-card__head">
                    <span class="kd-card__icon"><i class="pe-7s-clock"></i></span>
                    <div>
                        <h2 class="kd-card__title">Presensi Harian Terkini</h2>
                        <p class="kd-card__sub">Rekam kehadiran mesin / aplikasi presensi (maks. 31 catatan terakhir)</p>
                    </div>
                    <div class="kd-ml-auto">
                        <a href="{{ route('presensi.index') }}" class="kd-btn kd-btn--ghost kd-btn--sm">
                            Rekap Presensi <i class="fa fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>

                @forelse ($karyawan->presensi as $p)
                    @if ($loop->first)
                        <div class="kd-table-wrap">
                            <table class="kd-table">
                                <thead>
                                    <tr>
                                        <th class="kd-center" style="width: 48px;">#</th>
                                        <th>Tanggal Presensi</th>
                                        <th>Bulan</th>
                                        <th>Jam Masuk</th>
                                        <th>Jam Pulang</th>
                                        <th>Status Kehadiran</th>
                                        <th>Keterlambatan</th>
                                        <th>Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                    @endif
                    @php
                        $st = strtolower($p->status ?? '');
                        $stClass = match (true) {
                            $st === 'hadir' => 'kd-chip--green',
                            $st === 'terlambat' => 'kd-chip--amber',
                            in_array($st, ['izin', 'ijin', 'sakit']) => 'kd-chip--blue',
                            $st === 'alpa' => 'kd-chip--red',
                            default => 'kd-chip--neutral',
                        };
                    @endphp
                    <tr>
                        <td class="kd-center kd-muted kd-strong">{{ $loop->iteration }}</td>
                        <td class="kd-nowrap">
                            <div class="kd-strong">{{ $p->tanggal?->translatedFormat('d M Y') ?? '-' }}</div>
                            <div class="kd-muted kd-xs">{{ $p->tanggal?->translatedFormat('l') }}</div>
                        </td>
                        <td class="kd-muted">{{ $p->bulan?->nama_bulan ?? '-' }}</td>
                        <td>
                            @if ($p->jam_masuk)
                                <span class="kd-code font-weight-bold">{{ substr($p->jam_masuk, 0, 5) }}</span>
                            @else
                                <span class="kd-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if ($p->jam_pulang)
                                <span class="kd-code font-weight-bold">{{ substr($p->jam_pulang, 0, 5) }}</span>
                            @else
                                <span class="kd-muted">-</span>
                            @endif
                        </td>
                        <td><span class="kd-chip {{ $stClass }}">{{ ucfirst($p->status ?? '-') }}</span></td>
                        <td>
                            @if ($p->keterlambatan_menit > 0)
                                <span class="kd-text-red font-weight-bold">{{ $p->keterlambatan_menit }} menit</span>
                            @else
                                <span class="kd-muted">-</span>
                            @endif
                        </td>
                        <td class="kd-muted kd-clip">{{ $p->catatan ?: '-' }}</td>
                    </tr>
                    @if ($loop->last)
                                </tbody>
                            </table>
                        </div>
                    @endif
                @empty
                    <p class="kd-muted kd-pad text-center">Belum ada data presensi harian yang tercatat untuk pegawai ini.</p>
                @endforelse
            </div>

            {{-- Kehadiran Kegiatan Lembaga --}}
            <div class="kd-card">
                <div class="kd-card__head">
                    <span class="kd-card__icon"><i class="pe-7s-date"></i></span>
                    <div>
                        <h2 class="kd-card__title">Kehadiran Kegiatan Lembaga</h2>
                        <p class="kd-card__sub">Partisipasi acara dinas, rapat yayasan, workshop, dan seminar lembaga</p>
                    </div>
                    <div class="kd-ml-auto">
                        <a href="{{ route('kegiatan.index') }}" class="kd-btn kd-btn--ghost kd-btn--sm">
                            Daftar Kegiatan <i class="fa fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>

                @forelse ($karyawan->kehadiranKegiatan as $kh)
                    @if ($loop->first)
                        <div class="kd-table-wrap">
                            <table class="kd-table">
                                <thead>
                                    <tr>
                                        <th class="kd-center" style="width: 48px;">#</th>
                                        <th>Nama Kegiatan</th>
                                        <th>Waktu Presensi</th>
                                        <th>Metode Presensi</th>
                                        <th>Status Kehadiran</th>
                                        <th>Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                    @endif
                    <tr>
                        <td class="kd-center kd-muted kd-strong">{{ $loop->iteration }}</td>
                        <td class="kd-strong">{{ $kh->kegiatan?->nama_kegiatan ?? '-' }}</td>
                        <td class="kd-nowrap kd-muted">
                            <i class="fa fa-clock mr-1"></i>
                            {{ $kh->waktu_scan?->translatedFormat('d M Y, H:i') ?? '-' }} WIB
                        </td>
                        <td>
                            <span class="kd-chip kd-chip--neutral font-weight-bold">
                                <i class="fa fa-qrcode"></i> {{ strtoupper($kh->metode_presensi ?? 'QR') }}
                            </span>
                        </td>
                        <td><span class="kd-chip kd-chip--green">{{ ucfirst($kh->status ?? 'Hadir') }}</span></td>
                        <td class="kd-muted kd-clip">{{ $kh->catatan ?: '-' }}</td>
                    </tr>
                    @if ($loop->last)
                                </tbody>
                            </table>
                        </div>
                    @endif
                @empty
                    <p class="kd-muted kd-pad text-center">Belum ada riwayat kehadiran kegiatan lembaga untuk pegawai ini.</p>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection

@push('css')
<style>
    /* ==========================================================================
       SIPIT Karyawan Detail - Ultra Compact & Modern Clean Design System
       ========================================================================== */
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

    /* ---------- HERO SECTION (COMPACT) ---------- */
    .kd-hero {
        background: #ffffff;
        border: 1px solid var(--kd-line);
        border-radius: 14px;
        overflow: hidden;
        margin-bottom: 10px;
        box-shadow: 0 1px 3px 0 rgba(15, 23, 42, 0.04);
    }
    .kd-hero__cover {
        height: 68px;
        background:
            radial-gradient(circle at 1px 1px, rgba(255,255,255,.15) 1px, transparent 0) 0 0 / 18px 18px,
            linear-gradient(135deg, #0f172a 0%, #1e293b 45%, #ea580c 100%);
        padding: 8px 16px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }
    .kd-hero__back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: rgba(255, 255, 255, 0.9) !important;
        font-size: 0.72rem;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        transition: all 0.2s ease;
    }
    .kd-hero__back:hover {
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff !important;
        transform: translateX(-2px);
    }
    .kd-hero__badge-top {
        display: inline-flex;
        align-items: center;
        color: #ea580c;
        background: #ffffff;
        font-size: 0.7rem;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 999px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    }

    .kd-hero__body {
        display: flex;
        align-items: flex-end;
        gap: 14px;
        padding: 0 16px 10px;
        flex-wrap: wrap;
    }
    .kd-avatar {
        position: relative;
        width: 74px;
        height: 74px;
        flex-shrink: 0;
        margin-top: -37px;
        border-radius: 16px;
        background: var(--kd-grad);
        color: #ffffff;
        font-size: 1.55rem;
        font-weight: 700;
        letter-spacing: 0.02em;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3px solid #ffffff;
        box-shadow: 0 6px 16px -4px rgba(234, 88, 12, 0.45);
    }
    .kd-avatar--photo {
        width: 88px;
        height: 108px;
        margin-top: -54px;
        background: #f1f5f9;
        overflow: visible;
    }
    .kd-avatar__img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center top;
        border-radius: 13px;
        cursor: zoom-in;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .kd-avatar__img:hover {
        transform: scale(1.04);
        box-shadow: 0 8px 20px -6px rgba(0, 0, 0, 0.35);
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
    @media print { .kd-lightbox { display: none !important; } }
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
        min-width: 240px;
        padding-top: 6px;
    }
    .kd-hero__name {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--kd-ink);
        letter-spacing: -0.02em;
        line-height: 1.25;
    }
    .kd-hero__gelar {
        font-weight: 600;
        color: #64748b;
    }
    .kd-hero__meta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 3px 12px;
        margin-top: 3px;
        color: #64748b;
        font-size: 0.78rem;
    }
    .kd-hero__meta span, .kd-hero__meta a {
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .kd-hero__meta i {
        font-size: 0.95rem;
        color: var(--kd-primary);
    }
    .kd-hero-link {
        color: var(--kd-primary) !important;
        font-weight: 600;
        transition: color 0.2s;
    }
    .kd-hero-link:hover {
        text-decoration: underline;
    }
    .kd-hero-link--wa {
        color: #16a34a !important;
    }
    .kd-hero-link--wa i {
        color: #16a34a !important;
    }
    .kd-hero__chips {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        margin-top: 5px;
    }
    .kd-hero__actions {
        display: flex;
        gap: 6px;
        padding-top: 6px;
    }

    .kd-copy {
        border: 0;
        background: transparent;
        color: var(--kd-muted);
        padding: 2px 5px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 0.72rem;
        transition: all 0.2s;
    }
    .kd-copy:hover {
        color: var(--kd-primary);
        background: var(--kd-soft);
    }

    /* ---------- STAT STRIP (COMPACT) ---------- */
    .kd-stats {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        border-top: 1px solid var(--kd-line);
        background: #fafbfc;
    }
    .kd-stat {
        position: relative;
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        background: transparent;
        border: 0;
        text-align: left;
        cursor: pointer;
        border-right: 1px solid var(--kd-line);
        border-bottom: 2px solid transparent;
        transition: all 0.2s ease;
    }
    .kd-stat:last-child {
        border-right: 0;
    }
    .kd-stat:hover {
        background: #fff8f3;
    }
    .kd-stat.is-active {
        background: #ffffff;
        border-bottom: 2px solid var(--kd-primary);
    }
    .kd-stat__icon {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        flex-shrink: 0;
        background: #ffffff;
        color: var(--kd-primary);
        border: 1px solid var(--kd-line);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        transition: all 0.2s ease;
    }
    .kd-stat:hover .kd-stat__icon,
    .kd-stat.is-active .kd-stat__icon {
        background: var(--kd-grad);
        color: #ffffff;
        border-color: transparent;
        box-shadow: 0 2px 6px -1px rgba(234, 88, 12, 0.4);
    }
    .kd-stat__text {
        display: flex;
        flex-direction: column;
        line-height: 1.15;
        min-width: 0;
    }
    .kd-stat__num {
        font-size: 1.05rem;
        font-weight: 800;
        color: var(--kd-ink);
    }
    .kd-stat__label {
        font-size: 0.7rem;
        color: #64748b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-weight: 600;
    }
    .kd-stat__flag {
        position: absolute;
        top: 5px;
        right: 6px;
        font-size: 0.6rem;
        font-weight: 700;
        color: #b45309;
        background: #fef3c7;
        padding: 1px 5px;
        border-radius: 999px;
        border: 1px solid #fde68a;
    }

    /* ---------- STICKY TABS (COMPACT) ---------- */
    .kd-tabs {
        position: sticky;
        top: 8px;
        z-index: 40;
        display: flex;
        gap: 3px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid var(--kd-line);
        border-radius: var(--kd-radius);
        padding: 4px;
        margin-bottom: 10px;
        box-shadow: 0 3px 12px -2px rgba(15, 23, 42, 0.05);
        overflow-x: auto;
        scrollbar-width: none;
    }
    .kd-tabs::-webkit-scrollbar {
        display: none;
    }
    .kd-tab {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 11px;
        border: 0;
        border-radius: 7px;
        background: transparent;
        color: #64748b;
        font-size: 0.8rem;
        font-weight: 600;
        white-space: nowrap;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .kd-tab i {
        font-size: 0.95rem;
    }
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
        min-width: 17px;
        padding: 0 4px;
        height: 17px;
        line-height: 17px;
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

    /* ---------- TAB PANES & LAYOUT ---------- */
    .kd-pane {
        display: none;
    }
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
        grid-template-columns: minmax(0, 1fr) 280px;
        gap: 10px;
        align-items: start;
    }
    .kd-stack {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .kd-subgrid-2 {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }

    /* ---------- CARD COMPONENT (COMPACT) ---------- */
    .kd-card {
        background: #ffffff;
        border: 1px solid var(--kd-line);
        border-radius: var(--kd-radius);
        overflow: hidden;
        box-shadow: 0 1px 2px 0 rgba(15, 23, 42, 0.03);
        transition: box-shadow 0.2s ease, border-color 0.2s ease;
    }
    .kd-card:hover {
        box-shadow: 0 3px 10px -2px rgba(15, 23, 42, 0.05);
    }
    .kd-card__head {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-bottom: 1px solid var(--kd-line);
        background: #ffffff;
    }
    .kd-card__icon {
        width: 26px;
        height: 26px;
        border-radius: 6px;
        flex-shrink: 0;
        background: var(--kd-soft);
        color: var(--kd-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
    }
    .kd-card__title {
        font-size: 0.84rem;
        font-weight: 700;
        color: var(--kd-ink);
        letter-spacing: -0.01em;
    }
    .kd-card__sub {
        font-size: 0.7rem;
        color: var(--kd-muted);
        margin-top: 1px;
    }
    .kd-badge-ghost {
        font-size: 0.68rem;
        color: #64748b;
        background: #f1f5f9;
        padding: 2px 7px;
        border-radius: 4px;
        display: inline-block;
        font-weight: 600;
    }

    /* ---------- COMPACT DEFINITION LIST ---------- */
    .kd-dl > div {
        display: grid;
        grid-template-columns: 140px 1fr;
        gap: 8px;
        padding: 7px 14px;
        border-bottom: 1px solid #f6f7f9;
        align-items: center;
    }
    .kd-dl > div:last-child {
        border-bottom: 0;
    }
    .kd-dl dt {
        color: #64748b;
        font-weight: 600;
        font-size: 0.76rem;
    }
    .kd-dl dd {
        color: var(--kd-ink);
        font-weight: 500;
        word-break: break-word;
        font-size: 0.81rem;
    }
    .kd-dl--compact > div {
        grid-template-columns: 92px 1fr;
        padding: 5px 12px;
        gap: 6px;
    }
    .kd-dl--compact dt {
        font-size: 0.74rem;
    }
    .kd-dl--compact dd {
        font-size: 0.8rem;
    }

    /* ---------- CONTACT & WORK HOURS (COMPACT) ---------- */
    .kd-contact {
        padding: 4px;
    }
    .kd-contact__item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 7px 10px;
        border-radius: 7px;
        color: var(--kd-ink) !important;
        transition: background 0.15s;
    }
    .kd-contact__item:hover {
        background: #f8fafc;
    }
    .kd-contact__item.is-empty {
        pointer-events: none;
    }
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
        gap: 5px;
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
    .kd-hours > div:first-child strong {
        color: var(--kd-primary);
    }

    /* ---------- CHIPS & BADGES ---------- */
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
    .kd-chip--amber   { --chip: #d97706; background: #fffbeb; border: 1px solid #fef3c7; }
    .kd-chip--red     { --chip: #dc2626; background: #fef2f2; border: 1px solid #fee2e2; }
    .kd-chip--neutral { --chip: #64748b; background: #f8fafc; border: 1px solid #e2e8f0; }

    /* ---------- BUTTONS ---------- */
    .kd-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 600;
        line-height: 1;
        border: 1px solid transparent;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.2s ease;
    }
    .kd-btn--sm {
        padding: 6px 12px;
        font-size: 0.76rem;
    }
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

    .kd-file-btn {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 0.76rem;
        font-weight: 600;
        color: var(--kd-primary) !important;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    .kd-file-btn:hover {
        background: var(--kd-soft);
        border-color: #fed7aa;
        transform: translateY(-1px);
        box-shadow: 0 2px 5px -1px rgba(234, 88, 12, 0.15);
    }

    .kd-icon-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #64748b !important;
        font-size: 0.72rem;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .kd-icon-btn:hover {
        border-color: #fdba74;
        color: var(--kd-primary) !important;
        background: var(--kd-soft);
        transform: translateY(-1px);
    }
    .kd-inline {
        display: inline;
        margin: 0;
    }

    /* ---------- TABLE COMPONENT (COMPACT) ---------- */
    .kd-table-wrap {
        overflow-x: auto;
    }
    .kd-table {
        width: 100%;
        border-collapse: collapse;
    }
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
    .kd-table tbody tr:last-child td {
        border-bottom: 0;
    }
    .kd-table tbody tr {
        transition: background 0.15s ease;
    }
    .kd-table tbody tr:hover {
        background: #fffbf7;
    }
    .kd-row-warning {
        background: #fffdfa !important;
    }
    .kd-table .kd-icon-btn + .kd-icon-btn,
    .kd-table .kd-icon-btn + .kd-inline,
    .kd-inline .kd-icon-btn {
        margin-left: 3px;
    }

    .kd-code {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 0.76rem;
        background: #f8fafc;
        border: 1px solid var(--kd-line);
        padding: 2px 6px;
        border-radius: 4px;
        color: var(--kd-ink);
        display: inline-block;
    }

    /* ---------- MISCELLANEOUS UTILITIES ---------- */
    .kd-notice {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 10px 16px 0;
        padding: 8px 14px;
        border-radius: 8px;
        background: #fffbeb;
        color: #92400e;
        font-size: 0.78rem;
        font-weight: 500;
        border: 1px solid #fef3c7;
    }
    .kd-empty {
        text-align: center;
        padding: 36px 16px;
    }
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
    .kd-empty h3 {
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--kd-ink);
        margin: 0 0 3px;
    }
    .kd-empty p {
        color: var(--kd-muted);
        font-size: 0.78rem;
        margin: 0 0 14px;
    }

    .kd-muted { color: var(--kd-muted); }
    .kd-strong { font-weight: 700; color: var(--kd-ink); }
    .kd-xs { font-size: 0.72rem; margin-top: 1px; }
    .kd-right { text-align: right !important; }
    .kd-center { text-align: center !important; }
    .kd-nowrap { white-space: nowrap; }
    .kd-clip { max-width: 220px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .kd-ml-auto { margin-left: auto; }
    .kd-pad { padding: 16px; }
    .kd-text-red { color: #dc2626; font-weight: 600; }

    /* ---------- RESPONSIVE BREAKPOINTS ---------- */
    @media (max-width: 1199px) {
        .kd-stats { grid-template-columns: repeat(3, 1fr); }
        .kd-stat:nth-child(3n) { border-right: 0; }
        .kd-stat:nth-child(-n+3) { border-bottom: 1px solid var(--kd-line); }
        .kd-grid { grid-template-columns: minmax(0, 1fr) 260px; }
    }
    @media (max-width: 991px) {
        .kd-grid { grid-template-columns: 1fr; }
        .kd-subgrid-2 { grid-template-columns: 1fr; }
    }
    @media (max-width: 767px) {
        .kd-hero__cover { height: 60px; }
        .kd-hero__body { padding: 0 12px 10px; }
        .kd-hero__actions { width: 100%; }
        .kd-hero__actions .kd-btn { flex: 1; justify-content: center; }
        .kd-stats { grid-template-columns: repeat(2, 1fr); }
        .kd-stat { border-bottom: 1px solid var(--kd-line); }
        .kd-stat:nth-child(3n) { border-right: 1px solid var(--kd-line); }
        .kd-stat:nth-child(2n) { border-right: 0; }
        .kd-subgrid-2 { grid-template-columns: 1fr; }
        .kd-dl > div { grid-template-columns: 1fr; gap: 3px; padding: 6px 10px; }
        .kd-card__head { flex-wrap: wrap; }
    }

    /* ---------- PRINT STYLES ---------- */
    @media print {
        #appSidebar, .app-sidebar, .app-header, header, footer,
        .kd-tabs, .kd-hero__actions, .kd-hero__back, .kd-copy,
        .kd-btn, .kd-icon-btn, .kd-inline { display: none !important; }
        .kd-pane { display: block !important; margin-bottom: 16px; }
        .kd-card, .kd-hero { break-inside: avoid; border-color: #cbd5e1; box-shadow: none !important; }
        .kd-grid { grid-template-columns: 1fr; }
        .kd-subgrid-2 { grid-template-columns: repeat(2, 1fr); }
    }
</style>
@endpush

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var root = document.querySelector('.kd');
        if (!root) return;

        var tabs  = root.querySelectorAll('.kd-tab');
        var stats = root.querySelectorAll('.kd-stat');
        var panes = root.querySelectorAll('.kd-pane');

        function activate(name, pushHash) {
            var pane = root.querySelector('[data-kd-pane="' + name + '"]');
            if (!pane) return;

            tabs.forEach(function (t) { 
                t.classList.toggle('is-active', t.dataset.kdTab === name); 
            });
            stats.forEach(function (s) { 
                s.classList.toggle('is-active', s.dataset.kdTab === name); 
            });
            panes.forEach(function (p) { 
                p.classList.toggle('is-active', p === pane); 
            });

            if (pushHash && history.replaceState) {
                history.replaceState(null, '', '#' + name);
            }
        }

        root.querySelectorAll('[data-kd-tab]').forEach(function (el) {
            el.addEventListener('click', function () {
                var tabKey = this.dataset.kdTab;
                activate(tabKey, true);
                if (this.classList.contains('kd-stat')) {
                    var tabsEl = root.querySelector('.kd-tabs');
                    if (tabsEl) {
                        tabsEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }
            });
        });

        // Initialize active tab from URL hash if provided
        var initial = (window.location.hash || '').replace('#', '').replace(/^tab-/, '');
        if (initial) {
            activate(initial, false);
        } else {
            activate('profil', false);
        }

        // Copy NIPY with visual checkmark feedback
        root.querySelectorAll('.kd-copy').forEach(function (btn) {
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
