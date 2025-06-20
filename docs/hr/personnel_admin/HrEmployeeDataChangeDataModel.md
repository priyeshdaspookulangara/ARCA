# ARCA HR Module: "Employee Data Change" Data Model Design (MySQL)

This document outlines the proposed MySQL database schema additions and modifications to support the "Employee Data Change" functionality within the ARCA HR module. A core principle is the use of effective-dating for key employee data to maintain historical accuracy.

## 1. General Principles

*   **Prefixing:** Continue using `hr_` prefixes for HR-specific tables.
*   **Effective Dating:** Key employee data tables that change over time (e.g., job assignments, compensation) will be structured to hold historical records using `valid_from` and `valid_to` date columns. The currently active record typically has `valid_to` set to a high date (e.g., '9999-12-31').
*   **Change Request Logging:** A dedicated table will log all personnel action requests, tracking their workflow and approval status.
*   **Staging vs. Direct Update:** The strategy for applying approved changes (either via staging tables or by creating future-dated active records upon approval) will be crucial. This model leans towards creating future-dated records directly upon approval, which is a common and robust pattern in HR systems.

## 2. Effective-Dated Core HR Data Tables (Illustrative - may extend existing tables)

These tables store the actual employee data that changes over time. If these tables were not initially designed for effective dating during the "Hire Employee" feature, they would need to be modified or replaced.

