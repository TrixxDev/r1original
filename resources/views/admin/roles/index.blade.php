@extends('admin.layouts.app')

@section('content')

  <div class="container-fluid">

    <div class="row">

      <div class="col-9">

        <div class="m-4 role-settings">
          <ul class="nav nav-tabs" id="myTab">
            @foreach ($roles as $role)
            <li class="nav-item">
              <a href="#{{ Str::slug($role->name) }}" class="nav-link @if ($loop->first) active @endif" data-bs-toggle="tab">{{ ucwords($role->name) }}</a>
            </li>
            @endforeach
          </ul>
          <div class="tab-content">
            @foreach ($roles as $role)
            <div class="tab-pane @if ($loop->first) show active @endif fade" id="{{ Str::slug($role->name) }}">
              <div class="header"></div>
              <div class="content"></div>
              <div class="footer"></div>
            </div>
            @endforeach
          </div>
        </div>

      </div>

    </div>

  </div>

@endsection
