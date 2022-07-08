@extends('admin.layouts.app')

@section('content')

    <div class="container-fluid">
        <div class="fade-in">

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <form class="form-horizontal services_form" method="post">
                            @csrf
                            <input type="hidden" name="service_id">
                            <div class="card-header">Pakalpojumi</div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <input class="form-control" type="text" name="service" placeholder="Pakalpojums">
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button class="btn btn-sm btn-primary service_add_button" type="submit"> Izveidot</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <ul class="list-group bg-white services-list">
                        @if ($services->isEmpty())
                            <li class="list-group-item d-flex justify-content-center align-items-center no-services"><b>Nav neviena pakalpojuma</b></li>
                        @else
                            @foreach ($services as $service)
                            <li id="service_{{ $service->service_id }}" class="services list-group-item d-flex justify-content-between align-items-center">
                                <span class="service_title">{{ $service->title }}</span>
                                <div class="options">
                                    <a href="{{ route('admin.settings.services.edit', $service->service_id) }}" class="edit badge bg-primary rounded-pill service-edit">Labot</a>
                                    <a href="{{ route('admin.settings.services.destroy', $service->service_id) }}" class="destroy badge bg-primary rounded-pill service-delete">Dzēst</a>
                                </div>
                            </li>
                            @endforeach
                        @endif
                    </ul>

                </div>
            </div>
        </div>
    </div>

@endsection
