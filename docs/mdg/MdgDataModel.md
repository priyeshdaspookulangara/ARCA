# ARCA MDG (Master Data Governance) Module: Data Model Design (MySQL)

This document outlines the proposed MySQL database schema for the ARCA Master Data Governance (MDG) module. All MDG-specific tables will use the `mdg_` prefix. MDG will host the "golden record" for core global attributes of governed master data objects.

## 1. General Principles

*   **Prefixing:** All tables are prefixed with `mdg_`.
*   **MDG as Source of Truth for Core Data:** MDG tables store the definitive, globally unique core attributes of governed master data. Other modules link to these MDG core records via ID and manage their own module-specific extension attributes.
*   **Workflow-Centric:** Many tables support the workflow-driven nature of master data changes.
*   **Auditability:** Comprehensive versioning and audit trails for master data changes.

## 2. Core Master Data Object Tables (Hosted by MDG)

These tables represent the "golden record" for the globally unique and essential attributes.

*   **`mdg_materials_core`** (Governed Material Master - Global Fields)
    *   `id` (PK, ARCA Global Material ID)
    *   `material_number` (UK, User-facing/external material number)
    *   `base_unit_of_measure_id` (FK to `core_units_of_measure` or a local `mdg_units_of_measure`)
    *   `material_type_id` (FK to `mdg_material_types`)
    *   `description_short` (VARCHAR, global short text)
    *   `status_id` (FK to `mdg_master_data_statuses` - e.g., 'Active', 'BlockedForProcurement', 'MarkedForDeletion')
    *   `current_version_id` (FK to `mdg_master_data_versions` for this material, nullable)
    *   `created_at`, `updated_at`, `created_by_user_id`, `updated_by_user_id` (from last approved CR)

*   **`mdg_business_partners_core`** (Governed Customer/Vendor Master - Global Fields)
    *   `id` (PK, ARCA Global Business Partner ID)
    *   `bp_number` (UK, User-facing/external BP number)
    *   `bp_role_flags_json` (JSON, e.g., `{"is_customer": true, "is_vendor": true, "is_employee": false}`)
    *   `organization_name1` (VARCHAR, if BP type is Organization)
    *   `organization_name2` (VARCHAR, nullable)
    *   `person_last_name` (VARCHAR, if BP type is Person)
    *   `person_first_name` (VARCHAR, if BP type is Person)
    *   `status_id` (FK to `mdg_master_data_statuses`)
    *   `current_version_id` (FK to `mdg_master_data_versions` for this BP, nullable)
    *   `created_at`, `updated_at`, `created_by_user_id`, `updated_by_user_id`

*   **`mdg_gl_accounts_core`** (Governed GL Account Master - Global Fields, if in MDG scope)
    *   `id` (PK, ARCA Global GL Account ID)
    *   `gl_account_number` (UK within Chart of Accounts)
    *   `chart_of_accounts_id` (FK to `fina_charts_of_accounts` - FICO owns CoA definition)
    *   `description_short` (VARCHAR)
    *   `status_id` (FK to `mdg_master_data_statuses`)
    *   `current_version_id` (FK, nullable)
    *   `created_at`, `updated_at`, `created_by_user_id`, `updated_by_user_id`

*   **`mdg_cost_centers_core`** (Governed Cost Center Master - Global Fields, if in MDG scope)
    *   `id` (PK, ARCA Global Cost Center ID)
    *   `cost_center_code` (UK within Controlling Area)
    *   `controlling_area_id` (FK to `fina_co_controlling_areas` - FICO owns CA definition)
    *   `name` (VARCHAR)
    *   `valid_from_date`, `valid_to_date`
    *   `status_id` (FK to `mdg_master_data_statuses`)
    *   `current_version_id` (FK, nullable)
    *   `created_at`, `updated_at`, `created_by_user_id`, `updated_by_user_id`

*   **`mdg_master_data_statuses`** (e.g., Active, Inactive, Blocked, MarkedForDeletion)
    *   `id` (PK)
    *   `status_code` (UK)
    *   `description`
    *   `applies_to_object_type` (VARCHAR, e.g., "MATERIAL", "BUSINESS_PARTNER")

