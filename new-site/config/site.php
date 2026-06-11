<?php

// Сезонные и контактные настройки сайта (бывш. config('site.season') в старом проекте).
// season: 1 = лето (в меню первой показывается «Vasaras riepas», фон cover.webp),
//         иначе зима (первой «Ziemas riepas», фон cover3.webp).
return [
    'season' => (int) env('SITE_SEASON', 1),

    'phones' => [
        'ulbroka' => '67910555',
        'kalnciema' => '67615615',
    ],

    'whatsapp' => '37128336677',
];
