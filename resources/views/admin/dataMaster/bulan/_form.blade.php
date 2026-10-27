@php
    $isEdit = isset($bulan);
    $val = fn($field) => old($field, $isEdit ? $bulan->$field : null);
    $statusChecked = old('status', $isEdit ? $bulan->status : false);
@endphp

<div class="form-row">
    <div class="col-md-12">
        <div class="position-relative form-group">
            <label for="nama_bulan">Nama Bulan</label>
            <input name="nama_bulan" id="nama_bulan" placeholder="Masukkan Nama Bulan" type="text"
                class="form-control @error('nama_bulan') is-invalid @enderror"
                value="{{ $val('nama_bulan') }}">
            @error('nama_bulan')
                <div class="invalid-feedback" style="font-style: italic; font-size: 0.7rem;">
                    {{ strtolower($message) }}
                </div>
            @enderror
        </div>
    </div>
    <div class="col-md-12">
        <div class="position-relative form-group">
            <label for="akademik_master_tahun_ajaran_id">Tahun Ajaran</label>
            <select name="akademik_master_tahun_ajaran_id" id="akademik_master_tahun_ajaran_id"
                class="form-control @error('akademik_master_tahun_ajaran_id') is-invalid @enderror">
                <option value="">-- Pilih Tahun Ajaran --</option>
                @foreach ($tahunAjaran as $item)
                    <option value="{{ $item->id }}" @selected((string) $val('akademik_master_tahun_ajaran_id') === (string) $item->id)>
                        {{ $item->nama_tahun_ajaran }} - {{ $item->semester }}
                    </option>
                @endforeach
            </select>
            @error('akademik_master_tahun_ajaran_id')
                <div class="invalid-feedback" style="font-style: italic; font-size: 0.7rem;">
                    {{ strtolower($message) }}
                </div>
            @enderror
        </div>
    </div>
</div>
<div class="form-row">
    <div class="col-md-12">
        <div class="position-relative form-group">
            <label class="d-block">Status</label>
            <input type="hidden" name="status" value="0">
            <div class="custom-control custom-switch mt-1">
                <input type="checkbox" class="custom-control-input" id="status" name="status" value="1"
                    @checked((bool) $statusChecked)>
                <label class="custom-control-label" for="status">Aktif</label>
            </div>
        </div>
    </div>
</div>
<div class="form-group">
    <button type="submit" class="btn btn-xl text-white shadow-sm fw-bold px-4" id="submitBtn"
        style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 8px; border: none;">
        <i class="fa fa-save mr-1"></i> Simpan
    </button>
</div>
