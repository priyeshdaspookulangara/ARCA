<?php

return [
    'name' => 'LSCM',
    'description' => 'Logistics & Supply Chain Management Module',
    // Enable/disable LSCM sub-modules
    'mm' => ['enabled' => true],
    'sd' => ['enabled' => true],
    'pp' => ['enabled' => true],
    'pm' => ['enabled' => true],
    'qm' => ['enabled' => true],

    // Example sub-module specific config
    'mm_config' => [
        'default_procurement_type' => 'standard_po',
    ],
    'sd_config' => [
        'default_sales_order_type' => 'OR',
    ],
];
