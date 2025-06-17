<?php

return [
    'name' => 'ISRetail',
    'description' => 'ARCA Industry Solution for Retail, Apparel & Footwear.',
    'masterdata' => [
        'variant_characteristics_sets' => [
            'apparel' => ['COLOR', 'SIZE', 'FIT'],
            'footwear' => ['COLOR', 'SIZE', 'WIDTH'],
        ],
        'default_season_type' => 'MAIN_SEASON',
    ],
    'features' => [
        'enable_matrix_ui_for_variants' => true,
    ]
];
