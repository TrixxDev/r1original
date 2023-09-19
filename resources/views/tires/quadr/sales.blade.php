<section id="products">
    <div id="">
        <div id="js-product-list">
            <span class="tire-type-title tire-brand-name flipped-title" style="color: black">Kvadraciklu riepas</span>
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
                        echo '<h4 class="tire-brand-name">' . $cbrand . '</h4>';
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
                        <tr class="tire-table-row">
                            <th scope="row" class="tire-table-checkbox">
                                <input type="checkbox" value="{{ $tire->tire_id }}" name="product_ids[]"
                                       class="tire-table-checkbox">
                            </th>

                            <td class="table-tire-name-cell" data-link="{{ route('kvadraciklu-riepas') }}">
                                <a data-toggle="tooltip" data-html="true" class="tire-table-link"
                                   title='{!! App\Helper\Image::show('quadr', $tire->make_id) !!}'
                                   href="{{ route('kvadraciklu-riepa', [strtolower(\Tires::getQuadrTireBrand($tire->tread->brand_id)->title), strtolower(str_replace('/', '_', $tire->tread->title)), $tire->tire_id]) }}"
                                   data-content="{{ $tire->fullName }}"
                                   data-article="{{ $tire->article }}"
                                   data-quantity="{{ (new \App\Http\Controllers\QuadTireController(new \Illuminate\Http\Request()))->cartQty }}">
                                    {{ $tire->title }}
                                </a>
                            </td>

                            <td class="text-center">{{$tire->code}}</td>
                            <td id="store-price" class="text-center store-price">€ {{ $tire->price1 }}</td>
                            <td id="sale-price" class="text-center tire-price-red sellout">€ {{ $tire->price2 }}</td>
                            <td class="hidden-sm-down text-center sellout">{{$tire->comment}}</td>

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
        </div>
    </div>
</section>