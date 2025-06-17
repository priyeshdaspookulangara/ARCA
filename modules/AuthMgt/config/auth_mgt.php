<?php

return [
    'name' => 'AuthorizationManagement',
    'description' => 'ARCA User Role and Authorization Management Module.',

    'password_policies' => [
        'min_length' => 12,
        'require_uppercase' => true,
        'require_lowercase' => true,
        'require_numeric' => true,
        'require_special_char' => true,
        'history_count' => 5, // Cannot reuse last 5 passwords
        'expiry_days' => 90, // Password expires after 90 days
    ],

    'session_management' => [
        'dialog_user_timeout_minutes' => 30,
        'allow_multiple_dialog_logons' => false, // Or 'terminate_previous'
    ],

    'cua_settings' => [
        'is_cua_master' => false,
        'master_system_url' => null,
    ],

    'audit_log' => [
        'log_failed_authorizations' => false, // Can be verbose
        'retention_days' => 365,
    ],

    'emergency_access' => [
        'default_session_duration_minutes' => 240, // 4 hours
    ],
];
