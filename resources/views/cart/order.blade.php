@extends('layouts.app')

@section('content')


  <div class="container">
    <div class="row">
      <div class="main-content clearfix col-md-12 col-xl-10">
        <div id="content-wrapper" class="right-column col-lg-12">
          <section id="main">
            <div class="cart-grid row">
              <div class="spinner-border"></div>
              <form method="POST">
                @CSRF
                <input type="hidden" name="person" value="{{ \Illuminate\Support\Facades\Session::get('person') }}">

              <h1 class="cart-header">Pasūtījuma noformēšana - <span id="status-name">Privātpersona</span></h1>

              <div class="cart-card card row">

                <div class="btn-group btn-group-toggle person-status-container col-md-12" data-toggle="buttons">

                  <label class="btn btn-secondary @if(\Illuminate\Support\Facades\Session::get('person') == 1) active @endif person-status-item" id="f">
                    <input type="radio" name="data[status]" @if(\Illuminate\Support\Facades\Session::get('person') == 1) checked @endif value="1">
                    Fiziska persona

                  </label>

                  <label class="btn btn-secondary person-status-item @if(\Illuminate\Support\Facades\Session::get('person') == 2) active @endif" id="j">
                    <input type="radio" name="data[status]" @if(\Illuminate\Support\Facades\Session::get('person') == 2) checked @endif value="2">
                    Juridiska persona
                  </label>

                </div>
              </div>


              <div class="cart-card card">
                <h4>Pamatinformācija</h4>

                  <div class="row justify-content-between text-left ">
                    <div class="form-group col-sm-6 flex-column d-flex">
                      <label for="name">Vārds<span class="required-field"> *</span></label>
                      <input type="text" class="form-control" name="data[name]" id="name" value="@if (\Illuminate\Support\Facades\Session::has('cart.name')){{\Illuminate\Support\Facades\Session::get('cart.name')}}@endif" placeholder="Vārds" required>
                    </div>
                    <div class="form-group col-sm-6 flex-column d-flex">
                      <label for="surname">Uzvārds<span class="required-field"> *</span></label>
                      <input type="text" class="form-control" name="data[surname]" id="surname" value="@if (\Illuminate\Support\Facades\Session::has('cart.surname')){{\Illuminate\Support\Facades\Session::get('cart.surname')}}@endif" placeholder="Uzvārds" required>
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="email">E-pasts<span class="required-field"> *</span></label>
                    <input type="email" class="form-control" name="data[email]" id="email" value="@if (\Illuminate\Support\Facades\Session::has('cart.email')){{\Illuminate\Support\Facades\Session::get('cart.email')}}@endif" placeholder="E-pasts" required>
                  </div>

                  <div class="form-group">
                    <label for="phone_number">Tālrunis<span class="required-field"> *</span></label>
                    <input type="text" class="form-control" name="data[phone_number]" id="phone_number" value="@if (\Illuminate\Support\Facades\Session::has('cart.phone_number')){{\Illuminate\Support\Facades\Session::get('cart.phone_number')}}@endif" placeholder="Tālrunis" required>
                  </div>

                    <div class="reveal-if-active">

                      <div class="form-group">
                        <label for="company_registration_number">Reģistrācijas Nr.<span class="required-field"> *</span></label>
                        <input type="text" class="form-control" name="data[company_registration_number]" id="company_registration_number" value="@if (\Illuminate\Support\Facades\Session::has('cart.company_registration_number')){{\Illuminate\Support\Facades\Session::get('cart.company_registration_number')}}@endif" placeholder="Reģistrācijas Nr." required>
                        <span class="registration_number_error">Jūsu ievadītais reģistrācijas numurs nav pareizs</span>
                      </div>

                      <div class="form-group">
                        <label for="company_pvn_number">PVN numurs</label>
                        <input type="text" class="form-control" name="data[company_pvn_number]" id="company_pvn_number" value="@if (\Illuminate\Support\Facades\Session::has('cart.company_pvn_number')){{\Illuminate\Support\Facades\Session::get('cart.company_pvn_number')}}@endif" placeholder="PVN numurs">
                      </div>

                      <div class="form-group">
                        <label for="company_name">Uzņēmuma nosaukums<span class="required-field"> *</span></label>
                        <input type="text" class="form-control" name="data[company_name]" id="company_name" value="@if (\Illuminate\Support\Facades\Session::has('cart.company_name')){{\Illuminate\Support\Facades\Session::get('cart.company_name')}}@endif" placeholder="Uzņēmuma nosaukums" required>
                      </div>

                      <div class="form-group">
                        <label for="company_address">Juridiskā adrese<span class="required-field"> *</span></label>
                        <input type="text" class="form-control" name="data[company_address]" id="company_address" value="@if (\Illuminate\Support\Facades\Session::has('cart.company_address')){{\Illuminate\Support\Facades\Session::get('cart.company_address')}}@endif" placeholder="Juridiskā adrese" required>
                      </div>

                    </div>

                  <div class="form-group">
                    <label for="notes">Piezīmes</label>
                    <input type="text" class="form-control" name="data[notes]" id="notes" value="@if (\Illuminate\Support\Facades\Session::has('cart.notes')){{\Illuminate\Support\Facades\Session::get('cart.notes')}}@endif" placeholder="Piezīmes">
                  </div>

                  <h4>Informācija par transportlīdzekli</h4>

                  <div class="form-group">
                    <label for="brand">Marka<span class="required-field"></span></label>
                    <input type="text" class="form-control" name="data[car_brand]" id="brand" value="@if (\Illuminate\Support\Facades\Session::has('cart.car_brand')){{\Illuminate\Support\Facades\Session::get('cart.car_brand')}}@endif" placeholder="BMW" >
                  </div>

                  <div class="form-group">
                    <label for="model">Modelis<span class="required-field"></span></label>
                    <input type="text" class="form-control" name="data[car_model]" id="model" value="@if (\Illuminate\Support\Facades\Session::has('cart.car_model')){{\Illuminate\Support\Facades\Session::get('cart.car_model')}}@endif" placeholder="330ci" >
                  </div>

                  <div class="form-group">
                    <label for="car_release-year">Izlaiduma gads<span class="required-field"></span></label>
                    <input type="text" class="form-control" name="data[car_release_year]" id="car_release-year" value="@if (\Illuminate\Support\Facades\Session::has('cart.car_release_year')){{\Illuminate\Support\Facades\Session::get('cart.car_release_year')}}@endif" placeholder="2015" >
                  </div>

                  <div class="form-group">
                    <label for="car_engine_size">Dzineja tilpums<span class="required-field"></span></label>
                    <input type="text" class="form-control" name="data[car_engine_size]" id="car_engine_size" value="@if (\Illuminate\Support\Facades\Session::has('cart.car_engine_size')){{\Illuminate\Support\Facades\Session::get('cart.car_engine_size')}}@endif" placeholder="3.0" >
                  </div>

                <div class="cart-checkboxes-card">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="@if (\Session::has('cart.email_notifications')){{1}}@else{{2}}@endif" id="email_notifications" name="data[email_notifications]">
                    <label class="form-check-label" for="email_notifications">
                      Atļaut man sūtīt paziņojumus par akcijām un jaunumiem uz norādīto e-pastu
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="defaultCheck2" required>
                    <label class="form-check-label" for="defaultCheck2">
                      <span class="required-field">*</span>Piekrītu SIA R1 <a href="https://www.r1-dev.area.lv/privatuma-politika" target="_blank">privātuma politikai</a>
                    </label>
                  </div>
                </div>

                  <button type="submit" class="btn btn-primary" name="submit">Tālāk</button>
                </div>

              </form>
            </div>
          </section>
        </div>
      </div>
      @include('components.right-sidebar')
    </div>
  </div>

@endsection
