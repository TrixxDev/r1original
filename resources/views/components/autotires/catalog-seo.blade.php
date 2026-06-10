@php
  $isWinter = ($season_title ?? '') === 'ziemas-riepas';
  $seasonName = $isWinter ? 'Ziemas' : 'Vasaras';
  $seasonNameLower = $isWinter ? 'ziemas' : 'vasaras';
  $branchLocations = collect(config('seo.organization.locations', []))->keyBy('key');
  $ulbrokaBranch = $branchLocations->get('ulbroka');
  $rigaBranch = $branchLocations->get('riga');
@endphp

<div class="catalog-seo-text" id="category-description">
  <h2 class="catalog-seo-title h5">{{ $seasonName }} riepas — katalogs un montāža Rīgā un Ulbrokā</h2>
  <p>
    R1 Riepu Serviss piedāvā plašu {{ $seasonNameLower }} riepu klāstu vieglajām automašīnām, apvidus auto, komerctransportam kā arī piekabēm.
    Katalogā varat izvēlēties riepas pēc izmēra (piemēram, 205/55 R16), ražotāja un cenas — salīdziniet sortimentu un pasūtiet riepas internetā.
    Pēc pasūtījuma izvēlieties ērtāko variantu: <strong>piegādi uz norādīto adresi</strong>, <strong>piegādi uz servisu</strong> vai <strong>saņemšanu Rīgā vai Ulbrokā</strong>.
    Ja vēlaties uzmontēt jaunās riepas, piesakiet <strong>montāžu un balansēšanu servisā</strong> —
    <a href="{{ url('/pakalpojumi') }}">skatiet montāžas cenas</a>. Riepu piegādi veicam uz jebkuru vietu Baltijas valstīs — <a href="{{ url('/interneta-veikals') }}">skatiet piegādes izmaksas</a>.
  </p>
  <p>
    Montāžu veicam abās filiālēs —
    <a href="{{ route($rigaBranch['route_name']) }}">{{ $rigaBranch['locality'] }}, {{ $rigaBranch['street'] }}</a> un
    <a href="{{ route($ulbrokaBranch['route_name']) }}">{{ $ulbrokaBranch['locality'] }}, {{ $ulbrokaBranch['street'] }}</a>.
    Rezervējiet laiku <a href="{{ route('pieraksts') }}">e-pierakstā</a>, un mēs parūpēsimies par riepu nomaiņu, balansēšanu un spiediena pārbaudi.
    Abos servisos iespējama arī riepu uzglabāšana un konsultācija par piemērotāko izvēli.
  </p>
  <p>
    Piedāvājam {{ $seasonNameLower }} riepas no pazīstamiem ražotājiem — Michelin, Continental, GoodYear, Pirelli, Nokian, Hankook,
    Lassa, Sailun, Triangle un citiem brendiem. Ja nezināt, kuras riepas izvēlēties, mūsu speciālisti palīdzēs piemeklēt
    piemērotu modeli atbilstoši jūsu auto un braukšanas stilam.
  </p>
</div>

