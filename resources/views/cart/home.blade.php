@extends('layouts.app')

@section('content')

  @php
    $cart_have_others = 0;
  @endphp

    <div class="container">
        <div class="row">
            <div class="main-content clearfix col-md-12 col-xl-10">
                <div id="content-wrapper" class="right-column col-lg-12">
                    <section id="main">
                      <div class="stepper-wrapper">
                        <ol class="stepper">
                          <li class="stepper-item stepper-active">
                            <h3 class="stepper-title hidden-md-down">Grozs</h3>
                          </li>
                          <li class="stepper-item">
                            <h3 class="stepper-title hidden-md-down">Dati</h3>
                          </li>
                          <li class="stepper-item">
                            <h3 class="stepper-title hidden-md-down">Maksājums</h3>
                          </li>
                          <li class="stepper-item stepper-last">
                            <h3 class="stepper-title hidden-md-down">Pabeigts</h3>
                          </li>
                        </ol>
                      </div>
                        <div class="cart-grid row">
                            <!-- Left Block: cart product informations & shpping -->
                            <div class="cart-grid-body @if (\Cart::count() > 0)col-xs-12 col-lg-8 @else col-xs-12 col-lg-12 @endif">
                                <!-- cart products detailed -->
                                <div class="card cart-container">
                                    <div class="card-block">
                                        <h1 class="h1">Iepirkšanās grozs</h1>
                                    </div>
                                    <hr class="separator">
                                    @if (\Cart::count() > 0)
                                        @foreach (\Cart::content() as $item)
                                        <div class="cart-item-table cart-item-container">
                                          <div class="item-name cart-item-name">
                                              <a href="{{ $item->options->link }}" data-id_customization="0" style="text-transform: uppercase;">{{ strtoupper($item->options->tireObj->fullName) }}</a>
                                              <br>
                                              @if ($item->associatedModel == "App\Models\Rim")
                                                <span class="item-price">€ {{ $item->options->tire['price3'] }}</span>
                                              @else
                                                <span class="item-price">€ {{ $item->options->tire['price2'] }}</span>
                                              @endif
                                            <br>
                                          </div>
                                          <div class="qty-item">
                                            <div class="input-group bootstrap-touchspin">
                                              <span class="input-group-addon bootstrap-touchspin-prefix" style="display: none;"></span>
                                              <input class="js-cart-line-product-quantity form-control" data-down-url="" data-up-url="" data-update-url="" data-item-price="{{ $item->price }}" data-product-id="{{ $item->rowId }}" type="text" value="{{ $item->qty }}" name="product-quantity-spin" min="1" style="display: block;">
                                              <span class="input-group-addon bootstrap-touchspin-postfix" style="display: none;"></span>
                                              <span class="input-group-btn-vertical">
                                                  <button class="btn btn-touchspin js-touchspin js-increase-product-quantity bootstrap-touchspin-up" type="button">
                                                      <i class="material-icons touchspin-up"></i>
                                                  </button>
                                                  <button class="btn btn-touchspin js-touchspin js-decrease-product-quantity bootstrap-touchspin-down" type="button">
                                                      <i class="material-icons touchspin-down"></i>
                                                  </button>
                                              </span>
                                            </div>
                                            </div>
                                          <div class="tire-price">
                                            <div class="price">
                                              <span class="product-price" data-product-id="{{ $item->rowId }}">
                                                <strong>€ {{ round($item->price * $item->qty) }}</strong>
                                              </span>
                                            </div>
                                          </div>
                                          <div class="cart-trash">
                                            <div class="cart-line-product-actions">
                                              <a class="remove-from-cart" rel="nofollow" href="{{ route('cart.remove', $item->rowId) }}" data-link-action="delete-from-cart" data-id-product="{{ $item->id }}" data-id-product-attribute="8274" data-id-customization="">
                                                <i class="material-icons float-xs-left" title="Dzēst">delete</i>
                                              </a>
                                            </div>
                                          </div>
                                        </div>
                                      <hr class="separator">
                                        @endforeach
                                    @else
                                        <div class="cart-overview js-cart" data-refresh-url="//r1riepas.lv/index.php?controller=cart&amp;ajax=1&amp;action=refresh">
                                            <span class="no-items">Jūsu grozs ir tukšs</span>
                                        </div>
                                    @endif
{{--                                  @if ($cart->count() > 0)--}}
{{--                                  <div class="cart-delivery-choice">--}}
{{--                                    <form method="POST">--}}
{{--                                      <h4>Saņemšanas vieta</h4>--}}
{{--                                      <div class="cart-options">--}}
{{--                                        <label class="cart-delivery-label">--}}
{{--                                          <input type="radio" name="cart-delivery-radio" checked value="1">--}}
{{--                                          <span>Ulbroka, Institūta iela 1</span>--}}
{{--                                        </label>--}}
{{--                                        <label class="cart-delivery-label">--}}
{{--                                          <input type="radio" name="cart-delivery-radio" value="2">--}}
{{--                                          <span>Rīga, Kalnciema iela 39</span>--}}
{{--                                        </label>--}}
{{--                                        <label class="cart-delivery-label">--}}
{{--                                          <input type="radio" name="cart-delivery-radio" value="3">--}}
{{--                                          <span>Piegāde</span>--}}
{{--                                        </label>--}}
{{--                                      </div>--}}

