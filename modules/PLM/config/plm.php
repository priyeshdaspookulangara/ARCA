<?php

return [
    'name' => 'ARCAPLM',
    'description' => 'ARCA Product Lifecycle Management Module.',

    'versioning_scheme' => [
        'item' => 'major_minor', // e.g., 1.0, 1.1, 2.0
        'document' => 'sequential_revision', // e.g., A, B, C then 1, 2, 3 for major
    ],

    'change_management' => [
        'default_ecr_workflow' => 'standard_ecr_flow',
        'default_eco_workflow' => 'standard_eco_flow',
    ],

    'file_storage' => [
        'default_disk' => 'local_plm_files', // Must match a disk defined in filesystems.php
        // Consider adding a specific disk for PLM for better separation or cloud storage
    ],

    'features' => [
        'enable_npi_ps_integration' => true,
        'enable_collaboration_tools' => true,
    ]
];
