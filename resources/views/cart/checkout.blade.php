@extends('layouts.app')

@section('content')

  <div class="container">
    <div class="row">
      <div class="main-content clearfix col-md-12 col-xl-10">
        <div id="content-wrapper" class="right-column col-lg-12">
          <section id="main">
            <div class="cart-grid row">
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
                        <td><i class="fa fa-mobile fa-lg m-r-5"></i>@if (\Session::has('cart.email')) {{ \Session::get('cart.email') }} @endif</td>
                      </tr>
                      <tr>
                        <td class="field">Tālrunis</td>
                        <td>+371 @if (\Session::has('cart.phone_number')) {{ \Session::get('cart.phone_number') }} @endif</td>
                      </tr>
                      <tr>
                        <td class="field">Saņemšanas vieta</td>
                        <td>Ulbroka, Institūta iela 1</td>
                      </tr>
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
                      <h4>Pasūtītās preces</h4>
                    @foreach (\Cart::content() as $item)
                      <div class="cart-item-table cart-item-container">
                        <div class="item-name cart-item-name">
                          <a href="{{ $item->options->link }}" data-id_customization="0">{{ strtoupper($item->name) . ' ' . $item->options->tire['d1'] . ' ' . $item->options->tire['d2'] . ' ' . $item->options->tire['d3'] . ' ' . $item->options->tire['li'].$item->options->tire['si'] }}</a>
                          <br>
                          <span class="item-price">€ {{ $item->options->tire['price2'] }} x {{$item->qty}}</span>
                          <br>
                        </div>
                        <div class="qty-item">
                          <div class="input-group bootstrap-touchspin">
                            <span class="input-group-addon bootstrap-touchspin-prefix" style="display: none;"></span>
                            <span class="input-group-addon bootstrap-touchspin-postfix" style="display: none;"></span>
                          </div>
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
                    <hr>
                    <form method="POST">
                      <div class="form-check">
                        <input type="radio" value="" id="paymentCheck1" name="payment">
                        <label for="paymentCheck1">
                          Apmaksa saņemšanas brīdī
                        </label>
                      </div>
                      <div class="form-check">
                        <input type="radio" value="" id="paymentCheck2" name="payment">
                        <label for="paymentCheck2">
                          Bankas pārskaitījums
                        </label>
                      </div>
                      @if (count($cats) == 1 && !in_array('red', $dogs))
                      <div class="form-check">
                        <input type="radio" value="" id="paymentCheck3" name="payment" checked>
                        <label for="paymentCheck3">
                          Tiešsaistes apmaksa
                        </label>
                      </div>
                      @endif
                    </form>
                    <hr>
                    <form method="post" class="checkout-buttons">
                      @csrf
                      <div class="btn-checkout-group" role="group" aria-label="Basic example">
                        <a href="{{ route('cart') }}" class="btn-secondary btn-checkout">Labot grozu</a>
                        <a href="{{ route('order') }}" class="btn-secondary btn-checkout">Labot datus</a>
                        <a href="{{ route('order.printCart', $order_id) }}" class="btn-secondary btn-checkout print-checkout-button">Drukāt</a>
                        <button type="submit" name="pay" value="pay" class="btn-checkout-primary btn-checkout">Apmaksāt</button>
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

@endsection

