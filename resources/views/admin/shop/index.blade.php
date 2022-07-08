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
                        <th scope="col">Piezīmes</th>
                      </tr>
                      </thead>
                      <tbody>
                        @foreach ($orders as $order)
                        @php
                          $item_count = [];
                          $items = unserialize($order->info);
                          foreach ($items as $item) {
                              array_push($item_count, $item['quantity']);
                          }
                          $item_count = array_sum($item_count);

                          $enum = [
                            1 => 'Pildās',
                            2 => 'Apstrādājās',
                            3 => 'Gatavs'
                          ];

                        @endphp
                        <tr>
                          <td>{{ $order->created_at }}</td>
                          <td>{{ $item_count }}</td>
                          <td>{{ $order->price }} €</td>
                          <td>{{ $enum[$order->status] }}</td>
                          <td>#</td>
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
