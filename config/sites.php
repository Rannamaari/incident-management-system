<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Site Total Counts
    |--------------------------------------------------------------------------
    |
    | The total number of sites/FBB for each network generation.
    | These values are used on the dashboard to calculate online/offline counts.
    |
    */
    'total_counts' => [
        '2g' => 300,
        '3g' => 300,
        '4g' => 250,
        '5g' => 120,
        'fbb' => 203,
    ],

    /*
    |--------------------------------------------------------------------------
    | Site Labels
    |--------------------------------------------------------------------------
    |
    | Display labels for each site type.
    |
    */
    'labels' => [
        '2g' => '2G Sites',
        '3g' => '3G Sites',
        '4g' => '4G Sites',
        '5g' => '5G Sites',
        'fbb' => 'FBB',
    ],

    /*
    |--------------------------------------------------------------------------
    | Site Color Schemes
    |--------------------------------------------------------------------------
    |
    | Tailwind CSS color classes for each site type used in the dashboard cards.
    |
    */
    'colors' => [
        '2g' => [
            'bg' => 'bg-purple-100',
            'text' => 'text-purple-600',
            'icon_bg' => 'bg-purple-100 group-hover:bg-purple-200',
            'accent' => 'from-purple-50',
        ],
        '3g' => [
            'bg' => 'bg-blue-100',
            'text' => 'text-blue-600',
            'icon_bg' => 'bg-blue-100 group-hover:bg-blue-200',
            'accent' => 'from-blue-50',
        ],
        '4g' => [
            'bg' => 'bg-green-100',
            'text' => 'text-green-600',
            'icon_bg' => 'bg-green-100 group-hover:bg-green-200',
            'accent' => 'from-green-50',
        ],
        '5g' => [
            'bg' => 'bg-red-100',
            'text' => 'text-red-600',
            'icon_bg' => 'bg-red-100 group-hover:bg-red-200',
            'accent' => 'from-red-50',
        ],
        'fbb' => [
            'bg' => 'bg-orange-100',
            'text' => 'text-orange-600',
            'icon_bg' => 'bg-orange-100 group-hover:bg-orange-200',
            'accent' => 'from-orange-50',
        ],
    ],
];
