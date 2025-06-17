<?php

return [
    'name' => 'ARCAMDG',
    'description' => 'ARCA Master Data Governance Module.',

    'governed_objects' => [
        'material' => ['workflow_definition' => 'material_default_workflow'],
        'business_partner' => ['workflow_definition'ika => 'bp_default_workflow'],
        'gl_account' => ['workflow_definition' => 'financial_master_workflow'],
        // Add other governed objects and their default workflow keys
    ],

    'data_quality' => [
        'default_match_threshold_percent' => 85,
    ],

    'replication' => [
        'default_method' => 'event', // 'event' or 'batch'
        'batch_schedule_cron' => '0 2 * * *', // Default cron for batch replication
    ],
];
