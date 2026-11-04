@extends('layouts.appui')

@section('title')
    <title>Formulir Pengajuan Cuti - {{ $karyawan->nama_lengkap ?? Auth::user()->name }} | SIPIT PION</title>
@endsection

@section('content')
<div class="user-presensi-wrapper user-cuti-create-wrapper">

    {{-- Page Header Action Bar --}}
    <div class="user-cuti-header-bar">
        <div class="user-cuti-header-bar__left">
            <a href="{{ route('user.cuti.index') }}" class="btn-cuti-back">
                <i class="fa fa-arrow-left mr-1.5"></i>
                <span>Kembali ke Riwayat Cuti</span>
            </a>
            <div class="user-cuti-header-bar__title">
                <h2>Formulir Permohonan Cuti Pegawai</h2>
                <p>Isi formulir berikut dengan teliti. Pengajuan cuti akan ditinjau dan disetujui (ACC) oleh pimpinan.</p>
            </div>
        </div>
    </div>

    <div class="user-cuti-form-container">
        
        {{-- Profile Pegawai Summary Banner --}}
        <div class="user-cuti-profile-banner">
            <div class="profile-banner__avatar">
                <i class="fa fa-user-circle"></i>
            </div>
            <div class="profile-banner__info">
                <div class="profile-banner__name">
                    {{ $karyawan->nama_lengkap ?? Auth::user()->name }}{{ $karyawan?->gelar ? ', ' . $karyawan->gelar : '' }}
                </div>
                <div class="profile-banner__meta">
                    <span><i class="fa fa-id-card mr-1"></i> NIPY: <strong>{{ $karyawan->nipy ?? Auth::user()->no_induk }}</strong></span>
                    @if ($karyawan?->unit)
                        <span>&bull; <i class="fa fa-building mr-1"></i> Unit: <strong>{{ $karyawan->unit }}</strong></span>
                    @endif
                    @if ($karyawan?->jabatan)
                        <span>&bull; <i class="fa fa-briefcase mr-1"></i> Jabatan: <strong>{{ $karyawan->jabatan }}</strong></span>
                    @endif
                </div>
            </div>
            <div class="profile-banner__badge">
                <span class="badge-role">Pegawai Pemohon</span>
            </div>
        </div>

        {{-- Form Permohonan --}}
        <div class="user-cuti-form-card">
            <form action="{{ route('user.cuti.store') }}" method="POST" id="formPengajuanCuti">
                @csrf

                <div class="form-section-title">
                    <i class="fa fa-calendar-check text-orange mr-2"></i>
                    <span>Informasi Kategori &amp; Rentang Waktu Cuti</span>
                </div>

                <div class="row">
                    {{-- Kategori Jenis Cuti --}}
                    <div class="col-md-6 mb-3">
                        <label for="kepegawaian_master_jenis_cuti_id" class="up-form-label">
                            Kategori Jenis Cuti <span class="text-danger">*</span>
                        </label>
                        <select name="kepegawaian_master_jenis_cuti_id" id="kepegawaian_master_jenis_cuti_id"
                            class="up-form-control @error('kepegawaian_master_jenis_cuti_id') is-invalid @enderror"
                            onchange="if(window.onJenisCutiChange) window.onJenisCutiChange();" required>
                            <option value="">-- Pilih Jenis Cuti --</option>
                            @foreach ($jenisCutiList as $jc)
                                @php
                                    $namaLower = strtolower($jc->nama ?? '');
                                    $isIstri = str_contains($namaLower, 'istri') || str_contains($namaLower, 'suami') || str_contains($namaLower, 'mendampingi');
                                    $isMlh = !$isIstri && (method_exists($jc, 'isMelahirkan')
                                        ? $jc->isMelahirkan()
                                        : str_contains($namaLower, 'melahirkan'));
                                @endphp
                                <option value="{{ $jc->id }}"
                                    data-kuota="{{ $jc->lama_hari }}"
                                    data-melahirkan="{{ $isMlh ? '1' : '0' }}"
                                    {{ old('kepegawaian_master_jenis_cuti_id') == $jc->id ? 'selected' : '' }}>
                                    {{ $jc->nama }} (Kuota: {{ $jc->lama_hari }} Hari{{ $isMlh ? ' Kalender' : ' Kerja' }})
                                </option>
                            @endforeach
                        </select>
                        <small class="up-form-hint">Pilih jenis cuti sesuai kebutuhan Anda.</small>
                        @error('kepegawaian_master_jenis_cuti_id')
                            <div class="up-form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Kuota Badge Note --}}
                    <div class="col-md-6 mb-3 d-flex align-items-center">
                        <div id="kuotaInfoBox" class="kuota-info-box" style="display: none;">
                            <i class="fa fa-info-circle text-primary mr-2" id="kuotaIcon"></i>
                            <div>
                                <span class="d-block font-weight-bold" id="kuotaTitle">Batas Kuota</span>
                                <span class="small text-muted" id="kuotaDesc">Maksimal hari cuti yang dapat diambil.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- Tanggal Awal Cuti --}}
                    <div class="col-md-4 mb-3">
                        <label for="awal_cuti" class="up-form-label">
                            Tanggal Awal Cuti <span class="text-danger">*</span>
                        </label>
                        <div class="up-input-icon-wrap">
                            <i class="fa fa-calendar-alt up-input-icon"></i>
                            <input type="date" name="awal_cuti" id="awal_cuti"
                                class="up-form-control has-icon @error('awal_cuti') is-invalid @enderror"
                                value="{{ old('awal_cuti') }}"
                                onchange="if(window.updateFormByDuration) window.updateFormByDuration();"
                                oninput="if(window.updateFormByDuration) window.updateFormByDuration();" required>
                        </div>
                        <small class="up-form-hint">Tanggal mulai cuti berlaku.</small>
                        @error('awal_cuti')
                            <div class="up-form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Lama Hari Kerja --}}
                    <div class="col-md-4 mb-3">
                        <label for="jumlah_hari" class="up-form-label" id="labelJumlahHari">
                            Lama Hari (<span id="jenisHariLabel">Hari Kerja</span>) <span class="text-danger">*</span>
                        </label>
                        <div class="up-input-icon-wrap">
                            <input type="number" name="jumlah_hari" id="jumlah_hari"
                                class="up-form-control font-weight-bold text-orange @error('jumlah_hari') is-invalid @enderror"
                                value="{{ old('jumlah_hari') }}" min="1" max="365"
                                placeholder="Pilih jenis cuti..."
                                onchange="if(window.updateFormByDuration) window.updateFormByDuration();"
                                oninput="if(window.updateFormByDuration) window.updateFormByDuration();" required>
                        </div>
                        <small class="up-form-hint" id="hintJumlahHari">Hari Sabtu, Minggu, &amp; Libur Nasional tidak dihitung.</small>
                        @error('jumlah_hari')
                            <div class="up-form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tanggal Akhir Cuti --}}
                    <div class="col-md-4 mb-3">
                        <label for="akhir_cuti" class="up-form-label">
                            Tanggal Akhir Cuti <span class="text-danger">*</span>
                        </label>
                        <div class="up-input-icon-wrap">
                            <i class="fa fa-calendar-check up-input-icon text-success"></i>
                            <input type="date" name="akhir_cuti" id="akhir_cuti"
                                class="up-form-control has-icon @error('akhir_cuti') is-invalid @enderror"
                                value="{{ old('akhir_cuti') }}"
                                onchange="if(window.updateFormByEndDate) window.updateFormByEndDate();"
                                oninput="if(window.updateFormByEndDate) window.updateFormByEndDate();" required>
                        </div>
                        <small class="up-form-hint">Otomatis terisi saat tanggal awal dipilih.</small>
                        @error('akhir_cuti')
                            <div class="up-form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Live Preview Rentang & Hari Kerja --}}
                    <div class="col-md-12 mb-3">
                        <div id="previewRentangCuti" class="up-preview-rentang" style="display: none;">
                            <span id="previewText">Kalkulasi rentang tanggal...</span>
                        </div>
                    </div>
                </div>

                <div class="form-section-title mt-3">
                    <i class="fa fa-map-marked-alt text-orange mr-2"></i>
                    <span>Informasi Kontak Darurat &amp; Alasan</span>
                </div>

                <div class="row">
                    {{-- Nomor Telepon / HP Darurat --}}
                    <div class="col-md-5 mb-3">
                        <label for="telepon" class="up-form-label">
                            Nomor Telepon / WhatsApp <span class="text-danger">*</span>
                        </label>
                        <div class="up-input-icon-wrap">
                            <i class="fa fa-phone up-input-icon"></i>
                            <input type="text" name="telepon" id="telepon"
                                class="up-form-control has-icon @error('telepon') is-invalid @enderror"
                                placeholder="Contoh: 08123456789"
                                value="{{ old('telepon', $karyawan?->whatsapp) }}" required>
                        </div>
                        <small class="up-form-hint">Nomor yang dapat dihubungi selama Anda cuti.</small>
                        @error('telepon')
                            <div class="up-form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Alamat Selama Menjalankan Cuti --}}
                    <div class="col-md-7 mb-3">
                        <label for="alamat" class="up-form-label">
                            Alamat Selama Menjalankan Cuti <span class="text-danger">*</span>
                        </label>
                        <textarea name="alamat" id="alamat" rows="2"
                            class="up-form-control @error('alamat') is-invalid @enderror"
                            placeholder="Alamat lengkap tempat tinggal selama cuti..." required>{{ old('alamat') }}</textarea>
                        <small class="up-form-hint">Alamat domisili atau tujuan Anda saat cuti.</small>
                        @error('alamat')
                            <div class="up-form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Alasan Permohonan Cuti --}}
                    <div class="col-md-12 mb-3">
                        <label for="alasan" class="up-form-label">
                            Alasan Permohonan Cuti <span class="text-muted font-normal">(Opsional)</span>
                        </label>
                        <textarea name="alasan" id="alasan" rows="3"
                            class="up-form-control @error('alasan') is-invalid @enderror"
                            placeholder="Tuliskan keterangan atau alasan pengajuan cuti secara singkat dan jelas...">{{ old('alasan') }}</textarea>
                        @error('alasan')
                            <div class="up-form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="user-cuti-form-actions">
                    <a href="{{ route('user.cuti.index') }}" class="btn-cuti-cancel">
                        <i class="fa fa-times mr-1"></i> Batal
                    </a>
                    <button type="submit" class="btn-cuti-submit">
                        <i class="fa fa-paper-plane mr-1.5"></i> Kirim Permohonan Cuti
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>

