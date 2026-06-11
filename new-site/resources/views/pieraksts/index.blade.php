@extends('layouts.app')

@section('body-title', 'main-schedule')
@section('title', 'lang-lv country-lv layout-both-columns page-pieraksts tax-display-enabled')
@section('meta_title', 'E-pieraksts | R1 Riepu Serviss')
@section('meta_description', 'Online pieraksts uz riepu montāžu, balansēšanu un auto apkalpošanu Rīgā un Ulbrokā. Izvēlies datumu un brīvu laiku — R1 Riepu Serviss.')
@section('canonical_url', route('pieraksts'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/schedule.css') }}">
@endpush

@php
    $dayTitles = [1 => 'Pirmdiena', 2 => 'Otrdiena', 3 => 'Trešdiena', 4 => 'Ceturtdiena', 5 => 'Piektdiena', 6 => 'Sestdiena', 7 => 'Svētdiena'];
    $slotWrapperClass = [
        'free' => 'time-free',
        'discount' => 'time-free discount',
        'moto' => 'time-free',
        'ac' => 'time-free',
        'taken' => 'taken-slot',
        'unavailable' => 'slot-gray',
        'reserved' => 'taken-slot',
    ];
@endphp

@section('content')
    <div class="container-fluid records">
        <div class="main-content clearfix col-md-12 col-xl-12">
            <div class="loading" style="display: none"></div>
            <div id="content-wrapper" class="right-column col-lg-12">
                <div class="schedule-table dashboard">

                    @foreach (['success', 'warning', 'danger'] as $flash)
                        @if (session($flash))
                            <div class="alert alert-{{ $flash === 'danger' ? 'danger' : ($flash === 'warning' ? 'warning' : 'success') }}">{!! session($flash) !!}</div>
                        @endif
                    @endforeach

                    @foreach ($days as $date => $dayQueues)
                        @continue(empty($dayQueues))
                        @php $dateTs = strtotime($date); @endphp
                        <h1 style="font-size: 1.7em; margin-top: 20px;">{{ $dayTitles[(int) date('N', $dateTs)] }}, {{ date('d.m.Y', $dateTs) }}</h1>
                        <div class="row">
                            @foreach ($offices as $office)
                                @php
                                    $officeQueues = array_values(array_filter($dayQueues, fn ($q) => $q['office_id'] === $office->id));
                                @endphp
                                @continue(empty($officeQueues))
                                <div class="col-md-{{ (int) floor(12 / max(1, count($offices))) }} grid grid-cols-{{ count($officeQueues) }}"
                                     @if ($office->id === 1) style="border-right: 2px solid black;" @endif
                                     data-date="{{ $date }}">
                                    @foreach ($officeQueues as $queue)
                                        <div class="table office_{{ $office->id }}" data-queue-id="{{ $queue['queue_id'] }}">
                                            <div class="title text-sm">{{ $office->title }}</div>
                                            @foreach ($queue['slots'] as $slot)
                                                <div class="time-status flex {{ $slotWrapperClass[$slot['kind']] }}" data-iorder="{{ $slot['position'] }}">
                                                    <div class="time-slot">{{ $slot['time'] }}</div>
                                                    @if (in_array($slot['kind'], ['free', 'discount', 'moto', 'ac'], true))
                                                        <button class="status free-slot-link available-slot @if ($slot['kind'] === 'discount') discount-slot @endif"
                                                                data-service-filter="{{ $slot['kind'] }}">{{ $slot['label'] }}</button>
                                                    @elseif ($slot['kind'] === 'taken')
                                                        <div class="slot taken-slot">{{ $slot['label'] }}</div>
                                                    @elseif ($slot['kind'] === 'reserved')
                                                        <div class="slot unavailable taken-slot">{{ $slot['label'] }}</div>
                                                    @else
                                                        <div class="slot unavailable taken-slot disabled-slot">{{ $slot['label'] }}</div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>

                <section id="mobile-main" style="display: none;"
                         data-reserve-url="{{ route('pieraksts.reserve') }}"
                         data-release-url="{{ route('pieraksts.release') }}"
                         data-extend-url="{{ route('pieraksts.extend') }}"
                         data-store-url="{{ route('pieraksts.store') }}">
                    <form method="POST" id="booking-form">
                        <input type="hidden" name="queue_id">
                        <input type="hidden" name="date">
                        <input type="hidden" name="position">
                        <input type="hidden" name="slot_id">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="mobile-modalTitle">Pieraksts</h5>
                                    <span id="reservation-countdown" style="font-weight: bold;"></span>
                                </div>
                                <div class="modal-body mobile-reservation-modal-body">
                                    <div class="container-fluid">
                                        <div class="col-md-12 mobile-body">
                                            <div id="booking-errors" class="alert alert-danger" style="display: none;"></div>

                                            <div id="mobile-reservation-form" tabindex="1">
                                                <div class="form-group purpose">
                                                    <label for="serviceOption"><span class="validate">*</span>Es vēlos:</label>
                                                    <div id="mobile-service">
                                                        <select class="custom-select" name="service_id" id="serviceOption" required="required">
                                                            @foreach ($services as $service)
                                                                <option value="{{ $service->id }}"
                                                                        @if ($service->allows_storage) data-save="1" @endif
                                                                        @if ($service->allows_car) data-ac="1" @endif
                                                                        @if ($service->allows_moto) data-moto="1" @endif>{{ $service->title }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group rims-with-mobile" style="display: none;">
                                                    <div class="form-check">
                                                        <label class="form-check-label" for="rimsWith1">
                                                            <img loading="lazy" src="{{ asset('images/bez_diskiem.jpg') }}" alt="">
                                                        </label>
                                                        <br>
                                                        <input value="1" class="form-check-input" type="radio" name="rims_with" id="rimsWith1" title="">
                                                        Līdzi būs riepas bez diskiem
                                                    </div>
                                                    <div class="form-check">
                                                        <label class="form-check-label" for="rimsWith2">
                                                            <img loading="lazy" src="{{ asset('images/ar_diskiem.png') }}" alt="">
                                                        </label>
                                                        <br>
                                                        <input value="2" class="form-check-input" type="radio" name="rims_with" id="rimsWith2" title="">
                                                        Līdzi būs riepas ar diskiem
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label for="mobile-reg_nr"><span class="validate">*</span>Reģistrācijas numurs:</label>
                                                    <input type="text" class="form-control" id="mobile-reg_nr" name="license_plate" title="">
                                                </div>
                                                <div class="form-group">
                                                    <label for="mobile-brand"><span class="validate">*</span>Auto marka:</label>
                                                    <input id="mobile-brand" type="text" class="form-control" name="car_brand" title="">
                                                </div>
                                                <div class="form-group">
                                                    <label for="mobile-model"><span class="validate">*</span>Auto modelis:</label>
                                                    <input id="mobile-model" type="text" class="form-control" name="car_model" title="">
                                                </div>
                                                <div class="form-group">
                                                    <label for="mobile-comment">Piezīmes</label>
                                                    <textarea id="mobile-comment" cols="40" rows="4" class="form-control" name="customer_comment"></textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label for="mobile-name">Mans vārds:</label>
                                                    <input id="mobile-name" type="text" class="form-control" name="customer_name" title="">
                                                </div>
                                                <div class="form-group phone-number">
                                                    <label for="mobile-phone"><span class="validate">*</span>Mans tālruņa numurs:</label>
                                                    <input type="text" class="form-control" id="mobile-phone" name="phone_number" title="" inputmode="numeric">
                                                </div>
                                                <div class="form-group client-email last">
                                                    <label for="mobile-email">Mans e-pasts:</label>
                                                    <input type="email" class="form-control" id="mobile-email" name="email" title="">
                                                </div>
                                                <div class="modal-footer reservation-modal-footer">
                                                    <button type="submit" class="btn btn-primary" id="mobile-submit-reservation">Pierakstīties</button>
                                                    <button type="button" class="btn btn-primary" id="mobile-close-modal">Atgriezties</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mobile-body-success" style="display: none;">
                                            <div class="alert alert-success"></div>
                                            <a href="{{ route('pieraksts') }}" class="btn btn-success">Atgriezties</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </div>
@endsection
