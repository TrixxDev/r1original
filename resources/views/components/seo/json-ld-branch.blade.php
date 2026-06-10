@php
    $org = config('seo.organization');
    $baseUrl = rtrim((string) ($org['url'] ?? config('app.url')), '/');
    $routeName = $branch['route_name'] ?? null;
    $openingHours = [];

    foreach ($branch['opening_hours'] ?? [] as $hours) {
        $openingHours[] = [
            '@type' => 'OpeningHoursSpecification',
            'dayOfWeek' => $hours['days'],
            'opens' => $hours['opens'],
            'closes' => $hours['closes'],
        ];
    }

    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'AutoRepair',
        'name' => $branch['name'],
        'url' => $routeName ? route($routeName) : url()->current(),
        'telephone' => $branch['phone'],
        'image' => asset(ltrim((string) ($org['logo'] ?? '/images/favicon.png'), '/')),
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => $branch['street'],
            'addressLocality' => $branch['locality'],
            'postalCode' => $branch['postal_code'],
            'addressCountry' => $branch['country'],
        ],
        'geo' => [
            '@type' => 'GeoCoordinates',
            'latitude' => $branch['latitude'],
            'longitude' => $branch['longitude'],
        ],
    ];

    if ($openingHours !== []) {
        $schema['openingHoursSpecification'] = $openingHours;
    }
@endphp
<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
