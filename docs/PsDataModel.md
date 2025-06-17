# "PS" Module: Data Model Design (MySQL)

This document outlines the proposed MySQL database schema design for the Project System (PS) module. All PS-specific tables will use the `ps_` prefix and reside within the primary ERP database, adhering to the modular architecture.

## 1. General Principles

*   **Prefixing:** All tables specific to the PS module are prefixed with `ps_`.
*   **Relationships:** Foreign keys will enforce relationships within PS data and to stable `core_` entities (like `core_users`). Links to transactional data in other modules (e.g., Fina documents, LSCM POs) will often be by storing the external document ID for reference, with core processing logic relying on events or API calls for deep integration.
*   **Auditability:** Standard audit columns (`created_at`, `updated_at`, `created_by_user_id`, `updated_by_user_id`) on key tables.
*   **Status Management:** Many key entities will have status fields to control lifecycle and allowed transactions.

## 2. Core Project Structures

### 2.1. Project Definition
*   **`ps_projects_definition`**
    *   `id` (PK)
    *   `project_definition_code` (UK, user-friendly project ID, e.g., "PROJ-0001")
    *   `description` (VARCHAR)
    *   `project_profile_id` (FK to `ps_project_profiles` - defines default settings)
    *   `company_code_id` (FK to `fina_company_codes`)
    *   `controlling_area_id` (FK to `fina_co_controlling_areas`)
    *   `profit_center_id` (FK to `fina_co_profit_centers`, optional default)
    *   `person_responsible_user_id` (FK to `core_users`)
    *   `project_start_date_planned`, `project_finish_date_planned`
    *   `project_start_date_actual`, `project_finish_date_actual` (nullable)
    *   `project_start_date_forecast`, `project_finish_date_forecast` (nullable)
    *   `project_currency_code` (FK to `fina_currencies`)
    *   `status_system` (ENUM: e.g., 'CREATED', 'RELEASED', 'BUDGETED', 'TECH_COMPLETED', 'CLOSED', 'LOCKED')
    *   `status_user_json` (JSON, for custom user statuses)
    *   `created_at`, `updated_at`, `created_by_user_id`, `updated_by_user_id`

*   **`ps_project_profiles`** (Defines default values and control parameters for projects)
    *   `id` (PK)
    *   `profile_code` (UK)
    *   `description`
    *   `default_project_calendar_id` (FK to `core_calendars`)
    *   `budget_profile_id` (FK to `ps_budget_profiles`)
    *   `network_profile_id` (FK to `ps_network_profiles`)
    *   ... (other control parameters)

### 2.2. Work Breakdown Structure (WBS)
*   **`ps_wbs_elements`**
    *   `id` (PK)
    *   `project_definition_id` (FK to `ps_projects_definition`)
    *   `wbs_element_code` (UK within project, user-friendly ID, e.g., "P-0001.1.A")
    *   `description` (VARCHAR)
    *   `parent_wbs_id` (Self-referential FK for hierarchy, nullable for top-level WBS)
    *   `level` (INT, hierarchy level)
    *   `person_responsible_user_id` (FK to `core_users`, optional, can inherit)
    *   `company_code_id` (FK, can inherit from project)
    *   `profit_center_id` (FK, optional, can inherit)
    *   `cost_center_id` (FK to `fina_co_cost_centers`, responsible CC, optional)
    *   `is_planning_element` (Boolean)
    *   `is_account_assignment_element` (Boolean - can costs be posted directly?)
    *   `is_billing_element` (Boolean - relevant for SD integration)
    *   `auc_master_id` (FK to `fina_aa_asset_master` where asset class is AUC, optional)
    *   `start_date_planned`, `finish_date_planned`
    *   `start_date_actual`, `finish_date_actual` (nullable)
    *   `start_date_forecast`, `finish_date_forecast` (nullable)
    *   `duration_planned_days` (INT)
    *   `work_planned_hours` (Decimal)
    *   `progress_percentage_confirmed` (Decimal, 0-100)
    *   `status_system` (ENUM similar to project definition, plus e.g. 'PART_CONFIRMED')
    *   `status_user_json` (JSON)
    *   `total_cost_planned` (Decimal, aggregated from cost planning)
    *   `total_budget_allocated` (Decimal, from budgeting)
    *   `total_cost_actual` (Decimal, aggregated from Fina/LSCM actuals)
    *   `total_revenue_planned` (Decimal)
    *   `total_revenue_actual` (Decimal)
    *   `created_at`, `updated_at`, `created_by_user_id`, `updated_by_user_id`

