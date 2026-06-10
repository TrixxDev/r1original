@php
    $hoursHeading = $branch['key'] === 'ulbroka'
        ? 'Darba laiks Ulbrokā'
        : 'Darba laiks Kalnciema ielā';
    $mapsUrl = 'https://www.google.com/maps/search/?api=1&query=' . $branch['maps_query'];
    $tableFont = "font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;";
@endphp

<div class="contactsContainer">
    <div class="contactsRow">
        <div class="contactsCol-12 contactsCol-sm-6">
            <table class="contacts table" style="{{ $tableFont }}" border="0">
                <thead>
                    <tr>
                        <th>{{ $branch['pieraksts_hint'] }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <a href="{{ $mapsUrl }}" target="_blank" rel="noopener">{{ $branch['address_line'] }}</a>
                        </td>
                    </tr>
                    <tr>
                        <td><a href="tel:{{ $branch['phone'] }}"><strong>{{ $branch['phone_display'] }}</strong></a></td>
                    </tr>
                    <tr>
                        <td><a href="mailto:{{ $branch['email'] }}">{{ $branch['email'] }}</a></td>
                    </tr>
                </tbody>
            </table>
            <table class="contacts table" style="{{ $tableFont }}" border="0">
                <thead>
                    <tr>
                        <th>{{ $hoursHeading }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Pirm. - Piekt. <strong>9:00 - 18:00</strong></td>
                    </tr>
                    <tr>
                        <td>Sestdiena - <strong>Slēgts</strong></td>
                    </tr>
                    <tr>
                        <td>Svētdiena - <strong>Slēgts</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="contactsCol-12 contactsCol-sm-6">
            <table class="contacts table" style="{{ $tableFont }}" border="0">
                <thead>
                    <tr>
                        <th>Pakalpojumi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>Riepu maiņa un montāža</td></tr>
                    <tr><td>Disku balansēšana</td></tr>
                    <tr><td>Riepu remonts</td></tr>
                    <tr><td>Disku remonts un valcēšana</td></tr>
                    <tr><td>Riepu uzglabāšana</td></tr>
                    <tr><td>Riepu un disku tirdzniecība</td></tr>
                </tbody>
            </table>
            <table class="contacts table" style="{{ $tableFont }}" border="0">
                <thead>
                    <tr>
                        <th>E-pieraksts</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <a href="{{ route('pieraksts') }}"><strong>Pieteikties uz vizīti</strong></a>
                        </td>
                    </tr>
                    <tr>
                        <td>Izvēlieties filiāli <strong>{{ $branch['pieraksts_hint'] }}</strong></td>
                    </tr>
                    <tr>
                        <td><a href="{{ url('/kontakti') }}">Visas filiāles un kontakti</a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

