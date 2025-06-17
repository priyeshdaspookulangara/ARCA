<?php

return [
    'name' => 'ARCA EWM',
    'description' => 'ARCA Extended Warehouse Management Module',
    'warehouse_defaults' => [
        'default_putaway_strategy' => 'EmptyBin',
        'default_picking_strategy' => 'FIFO',
    ],
    'rf_settings' => [
        'theme' => 'high_contrast',
        'default_menu' => 'main_rf_menu',
    ],
    // Feature flags for EWM sub-functionalities
    'features' => [
        'enable_yard_management' => true,
        'enable_vas' => true,
        'enable_cross_docking' => true,
        'enable_mfs_integration' => false, // Default to false
    ]
];