*   **`hr_employee_job_assignments`** (Stores an employee's job, position, org unit, manager, etc., over time)
    *   `id` (PK)
    *   `employee_id` (FK to `hr_employees`)
    *   `valid_from` (DATE)
    *   `valid_to` (DATE, e.g., '9999-12-31' for current record)
    *   `action_request_id_triggered_by` (FK to `hr_personnel_action_requests` - the action that created this slice)
    *   `position_id` (FK to `hr_positions`)
    *   `job_title_id` (FK to `hr_job_titles`)
    *   `department_id` (FK to `hr_departments` or `core_organization_units`)
    *   `cost_center_id` (FK to `fina_co_cost_centers`)
    *   `company_code_id` (FK to `fina_company_codes`)
    *   `personnel_area_id` (FK to `hr_personnel_areas`)
    *   `personnel_sub_area_id` (FK to `hr_personnel_sub_areas`)
    *   `employee_group_id` (FK to `hr_employee_groups`)
    *   `employee_sub_group_id` (FK to `hr_employee_sub_groups`)
    *   `manager_core_user_id` (FK to `core_users` or `auth_users`)
    *   `employment_status_id` (FK to `hr_employment_statuses` - e.g., 'Active', 'InactiveOnLeave', 'Terminated')
    *   `reason_for_change_code` (VARCHAR, nullable - e.g., "PROM", "TRAN", "HIRING")
    *   `created_at`, `updated_at`
    *   INDEX (`employee_id`, `valid_from`, `valid_to`)

*   **`hr_employee_compensation`** (Stores salary, allowances, etc., over time)
    *   `id` (PK)
    *   `employee_id` (FK to `hr_employees`)
    *   `valid_from` (DATE)
    *   `valid_to` (DATE)
    *   `action_request_id_triggered_by` (FK to `hr_personnel_action_requests`)
    *   `base_salary_amount` (Decimal)
    *   `salary_currency_code` (FK to `fina_currencies`)
    *   `pay_frequency` (ENUM: 'Monthly', 'BiWeekly', 'Weekly', 'Hourly')
    *   `other_components_json` (JSON, for storing various allowances, bonuses if not in separate tables)
    *   `reason_for_change_code` (VARCHAR, nullable)
    *   `created_at`, `updated_at`
    *   INDEX (`employee_id`, `valid_from`, `valid_to`)

*   **`hr_employee_addresses`** (If addresses need to be historically tracked with effective dates)
    *   `id` (PK)
    *   `employee_id` (FK to `hr_employees`)
    *   `address_type` (ENUM: 'PrimaryHome', 'Mailing', 'SecondaryHome')
    *   `valid_from` (DATE)
    *   `valid_to` (DATE)
    *   `action_request_id_triggered_by` (FK to `hr_personnel_action_requests`)
    *   `street`, `city`, `postal_code`, `state_or_province`, `country_code`
    *   `created_at`, `updated_at`
    *   INDEX (`employee_id`, `address_type`, `valid_from`, `valid_to`)

*   **`hr_employee_personal_data_versions`** (For changes like name, marital status, if full history is needed beyond audit)
    *   `id` (PK)
    *   `employee_id` (FK to `hr_employees`)
    *   `valid_from` (DATE)
    *   `valid_to` (DATE)
    *   `action_request_id_triggered_by` (FK to `hr_personnel_action_requests`)
    *   `last_name` (VARCHAR)
    *   `first_name` (VARCHAR) // To capture name changes
    *   `marital_status_id` (FK to `hr_marital_statuses`)
    *   `emergency_contact_name` (VARCHAR, nullable)
    *   `emergency_contact_phone` (VARCHAR, nullable)
    *   `bank_account_details_json_encrypted` (TEXT, nullable - for payroll bank details)
    *   `created_at`, `updated_at`
    *   INDEX (`employee_id`, `valid_from`, `valid_to`)

*   **`hr_employee_work_schedules`** (Tracks work schedule, employment type over time)
    *   `id` (PK)
    *   `employee_id` (FK to `hr_employees`)
    *   `valid_from` (DATE)
    *   `valid_to` (DATE)
    *   `action_request_id_triggered_by` (FK to `hr_personnel_action_requests`)
    *   `employment_type_id` (FK to `hr_employment_types` - e.g., FullTime, PartTime, Contractor)
    *   `work_schedule_rule_id` (FK to `hr_work_schedule_rules` - defines standard hours, days)
    *   `weekly_hours` (Decimal, if not derived from rule)
    *   `fte_percentage` (Decimal, Full-Time Equivalent)
    *   `created_at`, `updated_at`
    *   INDEX (`employee_id`, `valid_from`, `valid_to`)

*   **`hr_employment_statuses`** (Lookup: Active, InactiveOnLeave, Terminated, Retired)
    *   `id` (PK), `status_code` (UK), `description`

## 3. Change Request Logging & Workflow

*   **`hr_personnel_action_requests`** (Logs each initiated data change request)
    *   `id` (PK)
    *   `request_number` (UK, system-generated)
    *   `employee_id` (FK to `hr_employees` - the employee whose data is being changed)
    *   `action_type_id` (FK to `hr_personnel_action_types`)
    *   `requested_effective_date` (DATE - when the change should become active)
    *   `reason_for_action_text` (TEXT, nullable)
    *   `status` (ENUM: 'Draft', 'PendingApproval_Manager', 'PendingApproval_HRBP', 'PendingApproval_Payroll', 'Approved', 'Rejected', 'Implemented', 'WithError', 'Cancelled')
    *   `initiator_user_id` (FK to `auth_users`)
    *   `submission_datetime` (TIMESTAMP)
    *   `last_approval_datetime` (TIMESTAMP, nullable)
    *   `implemented_datetime` (TIMESTAMP, nullable)
    *   `workflow_instance_id` (VARCHAR, nullable - link to a generic ARCA workflow engine)
    *   `current_approver_user_id` (FK to `auth_users`, nullable)
    *   `proposed_data_snapshot_json` (JSON, a snapshot of all *changed* data fields and their proposed new values at the time of submission/approval. This aids in approvals and audit without needing complex staging tables for all scenarios).
    *   `created_at`, `updated_at`, `created_by_user_id`, `updated_by_user_id`

*   **`hr_personnel_action_types`** (Defines the types of changes)
    *   `id` (PK)
    *   `action_code` (UK, e.g., "PROMOTE", "TRANSFER_ORG", "SALARY_CHG", "ADDRESS_UPD", "START_LT_LEAVE")
    *   `description` (VARCHAR)
    *   `default_workflow_definition_key` (VARCHAR, key to a workflow definition in the engine)
    *   `is_ess_allowed` (Boolean)
    *   `is_mss_allowed` (Boolean)

*   **`hr_personnel_action_approval_history`**
    *   `id` (PK)
    *   `action_request_id` (FK to `hr_personnel_action_requests`)
    *   `approval_step_name` (VARCHAR, e.g., "Manager Approval", "HRBP Review")
    *   `approver_user_id` (FK to `auth_users`)
    *   `decision` (ENUM: 'Approved', 'Rejected', 'SentBackForChanges')
    *   `decision_datetime` (TIMESTAMP)
    *   `comments` (TEXT, nullable)

## 4. Long-Term Leave Specific Data

*   **`hr_employee_long_term_leaves`** (Tracks specific long-term leave periods)
    *   `id` (PK)
    *   `employee_id` (FK to `hr_employees`)
    *   `action_request_id_start` (FK to `hr_personnel_action_requests` for the leave start action)
    *   `action_request_id_end` (FK to `hr_personnel_action_requests` for the return from leave action, nullable)
    *   `leave_type_id` (FK to `hr_leave_types` - e.g., Sabbatical, Maternity, ExtendedMedical)
    *   `planned_start_date` (DATE)
    *   `actual_start_date` (DATE, nullable)
    *   `expected_return_date` (DATE, nullable)
    *   `actual_return_date` (DATE, nullable)
    *   `status` (ENUM: 'Planned', 'Active', 'Returned', 'Cancelled') // Status of the leave itself
    *   `notes` (TEXT)

*   **`hr_leave_types`** (Lookup for long-term leave types)
    *   `id` (PK), `leave_type_code` (UK), `description`, `affects_payroll` (Boolean), `is_paid_leave` (Boolean)


**Note on Staging Data vs. Direct Future-Dated Records:**
This model leans towards **Option 3 (Directly create future-dated records)** mentioned in the initial thought process. When a change is fully *approved* via `hr_personnel_action_requests`:
1.  The previous active slice in tables like `hr_employee_job_assignments` has its `valid_to` date set to `requested_effective_date - 1 day`.
2.  A new slice is inserted with `valid_from = requested_effective_date` and `valid_to = '9999-12-31'`, containing the new data.
The `proposed_data_snapshot_json` in `hr_personnel_action_requests` serves as the record of what was requested and approved before it's applied to the master effective-dated tables. This simplifies querying current/historical data directly from the master tables.

This data model provides a robust foundation for managing effective-dated employee changes and their associated approval workflows.
