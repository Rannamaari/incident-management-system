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
    | Temporary Sites - Calculated Dynamically
    |--------------------------------------------------------------------------
    |
    | Temporary sites are counted from the database based on their coverage field.
    | The dashboard calculates online/offline status based on is_Xg_online flags.
    |
    */
    'temp_sites_enabled' => true,

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
        'temp_sites' => 'Temp Sites',
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
            'bg' => 'bg-gradient-to-r from-purple-500 to-purple-600',
            'text' => 'text-purple-700',
            'icon_bg' => 'bg-gradient-to-br from-purple-500 to-purple-600 group-hover:from-purple-600 group-hover:to-purple-700 shadow-lg',
            'accent' => 'from-purple-500',
        ],
        '3g' => [
            'bg' => 'bg-gradient-to-r from-blue-500 to-blue-600',
            'text' => 'text-blue-700',
            'icon_bg' => 'bg-gradient-to-br from-blue-500 to-blue-600 group-hover:from-blue-600 group-hover:to-blue-700 shadow-lg',
            'accent' => 'from-blue-500',
        ],
        '4g' => [
            'bg' => 'bg-gradient-to-r from-emerald-500 to-green-600',
            'text' => 'text-emerald-700',
            'icon_bg' => 'bg-gradient-to-br from-emerald-500 to-green-600 group-hover:from-emerald-600 group-hover:to-green-700 shadow-lg',
            'accent' => 'from-emerald-500',
        ],
        '5g' => [
            'bg' => 'bg-gradient-to-r from-red-500 to-rose-600',
            'text' => 'text-red-700',
            'icon_bg' => 'bg-gradient-to-br from-red-500 to-rose-600 group-hover:from-red-600 group-hover:to-rose-700 shadow-lg',
            'accent' => 'from-red-500',
        ],
        'fbb' => [
            'bg' => 'bg-gradient-to-r from-orange-500 to-amber-600',
            'text' => 'text-orange-700',
            'icon_bg' => 'bg-gradient-to-br from-orange-500 to-amber-600 group-hover:from-orange-600 group-hover:to-amber-700 shadow-lg',
            'accent' => 'from-orange-500',
        ],
        'temp_sites' => [
            'bg' => 'bg-gradient-to-r from-teal-500 to-cyan-600',
            'text' => 'text-teal-700',
            'icon_bg' => 'bg-gradient-to-br from-teal-500 to-cyan-600 group-hover:from-teal-600 group-hover:to-cyan-700 shadow-lg',
            'accent' => 'from-teal-500',
        ],
    ],
];