{{-- SCRIPT OTOMATISASI CUTI --}}
<script>
    (function () {
        const holidayMap = @json($hariLiburMap ?? []);
        const jenisCutiSelect = document.getElementById('kepegawaian_master_jenis_cuti_id');
        const awalCutiInput = document.getElementById('awal_cuti');
        const jumlahHariInput = document.getElementById('jumlah_hari');
        const akhirCutiInput = document.getElementById('akhir_cuti');
        const previewBox = document.getElementById('previewRentangCuti');
        const previewText = document.getElementById('previewText');
        const kuotaBox = document.getElementById('kuotaInfoBox');
        const kuotaTitle = document.getElementById('kuotaTitle');
        const kuotaDesc = document.getElementById('kuotaDesc');
        const jenisHariLabel = document.getElementById('jenisHariLabel');
        const hintJumlahHari = document.getElementById('hintJumlahHari');

        const namaHari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const namaBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        function parseIso(isoStr) {
            if (!isoStr || typeof isoStr !== 'string') return null;
            const parts = isoStr.split('-');
            if (parts.length !== 3) return null;
            const y = parseInt(parts[0], 10);
            const m = parseInt(parts[1], 10) - 1;
            const d = parseInt(parts[2], 10);
            if (isNaN(y) || isNaN(m) || isNaN(d)) return null;
            return new Date(y, m, d);
        }

        function formatIso(d) {
            if (!d || isNaN(d.getTime())) return '';
            const year = d.getFullYear();
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        function formatTanggalIndo(d) {
            if (!d || isNaN(d.getTime())) return '';
            const h = namaHari[d.getDay()];
            const tgl = String(d.getDate()).padStart(2, '0');
            const bln = namaBulan[d.getMonth()];
            const thn = d.getFullYear();
            return `${h}, ${tgl} ${bln} ${thn}`;
        }

        function isWeekend(d) {
            const day = d.getDay();
            return day === 0 || day === 6; // 0 = Minggu, 6 = Sabtu
        }

        function isHoliday(dateStr) {
            return holidayMap && Boolean(holidayMap[dateStr]);
        }

        function isMelahirkanSelected() {
            if (!jenisCutiSelect) return false;
            const selected = jenisCutiSelect.options[jenisCutiSelect.selectedIndex];
            if (!selected || !selected.value) return false;

            const isMlh = selected.getAttribute('data-melahirkan');
            if (isMlh === '1' || isMlh === 'true') return true;
            if (isMlh === '0' || isMlh === 'false') return false;

            const text = (selected.text || '').toLowerCase();
            // Pengecualian Cuti Istri Melahirkan (2 hari)
            if (text.includes('istri') || text.includes('suami') || text.includes('mendampingi')) {
                return false;
            }

            return text.includes('melahirkan');
        }

        // Kalkulasi jumlah hari dari rentang awal s/d akhir
        function hitungDurasi(awalStr, akhirStr, isMlh) {
            const d1 = parseIso(awalStr);
            const d2 = parseIso(akhirStr);
            if (!d1 || !d2 || d2 < d1) return { days: 0, holidaysEncountered: 0, weekendEncountered: 0 };

            if (isMlh) {
                const diffTime = d2.getTime() - d1.getTime();
                const calendarDays = Math.round(diffTime / (1000 * 60 * 60 * 24)) + 1;

                let holidays = 0;
                let weekends = 0;
                let cur = new Date(d1.getFullYear(), d1.getMonth(), d1.getDate());
                while (cur <= d2) {
                    const iso = formatIso(cur);
                    if (isHoliday(iso)) holidays++;
                    if (isWeekend(cur)) weekends++;
                    cur.setDate(cur.getDate() + 1);
                }

                return { days: Math.max(1, calendarDays), holidaysEncountered: holidays, weekendEncountered: weekends };
            } else {
                let workingDays = 0;
                let holidays = 0;
                let weekends = 0;
                let cur = new Date(d1.getFullYear(), d1.getMonth(), d1.getDate());

                let loopLimit = 0;
                while (cur <= d2 && loopLimit < 1000) {
                    loopLimit++;
                    const iso = formatIso(cur);
                    const weekend = isWeekend(cur);
                    const holiday = isHoliday(iso);

                    if (!weekend && !holiday) {
                        workingDays++;
                    } else {
                        if (holiday) holidays++;
                        if (weekend) weekends++;
                    }
                    cur.setDate(cur.getDate() + 1);
                }

                return { days: workingDays, holidaysEncountered: holidays, weekendEncountered: weekends };
            }
        }

        // Kalkulasi tanggal akhir cuti dari tanggal awal + target hari
        function hitungAkhir(awalStr, targetDays, isMlh) {
            const d1 = parseIso(awalStr);
            if (!d1 || targetDays < 1) return { endDateStr: '', holidaysEncountered: 0, weekendEncountered: 0 };

            if (isMlh) {
                const dEnd = new Date(d1.getFullYear(), d1.getMonth(), d1.getDate());
                dEnd.setDate(dEnd.getDate() + (targetDays - 1));

                let holidays = 0;
                let weekends = 0;
                let cur = new Date(d1.getFullYear(), d1.getMonth(), d1.getDate());
                while (cur <= dEnd) {
                    const iso = formatIso(cur);
                    if (isHoliday(iso)) holidays++;
                    if (isWeekend(cur)) weekends++;
                    cur.setDate(cur.getDate() + 1);
                }

                return { endDateStr: formatIso(dEnd), holidaysEncountered: holidays, weekendEncountered: weekends };
            } else {
                let counted = 0;
                let holidays = 0;
                let weekends = 0;
                let cur = new Date(d1.getFullYear(), d1.getMonth(), d1.getDate());

                let loopLimit = 0;
                while (counted < targetDays && loopLimit < 1000) {
                    loopLimit++;
                    const iso = formatIso(cur);
                    const weekend = isWeekend(cur);
                    const holiday = isHoliday(iso);

                    if (!weekend && !holiday) {
                        counted++;
                        if (counted === targetDays) {
                            break;
                        }
                    } else {
                        if (holiday) holidays++;
                        if (weekend) weekends++;
                    }
                    cur.setDate(cur.getDate() + 1);
                }

                return { endDateStr: formatIso(cur), holidaysEncountered: holidays, weekendEncountered: weekends };
            }
        }

        function showPreview(awalStr, akhirStr, days, isMlh, holidaysCount, weekendsCount) {
            if (!previewBox || !previewText || !awalStr || !akhirStr) {
                if (previewBox) previewBox.style.display = 'none';
                return;
            }
            const d1 = parseIso(awalStr);
            const d2 = parseIso(akhirStr);
            if (!d1 || !d2) {
                previewBox.style.display = 'none';
                return;
            }

            previewBox.style.display = 'flex';

            if (isMlh) {
                previewBox.className = 'up-preview-rentang up-preview-rentang--melahirkan';
                previewText.innerHTML = `
                    <div>
                        <div class="font-weight-bold" style="color: #9a3412;">
                            <i class="fa fa-baby mr-1"></i> Cuti Melahirkan: <strong>${days} Hari Kalender Penuh</strong>
                        </div>
                        <div class="small text-muted mt-1">
                            Rentang: <strong>${formatTanggalIndo(d1)}</strong> s/d <strong>${formatTanggalIndo(d2)}</strong>.
                        </div>
                        <div class="small mt-1" style="color: #c2410c;">
                            <i class="fa fa-info-circle mr-1"></i> Sesuai ketentuan, masa cuti melahirkan dihitung 90 hari kalender penuh secara berurutan (termasuk Sabtu, Minggu &amp; Libur Nasional).
                        </div>
                    </div>
                `;
            } else {
                previewBox.className = 'up-preview-rentang';
                let holidayNote = '';
                const totalLibur = (holidaysCount || 0) + (weekendsCount || 0);
                if (totalLibur > 0) {
                    holidayNote = ` <span class="text-muted">(melewati ${totalLibur} hari akhir pekan / libur nasional yang bebas cuti)</span>`;
                }
                previewText.innerHTML = `
                    <div>
                        <div class="font-weight-bold text-success">
                            <i class="fa fa-calendar-check mr-1"></i> Total Efektif: <strong>${days} Hari Kerja</strong>${holidayNote}
                        </div>
                        <div class="small text-muted mt-1">
                            Rentang: <strong>${formatTanggalIndo(d1)}</strong> s/d <strong>${formatTanggalIndo(d2)}</strong>.
                        </div>
                        <div class="small text-muted mt-0.5">
                            <i class="fa fa-check-circle text-success mr-1"></i> Sabtu, Minggu, &amp; Libur Nasional resmi otomatis tidak memotong kuota hak cuti Anda.
                        </div>
                    </div>
                `;
            }
        }

        // Kalkulasi form saat tanggal awal atau jumlah hari berubah
        function updateFormByDuration() {
            const awalVal = awalCutiInput ? awalCutiInput.value : '';
            if (!awalVal) {
                if (previewBox) previewBox.style.display = 'none';
                return;
            }

            const isMlh = isMelahirkanSelected();
            let targetDays = parseInt(jumlahHariInput.value, 10);

            // Jika jumlah hari belum terisi atau tidak valid, otomatis ambil dari kuota kategori terpilih
            if (isNaN(targetDays) || targetDays < 1) {
                if (isMlh) {
                    targetDays = 90;
                } else if (jenisCutiSelect && jenisCutiSelect.selectedIndex >= 0) {
                    const sel = jenisCutiSelect.options[jenisCutiSelect.selectedIndex];
                    const k = sel ? sel.getAttribute('data-kuota') : null;
                    targetDays = (k && parseInt(k, 10) > 0) ? parseInt(k, 10) : 1;
                } else {
                    targetDays = 1;
                }
                jumlahHariInput.value = targetDays;
            }

            const res = hitungAkhir(awalVal, targetDays, isMlh);
            if (res.endDateStr) {
                akhirCutiInput.value = res.endDateStr;
                akhirCutiInput.classList.remove('up-input-highlight');
                void akhirCutiInput.offsetWidth; // Reflow untuk memicu animasi
                akhirCutiInput.classList.add('up-input-highlight');
            }

            showPreview(awalVal, akhirCutiInput.value, targetDays, isMlh, res.holidaysEncountered, res.weekendEncountered);
        }

        // Kalkulasi form saat tanggal akhir cuti dipilih / diubah langsung
        function updateFormByEndDate() {
            const awalVal = awalCutiInput ? awalCutiInput.value : '';
            const akhirVal = akhirCutiInput ? akhirCutiInput.value : '';
            if (!awalVal || !akhirVal) {
                if (previewBox) previewBox.style.display = 'none';
                return;
            }

            const d1 = parseIso(awalVal);
            const d2 = parseIso(akhirVal);
            if (!d1 || !d2) return;

            if (d2 < d1) {
                akhirCutiInput.value = awalVal;
            }

            const isMlh = isMelahirkanSelected();
            const res = hitungDurasi(awalVal, akhirCutiInput.value, isMlh);
            jumlahHariInput.value = Math.max(1, res.days);

            showPreview(awalVal, akhirCutiInput.value, res.days, isMlh, res.holidaysEncountered, res.weekendEncountered);
        }

        // Handler saat memilih kategori jenis cuti
        function onJenisCutiChange() {
            if (!jenisCutiSelect) return;
            const selected = jenisCutiSelect.options[jenisCutiSelect.selectedIndex];
            if (!selected || !selected.value) {
                if (kuotaBox) kuotaBox.style.display = 'none';
                if (jenisHariLabel) jenisHariLabel.innerText = 'Hari Kerja';
                if (hintJumlahHari) hintJumlahHari.innerHTML = 'Hari Sabtu, Minggu, &amp; Libur Nasional tidak dihitung.';
                return;
            }

            const kuota = selected.getAttribute('data-kuota');
            const isMlh = isMelahirkanSelected();

            if (isMlh) {
                // Pengecualian Cuti Melahirkan: Otomatis 90 Hari Kalender Penuh
                if (kuotaBox) {
                    kuotaBox.style.display = 'flex';
                    kuotaBox.className = 'kuota-info-box kuota-info-box--melahirkan';
                    kuotaTitle.innerHTML = `<i class="fa fa-baby text-orange mr-1"></i> Cuti Melahirkan (90 Hari Kalender Penuh)`;
                    kuotaDesc.innerText = 'Dihitung 90 hari kalender penuh berturut-turut (termasuk hari Sabtu, Minggu, dan Libur Nasional).';
                }
                if (jenisHariLabel) jenisHariLabel.innerText = 'Hari Kalender Penuh';
                if (hintJumlahHari) hintJumlahHari.innerHTML = '<span class="text-orange font-weight-bold">Pengecualian Cuti Melahirkan:</span> 90 hari kalender penuh (Sabtu, Minggu &amp; Libur Nasional tetap dihitung).';

                // Otomatis terisi 90 hari
                jumlahHariInput.value = 90;
                jumlahHariInput.max = 90;
            } else {
                // Cuti Reguler: Otomatis terisi sesuai kuota kategori
                const kuotaNum = (kuota && parseInt(kuota, 10) > 0) ? parseInt(kuota, 10) : 1;

                if (kuota && parseInt(kuota, 10) > 0) {
                    if (kuotaBox) {
                        kuotaBox.style.display = 'flex';
                        kuotaBox.className = 'kuota-info-box';
                        kuotaTitle.innerText = `${selected.text.split('(')[0].trim()}`;
                        kuotaDesc.innerText = `Kuota maksimal yang diizinkan yayasan adalah ${kuotaNum} hari kerja.`;
                    }
                } else {
                    if (kuotaBox) kuotaBox.style.display = 'none';
                }

                if (jenisHariLabel) jenisHariLabel.innerText = 'Hari Kerja';
                if (hintJumlahHari) hintJumlahHari.innerHTML = 'Hari Sabtu, Minggu, &amp; Libur Nasional tidak dihitung.';

                // LAMA HARI LANGSUNG OTOMATIS TERISI SESUAI KUOTA KATEGORI
                jumlahHariInput.value = kuotaNum;
                jumlahHariInput.max = kuotaNum;
            }

            // Animasi efek highlight pada input lama hari
            jumlahHariInput.classList.remove('up-input-highlight');
            void jumlahHariInput.offsetWidth;
            jumlahHariInput.classList.add('up-input-highlight');

            // Jika tanggal awal sudah diisi, langsung update tanggal akhir secara otomatis seketika
            if (awalCutiInput && awalCutiInput.value) {
                updateFormByDuration();
            }
        }

        // Expose ke window agar dapat dipanggil dari inline HTML onchange/oninput
        window.onJenisCutiChange = onJenisCutiChange;
        window.updateFormByDuration = updateFormByDuration;
        window.updateFormByEndDate = updateFormByEndDate;

        // Pasang event listeners pada seluruh elemen secara menyeluruh (input, change, blur, keyup)
        const inputsDuration = [awalCutiInput, jumlahHariInput];
        inputsDuration.forEach(el => {
            if (!el) return;
            ['input', 'change', 'blur', 'keyup'].forEach(evt => {
                el.addEventListener(evt, updateFormByDuration);
            });
        });

        if (akhirCutiInput) {
            ['input', 'change', 'blur', 'keyup'].forEach(evt => {
                akhirCutiInput.addEventListener(evt, updateFormByEndDate);
            });
        }

        if (jenisCutiSelect) {
            jenisCutiSelect.addEventListener('change', onJenisCutiChange);
            jenisCutiSelect.addEventListener('input', onJenisCutiChange);
        }

        // Kompatibilitas jika dipicu melalui jQuery
        if (window.jQuery) {
            $(jenisCutiSelect).on('change input', onJenisCutiChange);
            $(awalCutiInput).on('input change blur keyup', updateFormByDuration);
            $(jumlahHariInput).on('input change blur keyup', updateFormByDuration);
            $(akhirCutiInput).on('input change blur keyup', updateFormByEndDate);
        }

        // Inisialisasi awal saat halaman pertama kali dimuat
        if (jenisCutiSelect && jenisCutiSelect.value) {
            onJenisCutiChange();
        }
        if (awalCutiInput && awalCutiInput.value) {
            updateFormByDuration();
        }
    })();
</script>
@endsection

@push('css')
<style>
    .user-cuti-create-wrapper {
        margin: 0 auto;
        padding-bottom: 40px;
    }

    .user-cuti-header-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    .user-cuti-header-bar__left {
        display: flex;
        align-items: flex-start;
        gap: 16px;
    }
    .user-cuti-header-bar__title h2 {
        font-size: 1.25rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 2px 0;
    }
    .user-cuti-header-bar__title p {
        font-size: 0.82rem;
        color: #64748b;
        margin: 0;
    }
    .btn-cuti-back {
        display: inline-flex;
        align-items: center;
        padding: 8px 14px;
        border-radius: 10px;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        color: #334155 !important;
        font-weight: 700;
        font-size: 0.8rem;
        text-decoration: none;
        transition: all 0.2s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        flex-shrink: 0;
    }
    .btn-cuti-back:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        transform: translateX(-2px);
    }

    /* Profile Banner */
    .user-cuti-profile-banner {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 60%, #c2410c 100%);
        border-radius: 16px;
        padding: 18px 24px;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 20px;
        box-shadow: 0 8px 20px -4px rgba(15, 23, 42, 0.15);
        flex-wrap: wrap;
    }
    .profile-banner__avatar {
        font-size: 40px;
        color: #fb923c;
        flex-shrink: 0;
    }
    .profile-banner__info {
        flex-grow: 1;
    }
    .profile-banner__name {
        font-size: 1.1rem;
        font-weight: 800;
        color: #ffffff;
    }
    .profile-banner__meta {
        font-size: 0.78rem;
        color: #cbd5e1;
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 2px;
    }
    .badge-role {
        display: inline-block;
        padding: 4px 10px;
        background: rgba(249, 115, 22, 0.25);
        border: 1px solid rgba(251, 146, 60, 0.4);
        color: #fdba74;
        border-radius: 20px;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.04em;
    }

    /* Form Card */
    .user-cuti-form-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px 28px;
        box-shadow: 0 4px 14px -2px rgba(15, 23, 42, 0.05);
    }
    .form-section-title {
        font-size: 0.92rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 16px;
        padding-bottom: 8px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
    }

    .up-form-label {
        display: block;
        font-size: 0.78rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 6px;
    }
    .up-form-control {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 9px 12px;
        font-size: 0.86rem;
        color: #0f172a;
        background: #ffffff;
        outline: none;
        transition: all 0.2s ease;
    }
    .up-form-control:focus {
        border-color: #f97316;
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.15);
    }
    .up-form-control.is-invalid {
        border-color: #e11d48;
    }
    .up-input-icon-wrap {
        position: relative;
    }
    .up-input-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 14px;
        pointer-events: none;
    }
    .up-form-control.has-icon {
        padding-left: 36px;
    }
    .up-form-hint {
        display: block;
        font-size: 0.73rem;
        color: #64748b;
        margin-top: 4px;
    }
    .up-form-error {
        font-size: 0.75rem;
        color: #e11d48;
        font-weight: 600;
        margin-top: 4px;
    }

    .kuota-info-box {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 10px;
        padding: 10px 14px;
        display: flex;
        align-items: center;
        width: 100%;
        transition: all 0.25s ease;
    }
    .kuota-info-box--melahirkan {
        background: #fff7ed !important;
        border-color: #fdba74 !important;
    }
    .kuota-info-box--melahirkan #kuotaIcon {
        color: #ea580c !important;
    }
    .kuota-info-box--melahirkan #kuotaTitle {
        color: #9a3412 !important;
    }
    .kuota-info-box--melahirkan #kuotaDesc {
        color: #c2410c !important;
    }
    #kuotaTitle {
        font-size: 0.82rem;
        color: #166534;
    }
    #kuotaDesc {
        font-size: 0.74rem;
        color: #15803d;
    }

    .up-preview-rentang {
        background: #f0fdf4;
        border: 1px solid #86efac;
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 0.82rem;
        font-weight: 500;
        color: #166534;
        display: flex;
        align-items: flex-start;
        width: 100%;
        transition: all 0.25s ease;
    }
    .up-preview-rentang--melahirkan {
        background: #fff7ed !important;
        border-color: #fb923c !important;
        color: #7c2d12 !important;
    }

    @keyframes upInputPulse {
        0% {
            background-color: #f0fdf4;
            border-color: #22c55e;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.25);
        }
        100% {
            background-color: #ffffff;
            border-color: #cbd5e1;
            box-shadow: none;
        }
    }
    .up-input-highlight {
        animation: upInputPulse 0.8s ease-out;
    }

    .user-cuti-form-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 24px;
        padding-top: 18px;
        border-top: 1px solid #f1f5f9;
    }
    .btn-cuti-cancel {
        display: inline-flex;
        align-items: center;
        padding: 10px 18px;
        border-radius: 10px;
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        color: #475569 !important;
        font-weight: 700;
        font-size: 0.84rem;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .btn-cuti-cancel:hover {
        background: #e2e8f0;
        color: #1e293b !important;
    }
    .btn-cuti-submit {
        display: inline-flex;
        align-items: center;
        padding: 10px 24px;
        border-radius: 10px;
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        border: none;
        color: #ffffff;
        font-weight: 700;
        font-size: 0.86rem;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(234, 88, 12, 0.3);
        transition: all 0.2s ease;
    }
    .btn-cuti-submit:hover {
        background: linear-gradient(135deg, #fb923c 0%, #f97316 100%);
        box-shadow: 0 6px 16px rgba(234, 88, 12, 0.4);
        transform: translateY(-1px);
    }
</style>
@endpush

