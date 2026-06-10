@php
  $branchLocations = collect(config('seo.organization.locations', []))->keyBy('key');
  $ulbrokaBranch = $branchLocations->get('ulbroka');
  $rigaBranch = $branchLocations->get('riga');
@endphp

<div class="catalog-seo-text" id="category-description">
  <h2 class="catalog-seo-title h5">Motociklu riepas — katalogs un montāža Rīgā un Ulbrokā</h2>
  <p>
    R1 Riepu Serviss piedāvā plašu motociklu riepu klāstu — sporta, tūrisma, skrejriteņu, krosa un citu tipu riepas.
    Katalogā varat izvēlēties riepas pēc izmēra (piemēram, 120/70 R17), ražotāja un cenas — salīdziniet sortimentu un pasūtiet riepas internetā.
    Pēc pasūtījuma izvēlieties ērtāko variantu: <strong>piegādi uz norādīto adresi</strong>, <strong>piegādi uz servisu</strong> vai <strong>saņemšanu Rīgā vai Ulbrokā</strong>.
    Ja vēlaties uzmontēt jaunās riepas, piesakiet <strong>montāžu un balansēšanu servisā</strong> —
    <a href="{{ url('/pakalpojumi') }}">skatiet montāžas cenas</a>.
  </p>
  <p>
    Motociklu riepu montāžu veicam abās filiālēs —
    <a href="{{ route($rigaBranch['route_name']) }}">{{ $rigaBranch['locality'] }}, {{ $rigaBranch['street'] }}</a> un
    <a href="{{ route($ulbrokaBranch['route_name']) }}">{{ $ulbrokaBranch['locality'] }}, {{ $ulbrokaBranch['street'] }}</a>.
    Rezervējiet laiku <a href="{{ route('pieraksts') }}">e-pierakstā</a>, un mēs parūpēsimies par moto riepu nomaiņu un balansēšanu.
    Ja neesat pārliecināti par izmēru vai tipu, speciālisti palīdzēs izvēlēties pirms vizītes.
  </p>
  <p>
    Piedāvājam motociklu riepas no pazīstamiem ražotājiem — Michelin, Pirelli, Bridgestone, Metzeler, Dunlop, Continental
    un citiem brendiem. Ja nezināt, kuras riepas izvēlēties, mūsu speciālisti palīdzēs piemeklēt piemērotu modeli
    atbilstoši jūsu motociklam un braukšanas stilam.
  </p>
</div>
