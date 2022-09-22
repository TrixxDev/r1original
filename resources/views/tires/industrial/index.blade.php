@extends('layouts.app')

@section('body-title', 'category')
@section('title', 'lang-' . app()->getLocale() . ' country-' . app()->getLocale() . ' layout-both-columns page-category tax-display-enabled category-id-2 category-lielas-riepas category-id-parent-12 category-depth-level-3')

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
            <div id="search_filters" class="params">
              <input type="hidden" id="facet_all_val" value="Visi">
              <div class="wrap">

                <h4 class="text-uppercase h6 hidden-sm-down">
                  <span id="search_filters_auto" class="params auto">Auto</span><span
                    id="search_filters_params" class="params active">Parametri</span>
                </h4>

                <div class="can-collapse">

                  <span class="show_list active"><i class="material-icons "></i>Saraksts</span>
                  <span class="show_grid"><i class="material-icons "></i>Bilde</span>

                  <template id="facet-template">
                    <section class="facet clearfix">
                      <h1 class="h6 facet-title hidden-sm-down">Kods</h1>
                      <input type="text" value="" id="autofind_atr">
                      <button id="autofind_sub">Meklēt <i class="material-icons search"></i>
                      </button>
                    </section>
                  </template>

                  <form method="post">
                    @csrf
                    <div class="sidebar-top">


                      <section class="facet clearfix facet--0 facet-ind-0">
                        <h1 class="h6 facet-title hidden-sm-down">Ražotājs</h1>
                        <div class="title hidden-md-up" data-target="#facet_20294"
                             data-toggle="collapse">
                          <h1 class="h6 facet-title">Ražotājs</h1>
                          <span class="float-xs-right">
                            <span class="navbar-toggler collapse-icons">
                              <i class="material-icons add"></i>
                              <i class="material-icons remove"></i>
                            </span>
                          </span>
                        </div>
                        <ul id="facet_20294" class="collapse">
                          <li>
                            <div class="col-sm-12 col-xs-12 col-md-12 facet-dropdown dropdown size-dropdown">
                              <input type="text" readonly class="select-title tire-brand" name="brand" value="{{ $currBrand }}">
                              <i class="material-icons float-xs-right"></i>
                              <div class="dropdown-menu">
                                <a rel="nofollow" id="Visi" class="select-list">
                                  Visi
                                </a>
                                @foreach ($brands as $brand)
                                  <a rel="nofollow" class="select-list" id="{{ $brand->title }}">
                                    {{ $brand->title }}
                                  </a>
                                @endforeach

                              </div>
                            </div>
                          </li>
                        </ul>


                      </section>


                      <section class="facet clearfix facet--1 facet-ind-1">
                        <h1 class="h6 facet-title hidden-sm-down">Platums</h1>
                        <div class="title hidden-md-up" data-target="#facet_78843"
                             data-toggle="collapse" aria-expanded="true">
                          <h1 class="h6 facet-title">Platums</h1>
                          <span class="float-xs-right">
                            <span class="navbar-toggler collapse-icons">
                              <i class="material-icons add"></i>
                              <i class="material-icons remove"></i>
                            </span>
                          </span>
                        </div>


                        <ul id="facet_78843" class="collapse in">
                          <li>
                            <div class="col-sm-12 col-xs-12 col-md-12 facet-dropdown dropdown size-dropdown">
                              <input type="text" readonly class="select-title tire-width" name="d1" value="{{ $d1 }}">
                              <i class="material-icons float-xs-right"></i>
                              <div class="dropdown-menu width">

                                <a rel="nofollow" id="Visi" class="select-list">
                                  Visi
                                </a>
                                @foreach ($bigTiresD1 as $tire)
                                  <a rel="nofollow" class="select-list" id="{{ $tire->d1 }}">
                                    {{ $tire->d1 }}
                                  </a>
                                @endforeach
                              </div>
                            </div>
                          </li>
                        </ul>


                      </section>


                      <section class="facet clearfix facet--2 facet-ind-2">
                        <h1 class="h6 facet-title hidden-sm-down">Augstums</h1>
                        <div class="title hidden-md-up" data-target="#facet_15402"
                             data-toggle="collapse" aria-expanded="true">
                          <h1 class="h6 facet-title">Augstums</h1>
                          <span class="float-xs-right">
                            <span class="navbar-toggler collapse-icons">
                              <i class="material-icons add"></i>
                              <i class="material-icons remove"></i>
                            </span>
                          </span>
                        </div>


                        <ul id="facet_15402" class="collapse in">
                          <li>
                            <div class="col-sm-12 col-xs-12 col-md-12 facet-dropdown dropdown size-dropdown">
                              <input type="text" class="select-title tire-height" readonly name="d2" value="@if($d2 === ''){{'Visi'}}@else{{$d2}}@endif">
                              <i class="material-icons float-xs-right"></i>
                              <div class="dropdown-menu height">

                                <a rel="nofollow" id="Visi" class="select-list">
                                  Visi
                                </a>
                                @foreach ($bigTiresD2 as $tire)
                                  <a rel="nofollow" class="select-list" id="{{ $tire->d2 }}">
                                    {{ $tire->d2 }}
                                  </a>
                                @endforeach
                              </div>
                            </div>
                          </li>
                        </ul>


                      </section>


                      <section class="facet clearfix facet--3 facet-ind-3">
                        <h1 class="h6 facet-title hidden-sm-down">Diametrs</h1>
                        <div class="title hidden-md-up" data-target="#facet_24954"
                             data-toggle="collapse" aria-expanded="true">
                          <h1 class="h6 facet-title">Diametrs</h1>
                          <span class="float-xs-right">
                            <span class="navbar-toggler collapse-icons">
                              <i class="material-icons add"></i>
                              <i class="material-icons remove"></i>
                            </span>
                          </span>
                        </div>


                        <ul id="facet_24954" class="collapse in">
                          <li>
                            <div class="col-sm-12 col-xs-12 col-md-12 facet-dropdown dropdown size-dropdown">
                              <input type="text" class="select-title tire-radius" readonly name="d3" value="{{ $d3 }}">
                              <i class="material-icons float-xs-right"></i>
                              <div class="dropdown-menu radius">

                                @foreach ($bigTiresD3 as $tire)
                                  <a rel="nofollow" class="select-list" id="{{ $tire->d3 }}">
                                    {{ $tire->d3 }}
                                  </a>
                                @endforeach
                              </div>
                            </div>
                          </li>
                        </ul>

                      </section>
                      <section class="facet clearfix">
                        <h1 style="display: none;" class="h6 facet-title hidden-sm-down">Kods</h1>
                        <input style="display: none;" type="text" value="" id="autofind_atr">
                        <button id="autofind_sub" type="submit">Meklēt <i class="material-icons search"></i>
                        </button>
                      </section>

                    </div>
                  </form>
                </div>
              </div>
              <div class="wrap">
                <div class="sidebar-bottom">

                  <section class="facet clearfix facet--availability">
                    <h1 class="h6 facet-title hidden-sm-down">Pieejamība</h1>
                    <ul id="facet_availability" class="collapse">
                      <li>
                        <label class="facet-label" for="facet_availability_0" style="width: 100%;text-align: left;cursor: pointer;margin-bottom: 5px">
                          <span class="custom-checkbox">
                            <input id="facet_availability_0" class="green" type="checkbox" data-search-url="#" name="availability[]" value="green" data-for="dot" data-value="green" data-color="green">
                            <span class="ps-shown-by-js">
                              <i class="material-icons checkbox-checked"></i>
                            </span>
                          </span>
                          Pieejams
                          <span class="dot green" style="float:right;margin-top: 3px;"></span>
                        </label>
                      </li>
                      <li>
                        <label class="facet-label" for="facet_availability_1" style="width: 100%;text-align: left;cursor: pointer;margin-bottom: 5px">
                          <span class="custom-checkbox">
                            <input id="facet_availability_1" class="yellow" type="checkbox" data-search-url="#" name="availability[]" value="yellow" data-for="dot" data-value="yellow" data-color="yellow">
                            <span class="ps-shown-by-js"><i class="material-icons checkbox-checked"></i></span>
                          </span>
                          Pasutams
                          <span class="dot yellow" style="float:right;margin-top: 3px;"></span>
                        </label>
                      </li>
                      <li>
                        <label class="facet-label" for="facet_availability_2" style="width: 100%;text-align: left;cursor: pointer">
                          <span class="custom-checkbox">
                            <input id="facet_availability_2" class="red" type="checkbox" data-search-url="#" name="availability[]" value="red" data-for="dot" data-value="red" data-color="red">
                            <span class="ps-shown-by-js"><i class="material-icons checkbox-checked"></i></span>
                          </span>
                          Zvaniet!
                          <span class="dot red" style="float:right;margin-top: 3px;"></span>
                        </label>
                      </li>
                    </ul>
                  </section>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div id="content-wrapper" class="col-md-12 col-lg-9">
          <section id="main">
            <section id="products" class="">
              <div class="tire-image-container" style="display: none">
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
                          echo '</div><h4 class="tire-brand-name">' . $brand;
                          echo ' <span class="text-uppercase" style="color:black;">Lielās riepas</span>';
                          echo '</h4><div class="row grid-ex pr-1">';
                          $cbrand = $brand;
                          $stripe = 1;
                        } else {
                            $brand = str_replace(" ", "", $brand);
                        }
                      @endphp
                      @if($tire->price1)
                        <a href="{{ route('lielas-riepa', [strtolower(\Tires::getBigTireBrand($tire->tread->brand_id)->title), $tire->tread->slug, $tire->tire_id]) }}">
                          <div class="tire-image-card sort-order">
                            <div class="text-center image-grid-overflow">
                              {!! \Image::showGrid('big', $tire->make_id) !!}
                            </div>

                            <div class="tire-list-caption">

                              <div class="card-title-text" data-toggle="tooltip" title="<div>{{$tire->title}}</div>">
                            <span class="grid-dot {{ $tire->dotAvailable }} {{ $tire->stockCount }}" data-toggle="tooltip"
                                  data-html="true"
                                  title="{{ $tire->stockAvailability }}">
                            </span>
                                {{$tire->title}}
                              </div>

                              <div class="tire-tread">
                                <b>{{$tire->d1}} / {{$tire->d2}} / {{$tire->d3}} </b>
                                <span data-toggle="tooltip" title="<span style='color: black'>NOT FINISHED YET</span>">{{ $tire->li . $tire->si }}</span>
                                <span class="tire-image-code">{{$tire->code}}</span>
                              </div>
                              <div style="display: inline-flex">
                                <div class="rim-price-old">€{{$tire->price1}}</div>
                                <div class="rim-price-red">€{{$tire->price2}}</div>
                              </div>
                            </div>
                            {{--                        <button class="grid-shopping-button grid-cart-btn" data-toggle="modal" data-target="#blockcart-modal" data-info="148204">Pirkt--}}
                            {{--                        </button>--}}

                            <button class="grid-shopping-button grid-cart-btn" data-toggle="modal"
                                    @hasrole('administrators') data-target="#quick-popup" @else data-target="#blockcart-modal"
                            @endhasrole data-info="{{ $tire->tire_id }}" onclick="event.preventDefault()"><span style="letter-spacing: 2px; text-transform: uppercase;">Pirkt</span>
                            </button>

                          </div>
                        </a>
                      @endif
                      @php
                        $index++;
                      @endphp
                    @endforeach
                  </div>
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
                            echo '<h4 class="tire-brand-name">' . $cbrand . '<span class="top-product-title flipped-title">Lielās riepas</span></h4>';
                          } else {
                            '<h4 class="tire-brand-name">' . $cbrand . '</h4>';
                          }

                      @endphp

                      <table id="tires-table" class="table industrial-sorter tires-table table-hover tablesorter">
                        <thead class="tires-thead">
                        <tr>
                          <th scope="col"><input type="checkbox" value="only_selected" class="tire-table-checkbox" id="show-selected-checkbox" name="product_ids[]" title="Rādīt tikai atzīmētās preces"></th>
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
                             @if ($tire->image)
                             title="<img src='{{ $tire->image }}' style='width: 280px; height: 280px;'>"
                             @else
                             title="<img src='{{ asset('img/p/en-default-home_default.jpg') }}'>"
                             @endif
                             href="
                                {{ route('lielas-riepa', [strtolower(\Tires::getBigTireBrand($tire->tread->brand_id)->title), $tire->tread->slug, $tire->tire_id]) }}
                               "
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
                          <span>
                            <span data-toggle="tooltip"
                                  title="<span style='color: black'>Kravnesības indekss: 91 – 615 kg</span>">{{ $tire->li }}</span>
                            <span data-toggle="tooltip"
                                  title="<span style='color: black'>{{ $tire->si }}</span>">{{ $tire->si }}</span>
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
                  <div class="hidden-md-up text-xs-right up">
                    <a href="#header" class="btn btn-secondary">
                      Back to top
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



@endsection
