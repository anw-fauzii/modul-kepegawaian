<div class="row">
    <!-- Pilihan Karyawan -->
    <div class="col-md-5 mb-3">
        <label for="karyawan_nipy" class="form-label font-weight-bold">
            Pilih Karyawan <span class="text-danger">*</span>
        </label>
        <select name="karyawan_nipy" id="karyawan_nipy" class="form-control select2 @error('karyawan_nipy') is-invalid @enderror" required>
            <option value="">-- Pilih Karyawan --</option>
            @foreach ($karyawanList as $karyawan)
                <option value="{{ $karyawan->nipy }}"
                    {{ old('karyawan_nipy', $presensi->karyawan_nipy ?? '') == $karyawan->nipy ? 'selected' : '' }}>
                    {{ $karyawan->nipy }} - {{ $karyawan->nama_lengkap }} ({{ $karyawan->kategori?->nama_kategori ?? 'Tanpa Kategori' }})
                </option>
            @endforeach
        </select>
        <small class="form-text text-muted">Jam kerja otomatis mengikuti jam kerja default karyawan.</small>
        @error('karyawan_nipy')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Pilihan Bulan Periode (Tutup Buku tgl 28) -->
    <div class="col-md-4 mb-3">
        <label for="kepegawaian_master_bulan_id" class="form-label font-weight-bold">
            Periode Bulan
        </label>
        <select name="kepegawaian_master_bulan_id" id="kepegawaian_master_bulan_id" class="form-control @error('kepegawaian_master_bulan_id') is-invalid @enderror">
            <option value="">-- Otomatis Sesuai Tanggal (Cut-off 28) --</option>
            @foreach ($bulanList as $b)
                <option value="{{ $b->id }}"
                    {{ old('kepegawaian_master_bulan_id', $presensi->kepegawaian_master_bulan_id ?? ($activeBulan->id ?? '')) == $b->id ? 'selected' : '' }}>
                    {{ $b->nama_bulan }} {{ $b->tahunAjaran ? '(' . $b->tahunAjaran->nama_tahun_ajaran . ')' : '' }} {{ $b->status ? ' [Aktif]' : '' }}
                </option>
            @endforeach
        </select>
        <small class="form-text text-muted">Bisa dikosongkan (sistem otomatis mendeteksi cut-off tgl 28).</small>
        @error('kepegawaian_master_bulan_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Tanggal Presensi -->
    <div class="col-md-3 mb-3">
        <label for="tanggal" class="form-label font-weight-bold">
            Tanggal <span class="text-danger">*</span>
        </label>
        <input type="date" name="tanggal" id="tanggal"
            class="form-control @error('tanggal') is-invalid @enderror"
            value="{{ old('tanggal', isset($presensi->tanggal) ? (is_string($presensi->tanggal) ? $presensi->tanggal : $presensi->tanggal->format('Y-m-d')) : date('Y-m-d')) }}" required>
        @error('tanggal')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row">
    <!-- Status Presensi -->
    <div class="col-md-4 mb-3">
        <label for="status" class="form-label font-weight-bold">
            Status Kehadiran <span class="text-danger">*</span>
        </label>
        <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
            @foreach ([
                'hadir'     => 'Hadir',
                'terlambat' => 'Terlambat',
                'tugas'     => 'Tugas',
                'izin'      => 'Izin',
                'sakit'     => 'Sakit',
                'cuti'      => 'Cuti',
                'alpa'      => 'Alpa'
            ] as $key => $label)
                <option value="{{ $key }}"
                    {{ old('status', $presensi->status ?? 'hadir') == $key ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Jam Masuk -->
    <div class="col-md-4 mb-3">
        <label for="jam_masuk" class="form-label font-weight-bold">Jam Masuk (JJ:MM)</label>
        <input type="text" name="jam_masuk" id="jam_masuk"
            placeholder="Contoh: 07:15"
            class="form-control timepicker-24 @error('jam_masuk') is-invalid @enderror"
            value="{{ old('jam_masuk', isset($presensi->jam_masuk) ? substr($presensi->jam_masuk, 0, 5) : '') }}">
        <small class="form-text text-muted">Kosongkan jika Izin/Sakit/Cuti/Alpa.</small>
        @error('jam_masuk')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Jam Pulang -->
    <div class="col-md-4 mb-3">
        <label for="jam_pulang" class="form-label font-weight-bold">Jam Pulang (JJ:MM)</label>
        <input type="text" name="jam_pulang" id="jam_pulang"
            placeholder="Contoh: 16:00"
            class="form-control timepicker-24 @error('jam_pulang') is-invalid @enderror"
            value="{{ old('jam_pulang', isset($presensi->jam_pulang) ? substr($presensi->jam_pulang, 0, 5) : '') }}">
        <small class="form-text text-muted">Kosongkan jika belum pulang.</small>
        @error('jam_pulang')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<!-- Dispensasi / Izin Khusus -->
<div class="row mb-3">
    <div class="col-md-6">
        <div class="custom-control custom-checkbox bg-light p-3 rounded border">
            <input type="checkbox" class="custom-control-input" id="izin_terlambat" name="izin_terlambat" value="1"
                {{ old('izin_terlambat', $presensi->izin_terlambat ?? false) ? 'checked' : '' }}>
            <label class="custom-control-label font-weight-bold text-primary" for="izin_terlambat">
                Izin Terlambat (Dispensasi)
            </label>
            <div class="small text-muted">Centang jika keterlambatan memiliki surat izin/dispensasi tugas dinas.</div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="custom-control custom-checkbox bg-light p-3 rounded border">
            <input type="checkbox" class="custom-control-input" id="izin_pulang_cepat" name="izin_pulang_cepat" value="1"
                {{ old('izin_pulang_cepat', $presensi->izin_pulang_cepat ?? false) ? 'checked' : '' }}>
            <label class="custom-control-label font-weight-bold text-primary" for="izin_pulang_cepat">
                Izin Pulang Cepat (Dispensasi)
            </label>
            <div class="small text-muted">Centang jika kepulangan lebih awal memiliki izin resmi/tugas luar.</div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Catatan -->
    <div class="col-12 mb-3">
        <label for="catatan" class="form-label font-weight-bold">Catatan / Keterangan</label>
        <textarea name="catatan" id="catatan" rows="3"
            placeholder="Masukkan keterangan alasan terlambat / izin / sakit jika ada..."
            class="form-control @error('catatan') is-invalid @enderror">{{ old('catatan', $presensi->catatan ?? '') }}</textarea>
        @error('catatan')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="text-right mt-3">
    <button type="submit" class="btn text-white shadow-sm px-4"
        style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 8px;">
        <i class="fa fa-save mr-1"></i> SIMPAN DATA PRESENSI
    </button>
</div>
