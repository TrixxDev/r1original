@extends('layouts.app')

@section('body-title', 'category')
@section('title', 'lang-' . app()->getLocale() . ' country-' . app()->getLocale() . ' layout-both-columns page-category tax-display-enabled category-id-2 category-lielas-riepas category-id-parent-12 category-depth-level-3')

@section('content')

  <div class="container">
    <div class="row">
      <div class="main-content clearfix col-md-12 col-xl-12">

        <div id="left-column" class="col-md-12 col-lg-3">
          <!-- begin D:\OpenServer\domains\r1old/themes/classic/modules/ps_facetedsearch/ps_facetedsearch.tpl -->
          @include('components.bigtirefilter')
        </div>
        <div id="content-wrapper" class="col-md-12 col-lg-9">
          <section id="main">
            <section id="products" class="">
                <div id="search_filters" class="params">
                    <input type="hidden" id="facet_all_val" value="Visi">
                    <div class="wrap hidden-md-up">

                      <h4 class="text-uppercase h6 hidden-sm-down">
                        Parametri
                      </h4>

                      <div class="can-collapse">

                        <span class="show_list active" data-dismiss="modal"><i class="material-icons "></i>Saraksts</span>
                        <span class="show_grid" data-dismiss="modal"><i class="material-icons "></i>Bildes</span>

                        <template id="facet-template">
                          <section class="facet clearfix">
                            <h1 class="h6 facet-title hidden-sm-down">Kods</h1>
                            <input type="text" value="" id="autofind_atr">
                            <button id="autofind_sub">Meklēt <i class="material-icons search"></i>
                            </button>
                          </section>
                        </template>

                        <form method="get" action="{{ route('lielas-riepas-meklet') }}">
                          <div class="sidebar-top">

                            <div style="width: 100%">
                              <div class="form-group facet mb-0">
                                <h1 class="h6 facet-title">Ražotājs</h1>
                                <select name="brand" class="r1-select select-title tire-brand">
                                  <option class="select-list" id="Visi">Visi</option>
                                  @foreach ($brands as $brand_id => $brand_title)
                                    <option class="select-list" id="{{ $brand_id }}" @if ($brand_title == $currBrand) selected @endif>{{ ucwords(strtolower($brand_title)) }}</option>
                                  @endforeach
                                </select>
                              </div>
                            </div>

                            {{-- PLATUMS --}}

                            <div class="r1-select-params">
                              <div style="width: 100%">
                                <div class="form-group facet">
                                  <h1 class="h6 facet-title">Platums</h1>
                                  <select name="d1" class="r1-select select-title tire-width">
                                    <option class="select-list" id="Visi">Visi</option>
                                    @foreach ($bigTiresD1 as $tire)
                                      <option class="select-list" id="{{ $tire->d1 }}" @if ($tire->d1 == $d1) selected @endif>{{ $tire->d1 }}</option>
                                    @endforeach
                                  </select>
                                </div>
                              </div>
                              <div style="width: 100%">
                                <div class="form-group facet">
                                  <h1 class="h6 facet-title">Augstums</h1>
                                  <select name="d2" class="r1-select select-title tire-height">
                                    <option class="select-list" id="Visi">Visi</option>
                                    @foreach ($bigTiresD2 as $tire)
                                      <option class="select-list" id="{{ $tire->d2 }}" @if ($tire->d2 == $d2) selected @endif>{{ $tire->d2 }}</option>
                                    @endforeach
                                  </select>
                                </div>
                              </div>
                              <div style="width: 100%">
                                <div class="form-group facet">
                                  <h1 class="h6 facet-title facet-select">Diametrs</h1>
                                  <select name="d3" class="r1-select select-title tire-radius">
                                    <option class="select-list" id="Visi">Visi</option>
                                    @foreach ($bigTiresD3 as $tire)
                                      <option class="select-list" id="{{ $tire->d3 }}" @if ($tire->d3 == $d3) selected @endif>{{ $tire->d3 }}</option>
                                    @endforeach
                                  </select>

                                </div>
                              </div>
                            </div>

                            <section class="facet clearfix">
                              <h1 style="display: none;" class="h6 facet-title">Kods</h1>
                              <input style="display: none;" type="text" value="" id="autofind_atr">
                              <button id="autofind_sub" type="submit">Meklēt <i class="material-icons search"></i>
                              </button>
                            </section>

                          </div>
                        </form>
                      </div>
                    </div>
{{--                    <div class="wrap">--}}
{{--                      <div class="sidebar-bottom">--}}
{{--                        <h3 class="text-uppercase h6 hidden-sm-down">Filtrs</h3>--}}
{{--                        <section class="facet clearfix facet--availability">--}}
{{--                          <h1 class="h6 facet-title hidden-sm-down">Atlase</h1>--}}
{{--                          <ul class="collapse">--}}
{{--                            <li class="show-selected-checkbox-li">--}}
{{--                              <label class="facet-label" for="show-selected-checkbox"--}}
{{--                                     style="width: 100%;text-align: left;cursor: pointer;margin-bottom: 5px">--}}
{{--                          <span class="custom-checkbox">--}}
{{--                            <input type="checkbox" value="only_selected" class="tire-table-checkbox" id="show-selected-checkbox" name="product_ids[]" title="Rādīt tikai atzīmētās preces" disabled>--}}
{{--                            <span class="ps-shown-by-js">--}}
{{--                              <i class="material-icons checkbox-checked"></i>--}}
{{--                            </span>--}}
{{--                          </span>--}}
{{--                                <span>Rādīt izvēlētos</span>--}}
{{--                              </label>--}}
{{--                            </li>--}}
{{--                          </ul>--}}
{{--                          <h1 class="h6 facet-title hidden-sm-down">Pieejamība</h1>--}}
{{--                          <ul id="facet_availability" class="collapse">--}}
{{--                            <li>--}}
{{--                              <label class="facet-label" for="facet_availability_0" style="width: 100%;text-align: left;cursor: pointer;margin-bottom: 5px">--}}
{{--                          <span class="custom-checkbox">--}}
{{--                            <input id="facet_availability_0" class="green" type="checkbox" data-search-url="#" name="availability[]" value="green" data-for="dot" data-value="green" data-color="green">--}}
{{--                            <span class="ps-shown-by-js">--}}
{{--                              <i class="material-icons checkbox-checked"></i>--}}
{{--                            </span>--}}
{{--                          </span>--}}
{{--                                Pieejams--}}
{{--                                <span class="dot green" style="float:right;margin-top: 3px;"></span>--}}
{{--                              </label>--}}
{{--                            </li>--}}
{{--                            <li>--}}
{{--                              <label class="facet-label" for="facet_availability_1" style="width: 100%;text-align: left;cursor: pointer;margin-bottom: 5px">--}}
{{--                          <span class="custom-checkbox">--}}
{{--                            <input id="facet_availability_1" class="yellow" type="checkbox" data-search-url="#" name="availability[]" value="yellow" data-for="dot" data-value="yellow" data-color="yellow">--}}
{{--                            <span class="ps-shown-by-js"><i class="material-icons checkbox-checked"></i></span>--}}
{{--                          </span>--}}
{{--                                Pasūtāms--}}
{{--                                <span class="dot yellow" style="float:right;margin-top: 3px;"></span>--}}
{{--                              </label>--}}
{{--                            </li>--}}
{{--                            <li>--}}
{{--                              <label class="facet-label" for="facet_availability_2" style="width: 100%;text-align: left;cursor: pointer">--}}
{{--                          <span class="custom-checkbox">--}}
{{--                            <input id="facet_availability_2" class="red" type="checkbox" data-search-url="#" name="availability[]" value="red" data-for="dot" data-value="red" data-color="red">--}}
{{--                            <span class="ps-shown-by-js"><i class="material-icons checkbox-checked"></i></span>--}}
{{--                          </span>--}}
{{--                                Zvaniet!--}}
{{--                                <span class="dot red" style="float:right;margin-top: 3px;"></span>--}}
{{--                              </label>--}}
{{--                            </li>--}}
{{--                          </ul>--}}
{{--                        </section>--}}
{{--                        </form>--}}
{{--                      </div>--}}
{{--                    </div>--}}
                  </div>
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
                          echo ' <span class="tire-type-title">Lielās riepas</span><span style="margin: 0 auto;"></span><button type="button" class="btn-sm btn-outline-danger hidden-md-up sm-filter-btn" data-toggle="modal" data-target="#mobileFilterModal">
                                    Filtrs (' . $filterCount . ')
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
                      <a
                        href="{{ route('lielas-riepa', [strtolower(\Tires::getBigTireBrand($tire->tread->brand_id)->title), $tire->tread->slug, $tire->tire_id]) }}"
                        class="grid-view-link"
                        data-article="{{ $tire->article }}">
                        <div class="tire-image-card sort-order">
                          <div class="text-center image-grid-overflow">
                            {!! App\Helper\Image::showGrid('big', $tire->make_id) !!}
                          </div>

                          <div class="tire-list-caption">

                            <div class="card-title-text" data-toggle="tooltip" title="<div>{{$tire->title}}</div>">
                              {{$tire->title}}
                            </div>

                            <div class="tire-tread">
                              <b>{{$tire->d1}} / {{$tire->d3}} </b>
                              <span data-toggle="tooltip" title="<span style='color: black'>{{ $tire->lisiDesc($tire->li, $tire->si) }}</span>">{{ $tire->li . ' ' . $tire->si }}</span>
                              <span class="tire-image-code">{{$tire->code}}</span>
                            </div>
                            <div style="display: flex;">
                              <input type="checkbox" name="product_ids[]" value="{{$tire->tire_id}}" style="margin-right: 5px;">
                              <div class="rim-price-old" style="align-self: center;">€{{$tire->price1}}</div>
                              <div class="rim-price-red" style="align-self: center;">€{{$tire->price2}}</div>
                              <button style="margin-left: auto;"
                                      class="grid-buy-btn cart-shopping-button"
                                      data-toggle="modal"
                                      data-info="{{ $tire->tire_id }}"
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
                    {{-- LIST VIEW --}}
                    @php
                      $cbrand = '';
                      $index = 0;
                    @endphp
                    @foreach ($tires as $tire)
                      @php
                        $brand = $tire->fullSize;
                        $tire->includeStock = true;
                        if ($cbrand!=$brand){
                          if ($index == 0) {
                        //    echo '<h4 class="tire-brand-name">' . $cbrand . '<span class="tire-type-title flipped-title">Lielās riepas</span></h4>';

                            echo '<button type="button" class="btn-sm btn-outline-danger hidden-md-up sm-filter-btn" data-toggle="modal" data-target="#mobileFilterModal">
                                    Filtrs ('. $filterCount .')
                                  </button><div class="filters" style="margin: 0 auto;"></div>';
                            echo '<h4 class="tire-brand-name">' . $cbrand . '<span class="tire-type-title flipped-title">Lielās riepas</span></h4>';
                          } else {
                            '<h4 class="tire-brand-name">' . $cbrand . '</h4>';
                          }

                      @endphp

                      <table id="tires-table" class="table table-striped industrial-sorter tires-table table-hover tablesorter">
                        <thead class="tires-thead sticky-table">
                        <tr>
                          <th scope="col"></th>
                          <th scope="col" class="table-tire-name-cell">Brends / modelis</th>
                          <th scope="col">Ass</th>
                          <th scope="col" class="text-center">Segums</th>
                          <th scope="col" class="text-center">LI/SI</th>
                          <th scope="col" class="hidden-sm-down text-center">
                            Kods
                          </th>

                          <th id="store-price-button" scope="col" class="text-center">
                            Veikala cena
                          </th>

                          <th id="store-sale-button" scope="col" class="text-center">Akcijas cena</th>
                          <th scope="col" class="hidden-sm-down">Piezīmes</th>
                          <th scope="col"></th>
                          <th scope="col">
                            <div class="tire-table-icon icon-question"></div>
                          </th>

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
                      <tr class="tire-table-row">
                        <th scope="row" class="tire-table-checkbox">
                          <input type="checkbox" value="{{ $tire->tire_id }}" name="product_ids[]"
                                 class="tire-table-checkbox">
                        </th>

                        <td class="table-tire-name-cell">
                          <a data-toggle="tooltip" data-html="true" class="tire-table-link"
                             title='{!! App\Helper\Image::show('big', $tire->make_id) !!}'
                             href="{{ route('lielas-riepa', [strtolower(\Tires::getBigTireBrand($tire->tread->brand_id)->title), $tire->tread->slug, $tire->tire_id]) }}"
                             data-content="{{ $tire->title }}">
                            {{ $tire->title }}
                          </a>
                        </td>
{{--                           ass --}}
                        <td>
                          @if ($tire->axis_bus)
                            {{ $tire->axis_bus }}
                            @if ($tire->axis_truck)
                              | {{ $tire->axis_truck }}
                            @endif
                          @endif
                          @if ($tire->axis_truck)
                            {{ $tire->axis_truck }}
                            @if ($tire->axis_bus)
                              | {{ $tire->axis_bus }}
                            @endif
                          @endif
                        </td>
{{--                        Segums--}}
                        <td>
                          @if ($tire->conditions_bus)
                            {{ $tire->conditions_bus }}
                            @if ($tire->conditions_truck)
                              | {{ $tire->conditions_truck }}
                            @endif
                          @endif
                          @if ($tire->conditions_truck)
                            {{ $tire->conditions_truck }}
                            @if ($tire->conditions_bus)
                              | {{ $tire->conditions_bus }}
                            @endif
                          @endif
                        </td>
{{--                        LI/SI--}}
                        <td class="text-center">
                          <span data-toggle="tooltip"
                            title="<span style='color: black'>{{ $tire->lisiDesc($tire->li, $tire->si) }}</span>">{{ $tire->li . ' ' . $tire->si }}
                          </span>
                        </td>

                        <td class="hidden-sm-down text-center">
                          <span data-toggle="tooltip"
                                title="<span style='color: black'>RSC – Runflat System Component (nulles spiediena riepa)</span>"
                                class="hidden-sm-down table-cell prod-code">{{ $tire->code }}</span>
                        </td>

                        <td id="store-price" class="text-center store-price">€ {{ $tire->price1 }}</td>
                        <td id="sale-price" class="text-center tire-price-red">€ {{ $tire->price2 }}</td>
                        <td class="hidden-sm-down text-center"></td>

                        <td class="shopping-cart-col">
                          <div class="clearfix atc_div text-right">
                            <button class="cart-shopping-button grid-cart-btn" data-toggle="modal"
                                    @if (Auth::user()) data-target="#quick-popup" @else data-target="#blockcart-modal"
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
          @include('components.bigtirefilter')
        </div>
      </div>
    </div>
  </div>

@endsection
