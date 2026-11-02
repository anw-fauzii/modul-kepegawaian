@php
    $isEdit = isset($cuti);
    $hariLiburData = $hariLiburMap ?? \App\Models\Admin\DataMaster\HariLibur::getHolidayMap();
@endphp

<div class="row">
    {{-- PILIH KARYAWAN PEMOHON --}}
    <div class="col-md-6 mb-3">
        <label for="karyawan_nipy" class="font-weight-bold text-dark">
            Pegawai Pemohon <span class="text-danger">*</span>
        </label>
        <select name="karyawan_nipy" id="karyawan_nipy" class="form-control select2 @error('karyawan_nipy') is-invalid @enderror" required>
            <option value="">-- Pilih Pegawai --</option>
            @foreach ($karyawanList as $k)
                <option value="{{ $k->nipy }}"
                    data-phone="{{ $k->whatsapp }}"
                    {{ old('karyawan_nipy', $isEdit ? $cuti->karyawan_nipy : request('karyawan_nipy')) == $k->nipy ? 'selected' : '' }}>
                    {{ $k->nipy }} - {{ $k->nama_lengkap }}{{ $k->gelar ? ', ' . $k->gelar : '' }}
                    @if ($k->unit) ({{ $k->unit }}) @endif
                </option>
            @endforeach
        </select>
        <small class="form-text text-muted">Pilih pegawai yang mengajukan permohonan cuti.</small>
        @error('karyawan_nipy')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    {{-- PILIH JENIS CUTI --}}
    <div class="col-md-6 mb-3">
        <label for="kepegawaian_master_jenis_cuti_id" class="font-weight-bold text-dark">
            Jenis Cuti <span class="text-danger">*</span>
        </label>
        <select name="kepegawaian_master_jenis_cuti_id" id="kepegawaian_master_jenis_cuti_id"
            class="form-control select2 @error('kepegawaian_master_jenis_cuti_id') is-invalid @enderror" required>
            <option value="">-- Pilih Kategori Jenis Cuti --</option>
            @foreach ($jenisCutiList as $jc)
                <option value="{{ $jc->id }}"
                    data-kuota="{{ $jc->lama_hari }}"
                    {{ old('kepegawaian_master_jenis_cuti_id', $isEdit ? $cuti->kepegawaian_master_jenis_cuti_id : null) == $jc->id ? 'selected' : '' }}>
                    {{ $jc->nama }} (Kuota Maks. {{ $jc->lama_hari }} Hari)
                </option>
            @endforeach
        </select>
        <small class="form-text text-muted">Kategori cuti sesuai Master Data Jenis Cuti yayasan.</small>
        @error('kepegawaian_master_jenis_cuti_id')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row">
    {{-- AWAL CUTI --}}
    <div class="col-md-4 mb-3">
        <label for="awal_cuti" class="font-weight-bold text-dark">
            Awal Cuti (Mulai) <span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text bg-light"><i class="fa fa-calendar-alt text-primary"></i></span>
            </div>
            <input type="date" name="awal_cuti" id="awal_cuti"
                class="form-control @error('awal_cuti') is-invalid @enderror"
                value="{{ old('awal_cuti', $isEdit && $cuti->awal_cuti ? $cuti->awal_cuti->format('Y-m-d') : null) }}" required>
        </div>
        <small class="form-text text-muted">Pilih tanggal awal mulai cuti.</small>
        @error('awal_cuti')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    {{-- LAMA HARI (HARI KERJA) --}}
    <div class="col-md-4 mb-3">
        <label for="jumlah_hari" class="font-weight-bold text-dark">
            Lama Hari (Hari Kerja) <span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <input type="number" name="jumlah_hari" id="jumlah_hari"
                class="form-control font-weight-bold text-primary @error('jumlah_hari') is-invalid @enderror"
                value="{{ old('jumlah_hari', $isEdit ? $cuti->jumlah_hari : 1) }}" min="1" max="365" required>
            <div class="input-group-append">
                <span class="input-group-text bg-light font-weight-bold">Hari Kerja</span>
            </div>
        </div>
        <small class="form-text text-muted" id="durasiNote">
            <span class="text-info"><i class="fa fa-info-circle"></i> Otomatis terisi dari kuota jenis cuti.</span>
        </small>
        @error('jumlah_hari')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    {{-- AKHIR CUTI --}}
    <div class="col-md-4 mb-3">
        <label for="akhir_cuti" class="font-weight-bold text-dark">
            Akhir Cuti (Selesai) <span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text bg-light"><i class="fa fa-calendar-check text-success"></i></span>
            </div>
            <input type="date" name="akhir_cuti" id="akhir_cuti"
                class="form-control @error('akhir_cuti') is-invalid @enderror"
                value="{{ old('akhir_cuti', $isEdit && $cuti->akhir_cuti ? $cuti->akhir_cuti->format('Y-m-d') : null) }}" required>
        </div>
        <small class="form-text text-muted">Otomatis terisi sesuai lama hari kerja.</small>
        @error('akhir_cuti')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    {{-- LIVE PREVIEW RENTANG HARI KERJA --}}
    <div class="col-md-12 mb-3">
        <div id="previewRentangCuti" class="p-2.5 px-3 rounded text-dark small"
            style="background: #f0fdf4; border: 1px solid #bbf7d0; display: none;">
            <i class="fa fa-check-circle text-success mr-1.5"></i>
            <span id="previewText"></span>
        </div>
    </div>
