@php
    $items = $items ?? [];
    $depth = count($items);
@endphp

@if ($depth > 0)
    <nav class="breadcrumb brand-hub-breadcrumb" data-depth="{{ $depth }}" aria-label="Breadcrumb">
        <ol itemscope itemtype="https://schema.org/BreadcrumbList">
            @foreach ($items as $position => $item)
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    @if (!empty($item['url']))
                        <a itemprop="item" href="{{ $item['url'] }}">
                            <span itemprop="name">{{ $item['label'] }}</span>
                        </a>
                    @else
                        <span itemprop="name" aria-current="page">{{ $item['label'] }}</span>
                    @endif
                    <meta itemprop="position" content="{{ $position + 1 }}">
                </li>
            @endforeach
        </ol>
    </nav>
    @include('components.seo.json-ld-breadcrumb', ['items' => $items])
@endif
