@extends('admin.layouts.app')

@section('content')

  <div class="container-fluid">
    <div class="fade-in">
      @if (session('success'))
        <div class="alert alert-success">
          {{ session('success') }}
        </div>
      @endif
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <form class="form-horizontal services_form" method="post">
              @csrf
              <input type="hidden" name="service_id">
              <div class="card-header">Administratori <span style="float: right;"><a class="btn btn-primary" href="{{ route('admin.settings.users.create') }}">Izveidot</a></span></div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-12">
                    <table class="table table-striped table-bordered">
                      <thead>
                      <tr>
                        <th scope="col">Vārds</th>
                        <th scope="col">Uzvārds</th>
                        <th scope="col">Lietotājvārds</th>
                        <th scope="col">Grupa</th>
                        <th scope="col">Darbības</th>
                      </tr>
                      </thead>
                      <tbody>
                      @foreach ($users as $user)
                        <tr>
                          <td>{{ ucfirst($user->name) }}</td>
                          <td>{{ ucfirst($user->surname) }}</td>
                          <td>{{ $user->username }}</td>
                          <td>
                            {{ ucfirst(implode(', ', array_map("ucfirst", $user->getRoleNames()->toArray()))) }}
                          </td>
                          <td>
                            <a class="btn btn-success" href="{{ route('admin.settings.users.edit', $user->id) }}">
                              <svg class="c-icon">
                                <use xlink:href="/node_modules/@coreui/icons/sprites/free.svg#cil-description"></use>
                              </svg>
                            </a>
                            @if ($user->id !== Auth::user()->id)
                            <a class="btn btn-danger" href="{{ route('admin.settings.users.destroy', $user->id) }}">
                              <svg class="c-icon">
                                <use xlink:href="/node_modules/@coreui/icons/sprites/free.svg#cil-trash"></use>
                              </svg>
                            </a>
                            @endif
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