{{--                                      <div class="cart-delivery-option">--}}
{{--                                        <div class="form-group">--}}
{{--                                          <select class="custom-select">--}}
{{--                                            <option selected>Rīga</option>--}}
{{--                                            <option value="1">Salaspils</option>--}}
{{--                                            <option value="2">Cits</option>--}}
{{--                                          </select>--}}
{{--                                        </div>--}}

{{--                                        <div class="form-group">--}}
{{--                                          <label for="shipping_address">Piegādes adrese<span class="required-field"> *</span></label>--}}
{{--                                          <input type="text" class="form-control" name="data[shipping_address]" id="shipping_address" value="@if (\Illuminate\Support\Facades\Session::has('cart.surname')){{\Illuminate\Support\Facades\Session::get('cart.surname')}}@endif" placeholder="Piegādes adrese" required>--}}
{{--                                        </div>--}}

{{--                                        <div class="form-group">--}}
{{--                                          <label for="email">Durvju kods</label>--}}
{{--                                          <input type="email" class="form-control" name="data[email]" id="email" value="@if (\Illuminate\Support\Facades\Session::has('cart.email')){{\Illuminate\Support\Facades\Session::get('cart.email')}}@endif" placeholder="Durvju kods">--}}
{{--                                        </div>--}}
{{--                                      </div>--}}
{{--                                    </form>--}}
{{--                                  </div>--}}
{{--                                  @endif--}}
                                </div>
                                <a class="label" href="@if (isset($_SERVER['HTTP_REFERER'])) {{ $_SERVER['HTTP_REFERER'] }} @else {{ 'javascript:history.back(-1)' }} @endif">
                                    <i class="material-icons">chevron_left</i>Turpināt iepirkties
                                </a>
                                <!-- shipping informations -->
                            </div>
                          @if (\Cart::count() > 0)
                            <form method="POST">
                            @csrf
                            <input type="hidden" name="delivery">
                            <input type="hidden" name="fitting">
                            <input type="hidden" name="delivery_price">
                            <input type="hidden" name="fitting_price">
                            <div class="cart-grid-right col-xs-12 col-lg-4">
                              <div class="card cart-summary">
                                <div class="cart-delivery-choice">
                                    <h4>Saņemšanas vieta</h4>
                                    <div class="cart-options">
                                      <label class="cart-delivery-label">
                                        <input type="radio" name="data[cart_delivery_radio]" checked value="1">
                                        <span>Ulbroka, Acones iela 2A</span>
                                      </label>
                                      <label class="cart-delivery-label">
                                        <input type="radio" name="data[cart_delivery_radio]" value="2">
                                        <span>Rīga, Kalnciema iela 39</span>
                                      </label>
                                      <label class="cart-delivery-label">
                                        <input type="radio" name="data[cart_delivery_radio]" value="3" id="cart_delivery">
                                        <span>Piegāde</span>
                                      </label>
                                    </div>
                                    <div class="cart-montage-choice">
                                      <hr>
                                      <h4>Montāža</h4>
                                      <div class="cart-delivery-options">
                                        <label class="cart-delivery-label">
                                          <input type="radio" name="cart-montage-radio" value="1" @if (\Session::has('cartOptions.fitting_needs')) checked @endif>
                                          <span>{{ \Cart::count() }} @if (\Cart::count() == 1) Riepai @else Riepām @endif</span>
                                        </label>
                                        <label class="cart-delivery-label">
                                          <input type="radio" name="cart-montage-radio" value="0" @if (!\Session::has('cartOptions.fitting_needs')) checked @endif>
                                          <span>Montāža nebūs nepieciešama vai par to maksāšu uz vietas</span>
                                        </label>
                                      </div>
                                    </div>
                                    <div class="cart-delivery-option">
                                      <div class="form-group">
                                        <select class="custom-select" name="data[shipping_city]">
                                          <option value="1" selected>Rīga</option>
                                          <option value="3">Cits</option>
                                        </select>
                                      </div>

                                      <div class="form-group">
                                        <label for="shipping_address">Piegādes adrese<span class="required-field"> *</span></label>
                                        <input type="text" class="form-control" name="data[shipping_address]" id="shipping_address" value="@if (\Illuminate\Support\Facades\Session::has('cart.shipping_address')){{\Illuminate\Support\Facades\Session::get('cart.shipping_address')}}@endif" placeholder="Piegādes adrese" required>
                                      </div>

                                      <div class="form-group">
                                        <label for="door_code">Durvju kods</label>
                                        <input type="text" class="form-control" name="data[door_code]" id="door_code" value="@if (\Illuminate\Support\Facades\Session::has('cart.door_code')){{\Illuminate\Support\Facades\Session::get('cart.door_code')}}@endif" placeholder="Durvju kods">
                                      </div>
                                    </div>
                                </div>
                              </div>
{{--                            </div>--}}

