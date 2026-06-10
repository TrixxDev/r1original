@php
    $logoUrl = $logoUrl ?? ($brand->logo_url ?? null);
@endphp
<div class="brand-hub-card-logo{{ $logoUrl ? ' brand-hub-card-logo--image' : '' }}">
    @if ($logoUrl)
        <img src="{{ $logoUrl }}" alt="{{ $brand->title }}" class="brand-hub-card-logo-img" loading="lazy" decoding="async">
    @else
        <span class="brand-hub-card-initial">{{ mb_strtoupper(mb_substr($brand->title, 0, 1)) }}</span>
    @endif
</div>