*   **`mdg_material_types` / `mdg_units_of_measure`** (If not using `core_` tables, MDG might define its own basic lookups)

## 3. Change Request & Workflow Management

*   **`mdg_change_requests`** (Header for all master data C/U/D requests)
    *   `id` (PK)
    *   `cr_number` (UK, system-generated)
    *   `governed_object_type` (VARCHAR, e.g., "MATERIAL", "BUSINESS_PARTNER", "GL_ACCOUNT")
    *   `target_object_core_id` (BIGINT UNSIGNED, FK to respective `mdg_..._core` table, nullable for CREATE requests)
    *   `request_type` (ENUM: 'CREATE', 'CHANGE', 'BLOCK', 'UNBLOCK', 'MARK_FOR_DELETION')
    *   `status_id` (FK to `mdg_cr_statuses` - e.g., 'Draft', 'Submitted', 'InReview', 'Approved', 'Rejected', 'Activated', 'Closed')
    *   `priority` (ENUM: 'Low', 'Medium', 'High')
    *   `requester_user_id` (FK to `auth_users`)
    *   `justification` (TEXT)
    *   `current_workflow_instance_id` (FK to `mdg_workflow_instances`, nullable)
    *   `created_at`, `updated_at`

*   **`mdg_cr_staged_data`** (Proposed data for the CR - flexible structure)
    *   `id` (PK)
    *   `change_request_id` (FK)
    *   `attribute_group` (VARCHAR, e.g., "BasicData", "SalesData", "PurchasingData" - helps organize)
    *   `data_json` (JSON, storing the proposed field names and their new values for this CR. Structure depends on `governed_object_type`)
    *   `version` (INT, if data is staged in multiple steps of a workflow)

*   **`mdg_cr_statuses`** (Workflow statuses for Change Requests)
    *   `id` (PK)
    *   `status_code` (UK)
    *   `description`

*   **`mdg_workflow_definitions`** (Templates for workflows)
    *   `id` (PK)
    *   `workflow_name` (UK)
    *   `governed_object_type` (VARCHAR)
    *   `request_type` (ENUM, optional - if workflow is specific to CREATE vs CHANGE)
    *   `definition_json` (JSON, defining steps, approver roles, conditions)
    *   `is_active` (Boolean)

*   **`mdg_workflow_instances`** (Active workflow runs for CRs)
    *   `id` (PK)
    *   `workflow_definition_id` (FK)
    *   `change_request_id` (FK, UK)
    *   `current_step_key` (VARCHAR, identifier of the current step in definition_json)
    *   `status` (ENUM: 'InProgress', 'Completed', 'Cancelled')
    *   `started_at`, `completed_at` (nullable)

*   **`mdg_workflow_tasks`** (Individual approval/stewardship tasks)
    *   `id` (PK)
    *   `workflow_instance_id` (FK)
    *   `step_key` (VARCHAR)
    *   `task_description` (VARCHAR)
    *   `assigned_to_role_id` (FK to `auth_roles_single_header`, nullable)
    *   `assigned_to_user_id` (FK to `auth_users`, nullable)
    *   `status` (ENUM: 'Pending', 'InProgress', 'Completed', 'Rejected', 'Skipped')
    *   `due_date` (DATE, nullable)
    *   `completed_at` (DATETIME, nullable)
    *   `decision_comments` (TEXT)

## 4. Data Quality & Validation

*   **`mdg_dq_rules`** (Data Quality / Validation Rules)
    *   `id` (PK)
    *   `rule_name` (UK)
    *   `description` (TEXT)
    *   `governed_object_type` (VARCHAR)
    *   `target_attribute_name` (VARCHAR, can be dot-notation for JSON fields in staging)
    *   `validation_type` (ENUM: 'Mandatory', 'RegexFormat', 'Lookup', 'Uniqueness', 'CrossFieldDependency', 'CustomLogic')
    *   `validation_parameters_json` (JSON, e.g., regex pattern, lookup table, fields for cross-field check)
    *   `error_message` (VARCHAR)
    *   `is_active` (Boolean)
    *   `execution_trigger` (ENUM: 'OnSaveStaging', 'OnSubmitCR', 'OnApproveStepX', 'Batch')

