@extends('admin.layouts.app')

@section('content')

  @php
    $dateFmt = date('d.m.Y', strtotime($date.' 00:00:00'));
		$dayOfWeek = $_weekDays[date('N', strtotime($date.' 00:00:00'))];
		$todayDate = date('Y-m-d');

		$yesterday = date('Y-m-d', strtotime("-1 days",$currentDate));
		$tomorrow = date('Y-m-d', strtotime("+1 days",$currentDate));

    $openTime = 0;
		$closeTime = -1;
  @endphp

  <div class="working-day container-fluid">
    <div class="col-12">
      <div class="row">
        <h4>
          <a href="{{ route('admin.reservations.date', $todayDate) }}">
            <svg class="c-sidebar-nav-icon icons">
              <use xlink:href="/node_modules/@coreui/icons/sprites/free.svg#cil-home"></use>
            </svg>
          </a>
          <a href="{{ route('admin.reservations.date', $yesterday) }}">
            <svg class="c-sidebar-nav-icon icons">
              <use xlink:href="/node_modules/@coreui/icons/sprites/free.svg#cil-arrow-left"></use>
            </svg>
          </a>
          <a href="{{ route('admin.reservations.date', $tomorrow) }}">
            <svg class="c-sidebar-nav-icon icons">
              <use xlink:href="/node_modules/@coreui/icons/sprites/free.svg#cil-arrow-right"></use>
            </svg>
          </a>
          {{ $dayOfWeek }}, {{ $dateFmt }}
        </h4>
      </div>
    </div>

    <div class="col-12">
      <div class="row">
        @foreach ($offices as $office)
          <table class="queueTable reservation">
            @php
              if ($openTime==0){
                $openTime = $office->getOpenTime($date);
              } else {
                $t = $office->getOpenTime($date);
                if ($t>0){
                  $openTime = min($openTime, $t);
                }
              }
              $closeTime = max($closeTime, $office->getCloseTime($date));
              $col = 0;
              $iteration = $loop->iteration;
            @endphp
            <tr>
              @if ($iteration == 1)
                <th class="header"></th>
              @endif
              <th class="header" colspan="{{ count($office->_queues) }}">{{ $office->title }}</th>
            </tr>
            @foreach ($office->_queues as $queue)
              @php $col++; @endphp
              @if ($col==1)
                @if ($iteration == 1)
                  <th class="subheader" colspan="2"><span>{{ $queue->title }}</span></th>
                @else
                  <th class="subheader" colspan="1"><span>{{ $queue->title }}</span></th>
                @endif
              @else
                <th class="subheader" colspan="1"><span>{{ $queue->title }}</span></th>
              @endif
            @endforeach
            @for ($i=$openTime;$i<$closeTime;$i+=$timeStep)
              <tr>
                @php $col = 0; @endphp
                @foreach ($office->_queues as $queue)

                  @php
                    $col++;
                    $slotNumber = $queue->getSlotNumberByInterval($date,$i);
                  @endphp

                  @if (($slotNumber!==false)&&($queue->isVisible($date)))
                    @php
                      $buttons2 = '';
                      $slotText2 = '';
                    @endphp
                    @if ($queue->isIntervalBeginning($date,$i))
                      @php $slot = $queue->_slots[$date][$slotNumber]; @endphp

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
                            @php $takenBy = json_decode($slot->takenby2); @endphp
                            @if ($slotText=='')
                              @php
                                $slotText2='<span style="color: red;font-weight:normal">'.$takenBy->vehicleMake.' '.$takenBy->vehicleModel.' '.$takenBy->ownerPhone.'</span>';
                              @endphp
                            @endif
                          @endif

                          @if ($queue->_workingDays[$date]->secondaryAvailable)
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

                          @php $slotText = ''.$takenBy->vehicleMake.' '.$takenBy->vehicleModel.' '.$takenBy->ownerPhone; @endphp

                          @if ($queue->_workingDays[$date]->secondaryAvailable && $slot->status2==SLOT_STATUS_TAKEN)
                            @php
                              $takenBy = json_decode($slot->takenby2);
                              $slotText2='<span style="color: red;font-weight:normal">'. $takenBy->vehicleMake .' '. $takenBy->vehicleModel .' '. $takenBy->ownerPhone .'</span>';
                            @endphp
                          @endif

                          @if ($queue->_workingDays[$date]->secondaryAvailable)
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
                      @if ($queue->_workingDays[$date]->secondaryAvailable)
                        @php $cellContents = '<table class="queueSubTable"><tr><td class="slot-free">'.$slotText.$buttons.'</td></tr><tr><td class="slot-free">'.$slotText2.$buttons2.'</td></tr></table>'; @endphp
                      @else
                        @php $cellContents = $slotText.$buttons; @endphp
                      @endif

                      @if ($col==1)
                        @if ($iteration == 1)
                          <td class="header-time">{{ App\Models\Office::timeByInterval($i) }}</td><td @if ($queue->_workingDays[$date]->slotSize>1) rowspan="{!! $queue->_workingDays[$date]->slotSize/$timeStep !!}" @else '' @endif class="slot {{ $slotClass }}">{!! $cellContents !!}</td>
                        @else
                        <td @if ($queue->_workingDays[$date]->slotSize>1) rowspan="{!! $queue->_workingDays[$date]->slotSize/$timeStep !!}" @else '' @endif class="slot {{ $slotClass }}">{!! $cellContents !!}</td>
                        @endif
                      @else
                        <td @if ($queue->_workingDays[$date]->slotSize>1) rowspan="{!! $queue->_workingDays[$date]->slotSize/$timeStep !!}" @else '' @endif class="slot {{ $slotClass }}">{!! $cellContents !!}</td>
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
                        <td class="slot slot-empty"></td>
                      @endif
                    @else
                      <td class="slot slot-empty"></td>
                    @endif
                  @endif

                @endforeach
              </tr>
            @endfor
          </table>
        @endforeach
      </div>
    </div>
  </div>
  <!-- Modal -->
  <div class="modal fade" id="slotModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="slotModalLabel" aria-hidden="true">
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
              <div class="col-3"><input type="text" class="form-control ui-datepicker" id="f_date"></div>
              <div class="col-3"><input type="text" class="form-control ui-datepicker" id="f_time"></div> *
            </div>
            <div class="form-group row">
              <label for="title" class="col-sm-3 col-form-label text-right">Filiāle/rinda:</label>
              <div class="col-8">
                <select class="form-control" id="f_office">
                  @foreach ($offices as $office)
                    @foreach ($office->_queues as $queue)
                      @php $queue->loadWorkingDay($date); @endphp
                      @if ($queue->_workingDays[$date]->secondaryAvailable)
                        <option value="{{ $queue->queue_id }}a">{{ $office->title }} | {{ $queue->title }} | A</option>
                        <option value="{{ $queue->queue_id }}b">{{ $office->title }} | {{ $queue->title }} | B</option>
                      @else
                        <option value="{{ $queue->queue_id }}a">{{ $office->title }} | {{ $queue->title }}</option>
                      @endif
                    @endforeach
                  @endforeach
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
            <div class="form-group row bg-light">
              <legend class="col-form-label col-sm-3 float-sm-left pt-0 text-right">Es vēlos:</legend>
              <div class="col-sm-9">
                @foreach ($services as $service)
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="gridRadios" id="f_purpose{{ $loop->iteration }}" value="service{{ $service->service_id }}">
                  <label class="form-check-label" for="f_purpose{{ $loop->iteration }}">
                    {{ $service->title }}
                  </label>
                </div>
                @endforeach
              </div>
            </div>
            <div class="form-group row">
              <label for="title" class="col-sm-3 col-form-label text-right">Piezīmes:</label>
              <div class="col-9">
                <textarea id="f_comment" class="form-control" cols="30" rows="5"></textarea>
              </div>
            </div>
            <div class="form-group row time bg-light">
              <label for="f_name" class="col-sm-3 col-form-label text-right">Vārds:</label>
              <div class="col-3"><input type="text" class="form-control ui-datepicker" id="f_name"></div>
            </div>
            <div class="form-group row time">
              <label for="f_phone" class="col-sm-3 col-form-label text-right">Tālrunis:</label>
              <div class="col-3"><input type="text" class="form-control ui-datepicker" id="f_phone"></div>
            </div>
            <div class="form-group row time bg-light">
              <label for="f_email" class="col-sm-3 col-form-label text-right">E-pasts:</label>
              <div class="col-3"><input type="text" class="form-control ui-datepicker" id="f_email"></div>
            </div>
            <div class="separator"></div>
            <div class="form-group row">
              <label for="f_status" class="col-sm-3 col-form-label text-right">Statuss:</label>
              <div class="col-3">
                <select class="custom-select mr-sm-2" id="f_status">
                  <option value="0">Brīvs</option>
                  <option value="1">Aizņemts</option>
                  <option value="3">Slēgts</option>
                </select>
              </div>
            </div>
            <div class="form-group row bg-light">
              <label for="title" class="col-sm-3 col-form-label text-right">Komentāri:</label>
              <div class="col-9">
                <textarea id="f_slotcomment" class="form-control" cols="30" rows="5"></textarea>
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

@endsection
