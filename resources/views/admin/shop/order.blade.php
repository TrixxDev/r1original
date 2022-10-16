@extends('admin.layouts.app')

@section('content')

{{--  <pre>{{ json_encode($order, JSON_PRETTY_PRINT) }}</pre>--}}

{{--  <div class="container">--}}
{{--    <div class="row">--}}
{{--      <div class="col-4 float-right">Nosaukums</div>--}}
{{--      <div class="col-6">Labs nosaukums</div>--}}
{{--    </div>--}}
{{--  </div>--}}
{{--  <pre>--}}
{{--    {{ json_encode(unserialize($order->info), JSON_PRETTY_PRINT) }}--}}
{{--  </pre>--}}

    <div class="container">

      <div class="form-group row">
        <label class="col-md-3 form-control-label text-left text-md-right">
          <h3>Pasūtījuma informācija</h3>
        </label>
        <div class="col-md-6">

        </div>
        <div class="col-md-3 form-control-comment">
        </div>
      </div>
      <form id="orderUpdate" method="POST" action="{{ route('admin.order.update', $order->id) }}">
      @csrf
      <div class="form-group row">
        <label class="col-md-3 form-control-label text-left text-md-right">
          Rēķina numurs
        </label>
        <div class="col-md-6">
          <input class="form-control" disabled="" name="bill_number" type="text" value="{{$order->id}}">
        </div>
        <div class="col-md-3 form-control-comment">
        </div>
      </div>

      <div class="form-group row">
        <label class="col-md-3 form-control-label text-left text-md-right">
          Pasūtīšanas datums
        </label>
        <div class="col-md-6">
          <input class="form-control" name="order_date" type="text" value="{{$order->created_at}}">
        </div>
        <div class="col-md-3 form-control-comment">
        </div>
      </div>
      <div class="form-group row">
        <label class="col-md-3 form-control-label text-left text-md-right">
          Statuss
        </label>
        <div class="col-md-6">
{{--          <input class="form-control" name="status" type="text" value="{{$order->status}}" required="">--}}
          <select id="select" name="order_status" class="custom-select">
            <option value="2" @if ($order->status == 2) selected="" @endif>Jauns</option>
            <option value="3" @if ($order->status == 3) selected="" @endif>Gaidam samaksu</option>
            <option value="4" @if ($order->status == 4) selected="" @endif>Gaidam preci</option>
	    <option value="5" @if ($order->status == 5) selected="" @endif>Gatavs</option>
          </select>
        </div>
        <div class="col-md-3 form-control-comment">
        </div>
      </div>

      <div class="form-group row">
        <label class="col-md-3 form-control-label text-left text-md-right">
          Kopsumma
        </label>
        <div class="col-md-6">
          <input class="form-control" name="total" type="text" value="{{$order->price}}">
        </div>
        <div class="col-md-3 form-control-comment">
        </div>
      </div>

      <div class="form-group row">
        <label class="col-md-3 form-control-label text-left text-md-right">
          <h4>Pamatinformācija</h4>
        </label>
        <div class="col-md-6">

        </div>
        <div class="col-md-3 form-control-comment">
        </div>
      </div>

      <div class="form-group row">
        <label class="col-md-3 form-control-label text-left text-md-right">
          Vārds, uzvārds
        </label>
        <div class="col-md-6">
          <input class="form-control" name="name_suraname" type="text" value="{{$userData->name . ", " . $userData->surname}}">
        </div>
        <div class="col-md-3 form-control-comment">
        </div>
      </div>

      <div class="form-group row">
        <label class="col-md-3 form-control-label text-left text-md-right">
          E-pasts
        </label>
        <div class="col-md-6">
          <input class="form-control" name="email" type="email" value="{{$userData->email}}">
        </div>
        <div class="col-md-3 form-control-comment">
        </div>
      </div>

      <div class="form-group row">
        <label class="col-md-3 form-control-label text-left text-md-right">
          Tālrunis
        </label>
        <div class="col-md-6">
          <input class="form-control" name="phone_number" type="text" value="{{$userData->phone_number}}">
        </div>
        <div class="col-md-3 form-control-comment">
        </div>
      </div>

      <div class="form-group row">
        <label class="col-md-3 form-control-label text-left text-md-right">
          Piegādes adrese
        </label>
        <div class="col-md-6 col-sm">
          <input class="form-control" name="delivery_adress" type="text" value="@if (isset($userData->delivery_address)) {{ $userData->delivery_address }} @endif">
        </div>
        <div class="col-md-3 form-control-comment">
        </div>
      </div>
{{--      @php echo $order @endphp--}}

      @php
        if (property_exists($userData,'company_registration_number')){
          $hasCompanyData = true;
        } else {
          $hasCompanyData = false;
        }
      @endphp

