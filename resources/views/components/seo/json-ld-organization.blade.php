@php
    $org = config('seo.organization');
    $baseUrl = rtrim((string) ($org['url'] ?? config('app.url')), '/');
    $locations = [];

    foreach ($org['locations'] ?? [] as $loc) {
        $locations[] = [
            '@type' => 'AutoRepair',
            'name' => $loc['name'],
            'telephone' => $loc['phone'],
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $loc['street'],
                'addressLocality' => $loc['locality'],
                'postalCode' => $loc['postal_code'],
                'addressCountry' => $loc['country'],
            ],
            'geo' => [
                '@type' => 'GeoCoordinates',
                'latitude' => $loc['latitude'],
                'longitude' => $loc['longitude'],
            ],
        ];
    }

    $schema = [
        '@context' => 'https://schema.org',
        '@graph' => array_merge([
            [
                '@type' => 'Organization',
                '@id' => $baseUrl . '/#organization',
                'name' => $org['name'],
                'legalName' => $org['legal_name'] ?? $org['name'],
                'url' => $baseUrl,
                'logo' => asset(ltrim((string) ($org['logo'] ?? '/images/favicon.png'), '/')),
            ],
            [
                '@type' => 'WebSite',
                '@id' => $baseUrl . '/#website',
                'url' => $baseUrl,
                'name' => $org['name'],
                'publisher' => ['@id' => $baseUrl . '/#organization'],
            ],
        ], $locations),
    ];
@endphp
<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
