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
      @if (session('success'))
        <div class="alert alert-success">
          {{ session('success') }}
        </div>
      @endif
      @if (session('danger'))
        <div class="alert alert-danger">
          {{ session('danger') }}
        </div>
      @endif
      <div class="form-group row">
          <div class="col-sm-3"></div>
          <div class="col-sm-6">
              <button type="submit" form="orderUpdate" class="btn btn-primary ml-1 float-right">Saglabāt</button>
              <a href="{{ route('admin.orders') }}" class="btn btn-secondary float-right">Atgriezties</a>
          </div>
          <div class="col-sm-3"></div>
      </div>
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
              Komentāri
          </label>
          <div class="col-md-6 col-sm">
              <textarea name="admin_info" class="form-control" cols="30" rows="5">@if (!empty($order->admin_info)){{ $order->admin_info }}@endif</textarea>
          </div>
      </div>

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
          <input class="form-control" name="order_date" type="text" disabled value="{{$order->created_at}}">
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
            @foreach ($status_enum as $status_id => $status_name)
              @if ($loop->first) @continue @endif
              <option value="{{ $status_id }}" @if ($order->status == $status_id) selected="" @endif>{{ $status_name }}</option>
            @endforeach
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
          <input class="form-control" name="total" type="text" disabled value="{{$order->price + (($order->fit_price) ? substr($order->fit_price, 0, -2) : substr($order->delivery_price, 0, -2)) }} &euro; - {{ $pay_enum[$order->payment] }}">
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
          <input class="form-control" name="name_suraname" type="text" value="{{$userData->name . " " . $userData->surname}}">
        </div>
        <div class="col-md-3 form-control-comment">
        </div>
      </div>

      <div class="form-group row">
        <label class="col-md-3 form-control-label text-left text-md-right">
          E-pasts
        </label>
        <div class="col-md-6">
          <a href="mailto:{{$userData->email}}" class="form-control" style="color: #321fdb">{{$userData->email}}</a>
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
          Saņemšanas vieta
        </label>
	<div class="col-md-6 col-sm">
	  <select id="select" name="delivery_address" class="custom-select">
	    @foreach ($offices as $office)
            <option value="{{ $office->office_id }}" @if (isset($userData->fitting_address) && $userData->fitting_address == $office->office_id) selected="" @endif>{{ $office->shipping }}</option>
	    @endforeach
	    @if (isset($userData->shipping_city))
        @switch($userData->shipping_city)
          @case(1)
          @case(2)
          @case(3)
            <option value="3" selected="">Piegāde</option>
          @break
          @default
            <option value="3">Piegāde</option>
        @endswitch
	    @else
	    	<option value="3">Piegāde</option>
	    @endif
	  </select>
<!--          <input class="form-control" name="delivery_adress" type="text" value="">-->
        </div>

        <div class="col-md-3 form-control-comment">
        </div>

      </div>
      <div class="form-group row" style="display: none;">
       <label class="col-md-3 form-control-label text-left text-md-right">
            Piegādes adrese
       </label>
       <div class="col-md-2 col-sm">
        <select id="select" class="custom-select" name="shipping_city">
          <option value="1" @if (isset($userData->shipping_city) && $userData->shipping_city == 1) selected="" @endif>Rīga</option>
          <option value="3" @if (isset($userData->shipping_city) && $userData->shipping_city == 3) selected="" @endif>Cits</option>
        </select>
       </div>
       <div class="col-md-4">
        <input class="form-control" name="shipping_address" type="text" @if (isset($userData->shipping_address)) value="{{ $userData->shipping_address }}" @endif>
       </div>
      </div>

      <div class="form-group row">
        <label class="col-md-3 form-control-label text-left text-md-right">
          Piezīmes
        </label>
        <div class="col-md-6 col-sm">
          <textarea name="notes" class="form-control" cols="30" rows="5">@if (!empty($userData->notes)){{ $userData->notes }}@endif</textarea>
        </div>
      </div>


{{--      @php var_dump($userData); @endphp--}}

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
      <div class="form-group col-10" style="margin: auto;">
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
              @php
              if (isset($tire->article)) {
                $tireObj = App\Models\Autotire::where('article', $tire->article)->first();
                if (!$tireObj) $tireObj = App\Models\Moto::where('article', $tire->article)->first();
                if (!$tireObj) $tireObj = App\Models\Quadr::where('article', $tire->article)->first();
              }
              @endphp
              <tr id="confirm-table">
                <th style="border-color: #c6c6c6;" scope="row">{{$tire->tire_id}}</th>
                @if (isset($tireObj))
                  <td style="border-color: #c6c6c6;"><a target="_blank" href="{{ $tireObj->link }}">{!! $tireObj->fullName!!}</a></td>
                @else
                  <td style="border-color: #c6c6c6;">{!! $tire->title!!}</td>
                @endif
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
              <td style="border-color: #c6c6c6;">{{ ($order->fit_price) ? substr($order->fit_price, 0, -2) : substr($order->delivery_price, 0, -2) }} &euro;</td>
            </tr>
	          @endif
            <tr class="table-dark">
              <th style="border-color: #c6c6c6; text-align: right" colspan="4"></th>
              <th style="border-color: #c6c6c6;">{{$order->price + (($order->fit_price) ? substr($order->fit_price, 0, -2) : substr($order->delivery_price, 0, -2)) }} &euro;</th>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="form-group row">
        <div class="col-sm-3"></div>
        <div class="col-sm-6">
          <button type="submit" form="orderUpdate" class="btn btn-primary ml-1 float-right">Saglabāt</button>
          <a href="{{ route('admin.orders') }}" class="btn btn-secondary float-right">Atgriezties</a>
        </div>
        <div class="col-sm-3"></div>
      </div>
    </div>
  @endsection

