@php
    $hubTreadMode = $hubTreadMode ?? false;
    $catalogAjaxUrl = $catalogAjaxUrl ?? null;
    $productHeading = $productHeading ?? trim(($currTire->title ?: '') . ' ' . $currTire->fullSize);
    $activeTireId = $activeTireId ?? ($hubTreadMode ? null : $currTire->tire_id);

    if ($hubTreadMode) {
        $hubSeason = (int) ($season ?? 0);
        $hubSeasonLabel = $hubSeason === 1 ? 'Vasaras' : 'Ziemas';
        $hubSizeCount = $tires->count();
        $hubMinPrice = $tires->min('price2');
        $hubMaxPrice = $tires->max('price2');
        // green/half-green = Ulbroka/Kalnciema; yellow = partner stock; red = check availability (grey dot)
        $hubInStockCount = $tires->filter(function ($tire) {
            return in_array(strtolower((string) ($tire->dotAvailable ?? '')), ['green', 'half-green'], true);
        })->count();
        $hubPartnerStockCount = $tires->filter(function ($tire) {
            return in_array(strtolower((string) ($tire->dotAvailable ?? '')), ['yellow', 'half-yellow'], true);
        })->count();
        $hubDescriptionSource = ($currTire->t_comment ?? '') ?: ($currBrand->b_comment ?? '');
        $hubDescription = trim(\Illuminate\Support\Str::limit(strip_tags($hubDescriptionSource), 360));
        $hubBrandTitle = $hubBreadcrumb['brand_title'] ?? '';
        $hubBrandSlug = $hubBreadcrumb['brand_slug'] ?? '';
    }
@endphp

