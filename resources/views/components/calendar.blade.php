@if (Auth::check() && !Auth::user()->hasRole(['Administrators', 'Moderators']))
  <div class="card extra-options">
    <div class="dropdown-calendar">
      <div class="btn btn-danger">Kalendārs <span class="material-icons">keyboard_arrow_down</span></div>
      <div class="dropdown-content calendar">
        <a href="{{ route('pieraksts') }}">Pieraksts</a>
        <a href="{{ route('rezervacijas') }}">Rezervācijas</a>
      </div>
    </div>

    <div class="dropdown-print">
      <div class="btn btn-danger" onclick="togglePrintDropdown()">Drukāt <span class="material-icons">keyboard_arrow_down</span></div>
      <div class="dropdown-content print">

        @foreach ($offices as $office)
          @php
            $visibleDays2 = 6;
            $workingDays = [];
          @endphp
          @for ($day = 0; $day < $visibleDays; $day++)
            @php
              array_push($workingDays, date('Y-m-d', strtotime(date('Y-m-d').'+' . $day . ' days')));
            @endphp
          @endfor
          @for ($day = 0; $day < $visibleDays2; $day++)
            @php
              $date1 = $workingDays[$day];
              $date2 = date('Y-m-d', strtotime($date1.'-2 days'));
              $dayOfWeek1 = $_weekDays[date('N', strtotime($date1.' 00:00:00'))];
              $dateFmt1 = date('d.m.Y', strtotime($date2.' 00:00:00'));
              $today = date('Y-m-d');

              $openTime = 0;
              $closeTime = -1;
            @endphp
            <a href="#">
              @if (date('Y-m-d', strtotime($dateFmt1)) === $today)
                <b>{{ $office->title . ' ' . $dateFmt1 }}</b>
              @else
                {{ $office->title . ' ' . $dateFmt1 }}
              @endif
            </a>
          @endfor
          @if($loop->first) <hr class="r1-hr"> @endif
        @endforeach
      </div>
    </div>

  </div>
@endif