</div>

<div class="row">
    {{-- TELEPON SELAMA CUTI --}}
    <div class="col-md-4 mb-3">
        <label for="telepon" class="font-weight-bold text-dark">
            Nomor Telepon / HP <span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text bg-light"><i class="fa fa-phone text-muted"></i></span>
            </div>
            <input type="text" name="telepon" id="telepon"
                placeholder="Contoh: 081234567890"
                class="form-control @error('telepon') is-invalid @enderror"
                value="{{ old('telepon', $isEdit ? $cuti->telepon : null) }}" required>
        </div>
        <small class="form-text text-muted">Kontak darurat yang aktif selama cuti.</small>
        @error('telepon')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    {{-- ALAMAT SELAMA CUTI --}}
    <div class="col-md-8 mb-3">
        <label for="alamat" class="font-weight-bold text-dark">
            Alamat Selama Menjalankan Cuti <span class="text-danger">*</span>
        </label>
        <textarea name="alamat" id="alamat" rows="2"
            placeholder="Contoh: Jl. Anggrek No. 12 RT 02/05, Desa Sukamaju, Kec. Sumedang Selatan, Kab. Sumedang"
            class="form-control @error('alamat') is-invalid @enderror" required>{{ old('alamat', $isEdit ? $cuti->alamat : null) }}</textarea>
        <small class="form-text text-muted">Alamat domisili/keberadaan pegawai saat masa cuti.</small>
        @error('alamat')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row">
    {{-- ALASAN / KEPERLUAN CUTI --}}
    <div class="col-md-12 mb-3">
        <label for="alasan" class="font-weight-bold text-dark">
            Alasan / Keperluan Cuti
        </label>
        <textarea name="alasan" id="alasan" rows="2"
            placeholder="Contoh: Melangsungkan pernikahan di kampung halaman / keperluan keluarga darurat"
            class="form-control @error('alasan') is-invalid @enderror">{{ old('alasan', $isEdit ? $cuti->alasan : null) }}</textarea>
        <small class="form-text text-muted">Opsional: Deskripsi singkat permohonan izin cuti.</small>
        @error('alasan')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</div>