### 2.3. Networks & Activities
*   **`ps_networks_header`**
    *   `id` (PK)
    *   `project_definition_id` (FK)
    *   `network_number` (UK within project)
    *   `description` (VARCHAR)
    *   `network_profile_id` (FK to `ps_network_profiles`)
    *   `status_system`, `status_user_json`
    *   `created_at`, `updated_at`

*   **`ps_network_profiles`** (Defines defaults for networks and activities)
    *   `id` (PK)
    *   `profile_code` (UK)
    *   `description`
    *   `default_activity_calendar_id` (FK to `core_calendars`)
    *   `scheduling_parameters_json` (e.g., default dependency type)

*   **`ps_network_activities`**
    *   `id` (PK)
    *   `network_header_id` (FK)
    *   `wbs_element_id` (FK, account assignment WBS)
    *   `activity_number` (UK within network, e.g., "0010", "0020")
    *   `description` (VARCHAR)
    *   `control_key_id` (FK to `ps_activity_control_keys`)
    *   `activity_type` (ENUM: 'InternalProcessing', 'ExternalProcessing', 'Service', 'MaterialComponent', 'CostActivity', 'Milestone')
    *   `work_center_id` (FK to `lscm_pp_work_centers` or `ps_work_centers` if PS has its own)
    *   `duration_planned_days` (INT)
    *   `work_planned_hours` (Decimal)
    *   `start_date_planned`, `finish_date_planned`
    *   `start_date_actual`, `finish_date_actual` (nullable)
    *   `start_date_forecast`, `finish_date_forecast` (nullable)
    *   `constraint_type` (ENUM: 'MustStartOn', 'FinishNoLaterThan', etc., nullable)
    *   `constraint_date` (DATE, nullable)
    *   `progress_percentage_confirmed` (Decimal)
    *   `status_system`, `status_user_json`
    *   `total_cost_planned` (Decimal)
    *   `total_cost_actual` (Decimal)
    *   `lscm_mm_purchase_requisition_id` (VARCHAR, if PR created for external activity)
    *   `created_at`, `updated_at`

*   **`ps_activity_control_keys`** (Define how activities are processed)
    *   `id` (PK)
    *   `control_key_code` (UK)
    *   `description`
    *   `is_schedulable` (Boolean)
    *   `determines_capacity_reqs` (Boolean)
    *   `is_cost_relevant` (Boolean)
    *   `requires_confirmation` (Boolean)
    *   `can_trigger_pr` (Boolean for external processing)

*   **`ps_activity_dependencies`**
    *   `id` (PK)
    *   `network_header_id` (FK)
    *   `predecessor_activity_id` (FK to `ps_network_activities`)
    *   `successor_activity_id` (FK to `ps_network_activities`)
    *   `dependency_type` (ENUM: 'FS', 'SS', 'FF', 'SF')
    *   `lead_lag_duration_days` (INT, positive for lag, negative for lead)
    *   `created_at`, `updated_at`

### 2.4. Milestones
*   **`ps_milestones`**
    *   `id` (PK)
    *   `project_definition_id` (FK)
    *   `wbs_element_id` (FK, optional, if milestone directly on WBS)
    *   `network_activity_id` (FK, optional, if milestone on activity)
    *   `milestone_code` (UK within project)
    *   `description` (VARCHAR)
    *   `milestone_type` (ENUM: 'Billing', 'Progress', 'KeyDate')
    *   `date_planned`, `date_actual`, `date_forecast`
    *   `status` (ENUM: 'Open', 'Released', 'Completed')
    *   `is_billing_relevant` (Boolean)
    *   `lscm_sd_sales_document_item_id` (Link to sales document item for billing, optional)
    *   `created_at`, `updated_at`

