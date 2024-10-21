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
            <div class="card-header">Pasūtījumi
              <span style="float: right;">
                <form method="post" class="form-inline" action="{{ route('admin.orders_print') }}">
                  @csrf
                  <label for="orders_from">No: </label>
{{--                  <input class="form-control" style="margin: 0 10px" type="date" id="orders_from" name="orders_from">--}}
                  <input class="date form-control" name="orders_from" style="margin: 0 10px" id="orders_from" type="text" autocomplete="off">
                  <label for="order_to">Līdz: </label>
{{--                  <input class="form-control" style="margin: 0 10px" type="date" id="orders_to" name="orders_to">--}}
                  <input class="date form-control" name="orders_to" style="margin: 0 10px" id="orders_to" type="text" autocomplete="off">
                  <button name="print" class="btn btn-success" type="submit" style="margin: 0 10px">Printēt</button>
                  <a class="btn btn-primary" href="#">Izveidot</a>
                </form>
              </span>
            </div>
            <form class="form-horizontal services_form" id="ordersForm" method="get">
              <input type="hidden" disabled name="csrf_token" value="{{ csrf_token() }}">
              <input type="hidden" disabled name="service_id">
              <div class="card-body">
                <div class="row">
                  <div class="col-md-12">
                    <table class="table table-striped table-bordered">
                        <thead>
                      <tr>
                        <th scope="col">Datums</th>
                        <th scope="col">Pas. Nr.</th>
                        <th scope="col">Preces</th>
                        <th scope="col">Summa</th>
                        <th scope="col">
                          Status
                          <select name="admin-order-status-select" id="admin-order-status-select" class="custom-select">
                            <option @if (empty($filteredStatus)) selected @endif value="">Visi</option>
                            @foreach($status_enum as $id => $title)
                              <option @if ($filteredStatus == $id) selected @endif value="{{ $id }}">{{$title}}</option>
                            @endforeach
                          </select>
                        </th>
                        <th scope="col">Apmaksas veids</th>
                        <th scope="col">
                          Pēdējais labojums
                          <select name="admin-order-editor-select" id="admin-order-editor-select" class="custom-select">
                            <option @if (empty($filteredEditor)) selected @endif value="">Visi</option>
                            @foreach(App\Models\User::orderBy('name', 'asc')->get() as $user)
                              <option @if ($filteredEditor == $user->id) selected @endif value="{{ $user->id }}">{{ $user->fullName }}</option>
                            @endforeach
                          </select>
                        </th>
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
                                }
                            }
                          $item_count = array_sum($item_count);
                          $item_sum = array_sum($item_sum);
                          if ($order->used_promo != 0) {
                              $promo = \App\Models\Promo::where('promo_id', $order->used_promo)->first();
                              if ($promo->status === '1') {
                                $item_sum = $item_sum * (1 - $promo->value / 100);
                              } else {
                                $item_sum = $item_sum - $promo->value;
                              }
                              $item_sum = round($item_sum);
                          }
                          if ($order->delivery_price > 0) {
                            $item_sum = $item_sum + (int) substr($order->delivery_price, 0, -2);
                          } else if ($order->fit_price > 0) {
                            $item_sum = $item_sum + (int) substr($order->fit_price, 0, -2);
                          }
                        @endphp
                          <tr>
                            @if (isset($items['name']) || isset($items['surname']))
                              <td>{{ $order->created_at . ' - ' . $items['name'] . ', ' . $items['surname'] . ' (' . $items['phone_number'] . ')'}}</td>
                            @else
                              <td>{{ $order->created_at }}</td>
                            @endif
                            <td>{{ $order->id }}</td>
                            <td>{{ $item_count }}</td>
                            <td>{{ $item_sum }} €</td>
                            <td>
                                @if (!is_null($order->admin_info))
                                    <span style="font-weight: bold;" class="tippy" data-tippy-content="{!! $order->admin_info !!}">{{ $status_enum[$order->status] }}</span>
                                @else
                                    <span>{{ $status_enum[$order->status] }}</span>
                                @endif
                            </td>
                            <td>{{ $pay_enum[$order->payment] }}</td>
                            <td>
                              @if (\App\Models\User::find($order->edituser))
                                {{ \App\Models\User::find($order->edituser)->fullName . ' ' . $order->updated_at }}
                              @else
                                Neviens nav veicis labojumus
                              @endif
                            </td>
                            <td style="width: 153px;">

                              <a href="{{ route('admin.order', $order->id) }}" class="btn btn-warning">
                                <i class="fa-solid fa-pencil" style="color:#fff;"></i>
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
                <div style="margin-left: -15px">{{ $orders->links() }}</div>
              </div>
            </form>
          </div>

        </div>
      </div>
    </div>
  </div>

@endsection
