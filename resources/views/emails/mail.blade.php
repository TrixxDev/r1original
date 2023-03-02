<!DOCTYPE html>
<html>
<body>
<p>
  Jūsu R1 riepu servisa apmeklējuma rezervācijas detaļas:<br>
  Automašīnas {{ $details['car'] }} - {{ $details['make'] }} {{ $details['purpose'] }}<br>
  R1 riepu servisā {{ $details['office'] }}, {{ $details['day'] }}, {{ $details['date'] }}, pl. {{ $details['time'] }}<br>
  {{ $details['longPurpose'] }}<br>
  <br>
  Ar pakalpojumu cenām iespējams iepazīties šeit: https://r1riepas.lv/pakalpojumi<br>
  <br>
  Nepieciešamības gadījumā pierakstu iespējams anulēt izmantojot šo saiti - https://r1riepas.lv/pieraksts/cancel={{ $details['cancelId'] }}<br>
  Pieraksta anulēšana iespējama līdz pieraksta dienas pl. 7:30, ja nepieciešams rediģet pierakstu<br>
  pēc 7:30, lūdzu, sazinieties ar mums pa tālruni +37167910555 vai +37167615615<br>
  <br>
  Drošu ceļu vēlot,<br>
  R1<br>
</p>
</body>
</html>
