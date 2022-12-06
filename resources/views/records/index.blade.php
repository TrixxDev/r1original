@extends('layouts.app')

@section('content')

    <div class="container-fluid records">
        <div class="">
            <div class="main-content clearfix col-md-12 col-xl-12">
                <div class="loading"></div>
                <div id="content-wrapper" class="right-column col-lg-12">
                  <div class="schedule-table">
                    @include('components.calendar')
                    @php
                      $iteration = 0;
                      $timeToClose = \Carbon\Carbon::create(date('Y'), date('m'), date('d'), 7, 30);
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
                          @if ($timeToClose < $now)
                            <div class="alert alert-warning">Tekošajā dienā E-pierakstīties nav iespējams, ja redzat brīvus laikus un vēlaties šodien nomainīt riepas, tad lūdzu zvaniet!</div>
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
                                                    @if ($date == $today && $timeToClose < $now)
                                                      @php
                                                        $slotClass = 'slot-gray-free';
                                                        $slotCaption = 'Brīvs';
                                                        $slotText = ''.$slotCaption.'';
                                                      @endphp
                                                    @else
                                                      @if (trim($slot->comment)=='')
                                                        @php
                                                          $slotClass = 'available-slot';
                                                          $slotCaption = '<button class="free-slot-link" id="slot' . $slot->iorder . '-' . $slot->queue_id . '" data-col="' . $slot->queue_id . '" data-iorder="' . $slot->iorder . '" data-date="' . $slot->date . '" data-toggle="modal" data-target="#reservation">Brīvs</button>';
                                                        @endphp
                                                      @else
                                                        @php
                                                          $slotClass = 'slot-offer';
                                                          $slotCaption = '<button class="offer-slot-link" id="slot' . $slot->iorder . '-' . $slot->queue_id . '" data-col="' . $slot->queue_id . '" data-iorder="' . $slot->iorder . '" data-date="' . $slot->date . '" data-toggle="modal" data-target="#reservation">' . $slot->comment . '</button>';
                                                          //$slotCaption = $slot->comment;
                                                        @endphp
                                                      @endif
                                                      @php
                                                        //'. url_self_reference(array('d'=>$date,'qu'=>$queue->id,'time'=>$slot->iorder)).'
                                                        $slotText = $slotCaption;
                                                      @endphp
                                                    @endif
                                                    @break;

                                                    @case (SLOT_STATUS_TAKEN)
                                                    @if ($date == $today && $timeToClose < $now)
                                                      @php $slotClass = 'slot-gray'; @endphp
                                                    @else
                                                      @php $slotClass = 'taken-slot'; @endphp
                                                    @endif
                                                    @php
                                                      $takenBy = json_decode($slot->takenby);
                                                      $plate = substr($takenBy->ownerPhone,-3,3);
                                                      $plate = filter_var($plate, FILTER_SANITIZE_NUMBER_INT);
                                                      $plate = trim($plate,' -.');

                                                      $slotText = ''. \App\Helper\Tires::truncateCharacters(trim($takenBy->vehicleMake),8,'&mldr;',1).' xxxxx'.$plate.'';
                                                    @endphp
                                                    @break

                                                    @case (SLOT_STATUS_OFFER)
                                                    @if ($date == $today && $timeToClose < $now)
                                                      @php
                                                        $slotClass = 'slot-gray';
                                                        $slotText = '';
                                                      @endphp
                                                    @else
                                                      @php
                                                        $slotClass = 'slot-offer';
                                                        //'. url_self_reference(array('d'=>$date,'qu'=>$queue->id,'time'=>$slot->iorder)).'
                                                        $slotText = '<button class="offer-slot-link" id="slot' . $slot->iorder . '-' . $slot->queue_id . '" data-col="' . $slot->queue_id . '" data-iorder="' . $slot->iorder . '" data-date="' . $slot->date . '" data-toggle="modal" data-target="#reservation">' . $slot->comment . '</button>';
                                                      @endphp
                                                    @endif
                                                    @break

                                                    @case (SLOT_STATUS_CLOSED)
                                                    @if (trim($slot->comment)=='')
                                                      @php $slotCaption = 'Slēgts'; @endphp
                                                    @else
                                                      @php $slotCaption = $slot->comment; @endphp
                                                    @endif
                                                    @if ($date == $today && $timeToClose < $now)
                                                      @php
                                                        $slotClass = 'slot-gray';
                                                        $slotText = '';
                                                      @endphp
                                                    @else
                                                      @php
                                                        $slotClass = 'closed-slot';
                                                        $slotText = $slotCaption;
                                                      @endphp
                                                    @endif
                                                    @break
                                                  @endswitch

                                                  <td class="time-slot">{{ App\Models\Office::timeByInterval($i) }}</td>
                                                  <td class="{{ $slotClass }} slot">
                                                    {!! $slotText !!}
                                                  </td>

                                                @else
                                                  @if ($queue->_workingDays[$date]->secondaryAvailable)

                                                    @php $slot = $queue->_slots[$date][$slotNumber]; @endphp

                                                    @switch ($slot->status2)
                                                      @case (SLOT_STATUS_OFFER)
                                                      @case (SLOT_STATUS_CLOSED)
                                                      @case (SLOT_STATUS_FREE)
                                                      @if ($date == $today && $timeToClose < $now)
                                                        @php
                                                          $slotClass = 'slot-gray';
                                                          $slotText = '';
                                                        @endphp
                                                      @else
                                                        @php
                                                          $slotClass = 'available-slot';
                                                          $slotText = '';
                                                        @endphp
                                                      @endif
                                                      @break

                                                      @case (SLOT_STATUS_TAKEN)
                                                      @if ($date == $today && $timeToClose < $now)
                                                        @php $slotClass = 'slot-gray'; @endphp
                                                      @else
                                                        @php $slotClass = 'slot-taken'; @endphp
                                                      @endif
                                                      @php
                                                        $takenBy = json_decode($slot->takenby2);
                                                        //$slotText = '<a href="'. url_self_reference(array('d'=>$date,'qu'=>$queue->id,'time'=>$i)).'">'.H($takenBy).'</a>';
                                                        $plate = substr($takenBy->ownerPhone,-3,3);
                                                        $plate = filter_var($plate, FILTER_SANITIZE_NUMBER_INT);
                                                        $plate = trim($plate,' -.');

                                                        //$slotText = ''.H($takenBy->vehicleMake).' xxxxx'.H($plate).'';
                                                        $slotText = ''. \App\Helper\Tires::truncateCharacters(trim($takenBy->vehicleMake),8,'&mldr;',1).' xxxxx'.$plate.'';
                                                      @endphp
                                                      @break
                                                    @endswitch

                                                    <td class="time-slot">{{ App\Models\Office::timeByInterval($i) }}</td>
                                                    <td class="{{ $slotClass }} slot">
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
                            <input type="hidden" name="queue_id">
                            <input type="hidden" name="slotNumber">
                            <input type="hidden" name="filiale">
                            <div class="modal-dialog" role="document">
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
                                                        <select class="custom-select" name="filiale" required="required">
                                                          <option disabled selected>Izvēlēties</option>
                                                        </select>
                                                      </div>
                                                    </div>

                                                    <div class="form-group hidden-dates" style="display: none;">
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
                                                            <option value="{{ $service->service_id }}">{{ $service->title }}</option>
                                                          @endforeach
                                                        </select>
                                                      </div>
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
                                                      <label for="mobile-email"><span class="validate">*</span>Mans e-pasts:</label>
                                                        <input type="email" class="form-control" id="mobile-email">
                                                    </div>

                                                </div>
                                                <div class="mobile-body-success" style="display: none;">
                                                    <div class="alert alert-success">

                                                    </div>
                                                </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer reservation-modal-footer">
                                        <button type="button" class="btn btn-primary" id="mobile-submit-reservation">Pierakstīties</button>
                                        <button type="button" class="btn btn-primary" id="mobile-close-modal" style="display: none;">Atgriezties</button>
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
                <input type="hidden" name="grecaptcha">
                <input type="hidden" name="grecaptcha_app">
                <div class="modal-dialog" role="document">
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
                                            <div class="form-group col-md-3">
                                                <label for="brand"><span class="validate" style="color: red;">*</span>Auto marka un modelis:</label>
                                            </div>
                                            <div class="form-group col-md-5">
                                                <input type="text" class="form-control" id="brand">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <input type="text" class="form-control" id="model">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="reg_nr" class="col-sm-3" style="text-align: left;"><span class="validate">*</span>Reģistrācijas numurs:</label>
                                            <div class="col-sm-9">
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
                                                        <input class="form-check-input" type="radio" name="serviceOption" id="serviceOption{{ $service->service_id }}" @if ($service->f_save == 1) data-save="1"@endif @if ($service->f_save == 2) data-save="2"@endif value="{{ $service->service_id }}">
                                                        <label class="form-check-label" for="serviceOption{{ $service->service_id }}">
                                                            {{ $service->title }}
                                                        </label>
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
                                            <label for="email" class="col-sm-3" style="text-align: left;"><span class="validate" style="color: red;">*</span>Mans e-pasts:</label>
                                            <div class="col-sm-9">
                                                <input type="email" class="form-control" id="email">
                                            </div>
                                        </div>
                                      <input type="hidden" name="recaptcha" id="recaptcha">
                                    </div>
                            </div>
                        </div>
                        <div class="modal-footer reservation-modal-footer">
                            <button type="button" class="btn btn-secondary" id="close-modal" data-dismiss="modal" style="margin-right: 10px;">Atcelt</button>
                            <button type="button" class="btn btn-primary" id="submit-reservation">Pierakstīties</button>
                        </div>
                    </div>
                </div>
                </div>

            </form>
        </div>
    </div>
@endsection
