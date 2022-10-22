@extends('admin.layouts.app')

@section('content')

  <div class="container-fluid">
    <div class="fade-in">
      @if (session('success'))
        <div class="alert alert-success">
          {{ session('success') }}
        </div>
      @endif
      @if (session('error'))
        <div class="alert alert-danger">
          {{ session('error') }}
        </div>
      @endif
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <form class="form-horizontal services_form" method="post">
              @csrf
              <input type="hidden" name="service_id">
              <div class="card-header">Pasūtījumi <span style="float: right;"><a class="btn btn-primary" href="#">Izveidot</a></span></div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-12">
                    <table class="table table-striped table-bordered">
                      <thead>
                      <tr>
                        <th scope="col">Datums</th>
                        <th scope="col">Preces</th>
                        <th scope="col">Summa</th>
                        <th scope="col">Status</th>
                        <th scope="col">Apmaksas veids</th>
                        <th scope="col"></th>
                      </tr>
                      </thead>
                      <tbody>
                        @foreach ($orders as $order)
                        @php
			  $item_count = [];
                          $item_sum = [];
                          @$items = unserialize($order->info);
                          //unset($items['data']);
			  if (isset($items['items'])) {
                          foreach ($items['items'] as $item) {
                            if (!isset($item['quantity'])) continue;
                            array_push($item_count, $item['quantity']);
                            array_push($item_sum, ($item['price'] * $item['quantity']));
                          }}
                          $item_count = array_sum($item_count);
                          $item_sum = array_sum($item_sum);
			  if ($order->delivery_price > 0) {
			    $item_sum = $item_sum + (int) substr($order->delivery_price, 0, -2);
			  } else if ($order->fit_price > 0) {
			    $item_sum = $item_sum + (int) substr($order->fit_price, 0, -2);
			  }

                          $status_enum = [
                            1 => 'Nav pabeigts/Nav informācijas',
                            2 => 'Jauns',
                            3 => 'Gaidām apmaksu',
                            4 => 'Gaidām preci',
                            5 => 'Pabeigts',
                          ];

			  $pay_enum = [
                            0 => '',
			    1 => 'Apmaksa saņemšanas brīdī',
                            2 => 'Bankas pārskaitījums',
                            3 => 'Tiešsaistes apmaksa',
                          ];

                        @endphp
                          <tr>
                            @if (isset($items['name']) || isset($items['surname']))
			      <td>{{ $order->created_at . ' - ' . $items['name'] . ', ' . $items['surname'] }}</td>
                            @else
			      <td>{{ $order->created_at }}</td>
			    @endif
			    <td>{{ $item_count }}</td>
                            <td>{{ $item_sum }} €</td>
                            <td>{{ $status_enum[$order->status] }}</td>
                            <td>{{ $pay_enum[$order->payment] }}</td>
                            <td style="width: 153px;">

                              <a href="{{ route('admin.order', $order->id) }}" class="btn btn-warning">
                                <i class="fa-solid fa-pencil" style="color:#fff;"></i>
                              </a>


                              <a href="{{ route('admin.order', $order->id) }}" class="btn btn-success disabled">
                                <i class="fa-solid fa-circle-check" style="color:#fff;"></i>
                              </a>

                              <a onclick="return confirm('Tiešām vēlies dzēst?')" href="{{ route('admin.order.delete', $order->id) }}" class="btn btn-danger">
                                <i class="fa-solid fa-trash" style="color:#fff;"></i>
                              </button>

                            </td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </form>
          </div>

        </div>
      </div>
    </div>
  </div>

@endsection
