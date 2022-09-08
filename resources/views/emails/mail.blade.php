<!DOCTYPE html>
<html>
<body>
<p>
  Jūsu R1 riepu servisa apmeklējuma rezervācijas detaļas:<br>
  Automašīnas {{ $details['car'] }} - {{ $details['make'] }} {{ $details['purpose'] }}<br>
  R1 riepu servisā {{ $details['office'] }}, {{ $details['day'] }}, {{ $details['date'] }}, pl. {{ $details['time'] }}<br>
  {{ $details['longPurpose'] }}<br>
  <br>
  !!! Ja netiksiet šajā laikā vai radušies kādi citi jautājumi<br>
  * Zvaniet uz riepu servisu {{ $details['office'] }} - 67910555<br>
  <br>
  Aktuālie pakalpojumu izcenojums Šeit: {{ route('pakalpojumi') }}<br>
  <br>
  Drošu ceļu vēlot,<br>
  R1<br>
</p>
</body>
</html>
