@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{asset('css/schedule.css?rev=' . time())}}">
    <div class="container-fluid records">
        <div class="">
            <div class="main-content clearfix col-md-12 col-xl-12">
                <div class="loading"></div>
                <div id="content-wrapper" class="right-column col-lg-12">
                  <div class="schedule-table">
                    @include('components.calendar')
                    @php
                      $iteration = 0;
                      $timeToClose = \Carbon\Carbon::create(date('Y'), date('m'), date('d'), 8, 45);
                      $now = \Carbon\Carbon::now();
                    @endphp
                      @for ($day = 0; $day < $visibleDays; $day++)

                        @php
                          $date = $workingDays[$day];
                          $dayOfWeek = $_weekDays[date('N', strtotime($date.' 00:00:00'))];
                          $dateFmt = date('d.m.Y', strtotime($date.' 00:00:00'));
                          $today = date('Y-m-d');

                          $openTime = 0;
                          $closeTime = -1;
                        @endphp


                        @if ($iteration == 0)
                          <h1>{{ $dayOfWeek . ", " . $dateFmt }}</h1>
                          @if (session('success'))
                            <div class="alert alert-success" style="border-color: #75bd59;">{!! session('success') !!}</div>
                          @endif
                          @if (session('danger'))
                            <div class="alert alert-danger" style="border-color: #ee6868;">{!! session('danger') !!}</div>
                          @endif
                          @if (session('warning'))
                            <div class="alert alert-warning">{!! session('warning') !!}</div>
                          @endif
                        @else
                          <h1>{{ $dayOfWeek . ", " . $dateFmt  }}</h1>
                        @endif
                        <div class="row">
                          @foreach ($offices as $office)
                            @php

                              $openTime = 0;
                              $closeTime = -1;

                              $office->_openQueues = 0;
                              foreach ($office->_queues as $queue){
                                  if ($queue->isVisible($date)) $office->_openQueues++;
                              }

                              if ($openTime==0){
                                  $openTime = $office->getOpenTime($date);
                              } else {
                                  $t = $office->getOpenTime($date);
                                  if ($t>0){
                                      $openTime = min($openTime, $t);
                                  }
                              }
                              $closeTime = max($closeTime, $office->getCloseTime($date));

                            @endphp
                            @if (count($office->_queues) >= 3)
                              <div class="col-md-7">
                                @else
                                  <div class="col-md-5">
                                    @endif
                                    <table class="table table-@if($office->office_id === 1){{'ulbroka'}}@else{{'kalnciema'}}@endif">
                                      <thead>
                                      <tr>
                                        <th scope="col" colspan="{{ count($office->_queues) * 2 }}" class="text-center">{{ $office['title'] }}</th>
                                      </tr>
                                      </thead>
                                      <tbody>
                                      @php
                                        @endphp
                                      @if (($openTime==0)&&($closeTime==0))
                                        <tr>
                                          @foreach ($office->_queues as $queue)
                                            <td class="closed-slot">Slēgts</td>
                                          @endforeach
                                        </tr>
                                      @else
                                        @for ($i=$openTime;$i<$closeTime;$i+=$timeStep)
                                          <tr>
                                            @foreach ($office->_queues as $queue)
                                              @php $slotNumber = $queue->getSlotNumberByInterval($date,$i); @endphp
                                              @if (($slotNumber!==false)&&($queue->_workingDays[$date]->isVisible()))
                                                @if ($queue->isIntervalBeginning($date,$i))
                                                  @php $slot = $queue->_slots[$date][$slotNumber]; @endphp

                                                  @switch ($slot->status)
                                                    @case (SLOT_STATUS_FREE)
                                                    @if ($date == $today)
                                                      @if (\Carbon\Carbon::parse(App\Models\Office::timeByInterval($i))->subHour() >= \Carbon\Carbon::now())
                                                        @if (trim($slot->comment)=='')
                                                          @php
                                                            if ($queue->_workingDays[$date]->isHalf()) {
                                                              $service = \App\Models\Service::where('f_ac', 1)->first();
                                                              if (!is_null($service)) {
                                                                $slotClass = 'available-slot';
                                                                $slotCaption = '<button class="free-slot-link" id="slot' . $slotNumber . '-' . $slot->queue_id . '" data-col="' . $slot->queue_id . '" data-iorder="' . $slotNumber . '" data-date="' . $slot->date . '" data-part="a" data-service="ac" data-toggle="modal" data-target="#reservation">Kondicioniera apkope</button>';
                                                              } else {
                                                                $slotClass = 'available-slot';
                                                                $slotCaption = '<button class="free-slot-link" id="slot' . $slotNumber . '-' . $slot->queue_id . '" data-col="' . $slot->queue_id . '" data-iorder="' . $slotNumber . '" data-date="' . $slot->date . '" data-toggle="modal" data-target="#reservation">Brīvs</button>';
                                                              }
                                                            } else {
                                                              $slotClass = 'available-slot';
                                                              $slotCaption = '<button class="free-slot-link" id="slot' . $slotNumber . '-' . $slot->queue_id . '" data-col="' . $slot->queue_id . '" data-iorder="' . $slotNumber . '" data-date="' . $slot->date . '" data-toggle="modal" data-target="#reservation">Brīvs</button>';
                                                            }
                                                          @endphp
                                                        @else
                                                          @php
                                                            $slotClass = 'slot-offer';
                                                            $slotCaption = '<button class="offer-slot-link" id="slot' . $slotNumber . '-' . $slot->queue_id . '" data-col="' . $slot->queue_id . '" data-iorder="' . $slotNumber . '" data-date="' . $slot->date . '" data-toggle="modal" data-target="#reservation">' . $slot->comment . '</button>';
                                                            //$slotCaption = $slot->comment;
                                                          @endphp
                                                        @endif
                                                      @else
                                                        @php
                                                          $slotClass = 'slot-gray';
                                                          $slotCaption = 'Aizņemts';
                                                        @endphp
                                                      @endif
                                                    @else
                                                      @if (trim($slot->comment)=='')
                                                        @php
                                                          if ($queue->_workingDays[$date]->isHalf()) {
                                                            $service = \App\Models\Service::where('f_ac', 1)->first();
                                                            if (!is_null($service)) {
                                                              $slotClass = 'available-slot';
                                                              $slotCaption = '<button class="free-slot-link" id="slot' . $slotNumber . '-' . $slot->queue_id . '" data-col="' . $slot->queue_id . '" data-iorder="' . $slotNumber . '" data-date="' . $slot->date . '" data-part="a" data-service="ac" data-toggle="modal" data-target="#reservation">Kondicioniera apkope</button>';
                                                            } else {
                                                              $slotClass = 'available-slot';
                                                              $slotCaption = '<button class="free-slot-link" id="slot' . $slotNumber . '-' . $slot->queue_id . '" data-col="' . $slot->queue_id . '" data-iorder="' . $slotNumber . '" data-date="' . $slot->date . '" data-toggle="modal" data-target="#reservation">Brīvs</button>';
                                                            }
                                                          } else {
                                                            $slotClass = 'available-slot';
                                                            $slotCaption = '<button class="free-slot-link" id="slot' . $slotNumber . '-' . $slot->queue_id . '" data-col="' . $slot->queue_id . '" data-iorder="' . $slotNumber . '" data-date="' . $slot->date . '" data-toggle="modal" data-target="#reservation">Brīvs</button>';
                                                          }
                                                        @endphp
                                                      @else
                                                        @php
                                                          $slotClass = 'slot-offer';
                                                          $slotCaption = '<button class="offer-slot-link" id="slot' . $slotNumber . '-' . $slot->queue_id . '" data-col="' . $slot->queue_id . '" data-iorder="' . $slotNumber . '" data-date="' . $slot->date . '" data-toggle="modal" data-target="#reservation">' . $slot->comment . '</button>';
                                                          //$slotCaption = $slot->comment;
                                                        @endphp
                                                      @endif
                                                    @endif
                                                    @php
                                                      //'. url_self_reference(array('d'=>$date,'qu'=>$queue->id,'time'=>$slot->iorder)).'
                                                      $slotText = $slotCaption;
                                                    @endphp
                                                    @break;

                                                    @case (SLOT_STATUS_TAKEN)
                                                      @if ($date == $today)
                                                        @if (\Carbon\Carbon::parse(App\Models\Office::timeByInterval($i))->subHour() >= \Carbon\Carbon::now())
                                                          @php
                                                          $slotClass = 'taken-slot';
                                                          @endphp
                                                        @else
                                                          @php
                                                            $slotClass = 'slot-gray';
                                                          @endphp
                                                        @endif
                                                      @else
                                                        @php
                                                          $slotClass = 'taken-slot';
                                                        @endphp
                                                      @endif
                                                      @php
                                                      $takenBy = json_decode($slot->takenby);
                                                      $service = \App\Models\Service::where('service_id', $takenBy->purpose)->first();
                                                      $ac = (isset($service->f_ac) && $service->f_ac != 0) ? '*' : '';
                                                      $plate = substr($takenBy->ownerPhone,-3,3);
                                                      $plate = filter_var($plate, FILTER_SANITIZE_NUMBER_INT);
                                                      $plate = trim($plate,' -.');

                                                      $slotText = ''. \App\Helper\Tires::truncateCharacters(trim($takenBy->vehicleMake),8,'&mldr;',1) . $ac . ' xxxxx'.$plate.'';
                                                      @endphp
                                                    @break

                                                    @case (SLOT_STATUS_OFFER)
                                                    @if ($date == $today)
                                                      @if (\Carbon\Carbon::parse(App\Models\Office::timeByInterval($i))->subHour() >= \Carbon\Carbon::now())
                                                        @php
                                                          $slotClass = 'slot-offer';
                                                          //'. url_self_reference(array('d'=>$date,'qu'=>$queue->id,'time'=>$slot->iorder)).'
                                                          $slotText = '<button class="offer-slot-link" id="slot' . $slotNumber . '-' . $slot->queue_id . '" data-col="' . $slot->queue_id . '" data-iorder="' . $slotNumber . '" data-date="' . $slot->date . '" data-toggle="modal" data-target="#reservation">' . $slot->comment . '</button>';
                                                        @endphp
                                                      @else
                                                        @php
                                                          $slotClass = 'slot-gray';
                                                          $slotCaption = 'Aizņemts';
                                                        @endphp
                                                      @endif
                                                    @else
                                                      @php
                                                        $slotClass = 'slot-offer';
                                                        //'. url_self_reference(array('d'=>$date,'qu'=>$queue->id,'time'=>$slot->iorder)).'
                                                        $slotText = '<button class="offer-slot-link" id="slot' . $slotNumber . '-' . $slot->queue_id . '" data-col="' . $slot->queue_id . '" data-iorder="' . $slotNumber . '" data-date="' . $slot->date . '" data-toggle="modal" data-target="#reservation">' . $slot->comment . '</button>';
                                                      @endphp
                                                    @endif
                                                    @break

                                                    @case (SLOT_STATUS_CLOSED)
                                                    @if (trim($slot->comment)=='')
                                                      @php $slotCaption = 'Slēgts'; @endphp
                                                    @else
                                                      @php $slotCaption = $slot->comment; @endphp
                                                    @endif
                                                    @php
                                                      $slotClass = 'closed-slot';
                                                      $slotText = $slotCaption;
                                                    @endphp
                                                    @break
                                                  @endswitch

                                                  <td class="time-slot">{{ App\Models\Office::timeByInterval($i) }}</td>
                                                  <td class="{{ $slotClass }} slot{{ $slotNumber }}-{{ $slot->queue_id }} slot" data-date="{{ $date }}" data-queue="{{ $slot->queue_id }}">
                                                    {!! $slotText !!}
                                                  </td>

                                                @else
                                                  @if ($queue->_workingDays[$date]->secondaryAvailable)

                                                    @php $slot = $queue->_slots[$date][$slotNumber]; @endphp

                                                    @switch ($slot->status2)
                                                      @case (SLOT_STATUS_OFFER)
                                                      @case (SLOT_STATUS_FREE)
                                                      @if ($date == $today)
                                                        @if (\Carbon\Carbon::parse(App\Models\Office::timeByInterval($i))->subHour() >= \Carbon\Carbon::now())
                                                          @php
                                                            $service = \App\Models\Service::where('f_moto', 1)->first();
                                                            if (!is_null($service)) {
                                                              $slotClass = 'available-slot';
                                                              $slotText = '<button class="free-slot-link" id="slot' . $slotNumber . '-' . $slot->queue_id . '" data-col="' . $slot->queue_id . '" data-iorder="' . $slotNumber . '" data-date="' . $slot->date . '" data-part="b" data-service="moto" data-toggle="modal" data-target="#reservation">' . $service->title . '</button>';
                                                            } else {
                                                              $slotClass = 'available-slot unavailable';
                                                              $slotText = '----------';
                                                            }
                                                          @endphp
                                                        @else
                                                          @php
                                                            $slotClass = 'slot-gray';
                                                            $slotText = 'Aizņemts';
                                                          @endphp
                                                        @endif
                                                      @else
                                                        @php
                                                          $service = \App\Models\Service::where('f_moto', 1)->first();
                                                          if (!is_null($service)) {
                                                            $slotClass = 'available-slot';
                                                            $slotText = '<button class="free-slot-link" id="slot' . $slotNumber . '-' . $slot->queue_id . '" data-col="' . $slot->queue_id . '" data-iorder="' . $slotNumber . '" data-date="' . $slot->date . '" data-part="b" data-service="moto" data-toggle="modal" data-target="#reservation">' . $service->title . '</button>';
                                                          } else {
                                                            $slotClass = 'available-slot unavailable';
                                                            $slotText = '----------';
                                                          }
                                                        @endphp
                                                      @endif
                                                      @break

                                                      @case (SLOT_STATUS_CLOSED)
                                                      @if (trim($slot->comment)=='')
                                                        @php $slotCaption = 'Slēgts'; @endphp
                                                      @else
                                                        @php $slotCaption = $slot->comment; @endphp
                                                      @endif
                                                      @php
                                                        $slotClass = 'closed-slot';
                                                        $slotText = $slotCaption;
                                                      @endphp
                                                      @break;

                                                      @case (SLOT_STATUS_TAKEN)
                                                      @if ($date == $today)
                                                        @if (\Carbon\Carbon::parse(App\Models\Office::timeByInterval($i))->subHour() >= \Carbon\Carbon::now())
                                                          @php
                                                            $slotClass = 'taken-slot';
                                                          @endphp
                                                        @else
                                                          @php
                                                            $slotClass = 'slot-gray';
                                                          @endphp
                                                        @endif
                                                      @else
                                                        @php
                                                          $slotClass = 'taken-slot';
                                                        @endphp
                                                      @endif
                                                      @php
                                                        $takenBy = json_decode($slot->takenby2);
                                                        $plate = substr($takenBy->ownerPhone,-3,3);
                                                        $plate = filter_var($plate, FILTER_SANITIZE_NUMBER_INT);
                                                        $plate = trim($plate,' -.');

                                                        $slotText = ''. \App\Helper\Tires::truncateCharacters(trim($takenBy->vehicleMake),8,'&mldr;',1) . ' xxxxx'.$plate.'';
                                                      @endphp
                                                      @break
                                                    @endswitch

                                                    <td class="time-slot">{{ App\Models\Office::timeByInterval($i) }}</td>
                                                    <td class="{{ $slotClass }} slot{{ $slotNumber }}-{{ $slot->queue_id }} slot" data-date="{{ $date }}" data-queue="{{ $slot->queue_id }}">
                                                      {!! $slotText !!}
                                                    </td>
                                                  @endif
                                                @endif
                                              @else
                                                @if (!$queue->isVisible($date))
                                                  @if ($office->_openQueues==0)
                                                    <td class="header-time">&nbsp;</td><td class="slot slot-closed">Slēgts</td>
                                                  @else
                                                    <td class="header-time"></td><td class="slot-empty slot"></td>
                                                  @endif
                                                @else
                                                  <td class="header-time"></td><td class="slot-empty slot"></td>
                                                @endif
                                              @endif
                                            @endforeach
                                          </tr>
                                        @endfor
                                      @endif
                                      </tbody>
                                    </table>
                                  </div>
                                  @endforeach
                              </div>

                              @php
                                $iteration++;
                              @endphp
                              @endfor
                        </div>
                    <section id="mobile-main">
                        <form method="POST">
                            @csrf
                            <input type="hidden" name="date">
                            <input type="hidden" name="slotNumber">
                            <input type="hidden" name="filiale">
                            <input type="hidden" name="part">
                            <div class="modal-dialog" role="document">
                                @if (session('success'))
                                  <div class="alert alert-success" style="border-color: #75bd59;">{!! session('success') !!}</div>
                                @endif
                                @if (session('danger'))
                                  <div class="alert alert-danger" style="border-color: #ee6868;">{!! session('danger') !!}</div>
                                @endif
                                @if (session('warning'))
                                  <div class="alert alert-warning">{!! session('warning') !!}</div>
                                @endif
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="mobile-modalTitle">
                                            Pieraksts
                                        </h5>
                                    </div>
                                    <div class="modal-body mobile-reservation-modal-body">
                                        <div class="container-fluid">

                                                <div class="col-md-12 mobile-body">

                                                    <div class="form-group reservation-filiale">
                                                      <span class="validate">*</span><label for="select">Filiāle</label>

                                                      <div id="mobile-filiale">
{{--                                                        <select class="custom-select" name="filiale" required="required">--}}
{{--                                                          <option disabled selected>Izvēlēties</option>--}}
{{--                                                        </select>--}}

                                                        <div class="filiale_grid">
                                                          <label class="filiale_card">
                                                            <input name="filiale" class="filiale_radio" type="radio" id="filiale_ulbroka" value="1">

                                                            <span class="filiale_plan-details">
                                                              <span class="filiale_plan-cost">Ulbroka</span>
                                                              <span>Acones iela 2a</span>
                                                              <span>67910555</span>
{{--                                                              <br>--}}
{{--                                                              <span>Pirm. - Piekt. 9:00 - 18:00</span>--}}
{{--                                                              <span>Sestdiena - 10:00 - 15:00</span>--}}
{{--                                                              <span>Svētdiena - Slēgts</span>--}}
                                                            </span>
                                                          </label>
                                                          <label class="filiale_card">
                                                            <input name="filiale" class="filiale_radio" type="radio" id="filiale_riga" value="2">
                                                            <span class="filiale_hidden-visually">Pro - $50 per month, 5 team members, 500 GB per month, 5 concurrent builds</span>
                                                            <span class="filiale_plan-details" aria-hidden="true">
                                                              <span class="filiale_plan-cost">Rīga</span>
                                                              <span>Kalnciema iela 39</span>
                                                              <span>67615615</span>
{{--                                                              <br>--}}
{{--                                                              <span>Pirm. - Piekt. 9:00 - 18:00</span>--}}
{{--                                                              <span>Sestdiena - Slēgts</span>--}}
{{--                                                              <span>Svētdiena - Slēgts</span>--}}
                                                            </span>
                                                          </label>
                                                        </div>

                                                      </div>
                                                    </div>

                                                    <div id="mobile-slots-choice">

                                                    </div>
                                                  <div id="mobile-reservation-form" tabindex='1'>
                                                    <div class="form-group hidden-dates" style="display: none; margin-top: 2rem;">
                                                      <label for="reservation-date"><span class="validate">*</span>Datums</label>
                                                      <div id="mobile-date">
                                                        <select class="custom-select" name="reservation-date">
                                                          <option class="reservation-disabled" value="0" disabled selected>Izvēlēties</option>
                                                        </select>
                                                      </div>
                                                    </div>

                                                    <div class="form-group hidden-times" style="display: none;">
                                                      <label for="reservation-time"><span class="validate">*</span>Brīvie laiki</label>
                                                      <div id="mobile-time">
                                                        <select required="required" class="custom-select" name="reservation-time">
                                                          <option class="reservation-disabled" value="0" disabled selected>Izvēlēties</option>
                                                        </select>
                                                      </div>
                                                    </div>

                                                    <div class="form-group purpose">
                                                      <label for="serviceOption"><span class="validate">*</span>Es vēlos:</label>
                                                      <div id="mobile-service">
                                                        <select class="custom-select" name="serviceOption" required="required">
                                                          <option disabled selected>Izvēlēties</option>
                                                          @foreach ($services as $service)
                                                            <option value="{{ $service->service_id }}" @if ($service->enabled == 0) disabled @endif @if ($service->f_ac == 1) data-ac="1" @endif @if ($service->f_moto == 1) data-moto="1" @endif>{{ $service->title }}</option>
                                                          @endforeach
                                                        </select>
                                                      </div>
                                                    </div>

                                                    <div class="form-group rims-with-mobile" style="display: none;">
                                                      <div class="form-check">
                                                        <label class="form-check-label" for="rimsWith1">
                                                          <img src="{{asset('images/bez_diskiem.jpg')}}" alt="">
                                                        </label>
                                                        <br>
                                                        <input value="1" class="form-check-input" type="radio" name="rims_with_input" id="rimsWith1">
                                                        Līdzi būs riepas bez diskiem
                                                      </div>
                                                      <div class="form-check">
                                                        <label class="form-check-label" for="rimsWith2">
                                                          <img src="{{asset('images/ar_diskiem.png')}}" alt="">
                                                        </label>
                                                        <br>
                                                        <input value="2" class="form-check-input" type="radio" name="rims_with_input" id="rimsWith2">
                                                        Līdzi būs riepas ar diskiem
                                                      </div>
                                                    </div>

                                                    <div class="form-group rims-storageBin" style="display: none;">
                                                      <label for="mobile_storage_bin">Glabāšanas talona numurs:</label>
                                                      <input id="mobile_storage_bin" type="text" class="form-control" title="">
                                                      <span style="font-size: 11px;line-height: 10px;">Ja Jums pašlaik nav zināms glabāšanas talona numurs, tas nekas, atradīsim Jūsu riepas vai riteņus pēc automašīnas numura</span>
                                                    </div>
                                                    <div class="form-group">
                                                      <label for="mobile-brand"><span class="validate">*</span>Auto marka:</label>
                                                      <input id="mobile-brand" type="text" class="form-control">
                                                    </div>

                                                    <div class="form-group">
                                                      <label for="mobile-model"><span class="validate">*</span>Auto modelis:</label>
                                                      <input id="mobile-model" type="text" class="form-control">
                                                    </div>

                                                    <div class="form-group">
                                                      <label for="mobile-reg_nr"><span class="validate">*</span>Reģistrācijas numurs:</label>
                                                      <input type="text" class="form-control" id="mobile-reg_nr">
                                                    </div>

                                                    <div class="form-group">
                                                      <label for="mobile-comment">Piezīmes</label>
                                                      <textarea id="mobile-comment" name="" cols="40" rows="4" class="form-control"></textarea>
                                                    </div>

                                                    <div class="form-group">
                                                      <label for="mobile-name">Mans vārds:</label>
                                                      <input id="mobile-name" type="text" class="form-control">
                                                    </div>

                                                    <div class="form-group phone-number">
                                                      <label for="mobile-phone"><span class="validate">*</span>Mans tālruņa numurs:</label>
                                                      <input type="text" class="form-control" id="mobile-phone">
                                                    </div>
                                                    <div class="form-group client-email last">
                                                      <label for="mobile-email">Mans e-pasts:</label>
                                                        <input type="email" class="form-control" id="mobile-email">
                                                    </div>
                                                    <div class="modal-footer reservation-modal-footer">
                                                      <button type="button" class="btn btn-primary" id="mobile-submit-reservation">Pierakstīties</button>
                                                      <button type="button" class="btn btn-primary" id="mobile-close-modal" style="display: none;">Atgriezties</button>
                                                    </div>
                                                  </div>
                                                </div>
                                                <div class="mobile-body-success" style="display: none;">
                                                    <div class="alert alert-success">

                                                    </div>
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
    </div>

    <div class="modal fade" id="reservation" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-background" data-dissmiss="modal"></div>
            <form method="POST">
                @csrf
                <input type="hidden" name="date">
                <input type="hidden" name="queue_id">
                <input type="hidden" name="slotNumber">
                <input type="hidden" name="part">
                <input type="hidden" name="grecaptcha">
                <input type="hidden" name="grecaptcha_app">
                <div class="modal-dialog reservation-modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalTitle">
                                Mans pieraksts - <span class="dayOfWeek"></span>, <span class="dateOfDay"></span> <span class="timeOfDay"></span>, <span class="officeTitle"></span>
                            </h5>
                        </div>
                        <div class="modal-body reservation-modal-body">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-row row">
                                            <div class="form-group col-md-3 col-sm-12 hidden-sm-down">
                                                <label for="brand"><span class="validate" style="color: red;">*</span>Auto marka un modelis:</label>
                                            </div>
                                            <div class="col-md-3 col-sm-12 hidden-md-up">
                                              <label for="brand"><span class="validate" style="color: red;">*</span>Auto marka:</label>
                                            </div>
                                            <div class="form-group col-md-5 col-sm-12">
                                              <input type="text" class="form-control" id="brand" title="">
                                            </div>
                                            <div class="col-md-3 col-sm-12 hidden-md-up">
                                              <label for="model"><span class="validate" style="color: red;">*</span>Auto modelis:</label>
                                            </div>
                                            <div class="form-group col-md-4 col-sm-12">
                                              <input type="text" class="form-control" id="model" title="">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="reg_nr" class="col-sm-12 col-md-3" style="text-align: left;"><span class="validate">*</span>Reģistrācijas numurs:</label>
                                            <div class="col-sm-12 col-md-9">
                                                <input type="text" class="form-control" id="reg_nr">
                                            </div>
                                        </div>
                                        <div class="form-group services">
                                            <div class="row">
                                                <div class="form-group col-md-3">
                                                    <label for="service"><span class="validate" style="color: red;">*</span>Es vēlos:</label>
                                                </div>
                                                <div class="col-md-8" id="service">
                                                    @foreach ($services as $service)
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="serviceOption" @if ($service->enabled == 0) disabled @endif id="serviceOption{{ $service->service_id }}" @if ($service->f_save == 1) data-save="1"@endif @if ($service->f_save == 2) data-save="2"@endif @if ($service->f_ac == 1) data-ac="1" @endif @if ($service->f_moto == 1) data-moto="1" @endif value="{{ $service->service_id }}">
                                                        <label class="form-check-label" for="serviceOption{{ $service->service_id }}">
                                                            {{ $service->title }}
                                                        </label>
                                                      @if($service->service_id == 1)
                                                        <div id="service-option-selection"></div>
                                                      @endif
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="comment" class="col-sm-3" style="text-align: left;">Piezīmes:</label>
                                            <div class="col-sm-9">
                                                <textarea id="comment" class="form-control" cols="20" rows="4"></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="name" class="col-sm-3" style="text-align: left;">Mans vārds:</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="name">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="phone" class="col-sm-3" style="text-align: left;"><span class="validate" style="color: red;">*</span>Mans tālruņa numurs:</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="phone">
                                            </div>
                                        </div>
                                        <div class="form-group row last">
                                            <label for="email" class="col-sm-3" style="text-align: left;">Mans e-pasts:</label>
                                            <div class="col-sm-9">
                                                <input type="email" class="form-control" id="email">
                                            </div>
                                        </div>
                                      <input type="hidden" name="recaptcha" id="recaptcha">
                                    </div>
                            </div>
                        </div>
                        <div class="modal-footer reservation-modal-footer">
                            <button type="button" class="btn btn-secondary col-xs-12 col-md-6 mb-1" id="close-modal" data-dismiss="modal">Atcelt</button>
                            <span style="margin: 0 auto;" class="hidden-md-down"></span>
                            <button type="button" class="btn btn-primary col-xs-12 col-md-6 mb-1" id="submit-reservation">Pierakstīties</button>
                        </div>
                    </div>
                </div>
                </div>

            </form>
        </div>
    </div>
@endsection
