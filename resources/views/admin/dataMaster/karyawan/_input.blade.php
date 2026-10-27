<div class="col-md-{{ $col ?? 12 }}">
    <div class="position-relative form-group">
        <label for="{{ $name }}" class="font-weight-bold" style="font-size: 0.85rem; color: #475569;">
            {{ $label }}
            @if (!str_contains(strtolower($label), 'opsional'))
                <span class="text-danger">*</span>
            @endif
        </label>
        <input name="{{ $name }}" id="{{ $name }}" type="{{ $type ?? 'text' }}"
            placeholder="{{ $placeholder ?? 'Masukkan ' . str_replace(' (opsional)', '', $label) }}"
            class="form-control @error($name) is-invalid @enderror"
            value="{{ $val($name, ($type ?? 'text') === 'date' ? 'Y-m-d' : null) }}"
            @if ($name === 'nipy' && $isEdit) readonly @endif
            @if (isset($maxlength)) maxlength="{{ $maxlength }}" @endif
            style="border-radius: 6px;">
        @error($name)
            <div class="invalid-feedback" style="font-style: italic; font-size: 0.7rem;">
                {{ strtolower($message) }}
            </div>
        @enderror
    </div>
</div>
