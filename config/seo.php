<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SEO keywords (AdWords high-value terms, mapped per page)
    |--------------------------------------------------------------------------
    */

    'keywords' => [
        'default' => 'riepas, riepu serviss, auto riepas, riepas lv, riepu centrs, riepas un diski, riepu veikals, riepu tirdzniecība, riepas internetā',

        'home' => 'riepas, riepu serviss, auto riepas, riepas lv, riepas un diski, riepu veikals, riepas internetā, kur pirkt riepas, nopirkt riepas',

        'pieraksts' => 'riepu maiņa, riepu maina, riepu montāža, riepu montaza, riepu montāžas cenas, riepu maiņa cena, riepu serviss, riepu remonts, riepu garaža, e-pieraksts, Rīga, Ulbroka',

        'vasaras' => 'vasaras riepas, auto riepas, riepas pirkt, pirkt riepas, riepas internetā, nopirkt riepas, kur pirkt riepas, riepu veikals, riepas lv',

        'ziemas' => 'ziemas riepas, riepas ziemas, jaunas ziemas riepas, ziemas riepas r17, vissezonas riepas r17, auto riepas, riepas pirkt, riepas lv',

        'moto' => 'moto riepas, motociklu riepas, riepas pirkt, riepas internetā, riepu veikals',

        'quadr' => 'kvadraciklu riepas, riepas, riepas pirkt, riepas internetā',

        'lielas' => 'lielas riepas, lauksaimniecības riepas, riepu tirdzniecība, riepas lv',

        'rims' => 'riepas un diski, disku remonts, disku valcēšana, lietie diski, riepu veikals',

        'atv_rims' => 'kvadraciklu diski, riepas un diski, riepu veikals',

        'studs' => 'radzes, ziemas riepas, riepu remonts',

        'akcijas' => 'akcijas riepas, izpārdošana, riepas ar atlaidi, akcijas cena, riepas internetā, riepu veikals, riepas lv, lietie diski akcija',

        'product_tire' => 'riepas pirkt, pirkt riepas, nopirkt riepas, auto riepas, riepas internetā, riepu veikals',

        'product_rim' => 'riepas un diski, disku remonts, disku valcēšana, riepu veikals',

        'brands_index' => 'riepu ražotāji, auto riepas, Michelin, Continental, GoodYear, riepas lv, riepu veikals',

        'brands_show' => 'auto riepas, riepu ražotāji, riepas pirkt, riepas internetā, riepu veikals',

        'brands_tread' => 'auto riepas, riepas pirkt, pirkt riepas, riepas internetā, riepu veikals',

        'kontakti' => 'kontakti, riepu serviss, Rīga, Ulbroka, darba laiks, e-pieraksts, riepu montāža, riepu centrs',

        'paskaidrojumi' => 'riepu kodi, LI SI, EU marķējums, riepu paskaidrojumi, auto riepas, riepu parametri',

        'shop' => 'grozs, riepas internetā, pirkt riepas, riepas ar piegādi, riepu montāža, nopirkt riepas, riepu veikals',

        'pakalpojumi' => 'riepu montāža, riepu maiņa, riepu montāžas cenas, riepu serviss, balansēšana, disku remonts, Rīga, Ulbroka',

        'kondicionieris' => 'kondicionieru uzpilde, auto kondicionieris, kondicioniera apkope, kondicioniera remonts, riepu serviss, Rīga',

        'internet_veikals' => 'riepas internetā, riepu veikals, riepas ar piegādi, nopirkt riepas, riepu tirdzniecība, riepas lv',

        'li_si_tabula' => 'LI indekss, SI indekss, riepu ātruma indekss, riepu slodzes indekss, riepu kodi, auto riepas',

        'kalkulators' => 'riepu izmēra kalkulators, riepu izmērs, alternatīvie izmēri, riepu parametri, auto riepas',

        'privatuma_politika' => 'privātuma politika, personas dati, SIA R1, riepu serviss',

        'disku_remonts' => 'disku remonts, disku valcēšana, disku restaurācija, lietie diski, riepu serviss, Rīga',

        'filiale_ulbroka' => 'riepu serviss Ulbroka, riepu maiņa Ulbroka, riepu montāža, balansēšana, Dreiliņi, riepu serviss tuvumā, e-pieraksts, R1',

        'filiale_riga' => 'riepu serviss Rīga, riepu maiņa Āgenskalns, Kalnciema iela, Pārdaugava, riepu montāža, balansēšana, riepu serviss tuvumā, e-pieraksts',
    ],

    'organization' => [
        'name' => 'R1 Riepu Serviss',
        'legal_name' => 'SIA R1',
        'url' => env('APP_URL', 'https://r1riepas.lv'),
        'logo' => '/images/favicon.png',
        'locations' => [
            [
                'key' => 'ulbroka',
                'route_name' => 'filiale-ulbroka',
                'name' => 'R1 Riepu Serviss — Ulbroka',
                'h1' => 'R1 Riepu Serviss — Ulbroka',
                'breadcrumb' => 'Ulbroka',
                'street' => 'Acones iela 2A',
                'address_line' => 'Ulbroka, Acones iela 2A, LV-2130',
                'locality' => 'Ulbroka',
                'postal_code' => 'LV-2130',
                'country' => 'LV',
                'phone' => '+37167910555',
                'phone_display' => '+371 67910555',
                'email' => 'info@r1riepas.lv',
                'latitude' => 56.9444,
                'longitude' => 24.2890,
                'maps_query' => '56.94440000,24.28898000',
                'map_icon' => 'images/kartei_u.png',
                'pieraksts_hint' => 'Ulbroka',
                'meta_title' => 'Riepu serviss Ulbrokā | R1 Riepu Serviss — Acones iela 2A',
                'meta_description' => 'R1 riepu serviss Ulbrokā — riepu maiņa, montāža un balansēšana Acones ielā 2A. Tuvumā Dreiliņi. E-pieraksts un darba laiks: P.–Pk. 9:00–18:00.',
                'meta_keywords_key' => 'filiale_ulbroka',
                'opening_hours' => [
                    ['days' => 'Mo-Fr', 'opens' => '09:00', 'closes' => '18:00'],
                ],
            ],
            [
                'key' => 'riga',
                'route_name' => 'filiale-riga',
                'name' => 'R1 Riepu Serviss — Rīga',
                'h1' => 'R1 Riepu Serviss — Rīga, Kalnciema ielā',
                'breadcrumb' => 'Rīga, Kalnciema',
                'street' => 'Kalnciema iela 39',
                'address_line' => 'Rīga, Kalnciema iela 39, LV-1046',
                'locality' => 'Rīga',
                'postal_code' => 'LV-1046',
                'country' => 'LV',
                'phone' => '+37167615615',
                'phone_display' => '+371 67615615',
                'email' => 'kalnciema@r1riepas.lv',
                'latitude' => 56.9432,
                'longitude' => 24.0655,
                'maps_query' => '56.94318810,24.06548220',
                'map_icon' => 'images/kartei_k.png',
                'pieraksts_hint' => 'Kalnciema',
                'meta_title' => 'Riepu serviss Rīgā — Kalnciema iela 39 | R1 Riepu Serviss',
                'meta_description' => 'R1 riepu serviss Rīgā, Kalnciema ielā 39 — riepu maiņa, montāža un balansēšana Āgenskalnā un Pārdaugavā. E-pieraksts. Darba laiks: P.–Pk. 9:00–18:00.',
                'meta_keywords_key' => 'filiale_riga',
                'opening_hours' => [
                    ['days' => 'Mo-Fr', 'opens' => '09:00', 'closes' => '18:00'],
                ],
            ],
        ],
    ],

];
