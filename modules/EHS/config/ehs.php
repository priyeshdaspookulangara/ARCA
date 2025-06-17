<?php

return [
    'name' => 'ARCAEHS',
    'description' => 'ARCA Environmental, Health, and Safety Module.',

    'incident_management' => [
        'default_severity' => 'medium',
        'auto_notify_safety_officer_on_severity' => ['high', 'critical'],
    ],

    'risk_assessment' => [
        'default_risk_matrix_id' => 'standard_5x5',
    ],

    'document_management_integration' => [
        'sds_document_type_in_dms' => 'SAFETY_DATA_SHEET',
    ],

    'features' => [
        'enable_occupational_health' => true,
        'enable_emissions_tracking' => true,
    ]
];
