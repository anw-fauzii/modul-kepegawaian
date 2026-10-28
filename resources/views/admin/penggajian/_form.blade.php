@php
    $isEdit = isset($penggajian);
    $val = fn ($field, $default = null) => old($field, $isEdit ? $penggajian->{$field} : (request($field) ?? $default));
    $rp = fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
@endphp

<style>
    .pg-section { border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px 18px; margin-bottom: 18px; background: #fff; }
    .pg-section__title { font-weight: 700; font-size: 0.92rem; color: #1e293b; margin-bottom: 12px; display: flex; align-items: center; gap: 8px; }
    .pg-section__title .dot { width: 8px; height: 8px; border-radius: 50%; }
    .pg-input label { font-size: 0.78rem; font-weight: 600; color: #475569; margin-bottom: 3px; }
    .pg-input .input-group-text { font-size: 0.78rem; background: #f8fafc; color: #64748b; }
    .pg-summary { position: sticky; top: 12px; border-radius: 12px; border: 1px solid #e2e8f0; background: linear-gradient(160deg, #0f172a, #1e293b); color: #fff; padding: 18px; }
    .pg-summary .row-line { display: flex; justify-content: space-between; padding: 6px 0; font-size: 0.85rem; color: #cbd5e1; }
    .pg-summary .total { font-size: 1.5rem; font-weight: 800; color: #fb923c; }
</style>

<div class="row">
    <div class="col-lg-9">
        {{-- IDENTITAS --}}
        <div class="pg-section">
            <div class="pg-section__title"><span class="dot" style="background:#3b82f6"></span> Identitas &amp; Periode</div>
            <div class="form-row">
                <div class="col-md-6 mb-3">
                    <label class="font-weight-bold small text-muted mb-1">Karyawan <span class="text-danger">*</span></label>
                    <select name="karyawan_nipy" id="karyawan_nipy" class="form-control @error('karyawan_nipy') is-invalid @enderror" required>
                        <option value="">-- Pilih Karyawan --</option>
                        @foreach ($karyawanList as $k)
                            <option value="{{ $k->nipy }}"
                                data-unit="{{ $k->unit }}" data-jabatan="{{ $k->jabatan }}" data-golongan="{{ $k->golongan }}"
                                @selected((string) $val('karyawan_nipy') === (string) $k->nipy)>
                                {{ $k->nipy }} - {{ $k->nama_lengkap }}{{ $k->gelar ? ', ' . $k->gelar : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('karyawan_nipy') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="font-weight-bold small text-muted mb-1">Periode Bulan <span class="text-danger">*</span></label>
                    <select name="kepegawaian_master_bulan_id" class="form-control @error('kepegawaian_master_bulan_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Bulan --</option>
                        @foreach ($bulanList as $b)
                            <option value="{{ $b->id }}" @selected((string) $val('kepegawaian_master_bulan_id') === (string) $b->id)>
                                {{ $b->nama_bulan }}{{ $b->tahunAjaran ? ' - ' . $b->tahunAjaran->nama_tahun_ajaran : '' }}{{ $b->status ? ' ★ Aktif' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('kepegawaian_master_bulan_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-2 mb-3">
                    <label class="font-weight-bold small text-muted mb-1">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                        @foreach (\App\Models\Admin\Penggajian\Penggajian::STATUS as $key => $label)
                            <option value="{{ $key }}" @selected($val('status', 'draft') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="form-row">
                @foreach (['unit' => 'Unit', 'jabatan' => 'Jabatan', 'golongan' => 'Golongan'] as $f => $label)
                    <div class="col-md-4 mb-2">
                        <label class="font-weight-bold small text-muted mb-1">{{ $label }} <span class="text-muted font-weight-normal">(otomatis dari data karyawan)</span></label>
                        <input type="text" name="{{ $f }}" id="{{ $f }}" class="form-control @error($f) is-invalid @enderror" value="{{ $val($f) }}">
                        @error($f) <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                @endforeach
            </div>
        </div>

        {{-- PENERIMAAN --}}
        <div class="pg-section">
            <div class="pg-section__title"><span class="dot" style="background:#10b981"></span> Penerimaan</div>
            <div class="form-row">
                @foreach (\App\Models\Admin\Penggajian\Penggajian::PENERIMAAN as $f => $label)
                    <div class="col-md-4 mb-3 pg-input">
                        <label for="{{ $f }}">{{ $label }}</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                            <input type="number" step="1" {{ $f === 'pembulatan' ? '' : 'min=0' }} name="{{ $f }}" id="{{ $f }}"
                                class="form-control pg-in pg-in--plus @error($f) is-invalid @enderror"
                                value="{{ $val($f, 0) + 0 }}">
                            @error($f) <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- POTONGAN --}}
        <div class="pg-section">
            <div class="pg-section__title"><span class="dot" style="background:#ef4444"></span> Potongan</div>
            <div class="form-row">
                @foreach (\App\Models\Admin\Penggajian\Penggajian::POTONGAN as $f => $label)
                    <div class="col-md-4 mb-3 pg-input">
                        <label for="{{ $f }}">{{ $label }}</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                            <input type="number" step="1" min="0" name="{{ $f }}" id="{{ $f }}"
                                class="form-control pg-in pg-in--minus @error($f) is-invalid @enderror"
                                value="{{ $val($f, 0) + 0 }}">
                            @error($f) <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- RINGKASAN LIVE --}}
    <div class="col-lg-3">
        <div class="pg-summary">
            <div class="text-uppercase small font-weight-bold" style="letter-spacing: .06em; color:#94a3b8;">Ringkasan</div>
            <div class="row-line mt-2"><span>Total Penerimaan</span><strong id="sumPlus" class="text-success">Rp 0</strong></div>
            <div class="row-line"><span>Total Potongan</span><strong id="sumMinus" class="text-danger">Rp 0</strong></div>
            <hr style="border-color: rgba(255,255,255,.12);">
            <div class="small" style="color:#94a3b8;">Gaji Bersih</div>
            <div class="total" id="sumNet">Rp 0</div>

            <div class="mt-4 d-flex flex-column" style="gap: 8px;">
                <button type="submit" class="btn text-white font-weight-bold shadow-sm"
                    style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%); border: 0; border-radius: 8px;">
                    <i class="fa fa-save mr-1"></i> Simpan Slip Gaji
                </button>
                <a href="{{ route('penggajian.index') }}" class="btn btn-outline-light btn-sm" style="border-radius: 8px;">
                    <i class="fa fa-reply mr-1"></i> Batal
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var fmt = function (n) { return 'Rp ' + Math.round(n).toLocaleString('id-ID'); };
        var sum = function (sel) {
            var t = 0;
            document.querySelectorAll(sel).forEach(function (i) { t += parseFloat(i.value) || 0; });
            return t;
        };
        var recalc = function () {
            var plus = sum('.pg-in--plus'), minus = sum('.pg-in--minus');
            document.getElementById('sumPlus').textContent = fmt(plus);
            document.getElementById('sumMinus').textContent = fmt(minus);
            document.getElementById('sumNet').textContent = fmt(plus - minus);
        };
        document.querySelectorAll('.pg-in').forEach(function (i) { i.addEventListener('input', recalc); });
        recalc();

        // Isi otomatis unit/jabatan/golongan saat karyawan dipilih
        var sel = document.getElementById('karyawan_nipy');
        sel.addEventListener('change', function () {
            var opt = sel.options[sel.selectedIndex];
            ['unit', 'jabatan', 'golongan'].forEach(function (f) {
                var el = document.getElementById(f);
                if (el) el.value = opt.dataset[f] || '';
            });
        });
    });
</script>
