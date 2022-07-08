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
            <form class="form-horizontal alloy_rims" method="post">
              @csrf
              <input type="hidden" name="service_id">
              <div class="card-header">Jauni lietie diski <span style="float: right;"><a class="btn btn-primary" href="#">Izveidot</a></span></div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-12">
                    @foreach ($rims as $rim)
                    @if ($current_r != $rim->d3)
                      @if ($current_r >= 0) {!! '</table>' !!} @endif
                      <table class="table table-striped table-bordered table-hover">
                        <th class="" colspan="5"><h1>R{{ $rim->d3 }}</h1></th>
                        <tr>
                        <th scope="col">Izmērs</th>
                        <th scope="col">Brends</th>
                        <th scope="col">Marka</th>
                        <th scope="col">Veikala cena</th>
                        <th scope="col">Piezīmes</th>
                        </tr>
                      @php
                      $current_r = $rim->d3;
                      @endphp
                      @endif

                      <tr class="link" href="{{ route('admin.rims.edit', $rim->rim_id) }}">
                        <td>{{ $rim->d1 }} R{{ $rim->d3 }}</td>
                        <td>{{ $rim->brand_title }}</td>
                        <td>{{ $rim->make_title }}</td>
                        <td><span style="color: red;">{{ $rim->price2 }}</span></td>
                        <td><span style="color: red;">{{ $rim->comment }}</span></td>
                      </tr>
                      @endforeach
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
