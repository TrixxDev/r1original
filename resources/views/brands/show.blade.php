@extends('layouts.app')

@section('body-title', 'category')
@section('title', 'page-category layout-right-column tax-display-enabled')

@section('canonical_url', route('riepu-razotaji-brand', $brand->slug))
@section('meta_title', $brand->title . ' riepas — modeļi un izmēri | R1 Riepu Serviss')
@section('meta_description', $brand->title . ' auto riepas — ' . $stats['tread_count'] . ' modeļi, ' . $stats['tire_count'] . ' izmēri. Salīdziniet cenas un pasūtiet internetā ar piegādi vai montāžu Rīgā un Ulbrokā.')
@section('meta_keywords', config('seo.keywords.brands_show'))

@section('content')
@include('brands.partials.shell-open')

    @include('brands.partials.breadcrumb', [
        'items' => [
            ['label' => 'Sākums', 'url' => url('/')],
            ['label' => 'Riepu ražotāji', 'url' => route('riepu-razotaji')],
            ['label' => $brand->title],
        ],
    ])

    <header class="brand-hub-hero">
        <div class="brand-hub-hero__inner">
            @if (!empty($brand->logo_url))
                <div class="brand-hub-hero__logo">
                    <img src="{{ $brand->logo_url }}" alt="{{ $brand->title }}" loading="lazy" decoding="async">
                </div>
            @endif
            <div class="brand-hub-hero__body">
                <h1 class="brand-hub-hero__title">{{ $brand->title }} riepas</h1>
                <div class="brand-hub-hero__stats">
                    <span class="brand-hub-stat">{{ $stats['tread_count'] }} {{ $stats['tread_count'] === 1 ? 'modelis' : 'modeļi' }}</span>
                    <span class="brand-hub-stat">{{ $stats['tire_count'] }} {{ $stats['tire_count'] === 1 ? 'izmērs' : 'izmēri' }}</span>
                    @if ($stats['min_price'] > 0)
                        <span class="brand-hub-stat brand-hub-stat--price">no €{{ $stats['min_price'] }}</span>
                    @endif
                </div>
                @if ($stats['has_summer'] || $stats['has_winter'])
                    <div class="brand-hub-catalog-links brand-hub-tread-actions">
                        @if ($stats['has_summer'])
			    <a href="{{ \App\Http\Controllers\AutoBrandHubController::catalogUrlForBrand($brand->title, 1) }}" class="brand-hub-tread-btn">Vasaras katalogs</a>
                        @endif
                        @if ($stats['has_winter'])
			    <a href="{{ \App\Http\Controllers\AutoBrandHubController::catalogUrlForBrand($brand->title, 2) }}" class="brand-hub-tread-btn @if ($stats['has_summer']) brand-hub-tread-btn--ghost @endif">Ziemas katalogs</a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </header>

    @if ($brand->b_comment)
        @php
            $plainComment = trim(strip_tags($brand->b_comment));
            $commentLong = mb_strlen($plainComment) > 280;
        @endphp
        <div class="brand-hub-description{{ $commentLong ? ' brand-hub-description--collapsible' : '' }}"@if($commentLong) data-collapsible @endif>
            <div class="brand-hub-description__inner">
                {!! $brand->b_comment !!}
            </div>
            @if ($commentLong)
                <button type="button" class="brand-hub-description__toggle" data-collapse-toggle aria-expanded="false">
                    Lasīt vairāk
                </button>
            @endif
        </div>
    @endif

    @php
        $summerTreads = $treads->where('season', 1)->count();
        $winterTreads = $treads->where('season', 2)->count();
        $showSeasonFilter = $summerTreads > 0 && $winterTreads > 0;
    @endphp

    <section class="brand-hub-models" data-brand-models>
        <div class="brand-hub-models__head">
            <h2 class="brand-hub-models__title">Pieejamie modeļi</h2>
            @if ($showSeasonFilter)
                <div class="brand-hub-season-filter" role="tablist" aria-label="Filtrēt pēc sezonas">
                    <button type="button" class="brand-hub-season-filter__btn is-active" data-season-filter="all" role="tab" aria-selected="true">Visi</button>
                    <button type="button" class="brand-hub-season-filter__btn" data-season-filter="1" role="tab" aria-selected="false">Vasaras ({{ $summerTreads }})</button>
                    <button type="button" class="brand-hub-season-filter__btn" data-season-filter="2" role="tab" aria-selected="false">Ziemas ({{ $winterTreads }})</button>
                </div>
            @endif
        </div>

        <div class="brand-hub-grid brand-hub-grid--treads">
            @foreach ($treads as $tread)
                <a href="{{ $tread->hub_url }}"
                   class="brand-hub-card brand-hub-card--tread"
                   data-tread-season="{{ (int) $tread->season }}">
                    <div class="brand-hub-card-image">
			{!! App\Helper\Image::showGrid('auto', $tread->tread_id, '', $brand->title . ' ' . $tread->t_title) !!}
                    </div>
                    <h3 class="brand-hub-card-title">{{ $tread->t_title }}</h3>
                    <p class="brand-hub-card-meta">
                        <span class="brand-hub-season-badge brand-hub-season-badge--{{ (int) $tread->season === 1 ? 'summer' : 'winter' }}">
                            {{ $tread->season_label }}
                        </span>
                        · {{ (int) $tread->tire_count }} {{ (int) $tread->tire_count === 1 ? 'izmērs' : 'izmēri' }}
                        @if ((int) $tread->min_price > 0)
                            · no €{{ (int) $tread->min_price }}
                        @endif
                    </p>
                </a>
            @endforeach
        </div>

        <p class="brand-hub-models__empty" data-models-empty hidden>Neviens modelis neatbilst izvēlētajam filtram.</p>
    </section>

    <script>
    (function () {
        var root = document.querySelector('[data-brand-models]');
        if (!root) return;

        var filterBtns = root.querySelectorAll('[data-season-filter]');
        var cards = root.querySelectorAll('[data-tread-season]');
        var emptyMsg = root.querySelector('[data-models-empty]');

        filterBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var season = btn.getAttribute('data-season-filter');
                filterBtns.forEach(function (b) {
                    b.classList.toggle('is-active', b === btn);
                    b.setAttribute('aria-selected', b === btn ? 'true' : 'false');
                });
                var visible = 0;
                cards.forEach(function (card) {
                    var show = season === 'all' || card.getAttribute('data-tread-season') === season;
                    card.hidden = !show;
                    if (show) visible++;
                });
                if (emptyMsg) emptyMsg.hidden = visible > 0;
            });
        });

        var desc = document.querySelector('[data-collapsible]');
        if (!desc) return;
        var toggle = desc.querySelector('[data-collapse-toggle]');
        if (!toggle) return;
        toggle.addEventListener('click', function () {
            var expanded = desc.classList.toggle('is-expanded');
            toggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
            toggle.textContent = expanded ? 'Paslēpt' : 'Lasīt vairāk';
        });
    })();
    </script>

@include('brands.partials.shell-close')
@endsection

