@extends('layouts.app')

@section('body-title', 'category')
@section('title', 'lang-' . app()->getLocale() . ' country-' . app()->getLocale() . ' layout-both-columns page-category tax-display-enabled category-id-16 category-motociklu-riepas category-id-parent-12 category-depth-level-3')

@section('content')

  <div class="container">
    <div class="row">
      <div class="main-content clearfix col-md-12 col-xl-12">

        <div id="left-column" class="col-md-12 col-lg-3">
          <!-- begin D:\OpenServer\domains\r1old/themes/classic/modules/ps_facetedsearch/ps_facetedsearch.tpl -->
          @include('components.mototirefilter')
        </div>
        <div id="content-wrapper" class="col-md-12 col-lg-9">
          <section id="main">
            <section id="products">
              <div class="tire-image-container" style="display: none">
                <div class="tire-image-cards">
                  {{-- GRID VIEW --}}
                  @php
                    $cbrand = '';
                    $index = 0;
                  @endphp
                  @foreach($tires as $tire)

                    @php
                      if (!$tire->tread) continue;
                      $brand = $tire->fullSize;
                      $tire->includeStock = true;
                      if ($cbrand!=$brand){
                        if ($index == 0) {
                          echo '</div><h4 class="tire-brand-name grid-t">' . $brand;
                          echo ' <span class="tire-type-title">Motociklu riepas</span><span style="margin: 0 auto;"></span><button type="button" class="btn-sm btn-outline-danger hidden-md-up sm-filter-btn" data-toggle="modal" data-target="#mobileFilterModal">
                                    Filtrs (' . $filterCount . ')
                                  </button></h4><div class="row grid-ex pr-1">';
                        } else {
                          echo '</div><h4 class="tire-brand-name grid-t">' . $brand;
                          echo '</h4><div class="row grid-ex pr-1">';
                        }

                        $cbrand = $brand;
                        $stripe = 1;
                      }

                    @endphp
                    @if($tire->price1)
                      <a href="{{ route('motociklu-riepa', [strtolower(\Tires::getMotoTireBrand($tire->tread->brand_id)->title), strtolower(str_replace('/', '_', $tire->tread->title)), $tire->tire_id]) }}" class="grid-view-link">
                        <div class="tire-image-card sort-order">
                          <div class="text-center image-grid-overflow">
                            {!! App\Helper\Image::showGrid('moto', $tire->make_id) !!}
                          </div>

                          <div class="tire-list-caption">

                            <div class="card-title-text" data-toggle="tooltip" title="<div>{{$tire->title}}</div>">
                              {{$tire->title}}
                            </div>

                            <div class="tire-tread">
                              <b>{{$tire->d1}} / {{$tire->d2}} / {{$tire->d3}} </b>
                              <span data-toggle="tooltip" title="<span style='color: black'>{{ $tire->lisiDesc($tire->li, $tire->si) }}</span>">{{ $tire->li . $tire->si }}</span>
                              <span class="tire-image-code">{{$tire->code}}</span>
                            </div>
                            <div style="display: flex;">
                              <input type="checkbox" name="product_ids[]" value="{{$tire->tire_id}}" style="margin-right: 5px;">
                              <div class="rim-price-old" style="align-self: center;">€{{$tire->price1}}</div>
                              <div class="rim-price-red" style="align-self: center;">€{{$tire->price2}}</div>

                              <button style="margin-left: auto;" class="grid-buy-btn cart-shopping-button"
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

                              <span class="grid-dot {{ $tire->dotAvailable }} {{ $tire->stockCount }}" data-toggle="tooltip"
                                    data-html="true"
                                    onclick="event.preventDefault()"
                                    title="{{ $tire->stockAvailability }}">
                              <span class="sort-order" style="display: none;">{{ $tire->dotAvailable }}</span>
                            </span>
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
              <div id="">
                <div id="js-product-list">
                  <div class="products row hide-price title-flip">

                    @php
                      $cbrand = '';
                      $index = 0;
                    @endphp
                    @foreach ($tires as $tire)
                      @php
                        if (!$tire->tread) continue;
                        $brand = $tire->fullSize;
                        $tire->includeStock = true;
                        if ($cbrand!=$brand){
                          if($index == 0) {
                            echo '<button type="button" class="btn-sm btn-outline-danger hidden-md-up sm-filter-btn" data-toggle="modal" data-target="#mobileFilterModal">
                                    Filtrs ('. $filterCount .')
                                  </button><div class="filters" style="margin: 0 auto;"></div>';
                            echo '<h4 class="tire-brand-name">' . $cbrand . '<span class="tire-type-title flipped-title">Motociklu riepas</span></h4>';
                          } else {
                            echo '<h4 class="tire-brand-name">' . $cbrand . '</h4>';
                          }
                      @endphp

                    {{--LIST VIEW--}}
                    <table id="tires-table" class="table table-striped moto-sorter tires-table table-hover tablesorter">
                        <thead class="tires-thead sticky-table">
                        <tr>
                          <th scope="col"></th>
                          <th scope="col" class="table-tire-name-cell">Brends / modelis</th>
                          <th scope="col" class="hidden-sm-down text-center">Tips</th>
                          <th scope="col" class="hidden-sm-down text-center">LI/SI</th>
                          <th scope="col" class="hidden-sm-down text-center">Kods</th>

                          <th id="store-price-button" scope="col" class="text-center">Veikala cena</th>
                          <th id="store-sale-button" scope="col" class="text-center">Akcijas cena</th>
                          <th scope="col" class="hidden-sm-down">Piezīmes</th>
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
                      @if ($loop->last) <h4 class="tire-brand-name">{!! $brand !!}</h4> @endif

                      <tr class="tire-table-row">
                        <th scope="row" class="tire-table-checkbox">
                          <input type="checkbox" value="{{ $tire->tire_id }}" name="product_ids[]"
                                 class="tire-table-checkbox">
                        </th>

                        <td class="table-tire-name-cell">
                          <a data-toggle="tooltip" data-html="true" class="tire-table-link"
                             title='{!! App\Helper\Image::show('moto', $tire->make_id) !!}'
                             href="{{ route('motociklu-riepa', [strtolower(\Tires::getMotoTireBrand($tire->tread->brand_id)->title), strtolower(str_replace('/', '_', $tire->tread->title)), $tire->tire_id]) }}"
                             data-content="{{ $tire->title . ' ' . $tire->fullSize }}" data-article="{{ $tire->article }}">
                            <div class="table-link-title">{{ $tire->title }}</div>
                          </a>
                        </td>

                        <td class="hidden-sm-down text-center">
                          <span data-toggle="tooltip"
                                title="<span style='color: black'>@if (isset($tire->typeDesc[1])) {{ $tire->typeDesc[1] }} @endif</span>">{{ $tire->motoType }}
                              </span>
                        </td>

                        <td class="hidden-sm-down text-center">
                          <span data-toggle="tooltip"
                                title="<span style='color: black'>{{ $tire->lisiDesc($tire->li, $tire->si) }}</span>">{{ $tire->li . $tire->si }}
                          </span>
                        </td>

                        <td class="hidden-sm-down text-center">
                            <span data-toggle="tooltip" title="<span style='color: black'>
                                                @php $codes = explode(' ', $tire->code); @endphp
                                                @foreach ($codes as $code)
                                                        @if (isset($code_array[$code]))
                                                                {!! $code_array[$code] . '<br>' !!}
                                                        @endif
                                                @endforeach
						@if (strpos($tire->code, 'DOT') !== false)
							{!! $code_array['DOT'] !!}
						@endif
                                               </span>" class="hidden-sm-down table-cell prod-code">{{ $tire->code }}
                                    </span>

                        </td>

                        <td id="store-price" class="text-center store-price">€ {{ $tire->price1 }}</td>
                        <td id="sale-price" class="text-center tire-price-red">€ {{ $tire->price2 }}</td>
                        <td class="hidden-sm-down">{{ $tire->comment }}</td>

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
                  </span>
                  <nav class="pagination">
                    <div class="col-md-12">
                    </div>
                  </nav>
                  <div class="hidden-md-up text-xs-right up">
                    <a href="#header" class="back-to-top-button">
                      <i class="material-icons"></i>
                    </a>
                  </div>
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
          @include('components.mototirefilter')
        </div>
      </div>
    </div>
  </div>


@endsection
