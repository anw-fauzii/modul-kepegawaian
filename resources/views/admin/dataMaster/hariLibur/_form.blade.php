@php
    $isEdit = isset($hariLibur);
@endphp

<div class="form-row">
    {{-- TANGGAL LIBUR --}}
    <div class="col-md-5">
        <div class="position-relative form-group">
            <label for="tanggal" class="font-weight-bold">
                Tanggal Libur <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text bg-light"><i class="fa fa-calendar-day text-danger"></i></span>
                </div>
                <input name="tanggal" id="tanggal" type="date"
                    class="form-control @error('tanggal') is-invalid @enderror"
                    value="{{ old('tanggal', $isEdit && $hariLibur->tanggal ? $hariLibur->tanggal->format('Y-m-d') : null) }}" required autofocus>
            </div>
            <small class="form-text text-muted">Tanggal jatuhnya hari libur.</small>
            @error('tanggal')
                <div class="invalid-feedback d-block" style="font-style: italic; font-size: 0.75rem;">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    {{-- NAMA HARI LIBUR --}}
    <div class="col-md-7">
        <div class="position-relative form-group">
            <label for="nama" class="font-weight-bold">
                Nama Hari Libur / Keterangan Libur <span class="text-danger">*</span>
            </label>
            <input name="nama" id="nama" placeholder="Contoh: Hari Raya Idul Fitri 1447 H / Cuti Bersama / Milad Yayasan"
                type="text" class="form-control @error('nama') is-invalid @enderror"
                value="{{ old('nama', $isEdit ? $hariLibur->nama : null) }}" required>
            <small class="form-text text-muted">Keterangan resmi hari libur nasional atau yayasan.</small>
            @error('nama')
                <div class="invalid-feedback" style="font-style: italic; font-size: 0.75rem;">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
</div>

<div class="form-row">
    {{-- KATEGORI LIBUR --}}
    <div class="col-md-5">
        <div class="position-relative form-group">
            <label for="is_libur_nasional" class="font-weight-bold">Kategori Libur</label>
            <select name="is_libur_nasional" id="is_libur_nasional" class="form-control @error('is_libur_nasional') is-invalid @enderror">
                <option value="1" {{ old('is_libur_nasional', $isEdit ? ($hariLibur->is_libur_nasional ? '1' : '0') : '1') === '1' ? 'selected' : '' }}>
                    🇮🇩 Libur Nasional Resmi / Cuti Bersama Pemerintah
                </option>
                <option value="0" {{ old('is_libur_nasional', $isEdit ? ($hariLibur->is_libur_nasional ? '1' : '0') : '1') === '0' ? 'selected' : '' }}>
                    🏫 Libur Khusus Lembaga / Yayasan Prima Insani
                </option>
            </select>
            <small class="form-text text-muted">Kategori penentu hari libur.</small>
            @error('is_libur_nasional')
                <div class="invalid-feedback" style="font-style: italic; font-size: 0.75rem;">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    {{-- KETERANGAN TAMBAHAN --}}
    <div class="col-md-7">
        <div class="position-relative form-group">
            <label for="keterangan" class="font-weight-bold">Catatan Tambahan (Opsional)</label>
            <input name="keterangan" id="keterangan"
                placeholder="Contoh: Berdasarkan SKB 3 Menteri No... / Libur akhir semester"
                type="text" class="form-control @error('keterangan') is-invalid @enderror"
                value="{{ old('keterangan', $isEdit ? $hariLibur->keterangan : null) }}">
            <small class="form-text text-muted">Opsional: SK rujukan atau catatan penetapan.</small>
            @error('keterangan')
                <div class="invalid-feedback" style="font-style: italic; font-size: 0.75rem;">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
</div>

<div class="form-group mt-3 pt-3 border-top">
    <button type="submit" class="btn text-white shadow-sm font-weight-bold px-4" id="submitBtn"
        style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 8px; border: none;">
        <i class="fa fa-save mr-1.5"></i> Simpan Hari Libur
    </button>
    <a href="{{ route('hari-libur.index') }}" class="btn btn-outline-secondary ml-2 font-weight-bold" style="border-radius: 8px;">
        Batal
    </a>
</div>
