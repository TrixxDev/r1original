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
                    <h3 class="stepper-title">Grozs</h3>
                  </li>
                  <li class="stepper-item stepper-completed">
                    <h3 class="stepper-title">Dati</h3>
                  </li>
                  <li class="stepper-item stepper-completed">
                    <h3 class="stepper-title">Maksājums</h3>
                  </li>
                  <li class="stepper-item stepper-completed stepper-last">
                    <h3 class="stepper-title">Pabeigts</h3>
                  </li>
                </ol>
              </div>

              <div class="card cart-card text-center">
                <div style="display: inline-flex;">
                  <i class="fa-solid fa-cart-shopping" style="font-size: 23px; margin-right: 10px;"></i>
                  <h1 class="done-cart-title">Paldies!</h1>
                </div>

                <h2>Jūsu pasūtījums ir saņemts un tiek izpildīts.</h2>

                <div class="done-cart-subtext">Pasūtījuma numurs <b>B001240379</b>. Par pasūtījuma gaitu paziņosim Jums e-pastā. Paldies, ka iepērkaties pie mums.</div>

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

