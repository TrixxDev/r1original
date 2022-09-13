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
          <a href="{{ route('admin.records.date', $todayDate) }}">
            <svg class="c-sidebar-nav-icon icons">
              <use xlink:href="/node_modules/@coreui/icons/sprites/free.svg#cil-home"></use>
            </svg>
          </a>
          <a href="{{ route('admin.records.date', $yesterday) }}">
            <svg class="c-sidebar-nav-icon icons">
              <use xlink:href="/node_modules/@coreui/icons/sprites/free.svg#cil-arrow-left"></use>
            </svg>
          </a>
          <a href="{{ route('admin.records.date', $tomorrow) }}">
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
          <table class="queueTable records">
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
              <th class="subheader" colspan="2"><span>{{ $queue->title }}</span>
                <svg data-toggle="modal" data-target="#queueModal" data-date="{{ date('Y-m-d', strtotime($dateFmt)) }}" data-queue-id="{{ $queue->queue_id }}" class="c-sidebar-nav-icon icons">
                  <use xlink:href="/node_modules/@coreui/icons/sprites/free.svg#cil-pencil"></use>
                </svg>
              </th>
            @else
              <th class="subheader" colspan="1"><span>{{ $queue->title }}</span>
                <svg data-toggle="modal" data-target="#queueModal" data-date="{{ date('Y-m-d', strtotime($dateFmt)) }}" data-queue-id="{{ $queue->queue_id }}" class="c-sidebar-nav-icon icons" >
                  <use xlink:href="/node_modules/@coreui/icons/sprites/free.svg#cil-pencil"></use>
                </svg>
              </th>
            @endif
          @else
            <th class="subheader" colspan="1"><span>{{ $queue->title }}</span>
              <svg data-toggle="modal" data-target="#queueModal" data-date="{{ date('Y-m-d', strtotime($dateFmt)) }}" data-queue-id="{{ $queue->queue_id }}" class="c-sidebar-nav-icon icons" >
                <use xlink:href="/node_modules/@coreui/icons/sprites/free.svg#cil-pencil"></use>
              </svg>
            </th>
          @endif
          @endforeach
          @for ($i=$openTime;$i<$closeTime;$i++)
            <tr>
            @php $col = 0; @endphp
            @foreach ($office->_queues as $queue)

              @php
                $slotNumber = $queue->getSlotNumberByInterval($date,$i); $col++;
              @endphp

              @if ($slotNumber!==false)
                @if ($queue->isIntervalBeginning($date,$i))
                  @php $slot = $queue->_slots[$date][$slotNumber]; @endphp

                  @switch ($slot->status)
                    @case (SLOT_STATUS_TAKEN)
                    @case (SLOT_STATUS_OFFER)
                    @case (SLOT_STATUS_FREE)
                      @php
                        if ($slot->status == 2 && $slot->comment != null)
                            $slotCaption = '<input type="checkbox" class="discount" data-slot-id="' . $slot->slot_id . '" checked><span>' . $queue->getSlotStartTime($date, $slotNumber).' - '.$queue->getSlotEndTime($date, $slotNumber) . '</span>';
                        else
                            $slotCaption = '<input type="checkbox" class="discount" data-slot-id="' . $slot->slot_id . '"><span>' . $queue->getSlotStartTime($date, $slotNumber).' - '.$queue->getSlotEndTime($date, $slotNumber) . '</span>';
                      @endphp
                      @if (trim($slot->comment)!='')
                        @php $slotCaption .= ' <span class="slot-comment">' . $slot->comment . '</span>'; @endphp
                      @else
                        @php $slotCaption .= ' <span class="slot-comment"></span>'; @endphp
                      @endif
                      @if ($queue->isVisible($date))
                        @php $slotClass = 'slot-taken-admin'; @endphp
                      @else
                        @php $slotClass = 'slot-gray'; @endphp
                      @endif
                      @php
                      //$slotText = '<a href="'. url_self_reference(array('d'=>$date,'qu'=>$queue->id,'time'=>$slot->iorder)).'"></a>';
                      $slotText = $slotCaption;
                      $buttons = '<div class="buttonbar"><svg data-toggle="modal" data-target="#slotModal" data-date="' . date('Y-m-d', strtotime($dateFmt)) . '" data-queue-id="' . $queue->queue_id . '" data-slot-id="' . $slotNumber . '" class="c-sidebar-nav-icon icons"><use xlink:href="/node_modules/@coreui/icons/sprites/free.svg#cil-pencil"></use></svg></div>';
                      @endphp
                    @break
                    @case (SLOT_STATUS_CLOSED)
                      @php $slotCaption = $queue->getSlotStartTime($date, $slotNumber).' - '.$queue->getSlotEndTime($date, $slotNumber) @endphp
                      @if (trim($slot->comment)=='')
                        @php $slotCaption .= ' Slēgts!'; @endphp
                      @else
                        @php $slotCaption .= $slot->comment; @endphp
                      @endif
                      @php
                      $slotClass = 'slot-closed';
                      $slotText = $slotCaption;

                      $buttons = '<div class="buttonbar"><svg data-toggle="modal" data-target="#slotModal" data-date="' . date('Y-m-d', strtotime($dateFmt)) . '" data-queue-id="' . $queue->queue_id . '" data-slot-id="' . $slotNumber . '" class="c-sidebar-nav-icon icons"><use xlink:href="/node_modules/@coreui/icons/sprites/free.svg#cil-pencil"></use></svg></div>';
                      @endphp
                    @break;
                  @endswitch
                  @if ($col==1)
                    @if ($iteration == 1)
                      <td class="header-time">{{ App\Models\Office::timeByInterval($i) }}</td>
                    @endif
                    <td @if ($queue->_workingDays[$date]->slotSize>1) rowspan="{!! $queue->_workingDays[$date]->slotSize !!}" @else '' @endif class="slot {{ $slotClass }}"> {!! $slotText.$buttons !!}</td>
                  @else
                    <td @if ($queue->_workingDays[$date]->slotSize>1) rowspan="{!! $queue->_workingDays[$date]->slotSize !!}" @else '' @endif class="slot {{ $slotClass }}"> {!! $slotText.$buttons !!}</td>
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
  <div class="modal fade" id="queueModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="queueModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="queueModalLabel">Labot rindu<br><span><h6 class="title"></h6></span></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form method="post">
            <input type="hidden" name="queue_id">
            <input type="hidden" name="date">
            <div class="form-group row">
              <label for="title" class="col-sm-2 col-form-label text-right">Nosaukums:</label>
              <div class="col-10">
                <input type="text" class="form-control" id="title">
              </div>
            </div>
            <div class="separator"></div>
            <div class="form-group row time">
              <label for="isActive" class="col-sm-2 col-form-label text-right">Darba laiks:</label>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="isActive">
              </div>
              <div class="col-3"><input type="text" class="form-control ui-datepicker" id="openTime"></div>
              <span class="timeSeparator">-</span>
              <div class="col-3"><input type="text" class="form-control ui-datepicker" id="closeTime"></div>
            </div>
            <div class="form-group row">
              <legend class="col-form-label col-sm-2 float-sm-left pt-0 text-right">Mainīt</legend>
              <div class="col-sm-10">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="gridRadios" id="gridRadios2" value="f_purpose1" checked>
                  <label class="form-check-label" for="gridRadios2">
                    Vienai dienai
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="gridRadios" id="gridRadios3" value="f_purpose2">
                  <label class="form-check-label" for="gridRadios3">
                    Visām <b class="f_day">pirmdienām</b> uz priekšu (ieskaitot)
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="gridRadios" id="gridRadios4" value="f_purpose3">
                  <label class="form-check-label" for="gridRadios4">
                    Visām darba dienām uz priekšu (ieskaitot)
                  </label>
                </div>
              </div>
            </div>
            <div class="form-group row">
              <legend class="col-form-label col-sm-2 float-sm-left pt-0 text-right">Rindu sadalījums</legend>
              <div class="col-sm-10">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="rows" id="gridRadios5" value="f_rows0" checked>
                  <label class="form-check-label" for="gridRadios5">
                    Pilna rinda
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="rows" id="gridRadios6" value="f_rows1">
                  <label class="form-check-label" for="gridRadios6">
                    Pusrinda
                  </label>
                </div>
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
  <div class="modal fade slotSettings" id="slotModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="slotModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="queueModalLabel">Labot darba laiku<br><span><h6 class="title"></h6></span></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form method="post">
            <input type="hidden" name="queue_id">
            <input type="hidden" name="date">
            <input type="hidden" name="slot">
            <input type="hidden" name="part" value="a">
            <div class="form-group row">
              <label for="title" class="col-sm-2 col-form-label text-right">Komentāri:</label>
              <div class="col-10">
                <textarea id="f_slotcomment" class="form-control" cols="30" rows="5"></textarea>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Atcelt</button>
          <button type="button" class="btn btn-primary submit">Saglabāt</button>
        </div>
      </div>
    </div>
  </div>

@endsection
