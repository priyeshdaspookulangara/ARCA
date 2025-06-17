# Authorization Module: Data Model Design (MySQL)

This document outlines the proposed MySQL database schema for the User Role and Authorization Management Module. All tables specific to this module will use the `auth_` prefix.

## 1. General Principles

*   **Prefixing:** All tables are prefixed with `auth_`.
*   **Normalization:** Aims for a good level of normalization to maintain data integrity, with considerations for performance in authorization checks.
*   **Auditability:** Key tables include standard audit columns (`created_at`, `updated_at`, `created_by_user_id`, `updated_by_user_id`).
*   **User Linkage:** Assumes a `core_users` table might exist for very basic user identity (like employee ID from HR). The `auth_users` table will extend this with authorization-specific attributes or fully define users if no such core table exists. For this model, we'll assume `auth_users` is the primary user definition for security.

## 2. User Master Data

*   **`auth_users`**
    *   `id` (PK)
    *   `user_id_erp` (UK, User's unique login ID, e.g., "JOHNDOE")
    *   `full_name` (VARCHAR)
    *   `email` (VARCHAR, UK)
    *   `user_type` (ENUM: 'Dialog', 'System', 'Communication', 'Reference', 'Service')
    *   `password_hash` (VARCHAR, securely hashed password for Dialog/Service users)
    *   `password_last_changed_at` (TIMESTAMP)
    *   `password_expires_at` (TIMESTAMP, nullable)
    *   `password_reset_required` (Boolean, forces change on next logon)
    *   `valid_from_date` (DATE)
    *   `valid_to_date` (DATE, nullable)
    *   `is_locked` (Boolean, for manual or automatic locking)
    *   `failed_logon_attempts` (INT, for auto-locking)
    *   `last_successful_logon_at` (TIMESTAMP, nullable)
    *   `last_logon_ip` (VARCHAR, nullable)
    *   `reference_user_id_link` (FK to `auth_users.id`, nullable, for Dialog users linked to a Reference user)
    *   `created_at`, `updated_at`, `created_by_user_id`, `updated_by_user_id`

*   **`auth_user_login_history`**
    *   `id` (PK)
    *   `auth_user_id` (FK)
    *   `timestamp` (TIMESTAMP)
    *   `logon_status` (ENUM: 'Success', 'FailedPassword', 'UserLocked', 'UserExpired')
    *   `source_ip_address` (VARCHAR)
    *   `user_agent_details` (TEXT, optional)

## 3. Authorization Objects, Fields, and Activities

These define the building blocks of authorizations. Assumed to be largely static definitions, potentially seeded.

*   **`auth_objects`** (Defines Authorization Objects)
    *   `id` (PK)
    *   `object_name` (UK, e.g., "ARCA_MM_PO", "ARCA_FI_GL_ACCOUNT")
    *   `description` (VARCHAR)
    *   `module_owner` (VARCHAR, e.g., "MM", "FI" - informational)
    *   `created_at`, `updated_at`

*   **`auth_fields`** (Defines Fields within Authorization Objects)
    *   `id` (PK)
    *   `auth_object_id` (FK to `auth_objects`)
    *   `field_name` (UK within object, e.g., "ACTVT", "EKORG", "BUKRS")
    *   `description` (VARCHAR)
    *   `data_type` (ENUM: 'String', 'Numeric', 'Date', 'Boolean' - for validation of values)
    *   `created_at`, `updated_at`

*   **`auth_activities`** (Standardized activity codes, can be pre-seeded)
    *   `id` (PK)
    *   `activity_code` (UK, e.g., "01", "02", "03")
    *   `description` (VARCHAR, e.g., "Create", "Change", "Display")
    *   `is_standard` (Boolean, to differentiate ARCA standard vs custom)

## 4. Roles (Single and Composite)

### 4.1. Single Roles
*   **`auth_roles_single_header`**
    *   `id` (PK)
    *   `role_name` (UK, e.g., "SR_FI_AP_CLERK")
    *   `description` (VARCHAR)
    *   `version` (INT, for role versioning)
    *   `is_active_version` (Boolean)
    *   `created_at`, `updated_at`, `created_by_user_id`, `updated_by_user_id`
    *   `parent_role_id_for_version` (FK to `auth_roles_single_header.id`, nullable, links a new version to its predecessor)

*   **`auth_roles_single_menu_items`** (Defines the user menu for a single role version)
    *   `id` (PK)
    *   `role_header_id` (FK to `auth_roles_single_header` - specific version)
    *   `menu_item_type` (ENUM: 'Transaction', 'Report', 'FioriApp', 'WebLink', 'Folder')
    *   `menu_item_identifier` (VARCHAR, e.g., Transaction code, App ID, URL)
    *   `display_text` (VARCHAR)
    *   `parent_menu_item_id` (Self-referential FK for hierarchy, nullable)
    *   `sort_order` (INT)

*   **`auth_roles_single_authorizations`** (Links a role version to an Authorization Object)
    *   `id` (PK)
    *   `role_header_id` (FK to `auth_roles_single_header` - specific version)
    *   `auth_object_id` (FK to `auth_objects`)
    *   `is_active` (Boolean, allows temporarily disabling an object within a role)
    *   UNIQUE (`role_header_id`, `auth_object_id`)

*   **`auth_role_authorization_values`** (Stores specific field values for an object within a role version)
    *   `id` (PK)
    *   `role_authorization_id` (FK to `auth_roles_single_authorizations`)
    *   `auth_field_id` (FK to `auth_fields`)
    *   `operator` (ENUM: 'EQ', 'NE', 'GT', 'GE', 'LT', 'LE', 'BT' (between), 'CP' (contains pattern), 'IN' (in list))
    *   `value_from` (VARCHAR, stores single value, start of range, pattern, or comma-separated list for 'IN')
    *   `value_to` (VARCHAR, nullable, for 'BT' operator)
    *   `is_excluded` (Boolean, to define NOT conditions, advanced)

*   **`auth_role_generated_profiles_data`** (Stores the compiled runtime authorization data - denormalized for performance)
    *   `id` (PK)
    *   `role_header_id` (FK to `auth_roles_single_header` - specific version, UK)
    *   `generated_at` (TIMESTAMP)
    *   `profile_data_json` (JSON or LONGTEXT, containing an optimized structure of all auth_object/field/value combinations for this role version, making runtime checks faster). This is the "generated profile".

### 4.2. Composite Roles
*   **`auth_roles_composite_header`**
    *   `id` (PK)
    *   `role_name` (UK, e.g., "CR_FINANCE_MANAGER")
    *   `description` (VARCHAR)
    *   `version` (INT)
    *   `is_active_version` (Boolean)
    *   `created_at`, `updated_at`, `created_by_user_id`, `updated_by_user_id`
    *   `parent_role_id_for_version` (FK to `auth_roles_composite_header.id`, nullable)

*   **`auth_roles_composite_single_links`** (Links a composite role version to single role versions)
    *   `composite_role_header_id` (FK to `auth_roles_composite_header`)
    *   `single_role_header_id` (FK to `auth_roles_single_header`)
    *   PRIMARY KEY (`composite_role_header_id`, `single_role_header_id`)

## 5. User Role Assignments

*   **`auth_user_role_assignments`**
    *   `id` (PK)
    *   `auth_user_id` (FK to `auth_users`)
    *   `role_type` (ENUM: 'Single', 'Composite')
    *   `role_id` (BIGINT UNSIGNED, FK to either `auth_roles_single_header.id` or `auth_roles_composite_header.id` based on `role_type`)
    *   `valid_from_date` (DATE)
    *   `valid_to_date` (DATE, nullable)
    *   `assigned_at`, `assigned_by_user_id`
    *   UNIQUE (`auth_user_id`, `role_type`, `role_id`)

## 6. Segregation of Duties (SoD) & Compliance

*   **`auth_sod_rules_header`**
    *   `id` (PK)
    *   `rule_code` (UK)
    *   `description` (VARCHAR, e.g., "Vendor Create vs. Vendor Pay")
    *   `risk_level` (ENUM: 'High', 'Medium', 'Low')
    *   `is_active` (Boolean)

*   **`auth_sod_rule_functions`** (A function is a set of conflicting authorizations)
    *   `id` (PK)
    *   `sod_rule_header_id` (FK)
    *   `function_sequence` (INT, e.g., 1 for first part of conflict, 2 for second)
    *   `description` (VARCHAR, e.g., "Vendor Creation Ability")
    *   `auth_object_id` (FK)
    *   `auth_field_id` (FK)
    *   `operator`, `value_from`, `value_to` (defining the specific conflicting auth values)

*   **`auth_sod_conflicts_log`**
    *   `id` (PK)
    *   `auth_user_id` (FK, if user-level conflict)
    *   `role_id` (FK to single or composite, if role-level inherent conflict)
    *   `role_type` (ENUM: 'Single', 'Composite', NULL)
    *   `sod_rule_header_id` (FK, the rule violated)
    *   `detected_at` (TIMESTAMP)
    *   `status` (ENUM: 'Open', 'Mitigated', 'AcceptedRisk')
    *   `mitigation_id` (FK to `auth_sod_mitigations`, nullable)

*   **`auth_sod_mitigations`**
    *   `id` (PK)
    *   `description` (TEXT, how the risk is mitigated)
    *   `approved_by_user_id` (FK)
    *   `approved_at` (TIMESTAMP)

## 7. Auditing & Workflows (Security Specific)

*   **`auth_audit_log`**
    *   `id` (PK)
    *   `timestamp` (TIMESTAMP)
    *   `performing_user_id` (FK to `auth_users`, nullable for system actions)
    *   `target_user_id` (FK to `auth_users`, nullable, if action is on another user)
    *   `action_type` (VARCHAR, e.g., "USER_LOGIN_SUCCESS", "USER_LOCK", "ROLE_CREATED", "ROLE_AUTH_CHANGED", "SOD_RULE_VIOLATION")
    *   `description` (TEXT, human-readable summary)
    *   `details_json` (JSON, storing old/new values, context)
    *   `source_ip_address` (VARCHAR, nullable)

*   **`auth_access_requests`** (For user access request workflow)
    *   `id` (PK)
    *   `requesting_user_id` (FK)
    *   `target_user_id` (FK, for whom access is requested)
    *   `request_type` (ENUM: 'NewUser', 'ChangeRoles', 'UnlockAccount')
    *   `justification` (TEXT)
    *   `requested_roles_json` (JSON, array of role_ids and validity)
    *   `status` (ENUM: 'PendingManagerApproval', 'PendingRoleOwnerApproval', 'PendingSecurityApproval', 'Approved', 'Rejected', 'Implemented')
    *   `created_at`, `updated_at`

*   **`auth_access_request_approvals`**
    *   `id` (PK)
    *   `access_request_id` (FK)
    *   `approval_step_name` (VARCHAR, e.g., "Manager", "RoleOwner_XYZ")
    *   `approver_user_id` (FK)
    *   `approval_status` (ENUM: 'Approved', 'Rejected')
    *   `comments` (TEXT)
    *   `approved_at` (TIMESTAMP)

*   **`auth_firefighter_ids_config`** (Configuration of available Firefighter IDs/Roles)
    *   `id` (PK)
    *   `ff_id_name` (VARCHAR, UK, e.g., "FF_BASIS_ADMIN", "FF_FI_SUPER")
    *   `description`
    *   `assigned_role_id` (FK to `auth_roles_single_header` or `auth_roles_composite_header` - the powerful role)
    *   `is_active` (Boolean)

*   **`auth_firefighter_sessions_log`**
    *   `id` (PK)
    *   `requesting_user_id` (FK to `auth_users` - the user who needs FF access)
    *   `firefighter_id_config_id` (FK to `auth_firefighter_ids_config` - the FF ID used)
    *   `reason` (TEXT)
    *   `status` (ENUM: 'Requested', 'Approved', 'Active', 'Completed', 'Reviewed', 'Rejected')
    *   `requested_at`, `approved_at`, `session_start_at`, `session_end_at`, `reviewed_at`
    *   `approved_by_user_id` (FK, nullable)
    *   `reviewed_by_user_id` (FK, nullable)

*   **`auth_firefighter_activity_log`** (Detailed log of actions during a FF session)
    *   `id` (PK)
    *   `ff_session_log_id` (FK)
    *   `timestamp` (TIMESTAMP)
    *   `transaction_or_action` (VARCHAR)
    *   `details_json` (JSON, specific data related to the action)

This data model provides a foundation for robust user and authorization management. Performance of authorization checks will rely heavily on efficient querying of user assignments and the (potentially cached) `auth_role_generated_profiles_data`.
