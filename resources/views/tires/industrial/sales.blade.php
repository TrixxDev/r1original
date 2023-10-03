<section id="products">
    <div id="">
        <div id="js-product-list">
            <span class="text-uppercase flipped-title tire-brand-name" style="color:black;">Lielās riepas</span>
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

                        if ($cbrand) {
                           echo '<h4 class="tire-brand-name">' . $cbrand;
                        }
                        if ($index == 0){
                          echo '</h4>';
                        }

                    @endphp

                    <table id="tires-table" class="table table-striped industrial-sorter tires-table table-hover tablesorter">
                        <thead class="tires-thead sticky-table">
                        <tr>
                            <th scope="col"></th>
                            <th scope="col" class="table-tire-name-cell">Brends / modelis</th>
                            <th scope="col" style="text-align: center;">Ass</th>
                            <th scope="col" class="text-center">LI/SI</th>
                            <th scope="col" class="hidden-sm-down text-center">
                                Kods
                            </th>

                            <th id="store-price-button" scope="col" class="text-center">
                                Veikala cena
                            </th>

                            <th id="store-sale-button" scope="col" class="text-center">Akcijas cena</th>
                            <th scope="col" class="hidden-sm-down text-center">Piezīmes</th>
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
                          }
                        @endphp
                        @if ($loop->last) <h4 class="tire-brand-name">{{ $brand }}</h4> @endif
                        <tr class="tire-table-row">
                            <th scope="row" class="tire-table-checkbox">
                                <input type="checkbox" value="{{ $tire->tire_id }}" name="product_ids[]"
                                       class="tire-table-checkbox">
                            </th>

                            <td class="table-tire-name-cell" data-link="{{ route('lielas-riepas') }}">
                                <a data-toggle="tooltip" data-html="true" class="tire-table-link"
                                   title='{!! App\Helper\Image::show('big', $tire->make_id) !!}'
                                   href="{{ route('lielas-riepa', [strtolower(\Tires::getBigTireBrand($tire->tread->brand_id)->title), strtolower(str_replace('/', '_', $tire->tread->title)), $tire->tire_id]) }}"
                                   data-content="{{ $tire->fullName }}"
                                   data-article="{{ $tire->article }}"
                                   data-quantity="{{ $cartQty }}">
                                    <div class="table-link-title">{{ $tire->title }}</div>
                                </a>
                            </td>
                            {{--                           ass --}}
                            <td class="hidden-sm-down text-center">
                                @if (isset($code_array[$tire->axis]))
                                    <span data-toggle="tooltip" title="<span style='color: black'>
                              @if (isset($code_array[$tire->axis]))
                                    {!! $code_array[$tire->axis] !!}
                                    @endif
                                            </span>" class="hidden-sm-down table-cell prod-code">
                              @if ($tire->axis)
                                            {{ $tire->axis }}
                                        @endif
                            </span>
                                @else
                                    @if ($tire->axis)
                                        {{ $tire->axis }}
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
                                @if (!empty($tire->code)) {{ $tire->code . 'PR' }} @endif
                            </td>

                            <td id="store-price" class="text-center store-price">€ {{ $tire->price1 }}</td>
                            <td id="sale-price" class="text-center tire-price-red">€ {{ $tire->price3 }}</td>
                            <td class="hidden-sm-down text-center @if($tire->comment == 'Izpārdošana!' || $tire->priceoffer == 1){{ 'sellout' }}@endif">{{$tire->comment}}</td>

                            <td class="shopping-cart-col">
                                <div class="clearfix atc_div text-right">
                                    <button class="cart-shopping-button" data-toggle="modal"
                                            @hasrole('administrators') data-target="#" @else data-target="#blockcart-modal" @endhasrole data-info="{{ $tire->tire_id }}"><i
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