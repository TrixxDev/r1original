@extends('layouts.app')

@section('body-title', 'category')
@section('title', 'lang-' . app()->getLocale() . ' country-' . app()->getLocale() . ' layout-both-columns page-category tax-display-enabled category-id-2 category-kvadraciklu-riepas category-id-parent-12 category-depth-level-3')

@section('content')

  <div class="container">
    <div class="row">
      <div class="main-content clearfix col-md-12 col-xl-12">

        <div id="left-column" class="col-md-12 col-lg-3">
          <!-- begin D:\OpenServer\domains\r1old/themes/classic/modules/ps_facetedsearch/ps_facetedsearch.tpl -->
          @include('components.quadtirefilter')
        </div>
        <div id="content-wrapper" class="col-md-12 col-lg-9">
          <section id="main">
            <section id="products" class="">
              <div class="tire-image-container" style="display: none">
                <div class="tire-image-cards">
                  {{-- GRID VIEW --}}
                  @php
                    $cbrand = '';
                    $index = 0;
                  @endphp
                  @foreach($tires as $tire)
                    @php
                      $brand = $tire->fullSize;
                      $tire->includeStock = true;
                      if ($cbrand!=$brand){
                        if ($index == 0) {
                          echo '</div><h4 class="tire-brand-name grid-t">' . $brand;
                          echo ' <span class="tire-type-title">kvadraciklu riepas</span><span style="margin: 0 auto;"></span><button type="button" class="btn-sm btn-outline-danger hidden-md-up sm-filter-btn" data-toggle="modal" data-target="#mobileFilterModal">
                                    Filtrs
                                  </button></h4></h4><div class="row grid-ex pr-1">';
                        } else {
                          echo '</div><h4 class="tire-brand-name grid-t">' . $brand . '<span style="margin: 0 auto;"></span><button type="button" class="btn-sm btn-outline-danger hidden-md-up sm-filter-btn" data-toggle="modal" data-target="#mobileFilterModal">';
                          echo '</h4><div class="row grid-ex pr-1">';
                        }
                        $cbrand = $brand;
                        $stripe = 1;
                      } else {
                          $brand = str_replace(" ", "", $brand);
                      }
                    @endphp
                    @if($tire->price1)
                      <a href="{{ route('kvadraciklu-riepa', [strtolower(\Tires::getQuadrTireBrand($tire->tread->brand_id)->title), strtolower(str_replace('/', '_', $tire->tread->title)), $tire->tire_id]) }}" class="grid-view-link">
                        <div class="tire-image-card sort-order">
                          <div class="text-center image-grid-overflow">
                            {!! App\Helper\Image::showGrid('quadr', $tire->make_id) !!}
                          </div>

                          <div class="tire-list-caption">

                            <div class="card-title-text" data-toggle="tooltip" title="<div>{{$tire->title}}</div>">
                              {{$tire->title}}
                            </div>

                            <div class="tire-tread">
                              <b>{{$tire->d1}} / {{$tire->d2}} / {{$tire->d3}} </b>
                              <span class="tire-image-code">{{$tire->code}}</span>
                            </div>
                            <div style="display: flex;">
                              <input type="checkbox" name="product_ids[]" value="{{$tire->tire_id}}" style="margin-right: 5px;">
                              <div class="rim-price-old" style="align-self: center;">€{{$tire->price1}}</div>
                              <div class="rim-price-red" style="align-self: center;">€{{$tire->price2}}</div>
{{--                              <i class="material-icons" style="margin-left: auto;">add_shopping_cart</i>--}}
{{--                              <span class="grid-dot {{ $tire->dotAvailable }} {{ $tire->stockCount }}" data-toggle="tooltip"--}}
{{--                                    data-html="true"--}}
{{--                                    title="{{ $tire->stockAvailability }}">--}}
                                <span style="margin-left: auto;" data-toggle="tooltip" data-html="true"
                                      title="<span style='color: black'>Pievienot grozam</span>">

                                  <button class="grid-buy-btn cart-shopping-button"
                                          data-toggle="modal"
                                          data-info="{{ $tire->tire_id }}"
                                          {{--                                      data-info="{{ $currTire->tire_id }}--}}
                                          onclick="event.preventDefault()"
                                          @hasrole('administrators')
                                            data-target="#"
                                          @else
                                            data-target="#blockcart-modal"
                                          @endhasrole>
                                    <i class="material-icons">add_shopping_cart</i>
                                  </button>
                                </span>
                                <span class="grid-dot {{ $tire->dotAvailable }} {{ $tire->stockCount }}"
                                      data-toggle="tooltip"
                                      data-html="true"
                                      onclick="event.preventDefault()"
                                      title="{{ $tire->stockAvailability }}"></span>
                                <span class="sort-order" style="display: none;">{{ $tire->dotAvailable }}</span>
                              </div>
                            </div>
                          </div>
                        </a>
                    @endif
                    @php
                      $index++;
                    @endphp
                    @endforeach
                </div>
              </div>
              {{-- BREADCRUMBS --}}
{{--              <div id="">--}}
{{--                <div class="row products-selection">--}}
{{--                  <div class="col-md-8 hidden-md-down">--}}
{{--                    <nav data-depth="3" class="breadcrumb hidden-sm-down">--}}
{{--                      <ol itemscope="" itemtype="http://schema.org/BreadcrumbList">--}}
{{--                        <li itemprop="itemListElement" itemscope=""--}}
{{--                            itemtype="http://schema.org/ListItem">--}}
{{--                          <a itemprop="item" href="/">--}}
{{--                            <span itemprop="name">Sākumlapa</span>--}}
{{--                          </a>--}}
{{--                          <meta itemprop="position" content="1">--}}
{{--                        </li>--}}
{{--                        <li itemprop="itemListElement" itemscope=""--}}
{{--                            itemtype="http://schema.org/ListItem">--}}
{{--                          <span itemprop="name">Kvadraciklu riepas</span>--}}
{{--                          <meta itemprop="position" content="2">--}}
{{--                        </li>--}}
{{--                      </ol>--}}
{{--                    </nav>--}}
{{--                  </div>--}}
{{--                </div>--}}
{{--              </div>--}}

              {{-- LIST VIEW --}}
              <div id="">
                <div id="js-product-list">
                  <div class="products row hide-price title-flip">
                    @php
                      $cbrand = '';
                      $index = 0;
                    @endphp
                    @foreach ($tires as $tire)
{{--                      @php--}}
{{--                        $tire->includeStock = true;--}}
{{--                        $brand = $tire->fullSize;--}}
{{--                        if ($cbrand!=$brand){--}}
{{--                            echo '<h4 class="custom_brand_name ' . str_replace([" ", "x", "/", "-"], "", $brand) . '"--}}
{{--                            style="display: block;">' . $brand . '</h4>';--}}
{{--                            $cbrand = $brand;--}}
{{--                            $stripe = 1;--}}
{{--                        } else {--}}
{{--                            $brand = str_replace(" ", "", $brand);--}}
{{--                        }--}}
{{--                      @endphp--}}
                      @php
                        $brand = $tire->fullSize;
                        $tire->includeStock = true;
                        if ($cbrand!=$brand){
                          if($index == 0) {
                            echo '<button type="button" class="btn-sm btn-outline-danger hidden-md-up sm-filter-btn" data-toggle="modal" data-target="#mobileFilterModal">
                                    Filtrs
                                  </button><div class="filters" style="margin: 0 auto;"></div>';
                            echo '<h4 class="tire-brand-name">' . $cbrand . '<span class="tire-type-title flipped-title">Kvadraciklu riepas</span></h4>';
                          } else {
                            echo '<h4 class="tire-brand-name">' . $cbrand . '</h4>';
                          }
                      @endphp
                      <table id="tires-table" class="table table-striped quadr-sorter tires-table table-hover tablesorter">
                        <thead class="tires-thead sticky-top">
                        <tr>
                          <th scope="col"></th>
                          <th scope="col" class="table-tire-name-cell" style="width:50%;">Brends / modelis</th>
                          <th scope="col" class="text-center">Kods</th>
                          <th scope="col" id="store-price-button" class="text-center">Veikala cena</th>
                          <th scope="col" id="store-sale-button" class="text-center">Akcijas cena</th>
                          <th scope="col" class="hidden-sm-down text-center">Piezīmes</th>
                          <th scope="col"></th>
                          <th scope="col"><div class="tire-table-icon icon-question" title="Pieejamība" data-toggle="tooltip"></div></th>

                        </tr>
                        </thead>
                        <tbody id="tires-table-body">
                        @php
                          $cbrand = $brand;
                          $stripe = 1;
                        } else {
                            $brand = str_replace(" ", "", $brand);
                        }
                        @endphp
                        @if ($loop->last) <h4 class="tire-brand-name">{{ $brand }}</h4> @endif
{{--                        <article class="product_show_list cat-14 product-miniature js-product-miniature"--}}
{{--                                 id="{{ str_replace(" ", "", $brand) }}"--}}
{{--                                 data-id-product="{{ $tire->tire_id }}" data-id-product-attribute="{{ $tire->tire_id }}"--}}
{{--                                 itemscope=""--}}
{{--                                 itemtype="http://schema.org/Product" data-brand="{{ $tire->brand }}"--}}
{{--                                 data-atv="{{ $tire->d1 }}{{ $tire->sep }}{{ $tire->d2 }}{{ $tire->sep2 }}{{ $tire->d3 }}">--}}
{{--                          <div class="thumbnail-container">--}}
{{--                            <a--}}
{{--                              href="{{ route('kvadraciklu-riepa', [strtolower(\Tires::getQuadrTireBrand($tire->tread->brand_id)->title), $tire->tread->slug, $tire->tire_id]) }}"--}}
{{--                              class="thumbnail product-thumbnail">--}}
{{--                              @if ($tire->image)--}}
{{--                                <img src='/storage/app/public/quadr/tread/{{ $tire->image }}'--}}
{{--                                     style='width: 280px; height: 280px;'>--}}
{{--                              @else--}}
{{--                                <img src='{{ asset('img/p/en-default-home_default.jpg') }}'>--}}
{{--                              @endif--}}
{{--                            </a>--}}
{{--                            <div class="product-description">--}}
{{--                              <input type="checkbox" value="{{ $tire->tire_id }}" name="product_ids[]">--}}
{{--                              <h1 class="h3 product-title" itemprop="name">--}}
{{--                                <a data-toggle="tooltip" data-html="true"--}}
{{--                                   @if ($tire->image)--}}
{{--                                   title="<img src='/storage/app/public/quadr/tread/{{ $tire->image }}' style='width: 280px; height: 280px;'>"--}}
{{--                                   @else--}}
{{--                                   title="<img src='{{ asset('img/p/en-default-home_default.jpg') }}'>"--}}
{{--                                   @endif--}}
{{--                                   href="{{ route('kvadraciklu-riepa', [strtolower(\Tires::getQuadrTireBrand($tire->tread->brand_id)->title), $tire->tread->slug, $tire->tire_id]) }}"--}}
{{--                                   data-content="{{ $tire->title }}">--}}
{{--                                  <div class="product-title-hidden">{{ $tire->title }}</div>--}}
{{--                                </a>--}}
{{--                              </h1>--}}
{{--                              <div class="product-price-and-shipping" data-content="{{ $tire->title }}">--}}
{{--                                                    <span class="hidden-sm-down table-cell">--}}
{{--                                                        <span data-toggle="tooltip"--}}
{{--                                                              title="<span style='color: black'>Kravnesības indekss: 91 – 615 kg</span>">{{ $tire->li }}</span>--}}
{{--                                                        <span data-toggle="tooltip"--}}
{{--                                                              title="<span style='color: black'>H</span>">{{ $tire->si }}</span>--}}
{{--                                                    </span>--}}
{{--                                <span class="sr-only">Veikala cena</span>--}}
{{--                                <span class="regular-price">€ {{ $tire->price1 }}</span>--}}
{{--                                <span class="sr-only">Akcijas cena</span>--}}
{{--                                <span itemprop="price" class="price">€ {{ $tire->price2 }}</span>--}}
{{--                                <span class="table-cell notes">--}}
{{--&nbsp;                                                      <span class="table-cell top40">Top 40</span>--}}
{{--                                                    </span>--}}
{{--                                <div class="clearfix atc_div">--}}
{{--                                  <button class="btn grid-cart-btn btn-primary" data-toggle="modal"--}}
{{--                                          @if (Auth::user()) data-target="#quick-popup"--}}
{{--                                          @else data-target="#blockcart-modal" @endif data-info="{{ $tire->tire_id }}">--}}
{{--                                    <i--}}
{{--                                      class="material-icons">add_shopping_cart</i>--}}
{{--                                  </button>--}}
{{--                                  <span class="dot {{ $tire->dotAvailable }}" data-toggle="tooltip"--}}
{{--                                        data-html="true"--}}
{{--                                        title="{{ $tire->stockAvailability }}">--}}
{{--                                                            <span class="sort-order">{{ $tire->dotAvailable }}</span>--}}
{{--                                                        </span>--}}
{{--                                </div>--}}
{{--                              </div>--}}
{{--                            </div>--}}
{{--                          </div>--}}
{{--                        </article>--}}
                        <tr class="tire-table-row">
                          <th scope="row" class="tire-table-checkbox">
                            <input type="checkbox" value="{{ $tire->tire_id }}" name="product_ids[]"
                                   class="tire-table-checkbox">
                          </th>

                          <td class="table-tire-name-cell">
                            <a data-toggle="tooltip" data-html="true" class="tire-table-link"
                               title='{!! App\Helper\Image::show('quadr', $tire->make_id) !!}'
                               href="{{ route('kvadraciklu-riepa', [strtolower(\Tires::getQuadrTireBrand($tire->tread->brand_id)->title), strtolower(str_replace('/', '_', $tire->tread->title)), $tire->tire_id]) }}"
                               data-content="{{ $tire->title . ' ' . $tire->fullSize }}"
                               data-article="{{ $tire->article }}"
                               data-quantity="{{ $cartQty }}">
                              {{ $tire->title }}
                            </a>
                          </td>

{{--                          <td class="hidden-sm-down text-center">--}}
{{--                            <span data-toggle="tooltip"--}}
{{--                                  title="<span style='color: black'>RSC – Runflat System Component (nulles spiediena riepa)</span>"--}}
{{--                                  class="hidden-sm-down table-cell prod-code">{{ $tire->code }}</span>--}}
{{--                          </td>--}}

{{--                          <td class="hidden-sm-down text-center">--}}
{{--                            <span data-toggle="tooltip"--}}
{{--                                  title="<span style='color: black'>{{ $tire->eco }}</span>">{{ $tire->eco }}</span>--}}
{{--                          </td>--}}

{{--                          <td class="hidden-sm-down text-center">--}}
{{--                            <span data-toggle="tooltip"--}}
{{--                                  title="<span style='color: black'>{{ $tire->wet }}</span>">{{ $tire->wet }}</span>--}}
{{--                          </td>--}}

{{--                          <td class="hidden-sm-down text-center">--}}
{{--                            <span data-toggle="tooltip"--}}
{{--                                  title="<span style='color: black'>{{ $tire->noise }}</span>">{{ $tire->noise }}</span>--}}
{{--                          </td>--}}
                          <td class="text-center">{{$tire->code}}</td>
                          <td id="store-price" class="text-center store-price">€ {{ $tire->price1 }}</td>
                          <td id="sale-price" class="text-center tire-price-red sale-price">€ {{ $tire->price2 }}</td>
                          <td class="hidden-sm-down text-center"></td>

                          <td class="shopping-cart-col">
                            <div class="clearfix atc_div text-right">
                              <button class="cart-shopping-button" data-toggle="modal"
                                      @if (Auth::user()) data-target="#" @else data-target="#blockcart-modal"
                                      @endif data-info="{{ $tire->tire_id }}"><i
                                  class="material-icons">add_shopping_cart</i>
                              </button>
                            </div>
                          </td>

                          <td class="dot-availability text-center">
                            <span class="dot {{ $tire->dotAvailable }}" data-toggle="tooltip"
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
                  <nav class="pagination">
                    <div class="col-md-12">
                    </div>
                  </nav>
                </div>
                {{ $tires->links() }}
              </div>
              <div id="js-product-list-bottom">
                <div id="js-product-list-bottom"></div>
              </div>
            </section>
          </section>
        </div>
      </div>
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
          @include('components.quadtirefilter')
        </div>
      </div>
    </div>
  </div>


@endsection
