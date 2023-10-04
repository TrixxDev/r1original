<section id="products">
    <div id="">
        <div id="js-product-list">
        <span class="text-uppercase flipped-title tire-brand-name" style="color:black;">Lietie diski</span>
        <div class="products row hide-price title-flip">
        @php
            $cbrand = '';
            $index = 0;
        @endphp
        @foreach ($rims as $rim)
        @php
          $brand = $rim->d3;
          $rim->includeStock = true;
          if ($cbrand!=$brand){
          $cbrand = $brand;
          $stripe = 1;
        @endphp
        <table id="tires-table" class="table table-striped rims-sorter tires-table table-hover tablesorter">
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
            <th scope="col"
                data-toggle="tooltip"
                data-html="true"
                title="<span style='color: black'>Pieejamība</span>">
              <span class="tire-table-icon icon-question"></span>
            </th>

          </tr>
          </thead>
          <tbody id="tires-table-body">
          <h4 class="tire-brand-name">R{{ $brand }} </h4>
          @php
          }
          @endphp
          <tr class="tire-table-row">
            <th scope="row" class="tire-table-checkbox">
              <input type="checkbox" value="{{$rim->rim_id}}" name="product_ids[]"
                     class="tire-table-checkbox">
            </th>

            <td class="table-tire-name-cell">
              <a class="tire-table-link tippy"
                 data-tippy-content="<div><img data-src='{!! App\Helper\Image::showAd('auto-rim', $rim->make_id) !!}'></div>"
                 href="{{ route('lietais-disks', [\Str::slug($rim->brandTitle), strtolower(str_replace('/', '_', $rim->treadTitle)), $rim->rim_id]) }}"
                 data-content="{{ $rim->fullName }}"
                 data-article="{{ $rim->article }}"
                 data-quantity="{{ $cartQty }}">
                {{ $rim->fullTitle }}
              </a>
            </td>
            <td class="text-center">
              {{$rim->d1}}*{{$rim->d3}}
            </td>

            <td class="text-center hidden-sm-down">
              {{$rim->skr}}x{{$rim->pcd}}
            </td>

            <td class="text-center hidden-sm-down">
              {{ $rim->et }}
            </td>

            <td class="text-center hidden-sm-down">
              {{$rim->dc}}
            </td>

            <td class="hidden-sm-down text-center">
              {{ strtoupper($rim->color) }}
            </td>

            <td id="store-price" class="text-center store-price">€ {{$rim->price1}}</td>
            <td id="sale-price" class="text-center sellout">€ {{$rim->price3}}</td>
            <td class="hidden-sm-down text-center sellout">{{ $rim->comment }}</td>

            <td class="shopping-cart-col">
              <div class="clearfix atc_div text-right">
                {{--                                    <button class="cart-shopping-button grid-cart-btn" data-toggle="modal">--}}
                {{--                                      <i class="material-icons">add_shopping_cart</i>--}}
                {{--                                    </button>--}}
                <button class="cart-shopping-button" data-toggle="modal"
                        @if (Auth::user()) data-target="#" @else data-target="#blockcart-modal"
                        @endif data-info="{{ $rim->rim_id }}"><i
                      class="material-icons">add_shopping_cart</i>
                </button>
              </div>
            </td>

            <td class="dot-availability text-center">
                                  <span class="dot {{ $rim->dotAvailable }} {{ $rim->stockCount }}" data-toggle="tooltip"
                                        data-html="true"
                                        title="{{ $rim->stockAvailability }}">
                                    <span class="sort-order">{{ $rim->dotAvailable }}</span>
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