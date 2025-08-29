<?php

return [
    'name' => 'FinCheck',
    'manifest' => [
        'name' => env('APP_NAME', 'My PWA App'),
        'short_name' => 'FinCheck',
        'start_url' => '/',
        'background_color' => '#ffffff',
        'theme_color' => '#22c55e',
        'display' => 'standalone',
        'orientation' => 'any',
        'status_bar' => 'black',
        'icons' => [
            '72x72' => [
                'path' => '/favicon.png',
                'purpose' => 'any'
            ],
            '96x96' => [
                'path' => '/favicon.png',
                'purpose' => 'any'
            ],
            '128x128' => [
                'path' => '/favicon.png',
                'purpose' => 'any'
            ],
            '144x144' => [
                'path' => '/favicon.png',
                'purpose' => 'any'
            ],
            '152x152' => [
                'path' => '/favicon.png',
                'purpose' => 'any'
            ],
            '192x192' => [
                'path' => '/favicon.png',
                'purpose' => 'any'
            ],
            '384x384' => [
                'path' => '/favicon.png',
                'purpose' => 'any'
            ],
            '512x512' => [
                'path' => '/favicon.png',
                'purpose' => 'any'
            ],
        ],
        'splash' => [
            '640x1136' => '/favicon.png',
            '750x1334' => '/favicon.png',
            '828x1792' => '/favicon.png',
            '1125x2436' => '/favicon.png',
            '1242x2208' => '/favicon.png',
            '1242x2688' => '/favicon.png',
            '1536x2048' => '/favicon.png',
            '1668x2224' => '/favicon.png',
            '1668x2388' => '/favicon.png',
            '2048x2732' => '/favicon.png',
        ],
        'shortcuts' => [
            [
                'name' => 'Incomes',
                'description' => 'Lihat dan kelola semua pemasukan keuangan Anda',
                'url' => '/finances/incomes',
                'icons' => [
                    'src' => '/favicon.png',
                    'purpose' => 'any',
                ],
            ],
            [
                'name' => 'Expenses',
                'description' => 'Catat dan pantau pengeluaran harian Anda',
                'url' => '/finances/expenses',
            ],
        ],
        'custom' => []
    ]
];