{{--                            <div class="cart-grid-right col-xs-12 col-lg-4">--}}
                                <div class="card cart-summary">
                                  <div class="cart-detailed-totals">
                                    <div class="card-block">
                                      <div class="cart-summary-line" id="cart-subtotal-products">
                                                <span class="label js-subtotal">
                                                    {{ \Cart::count() }} Preces
                                                </span>
                                        <span class="value">€ {{ substr(\Cart::subtotal(), 0, -3) }}</span>
                                      </div>
                                      <div class="cart-summary-line" id="cart-subtotal-montage">
                                                  <span class="label">
                                                      Montāža
                                                  </span>
                                        <span id="shipping_price" class="value">Nav</span>
                                        <div><small class="value"></small></div>
                                      </div>
                                      <div class="cart-summary-line" id="cart-subtotal-shipping">
                                                <span class="label">
                                                    Piegāde
                                                </span>
                                        <span id="shipping_price" class="value">Bezmaksas</span>
                                        <div><small class="value"></small></div>
                                      </div>
                                    </div>
                                    <hr class="separator">
                                      <div class="card-block">
                                          <div class="cart-summary-line">
                                              <span class="label">Atlaižu kods</span>
                                              <span class="value"></span>
                                          </div>

                                          <div class="cart-summary-line">
                                              <input type="text" class="form-control" name="data[promo_code]" @if (session('promo_error')) style="border: 1px solid #ef7272;" @endif value="" title="">
                                              @if (session('promo_error'))<span class="label promo_validation" style="color: red">Kods nav derīgs</span>@endif
                                              <br>
                                              <button type="button" class="btn btn-primary btn-block check_promo"><span>Pārbaudīt</span></button>
                                          </div>
                                      </div>
                                    <hr class="separator">
                                    <div class="card-block">
                                      <div class="cart-summary-line cart-total">
                                        <span class="label">Pavisam kopā: (ar PVN)</span>
                                        <span class="value">€ {{ substr(\Cart::subtotal(), 0, -3) }}</span>
                                      </div>

                                      <div class="cart-summary-line">
                                        <small class="label"></small>
                                        <small class="value"></small>
                                      </div>
                                    </div>
                                    <hr class="separator">
                                  </div>
                                  @if (\Cart::count() > 0)
                                      @if (Auth::check())
                                            <div class="checkout text-sm-center card-block checkout-button">
                                                <button type="submit" class="btn btn-primary btn-block"><span>Turpināt</span></button>
                                            </div>
                                        @else
                                            <div class="checkout text-sm-center card-block checkout-button">
                                                <div class="checkout-buttons">
                                                    <a href="/login" class="btn btn-primary"><span>Ienākt</span></a>
                                                    <a href="/register" class="btn btn-primary"><span>Reģistrēties</span></a>
                                                </div>
                                                <button type="submit" style="font-size: 14px;" class="btn btn-primary btn-block"><span>Pasūtīt nereģistrējoties</span></button>
                                            </div>
                                        @endif
                                  @else
                                    <div class="checkout text-sm-center card-block checkout-button">
                                      <button type="button" class="btn btn-primary disabled" disabled="">Noformēt pasūtījumu</button>
                                    </div>
                                  @endif
                                </div>
                              </div>
                            </form>
                          @endif
                            <!-- Right Block: cart subtotal & cart total -->

                        </div>
                    </section>
                </div>
            </div>
        @include('components.right-sidebar')
        </div>
    </div>

@endsection
