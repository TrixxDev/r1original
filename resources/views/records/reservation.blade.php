@extends('layouts.app')

@section('content')

  <div class="records container-fluid">
    <div class="row">
      <div class="main-content clearfix col-md-12 col-xl-12">
        <div id="content-wrapper" class="right-column col-lg-12">
          @include('components.calendar')
          @php
            $todayDate = date('Y-m-d');

            $yesterday = date('Y-m-d', strtotime("-1 days",$currentDate));
            $tomorrow = date('Y-m-d', strtotime("+1 days",$currentDate));
          @endphp
          <div class="loading"></div>
          <div class="working-day">
            <div class="col-12">
              <div class="row">
                <a href="{{ route('rezervacijas') }}">
                  <svg class="c-sidebar-nav-icon icons">
                    <use xlink:href="/node_modules/@coreui/icons/sprites/free.svg#cil-home"></use>
                  </svg>
                </a>
                <a href="{{ route('rezervacijas.date', $yesterday) }}">
                  <svg class="c-sidebar-nav-icon icons">
                    <use xlink:href="/node_modules/@coreui/icons/sprites/free.svg#cil-arrow-left"></use>
                  </svg>
                </a>
                <a href="{{ route('rezervacijas.date', $tomorrow) }}">
                  <svg class="c-sidebar-nav-icon icons">
                    <use xlink:href="/node_modules/@coreui/icons/sprites/free.svg#cil-arrow-right"></use>
                  </svg>
                </a>
              </div>
            </div>

            @foreach ($workingDays as $workingDay)

              @php
                $dateFmt = date('d.m.Y', strtotime($workingDay.' 00:00:00'));
                $dayOfWeek = $_weekDays[date('N', strtotime($workingDay.' 00:00:00'))];


                $openTime = 0;
                $closeTime = -1;
              @endphp

              <div class="col-12">
                <br>
                <h4>{{ $_weekDays[date('N', strtotime($workingDay.' 00:00:00'))] }}, {{ date('d.m.Y', strtotime($workingDay.' 00:00:00')) }}</h4>
                <div class="row" style="display: flex;">
                  @foreach ($offices as $office)
                    <table class="queueTable reservation">
                      @php
                        if ($openTime==0){
                          $openTime = $office->getOpenTime($workingDay);
                        } else {
                          $t = $office->getOpenTime($workingDay);
                          if ($t>0){
                            $openTime = min($openTime, $t);
                          }
                        }
                        $closeTime = max($closeTime, $office->getCloseTime($workingDay));
			$col = 0;
                        $iteration = $loop->iteration;
                      @endphp
                      <tr>
                        @if ($iteration == 1 && $openTime != 0 && $closeTime != 0)
                          <th class="header"></th>
                        @endif
                        <th class="header" colspan="{{ count($office->_queues) }}">{{ $office->title }}</th>
                      </tr>
                      <tr>
                        @foreach ($office->_queues as $queue)
                          @php $col++; @endphp
                          @if ($col==1)
                            @if ($iteration == 1 && $openTime != 0 && $closeTime != 0)
                              <th class="subheader" colspan="2"><span>{{ $queue->title }}</span></th>
                            @else
                              <th class="subheader" colspan="1"><span>{{ $queue->title }}</span></th>
                            @endif
                          @else
                            <th class="subheader" colspan="1"><span>{{ $queue->title }}</span></th>
                          @endif

                        @endforeach
                      </tr>
                      @if ($openTime == 0 && $closeTime == 0)
                        <tr>
                          @foreach ($office->_queues as $queue)
                            <td class="slot slot-closed" colspan="1"><span>Slēgts!</span></td>
                          @endforeach
                        </tr>
                      @endif
                      @for ($i=$openTime;$i<$closeTime;$i+=$timeStep)
                        <tr>
                          @php $col = 0; @endphp
                          @foreach ($office->_queues as $queue)

                            @php
                              $col++;
                              $slotNumber = $queue->getSlotNumberByInterval($workingDay,$i);
                            @endphp

                            @if (($slotNumber!==false)&&($queue->isVisible($workingDay)))
                              @php
                                $buttons2 = '';
                                $slotText2 = '';
                              @endphp
                              @if ($queue->isIntervalBeginning($workingDay,$i))
                                @php $slot = $queue->_slots[$workingDay][$slotNumber]; @endphp

                                @switch ($slot->status)
                                  @case (SLOT_STATUS_FREE)
                                    @if (trim($slot->comment)=='')
                                      @php
                                        $slotClass = 'slot-free';
                                        $slotCaption = '';
                                      @endphp
                                    @else
                                      @php
                                        $slotClass = 'slot-offer';
                                        $slotCaption = $slot->comment;
                                      @endphp
                                    @endif

                                    @php $slotText = $slotCaption; @endphp

                                    @if ($slot->status2 == SLOT_STATUS_TAKEN)
                                      @if ($slot->createuser>0)
                                        @php $slotClass2 = 'slot-taken-admin'; @endphp
                                      @else
                                        @php $slotClass2 = 'slot-taken'; @endphp
                                      @endif
                                      @php $takenBy = json_decode($slot->takenby2); @endphp
                                      @if ($slotText=='')
                                        @php
                                          $slotText2='<span style="color: red;font-weight:normal">'.$takenBy->vehicleMake.' '.$takenBy->vehicleModel.' '.$takenBy->ownerPhone.'</span>';
                                        @endphp
                                      @endif
                                    @elseif ($slot->status2 == SLOT_STATUS_FREE)
                                      @php $slotClass2 = 'slot-free'; @endphp
                                    @endif

                                    @if ($queue->_workingDays[$workingDay]->secondaryAvailable)
                                      @php $buttons2 = '<div class="buttonbar"><svg data-toggle="modal" data-target="#slotModal" data-date="' . date('Y-m-d', strtotime($dateFmt)) . '" data-queue-id="' . $queue->queue_id . '" data-slot-id="' . $slotNumber . '" data-slot-part="b" class="c-sidebar-nav-icon icons"><use xlink:href="/node_modules/@coreui/icons/sprites/free.svg#cil-pencil"></use></svg></div>'; @endphp
                                    @endif

                                    @php $buttons = '<div class="buttonbar"><svg data-toggle="modal" data-target="#slotModal" data-date="' . date('Y-m-d', strtotime($dateFmt)) . '" data-queue-id="' . $queue->queue_id . '" data-slot-id="' . $slotNumber . '" data-slot-part="a" class="c-sidebar-nav-icon icons"><use xlink:href="/node_modules/@coreui/icons/sprites/free.svg#cil-pencil"></use></svg></div>'; @endphp
                                  @break
                                  @case (SLOT_STATUS_TAKEN)

                                    @php $takenBy = json_decode($slot->takenby); @endphp

                                    @if ($slot->createuser>0)
                                      @php $slotClass = 'slot-taken-admin'; @endphp
                                    @else
                                      @php $slotClass = 'slot-taken'; @endphp
                                    @endif

                                    @php $slotText = '<span>'.$takenBy->vehicleMake.' '.$takenBy->vehicleModel.' '.$takenBy->ownerPhone . '</span>'; @endphp


                                    @if ($queue->_workingDays[$workingDay]->secondaryAvailable && $slot->status2==SLOT_STATUS_TAKEN)
                                      @if ($slot->createuser>0)
                                        @php $slotClass2 = 'slot-taken-admin'; @endphp
                                      @else
                                        @php $slotClass2 = 'slot-taken'; @endphp
                                      @endif
                                      @php
                                        $takenBy = json_decode($slot->takenby2);
                                        $slotText2='<span style="color: red;font-weight:normal">'. $takenBy->vehicleMake .' '. $takenBy->vehicleModel .' '. $takenBy->ownerPhone .'</span>';
                                      @endphp
                                    @endif

                                    @if ($queue->_workingDays[$workingDay]->secondaryAvailable)
                                      @php $buttons2 = '<div class="buttonbar"><svg data-toggle="modal" data-target="#slotModal" data-date="' . date('Y-m-d', strtotime($dateFmt)) . '" data-queue-id="' . $queue->queue_id . '" data-slot-id="' . $slotNumber . '" data-slot-part="b" class="c-sidebar-nav-icon icons"><use xlink:href="/node_modules/@coreui/icons/sprites/free.svg#cil-pencil"></use></svg></div>'; @endphp
                                    @endif

                                    @php
                                      $buttons = '<div class="buttonbar"><svg data-toggle="modal" data-target="#slotModal" data-date="' . date('Y-m-d', strtotime($dateFmt)) . '" data-queue-id="' . $queue->queue_id . '" data-slot-id="' . $slotNumber . '" data-slot-part="a" class="c-sidebar-nav-icon icons"><use xlink:href="/node_modules/@coreui/icons/sprites/free.svg#cil-pencil"></use></svg></div>';
                                    @endphp
                                  @break
                                  @case (SLOT_STATUS_OFFER)
                                    @php
                                      $slotClass = 'slot-offer';
                                      $slotText = ''.$slot->comment.'';
                                      $buttons = '<div class="buttonbar"><svg data-toggle="modal" data-target="#slotModal" data-date="' . date('Y-m-d', strtotime($dateFmt)) . '" data-queue-id="' . $queue->queue_id . '" data-slot-id="' . $slotNumber . '" data-slot-part="a" class="c-sidebar-nav-icon icons"><use xlink:href="/node_modules/@coreui/icons/sprites/free.svg#cil-pencil"></use></svg></div>';
                                    @endphp

				    @if ($queue->_workingDays[$workingDay]->secondaryAvailable && $slot->status2==SLOT_STATUS_OFFER)
                                      @php
                                        $slotClass2 = 'slot-offer';
                                      	$slotText = ''.$slot->comment.'';
                                        $buttons2 = '<div class="buttonbar"><svg data-toggle="modal" data-target="#slotModal" data-date="' . date('Y-m-d', strtotime($dateFmt)) . '" data-queue-id="' . $queue->queue_id . '" data-slot-id="' . $slotNumber . '" data-slot-part="a" class="c-sidebar-nav-icon icons"><use xlink:href="/node_modules/@coreui/icons/sprites/free.svg#cil-pencil"></use></svg></div>';
                                      @endphp
                                    @endif

                                    @if ($queue->_workingDays[$workingDay]->secondaryAvailable)
                                      @php $buttons2 = '<div class="buttonbar"><svg data-toggle="modal" data-target="#slotModal" data-date="' . date('Y-m-d', strtotime($dateFmt)) . '" data-queue-id="' . $queue->queue_id . '" data-slot-id="' . $slotNumber . '" data-slot-part="b" class="c-sidebar-nav-icon icons"><use xlink:href="/node_modules/@coreui/icons/sprites/free.svg#cil-pencil"></use></svg></div>'; @endphp
                                    @endif
                                    @break
                                    @case (SLOT_STATUS_CLOSED)
                                    @if (trim($slot->comment)=='')
                                      @php $slotCaption = 'Slēgts'; @endphp
                                    @else
                                      @php $slotCaption = $slot->comment; @endphp
                                    @endif

                                    @php
                                      $slotClass = 'slot-closed';
                                      $slotText = $slotCaption;

                                      $buttons = '<div class="buttonbar"><svg data-toggle="modal" data-target="#slotModal" data-date="' . date('Y-m-d', strtotime($dateFmt)) . '" data-queue-id="' . $queue->queue_id . '" data-slot-id="' . $slotNumber . '" data-slot-part="a" class="c-sidebar-nav-icon icons"><use xlink:href="/node_modules/@coreui/icons/sprites/free.svg#cil-pencil"></use></svg></div>';
                                    @endphp
                                  @break
                                @endswitch
                                @if ($queue->_workingDays[$workingDay]->secondaryAvailable)
                                  @php $cellContents = '<table class="queueSubTable"><tr><td class="' . $slotClass . '">'.$slotText.$buttons.'</td></tr><tr><td class="slot-free">'.$slotText2.$buttons2.'</td></tr></table>'; @endphp
                                @else
                                  @php $cellContents = $slotText.$buttons; @endphp
                                @endif

                                @if ($col==1)
                                  @if ($iteration == 1)
                                    <td class="header-time">{{ App\Models\Office::timeByInterval($i) }}</td><td @if ($queue->_workingDays[$workingDay]->slotSize>1) rowspan="{!! $queue->_workingDays[$workingDay]->slotSize/$timeStep !!}" @else '' @endif class="slot {{ $slotClass }}">{!! $cellContents !!}</td>
                                @else
                                  <td @if ($queue->_workingDays[$workingDay]->slotSize>1) rowspan="{!! $queue->_workingDays[$workingDay]->slotSize/$timeStep !!}" @else '' @endif class="slot {{ $slotClass }}">{!! $cellContents !!}</td>
                                @endif
                              @else
                                <td @if ($queue->_workingDays[$workingDay]->slotSize>1) rowspan="{!! $queue->_workingDays[$workingDay]->slotSize/$timeStep !!}" @else '' @endif class="slot {{ $slotClass }}">{!! $cellContents !!}</td>
                              @endif
                            @else
                              @if ($col==1)
                                @if ($iteration == 1)
                                  <td class="header-time header-time-small">{{ App\Models\Office::timeByInterval($i) }}</td>
                                @endif
                              @endif
                            @endif
                            @else
                              @if ($col==1)
                                @if ($iteration == 1)
                                  <td class="header-time">{{ App\Models\Office::timeByInterval($i) }}</td><td class="slot slot-empty"></td>
                                @else
                                  <td class="slot slot-free"></td>
                                @endif
                              @else
                                <td class="slot slot-free"></td>
                              @endif
                            @endif

                          @endforeach
                        </tr>
                      @endfor
                    </table>
                  @endforeach
                </div>
              </div>
            @endforeach
            <!-- Modal -->
            <div class="reservation_edit modal fade" id="slotModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="slotModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="slotModalLabel">Labot notikumu</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <form method="post">
                      <input type="hidden" name="queue_id">
                      <input type="hidden" name="date">
                      <input type="hidden" name="slot">
                      <input type="hidden" name="part">
                      <div class="form-group row time bg-light">
                        <label for="f_date" class="col-sm-3 col-form-label text-right">Datums un laiks:</label>
{{--                        <div class="col-3"><input type="text" class="form-control ui-datepicker" id="f_date"></div>--}}
                        <div class="col-3">
                          <select class="form-control" id="f_date">
                            @foreach ($workingDays as $workingDay)
                              <option value="{{ $workingDay }}">{{ $workingDay }}</option>
                            @endforeach
                          </select>
                        </div>
                        <div class="col-3">
{{--                          <input type="text" class="form-control ui-datepicker" id="f_time">--}}
                          <select class="form-control" id="f_time">
                            @for ($i=$openTime;$i<114;$i+=$timeStep)
                              <option value="{{ App\Models\Office::timeByInterval($i) }}">{{ App\Models\Office::timeByInterval($i) }}</option>
                            @endfor
                          </select>
                        </div> *
                      </div>
                      <div class="form-group row">
                        <label for="title" class="col-sm-3 col-form-label text-right">Filiāle/rinda:</label>
                        <div class="col-8">
                          <select class="form-control" id="f_office">

                          </select>
                        </div> *
                      </div>
                      <div class="separator"></div>
                      <div class="form-group row time bg-light">
                        <label for="f_car" class="col-sm-3 col-form-label text-right">Auto marka un modelis:</label>
                        <div class="col-3"><input type="text" class="form-control" id="f_car"></div>
                        <span class="timeSeparator">-</span>
                        <div class="col-3"><input type="text" class="form-control" id="f_model"></div>
                      </div>
                      <div class="form-group row time">
                        <label for="f_plate" class="col-sm-3 col-form-label text-right">Reģistrācijas numurs:</label>
                        <div class="col-3"><input type="text" class="form-control ui-datepicker" id="f_plate"></div>
                      </div>
                      <div class="form-group services row bg-light">
                        <div class="form-group col-md-3 text-right">
                          <label for="service"><span class="validate" style="color: red;">*</span>Es vēlos:</label>
                        </div>
                        <div class="col-md-8" id="service">
                          <label>
                            <select class="custom-select select-service-option">
                              @foreach ($services as $service)
                                <option name="serviceOption" id="serviceOption{{ $service->service_id }}" class="form-check-input" @if ($service->f_save == 1) data-save="1"@endif @if ($service->f_save == 2) data-save="2"@endif value="{{ $service->service_id }}">{{ $service->title }}</option>
                              @endforeach
                            </select>
                          </label>
                        </div>
                      </div>
                      <div class="rims-with-select-row row bg-light">
                        <div class="form-group col-md-3 text-right">
                          <label for="service"><span class="validate" style="color: red;">*</span>Izvēle:</label>
                        </div>
                        <div class="col-md-8" id="service">
                            <div class="form-check">
                              <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1" value="1">
                              <label class="form-check-label" for="flexRadioDefault1">
                                Riepas bez Diskiem
                              </label>
                            </div>
                            <div class="form-check">
                              <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault2" value="2">
                              <label class="form-check-label" for="flexRadioDefault2">
                                Riepas ar Diskiem
                              </label>
                            </div>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="title" class="col-sm-3 col-form-label text-right">Piezīmes:</label>
                        <div class="col-9">
                          <textarea id="f_comment" class="form-control specialClass" cols="30" rows="2"></textarea>
                        </div>
                      </div>
                      <div class="form-group row time bg-light">
                        <label for="f_name" class="col-sm-3 col-form-label text-right">Vārds/Telefons:</label>
                        <div class="col-3">
                          <input type="text" class="form-control ui-datepicker" id="f_name">
                        </div>
                        <span class="timeSeparator">/</span>
                        <div class="col-3">
                          <input type="text" class="form-control ui-datepicker" id="f_phone">
                        </div>
                      </div>
                      <div class="form-group row time">
                        <label for="f_email" class="col-sm-3 col-form-label text-right">E-pasts:</label>
                        <div class="col-3"><input type="text" class="form-control ui-datepicker" id="f_email"></div>
                      </div>
                      <div class="separator"></div>
                      <div class="form-group row bg-light">
                        <label for="f_status" class="col-sm-3 col-form-label text-right">Statuss:</label>
                        <div class="col-3">
                          <select class="custom-select mr-sm-2" id="f_status">
                            <option value="0">Brīvs</option>
                            <option value="1">Aizņemts</option>
                            <option value="3">Slēgts</option>
                          </select>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="title" class="col-sm-3 col-form-label text-right">Komentāri:</label>
                        <div class="col-9">
                          <textarea id="f_slotcomment" class="form-control" cols="30" rows="2"></textarea>
                        </div>
                      </div>
                    </form>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary decline" data-dismiss="modal">Atcelt</button>
                    <button type="button" class="btn btn-primary submit">Saglabāt</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

@endsection
