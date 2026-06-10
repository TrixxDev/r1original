@php
    $name = $name ?? '';
    $brandName = $brandName ?? '';
    $description = $description ?? '';
    $url = $url ?? url()->current();
    $image = $image ?? null;
    $lowPrice = $lowPrice ?? null;
    $highPrice = $highPrice ?? null;
    $offerCount = (int) ($offerCount ?? 0);

    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $name,
        'description' => $description,
        'url' => $url,
        'brand' => [
            '@type' => 'Brand',
            'name' => $brandName,
        ],
    ];

    if ($image) {
        $schema['image'] = $image;
    }

    if ($lowPrice !== null && $offerCount > 0) {
        $offer = [
            '@type' => 'AggregateOffer',
            'priceCurrency' => 'EUR',
            'lowPrice' => (string) $lowPrice,
            'offerCount' => $offerCount,
            'availability' => 'https://schema.org/InStock',
            'url' => $url,
        ];

        if ($highPrice !== null && $highPrice != $lowPrice) {
            $offer['highPrice'] = (string) $highPrice;
        }

        $schema['offers'] = $offer;
    }
@endphp
<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
