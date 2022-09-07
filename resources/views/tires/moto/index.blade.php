@extends('layouts.app')

@section('body-title', 'category')
@section('title', 'lang-' . app()->getLocale() . ' country-' . app()->getLocale() . ' layout-both-columns page-category tax-display-enabled category-id-16 category-motociklu-riepas category-id-parent-12 category-depth-level-3')

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
                                @foreach ($motoTiresD1 as $tire)
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
                              <input type="text" class="select-title tire-height" readonly name="d2" value="{{ $d2 }}">
                              <i class="material-icons float-xs-right"></i>
                              <div class="dropdown-menu height">

                                <a rel="nofollow" id="Visi" class="select-list">
                                  Visi
                                </a>
                                @foreach ($motoTiresD2 as $tire)
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

                                @foreach ($motoTiresD3 as $tire)
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
                    <h3 class="text-uppercase h6 hidden-sm-down">Filtrs</h3>
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

                  <section class="facet clearfix facet--4">
                    <h1 class="h6 facet-title hidden-sm-down facet-hover">Tips<span class="material-icons code-dropdown">keyboard_arrow_down</span></h1>
                    <div class="title hidden-md-up" data-target="#facet_11641" data-toggle="collapse">
                      <h1 class="h6 facet-title">Tips</h1>
                      <span class="float-xs-right">
                        <span class="navbar-toggler collapse-icons">
                          <i class="material-icons add"></i>
                          <i class="material-icons remove"></i>
                        </span>
                      </span>
                    </div>

                    <ul id="facet_code" class="collapse" style="display: none;">
                      @foreach ($types as $index => $value)
                        @php $index = strtolower($index); @endphp
                        <li data-label="{{ $index }}">
                          <label class="facet-label" for="facet_for_{{ $index }}">
                          <span class="custom-checkbox">
                            <input id="facet_for_{{ $index }}" data-search-url="" name="types[]" value="{{ $value }}" data-for="prod-code" data-value="{{ $value }}" type="checkbox">
                            <span class="ps-shown-by-js">
                              <i class="material-icons checkbox-checked"></i>
                            </span>
                          </span>
                            <a href="javascript:;" class="_gray-darker search-link js-search-link" rel="nofollow">{{ $value }}</a>
                          </label>
                        </li>
                      @endforeach
                    </ul>
                  </section>
                  <button class="filter-button" type="submit">Filtrēt <i class="material-icons search"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div id="content-wrapper" class="col-md-12 col-lg-9">
          <section id="main">
            <section id="products">
              <div class="tire-image-container" style="display: none">
                <div class="tire-image-cards">
                  {{--                <div style="width: auto;">BRAND NAME</div>--}}
                  @php
                    $cbrand = '';
                  @endphp
                  @foreach($tires as $tire)
                    @php
                      $brand = $tire->fullSize;
                      $tire->includeStock = true;
                      if ($cbrand!=$brand){
                        echo '</div><h4 class="tire-brand-name">' . $brand . '</h4><div class="row grid-ex">';
                        $cbrand = $brand;
                        $stripe = 1;
                      } else {
                          $brand = str_replace(" ", "", $brand);
                      }
                    @endphp
                    {{--                    @if ($loop->last) <h4 class="tire-brand-name">{{ $brand }}</h4> @endif--}}
                    <div class="tire-image-card">
                      <a href="{{ \Str::slug(\Tires::getMotoTireTread($tire->brand_id)) }}" class="">
                        <div class="text-center">
                          <img
                            @if ($tire->image)
                            src="{{ $tire->image }}" style='width: 100%; height: 100%;'
                            @else
                            src="{{ asset('img/p/r1-logo.svg') }}"
                            @endif alt="tire-image" class="img-thumbnail border-none text-center"
                          >
                        </div>
                        <div class="tire-list-caption">

                          <div class="card-title-text">{{$tire->title}}</div>
                          <div class="tire-tread">
                            {{$tire->d1}} / {{$tire->d2}} / {{$tire->d3}}
                          </div>
                          <div style="display: inline-flex">
                            <div class="rim-price-old">€{{$tire->price1}}</div>
                            <div class="rim-price-red">€{{$tire->price2}}</div>
                          </div>
                        </div>
                      </a>
                    </div>
                  @endforeach
                </div>
              </div>
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
{{--                          <span itemprop="name">Motociklu riepas</span>--}}
{{--                          <meta itemprop="position" content="2">--}}
{{--                        </li>--}}
{{--                      </ol>--}}
{{--                    </nav>--}}
{{--                  </div>--}}
{{--                </div>--}}
{{--              </div>--}}
              <div id="">
                <div id="js-product-list">
                  <div class="products row hide-price">

                    <div class="tire-image-container">
                      <div class="tire-image-cards" style="display: none">
                        {{--                <div style="width: auto;">BRAND NAME</div>--}}
                        @foreach($tires as $tire)
                          <div class="tire-image-card">
                            <a href="" class="">
                              <div class="text-center">
                                <img
                                  @if ($tire->image)
                                  src="{{ $tire->image }}" style='width: 100%; height: 100%;'
                                  @else
                                  src="{{ asset('img/p/r1-logo.svg') }}"
                                  @endif alt="tire-image" class="img-thumbnail border-none text-center"
                                >
                              </div>
                              <div class="tire-list-caption">

                                <div class="card-title-text">{{$tire->title}}</div>
                                <div class="tire-tread">
                                  {{$tire->d1}} / {{$tire->d2}} / {{$tire->d3}}
                                </div>
                                <div class="tire-price-red">€{{$tire->price1}}</div>
                              </div>
                            </a>
                          </div>
                        @endforeach
                      </div>
                    </div>
{{--                    <div class="table-top product_show_list">--}}
{{--                      <span class="table-cell">Brends / modelis</span>--}}
{{--                      <span class="table-cell hidden-sm-down">Tips</span>--}}
{{--                      <span class="table-cell hidden-sm-down">LI/SI</span>--}}
{{--                      <span class="table-cell hidden-sm-down">Kods</span>--}}
{{--                      <span class="table-cell sortable"--}}
{{--                            data-filter=".product-price-and-shipping .regular-price"--}}
{{--                            data-order="DESC">Veikala cena</span>--}}
{{--                      <span class="table-cell sortable"--}}
{{--                            data-filter=".product-price-and-shipping .price" data-order="DESC">Akcijas cena</span>--}}
{{--                      <span class="table-cell">Piezīmes--}}
{{--                        <!--{hook h='displayProductAttributesHeader' listing=$listing}--></span>--}}
{{--                      <span class="table-cell availability" style="margin-left: 24px;"> </span>--}}
{{--                    </div>--}}
                    @php
                      $cbrand = '';
                    @endphp
                    @foreach ($tires as $tire)
                      @php
                        $tire->includeStock = true;
                        $brand = $tire->fullSize;
                        if ($cbrand!=$brand){
                            echo '<h4 class="tire-brand-name">' . $cbrand . '</h4>';
                            $cbrand = $brand;
                            $stripe = 1;
                      @endphp

                    {{--LIST VIEW--}}
                    <table id="tires-table" class="table moto-sorter tires-table table-hover tablesorter">
                        <thead class="tires-thead">
                        <tr>
                          <th scope="col"><input type="checkbox" value="only_selected" class="tire-table-checkbox" id="show-selected-checkbox" name="product_ids[]" title="Rādīt tikai atzīmētās preces"></th>
                          <th scope="col" class="table-tire-name-cell">Brends / modelis</th>
                          <th scope="col" class="hidden-sm-down text-center">Tips</th>
                          <th scope="col" class="hidden-sm-down text-center">LI/SI</th>
                          <th scope="col" class="hidden-sm-down text-center">Kods</th>

{{--                          <th scope="col" class="hidden-sm-down">--}}
{{--                            <div class="tire-table-icon icon-tire-fuel" title="Degvielas ekonomija"></div>--}}
{{--                          </th>--}}

{{--                          <th scope="col" class="hidden-sm-down">--}}
{{--                            <div class="tire-table-icon icon-tire-rain" title="Slapjš segums"></div>--}}
{{--                          </th>--}}

{{--                          <th scope="col" class="hidden-sm-down">--}}
{{--                            <div class="tire-table-icon icon-tire-sound" title="Troksnis"></div>--}}
{{--                          </th>--}}

                          <th id="store-price-button" scope="col" class="text-center">
                            Veikala cena
                          </th>

                          <th id="store-sale-button" scope="col" class="text-center">Akcijas cena</th>
                          <th scope="col" class="">Piezīmes</th>
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
                             href="{{ route('motociklu-riepa', [strtolower(\Tires::getMotoTireBrand($tire->tread->brand_id)->title), $tire->tread->slug, $tire->tire_id]) }}"
                             data-content="{{ $tire->title }}">
                            {{ $tire->title }}
                          </a>
                        </td>

                        <td class="hidden-sm-down text-center">
                          {{ $tire->type }}
                        </td>

                        <td class="hidden-sm-down text-center">
                            <span>
{{--                              <span data-toggle="tooltip"--}}
{{--                                    title="<span style='color: black'>Kravnesības indekss: 91 – 615 kg</span>">{{ $tire->li }}</span>--}}
{{--                              <span data-toggle="tooltip"--}}
{{--                                    title="<span style='color: black'>{{ $tire->si }}</span>">{{ $tire->si }}</span>--}}

                              <span data-toggle="tooltip"
                                    title="
                                    <span style='color: black'>
                                    Kravnesības indekss: 91 – 615 kg
                                    <br>
                                    {{ $tire->si }} - Ātruma Indekss</span>">
                                    {{ $tire->li . $tire->si }}
                              </span>
                            </span>
                        </td>

                        <td class="hidden-sm-down text-center">
                            <span data-toggle="tooltip"
                                  title="<span style='color: black'>RSC – Runflat System Component (nulles spiediena riepa)</span>"
                                  class="hidden-sm-down table-cell prod-code">{{ $tire->code }}</span>
                        </td>

{{--                        <td class="hidden-sm-down text-center">--}}
{{--                            <span data-toggle="tooltip"--}}
{{--                                  title="<span style='color: black'>{{ $tire->eco }}</span>">{{ $tire->eco }}</span>--}}
{{--                        </td>--}}

{{--                        <td class="hidden-sm-down text-center">--}}
{{--                            <span data-toggle="tooltip"--}}
{{--                                  title="<span style='color: black'>{{ $tire->wet }}</span>">{{ $tire->wet }}</span>--}}
{{--                        </td>--}}

{{--                        <td class="hidden-sm-down text-center">--}}
{{--                            <span data-toggle="tooltip"--}}
{{--                                  title="<span style='color: black'>{{ $tire->noise }}</span>">{{ $tire->noise }}</span>--}}
{{--                        </td>--}}

                        <td id="store-price" class="text-center store-price">€ {{ $tire->price1 }}</td>
                        <td id="sale-price" class="text-center tire-price-red">€ {{ $tire->price2 }}</td>
                        <td>{{ $tire->comment }}</td>

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

{{--                      <article class="product_show_list cat-14 product-miniature js-product-miniature"--}}
{{--                               id="{{ str_replace(" ", "", $brand) }}"--}}
{{--                               data-id-product="{{ $tire->tire_id }}" data-id-product-attribute="{{ $tire->tire_id }}" itemscope=""--}}
{{--                               itemtype="http://schema.org/Product" data-brand="{{ $tire->brand }}"--}}
{{--                               data-atv="{{ $tire->d1 }}{{ $tire->sep }}{{ $tire->d2 }}{{ $tire->sep2 }}{{ $tire->d3 }}">--}}
{{--                        <div class="thumbnail-container">--}}
{{--                          <a href="{{ route('motociklu-riepa', [strtolower(\Tires::getMotoTireBrand($tire->tread->brand_id)->title), $tire->tread->slug, $tire->tire_id]) }}"--}}
{{--                             class="thumbnail product-thumbnail">--}}
{{--                            @if ($tire->image)--}}
{{--                              <img src='/storage/app/public/moto/tread/{{ $tire->image }}' style='width: 280px; height: 280px;'>--}}
{{--                            @else--}}
{{--                              <img src='{{ asset('img/p/en-default-home_default.jpg') }}'>--}}
{{--                            @endif--}}
{{--                          </a>--}}
{{--                          <div class="product-description">--}}
{{--                            <input type="checkbox" value="{{ $tire->tire_id }}" name="product_ids[]">--}}
{{--                            <h1 class="h3 product-title" itemprop="name">--}}
{{--                              <a data-toggle="tooltip" data-html="true"--}}
{{--                                 @if ($tire->image)--}}
{{--                                 title="<img src='/storage/app/public/moto/tread/{{ $tire->image }}' style='width: 280px; height: 280px;'>"--}}
{{--                                 @else--}}
{{--                                 title="<img src='{{ asset('img/p/en-default-home_default.jpg') }}'>"--}}
{{--                                 @endif--}}
{{--                                 href="{{ route('motociklu-riepa', [strtolower(\Tires::getMotoTireBrand($tire->tread->brand_id)->title), $tire->tread->slug, $tire->tire_id]) }}"--}}
{{--                                 data-content="{{ $tire->title }}">--}}
{{--                                <div class="product-title-hidden">{{ $tire->title }}</div>--}}
{{--                              </a>--}}
{{--                            </h1>--}}
{{--                            <div class="product-price-and-shipping" data-content="{{ $tire->title }}">--}}
{{--                              <span class="table-cell">{{ $tire->type }}</span>--}}
{{--                              <span class="hidden-sm-down table-cell">--}}
{{--                                  <span data-toggle="tooltip" title="<span style='color: black'>Kravnesības indekss: 91 – 615 kg</span>">{{ $tire->li }}</span>--}}
{{--                                  <span data-toggle="tooltip" title="<span style='color: black'>H</span>">{{ $tire->si }}</span>--}}
{{--                              </span>--}}
{{--                              <span data-toggle="tooltip" title="<span style='color: black'>RSC – Runflat System Component (nulles spiediena riepa)</span>" class="hidden-sm-down table-cell prod-code">--}}
{{--                                  {{ $tire->code }}--}}
{{--                              </span>--}}
{{--                              <span class="sr-only">Veikala cena</span>--}}
{{--                              <span class="regular-price">€ {{ $tire->price1 }}</span>--}}
{{--                              <span class="sr-only">Akcijas cena</span>--}}
{{--                              <span itemprop="price" class="price">€ {{ $tire->price2 }}</span>--}}
{{--                              <span class="table-cell notes"><nbsp></nbsp><span class="table-cell top40">Top 40</span>--}}
{{--                              </span>--}}
{{--                              <div class="clearfix atc_div">--}}
{{--                                <button class="btn grid-cart-btn btn-primary" data-toggle="modal" @if (Auth::user()) data-target="#quick-popup" @else data-target="#blockcart-modal" @endif data-info="{{ $tire->tire_id }}"><i--}}
{{--                                    class="material-icons">add_shopping_cart</i>--}}
{{--                                </button>--}}
{{--                                <span class="dot {{ $tire->dotAvailable }}" data-toggle="tooltip"--}}
{{--                                      data-html="true"--}}
{{--                                      title="{{ $tire->stockAvailability }}">--}}
{{--                                                            <span class="sort-order">{{ $tire->dotAvailable }}</span>--}}
{{--                                                        </span>--}}
{{--                              </div>--}}
{{--                            </div>--}}
{{--                          </div>--}}
{{--                        </div>--}}
{{--                      </article>--}}
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