@if ($hubTreadMode)
<div class="brand-hub-tread-page" itemscope itemtype="https://schema.org/WebPage">
@else
<section id="main" itemscope itemtype="https://schema.org/Product">
@endif
    <meta itemprop="url" content="{{ $hubTreadMode ? route('riepu-razotaji-tread', ['brand' => $hubBreadcrumb['brand_slug'], 'tread' => \App\Http\Controllers\AutoBrandHubController::treadSegment($hubBreadcrumb['tread_title'])]) : url()->full() }}">
  @if (!empty($hubBreadcrumb))
    @include('brands.partials.breadcrumb', [
        'items' => [
            ['label' => 'Sākums', 'url' => url('/')],
            ['label' => 'Riepu ražotāji', 'url' => route('riepu-razotaji')],
            ['label' => $hubBreadcrumb['brand_title'], 'url' => route('riepu-razotaji-brand', $hubBreadcrumb['brand_slug'])],
            ['label' => $hubBreadcrumb['tread_title']],
        ],
    ])
  @endif
    <div class="row{{ $hubTreadMode ? ' brand-hub-tread-hero' : '' }}">
        <div class="col-md-12 col-lg-4{{ $hubTreadMode ? ' brand-hub-tread-media' : '' }}">
            <section class="page-content" id="content">
                <div class="images-container">
                    {!! App\Helper\Image::treadZoom('auto', $currTire->make_id, $productHeading) !!}
                    <div class="js-qv-mask mask">
                        <ul class="product-images js-qv-product-images"></ul>
                    </div>
                </div>
                <div class="scroll-box-arrows">
                    <i class="material-icons left"></i>
                    <i class="material-icons right"></i>
                </div>
            </section>
        </div>
        <div class="col-md-12 col-lg-8">
          @if ($hubTreadMode)
            <div class="brand-hub-tread-head">
                <div class="brand-hub-tread-head__title-row">
                    <h1 class="brand-hub-tread-title" itemprop="name">{{ $productHeading }}</h1>
                    <span class="brand-hub-season-badge brand-hub-season-badge--{{ $hubSeason === 1 ? 'summer' : 'winter' }}">{{ $hubSeasonLabel }} riepas</span>
                </div>
                <div class="brand-hub-tread-stats">
                    <span class="brand-hub-stat">{{ $hubSizeCount }} {{ $hubSizeCount == 1 ? 'izmērs' : 'izmēri' }}</span>
                    @if ($hubMinPrice)
                        <span class="brand-hub-stat brand-hub-stat--price">
                            @if ($hubMaxPrice && $hubMaxPrice != $hubMinPrice)
                                € {{ $hubMinPrice }} – € {{ $hubMaxPrice }}
                            @else
                                No € {{ $hubMinPrice }}
                            @endif
                        </span>
                    @endif
                    @if ($hubInStockCount > 0)
                        <span class="brand-hub-stat brand-hub-stat--stock">{{ $hubInStockCount }} {{ $hubInStockCount == 1 ? 'izmērs' : 'izmēri' }} noliktavā</span>
                    @endif
                    @if ($hubPartnerStockCount > 0)
                        <span class="brand-hub-stat brand-hub-stat--partner">{{ $hubPartnerStockCount }} {{ $hubPartnerStockCount == 1 ? 'izmērs' : 'izmēri' }} pasūtāmi pie piegādātājiem</span>
                    @endif
                </div>
                @if ($hubDescription)
                    <p class="brand-hub-tread-desc">{{ $hubDescription }}</p>
                @endif
                <div class="brand-hub-tread-actions">
                    @if ($hubBrandSlug)
                        <a class="brand-hub-tread-btn" href="{{ route('riepu-razotaji-brand', $hubBrandSlug) }}">Visi {{ $hubBrandTitle }} modeļi</a>
                    @endif
                    @if ($hubBrandTitle)
                        <a class="brand-hub-tread-btn brand-hub-tread-btn--ghost" href="{{ \App\Http\Controllers\AutoBrandHubController::catalogUrlForBrand($hubBrandTitle, $hubSeason) }}">{{ $hubSeasonLabel }} riepu katalogs</a>
                    @endif
                </div>
            </div>
          @else
            <div class="row">
                <div class="col-sm-12 product-main-details">
                    <h1 class="h1 mt-1" itemprop="name">{{ $productHeading }}</h1>
                </div>
            </div>
          @endif
            @unless ($hubTreadMode)
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-6">
                    <div class="product-prices">
                        <div class="product-discount">
                            <span>Veikala cena:</span>
                            <span class="regular-price">€ {{ $currTire->price1 }}</span>
                        </div>
                        <div class="product-price h5 has-discount" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                            <link itemprop="availability" href="https://schema.org/InStock">
                            <meta itemprop="priceCurrency" content="EUR">
                            <div class="current-price">
                                <span>Akcijas cena:</span>
                                <span itemprop="price" content="{{ $currTire->price2 }}">€ {{ $currTire->price2 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-6">
                    <div class="product-add-to-cart">
                        <div class="product-quantity clearfix">
                            <div class="qty">
                                <div class="input-group bootstrap-touchspin" style="transform: none;">
                                    <span class="input-group-addon bootstrap-touchspin-prefix" style="display: none;"></span>
                                    <input type="hidden" name="article" class="tire_article" value="{{ $currTire->article }}">
                                    <input type="hidden" name="title" class="tire_title" value="{{ $currTire->fullName }}">
                                    <input type="text" name="qty" id="quantity_wanted" value="{{ $cartQty }}" class="input-group form-control" min="1" aria-label="Daudzums" style="display: block;">
                                    <span class="input-group-addon bootstrap-touchspin-postfix" style="display: none;"></span>
                                    <span class="input-group-btn-vertical">
                                        <button class="btn btn-touchspin js-touchspin bootstrap-touchspin-up" type="button">
                                            <i class="material-icons touchspin-up"></i>
                                        </button>
                                        <button class="btn btn-touchspin js-touchspin bootstrap-touchspin-down" type="button">
                                            <i class="material-icons touchspin-down"></i>
                                        </button>
                                    </span>
                                </div>
                            </div>
                            <div class="add">
                                <button class="btn btn-primary add-to-cart" data-toggle="modal" @if (Auth::user()) data-target="#" @else data-target="#blockcart-modal" @endif data-button-action="add-to-cart" data-info="{{ $currTire->tire_id }}">
                                    <i class="material-icons shopping-cart"></i>
                                    Pirkt
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-sm-12 col-md-4">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Platums</th>
                            <td>{{ $currTire->d1 }}</td>
                        </tr>
                        </thead>
                        <tbody>
                        <tr><th>Augstums</th><td>{{ $currTire->d2 }}</td></tr>
                        <tr><th>Diametrs</th><td>{{ $currTire->d3 }}</td></tr>
                        <tr><th>Kods</th><td>{{ $currTire->code }}</td></tr>
                        <tr>
                            <th>Li/Si</th>
                            <td>
                                <span data-toggle="tooltip" title="<span style='color: black'>{{ $currTire->lisiDesc($currTire->li, $currTire->si) }}</span>">{{ $currTire->li . $currTire->si }}</span>
                            </td>
                        </tr>
                        <tr><th>Degvielas ekonomija</th><td>{{ $currTire->eco }}</td></tr>
                        <tr><th>Slapjš segums</th><td>{{ $currTire->wet }}</td></tr>
                        <tr><th>Skaņa</th><td>{{ $currTire->noise }}</td></tr>
                        <tr>
                            <th>Piezīmes</th>
                            <td>{{ $currTire->autocomment ?: '-' }}</td>
                        </tr>
                        <tr><th>Pieejamība</th><td>{{ $currTire->available }}</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-sm-12 col-md-8">
                    <ul class="nav nav-tabs" style="border-bottom: none!important;">
                        @if ($currTire->t_comment)
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#tread" style="border-color: #68c0a8 #68c0a8 transparent">Apraksts</a>
                            </li>
                        @endif
                        @if ($currBrand->b_comment)
                            <li class="nav-item">
                                <a class="nav-link @if (!$currTire->t_comment) active @endif" data-toggle="tab" href="#brand" style="border-color: #68c0a8 #68c0a8 transparent">Par zīmolu</a>
                            </li>
                        @endif
                    </ul>
                    <div class="tab-content">
                        @if ($currTire->t_comment)
                            <div id="tread" class="container alert tab-pane active" style="border: 1px solid #68c0a8">
                                {!! $currTire->t_comment !!}
                            </div>
                        @endif
                        @if ($currBrand->b_comment)
                            <div id="brand" class="container alert tab-pane @if (!$currTire->t_comment) active @endif" style="border: 1px solid #68c0a8">
                                {!! $currBrand->b_comment !!}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endunless
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            @if ($hubTreadMode)
                <h2 class="brand-hub-sizes-heading">Visi pieejamie izmēri{{ isset($hubSizeCount) ? ' (' . $hubSizeCount . ')' : '' }}</h2>
            @endif
            <table id="tires-table" class="table table-striped summer-sorter tires-table table-hover tablesorter">
                <thead class="tires-thead" style="position:sticky; top: -1px;">
                <tr>
                    <th scope="col" class="text-center tread-tire-table-checkbox">
                        <input type="checkbox" value="only_selected" id="show-selected-tread-checkbox" class="tire-table-checkbox" title="Rādīt tikai atzīmētās preces" @if (request()->show_selected) checked @endif disabled>
                    </th>
                    <th scope="col" class="text-center">Izmērs</th>
                    <th scope="col" class="hidden-sm-down text-center">LI/SI</th>
                    <th scope="col" class="hidden-sm-down text-center">Kods</th>
                    <th scope="col" class="hidden-sm-down"><div class="tire-table-icon icon-tire-fuel" title="Degvielas ekonomija"></div></th>
                    <th scope="col" class="hidden-sm-down"><div class="tire-table-icon icon-tire-rain" title="Slapjš segums"></div></th>
                    <th scope="col" class="hidden-sm-down"><div class="tire-table-icon icon-tire-sound" title="Troksnis"></div></th>
                    <th id="store-price-button" scope="col" class="text-center">Veikala cena</th>
                    <th id="store-sale-button" scope="col" class="text-center">Akcijas cena</th>
                    <th scope="col" class="hidden-sm-down text-center">Piezīmes</th>
                    <th scope="col">Grozs</th>
                    <th scope="col"><div class="tire-table-icon icon-question" title="Pieejamība" data-toggle="tooltip"></div></th>
                </tr>
                </thead>
                <tbody id="tires-table-body">
                @foreach ($tires as $tire)
                    @php
                        $tire->includeStock = true;
                        $sizeUrl = $tire->link;
                    @endphp
                    <tr @if($activeTireId && $activeTireId == $tire->tire_id) style="font-weight: bold; background-color: #cbcbcb;"@endif class="tire-table-row @if (in_array($tire->tire_id, $selectedTires)) selected @endif">
                        <th class="tire-info" style="display: none;" data-article="{{ $tire->article }}" data-content="{{ $tire->fullName }}" data-quantity="{{ $cartQty }}"></th>
                        <th scope="row" class="tread-tire-table-checkbox text-center">
                            <input type="checkbox" value="{{ $tire->tire_id }}" @if (in_array($tire->tire_id, $selectedTires)) checked @endif name="product_ids[]" class="tire-table-checkbox">
                        </th>
                        <td class="tread-name-cell-size text-center">
                            <a href="{{ $sizeUrl }}"@if($hubTreadMode) class="brand-hub-size-link"@endif>{{ $tire->fullSize }}</a>
                        </td>
                        <td class="hidden-sm-down text-center">
                            <span data-toggle="tooltip" title="<span style='color: black'>{{ $tire->lisiDesc($tire->li, $tire->si) }}</span>">{{ $tire->li . $tire->si }}</span>
                        </td>
                        <td class="hidden-sm-down text-center tread-code-cell-size">
                            <span data-toggle="tooltip" title="<span style='color: black'>
                                @php $codes = explode(' ', $tire->code); @endphp
                                @foreach ($codes as $code)
                                    @if (isset($code_array[$code]))
                                        {!! $code_array[$code] . '<br>' !!}
                                    @endif
                                @endforeach
                                @if (strpos($tire->code, 'DOT') !== false && isset($code_array['DOT']))
                                    {!! $code_array['DOT'] !!}
                                @endif
                            </span>" class="hidden-sm-down table-cell prod-code">{{ $tire->code }}</span>
                        </td>
                        <td class="hidden-sm-down text-center"><span data-toggle="tooltip" title="<span style='color: black'>{{ $tire->eco }}</span>">{{ $tire->eco }}</span></td>
                        <td class="hidden-sm-down text-center"><span data-toggle="tooltip" title="<span style='color: black'>{{ $tire->wet }}</span>">{{ $tire->wet }}</span></td>
                        <td class="hidden-sm-down text-center"><span data-toggle="tooltip" title="<span style='color: black'>{{ $tire->noise }}</span>">{{ $tire->noise }}</span></td>
                        <td class="text-center store-price">€ {{ $tire->price1 }}</td>
                        <td class="text-center tire-price-red sale-price">€ {{ $tire->price2 }}</td>
                        <td class="hidden-sm-down text-center tread-comment-cell-size">{{ $tire->comment }}</td>
                        <td class="shopping-cart-col">
                            <div class="clearfix atc_div text-right">
                                <button class="grid-cart-btn" data-toggle="modal" @if (Auth::user()) data-target="#" @else data-target="#blockcart-modal" @endif data-info="{{ $tire->tire_id }}" data-url="{{ $hubTreadMode ? url()->current() : $tire->link }}">
                                    <i class="material-icons">add_shopping_cart</i>
                                </button>
                            </div>
                        </td>
                        <td class="dot-availability text-center">
                            <span class="dot {{ $tire->dotAvailable }}" data-toggle="tooltip" data-html="true" title="{{ $tire->stockAvailability }}">
                                <span class="sort-order">{{ $tire->dotAvailable }}</span>
                            </span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade js-product-images-modal" id="product-modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <figure>
                        <img class="js-modal-product-cover product-cover-modal" src="" alt="" title="" itemprop="image">
                        <figcaption class="image-caption">
                            <div id="product-description-short" itemprop="description"></div>
                        </figcaption>
                    </figure>
                    <aside id="thumbnails" class="thumbnails js-thumbnails text-sm-center">
                        <div class="js-modal-mask mask nomargin">
                            <ul class="product-images js-modal-product-images"></ul>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </div>
@if ($hubTreadMode)
</div>
@else
</section>
@endif

@if ($catalogAjaxUrl)
    <script>window.R1_TIRE_AJAX_URL = @json($catalogAjaxUrl);</script>
@endif
@push('scripts')
<script src="{{ \App\Helper\AssetHelper::v('js/productPageCart.js') }}" defer></script>
@endpush
