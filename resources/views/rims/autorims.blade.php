@extends('layouts.app')

@section('body-title', 'category')
@section('title', 'lang-' . app()->getLocale() . ' country-' . app()->getLocale() . ' layout-both-columns page-category tax-display-enabled category-id-21 category-jauni-lietie-diski category-id-parent-20 category-depth-level-3')


@section('content')

    <div class="container">
        <div class="row">
            <div class="main-content clearfix col-md-12">
                <div id="left-column" class="col-md-12 col-lg-3">
                    <!-- begin D:\OpenServer\domains\r1old/themes/classic/modules/ps_facetedsearch/ps_facetedsearch.tpl -->
                    @include('components.autorimsfilter')
                </div>
              <div id="content-wrapper" class="col-md-12 col-lg-9">
                <section id="main">
                  <section id="products" class="">
{{--                    <div class="tire-image-container" style="display: none">--}}
{{--                      <div class="tire-image-cards">--}}
{{--                         GRID VIEW--}}
{{--                        @php--}}
{{--                          $cbrand = '';--}}
{{--                          $index = 0;--}}
{{--                        @endphp--}}
{{--                        @foreach($rims as $rim)--}}
{{--                          @php--}}
{{--                            $brand = $rim->fullSize;--}}
{{--                            $rim->includeStock = true;--}}
{{--                            if ($cbrand!=$brand){--}}
{{--                              if ($index == 0) {--}}
{{--                                echo '</div><h4 class="tire-brand-name grid-t">' . $brand . ' <span class="tire-type-title">Lietie Diski</span></h4><div class="row grid-ex pr-1">';--}}
{{--                              } else {--}}
{{--                                echo '</div><h4 class="tire-brand-name grid-t">' . $brand . '</h4><div class="row grid-ex pr-1">';--}}
{{--                              }--}}
{{--                              $cbrand = $brand;--}}
{{--                              $stripe = 1;--}}
{{--                            } else {--}}
{{--                                $brand = str_replace(" ", "", $brand);--}}
{{--                            }--}}
{{--                          @endphp--}}
{{--                          @if($rim->price1)--}}
{{--                            <a href="--}}
{{--                              {{ route('kvadraciklu-riepa', [strtolower(\Tires::getQuadrTireBrand($tire->tread->brand_id)->title), $tire->tread->slug, $tire->tire_id]) }}--}}
{{--                            " class="grid-view-link">--}}
{{--                              <div class="tire-image-card sort-order">--}}
{{--                                <div class="text-center image-grid-overflow">--}}
{{--                                  {!! \Image::showGrid('quadr', $rim->make_id) !!}--}}
{{--                                </div>--}}

{{--                                <div class="tire-list-caption">--}}

{{--                                  <div class="card-title-text" data-toggle="tooltip" title="<div>{{$rim->title}}</div>">--}}
{{--                                    {{$rim->title}}--}}
{{--                                  </div>--}}

{{--                                  <div class="tire-tread">--}}
{{--                                    <b>{{$rim->d1}} / {{$rim->d2}} / {{$rim->d3}} </b>--}}
{{--                                    <span class="tire-image-code">{{$rim->code}}</span>--}}
{{--                                  </div>--}}
{{--                                  <div style="display: flex;">--}}
{{--                                    <input type="checkbox" name="product_ids[]" value="{{$rim->tire_id}}" style="margin-right: 5px;">--}}
{{--                                    <div class="rim-price-old" style="align-self: center;">€{{$rim->price1}}</div>--}}
{{--                                    <div class="rim-price-red" style="align-self: center;">€{{$rim->price2}}</div>--}}
{{--                                    <i class="material-icons" style="margin-left: auto;">add_shopping_cart</i>--}}
{{--                                    <span class="grid-dot {{ $rim->dotAvailable }} {{ $rim->stockCount }}" data-toggle="tooltip"--}}
{{--                                          data-html="true"--}}
{{--                                          title="{{ $rim->stockAvailability }}">--}}
{{--                              <span class="sort-order" style="display: none;">{{ $rim->dotAvailable }}</span>--}}
{{--                            </span>--}}
{{--                                  </div>--}}
{{--                                </div>--}}
{{--                              </div>--}}
{{--                            </a>--}}
{{--                          @endif--}}
{{--                          @php--}}
{{--                            $index++;--}}
{{--                          @endphp--}}
{{--                        @endforeach--}}
{{--                      </div>--}}
{{--                    </div>--}}
                    <div class="tire-image-container" style="display: none">
                      <div class="tire-image-cards">
                        {{--                <div style="width: auto;">BRAND NAME</div>--}}
                        @php
                          $cbrand = '';
                          $index = 0;
                        @endphp
                        @foreach($rims as $rim)
                          @php
                            $brand = $rim->brand_title;
                            $rim->includeStock = true;
                            if ($cbrand!=$brand){
                              if ($index == 0) {
                                echo '</div><h4 class="tire-brand-name grid-t">' . $brand . ' <span class="tire-type-title">Lietie diski</span></h4><div class="row grid-ex pr-1">';
                              } else {
                                echo '</div><h4 class="tire-brand-name grid-t">' . $brand . '</h4><div class="row grid-ex pr-1">';
                              }

                              $cbrand = $brand;
                              $stripe = 1;
                            }

                          @endphp
                          @if($rim->price1)
                            <a href="{{ route('lietais-disks', [\Str::slug($rim->brand_title), \Str::slug($rim->title), $rim->rim_id]) }}"
                               class="grid-view-link"
                               data-article="{{ $rim->article }}">
                            <div class="tire-image-card sort-order">
                                <div class="text-center image-grid-overflow">
                                  {!! App\Helper\Image::showGrid('auto-rim', $rim->make_id) !!}
                                </div>

                                <div class="tire-list-caption">

                                  <div class="card-title-text" data-toggle="tooltip" title="<div>{{$rim->title}}</div>">
                                    {{$rim->title}}
                                  </div>

                                  <div class="rim-tread">
                                    {{ $rim->d1 }}*{{ $rim->d3 }} ({{ $rim->skr }}*{{$rim->pcd}} et{{$rim->et}})
                                  </div>
                                  <div style="display: flex;">
                                    <input type="checkbox" name="product_ids[]" value="{{$rim->rim_id}}" style="margin-right: 5px;">
                                    <div class="rim-price-old" style="align-self: center;">€{{$rim->price1}}</div>
                                    <div class="rim-price-red" style="align-self: center;">€{{$rim->price2}}</div>

                                    <span style="margin-left: auto;" data-toggle="tooltip"
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
                    <div id="">
                      <div id="js-product-list">
                        <div class="products row hide-price title-flip">
                          @php
                            $cbrand = '';
                            $index = 0;
                          @endphp
{{--                          {{dd($rims)}}--}}
                          @foreach ($rims as $rim)
                            @php
                              $brand = $rim->brand_title;
                              $rim->includeStock = true;
                              if ($cbrand!=$brand){
                              if ($index == 0) {
                                echo '<h4 class="tire-brand-name">' . $cbrand . '<span class="tire-type-title flipped-title">Lietie diski</span></h4>';
                              } else {
                                '<h4 class="tire-brand-name">' . $cbrand . '</h4>';
                              }

                                echo '<h4 class="tire-brand-name">' . $cbrand . '</h4>';
                                $cbrand = $brand;
                                $stripe = 1;
                              @endphp
                            <table id="tires-table" class="table quadr-sorter tires-table table-hover tablesorter">
                              <thead class="tires-thead sticky-top">
                              <tr>
                                <th scope="col"></th>
                                <th scope="col">Nosaukums</th>
                                <th scope="col" class="text-center">Izmērs</th>
                                <th scope="col" class="hidden-sm-down text-center">Skrūvju attālums</th>
                                <th scope="col" class="hidden-sm-down text-center">ET</th>
                                <th scope="col" class="hidden-sm-down text-center">Centrs</th>
                                <th scope="col" class="hidden-sm-down text-center">Krāsa</th>

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

                                <td class="table-tire-name-cell">
                                  <a data-toggle="tooltip" data-html="true" class="tire-table-link"
                                     title='{!! App\Helper\Image::show('auto-rim', $rim->make_id) !!}'
                                     href="{{ route('lietais-disks', [\Str::slug($rim->brand_title), \Str::slug($rim->title), $rim->rim_id]) }}"
                                     data-content="{{ $rim->title . ' ' . $rim->fullSize }}"
                                     data-article="{{ $rim->article }}">
                                    <div class="table-link-title">{{ $rim->brand_title . ' ' . $rim->title }}</div>
                                  </a>
                                </td>
                                <td class="text-center">
                                  {{$rim->d1}}*{{$rim->d3}}
                                </td>

                                <td class="text-center hidden-sm-down">
                                  {{$rim->skr}} * {{$rim->pcd}}
                                </td>

                                <td class="text-center hidden-sm-down">
                                  et{{ $rim->et }}
                                </td>

                                <td class="text-center hidden-sm-down">
                                  {{$rim->dc}}
                                </td>

                                <td class="hidden-sm-down text-center">
                                  {{$rim->color}}
                                </td>

                                <td id="store-price" class="text-center store-price">€ {{$rim->price2}}</td>
                                <td id="sale-price" class="text-center tire-price-red sale-price">€ {{$rim->price3}}</td>
                                <td class="hidden-sm-down text-center"></td>

                                <td class="shopping-cart-col">
                                  <div class="clearfix atc_div text-right">
{{--                                    <button class="cart-shopping-button grid-cart-btn" data-toggle="modal">--}}
{{--                                      <i class="material-icons">add_shopping_cart</i>--}}
{{--                                    </button>--}}
                                    <button class="cart-shopping-button grid-cart-btn" data-toggle="modal"
                                            @if (Auth::user()) data-target="#quick-popup" @else data-target="#blockcart-modal"
                                            @endif data-info="{{ $rim->rim_id }}"><i
                                        class="material-icons">add_shopping_cart</i>
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
{{--                      {{ $rims->links() }}--}}
                    </div>
                    <div id="js-product-list-bottom">
                      <div id="js-product-list-bottom"></div>
                    </div>
                  </section>
                </section>
              </div>

              {{--                <div id="content-wrapper" class="col-md-12 col-lg-9">--}}
{{--                    <section id="main">--}}
{{--                        <section id="products">--}}
{{--                            <div id="" class="hidden-sm-down">--}}
{{--                                <section id="js-active-search-filters" class="hide">--}}
{{--                                    <h1 class="h6 hidden-xs-up">Active filters</h1>--}}
{{--                                </section>--}}
{{--                            </div>--}}

{{--                             GRID VIEW--}}
{{--                              <h4 class="rims-title text-uppercase" style="color: black">Lietie diski</h4>--}}
{{--                              <div class="tire-image-container" style="display: none">--}}
{{--                                <div class="tire-image-cards">--}}
{{--                                  @php--}}
{{--                                    $cbrand = '';--}}
{{--                                  @endphp--}}
{{--                                  @foreach($rims as $rim)--}}
{{--                                    @php--}}
{{--                                      $brand = $rim->brand_title;--}}
{{--                                      if ($cbrand!=$brand){--}}
{{--                                        echo '</div><h4 class="tire-brand-name grid-t">' . $brand . '</h4><div class="row grid-ex pr-1">';--}}
{{--                                        $cbrand = $brand;--}}
{{--                                        $stripe = 1;--}}
{{--                                      } else {--}}
{{--                                          $brand = str_replace(" ", "", $brand);--}}
{{--                                      }--}}
{{--                                    @endphp--}}
{{--                                    <a href="{{ route('lietais-disks', [\Str::slug($rim->brand_title), \Str::slug($rim->title), $rim->rim_id]) }}" class="">--}}
{{--                                      <div class="tire-image-card sort-order card">--}}

{{--                                        <div class="text-center">--}}
{{--                                          {!! \Image::showGrid('auto-rim', $rim->make_id) !!}--}}
{{--                                        </div>--}}

{{--                                        <div class="tire-list-caption">--}}
{{--                                          <div class="card-title-text">{{$rim->title}}</div>--}}
{{--                                          <div class="rim-tread">--}}
{{--                                            {{ $rim->d1 }}*{{ $rim->d3 }} ({{ $rim->skr }}*{{$rim->pcd}} et{{$rim->et}})--}}
{{--                                          </div>--}}
{{--                                          <div style="display: inline-flex">--}}
{{--                                            <div class="rim-price-old">€{{$rim->price2}}</div>--}}
{{--                                            <div class="rim-price-red">€{{$rim->price3}}</div>--}}
{{--                                          </div>--}}

{{--                                        </div>--}}
{{--                                      </div>--}}
{{--                                    </a>--}}
{{--                                  @endforeach--}}
{{--                                </div>--}}
{{--                              </div>--}}

{{--                               LIST VIEW--}}
{{--                              <div id="js-product-list">--}}
{{--                                <table id="tires-table" class="table rims-sorter tires-table table-hover tablesorter">--}}
{{--                                  <thead class="tires-thead">--}}
{{--                                  <tr>--}}
{{--                                    <th scope="col"></th>--}}
{{--                                    <th scope="col">Nosaukums</th>--}}
{{--                                    <th scope="col" class="text-center">Izmērs</th>--}}
{{--                                    <th scope="col" class="hidden-sm-down text-center">Skrūvju attālums</th>--}}
{{--                                    <th scope="col" class="hidden-sm-down text-center">ET</th>--}}
{{--                                    <th scope="col" class="hidden-sm-down text-center">Centrs</th>--}}
{{--                                    <th scope="col" class="hidden-sm-down text-center">Krāsa</th>--}}

{{--                                    <th id="store-price-button" scope="col" class="text-center">Veikala cena</th>--}}
{{--                                    <th id="store-sale-button" scope="col" class="text-center">Akcijas cena</th>--}}

{{--                                    <th scope="col" class="hidden-sm-down text-center">Piezīmes</th>--}}
{{--                                    <th scope="col"></th>--}}
{{--                                    <th scope="col">--}}
{{--                                      <div class="tire-table-icon icon-question" title="Pieejamība" data-toggle="tooltip"></div>--}}
{{--                                    </th>--}}

{{--                                  </tr>--}}
{{--                                  </thead>--}}
{{--                                  <tbody id="tires-table-body">--}}
{{--                                  @foreach($rims as $rim)--}}

{{--                                    <tr class="tire-table-row">--}}
{{--                                      <th scope="row" class="tire-table-checkbox">--}}
{{--                                        <input type="checkbox" value="{{$rim->rim_id}}" name="product_ids[]"--}}
{{--                                               class="tire-table-checkbox">--}}
{{--                                      </th>--}}

{{--                                      <td>--}}
{{--                                        <a data-toggle="tooltip" data-html="true" class="rim-table-link tire-table-link"--}}
{{--                                           @if (\Image::exists('auto-rim', $rim->make_id))--}}
{{--                                           title="{{ \Image::show('auto-rim', $rim->make_id) }}"--}}
{{--                                           @else--}}
{{--                                           title="<img src='{{ asset('img/p/en-default-home_default.jpg') }}'>"--}}
{{--                                           @endif--}}
{{--                                           href="{{ route('lietais-disks', [\Str::slug($rim->brand_title), \Str::slug($rim->title), $rim->rim_id]) }}"--}}
{{--                                        >--}}
{{--                                          {{ $rim->brand_title . ' ' . $rim->title }}--}}
{{--                                        </a>--}}
{{--                                      </td>--}}
{{--                                      <td class="text-center">--}}
{{--                                          {{$rim->d1}}*{{$rim->d3}}--}}
{{--                                      </td>--}}

{{--                                      <td class="text-center hidden-sm-down">--}}
{{--                                        {{$rim->skr}} * {{$rim->pcd}}--}}
{{--                                      </td>--}}

{{--                                      <td class="text-center hidden-sm-down">--}}
{{--                                        et{{ $rim->et }}--}}
{{--                                      </td>--}}

{{--                                      <td class="text-center hidden-sm-down">--}}
{{--                                        {{$rim->dc}}--}}
{{--                                      </td>--}}

{{--                                      <td class="hidden-sm-down text-center">--}}
{{--                                        {{$rim->color}}--}}
{{--                                      </td>--}}

{{--                                      <td id="store-price" class="text-center store-price">€ {{$rim->price2}}</td>--}}
{{--                                      <td id="sale-price" class="text-center tire-price-red sale-price">€ {{$rim->price3}}</td>--}}
{{--                                      <td class="hidden-sm-down text-center"></td>--}}

{{--                                      <td class="shopping-cart-col">--}}
{{--                                        <div class="clearfix atc_div text-right">--}}
{{--                                          <button class="cart-shopping-button grid-cart-btn" data-toggle="modal">--}}
{{--                                            <i class="material-icons">add_shopping_cart</i>--}}
{{--                                          </button>--}}
{{--                                        </div>--}}
{{--                                      </td>--}}

{{--                                      <td class="dot-availability text-center">--}}
{{--                                        <span class="dot red" data-toggle="tooltip"--}}
{{--                                              data-html="true"--}}
{{--                                              title="red">--}}
{{--                                          <span class="sort-order">red</span>--}}
{{--                                        </span>--}}
{{--                                      </td>--}}
{{--                                    </tr>--}}
{{--                                  @endforeach--}}
{{--                                  </tbody>--}}
{{--                                </table>--}}
{{--                              </div>--}}
{{--                          {{ $rims->links() }}--}}
{{--                            <div id="js-product-list-bottom">--}}
{{--                                <div id="js-product-list-bottom"></div>--}}
{{--                            </div>--}}
{{--                        </section>--}}
{{--                    </section>--}}
{{--                </div>--}}


            </div>
{{--            @include('components.right-sidebar')--}}
        </div>
    </div>

@endsection