## 3. Time Scheduling Data
*   (Primarily stored as date fields on `ps_projects_definition`, `ps_wbs_elements`, `ps_network_activities`, `ps_milestones`)
*   **`ps_project_versions`** (Baselines)
    *   `id` (PK)
    *   `project_definition_id` (FK)
    *   `version_code` (UK within project, e.g., "BASELINE_01")
    *   `description`
    *   `snapshot_datetime` (Timestamp when baseline was taken)
    *   `created_by_user_id` (FK)
    *   `created_at`

*   **`ps_project_version_data`** (Stores snapshot of key data for WBS/activities for a version)
    *   `id` (PK)
    *   `project_version_id` (FK)
    *   `entity_type` (ENUM: 'WBS', 'Activity', 'Milestone')
    *   `entity_id` (BIGINT UNSIGNED, FK to the respective table)
    *   `data_snapshot_json` (JSON, storing planned_start, planned_finish, planned_cost, planned_work, etc. at time of baseline)

## 4. Cost Planning & Budgeting Data

*   **`ps_cost_planning_items`**
    *   `id` (PK)
    *   `project_definition_id` (FK)
    *   `wbs_element_id` (FK, optional, if planning at WBS level)
    *   `network_activity_id` (FK, optional, if planning at Activity level)
    *   `fina_cost_element_id` (FK to `fina_gl_accounts` where account is a cost element)
    *   `planning_period_start_date`, `planning_period_end_date`
    *   `planned_cost_amount` (Decimal)
    *   `planned_cost_currency_code` (FK)
    *   `description`
    *   `version` (VARCHAR, e.g., "PLAN_V1", "FORECAST_V2")
    *   `created_at`, `updated_at`

*   **`ps_budgets`** (Overall budget for WBS elements)
    *   `id` (PK)
    *   `wbs_element_id` (FK, budget is typically on WBS)
    *   `budget_type` (ENUM: 'Original', 'Supplement', 'Return', 'TransferIn', 'TransferOut')
    *   `amount` (Decimal)
    *   `currency_code` (FK)
    *   `fiscal_year` (Optional, if budget is year-specific)
    *   `description`
    *   `approval_status` (ENUM: 'Pending', 'Approved', 'Rejected')
    *   `created_at`, `updated_at`

*   **`ps_budget_profiles`** (Defines how budgeting works, AVC settings)
    *   `id` (PK)
    *   `profile_code` (UK)
    *   `description`
    *   `availability_control_settings_json` (JSON, tolerance limits, etc.)

*   **`ps_actual_cost_references`** (PS table to reference actual cost postings in Fina if not directly on Fina tables)
    *   `id` (PK)
    *   `wbs_element_id` (FK, optional)
    *   `network_activity_id` (FK, optional)
    *   `fina_co_actual_posting_id` (FK to `fina_co_actual_postings`) OR `fina_gl_document_item_id` (FK)
    *   `posting_date`
    *   `amount`, `currency_code`
    *   `cost_element_id` (FK)
    *   `source_document_reference` (e.g., PO number, Invoice number)
    *   (This table might be redundant if `fina_co_actual_postings` can directly store `ps_wbs_element_id` and `ps_network_activity_id`)

## 5. Resource & Material Management Data

*   **`ps_resource_allocations`**
    *   `id` (PK)
    *   `network_activity_id` (FK)
    *   `resource_type` (ENUM: 'Personnel', 'Equipment', 'WorkCenter')
    *   `resource_id` (ID linking to `core_users` for personnel, `lscm_pm_equipment_master` for equipment, or `lscm_pp_work_centers`)
    *   `planned_work_hours` (Decimal)
    *   `start_date_planned`, `finish_date_planned`
    *   `notes`

