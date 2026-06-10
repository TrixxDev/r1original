<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pieraksta atcelšana — R1 Riepas</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 480px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 1.5rem; }
        .flash-danger { background: #fee2e2; color: #991b1b; padding: .75rem 1rem; border-radius: 6px; margin-bottom: 1rem; }
        input { width: 100%; padding: .5rem; box-sizing: border-box; margin: .5rem 0 1rem; }
        button { padding: .5rem 1.25rem; }
    </style>
</head>
<body>
<div class="card">
    <h1>Pieraksta atcelšana</h1>

    @if (session('danger'))
        <div class="flash-danger">{!! session('danger') !!}</div>
    @endif

    <p>
        <b>{{ $booking->slot->date->format('d.m.Y') }}@if ($time) plkst. {{ $time }}@endif</b><br>
        {{ $office->title }}<br>
        {{ $booking->car_brand }} {{ $booking->car_model }}
    </p>

    <form method="post" action="{{ route('pieraksts.cancel.confirm', $booking->cancel_code) }}">
        @csrf
        <label for="plate_suffix">Drošībai ievadiet auto numura pēdējos 2 simbolus:</label>
        <input id="plate_suffix" name="plate_suffix" maxlength="20" required>
        <button type="submit">Atcelt pierakstu</button>
    </form>
</div>
</body>
</html>
