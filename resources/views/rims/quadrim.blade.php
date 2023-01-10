@extends('layouts.app')

@section('body-title', 'category')
@section('title', 'lang-' . app()->getLocale() . ' country-' . app()->getLocale() . ' layout-both-columns page-category tax-display-enabled category-id-21 category-jauni-lietie-diski category-id-parent-20 category-depth-level-3')


@section('content')

    <div class="container">
        <div class="row">
            <div class="main-content clearfix col-md-12">
                <div id="left-column" class="col-md-12 col-lg-3">
                    <!-- begin D:\OpenServer\domains\r1old/themes/classic/modules/ps_facetedsearch/ps_facetedsearch.tpl -->
                  @include('components.quadrrimsfilter')
                </div>
                <div id="content-wrapper" class="col-md-12 col-lg-9">
{{--                    <section id="main">--}}
{{--                        <section id="products">--}}
{{--                            <div id="">--}}
{{--                                <div class="row products-selection">--}}
{{--                                    <div class="col-md-8 hidden-md-down">--}}
{{--                                        <nav data-depth="3" class="breadcrumb hidden-sm-down">--}}
{{--                                            <ol itemscope="" itemtype="http://schema.org/BreadcrumbList">--}}
{{--                                                <li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">--}}
{{--                                                    <a itemprop="item" href="http://r1riepas.lv/index.php">--}}
{{--                                                        <span itemprop="name">Sākumlapa</span>--}}
{{--                                                    </a>--}}
{{--                                                    <meta itemprop="position" content="1">--}}
{{--                                                </li>--}}
{{--                                                <li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">--}}
{{--                                                    <a itemprop="item" href="http://r1riepas.lv/index.php?id_category=20&amp;controller=category&amp;id_lang=2">--}}
{{--                                                        <span itemprop="name">Diski</span>--}}
{{--                                                    </a>--}}
{{--                                                    <meta itemprop="position" content="2">--}}
{{--                                                </li>--}}
{{--                                                <li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">--}}
{{--                                                    <a itemprop="item" href="http://r1riepas.lv/index.php?id_category=21&amp;controller=category&amp;id_lang=2">--}}
{{--                                                        <span itemprop="name">Kvadraciklu diski</span>--}}
{{--                                                    </a>--}}
{{--                                                    <meta itemprop="position" content="3">--}}
{{--                                                </li>--}}
{{--                                            </ol>--}}
{{--                                        </nav>--}}
{{--                                    </div>--}}
{{--                                    <div class="col-md-6">--}}
{{--                                        <div class="row sort-by-row">--}}
{{--                                            <div class="col-sm-3 col-xs-4 hidden-md-up filter-button">--}}
{{--                                                <button id="search_filter_toggler" class="btn btn-secondary">--}}
{{--                                                    Filtrs--}}
{{--                                                </button>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>       </div>--}}
{{--                            </div>--}}
{{--                            <div id="" class="hidden-sm-down">--}}
{{--                                <section id="js-active-search-filters" class="hide">--}}
{{--                                    <h1 class="h6 hidden-xs-up">Active filters</h1>--}}
{{--                                </section>--}}
{{--                            </div>--}}
{{--                            <div id="">--}}
{{--                                <div id="js-product-list">--}}
{{--                                    <div class="products row hide-price">--}}

{{--                                        <div class="table-top product_show_list">--}}
{{--                                            <span class="table-cell sortable" data-filter=".product-description .product-title a" data-order="DESC">Brends / modelis</span>--}}
{{--                                            <span class="table-cell hidden-sm-down">Krāsa</span>--}}
{{--                                            <span class="table-cell sortable" data-filter=".product-price-and-shipping .regular-price" data-order="DESC">Veikala cena</span>--}}
{{--                                            <span class="table-cell sortable" data-filter=".product-price-and-shipping .price" data-order="DESC">Akcijas cena</span>--}}
{{--                                            <span class="table-cell">Piezīmes<!--{hook h='displayProductAttributesHeader' listing=$listing}--></span>--}}
{{--                                            <span class="table-cell availability sortable" data-filter=".product-price-and-shipping .dot" data-order="DESC"> </span>--}}
{{--                                        </div>--}}
{{--                                        <h4 class="custom_brand_name product_list_view" data-brand="30" style="display: none;">BSA</h4><h4 class="custom_brand_name product_list_view" data-brand="28" style="display: none;">DRAG</h4><h4 class="custom_brand_name product_list_view" data-brand="29" style="display: none;">Dezent</h4><h4 class="custom_brand_name product_list_view" data-brand="26" style="display: none;">MOMO</h4><h4 class="custom_brand_name product_list_view" data-brand="25" style="display: none;">NANO</h4><h4 class="custom_brand_name product_list_view" data-brand="10" style="display: none;">Oriģinālie</h4><h4 class="custom_brand_name product_list_view" data-brand="24" style="display: none;">REDS</h4><h4 class="custom_brand_name product_list_view" data-brand="27" style="display: none;">Replika</h4><h4 class="custom_brand_name product_list_view" data-brand="11" style="display: none;">VIPER</h4><article class="cat-21 product-miniature js-product-miniature product_show_list" data-id-product="569" data-id-product-attribute="7834" itemscope="" itemtype="http://schema.org/Product" data-brand="28" data-dia="18" data-lug="5" data-stud="112" data-offset="35">--}}
{{--                                            <div class="thumbnail-container">--}}
{{--                                                <a href="http://r1riepas.lv/index.php?id_product=569&amp;id_product_attribute=7834&amp;rewrite=drag-52573&amp;controller=product&amp;id_lang=2#/134-lugcount-5/136-studspread-112/149-offset-35/357-wheelsdiameter-18/841-color-silver/971-widthinches-80" class="thumbnail product-thumbnail">--}}
{{--                                                    <img src="{{ asset('img\p\en-default-home_default.jpg') }}">--}}
{{--                                                    <!-- NOT WORKING like needed | RDP -->--}}
{{--                                                    <!-- NOT WORKING like needed | RDP -->--}}
{{--                                                </a>--}}
{{--                                                <div class="product-description">--}}
{{--                                                    <input type="checkbox" value="7834" name="product_ids[]">--}}
{{--                                                    <h1 class="h3 product-title" itemprop="name">--}}
{{--                                                        <a data-toggle="tooltip" data-html="true" title="<img src='{{ asset('img\p\en-default-home_default.jpg') }}'>" href="http://r1riepas.lv/index.php?id_product=569&amp;id_product_attribute=7834&amp;rewrite=drag-52573&amp;controller=product&amp;id_lang=2#/134-lugcount-5/136-studspread-112/149-offset-35/357-wheelsdiameter-18/841-color-silver/971-widthinches-80" data-content="DRAG 52573">--}}
{{--                                                            <div class="product-title-hidden"> DRAG 52573--}}
{{--                                                                <br> <span style="color: #65c2a5">18x8.0</span>--}}
{{--                                                            </div>--}}
{{--                                                        </a>--}}
{{--                                                    </h1>--}}
{{--                                                    <div class="product-price-and-shipping" data-content="DRAG 52573 - 8.0*18 (5x112 et35)">--}}
{{--                                                        <span class="hidden-sm-down table-cell">SILVER</span>--}}
{{--                                                        <!-- RDP | removed from IF -->--}}
{{--                                                        <span class="sr-only">Veikala cena</span>--}}
{{--                                                        <span class="regular-price">€ 168</span>--}}
{{--                                                        <!-- RDP removed from IF-->--}}
{{--                                                        <span class="sr-only">Akcijas cena</span>--}}
{{--                                                        <span itemprop="price" class="price">€ 137</span>--}}
{{--                                                        <span class="table-cell notes">--}}
{{--                                                            &nbsp;--}}
{{--                                                            <span class="table-cell top40">&nbsp;</span>--}}
{{--                                                        </span>--}}
{{--                                                        <div class="clearfix atc_div">--}}
{{--                                                            <button class="btn grid-cart-btn btn-primary" data-callback="showQuickBuyForm" data-callback-param="7834" data-popup-open="popup-2" data-backdrop="2" data-show="1" data-toggle="modal"> <i class="material-icons">add_shopping_cart</i> </button>--}}
{{--                                                            <span class="dot  red" data-toggle="tooltip" data-html="true" title="<p>R1 Kopā:0</p><br/><p>Noliktava: 0</p><br/><p>Veikals: 0</p><br/><p>Lattako: 0</p><br/><p>Goodyear: 0</p><br/><p>Nokian: 0</p><br/><p>Kumho: 0</p><br/><p>Nevetas: 0</p><br/>"><span class="sort-order">6</span></span>--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </article>--}}
{{--                                    </div>--}}
{{--                                    <a id="storage" href="http://aludiski.com/aludiski1001/wheels.php?cat=all&amp;lang=LV&amp;select_wheels=Search" target="_blank">NOLIKTAVA </a>--}}
{{--                                    <nav class="pagination">--}}
{{--                                        <div class="col-md-12">--}}
{{--                                        </div>--}}
{{--                                    </nav>--}}
{{--                                    <div class="hidden-md-up text-xs-right up">--}}
{{--                                        <a href="#header" class="btn btn-secondary">--}}
{{--                                            Back to top--}}
{{--                                            <i class="material-icons"></i>--}}
{{--                                        </a>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div id="js-product-list-bottom">--}}
{{--                                <div id="js-product-list-bottom"></div>--}}
{{--                            </div>--}}
{{--                        </section>--}}
{{--                    </section>--}}
                  <section id="main">
                    <section id="products">
                      <div id="" class="hidden-sm-down">
                        <section id="js-active-search-filters" class="hide">
                          <h1 class="h6 hidden-xs-up">Active filters</h1>
                        </section>
                      </div>
                      {{-- GRID VIEW --}}
                      <div id="">
                        <div class="tire-image-container" style="display: none">
                          <div class="tire-image-cards">
                            @php
                              $cbrand = '';
                              $index = 0;
                            @endphp
                            @foreach($rims as $rim)
                              @php
                                $brand = $rim->brand_title;
                                if ($cbrand!=$brand){
                                  if ($index == 0) {
                                    echo '</div><h4 class="tire-brand-name grid-t">' . $brand;
                                    echo ' <span class="tire-type-title">Kvadru diski</span><span style="margin: 0 auto;"></span><button type="button" class="btn-sm btn-outline-danger hidden-md-up sm-filter-btn" data-toggle="modal" data-target="#mobileFilterModal">
                                              Filtrs
                                      </button></h4></h4><div class="row grid-ex pr-1">';
                                  } else {
                                    echo '</div><h4 class="tire-brand-name grid-t">' . $brand;
                                    echo '</h4><div class="row grid-ex pr-1">';
                                  }
                                  $cbrand = $brand;
                                  $stripe = 1;
                                } else {
                                    $brand = str_replace(" ", "", $brand);
                                }
                              @endphp
                              @if($rim->price1)
                              <a href="{{ route('kvadru-disks', [\Str::slug($rim->brand_title), \Str::slug($rim->title), $rim->rim_id]) }}"
                                 class="grid-view-link"
                                 data-article="{{ $rim->article }}">
                                <div class="tire-image-card sort-order">
                                  <div class="text-center image-grid-overflow">
                                    {!! App\Helper\Image::showGrid('quadr-rim', $rim->make_id) !!}
                                  </div>

                                  <div class="tire-list-caption">

                                    <div class="card-title-text" data-toggle="tooltip" title="<div>{{$rim->title}}</div>">
                                      {{$rim->title}}
                                    </div>

                                    <div class="rim-tread">
                                      <b>{{ $rim->d1 }}*{{ $rim->d3 }} ({{ $rim->skr }}*{{$rim->pcd}} et{{$rim->et}})</b>
                                    </div>
                                    <div style="display: flex;">
                                      <input type="checkbox" name="product_ids[]" value="{{$rim->rim_id}}" style="margin-right: 5px;">
                                      <div class="rim-price-old" style="align-self: center;">€{{$rim->price2}}</div>
                                      <div class="rim-price-red" style="align-self: center;">€{{$rim->price3}}</div>

                                      <span style="margin-left: auto;" data-toggle="tooltip" data-html="true"
                                            title="<span style='color: black'>Pievienot grozam</span>">

                                        <button class="grid-buy-btn cart-shopping-button"
                                                data-toggle="modal"
                                                data-info="{{ $rim->tire_id }}"
                                                onclick="event.preventDefault()"
                                                @hasrole('administrators')
                                                  data-target="#"
                                                @else
                                            data-target="#blockcart-modal"
                                            @endhasrole>
                                            <i class="material-icons">add_shopping_cart</i>
                                        </button>
                                      </span>

                                      <span class="grid-dot {{ $rim->dotAvailable }} {{ $rim->stockCount }}"
                                            data-toggle="tooltip"
                                            data-html="true"
                                            onclick="event.preventDefault()"
                                            title="{{ $rim->stockAvailability }}">
                                        <span class="sort-order" style="display: none;">{{ $rim->dotAvailable }}</span>
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

                        {{-- LIST VIEW --}}
                        <div id="js-product-list">
                          <div class="products row hide-price title-flip">
                            @php
                              $cbrand = '';
                              $index = 0;
                            @endphp
                            @foreach ($rims as $rim)
                              @php
                                $brand = $rim->brand_title;
                                $rim->includeStock = true;
                                if ($cbrand!=$brand){
                                if($index == 0) {
                                  echo '<button type="button" class="btn-sm btn-outline-danger hidden-md-up sm-filter-btn" data-toggle="modal" data-target="#mobileFilterModal">
                                          Filtrs
                                        </button><div class="filters" style="margin: 0 auto;"></div>';
                                  echo '<h4 class="tire-brand-name">' . $cbrand . '<span class="tire-type-title flipped-title">Kvadraciklu diski</span></h4>';
                                } else {
                                  echo '<h4 class="tire-brand-name">' . $cbrand . '</h4>';
                                }
                              @endphp
                            <table id="tires-table" class="table table-striped quadr-rims-sorter tires-table table-hover tablesorter">
                              <thead class="tires-thead sticky-top">
                              <tr>
                                <th scope="col"></th>
                                <th scope="col">Nosaukums</th>
                                <th scope="col" class="text-center">Izmērs</th>
                                <th scope="col" class="hidden-sm-down text-center">Skrūvju attālums</th>
                                <th scope="col" class="hidden-sm-down text-center">ET</th>

                                <th id="store-price-button" scope="col" class="text-center">Veikala cena</th>
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
                                } else {
                                    $brand = str_replace(" ", "", $brand);
                                }
                                @endphp
                                @if ($loop->last) <h4 class="tire-brand-name">{{ $brand }}</h4> @endif
                                <tr class="tire-table-row">
                                  <th scope="row" class="tire-table-checkbox">
                                    <input type="checkbox" value="{{$rim->rim_id}}" name="product_ids[]"
                                           class="tire-table-checkbox">
                                  </th>

                                  <td>
                                    <a data-toggle="tooltip" data-html="true" class="rim-table-link tire-table-link"
                                       title="{!! App\Helper\Image::show('quadr-rim', $rim->make_id) !!}"
                                       href="{{ route('kvadru-disks', [\Str::slug($rim->brand_title), \Str::slug($rim->title), $rim->rim_id]) }}"
                                    >
                                      {{ $rim->brand_title . ' ' . $rim->title }}
                                    </a>
                                  </td>
                                  <td class="text-center">
                                    {{$rim->d1}}*{{$rim->d3}}
                                  </td>

                                  <td class="hidden-sm-down text-center">
                                    {{$rim->skr}} * {{$rim->pcd}}
                                  </td>

                                  <td class="hidden-sm-down text-center">
                                    et{{ $rim->et }}
                                  </td>

                                  <td id="store-price" class="text-center store-price">€ {{$rim->price2}}</td>
                                  <td id="sale-price" class="text-center tire-price-red sale-price">€ {{$rim->price3}}</td>
                                  <td class="hidden-sm-down text-center"></td>

                                  <td class="shopping-cart-col">
                                    <div class="clearfix atc_div text-right">
                                      <button class="cart-shopping-button grid-cart-btn" data-toggle="modal">
                                        <i class="material-icons">add_shopping_cart</i>
                                      </button>
                                    </div>
                                  </td>

                                  <td class="dot-availability text-center">
                              <span class="dot red" data-toggle="tooltip"
                                    data-html="true"
                                    title="red">
                                <span class="sort-order">red</span>
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
                        </div>
                        {{ $rims->links() }}
                        <div id="js-product-list-bottom">
                          <div id="js-product-list-bottom"></div>
                        </div>
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
            @include('components.quadrrimsfilter')
          </div>
        </div>
      </div>
    </div>
@endsection