*   **`ps_material_requirements`**
    *   `id` (PK)
    *   `network_activity_id` (FK)
    *   `core_material_id` (FK to `core_materials`)
    *   `quantity_required` (Decimal)
    *   `unit_of_measure_id` (FK)
    *   `requirement_date` (DATE)
    *   `reservation_number` (VARCHAR, from LSCM MM, nullable)
    *   `purchase_requisition_number` (VARCHAR, from LSCM MM, nullable)
    *   `status` (ENUM: 'New', 'ReservationCreated', 'PRCreated', 'Issued')

## 6. Project Execution & Monitoring Data

*   **`ps_progress_confirmations`**
    *   `id` (PK)
    *   `network_activity_id` (FK, usually confirmations are on activities)
    *   `wbs_element_id` (FK, if confirming at WBS level)
    *   `confirmation_date` (DATE)
    *   `actual_work_hours` (Decimal, for this confirmation)
    *   `remaining_work_hours_forecast` (Decimal)
    *   `percent_complete` (Decimal, for this confirmation or overall)
    *   `actual_start_date` (DATE, if confirmation triggers it)
    *   `actual_finish_date` (DATE, if confirmation completes it)
    *   `confirmed_by_user_id` (FK to `core_users`)
    *   `notes` (TEXT)
    *   `created_at`

*   **`ps_issues`** (Basic Issue Tracking)
    *   `id` (PK)
    *   `project_definition_id` (FK)
    *   `wbs_element_id` (FK, optional)
    *   `network_activity_id` (FK, optional)
    *   `summary` (VARCHAR)
    *   `description` (TEXT)
    *   `priority` (ENUM: 'Low', 'Medium', 'High', 'Critical')
    *   `status` (ENUM: 'Open', 'InProgress', 'Resolved', 'Closed')
    *   `reported_by_user_id` (FK)
    *   `assigned_to_user_id` (FK, optional)
    *   `due_date` (DATE, optional)
    *   `created_at`, `updated_at`

*   **`ps_risks`** (Basic Risk Tracking - similar structure to `ps_issues` but with risk-specific fields like probability, impact, mitigation plan)

## 7. Period-End Closing Data

*   **`ps_settlement_rules`**
    *   `id` (PK)
    *   `wbs_element_id` (FK, if settling WBS costs) OR `network_activity_id` (FK, if settling Network/Order costs)
    *   `receiver_type` (ENUM: 'CostCenter', 'GLAccount', 'WBS', 'Asset', 'SalesOrder', 'ProfitabilitySegment')
    *   `receiver_object_id` (ID of the cost center, GL account number, WBS ID, Asset master ID, etc.)
    *   `settlement_percentage` (Decimal, 0-100) OR `settlement_amount` (Decimal)
    *   `settlement_type` (ENUM: 'Periodic', 'Full')
    *   `is_active` (Boolean)
    *   `created_at`, `updated_at`

*   **`ps_result_analysis_keys`** (Define how WIP/Revenue Recognition is calculated)
    *   `id` (PK)
    *   `ra_key_code` (UK)
    *   `description`
    *   `ra_method` (ENUM: 'PercentageOfCompletion', 'CompletedContract', etc.)
    *   `gl_account_wip_asset_id` (FK)
    *   `gl_account_wip_expense_id` (FK)
    *   `gl_account_recognized_revenue_id` (FK)
    *   `gl_account_cost_of_sales_id` (FK)

*   **`ps_result_analysis_data`** (Stores calculated RA values per period)
    *   `id` (PK)
    *   `wbs_element_id` (FK, RA usually on billing WBS)
    *   `fiscal_year`, `period`
    *   `ra_key_id` (FK)
    *   `calculated_wip_amount` (Decimal)
    *   `calculated_recognized_revenue` (Decimal)
    *   `calculated_cost_of_sales` (Decimal)
    *   `posting_status` (ENUM: 'Calculated', 'PostedToFina')
    *   `fina_gl_document_header_id_ra_posting` (FK, nullable)
    *   `created_at`

This data model provides a robust foundation for the Project System module. Indexing strategies will be critical for performance, especially on date fields, status fields, and foreign keys used in common queries and reports.
