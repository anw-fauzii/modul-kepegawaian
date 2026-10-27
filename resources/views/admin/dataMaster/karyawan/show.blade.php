@extends('layouts.appui')

@section('title')
    <title>Detail Karyawan - {{ $karyawan->nama_lengkap }}</title>
@endsection

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title mb-3">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon text-white shadow-sm"
                        style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 12px;">
                        <i class="pe-7s-id" style="color: white;"></i>
                    </div>
                    <div>
                        Detail Data Karyawan
                        <div class="page-title-subheading">
                            Informasi lengkap profil, kepegawaian, alamat, dan pendidikan {{ $karyawan->nama_lengkap }}.
                        </div>
                    </div>
                </div>

                <div class="page-title-actions d-flex align-items-center">
                    <a href="{{ route('karyawan.index') }}" class="btn btn-secondary shadow-sm mr-2"
                        style="border-radius: 8px; font-weight: 500;">
                        <i class="fa fa-reply mr-1"></i> Kembali
                    </a>
                    <a href="{{ route('karyawan.edit', $karyawan->nipy) }}" class="btn text-white shadow-sm mr-2"
                        style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 8px; font-weight: 500; border: none;">
                        <i class="fa fa-edit mr-1"></i> Edit Data
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4 col-md-5 mb-4">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 16px; overflow: hidden;">
                    <div style="height: 100px; background: linear-gradient(135deg, #1e293b 0%, #334155 50%, #ea580c 100%); position: relative;">
                        <span class="badge badge-light shadow-sm position-absolute"
                            style="top: 12px; right: 12px; font-size: 0.75rem; border-radius: 20px; padding: 5px 12px; font-weight: 600; color: #ea580c;">
                            <i class="pe-7s-portfolio mr-1"></i> {{ $karyawan->kategori?->nama_kategori ?? 'Karyawan' }}
                        </span>
                    </div>

                    <div class="card-body text-center pt-0 pb-4 px-4 position-relative">
                        <div class="d-inline-flex justify-content-center align-items-center shadow text-white font-weight-bold"
                            style="width: 86px; height: 86px; border-radius: 50%; margin-top: -43px; border: 4px solid #ffffff; background: linear-gradient(135deg, #ea580c 0%, #d97706 100%); font-size: 2rem; position: relative;">
                            {{ strtoupper(substr($karyawan->nama_lengkap, 0, 1)) }}

                            @if ($karyawan->jk === 'L')
                                <span class="position-absolute bg-primary text-white rounded-circle shadow-sm d-flex align-items-center justify-content-center"
                                    style="width: 26px; height: 26px; bottom: 0; right: 0; font-size: 0.75rem; border: 2px solid #fff;"
                                    title="Laki-laki" data-toggle="tooltip">
                                    <i class="fa fa-mars"></i>
                                </span>
                            @elseif ($karyawan->jk === 'P')
                                <span class="position-absolute text-white rounded-circle shadow-sm d-flex align-items-center justify-content-center"
                                    style="width: 26px; height: 26px; bottom: 0; right: 0; font-size: 0.75rem; border: 2px solid #fff; background-color: #ec4899;"
                                    title="Perempuan" data-toggle="tooltip">
                                    <i class="fa fa-venus"></i>
                                </span>
                            @endif
                        </div>

                        <h5 class="font-weight-bold mt-3 mb-1" style="color: #0f172a; font-size: 1.15rem;">
                            {{ $karyawan->nama_lengkap }}{{ $karyawan->gelar ? ', ' . $karyawan->gelar : '' }}
                        </h5>
                        <p class="text-muted mb-2" style="font-size: 0.85rem;">
                            <span class="font-weight-bold" style="color: #475569;">{{ $karyawan->jabatan }}</span> &bull; {{ $karyawan->unit }}
                        </p>

                        <div class="d-flex flex-wrap justify-content-center align-items-center my-3" style="gap: 6px;">
                            @php $statusBadge = $karyawan->status_pegawai_badge; @endphp
                            <span class="badge {{ $statusBadge['class'] }} px-3 py-2 shadow-sm"
                                style="border-radius: 20px; font-size: 0.75rem; font-weight: 500;">
                                <i class="fa fa-id-badge mr-1"></i> Pegawai {{ $statusBadge['label'] }}
                            </span>

                            <span class="badge px-3 py-2 text-white shadow-sm"
                                style="border-radius: 20px; font-size: 0.75rem; background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); font-weight: 500;">
                                <i class="fa fa-business-time mr-1"></i> Masa Kerja: {{ $karyawan->lama_kerja }}
                            </span>

                            @if ($karyawan->sertifikasi)
                                <span class="badge badge-success px-3 py-2 shadow-sm"
                                    style="border-radius: 20px; font-size: 0.75rem; font-weight: 500;">
                                    <i class="fa fa-check-circle mr-1"></i> Bersertifikasi
                                </span>
                            @else
                                <span class="badge badge-secondary px-3 py-2 shadow-sm"
                                    style="border-radius: 20px; font-size: 0.75rem; font-weight: 500; background-color: #94a3b8;">
                                    <i class="fa fa-minus-circle mr-1"></i> Belum Sertifikasi
                                </span>
                            @endif
                        </div>

                        <hr class="my-3" style="border-top: 1px solid #f1f5f9;">

                        <div class="text-left">
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                <span class="text-muted font-weight-bold" style="font-size: 0.8rem;">
                                    <i class="pe-7s-id text-warning mr-2" style="font-size: 1.1rem; vertical-align: middle;"></i> NIPY
                                </span>
                                <div class="d-flex align-items-center">
                                    <span class="font-weight-bold mr-2 text-dark" style="font-size: 0.85rem;">{{ $karyawan->nipy }}</span>
                                    <button type="button" class="btn btn-sm btn-light p-1 copy-btn" data-clipboard="{{ $karyawan->nipy }}"
                                        title="Salin NIPY" data-toggle="tooltip" style="border-radius: 4px; line-height: 1;">
                                        <i class="fa fa-copy text-muted" style="font-size: 0.75rem;"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                <span class="text-muted font-weight-bold" style="font-size: 0.8rem;">
                                    <i class="pe-7s-mail text-danger mr-2" style="font-size: 1.1rem; vertical-align: middle;"></i> Email
                                </span>
                                @php
                                    $email = $karyawan->email ?? $karyawan->user?->email;
                                @endphp
                                @if ($email)
                                    <a href="mailto:{{ $email }}" class="font-weight-bold text-truncate"
                                        style="font-size: 0.85rem; max-width: 170px; color: #ea580c;" title="{{ $email }}">
                                        {{ $email }}
                                    </a>
                                @else
                                    <span class="text-muted" style="font-size: 0.85rem;">-</span>
                                @endif
                            </div>

                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                <span class="text-muted font-weight-bold" style="font-size: 0.8rem;">
                                    <i class="fab fa-whatsapp text-success mr-2" style="font-size: 1.1rem; vertical-align: middle;"></i> WhatsApp
                                </span>
                                @if ($karyawan->whatsapp)
                                    @php
                                        $cleanWa = preg_replace('/[^0-9]/', '', $karyawan->whatsapp);
                                        if (str_starts_with($cleanWa, '0')) {
                                            $cleanWa = '62' . substr($cleanWa, 1);
                                        }
                                    @endphp
                                    <a href="https://wa.me/{{ $cleanWa }}" target="_blank"
                                        class="badge badge-success px-2 py-1 shadow-sm d-inline-flex align-items-center"
                                        style="border-radius: 6px; font-size: 0.8rem;" title="Hubungi via WhatsApp">
                                        <i class="fab fa-whatsapp mr-1"></i> {{ $karyawan->whatsapp }}
                                    </a>
                                @else
                                    <span class="text-muted" style="font-size: 0.85rem;">-</span>
                                @endif
                            </div>

                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                <span class="text-muted font-weight-bold" style="font-size: 0.8rem;">
                                    <i class="pe-7s-credit text-primary mr-2" style="font-size: 1.1rem; vertical-align: middle;"></i> NIK
                                </span>
                                <span class="font-weight-bold text-dark" style="font-size: 0.85rem;">
                                    {{ $karyawan->nik ?? '-' }}
                                </span>
                            </div>

                            <div class="d-flex align-items-center justify-content-between py-2">
                                <span class="text-muted font-weight-bold" style="font-size: 0.8rem;">
                                    <i class="pe-7s-medal text-warning mr-2" style="font-size: 1.1rem; vertical-align: middle;"></i> NUPTK
                                </span>
                                <span class="font-weight-bold text-dark" style="font-size: 0.85rem;">
                                    {{ $karyawan->nuptk ?? '-' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0" style="border-radius: 16px;">
                    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center" style="border-radius: 16px 16px 0 0;">
                        <div class="mr-2 d-flex align-items-center justify-content-center text-white shadow-sm"
                            style="width: 28px; height: 28px; border-radius: 6px; background: linear-gradient(135deg, #ea580c 0%, #d97706 100%);">
                            <i class="pe-7s-clock" style="font-size: 1rem;"></i>
                        </div>
                        <h6 class="font-weight-bold mb-0" style="color: #1e293b; font-size: 0.9rem;">
                            Jadwal Jam Kerja
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="mb-2 p-2 rounded" style="background: #fff7ed; border-left: 4px solid #ea580c;">
                            <div class="text-muted" style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">
                                Pola Kerja
                            </div>
                            <div class="font-weight-bold" style="color: #9a3412; font-size: 0.95rem;">
                                {{ $karyawan->jamKerja?->nama ?? 'Belum ditentukan' }}
                            </div>
                        </div>

                        @if ($karyawan->jamKerja)
                            <div class="mt-3">
                                <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                    <span class="text-muted font-weight-bold" style="font-size: 0.8rem;">
                                        <i class="fa fa-sign-in-alt text-success mr-2"></i> Jam Masuk
                                    </span>
                                    <span class="badge badge-success px-2 py-1 shadow-sm font-weight-bold" style="font-size: 0.8rem; border-radius: 6px;">
                                        {{ substr($karyawan->jamKerja->jam_masuk, 0, 5) }} WIB
                                    </span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                    <span class="text-muted font-weight-bold" style="font-size: 0.8rem;">
                                        <i class="fa fa-sign-out-alt text-primary mr-2"></i> Pulang (Senin - Kamis)
                                    </span>
                                    <span class="badge badge-primary px-2 py-1 shadow-sm font-weight-bold" style="font-size: 0.8rem; border-radius: 6px;">
                                        {{ substr($karyawan->jamKerja->jam_pulang_senin_kamis, 0, 5) }} WIB
                                    </span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between py-2">
                                    <span class="text-muted font-weight-bold" style="font-size: 0.8rem;">
                                        <i class="fa fa-sign-out-alt text-info mr-2"></i> Pulang (Jumat)
                                    </span>
                                    <span class="badge badge-info px-2 py-1 shadow-sm font-weight-bold" style="font-size: 0.8rem; border-radius: 6px;">
                                        {{ substr($karyawan->jamKerja->jam_pulang_jumat, 0, 5) }} WIB
                                    </span>
                                </div>
                            </div>
                        @else
                            <p class="text-muted text-center mb-0 py-2" style="font-size: 0.8rem;">
                                Data jadwal jam kerja tidak tersedia.
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-8 col-md-7 mb-4">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 16px;">
                    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center" style="border-radius: 16px 16px 0 0;">
                        <div class="mr-2 d-flex align-items-center justify-content-center text-white shadow-sm"
                            style="width: 28px; height: 28px; border-radius: 6px; background: linear-gradient(135deg, #ea580c 0%, #d97706 100%);">
                            <i class="pe-7s-id" style="font-size: 1rem;"></i>
                        </div>
                        <h6 class="font-weight-bold mb-0" style="color: #1e293b; font-size: 0.95rem;">
                            1. Identitas &amp; Data Pribadi
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="detail-label">Nomor Induk Pegawai (NIPY)</label>
                                <div class="detail-value text-primary font-weight-bold">{{ $karyawan->nipy }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="detail-label">NIK (Kependudukan)</label>
                                <div class="detail-value">{{ $karyawan->nik ?? '-' }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="detail-label">Nama Lengkap</label>
                                <div class="detail-value">{{ $karyawan->nama_lengkap }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="detail-label">Gelar</label>
                                <div class="detail-value">{{ $karyawan->gelar ?: '-' }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="detail-label">Jenis Kelamin</label>
                                <div class="detail-value">
                                    @if ($karyawan->jk === 'L')
                                        <span class="badge badge-pill badge-primary px-3 py-1 font-weight-bold">
                                            <i class="fa fa-mars mr-1"></i> Laki-laki
                                        </span>
                                    @elseif ($karyawan->jk === 'P')
                                        <span class="badge badge-pill text-white px-3 py-1 font-weight-bold" style="background-color: #ec4899;">
                                            <i class="fa fa-venus mr-1"></i> Perempuan
                                        </span>
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="detail-label">Tempat, Tanggal Lahir</label>
                                <div class="detail-value">
                                    {{ $karyawan->tempat_lahir }},
                                    {{ $karyawan->tanggal_lahir ? $karyawan->tanggal_lahir->translatedFormat('d F Y') : '-' }}
                                    @if ($karyawan->tanggal_lahir)
                                        <span class="text-muted ml-1" style="font-size: 0.8rem;">
                                            ({{ $karyawan->tanggal_lahir->age }} tahun)
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4" style="border-radius: 16px;">
                    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center" style="border-radius: 16px 16px 0 0;">
                        <div class="mr-2 d-flex align-items-center justify-content-center text-white shadow-sm"
                            style="width: 28px; height: 28px; border-radius: 6px; background: linear-gradient(135deg, #ea580c 0%, #d97706 100%);">
                            <i class="pe-7s-portfolio" style="font-size: 1rem;"></i>
                        </div>
                        <h6 class="font-weight-bold mb-0" style="color: #1e293b; font-size: 0.95rem;">
                            2. Data Kepegawaian &amp; Penugasan
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="detail-label">Kategori Karyawan</label>
                                <div class="detail-value">
                                    <span class="badge px-3 py-1 shadow-sm font-weight-bold text-white"
                                        style="border-radius: 6px; background: linear-gradient(135deg, #ea580c 0%, #d97706 100%); font-size: 0.8rem;">
                                        {{ $karyawan->kategori?->nama_kategori ?? '-' }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="detail-label">Jabatan</label>
                                <div class="detail-value font-weight-bold" style="color: #0f172a;">{{ $karyawan->jabatan }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="detail-label">Golongan</label>
                                <div class="detail-value">{{ $karyawan->golongan ?: '-' }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="detail-label">Unit Penugasan</label>
                                <div class="detail-value">{{ $karyawan->unit }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="detail-label">Mulai Tugas</label>
                                <div class="detail-value">
                                    {{ $karyawan->mulai_tugas ? $karyawan->mulai_tugas->translatedFormat('d F Y') : '-' }}
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="detail-label">Status Kepegawaian (NIPY)</label>
                                <div class="detail-value">
                                    <span class="badge {{ $statusBadge['class'] }} px-3 py-1 shadow-sm font-weight-bold"
                                        style="border-radius: 6px; font-size: 0.8rem;">
                                        Pegawai {{ $statusBadge['label'] }} (Akhiran NIPY: {{ substr($karyawan->nipy, -2) }})
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="detail-label">Masa / Lama Kerja</label>
                                <div class="detail-value">
                                    <span class="badge badge-info px-2 py-1 shadow-sm font-weight-bold" style="border-radius: 6px;">
                                        {{ $karyawan->lama_kerja }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4" style="border-radius: 16px;">
                    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center" style="border-radius: 16px 16px 0 0;">
                        <div class="mr-2 d-flex align-items-center justify-content-center text-white shadow-sm"
                            style="width: 28px; height: 28px; border-radius: 6px; background: linear-gradient(135deg, #ea580c 0%, #d97706 100%);">
                            <i class="pe-7s-study" style="font-size: 1rem;"></i>
                        </div>
                        <h6 class="font-weight-bold mb-0" style="color: #1e293b; font-size: 0.95rem;">
                            3. Pendidikan Terakhir &amp; Kualifikasi
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="detail-label">Jenjang Pendidikan</label>
                                <div class="detail-value font-weight-bold">
                                    {{ $karyawan->jenjangPendidikan?->nama_jenjang_pendidikan ?? '-' }}
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="detail-label">Program Studi (Prodi)</label>
                                <div class="detail-value">{{ $karyawan->prodi ?: '-' }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="detail-label">Tahun Kelulusan</label>
                                <div class="detail-value">{{ $karyawan->tahun_lulus ?? '-' }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="detail-label">Sertifikasi Pendidik / Profesi</label>
                                <div class="detail-value">
                                    @if ($karyawan->sertifikasi)
                                        <span class="badge badge-success px-2 py-1 shadow-sm font-weight-bold" style="border-radius: 6px;">
                                            <i class="fa fa-check-circle mr-1"></i> Sudah Memiliki Sertifikasi
                                        </span>
                                    @else
                                        <span class="badge badge-secondary px-2 py-1 shadow-sm" style="border-radius: 6px; background-color: #94a3b8;">
                                            <i class="fa fa-minus-circle mr-1"></i> Belum Memiliki Sertifikasi
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="detail-label">NUPTK</label>
                                <div class="detail-value">{{ $karyawan->nuptk ?: '-' }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="detail-label">No. WhatsApp</label>
                                <div class="detail-value">
                                    @if ($karyawan->whatsapp)
                                        <a href="https://wa.me/{{ $cleanWa ?? preg_replace('/[^0-9]/', '', $karyawan->whatsapp) }}" target="_blank"
                                            class="text-success font-weight-bold">
                                            <i class="fab fa-whatsapp mr-1"></i> {{ $karyawan->whatsapp }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4" style="border-radius: 16px;">
                    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center" style="border-radius: 16px 16px 0 0;">
                        <div class="mr-2 d-flex align-items-center justify-content-center text-white shadow-sm"
                            style="width: 28px; height: 28px; border-radius: 6px; background: linear-gradient(135deg, #ea580c 0%, #d97706 100%);">
                            <i class="pe-7s-map-marker" style="font-size: 1rem;"></i>
                        </div>
                        <h6 class="font-weight-bold mb-0" style="color: #1e293b; font-size: 0.95rem;">
                            4. Alamat Lengkap &amp; Domisili
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3 p-3 rounded" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                            <label class="detail-label mb-1">
                                <i class="fa fa-map-pin text-danger mr-1"></i> Alamat Jalan / Rumah
                            </label>
                            <div class="font-weight-bold" style="color: #1e293b; font-size: 0.95rem;">
                                {{ $karyawan->alamat }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 col-6 mb-3">
                                <label class="detail-label">RT / RW</label>
                                <div class="detail-value">{{ $karyawan->rt }} / {{ $karyawan->rw }}</div>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <label class="detail-label">Kelurahan / Desa</label>
                                <div class="detail-value">{{ $karyawan->kelurahan }}</div>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <label class="detail-label">Kecamatan</label>
                                <div class="detail-value">{{ $karyawan->kecamatan }}</div>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <label class="detail-label">Kabupaten / Kota</label>
                                <div class="detail-value">{{ $karyawan->kabupaten }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0" style="border-radius: 16px;">
                    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center" style="border-radius: 16px 16px 0 0;">
                        <div class="mr-2 d-flex align-items-center justify-content-center text-white shadow-sm"
                            style="width: 28px; height: 28px; border-radius: 6px; background: linear-gradient(135deg, #ea580c 0%, #d97706 100%);">
                            <i class="pe-7s-user" style="font-size: 1rem;"></i>
                        </div>
                        <h6 class="font-weight-bold mb-0" style="color: #1e293b; font-size: 0.95rem;">
                            5. Akun Sistem &amp; SSO
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="detail-label">Email Akun (Login)</label>
                                <div class="detail-value font-weight-bold text-dark">
                                    {{ $karyawan->email ?? $karyawan->user?->email ?? '-' }}
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="detail-label">Username / No. Induk</label>
                                <div class="detail-value font-weight-bold text-dark">
                                    {{ $karyawan->user?->no_induk ?? $karyawan->nipy }}
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="detail-label">Nama Akun User</label>
                                <div class="detail-value">{{ $karyawan->user?->name ?? $karyawan->nama_lengkap }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="detail-label">Status Akun</label>
                                <div class="detail-value">
                                    @if ($karyawan->user)
                                        <span class="badge badge-success px-2 py-1 shadow-sm font-weight-bold" style="border-radius: 6px;">
                                            <i class="fa fa-check-circle mr-1"></i> Terhubung ke Sistem SSO
                                        </span>
                                    @else
                                        <span class="badge badge-warning px-2 py-1 shadow-sm text-white font-weight-bold" style="border-radius: 6px;">
                                            <i class="fa fa-exclamation-circle mr-1"></i> Belum Memiliki Akun User
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
<style>
    .detail-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        font-weight: 700;
        margin-bottom: 4px;
        display: block;
    }

    .detail-value {
        font-size: 0.95rem;
        color: #1e293b;
        font-weight: 500;
        word-break: break-word;
    }

    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card:hover {
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04) !important;
    }

    .copy-btn:hover {
        background-color: #e2e8f0 !important;
    }

    @media print {
        .app-sidebar,
        .app-header,
        .page-title-actions,
        .copy-btn {
            display: none !important;
        }

        .app-main__inner {
            padding: 0 !important;
        }

        .card {
            box-shadow: none !important;
            border: 1px solid #cbd5e1 !important;
            break-inside: avoid;
        }
    }
</style>
@endpush

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.copy-btn').forEach(function (button) {
            button.addEventListener('click', function () {
                var textToCopy = this.getAttribute('data-clipboard');
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(textToCopy).then(function () {
                        if (typeof toastr !== 'undefined') {
                            toastr.success('NIPY berhasil disalin ke clipboard!');
                        } else {
                            alert('Teks berhasil disalin: ' + textToCopy);
                        }
                    });
                }
            });
        });
    });
</script>
@endsection
