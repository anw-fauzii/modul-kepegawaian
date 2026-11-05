@if ($golonganBaruNaik->isNotEmpty() || $golonganAkanNaik->isNotEmpty())
    <div class="row">
        <!-- Baru Naik Golongan -->
        <div class="col-lg-6">
            <div class="content-card">
                <div class="content-card-header">
                    <div>
                        <h6 class="font-weight-bold text-dark mb-0">
                            <i class="pe-7s-ribbon text-success mr-1"></i> Naik Golongan
                        </h6>
                        <small class="text-muted">{{ config('golongan.notifikasi_hari_lalu') }} hari terakhir</small>
                    </div>
                    <span class="badge badge-success">{{ $golonganBaruNaik->count() }}</span>
                </div>
                <div class="p-3" style="max-height: 320px; overflow-y: auto;">
                    @forelse ($golonganBaruNaik as $r)
                        <div class="d-flex align-items-center justify-content-between border-bottom py-2">
                            <div>
                                <a href="{{ route('karyawan.show', $r->karyawan_nipy) }}" class="font-weight-bold text-dark">
                                    {{ $r->karyawan?->nama_gelar ?? $r->karyawan_nipy }}
                                </a>
                                <div class="small text-muted">
                                    {{ $r->karyawan?->unit ?: '-' }} &bull; masa kerja {{ $r->masa_kerja_tahun }} th
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="badge badge-secondary">{{ $r->golongan_lama ?: '-' }}</span>
                                <i class="fa fa-arrow-right text-muted mx-1"></i>
                                <span class="badge badge-success">{{ $r->golongan_baru }}</span>
                                <div class="small text-muted">{{ $r->tanggal_efektif->translatedFormat('d M Y') }}</div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">Belum ada kenaikan golongan.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Akan Naik Golongan -->
        <div class="col-lg-6">
            <div class="content-card">
                <div class="content-card-header">
                    <div>
                        <h6 class="font-weight-bold text-dark mb-0">
                            <i class="pe-7s-clock text-warning mr-1"></i> Akan Naik Golongan
                        </h6>
                        <small class="text-muted">{{ config('golongan.notifikasi_hari_depan') }} hari ke depan</small>
                    </div>
                    <span class="badge badge-warning">{{ $golonganAkanNaik->count() }}</span>
                </div>
                <div class="p-3" style="max-height: 320px; overflow-y: auto;">
                    @forelse ($golonganAkanNaik as $a)
                        <div class="d-flex align-items-center justify-content-between border-bottom py-2">
                            <div>
                                <a href="{{ route('karyawan.show', $a->karyawan->nipy) }}" class="font-weight-bold text-dark">
                                    {{ $a->karyawan->nama_gelar }}
                                </a>
                                <div class="small text-muted">{{ $a->karyawan->unit ?: '-' }}</div>
                            </div>
                            <div class="text-right">
                                <span class="badge badge-secondary">{{ $a->golongan_saat_ini }}</span>
                                <i class="fa fa-arrow-right text-muted mx-1"></i>
                                <span class="badge badge-warning">{{ $a->golongan_baru }}</span>
                                <div class="small text-muted">{{ $a->tanggal->translatedFormat('d M Y') }}</div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">Tidak ada yang akan naik golongan.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endif
