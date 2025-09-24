<?php

return [
    'name' => 'Fina',
    'description' => 'Finance and Controlling Module',
    // Add Fina-specific configurations here
    'default_company_code' => '1000',
    'feature_flags' => [
        'use_product_costing' => true,
        'enable_abc_costing' => false,
    ],
    'bank_accounting' => [
        'default_bank_country' => 'US',
    ],
];
