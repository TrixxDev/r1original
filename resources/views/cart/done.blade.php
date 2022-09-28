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

              <div class="card cart-card">
                <h1>Pasūtījuma informācija</h1>
                <hr>
                <!-- begin table -->


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

