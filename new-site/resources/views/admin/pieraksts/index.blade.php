@extends('admin.layouts.app')

@section('title', 'Pieraksts '.$day->format('d.m.Y'))

@php
    $dayTitles = [1 => 'Pirmdiena', 2 => 'Otrdiena', 3 => 'Trešdiena', 4 => 'Ceturtdiena', 5 => 'Piektdiena', 6 => 'Sestdiena', 7 => 'Svētdiena'];
@endphp

@section('content')
    <div class="day-nav">
        <a class="btn" href="{{ route('admin.pieraksts') }}">Šodien</a>
        <a class="btn" href="{{ route('admin.pieraksts.date', $day->copy()->subDay()->toDateString()) }}">←</a>
        <a class="btn" href="{{ route('admin.pieraksts.date', $day->copy()->addDay()->toDateString()) }}">→</a>
        <h1>{{ $dayTitles[(int) $day->format('N')] }}, {{ $day->format('d.m.Y') }}</h1>
        <input type="date" value="{{ $date }}" id="date-picker">
    </div>

    <div class="day-grid"
         data-date="{{ $date }}"
         data-block-url="{{ route('admin.pieraksts.block') }}"
         data-unblock-url="{{ route('admin.pieraksts.unblock') }}"
         data-comment-url="{{ route('admin.pieraksts.comment') }}"
         data-cancel-url="{{ route('admin.pieraksts.cancel') }}">
        @foreach ($grid as $officeBlock)
            <table class="queue-table">
                <thead>
                <tr>
                    <th class="office-name" colspan="{{ count($officeBlock['queues']) + 1 }}">{{ $officeBlock['office']->title }}</th>
                </tr>
                <tr>
                    <th class="time-col"></th>
                    @foreach ($officeBlock['queues'] as $qb)
                        <th>{{ $qb['queue']->title ?: 'Rinda '.$qb['queue']->id }}
                            @unless ($qb['queue']->is_public)<small>(iekšējā)</small>@endunless
                        </th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                @foreach ($times as $time)
                    <tr>
                        <td class="time-col">{{ $time }}</td>
                        @foreach ($officeBlock['queues'] as $qb)
                            @php $cell = $qb['cells'][$time]; @endphp
                            @if ($cell['state'] === 'closed')
                                <td class="cell cell--closed"></td>
                            @else
                                <td class="cell cell--{{ $cell['state'] }}"
                                    data-queue-id="{{ $qb['queue']->id }}"
                                    data-position="{{ $cell['position'] }}"
                                    data-time="{{ $time }}"
                                    data-state="{{ $cell['state'] }}"
                                    @isset($cell['slot_id']) data-slot-id="{{ $cell['slot_id'] }}" @endisset
                                    @isset($cell['comment']) data-comment="{{ $cell['comment'] }}" @endisset
                                    @isset($cell['booking']) data-booking='@json($cell['booking'])' @endisset>
                                    @if ($cell['state'] === 'booked')
                                        <b>{{ $cell['booking']['car'] }}</b><br>
                                        {{ $cell['booking']['plate'] }} · {{ $cell['booking']['phone'] }}
                                    @elseif ($cell['state'] === 'blocked')
                                        Slēgts
                                    @elseif ($cell['state'] === 'reserved')
                                        Rezervēts…
                                    @elseif ($cell['state'] === 'discount')
                                        {{ $cell['comment'] }}
                                    @else
                                        &nbsp;
                                    @endif
                                </td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endforeach
    </div>

    {{-- Панель деталей слота --}}
    <aside id="slot-panel" hidden>
        <div class="slot-panel__inner">
            <button type="button" class="slot-panel__close" aria-label="Aizvērt">×</button>
            <h2 id="sp-title"></h2>
            <div id="sp-booking" hidden>
                <dl>
                    <dt>Auto</dt><dd id="sp-car"></dd>
                    <dt>Numurs</dt><dd id="sp-plate"></dd>
                    <dt>Klients</dt><dd id="sp-name"></dd>
                    <dt>Tālrunis</dt><dd id="sp-phone"></dd>
                    <dt>E-pasts</dt><dd id="sp-email"></dd>
                    <dt>Pakalpojums</dt><dd id="sp-service"></dd>
                    <dt>Piezīmes</dt><dd id="sp-comment"></dd>
                    <dt>Izveidots</dt><dd id="sp-created"></dd>
                </dl>
                <button type="button" class="btn btn--danger" id="sp-cancel-booking">Atcelt pierakstu</button>
            </div>
            <div id="sp-free" hidden>
                <label>Atlaide / komentārs:
                    <input type="text" id="sp-discount" maxlength="100" placeholder="piem. -20%">
                </label>
                <button type="button" class="btn" id="sp-save-discount">Saglabāt</button>
                <hr>
                <button type="button" class="btn btn--danger" id="sp-block">Bloķēt laiku</button>
            </div>
            <div id="sp-blocked" hidden>
                <p>Laiks ir bloķēts.</p>
                <button type="button" class="btn" id="sp-unblock">Atbloķēt</button>
            </div>
        </div>
    </aside>
@endsection
