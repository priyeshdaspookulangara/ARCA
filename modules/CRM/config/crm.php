<?php

return [
    'name' => 'CRM',
    'description' => 'Customer Relationship Management Module',
    // Add CRM-specific configurations here
    'default_lead_status' => 'New',
    'feature_flags' => [
        'enable_mobile_optimizations' => true,
        'use_advanced_segmentation' => false,
    ]
];
