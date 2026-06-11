<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>R1 Riepu Serviss - Pieraksta atcelšana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <style>
        .confirm-delete-body {
            text-align: center;
            margin-bottom: 45px;
            font-size: calc(1.5rem + 1vw);
        }
        .cancelQ { color: #e30000; font-size: 3.5rem; position: relative; top: 15px; }
        input[name="plate_suffix"] { height: 86px; width: 100%; }
        button[name="delete"] { width: 75%; font-size: 3rem; }
        @media (max-width: 768px) {
            .confirm-delete-body { font-size: 2.8rem; }
            .confirm-delete-col { margin-top: 100px; }
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="row">
        <form method="post" action="{{ route('pieraksts.cancel.confirm', $booking->cancel_code) }}">
            @csrf
            <div class="col confirm-delete-col">
                <div class="confirm-delete-body">
                    <div>
                        Jūsu pieraksts:
                        {{ $office->title }}, {{ ['', 'pirmdien', 'otrdien', 'trešdien', 'ceturtdien', 'piektdien', 'sestdien', 'svētdien'][(int) $booking->slot->date->format('N')] }},
                        {{ $booking->slot->date->format('d.m.Y') }}@if ($time), pl. {{ $time }}@endif <br>
                        Automašīnai: {{ $booking->car_brand }} {{ $booking->car_model }} <br>
                        <span class="cancelQ">Vai vēlaties atcelt pierakstu?</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col" style="text-align: center; width: 100%;">
                        @if (session('danger'))
                            <span style="font-size: 2.8rem; color: red;">{!! session('danger') !!}</span><br><br>
                        @endif
                    </div>
                </div>
                <div class="row" style="text-align: center;">
                    <div class="col">
                        <input class="form-control" type="text" name="plate_suffix" required
                               placeholder="Lai apstiprinātu atcelšanu, ievadiet auto numura zīmi">
                    </div>
                    <div class="col">
                        <button class="btn btn-primary" type="submit" name="delete">Apstiprināt</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

</body>
</html>
