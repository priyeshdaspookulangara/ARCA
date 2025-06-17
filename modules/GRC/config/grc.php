<?php

return [
    'name' => 'ARCAGRC',
    'description' => 'ARCA Governance, Risk, and Compliance Module.',

    'sod_analysis' => [
        'default_risk_level_for_conflict' => 'High',
        'realtime_analysis_enabled' => false, // Requires significant performance consideration
    ],

    'process_control' => [
        'ccm_default_alert_recipient_role' => 'ControlMonitor',
    ],

    'risk_management' => [
        'default_risk_assessment_frequency_months' => 12,
    ],

    'audit_management' => [
        'default_audit_finding_severity' => 'Medium',
    ],

    'features' => [
        'enable_policy_attestation' => true,
        'enable_ccm' => true,
    ]
];
