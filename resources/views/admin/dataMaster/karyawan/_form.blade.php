@php
    $isEdit = isset($karyawan);
    $val = fn($field, $format = null) => old(
        $field,
        $isEdit
            ? ($format && $karyawan->$field ? $karyawan->$field->format($format) : $karyawan->$field)
            : null
    );
@endphp

<div class="form-section mb-4 pb-2 border-bottom">
    <div class="d-flex align-items-center mb-2">
        <div class="mr-2 d-flex align-items-center justify-content-center text-white shadow-sm"
            style="width: 30px; height: 30px; border-radius: 8px; background: linear-gradient(135deg, #ea580c 0%, #d97706 100%);">
            <i class="pe-7s-id" style="font-size: 1.1rem;"></i>
        </div>
        <h6 class="font-weight-bold mb-0" style="color: #1e293b; font-size: 0.95rem;">
            1. Identitas &amp; Data Pribadi
        </h6>
    </div>
    <p class="text-muted mb-3" style="font-size: 0.78rem;">
        Informasi nomor induk, identitas kependudukan, dan data pribadi karyawan.
    </p>

    <div class="form-row">
        @include('admin.dataMaster.karyawan._input', ['name' => 'nipy', 'label' => 'NIPY', 'type' => 'text', 'col' => 4])
        @include('admin.dataMaster.karyawan._input', ['name' => 'nik', 'label' => 'NIK (opsional)', 'type' => 'text', 'col' => 4, 'maxlength' => 20])
        @include('admin.dataMaster.karyawan._input', ['name' => 'nama_lengkap', 'label' => 'Nama Lengkap', 'type' => 'text', 'col' => 4])
    </div>

    <div class="form-row">
        @include('admin.dataMaster.karyawan._input', ['name' => 'gelar', 'label' => 'Gelar (opsional)', 'type' => 'text', 'col' => 3])

        <div class="col-md-3">
            <div class="position-relative form-group">
                <label for="jk" class="font-weight-bold" style="font-size: 0.85rem; color: #475569;">
                    Jenis Kelamin <span class="text-danger">*</span>
                </label>
                <select name="jk" id="jk" class="form-control @error('jk') is-invalid @enderror" style="border-radius: 6px;">
                    <option value="">-- Pilih Jenis Kelamin --</option>
                    <option value="L" @selected($val('jk') === 'L')>Laki-laki</option>
                    <option value="P" @selected($val('jk') === 'P')>Perempuan</option>
                </select>
                @error('jk')
                    <div class="invalid-feedback" style="font-style: italic; font-size: 0.7rem;">{{ strtolower($message) }}</div>
                @enderror
            </div>
        </div>

        @include('admin.dataMaster.karyawan._input', ['name' => 'tempat_lahir', 'label' => 'Tempat Lahir', 'type' => 'text', 'col' => 3])
        @include('admin.dataMaster.karyawan._input', ['name' => 'tanggal_lahir', 'label' => 'Tanggal Lahir', 'type' => 'date', 'col' => 3])
    </div>

    <div class="form-row">
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label for="foto" class="font-weight-bold" style="font-size: 0.85rem; color: #475569;">
                    Foto Karyawan (opsional)
                </label>
                <div class="d-flex align-items-center" style="gap: 14px;">
                    <img id="fotoPreview"
                        src="{{ $isEdit && $karyawan->foto_url ? $karyawan->foto_url : '' }}"
                        alt="Preview foto"
                        style="width: 80px; height: 100px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0; {{ $isEdit && $karyawan->foto_url ? '' : 'display:none;' }}">
                    <div class="flex-grow-1">
                        <input type="file" name="foto" id="foto" accept="image/jpeg,image/png,image/webp"
                            class="form-control-file @error('foto') is-invalid @enderror"
                            onchange="(function(i){var p=document.getElementById('fotoPreview');if(i.files&&i.files[0]){p.src=URL.createObjectURL(i.files[0]);p.style.display='block';}})(this)">
                        <small class="text-muted">Format JPG/PNG/WEBP, maksimal 2 MB.</small>
                        @error('foto')
                            <div class="text-danger" style="font-style: italic; font-size: 0.7rem;">{{ strtolower($message) }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="form-section mb-4 pb-2 border-bottom">
    <div class="d-flex align-items-center mb-2">
        <div class="mr-2 d-flex align-items-center justify-content-center text-white shadow-sm"
            style="width: 30px; height: 30px; border-radius: 8px; background: linear-gradient(135deg, #ea580c 0%, #d97706 100%);">
            <i class="pe-7s-map-marker" style="font-size: 1.1rem;"></i>
        </div>
        <h6 class="font-weight-bold mb-0" style="color: #1e293b; font-size: 0.95rem;">
            2. Alamat &amp; Domisili
        </h6>
    </div>
    <p class="text-muted mb-3" style="font-size: 0.78rem;">
        Detail tempat tinggal lengkap karyawan sesuai KTP / domisili.
    </p>

    <div class="form-row">
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label for="alamat" class="font-weight-bold" style="font-size: 0.85rem; color: #475569;">
                    Alamat Lengkap <span class="text-danger">*</span>
                </label>
                <textarea name="alamat" id="alamat" rows="2" placeholder="Masukkan jalan, gang, nomor rumah, dsb."
                    class="form-control @error('alamat') is-invalid @enderror" style="border-radius: 6px;">{{ $val('alamat') }}</textarea>
                @error('alamat')
                    <div class="invalid-feedback" style="font-style: italic; font-size: 0.7rem;">{{ strtolower($message) }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="form-row">
        @include('admin.dataMaster.karyawan._input', ['name' => 'rt', 'label' => 'RT', 'type' => 'text', 'col' => 2, 'maxlength' => 5])
        @include('admin.dataMaster.karyawan._input', ['name' => 'rw', 'label' => 'RW', 'type' => 'text', 'col' => 2, 'maxlength' => 5])
        @include('admin.dataMaster.karyawan._input', ['name' => 'kelurahan', 'label' => 'Kelurahan / Desa', 'type' => 'text', 'col' => 2])
        @include('admin.dataMaster.karyawan._input', ['name' => 'kecamatan', 'label' => 'Kecamatan', 'type' => 'text', 'col' => 3])
        @include('admin.dataMaster.karyawan._input', ['name' => 'kabupaten', 'label' => 'Kabupaten / Kota', 'type' => 'text', 'col' => 3])
    </div>
</div>

<div class="form-section mb-4 pb-2 border-bottom">
    <div class="d-flex align-items-center mb-2">
        <div class="mr-2 d-flex align-items-center justify-content-center text-white shadow-sm"
            style="width: 30px; height: 30px; border-radius: 8px; background: linear-gradient(135deg, #ea580c 0%, #d97706 100%);">
            <i class="pe-7s-portfolio" style="font-size: 1.1rem;"></i>
        </div>
        <h6 class="font-weight-bold mb-0" style="color: #1e293b; font-size: 0.95rem;">
            3. Data Kepegawaian &amp; Penugasan
        </h6>
    </div>
    <p class="text-muted mb-3" style="font-size: 0.78rem;">
        Penetapan kategori karyawan, jabatan, unit tugas, dan aturan jam kerja.
    </p>

    <div class="form-row">
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label for="kepegawaian_master_kategori_id" class="font-weight-bold" style="font-size: 0.85rem; color: #475569;">
                    Kategori Karyawan <span class="text-danger">*</span>
                </label>
                <select name="kepegawaian_master_kategori_id" id="kepegawaian_master_kategori_id"
                    class="form-control @error('kepegawaian_master_kategori_id') is-invalid @enderror" style="border-radius: 6px;">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach ($kategori as $item)
                        <option value="{{ $item->id }}" @selected((string) $val('kepegawaian_master_kategori_id') === (string) $item->id)>
                            {{ $item->nama_kategori }}
                        </option>
                    @endforeach
                </select>
                @error('kepegawaian_master_kategori_id')
                    <div class="invalid-feedback" style="font-style: italic; font-size: 0.7rem;">{{ strtolower($message) }}</div>
                @enderror
            </div>
        </div>

        @include('admin.dataMaster.karyawan._input', ['name' => 'jabatan', 'label' => 'Jabatan', 'type' => 'text', 'col' => 4])
        @include('admin.dataMaster.karyawan._input', ['name' => 'golongan', 'label' => 'Golongan (opsional)', 'type' => 'text', 'col' => 4])
    </div>

    <div class="form-row">
        @include('admin.dataMaster.karyawan._input', ['name' => 'unit', 'label' => 'Unit Penugasan', 'type' => 'text', 'col' => 4])

        <div class="col-md-4">
            <div class="position-relative form-group">
                <label for="kepegawaian_master_jam_kerja_id" class="font-weight-bold" style="font-size: 0.85rem; color: #475569;">
                    Jam Kerja <span class="text-danger">*</span>
                </label>
                <select name="kepegawaian_master_jam_kerja_id" id="kepegawaian_master_jam_kerja_id"
                    class="form-control @error('kepegawaian_master_jam_kerja_id') is-invalid @enderror" style="border-radius: 6px;">
                    <option value="">-- Pilih Jam Kerja --</option>
                    @foreach ($jamKerja as $item)
                        <option value="{{ $item->id }}" @selected((string) $val('kepegawaian_master_jam_kerja_id') === (string) $item->id)>
                            {{ $item->nama }} ({{ substr($item->jam_masuk, 0, 5) }} - {{ substr($item->jam_pulang_senin_kamis, 0, 5) }} / {{ substr($item->jam_pulang_jumat, 0, 5) }})
                        </option>
                    @endforeach
                </select>
                @error('kepegawaian_master_jam_kerja_id')
                    <div class="invalid-feedback" style="font-style: italic; font-size: 0.7rem;">{{ strtolower($message) }}</div>
                @enderror
            </div>
        </div>

        @include('admin.dataMaster.karyawan._input', ['name' => 'mulai_tugas', 'label' => 'Mulai Tugas', 'type' => 'date', 'col' => 4])
    </div>
</div>

<div class="form-section mb-4">
    <div class="d-flex align-items-center mb-2">
        <div class="mr-2 d-flex align-items-center justify-content-center text-white shadow-sm"
            style="width: 30px; height: 30px; border-radius: 8px; background: linear-gradient(135deg, #ea580c 0%, #d97706 100%);">
            <i class="pe-7s-study" style="font-size: 1.1rem;"></i>
        </div>
        <h6 class="font-weight-bold mb-0" style="color: #1e293b; font-size: 0.95rem;">
            4. Pendidikan, Kontak &amp; Lainnya
        </h6>
    </div>
    <p class="text-muted mb-3" style="font-size: 0.78rem;">
        Latar belakang pendidikan terakhir, kontak yang dapat dihubungi, dan sertifikasi.
    </p>

    <div class="form-row">
        <div class="col-md-3">
            <div class="position-relative form-group">
                <label for="akademik_pelengkap_jenjang_pendidikan_id" class="font-weight-bold" style="font-size: 0.85rem; color: #475569;">
                    Jenjang Pendidikan <span class="text-danger">*</span>
                </label>
                <select name="akademik_pelengkap_jenjang_pendidikan_id" id="akademik_pelengkap_jenjang_pendidikan_id"
                    class="form-control @error('akademik_pelengkap_jenjang_pendidikan_id') is-invalid @enderror" style="border-radius: 6px;">
                    <option value="">-- Pilih Jenjang --</option>
                    @foreach ($jenjang as $item)
                        <option value="{{ $item->id }}" @selected((string) $val('akademik_pelengkap_jenjang_pendidikan_id') === (string) $item->id)>
                            {{ $item->nama_jenjang_pendidikan }}
                        </option>
                    @endforeach
                </select>
                @error('akademik_pelengkap_jenjang_pendidikan_id')
                    <div class="invalid-feedback" style="font-style: italic; font-size: 0.7rem;">{{ strtolower($message) }}</div>
                @enderror
            </div>
        </div>

        @include('admin.dataMaster.karyawan._input', ['name' => 'prodi', 'label' => 'Program Studi (opsional)', 'type' => 'text', 'col' => 3])
        @include('admin.dataMaster.karyawan._input', ['name' => 'tahun_lulus', 'label' => 'Tahun Lulus', 'type' => 'text', 'col' => 3, 'maxlength' => 4])
        <div class="col-md-3">
            <div class="position-relative form-group">
                <label class="d-block font-weight-bold" style="font-size: 0.85rem; color: #475569;">
                    Sertifikasi Pendidik / Profesi
                </label>
                <input type="hidden" name="sertifikasi" value="0">
                <div class="custom-control custom-checkbox mt-2 pt-1">
                    <input type="checkbox" class="custom-control-input" id="sertifikasi" name="sertifikasi" value="1"
                        @checked((bool) $val('sertifikasi'))>
                    <label class="custom-control-label" for="sertifikasi" style="font-size: 0.88rem; cursor: pointer;">
                        Sudah Memiliki Sertifikasi
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="form-row">
        @include('admin.dataMaster.karyawan._input', ['name' => 'whatsapp', 'label' => 'No. WhatsApp (opsional)', 'type' => 'text', 'col' => 4])
        @include('admin.dataMaster.karyawan._input', ['name' => 'nuptk', 'label' => 'NUPTK (opsional)', 'type' => 'text', 'col' => 4])
        @include('admin.dataMaster.karyawan._input', ['name' => 'email', 'label' => 'Email (opsional)', 'type' => 'text', 'col' => 4])
    </div>
</div>

<!-- TOMBOL AKSI -->
<div class="form-actions-bar mt-4 pt-3 border-top">
    <a href="{{ route('karyawan.index') }}" class="btn btn-secondary px-4 shadow-sm" style="border-radius: 8px;">
        <i class="fa fa-reply mr-1"></i> Batal
    </a>
    <button type="submit" class="btn btn-xl text-white shadow-sm fw-bold px-4" id="submitBtn"
        style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 8px; border: none;">
        <i class="fa fa-save mr-1"></i> Simpan Data
    </button>
</div>
