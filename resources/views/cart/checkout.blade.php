@extends('layouts.app')

@section('content')

  <div class="container">
    <div class="row">
      <div class="main-content clearfix col-md-12 col-xl-10">
        <div id="content-wrapper" class="right-column col-lg-12">
          <section id="main">
            <div class="cart-grid row">
              {{--Stepper--}}
              <div class="stepper-wrapper">
                <ol class="stepper">
                  <li class="stepper-item stepper-completed">
                    <h3 class="stepper-title hidden-md-down">Grozs</h3>
                  </li>
                  <li class="stepper-item stepper-completed">
                    <h3 class="stepper-title hidden-md-down">Dati</h3>
                  </li>
                  <li class="stepper-item stepper-active">
                    <h3 class="stepper-title hidden-md-down">Maksājums</h3>
                  </li>
                  <li class="stepper-item stepper-last">
                    <h3 class="stepper-title hidden-md-down">Pabeigts</h3>
                  </li>
                </ol>
              </div>

                <div class="card cart-card">
                  <h1>Pasūtījuma informācija</h1>
                  <hr>
                  <!-- begin table -->
                  <div class="table-responsive checkout-table">
                    <h4>Pamatinformācija</h4>
                    <table class="table table-hover table-striped">
                      <tbody>
                      <tr class="d-flex">
                        <td class="field">Vārds, uzvārds</td>
                        <td>@if (\Session::has('cart.name') && \Session::has('cart.surname')) {{ \Session::get('cart.name') . ', ' . \Session::get('cart.surname') }}@endif</td>
                      </tr>
                      <tr>
                        <td class="field">e-pasts</td>
                        <td>@if (\Session::has('cart.email')) {{ \Session::get('cart.email') }} @endif</td>
                      </tr>
                      <tr>
                        <td class="field">Tālrunis</td>
                        <td>@if (\Session::has('cart.phone_number')) {{ \Session::get('cart.phone_number') }} @endif</td>
                      </tr>
                      <tr>
                        <td class="field">Saņemšanas vieta</td>
                        <td>
                          @if (isset($user_data['fitting']))
                            {{ \App\Models\Office::findOrFail($user_data['fitting_address'])->shipping }}
                          @else
                            @if (isset($user_data['shipping_address']))
                              {{ $user_data['shipping_address'] }}, @if ($user_data['shipping_city'] == 1) Rīga @else Cits @endif
                            @else
                              {{ \App\Models\Office::findOrFail($user_data['fitting_address'])->shipping }}
                            @endif
                          @endif
                        </td>
                      </tr>
                      @if (isset($user_data['door_code']))
                      <tr>
                        <td class="field">Durvju kods</td>
                        <td>{{ $user_data['door_code'] }}</td>
                      </tr>
                      @endif
                        @if (\Session::get('person') == 2)

                          <tr class="highlight">
                            <td class="field">Reģistrācijas Nr.</td>
                            <td>@if (\Session::has('cart.company_registration_number')) {{ \Session::get('cart.company_registration_number') }} @endif</td>
                          </tr>

                          <tr class="highlight">
                            <td class="field">PVN numurs</td>
                            <td>@if (\Session::has('cart.company_pvn_number')) {{ \Session::get('cart.company_pvn_number') }} @endif</td>
                          </tr>

                          <tr class="highlight">
                            <td class="field">Uzņēmuma nosaukums</td>
                            <td>@if (\Session::has('cart.company_name')) {{ \Session::get('cart.company_name') }} @endif</td>
                          </tr>

                          <tr class="highlight">
                            <td class="field">Juridiskā adrese</td>
                            <td>@if (\Session::has('cart.company_address')) {{ \Session::get('cart.company_address') }} @endif</td>
                          </tr>
                        @endif

                      </tbody>
                    </table>
                    <hr>
                    @if (\Session::has('cart.notes') || \Session::has('cart.email_notifications'))
                      <h4>Papildus informācija</h4>
                      <table class="table table-hover">
                        <tbody>
                        @if (\Session::has('cart.notes'))
                        <tr class="highlight">
                          <td class="field">Piezīmes</td>
                          <td>{{ \Session::get('cart.notes') }}</td>
                        </tr>
                        @endif
                        @if (\Session::has('cart.email_notifications'))
                        <tr class="highlight">
                          <td class="field">E-pasta paziņojumi</td>
                          <td>Atļauju man sūtīt paziņojumus par akcijām un jaunumiem uz norādīto e-pastu</td>
                        </tr>
                        @endif
                        </tbody>
                      </table>
                      <hr>
                    @endif
                    @if (\Session::has('cart.car_brand'))
                    <h4>Informācija par automašīnu</h4>
                    <table class="table table-hover">
                      <tbody>
                      <tr class="highlight d-flex">
                        <td class="field">Marka</td>
                        <td>@if (\Session::has('cart.car_brand')) {{ \Session::get('cart.car_brand') }} @endif</td>
                      </tr>
                      <tr class="highlight">
                        <td class="field">Modelis</td>
                        <td>@if (\Session::has('cart.car_model')) {{ \Session::get('cart.car_model') }} @endif</td>
                      </tr>
                      <tr class="highlight">
                        <td class="field">Izlaiduma gads</td>
                        <td>@if (\Session::has('cart.car_release_year')) {{ \Session::get('cart.car_release_year') }} @endif</td>
                      </tr>
                      <tr class="highlight">
                        <td class="field">Dzinēja tilpums</td>
                        <td>@if (\Session::has('cart.car_engine_size')) {{ \Session::get('cart.car_engine_size') }} @endif</td>
                      </tr>
                      </tbody>
                    </table>
                      <hr>
                    @endif
                    <h4>Pasūtītās preces</h4>
                    @foreach (\Cart::content() as $item)
                      @if (\Session::has('cart.promo_code_perc')) {{ \Cart::setDiscount($item->rowId, \Session::get('cart.promo_code_perc')->value) }} @endif
                      <div class="cart-item-table cart-item-container">
                        <div class="item-name cart-item-name">
                          <a href="{{ $item->options->link }}" data-id_customization="0" style="text-transform: uppercase;">{{ strtoupper($item->options->tireObj->fullName) }}</a>
                          <br>
                          <span class="item-price">€ {{ $item->options->tire['price2'] }} x {{$item->qty}}</span>
                          <br>
                        </div>
                        <div class="tire-price">
                          <div class="price">
                            <span class="product-price" data-product-id="{{ $item->rowId }}">
                              <strong>€ {{ round($item->price * $item->qty) }}</strong>
                            </span>
                          </div>
                        </div>
                      </div>
                    @endforeach
                      @if (\Cart::discount() > 0 || !is_null($total))
                      <div class="cart-item-table cart-item-container">
                          <div class="item-name cart-item-name">
                              Atlaižu kods
                          </div>
                          <div class="tire-price">
                              <div class="price">
                                  <span class="product-price">
                                      @if ($total)
                                          <strong>€ -{{ (int) substr(\Cart::subTotal(), 0, -3) - (int) substr($total, 0, -2) }}</strong>
                                      @else
                                          <strong>€ -{{ substr(\Cart::discount(), 0, -3) }}</strong>
                                      @endif
                                  </span>
                              </div>
                          </div>
                      </div>
                    @endif
                    @if (isset($user_data['fitting']) && $user_data['fitting'] == true)
                      <div class="cart-item-table cart-item-container">
                        <div class="item-name cart-item-name">
                          Riepu montāža
                        </div>
                        <div class="tire-price">
                          <div class="price">
                            <span class="product-price">
                              <strong>€ {{ substr($user_data['fitting_price'], 0, -2) }}</strong>
                            </span>
                          </div>
                        </div>
                      </div>
                    @endif
                    @if (isset($user_data['shipping_city']))
                      <div class="cart-item-table cart-item-container">
                        <div class="item-name cart-item-name">
                          Piegāde
                        </div>
                        <div class="tire-price">
                          <div class="price">
                            <span class="product-price">
                              @if ($user_data['delivery_price'] == null)
                                <strong>Bezmaksas!</strong>
                              @else
                                <strong>€ {{ substr($user_data['delivery_price'], 0, -2) }}</strong>
                              @endif
                            </span>
                          </div>
                        </div>
                      </div>
                    @endif
                      <hr>
                      <div class="cart-item-table cart-item-container">
                          <div class="item-name cart-item-name">
                              Kopā
                          </div>
                          <div class="tire-price">
                              <div class="price">
                            <span class="product-price">
                                @if (isset($total))
                                    @if (isset($user_data['fitting']) && $user_data['fitting'] == true)
                                        <strong>€ {{ (int) substr($total, 0, -2) + (int) substr($user_data['fitting_price'], 0, -2) }}</strong>
                                    @elseif (isset($user_data['shipping_city']))
                                        <strong>€ {{ (int) substr($total, 0, -2) + (int) substr($user_data['delivery_price'], 0, -2) }}</strong>
                                    @else
                                        <strong>€ {{ substr($total, 0, -2) }}</strong>
                                    @endif
                                @else
                                    @if (isset($user_data['fitting']) && $user_data['fitting'] == true)
                                        <strong>€ {{ (int) substr(\Cart::subTotal(), 0, -3) + (int) substr($user_data['fitting_price'], 0, -2) }}</strong>
                                    @elseif (isset($user_data['shipping_city']))
                                        <strong>€ {{ (int) substr(\Cart::subTotal(), 0, -3) + (int) substr($user_data['delivery_price'], 0, -2) }}</strong>
                                    @else
                                        <strong>€ {{ substr(\Cart::subTotal(), 0, -3) }}</strong>
                                    @endif
                                @endif
                            </span>
                              </div>
                          </div>
                      </div>
                    <hr>
                    <form method="post">
                      @csrf
                      @if (!isset($user_data['shipping_city']) || $user_data['shipping_city'] == 1)
                      <div class="form-check">
                        <input type="radio" value="1" id="paymentCheck1" name="payment" required checked>
                        <label for="paymentCheck1">
                          Apmaksa saņemšanas brīdī
                        </label>
                      </div>
                      @endif
                      <div class="form-check">
                        <input type="radio" value="2" id="paymentCheck2" name="payment" required @if (isset($user_data['shipping_city']) && $user_data['shipping_city'] != 1) checked @endif>
                        <label for="paymentCheck2">
                          Bankas pārskaitījums
                        </label>
                      </div>
                      @if (count($cats) == 1 && !in_array('red', $dogs))
                      <div class="form-check">
                        <input type="radio" value="3" id="paymentCheck3" name="payment" required checked>
                        <label for="paymentCheck3">
                          Tiešsaistes apmaksa
                        </label>
                      </div>
                      @endif
                      <hr>
                      <div class="btn-checkout-group" role="group" aria-label="Basic example">
                        <a href="{{ route('cart') }}" class="btn-secondary btn-checkout">Labot grozu</a>
                        <a href="{{ route('order') }}" class="btn-secondary btn-checkout">Labot datus</a>
                        @if (count($cats) == 1 && !in_array('red', $dogs))
                          <button type="submit" name="pay" value="pay" class="btn-checkout-primary btn-checkout">Apmaksāt</button>
                        @else
                          <button type="submit" name="end" value="end" class="btn-checkout-primary btn-checkout">Turpināt</button>
                        @endif
                      </div>
                    </form>
                  </div>

                </div>

            </div>
          </section>
        </div>
      </div>
      @include('components.right-sidebar')
    </div>
  </div>

{{--  <script>--}}
{{--    window.history.replaceState({}, '',window.location.href);--}}
{{--  </script>--}}

@endsection

