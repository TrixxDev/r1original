@php
  $branchLocations = collect(config('seo.organization.locations', []))->keyBy('key');
  $ulbrokaBranch = $branchLocations->get('ulbroka');
  $rigaBranch = $branchLocations->get('riga');
@endphp

<div class="catalog-seo-text" id="category-description">
  <h2 class="catalog-seo-title h5">Kvadraciklu riepas — katalogs un montāža Rīgā un Ulbrokā</h2>
  <p>
    R1 Riepu Serviss piedāvā kvadraciklu (ATV) riepas dažādiem izmēriem un ražotājiem.
    Katalogā varat izvēlēties riepas pēc izmēra (piemēram, 25x8-12), brenda un cenas — salīdziniet sortimentu un pasūtiet riepas internetā.
    Pēc pasūtījuma izvēlieties ērtāko variantu: <strong>piegādi uz norādīto adresi</strong>, <strong>piegādi uz servisu</strong> vai <strong>saņemšanu Rīgā vai Ulbrokā</strong>.
    Ja vēlaties uzmontēt jaunās riepas, piesakiet <strong>montāžu servisā</strong> —
    <a href="{{ url('/pakalpojumi') }}">skatiet montāžas cenas</a>.
  </p>
  <p>
    Kvadraciklu riepu montāžu veicam abās filiālēs —
    <a href="{{ route($rigaBranch['route_name']) }}">{{ $rigaBranch['locality'] }}, {{ $rigaBranch['street'] }}</a> un
    <a href="{{ route($ulbrokaBranch['route_name']) }}">{{ $ulbrokaBranch['locality'] }}, {{ $ulbrokaBranch['street'] }}</a>.
    Rezervējiet laiku <a href="{{ route('pieraksts') }}">e-pierakstā</a>, un mēs parūpēsimies par kvadracikla riepu nomaiņu.
    Pēc pasūtījuma riepas varat saņemt servisā vai pasūtīt piegādi uz norādīto adresi.
  </p>
  <p>
    Piedāvājam kvadraciklu riepas no pazīstamiem ražotājiem — Carlisle, Maxxis, ITP, Kenda, SunF un citiem brendiem.
    Ja nezināt, kuras riepas izvēlēties, mūsu speciālisti palīdzēs piemeklēt piemērotu modeli jūsu kvadraciklam un braukšanas apstākļiem.
  </p>
</div>
