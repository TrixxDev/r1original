<!DOCTYPE html>
<html>
<body>
<p>
Jauns pasūtījums:<br>
Pamatinformacija<br>
Vārds, uzvārds: {{ $details->info['name'] . ', ' . $details->info['surname'] }}<br>
e-pasts: {{ $details->info['email'] }}<br>
Tālrunis: {{ $details->info['phone_number'] }}<br>
Saņemšanas vieta: @if($details->info['fitting_address'] == 1) Ulbrokā, Acones iela 2A @elseif ($details->info['fitting_address'] == 2) Rīgā, Kalnciema iela 39 @endif
<br><br>
Papildus informācija<br>
Piezīmes: {{  }}<br>
<br>
Apmaksas veids: @if ($details->payment == 1) Apmaksa saņemšanas brīdī @elseif ($details->payment == 2) Bankas pārskaitījums @else Tiešsaistes apmaksa @endif<br><br><br>


Pasūtītās preces<br>
@foreach (\Cart::content() as $items)

Triangle TR619 141139M (10R22.5, skaits: 4 gab., summa: 1100,00 EUR<br><br>

@endforeach
Kopsumma: 1100,00 EUR<br><br>

//Apskatīt pasūtījumu: http://r1-dev.stormlv.eu/orders/157
</p>
</body>
</html>

