@php
    $isEdit = isset($jenisCuti);
@endphp

<div class="form-row">
    <!-- NAMA JENIS CUTI -->
    <div class="col-md-7">
        <div class="position-relative form-group">
            <label for="nama" class="font-weight-bold">
                Nama Jenis Cuti <span class="text-danger">*</span>
            </label>
            <input name="nama" id="nama" placeholder="Contoh: Cuti Tahunan, Cuti Melahirkan, Cuti Menikah"
                type="text" class="form-control @error('nama') is-invalid @enderror"
                value="{{ old('nama', $isEdit ? $jenisCuti->nama : null) }}" required autofocus>
            <small class="form-text text-muted">
                Nama resmi jenis cuti yang akan dipilih oleh pegawai atau admin.
            </small>
            @error('nama')
                <div class="invalid-feedback" style="font-style: italic; font-size: 0.75rem;">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <!-- LAMA HARI (DURASI MAKSIMAL) -->
    <div class="col-md-5">
        <div class="position-relative form-group">
            <label for="lama_hari" class="font-weight-bold">
                Lama Hari (Kuota/Maksimal) <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <input name="lama_hari" id="lama_hari" placeholder="Contoh: 12" type="number" min="1" max="365"
                    class="form-control @error('lama_hari') is-invalid @enderror"
                    value="{{ old('lama_hari', $isEdit ? $jenisCuti->lama_hari : 1) }}" required>
                <div class="input-group-append">
                    <span class="input-group-text font-weight-bold bg-light">Hari</span>
                </div>
            </div>
            <small class="form-text text-muted">
                Jumlah alokasi hari kerja atau batas maksimal per pengajuan.
            </small>
            @error('lama_hari')
                <div class="invalid-feedback d-block" style="font-style: italic; font-size: 0.75rem;">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
</div>

<div class="form-row">
    <!-- STATUS AKTIF -->
    <div class="col-md-4">
        <div class="position-relative form-group">
            <label for="is_active" class="font-weight-bold">Status Jenis Cuti</label>
            <select name="is_active" id="is_active" class="form-control @error('is_active') is-invalid @enderror">
                <option value="1" {{ old('is_active', $isEdit ? ($jenisCuti->is_active ? '1' : '0') : '1') === '1' ? 'selected' : '' }}>
                    Aktif (Dapat Dipilih)
                </option>
                <option value="0" {{ old('is_active', $isEdit ? ($jenisCuti->is_active ? '1' : '0') : '1') === '0' ? 'selected' : '' }}>
                    Non-Aktif (Disembunyikan)
                </option>
            </select>
            <small class="form-text text-muted">
                Hanya jenis cuti aktif yang dapat diajukan oleh pegawai.
            </small>
            @error('is_active')
                <div class="invalid-feedback" style="font-style: italic; font-size: 0.75rem;">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <!-- KETERANGAN / KETENTUAN CUTI -->
    <div class="col-md-8">
        <div class="position-relative form-group">
            <label for="keterangan" class="font-weight-bold">Keterangan / Persyaratan Khusus</label>
            <textarea name="keterangan" id="keterangan" rows="2"
                placeholder="Contoh: Diberikan untuk pegawai tetap yang telah bekerja minimal 1 tahun / lampirkan surat keterangan dokter"
                class="form-control @error('keterangan') is-invalid @enderror">{{ old('keterangan', $isEdit ? $jenisCuti->keterangan : null) }}</textarea>
            <small class="form-text text-muted">
                Opsional: Catatan ketentuan, syarat lampiran, atau kebijakan yayasan.
            </small>
            @error('keterangan')
                <div class="invalid-feedback" style="font-style: italic; font-size: 0.75rem;">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
</div>

<!-- TOMBOL SUBMIT -->
<div class="form-group mt-3 pt-3 border-top">
    <button type="submit" class="btn btn-xl text-white shadow-sm font-weight-bold px-4" id="submitBtn"
        style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 8px; border: none;">
        <i class="fa fa-save mr-1.5"></i> Simpan Data Cuti
    </button>
    <a href="{{ route('jenis-cuti.index') }}" class="btn btn-outline-secondary ml-2 font-weight-bold" style="border-radius: 8px;">
        Batal
    </a>
</div>
