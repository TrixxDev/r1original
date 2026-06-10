@php
    $name = $name ?? '';
    $brandName = $brandName ?? '';
    $description = $description ?? '';
    $url = $url ?? url()->current();
    $image = $image ?? null;
    $price = $price ?? null;
    $dotAvailable = strtolower((string) ($dotAvailable ?? ''));

    if (in_array($dotAvailable, ['green', 'half-green'], true)) {
        $availability = 'https://schema.org/InStock';
    } elseif (in_array($dotAvailable, ['yellow', 'half-yellow'], true)) {
        $availability = 'https://schema.org/PreOrder';
    } else {
        $availability = 'https://schema.org/OutOfStock';
    }

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

    if ($price !== null && $price !== '') {
        $schema['offers'] = [
            '@type' => 'Offer',
            'priceCurrency' => 'EUR',
            'price' => (string) $price,
            'availability' => $availability,
            'url' => $url,
        ];
    }
@endphp
<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>

