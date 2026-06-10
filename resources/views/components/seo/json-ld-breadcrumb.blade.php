@php
    $items = $items ?? [];
    $elements = [];

    $lastIndex = count($items) - 1;

    foreach ($items as $position => $item) {
        $entry = [
            '@type' => 'ListItem',
            'position' => $position + 1,
            'name' => $item['label'] ?? '',
        ];

        if (!empty($item['url'])) {
            $entry['item'] = $item['url'];
        } elseif ($position === $lastIndex) {
            $entry['item'] = url()->current();
        }

        $elements[] = $entry;
    }

    $schema = $elements === [] ? null : [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $elements,
    ];
@endphp
@if ($schema)
<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endif