{{--      @if ($hasCompanyData)--}}

        <div>

        <div class="form-group row">
          <label class="col-md-3 form-control-label text-left text-md-right">
            <h4>Uzņēmuma informācija</h4>
          </label>
          <div class="col-md-6">

          </div>
          <div class="col-md-3 form-control-comment">
          </div>
        </div>

        <div class="form-group row">
          <label class="col-md-3 form-control-label text-left text-md-right">
            Reģistrācijas Nr.
          </label>
          <div class="col-md-6">
            <input class="form-control"
                   name="company_registration_number"
                   type="text"
                   value="@if($hasCompanyData){{$userData->company_registration_number}}@endif"
            >
          </div>
          <div class="col-md-3 form-control-comment">
          </div>
        </div>

          <div class="form-group row">
          <label class="col-md-3 form-control-label text-left text-md-right">
            PVN numurs
          </label>
          <div class="col-md-6">
            <input class="form-control"
                   name="company_pvn_number"
                   type="text"
                   value="@if($hasCompanyData){{$userData->company_pvn_number}}@endif"
            >
          </div>
          <div class="col-md-3 form-control-comment">
          </div>
        </div>

        <div class="form-group row">
          <label class="col-md-3 form-control-label text-left text-md-right">
            Uzņēmuma nosaukums
          </label>
          <div class="col-md-6">
            <input class="form-control"
                   name="company_name"
                   type="text"
                   value="@if($hasCompanyData){{$userData->company_name}}@endif"
            >
          </div>
          <div class="col-md-3 form-control-comment">
          </div>
        </div>

        <div class="form-group row">
          <label class="col-md-3 form-control-label text-left text-md-right">
            Juridiskā adrese
          </label>
          <div class="col-md-6">
            <input class="form-control"
                   name="company_address"
                   type="text"
                   value="@if($hasCompanyData){{$userData->company_address}}@endif"
            >
          </div>
          <div class="col-md-3 form-control-comment">
          </div>
        </div>

        </div>

      <div class="form-group row">
        <label class="col-md-3 col-sm-12 form-control-label text-left text-md-right">
          <h4>Auto dati</h4>
        </label>
        <div class="col-md-6">

        </div>
        <div class="col-md-3 col-sm form-control-comment">
        </div>
      </div>

      <div class="form-group row">
        <label class="col-md-3 form-control-label text-left text-md-right">
          Auto brends
        </label>
        <div class="col-md-6">
          <input class="form-control" name="car_brand" type="text" value="{{$userData->car_brand}}">
        </div>
        <div class="col-md-3 form-control-comment">
        </div>
      </div>

      <div class="form-group row ">
        <label class="col-md-3 form-control-label text-left text-md-right">
          Auto modelis
        </label>
        <div class="col-md-6">
          <input class="form-control" name="car_model" type="text" value="{{$userData->car_model}}">
        </div>
        <div class="col-md-3 form-control-comment">
        </div>
      </div>

      <div class="form-group row ">
        <label class="col-md-3 form-control-label text-left text-md-right">
          Auto izlaiduma gads
        </label>
        <div class="col-md-6">
          <input class="form-control" name="car_release_year" type="text" value="{{$userData->car_release_year}}">
        </div>
        <div class="col-md-3 form-control-comment">
        </div>
      </div>

      <div class="form-group row ">
        <label class="col-md-3 form-control-label text-left text-md-right">
          Auto dzinēja izmērs
        </label>
        <div class="col-md-6">
          <input class="form-control" name="car_engine_size" type="text" value="{{$userData->car_engine_size}}">
        </div>
        <div class="col-md-3 form-control-comment">
        </div>
      </div>
      </form>

      <div class="form-group row">
        <label class="col-md-3 form-control-label text-left">
          <h4 class="float-md-right">Pasūtītās preces</h4>
        </label>
        <div class="col-md-6">

        </div>
        <div class="col-md-3 form-control-comment">
        </div>
      </div>
      <div class="form-group col-12 col-lg-6" style="margin: auto;">
        <table class="table admin-order-confirm-table table-light" >
          <thead class="table-dark">
          <tr>
            <th scope="col">ID</th>
            <th scope="col">Nosaukums</th>
            <th scope="col">Skaits</th>
            <th scope="col">Cena</th>
            <th scope="col">Kopā</th>
          </tr>
          </thead>
          <tbody>
          {{--@php dd($tires) @endphp--}}

          @foreach ($tires as $tire)
            <tr id="confirm-table">
              <th style="border-color: #c6c6c6;" scope="row">{{$tire->tire_id}}</th>
              <td style="border-color: #c6c6c6;">{{$tire->title}}</td>
              <td style="border-color: #c6c6c6;">{{$tire->quantity}}</td>
              <td style="border-color: #c6c6c6;">{{$tire->quantity}} x {{$tire->price}} &euro;</td>
              <td style="border-color: #c6c6c6;">@php echo ($tire->quantity * $tire->price) @endphp &euro;</td>
            </tr>
          @endforeach
	  @if ($order->fit_price != 0 || $order->delivery_price != 0)
          <tr id="confirm-table">
            <th style="border-color: #c6c6c6;" scope="row"></th>
	    @if ($order->fit_price != 0) <td style="border-color: #c6c6c6;" scope="row">Montāža</td> @endif
            @if ($order->delivery_price != 0) <td style="border-color: #c6c6c6;" scope="row">Piegāde</td> @endif
            <td style="border-color: #c6c6c6;" scope="row"></td>
            <td style="border-color: #c6c6c6;" scope="row"></td>
            <td style="border-color: #c6c6c6;">{{ ($order->fit_price) ? $order->fit_price : $order->delivery_price }} &euro;</td>
          </tr>
	  @endif
            <tr class="table-dark">
              <th style="border-color: #c6c6c6; text-align: right" colspan="4"></th>
              <th style="border-color: #c6c6c6;">{{$order->price + (($order->fit_price) ? $order->fit_price : $order->delivery_price) }} &euro;</th>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="form-group row">
        <div class="col-sm-3"></div>
        <div class="col-sm-6">
          <button type="submit" form="orderUpdate" class="btn btn-primary ml-1 float-right">Saglabāt</button>
          <a href="/admin/orders" class="btn btn-secondary float-right">Atgriezties</a>
        </div>
        <div class="col-sm-3"></div>
      </div>
    </div>
  @endsection

