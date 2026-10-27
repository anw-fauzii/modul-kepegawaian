@php
    $isEdit = isset($jamKerja);
    $time = fn($field) => old($field, $isEdit && $jamKerja->$field ? substr($jamKerja->$field, 0, 5) : null);
    $fields = [
        ['jam_masuk', 'Jam Masuk'],
        ['jam_pulang_senin_kamis', 'Jam Pulang Senin - Kamis'],
        ['jam_pulang_jumat', 'Jam Pulang Jumat'],
    ];
@endphp
<div class="form-row">
    <div class="col-md-12">
        <div class="position-relative form-group">
            <label for="nama">Nama Jam Kerja</label>
            <input name="nama" id="nama" placeholder="Masukkan Nama Jam Kerja" type="text"
                class="form-control @error('nama') is-invalid @enderror"
                value="{{ old('nama', $isEdit ? $jamKerja->nama : null) }}">
            @error('nama')
                <div class="invalid-feedback" style="font-style: italic; font-size: 0.7rem;">
                    {{ strtolower($message) }}
                </div>
            @enderror
        </div>
    </div>
</div>
<div class="form-row">
    @foreach ($fields as [$name, $label])
        <div class="col-md-4">
            <div class="position-relative form-group">
                <label for="{{ $name }}">{{ $label }}</label>
                <input name="{{ $name }}" id="{{ $name }}" type="text" placeholder="00:00"
                    class="form-control timepicker-24 @error($name) is-invalid @enderror"
                    value="{{ $time($name) }}" autocomplete="off">
                @error($name)
                    <div class="invalid-feedback" style="font-style: italic; font-size: 0.7rem;">
                        {{ strtolower($message) }}
                    </div>
                @enderror
            </div>
        </div>
    @endforeach
</div>
<div class="form-group">
    <button type="submit" class="btn btn-xl text-white shadow-sm fw-bold px-4" id="submitBtn"
        style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%) !important; border-radius: 8px; border: none;">
        <i class="fa fa-save mr-1"></i> Simpan
    </button>
</div>