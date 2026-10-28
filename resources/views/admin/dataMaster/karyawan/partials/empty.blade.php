<div class="kd-empty">
    <div class="kd-empty__icon"><i class="{{ $icon }}"></i></div>
    <h3>{{ $title }}</h3>
    <p>{{ $text }}</p>
    @isset($url)
        <a href="{{ $url }}" class="kd-btn kd-btn--ghost kd-btn--sm">
            <i class="fa fa-plus"></i> Tambah Data
        </a>
    @endisset
</div>