*   **`mdg_dq_validation_log`**
    *   `id` (PK)
    *   `change_request_id` (FK, if validation during CR workflow)
    *   `target_object_core_id` (FK, if batch validation on existing master)
    *   `dq_rule_id` (FK)
    *   `validation_datetime` (TIMESTAMP)
    *   `is_violation` (Boolean)
    *   `violation_details` (TEXT, nullable)

*   **`mdg_deduplication_runs`** (Log of batch deduplication jobs)
    *   `id` (PK)
    *   `run_datetime` (TIMESTAMP)
    *   `governed_object_type` (VARCHAR)
    *   `status` (ENUM: 'InProgress', 'Completed', 'Failed')
    *   `parameters_json` (JSON, matching criteria used)

*   **`mdg_potential_duplicates`** (Pairs/groups identified for review)
    *   `id` (PK)
    *   `deduplication_run_id` (FK, nullable if found during real-time check)
    *   `governed_object_type` (VARCHAR)
    *   `object1_core_id` (FK)
    *   `object2_core_id` (FK)
    *   `match_score` (Decimal, optional)
    *   `status` (ENUM: 'PendingReview', 'ConfirmedDuplicate_ToBeMerged', 'ConfirmedDuplicate_ToBeLinked', 'NotADuplicate')
    *   `reviewer_user_id` (FK, nullable)
    *   `review_notes` (TEXT, nullable)

## 5. Data Replication & Distribution

*   **`mdg_replication_subscribers`** (Consuming systems/modules)
    *   `id` (PK)
    *   `subscriber_code` (UK, e.g., "ARCA_FICO", "ARCA_MM", "EXTERNAL_CRM")
    *   `description` (VARCHAR)
    *   `is_active` (Boolean)

*   **`mdg_replication_object_config`** (Defines which objects a subscriber gets)
    *   `subscriber_id` (FK)
    *   `governed_object_type` (VARCHAR)
    *   `replication_method` (ENUM: 'RealTimeEvent', 'ScheduledBatchAPI', 'OnFileExtract')
    *   `is_active` (Boolean)
    *   PRIMARY KEY (`subscriber_id`, `governed_object_type`)

*   **`mdg_replication_log`**
    *   `id` (PK)
    *   `governed_object_type` (VARCHAR)
    *   `object_core_id` (BIGINT UNSIGNED)
    *   `object_version_id` (FK to `mdg_master_data_versions`)
    *   `subscriber_id` (FK)
    *   `replication_attempt_datetime` (TIMESTAMP)
    *   `status` (ENUM: 'Pending', 'Successful', 'Failed', 'Retrying')
    *   `confirmation_datetime` (TIMESTAMP, nullable - if subscriber sends confirmation)
    *   `error_message` (TEXT, nullable)

## 6. Versioning & Audit Trail (MDG Specific)

*   **`mdg_master_data_versions`** (Snapshot of a governed record at a point in time)
    *   `id` (PK)
    *   `governed_object_type` (VARCHAR)
    *   `object_core_id` (BIGINT UNSIGNED)
    *   `version_number` (INT)
    *   `change_request_id_approved` (FK to `mdg_change_requests` that led to this version)
    *   `data_snapshot_json` (JSON, full snapshot of the core governed attributes at this version)
    *   `is_active_version` (Boolean, only one active version per object_core_id)
    *   `activated_at` (TIMESTAMP)
    *   `created_at`, `created_by_user_id`

*   (Detailed attribute-level changes are auditable via `mdg_change_requests` and `mdg_cr_staged_data` in conjunction with workflow logs).

This data model establishes MDG as the central point for creating, managing, and distributing trusted master data across ARCA.
