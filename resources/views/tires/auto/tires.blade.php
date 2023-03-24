@extends('layouts.app')

@section('body-title', 'category')
@section('title', 'lang-' . app()->getLocale() . ' country-' . app()->getLocale() . ' layout-both-columns page-category tax-display-enabled category-id-14 category-' . $season_title . ' category-id-parent-12 category-depth-level-3')

@section('content')
  <div class="container">
    <div class="row">
      <div class="main-content clearfix col-md-12 col-xl-12">
        <div id="left-column" class="col-md-12 col-lg-3">
          <!-- begin D:\OpenServer\domains\r1old/themes/classic/modules/ps_facetedsearch/ps_facetedsearch.tpl -->
          <div id="search_filters_wrapper" class="hidden-sm-down">
            <div id="search_filter_controls" class="hidden-md-up">

              <button class="btn btn-secondary ok">
                <i class="material-icons"></i>
                Labi
              </button>
            </div>
            @include('components.autotirefilter')
          </div>
        </div>
        <div id="content-wrapper" class="col-md-12 col-lg-9">
          <section id="main">
            <section id="products">
              {{--GRID VIEW--}}
              <div class="tire-image-container" style="display: none">
                <div class="tire-image-cards">
                  {{--                <div style="width: auto;">BRAND NAME</div>--}}
                  @php
                    $cbrand = '';
                    $index = 0;
                  @endphp
                  @foreach($tires as $tire)
                    @php
                      $brand = $tire->fullSize;
                      $tire->includeStock = true;
                      if ($cbrand!=$brand){
                        echo '</div><h4 class="tire-brand-name grid-t">' . $brand;
                        if ($index == 0){
                          switch ($season_id){
                          case 1:
                            echo ' <span class="tire-type-title">Vasaras riepas</span>';
                            break;
                          case 2:
                            echo ' <span class="tire-type-title">Ziemas riepas</span>';
                            break;
                          }
                        }
                        echo '<span style="margin: 0 auto;"></span>';
                        echo '<button type="button" class="btn-sm btn-outline-danger hidden-md-up sm-filter-btn" data-toggle="modal" data-target="#mobileFilterModal">
                                    Filtrs (' . $filterCount . ')
                                  </button></h4>
                        <div class="row grid-ex pr-1">';
                        $cbrand = $brand;
                        $stripe = 1;
                      } else {
                          $brand = str_replace(" ", "", $brand);
                      }
                    @endphp
                    @if($tire->price1)
                      <a
                        href="{{ route($current_url, [\Str::slug(\Tires::getAutoTireBrand($tire->brand_id)->title), strtolower(str_replace('/', '_', $tire->t_title)), $tire->tire_id]) }}"
                        class="grid-view-link"
                        data-article="{{ $tire->article }}">
                        <div class="tire-image-card sort-order">
                          <div class="text-center image-grid-overflow">
                            {!! App\Helper\Image::showGrid('auto', $tire->make_id) !!}
                          </div>

                          <div class="tire-list-caption">

                            <div class="card-title-text" data-toggle="tooltip" title="<div>{{$tire->title}}</div>">
                              {{$tire->title}}
                            </div>

                            <div class="tire-tread">
                              <b>{{$tire->d1}} / {{$tire->d2}} / {{$tire->d3}} </b>
                              <span data-toggle="tooltip"
                                    title="<span style='color: black'>{{ $tire->lisiDesc($tire->li, $tire->si) }}</span>">{{ $tire->li . $tire->si }}</span>
                              <span class="tire-image-code">{{$tire->code}}</span>
                            </div>
                            <div style="display: flex;">
                              <input type="checkbox" name="product_ids[]" value="{{$tire->tire_id}}"
                                     style="margin-right: 5px;">
                              <div class="rim-price-old" style="align-self: center;">€{{$tire->price1}}</div>
                              <div class="rim-price-red" style="align-self: center;">€{{$tire->price2}}</div>
                              {{--                            <i class="material-icons" style="margin-left: auto;">add_shopping_cart</i>--}}
                              <span style="margin-left: auto;" data-toggle="tooltip"
                                    title="<span style='color: black'>Pievienot grozam</span>">
{{--                              <button class="grid-buy-btn" data-toggle="modal"--}}
                                {{--                                      @hasrole('administrators') data-target=""--}}
                                {{--                                      @else data-target="#blockcart-modal"--}}
                                {{--                                      @endhasrole data-info="{{ $tire->tire_id }}" onclick="event.preventDefault()">--}}
                                {{--                                <i class="material-icons">add_shopping_cart</i>--}}
                                {{--                              </button>--}}

                              <button class="grid-buy-btn cart-shopping-button"
                                      data-toggle="modal"
                                      data-info="{{ $tire->tire_id }}"
                                      {{--                                      data-info="{{ $currTire->tire_id }}--}}
                                      onclick="event.preventDefault()"
                                        data-target="#">
                                  <i class="material-icons">add_shopping_cart</i>
                                  </button>
                            </span>

                              {{--                            <div class="clearfix atc_div text-right">--}}
                              {{--                              <button class="grid-buy-btn" data-toggle="modal"--}}
                              {{--                                      @hasrole('administrators') data-target=""--}}
                              {{--                                      @else data-target="#blockcart-modal"--}}
                              {{--                                      @endhasrole data-info="{{ $tire->tire_id }}" onclick="event.preventDefault()">--}}
                              {{--                              <i class="material-icons">add_shopping_cart</i>--}}
                              {{--                              </button>--}}
                              {{--                            </div>--}}


                              <span class="grid-dot {{ $tire->dotAvailable }} {{ $tire->stockCount }}"
                                    data-toggle="tooltip"
                                    data-html="true"
                                    onclick="event.preventDefault()"
                                    title="{{ $tire->stockAvailability }}">
                              <span class="sort-order" style="display: none;">{{ $tire->dotAvailable }}</span>
                            </span>
                            </div>
                          </div>
                          {{--                        <button class="grid-shopping-button grid-cart-btn" data-toggle="modal" data-target="#blockcart-modal" data-info="148204">Pirkt--}}
                          {{--                        </button>--}}


                        </div>
                      </a>
                    @endif
                    @php
                      $index++;
                    @endphp
                  @endforeach
                </div>
              </div>
              {{-- LIST VIEW--}}
              <div id="">
                <div id="js-product-list">
                  <div class="products row hide-price title-flip">

                    @php
                      $cbrand = '';
                      $index = 0;
                    @endphp
                    @foreach ($tires as $tire)
                      @php
                        $brand = $tire->fullSize;
                        $tire->includeStock = true;
                        if ($cbrand!=$brand){

                        if ($cbrand) {
                           echo '<h4 class="tire-brand-name">' . $cbrand;
                        }
                        if ($index == 0){
                          //<h4 style="display: inline-block;">Izvēlētie filtri: </h4>
                          echo '<button type="button" class="btn-sm btn-outline-danger hidden-md-up sm-filter-btn" data-toggle="modal" data-target="#mobileFilterModal">
                                    Filtrs ('. $filterCount .')
                                  </button><div class="filters" style="margin: 0 auto;"></div>';
                          switch ($season_id){
                          case 1:
                            echo '<span class="text-uppercase flipped-title tire-brand-name" style="color:black;">Vasaras riepas</span>';
                            break;
                          case 2:
                            echo '<span class="text-uppercase flipped-title tire-brand-name" style="color:black;">Ziemas riepas</span>';
                            break;
                          }
                          echo '</h4>';
                        }
                        echo '';

                        $cbrand = $brand;
                        $stripe = 1;
                      @endphp
                      {{--                    TIRES IMAGES--}}
                      {{--                      <div class="image-list-item">--}}
                      {{--                        <img src='/storage/app/public/auto/tread/215.png' style='width: 200px; height: 200px;'>--}}
                      {{--                      </div>--}}
                      {{--                      <div class="image-list-item">--}}
                      {{--                        <img src='/storage/app/public/auto/tread/215.png' style='width: 200px; height: 200px;'>--}}
                      {{--                      </div>--}}
                      {{--                      <div class="image-list-item">--}}
                      {{--                        <img src='/storage/app/public/auto/tread/215.png' style='width: 200px; height: 200px;'>--}}
                      {{--                      </div>--}}

                      {{-- TIRES TABLE --}}
                      <table id="tires-table"
                             class="table table-striped summer-sorter tires-table table-hover tablesorter">
                        <thead class="tires-thead sticky-table">
                        <tr>
                          <th scope="col"></th>
                          <th scope="col" class="table-tire-name-cell">Brends / modelis</th>
                          <th scope="col" class="hidden-sm-down text-center">LI/SI</th>
                          @if ($season_id == 2)
                            <th scope="col" class="hidden-sm-down text-center">Tips</th>
                          @endif
                          <th scope="col" class="hidden-sm-down text-center">Kods</th>

                          <th scope="col" class="hidden-sm-down">
                            <div class="tire-table-icon icon-tire-fuel" title="Degvielas ekonomija"></div>
                          </th>

                          <th scope="col" class="hidden-sm-down">
                            <div class="tire-table-icon icon-tire-rain" title="Slapjš segums"></div>
                          </th>

                          <th scope="col" class="hidden-sm-down">
                            <div class="tire-table-icon icon-tire-sound" title="Troksnis"></div>
                          </th>

                          <th id="store-price-button" scope="col" class="text-center">
                            Veikala cena
                          </th>

                          <th id="store-sale-button" scope="col" class="text-center">Akcijas cena</th>
                          <th scope="col" class="hidden-sm-down text-center">Piezīmes</th>
                          <th scope="col"></th>
                          <th scope="col">
                            <div class="tire-table-icon icon-question" title="Pieejamība" data-toggle="tooltip"></div>
                          </th>

                        </tr>
                        </thead>
                        <tbody id="tires-table-body">
                        @php
                          $cbrand = $brand;
                          $stripe = 1;
                      }
                        @endphp
                        @if ($loop->last) <h4 class="tire-brand-name">{{ $brand }}</h4> @endif
                        {{--                                            <article class="product_show_list cat-14 product-miniature js-product-miniature"--}}
                        {{--                                                     id="{{ str_replace(" ", "", $brand) }}"--}}
                        {{--                                                     data-id-product="{{ $tire->tire_id }}" data-id-product-attribute="{{ $tire->tire_id }}" itemscope=""--}}
                        {{--                                                     itemtype="http://schema.org/Product" data-brand="{{ $tire->brand }}"--}}
                        {{--                                                     data-atv="{{ $tire->d1 }}/{{ $tire->d2 }}R{{ $tire->d3 }}">--}}
                        {{--                                                <div class="thumbnail-container">--}}
                        {{--                                                    <a href="{{ route($current_url, [strtolower(\Tires::getAutoTireBrand($tire->brand_id)->title), $tire->slug, $tire->tire_id]) }}"--}}
                        {{--                                                       class="product-thumbnail">--}}
                        {{--                                                    </a>--}}
                        {{--                                                    <div class="product-description">--}}
                        {{--                                                        <input type="checkbox" value="{{ $tire->tire_id }}" name="product_ids[]">--}}
                        {{--                                                        <h1 class="h3 product-title" itemprop="name">--}}
                        {{--                                                            <a data-toggle="tooltip" data-html="true"--}}
                        {{--                                                                @if ($tire->image)--}}
                        {{--                                                                    title="<img src='{{ $tire->image }}' style='width: 280px; height: 280px;'>"--}}
                        {{--                                                                @else--}}
                        {{--                                                                    title="<img src='{{ asset('img/p/en-default-home_default.jpg') }}'>"--}}
                        {{--                                                                @endif--}}
                        {{--                                                                href="{{ route($current_url, [strtolower(\Tires::getAutoTireBrand($tire->brand_id)->title), $tire->slug, $tire->tire_id]) }}"--}}
                        {{--                                                                data-content="{{ $tire->title }}">--}}
                        {{--                                                                <div class="product-title-hidden">{{ $tire->title }}</div>--}}
                        {{--                                                            </a>--}}
                        {{--                                                        </h1>--}}
                        {{--                                                        <span class="tire_article" data-article="{{ $tire->article }}" style="display: none;"></span>--}}
                        {{--                                                        <div class="product-price-and-shipping" data-content="{{ $tire->title }}">--}}
                        {{--                                                        @if ($season === 2)--}}
                        {{--                                                          <span class="table-cell">{{ $tire->type }}</span>--}}
                        {{--                                                        @endif--}}
                        {{--                                                        <span class="hidden-sm-down table-cell">--}}
                        {{--                                                          <span data-toggle="tooltip" title="<span style='color: black'>Kravnesības indekss: 91 – 615 kg</span>">{{ $tire->li }}</span>--}}
                        {{--                                                          <span data-toggle="tooltip" title="<span style='color: black'>{{ $tire->si }}</span>">{{ $tire->si }}</span>--}}
                        {{--                                                        </span>--}}
                        {{--                                                        <span data-toggle="tooltip" title="<span style='color: black'>RSC – Runflat System Component (nulles spiediena riepa)</span>" class="hidden-sm-down table-cell prod-code">{{ $tire->code }}</span>--}}
                        {{--                                                        <span data-toggle="tooltip" title="<span style='color: black'>{{ $tire->eco }}</span>" class="hidden-sm-down table-cell fuel_efficiency">{{ $tire->eco }}</span>--}}
                        {{--                                                        <span data-toggle="tooltip" title="<span style='color: black'>{{ $tire->wet }}</span>" class="hidden-sm-down table-cell wet_grip">{{ $tire->wet }}</span>--}}
                        {{--                                                        <span data-toggle="tooltip" title="<span style='color: black'>{{ $tire->noise }}</span>" class="hidden-sm-down table-cell tire_noise">{{ $tire->noise }}</span>--}}
                        {{--                                                        <span class="sr-only">Veikala cena</span>--}}
                        {{--                                                        <span class="regular-price">€ {{ $tire->price1 }}</span>--}}
                        {{--                                                        <span class="sr-only">Akcijas cena</span>--}}
                        {{--                                                        <span itemprop="price" class="price">€ {{ $tire->price2 }}</span>--}}
                        {{--                                                        <span class="table-cell notes">--}}
                        {{--&nbsp;                                                          <span class="table-cell top40">Top 40</span>--}}
                        {{--                                                        </span>--}}
                        {{--                                                        <div class="clearfix atc_div">--}}
                        {{--                                                            <button class="btn grid-cart-btn btn-primary" data-toggle="modal" @if (Auth::user()) data-target="#quick-popup" @else data-target="#blockcart-modal" @endif data-info="{{ $tire->tire_id }}"><i--}}
                        {{--                                                                    class="material-icons">add_shopping_cart</i>--}}
                        {{--                                                            </button>--}}
                        {{--                                                            <span class="dot {{ $tire->dotAvailable }}" data-toggle="tooltip"--}}
                        {{--                                                                  data-html="true"--}}
                        {{--                                                                  title="{{ $tire->stockAvailability }}">--}}
                        {{--                                                                <span class="sort-order">{{ $tire->dotAvailable }}</span>--}}
                        {{--                                                            </span>--}}
                        {{--                                                        </div>--}}
                        {{--                                                    </div>--}}
                        {{--                                                    </div>--}}
                        {{--                                                </div>--}}
                        {{--                                            </article>--}}
                        <tr class="tire-table-row">
                          <th scope="row" class="tire-table-checkbox">
                            <input type="checkbox" value="{{ $tire->tire_id }}" name="product_ids[]"
                                   class="tire-table-checkbox">
                          </th>

                          <td class="table-tire-name-cell">
                            <a data-toggle="tooltip" data-html="true" class="tire-table-link"
                               title='{!! App\Helper\Image::show('auto', $tire->make_id) !!}'
                               href="{{ route($current_url, [\Str::slug(\Tires::getAutoTireBrand($tire->brand_id)->title), strtolower(str_replace('/', '_', $tire->t_title)), $tire->tire_id]) }}"
                               data-content="{{ $tire->title . ' ' . $tire->fullSize }}"
                               data-article="{{ $tire->article }}"
                               data-quantity="{{ $cartQty }}">
                              <div class="table-link-title">{{ $tire->title }}</div>
                            </a>
                          </td>

                          <td class="hidden-sm-down text-center">
                              <span data-toggle="tooltip"
                                    title="<span style='color: black'>{{ $tire->lisiDesc($tire->li, $tire->si) }}</span>">{{ $tire->li . $tire->si }}
                              </span>
                          </td>

                          @if ($season_id == 2)
                            <td scope="col" class="hidden-sm-down text-center">

                              @switch($tire->type)
                                @case(1)
                                <span data-toggle="tooltip">
                                  <img src="{{asset('images/ms.png')}}" alt="ms"
                                       title="<span>Centrāleiropas tipa ziemas riepa</span>" style="margin:0;">
                                </span>

                                @break

                                @case(2)
                                <span data-toggle="tooltip">
                                  <img src="{{asset('images/radzeb.png')}}" alt="radzojama"
                                       title="<span>Radžojama</span>" style="margin:0;">
                                </span>

                                @break

                                @case(3)
                                <span data-toggle="tooltip">
                                  <img src="{{asset('images/radzea.png')}}" alt="ar radzem"
                                       title="<span>Ar radzēm</span>" style="margin:0;">
                                </span>

                                @break

                                @case(4)
                                <span data-toggle="tooltip">
                                  <img src="{{asset('images/parsla.png')}}" alt="skandinavijas"
                                       title="<span>Skandināvijas tipa ziemas riepa</span>" style="margin:0;">
                                </span>
                                @break

                              @endswitch

                            </td>
                          @endif

                          <td class="hidden-sm-down text-center">
                            <span data-toggle="tooltip" title="<span style='color: black'>
				                    @php $codes = explode(' ', $tire->code); @endphp
                            @foreach ($codes as $code1)
                            @if (isset($code_array[$code1]))
                            {!! $code_array[$code1] . '<br>' !!}
                            @endif
                            @endforeach
                            @if (strpos($tire->code, 'DOT') !== false)
                            {!! $code_array['DOT'] !!}
                            @endif
                              </span>" class="hidden-sm-down table-cell prod-code">{{ $tire->code }}</span>
                          </td>

                          <td class="hidden-sm-down text-center">
                            <span data-toggle="tooltip"
                                  title="<span style='color: black'>{{ $tire->eco }}</span>">{{ $tire->eco }}</span>
                          </td>

                          <td class="hidden-sm-down text-center">
                            <span data-toggle="tooltip"
                                  title="<span style='color: black'>{{ $tire->wet }}</span>">{{ $tire->wet }}</span>
                          </td>

                          <td class="hidden-sm-down text-center">
                            <span data-toggle="tooltip"
                                  title="<span style='color: black'>{{ $tire->noise }}</span>">{{ $tire->noise }}</span>
                          </td>

                          <td id="store-price" class="text-center store-price">€ {{ $tire->price1 }}</td>
                          <td id="sale-price" class="text-center tire-price-red sale-price">€ {{ $tire->price2 }}</td>
                          <td class="hidden-sm-down text-center @if($tire->comment == 'Izpārdošana!') sellout @endif">{{$tire->comment}}</td>

                          <td class="shopping-cart-col">
                            <div class="clearfix atc_div text-right">
                              <button class="cart-shopping-button" data-toggle="modal"
                                @hasrole('administrators') data-target="#" @else data-target="#blockcart-modal" @endhasrole data-info="{{ $tire->tire_id }}"><i
                                class="material-icons">add_shopping_cart</i>
                              </button>
                            </div>
                          </td>

                          <td class="dot-availability text-center">
                            <span class="dot {{ $tire->dotAvailable }} {{ $tire->stockCount }}" data-toggle="tooltip"
                                  data-html="true"
                                  title="{{ $tire->stockAvailability }}">
                              <span class="sort-order">{{ $tire->dotAvailable }}</span>
                            </span>
                          </td>

                        </tr>
                        @php
                          $index++;
                        @endphp
                        @endforeach
                        </tbody>
                      </table>
                  </div>
                  {{ $tires->links() }}
                </div>
              </div>
            </section>
          </section>
        </div>
      </div>
      <div class="hidden-md-up text-xs-right up">
        <a href="#header" class="back-to-top-button">
          <i class="material-icons"></i>
        </a>
      </div>
      <div class="modal fade" id="mobileFilterModal" tabindex="-1" role="dialog"
           aria-labelledby="mobileFilterModalTitle" aria-hidden="true">
        <div class="modal-dialog mobile-filter-modal" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              @include('components.autotirefilter')
            </div>
          </div>
        </div>
      </div>

@endsection
