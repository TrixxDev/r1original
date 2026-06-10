@php
    $mapId = 'map_branch_' . ($branch['key'] ?? 'default');
    $mapsUrl = 'https://www.google.com/maps/search/?api=1&query=' . ($branch['maps_query'] ?? '');
    $coords = explode(',', (string) ($branch['maps_query'] ?? '0,0'));
    $lat = (float) ($coords[0] ?? $branch['latitude']);
    $lng = (float) ($coords[1] ?? $branch['longitude']);
@endphp

<div id="{{ $mapId }}" style="min-height: 450px; width: 100%; position: relative; overflow: hidden;" aria-label="Karte: {{ $branch['address_line'] ?? '' }}"></div>

<script>
(function () {
    const mapId = @json($mapId);
    const markerData = {
        coords: { lat: {{ $lat }}, lng: {{ $lng }} },
        text: @json(
            ($branch['street'] ?? '') . ', ' . ($branch['locality'] ?? '') . ', ' . ($branch['postal_code'] ?? '') .
            '<br> Tālr.: <a href="tel:' . ($branch['phone'] ?? '') . '"><strong>' . ($branch['phone_display'] ?? '') . '</strong></a><br><br>' .
            '<a style="text-transform: uppercase;" href="' . $mapsUrl . '" target="_blank" rel="noopener"><strong>Atvērt kartē</strong></a>'
        ),
        icon: @json(asset($branch['map_icon'] ?? 'images/kartei_u.png')),
    };

    function initBranchMap() {
        const el = document.getElementById(mapId);
        if (!el || typeof google === 'undefined' || !google.maps) {
            return;
        }

        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        const map = new google.maps.Map(el, {
            zoom: isMobile ? 15 : 16,
            center: markerData.coords,
            gestureHandling: 'greedy',
            mapTypeControl: !isMobile,
            streetViewControl: !isMobile,
            fullscreenControl: !isMobile,
        });

        const marker = new google.maps.Marker({
            position: markerData.coords,
            map: map,
            icon: new google.maps.MarkerImage(markerData.icon, new google.maps.Size(25, 34)),
        });

        const infowindow = new google.maps.InfoWindow({ content: markerData.text });
        marker.addListener('click', function () {
            infowindow.open(map, marker);
        });
    }

    if (typeof google !== 'undefined' && google.maps) {
        initBranchMap();
    } else {
        document.addEventListener('mapLoaded', initBranchMap, { once: true });
    }
})();
</script>