{{-- SECTION STATUS PERSETUJUAN (ACC) --}}
<div class="p-3 mb-4 rounded" style="background: #f8fafc; border: 1px dashed #cbd5e1;">
    <h6 class="font-weight-bold text-dark mb-3">
        <i class="fa fa-stamp text-warning mr-1"></i> Status Persetujuan (ACC Pimpinan / Admin)
    </h6>

    <div class="row">
        {{-- STATUS ACC --}}
        <div class="col-md-4 mb-2">
            <label for="acc" class="font-weight-bold text-dark">Status ACC</label>
            <select name="acc" id="acc" class="form-control @error('acc') is-invalid @enderror">
                <option value="0" {{ old('acc', $isEdit ? ($cuti->acc ? '1' : '0') : '0') === '0' ? 'selected' : '' }}>
                    ⏳ Menunggu Persetujuan (Belum ACC)
                </option>
                <option value="1" {{ old('acc', $isEdit ? ($cuti->acc ? '1' : '0') : '0') === '1' ? 'selected' : '' }}>
                    ✅ Disetujui (Sudah ACC)
                </option>
            </select>
            <small class="form-text text-muted">Tentukan apakah pengajuan telah disetujui pimpinan.</small>
            @error('acc')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        {{-- CATATAN PERSATUJUAN / ACC --}}
        <div class="col-md-8 mb-2">
            <label for="catatan_acc" class="font-weight-bold text-dark">Catatan Persetujuan (Opsional)</label>
            <input type="text" name="catatan_acc" id="catatan_acc"
                placeholder="Contoh: Disetujui dengan catatan pekerjaan diserahterimakan kepada rekan piket"
                class="form-control @error('catatan_acc') is-invalid @enderror"
                value="{{ old('catatan_acc', $isEdit ? $cuti->catatan_acc : null) }}">
            <small class="form-text text-muted">Catatan atau disposisi saat cuti di-ACC.</small>
            @error('catatan_acc')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

{{-- ACTION BUTTONS --}}
<div class="form-group pt-2 border-top d-flex align-items-center">
    <button type="submit" class="btn text-white shadow-sm font-weight-bold px-4 py-2" id="submitBtn"
        style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 8px; border: none;">
        <i class="fa fa-save mr-1.5"></i> {{ $isEdit ? 'Perbarui Pengajuan Cuti' : 'Simpan Pengajuan Cuti' }}
    </button>
    <a href="{{ route('cuti.index') }}" class="btn btn-outline-secondary ml-2 font-weight-bold" style="border-radius: 8px;">
        Batal
    </a>
</div>

