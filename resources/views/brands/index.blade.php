@extends('layouts.app')

@section('body-title', 'category')
@section('title', 'page-category layout-right-column tax-display-enabled')

@section('canonical_url', route('riepu-razotaji'))
@section('meta_title', 'Riepu ražotāji — auto riepu zīmoli | R1 Riepu Serviss')
@section('meta_description', 'Visi auto riepu ražotāji vienuviet — Michelin, Continental, GoodYear, Nokian un citi. Izvēlieties zīmolu un skatiet pieejamos modeļus ar cenām.')
@section('meta_keywords', config('seo.keywords.brands_index'))

@section('content')
@include('brands.partials.shell-open')

    @include('brands.partials.breadcrumb', [
        'items' => [
            ['label' => 'Sākums', 'url' => url('/')],
            ['label' => 'Riepu ražotāji'],
        ],
    ])

    <header class="brand-hub-header">
        <h1 class="brand-hub-title">Riepu ražotāji</h1>
        <p class="brand-hub-lead">
            Izvēlieties ražotāju, lai apskatītu pieejamos modeļus un izmērus.
            Pēc izvēles pasūtiet riepas internetā vai piesakiet montāžu
            <a href="{{ route('pieraksts') }}">e-pierakstā</a>.
        </p>
    </header>

    <div class="brand-hub-grid">
        @foreach ($brands as $brand)
            <a href="{{ route('riepu-razotaji-brand', $brand->slug) }}" class="brand-hub-card">
                @include('brands.partials.card-logo', ['brand' => $brand, 'logoUrl' => $brand->logo_url ?? null])
                <h2 class="brand-hub-card-title">{{ $brand->title }}</h2>
                <p class="brand-hub-card-meta">
                    {{ (int) $brand->tread_count }} {{ (int) $brand->tread_count === 1 ? 'modelis' : 'modeļi' }}
                    @if ((int) $brand->min_price > 0)
                        · no €{{ (int) $brand->min_price }}
                    @endif
                </p>
            </a>
        @endforeach
    </div>

@include('brands.partials.shell-close')
@endsection