{{-- SCRIPT OTOMATISASI FORM (KALKULASI HARI KERJA: SABTU, MINGGU & LIBUR NASIONAL TIDAK DIHITUNG) --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const awalInput = document.getElementById('awal_cuti');
        const akhirInput = document.getElementById('akhir_cuti');
        const durasiInput = document.getElementById('jumlah_hari');
        const durasiNote = document.getElementById('durasiNote');
        const previewBox = document.getElementById('previewRentangCuti');
        const previewText = document.getElementById('previewText');
        const jenisCutiSelect = document.getElementById('kepegawaian_master_jenis_cuti_id');
        const karyawanSelect = document.getElementById('karyawan_nipy');
        const teleponInput = document.getElementById('telepon');

        // Peta hari libur nasional resmi dari server: { "YYYY-MM-DD": "Nama Libur" }
        const daftarHariLibur = @json($hariLiburData);

        const namaHari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const namaBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        function formatTanggalIndo(dateObj) {
            const h = namaHari[dateObj.getDay()];
            const tgl = String(dateObj.getDate()).padStart(2, '0');
            const bln = namaBulan[dateObj.getMonth()];
            const thn = dateObj.getFullYear();
            return `${h}, ${tgl} ${bln} ${thn}`;
        }

        function formatIso(dateObj) {
            const y = dateObj.getFullYear();
            const m = String(dateObj.getMonth() + 1).padStart(2, '0');
            const d = String(dateObj.getDate()).padStart(2, '0');
            return `${y}-${m}-${d}`;
        }

        function parseIso(isoStr) {
            const parts = isoStr.split('-');
            return new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
        }

        // Cek apakah suatu tanggal adalah hari kerja (bukan Sabtu/Minggu dan bukan Libur Nasional)
        function isHariKerja(dateObj) {
            const day = dateObj.getDay();
            if (day === 0 || day === 6) {
                return false; // Weekend (Sabtu & Minggu)
            }
            const iso = formatIso(dateObj);
            if (daftarHariLibur && daftarHariLibur[iso]) {
                return false; // Libur Nasional / Cuti Bersama
            }
            return true;
        }

        // Hitung tanggal akhir cuti berdasarkan tanggal awal dan jumlah hari kerja
        // (Sabtu, Minggu & Hari Libur Nasional dilewati)
        function hitungAkhirCuti(awalStr, lamaHari) {
            if (!awalStr || !lamaHari || lamaHari <= 0) return '';

            let curr = parseIso(awalStr);
            let counted = 0;
            const target = parseInt(lamaHari, 10);

            while (counted < target) {
                if (isHariKerja(curr)) {
                    counted++;
                    if (counted === target) {
                        break;
                    }
                }
                curr.setDate(curr.getDate() + 1);
            }

            return formatIso(curr);
        }

        // Hitung jumlah hari kerja antara 2 tanggal (Sabtu, Minggu & Hari Libur Nasional tidak dihitung)
        function hitungHariKerja(awalStr, akhirStr) {
            if (!awalStr || !akhirStr) return 0;

            const d1 = parseIso(awalStr);
            const d2 = parseIso(akhirStr);

            if (d2 < d1) return 0;

            let workingDays = 0;
            let curr = new Date(d1.getTime());

            while (curr <= d2) {
                if (isHariKerja(curr)) {
                    workingDays++;
                }
                curr.setDate(curr.getDate() + 1);
            }

            return workingDays;
        }

        // Ambil daftar hari libur nasional yang dilewati pada periode cuti
        function getLiburDalamRentang(awalStr, akhirStr) {
            if (!awalStr || !akhirStr) return [];
            const d1 = parseIso(awalStr);
            const d2 = parseIso(akhirStr);
            if (d2 < d1) return [];

            const liburList = [];
            let curr = new Date(d1.getTime());

            while (curr <= d2) {
                const iso = formatIso(curr);
                if (daftarHariLibur && daftarHariLibur[iso]) {
                    liburList.push({
                        tanggal: iso,
                        formatted: formatTanggalIndo(curr),
                        nama: daftarHariLibur[iso]
                    });
                }
                curr.setDate(curr.getDate() + 1);
            }

            return liburList;
        }

        function updatePreview() {
            if (!awalInput.value || !akhirInput.value) {
                if (previewBox) previewBox.style.display = 'none';
                return;
            }

            const d1 = parseIso(awalInput.value);
            const d2 = parseIso(akhirInput.value);

            if (d2 < d1) {
                if (previewBox) {
                    previewBox.style.display = 'block';
                    previewBox.className = 'p-2.5 px-3 rounded text-danger small bg-danger-light';
                    previewText.innerHTML = '<strong>Peringatan:</strong> Tanggal akhir cuti tidak boleh lebih awal dari tanggal awal cuti.';
                }
                return;
            }

            const hariKerja = hitungHariKerja(awalInput.value, akhirInput.value);
            const liburNasional = getLiburDalamRentang(awalInput.value, akhirInput.value);

            if (previewBox) {
                previewBox.style.display = 'block';
                previewBox.className = 'p-3 rounded text-dark small';
                previewBox.style.background = '#f0fdf4';
                previewBox.style.border = '1px solid #bbf7d0';

                let html = `<div><i class="fa fa-calendar-check text-success mr-1.5"></i> <strong>Rentang Cuti:</strong> ${formatTanggalIndo(d1)} s/d ${formatTanggalIndo(d2)} &bull; <strong class="text-success">${hariKerja} Hari Kerja Terhitung</strong></div>`;
                html += `<div class="text-muted mt-1"><i class="fa fa-info-circle text-info mr-1"></i> Sabtu, Minggu, & Libur Nasional otomatis <strong>tidak memotong</strong> kuota cuti.</div>`;

                if (liburNasional.length > 0) {
                    html += `<div class="mt-2 pt-2 border-top" style="border-color: #dcfce7 !important;">`;
                    html += `<div class="font-weight-bold text-danger mb-1"><i class="fa fa-calendar-times text-danger mr-1"></i> ${liburNasional.length} Hari Libur Nasional dilewati (bebas cuti):</div>`;
                    html += `<ul class="mb-0 pl-3">`;
                    liburNasional.forEach(l => {
                        html += `<li><strong>${l.formatted}:</strong> <span class="text-dark">${l.nama}</span></li>`;
                    });
                    html += `</ul></div>`;
                }

                previewText.innerHTML = html;
            }
        }

        // Handler saat memilih Jenis Cuti: isi kuota hari ke durasi dan hitung akhir_cuti
        function onJenisCutiChange() {
            if (!jenisCutiSelect) return;
            const selected = jenisCutiSelect.options[jenisCutiSelect.selectedIndex];
            const kuota = selected ? selected.getAttribute('data-kuota') : null;

            if (kuota && parseInt(kuota, 10) > 0) {
                durasiInput.value = parseInt(kuota, 10);
                durasiNote.innerHTML = `<span class="text-success font-weight-bold"><i class="fa fa-check mr-1"></i> Kuota jenis cuti: ${kuota} hari kerja.</span>`;

                if (awalInput.value) {
                    akhirInput.value = hitungAkhirCuti(awalInput.value, kuota);
                    updatePreview();
                }
            }
        }

        // Handler saat memilih tanggal awal cuti: otomatis isi tanggal akhir cuti
        function onAwalCutiChange() {
            const lamaHari = parseInt(durasiInput.value, 10) || 1;
            if (awalInput.value) {
                akhirInput.value = hitungAkhirCuti(awalInput.value, lamaHari);
                updatePreview();
            }
        }

        // Handler saat mengubah durasi hari kerja
        function onDurasiChange() {
            const lamaHari = parseInt(durasiInput.value, 10);
            if (awalInput.value && lamaHari && lamaHari > 0) {
                akhirInput.value = hitungAkhirCuti(awalInput.value, lamaHari);
                updatePreview();
            }
        }

        // Handler saat tanggal akhir cuti diubah manual
        function onAkhirCutiChange() {
            if (awalInput.value && akhirInput.value) {
                const hariKerja = hitungHariKerja(awalInput.value, akhirInput.value);
                durasiInput.value = hariKerja;
                updatePreview();
            }
        }

        // Event listeners
        if (awalInput) {
            awalInput.addEventListener('change', onAwalCutiChange);
            awalInput.addEventListener('input', onAwalCutiChange);
        }

        if (durasiInput) {
            durasiInput.addEventListener('change', onDurasiChange);
            durasiInput.addEventListener('input', onDurasiChange);
        }

        if (akhirInput) {
            akhirInput.addEventListener('change', onAkhirCutiChange);
            akhirInput.addEventListener('input', onAkhirCutiChange);
        }

        if (jenisCutiSelect) {
            jenisCutiSelect.addEventListener('change', onJenisCutiChange);
            if (window.jQuery && $(jenisCutiSelect).data('select2')) {
                $(jenisCutiSelect).on('select2:select', onJenisCutiChange);
            }
        }

        // Auto isi nomor telepon dari Karyawan yang dipilih
        if (karyawanSelect) {
            karyawanSelect.addEventListener('change', function () {
                const selected = this.options[this.selectedIndex];
                const phone = selected ? selected.getAttribute('data-phone') : '';
                if (phone && (!teleponInput.value || teleponInput.value.trim() === '')) {
                    teleponInput.value = phone;
                }
            });
            if (window.jQuery && $(karyawanSelect).data('select2')) {
                $(karyawanSelect).on('select2:select', function (e) {
                    const phone = $(this).find(':selected').data('phone');
                    if (phone && (!teleponInput.value || teleponInput.value.trim() === '')) {
                        teleponInput.value = phone;
                    }
                });
            }
        }

        // Initial preview jika dalam mode edit atau data sudah terisi
        if (awalInput.value && akhirInput.value) {
            updatePreview();
        }
    });
</script>
