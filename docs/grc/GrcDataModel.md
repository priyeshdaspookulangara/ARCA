# ARCA GRC (Governance, Risk, and Compliance) Module: Data Model Design (MySQL)

This document outlines the proposed MySQL database schema design for the ARCA Governance, Risk, and Compliance (GRC) module. All GRC-specific tables will use the `grc_` prefix. This model supports Access Control oversight, Process Control, Risk Management, Audit Management, and Compliance Management.

## 1. General Principles

*   **Prefixing:** All tables specific to GRC are prefixed with `grc_`.
*   **Integration Focus:** Tables are designed to store GRC-specific data and link to other ARCA modules (especially `AuthMgt`) via IDs. GRC does not duplicate master data owned by other modules but extends it for its purpose.
*   **Workflow Support:** Many entities will have status fields and link to user/timestamp data to support GRC workflows.
*   **Auditability:** Standard audit columns (`created_at`, `updated_at`, `created_by_user_id`, `updated_by_user_id`) on key tables.

## 2. Access Control (SoD & User Provisioning) Extensions

These tables extend or provide oversight to `AuthMgt` functionalities.

*   **`grc_sod_analysis_runs`** (Log of SoD analysis executions)
    *   `id` (PK)
    *   `run_datetime` (TIMESTAMP)
    *   `run_type` (ENUM: 'UserAssignments', 'RoleDefinitions', 'Simulation')
    *   `scope_description` (TEXT, e.g., "All active users", "Role SR_FIN_CLERK")
    *   `triggered_by_user_id` (FK to `auth_users`)
    *   `status` (ENUM: 'InProgress', 'Completed', 'Failed')
    *   `summary_json` (JSON, e.g., number of users analyzed, number of conflicts found)

*   **`grc_sod_run_violations`** (Detected violations from a specific SoD analysis run)
    *   `id` (PK)
    *   `sod_analysis_run_id` (FK to `grc_sod_analysis_runs`)
    *   `auth_user_id` (FK to `auth_users`, nullable if role-level violation)
    *   `auth_role_id` (FK to `auth_roles_single_header` or `auth_roles_composite_header`, nullable)
    *   `auth_sod_rule_header_id` (FK to `auth_sod_rules_header` from AuthMgt)
    *   `violation_details_json` (JSON, specific conflicting permissions/roles)
    *   `status` (ENUM: 'New', 'UnderReview', 'Mitigated', 'AcceptedRisk')
    *   `mitigation_id` (FK to `auth_sod_mitigations` from AuthMgt, nullable)

*   **`grc_user_provisioning_requests_ext`** (Extends `auth_access_requests` if GRC needs more workflow states/data)
    *   `id` (PK)
    *   `auth_access_request_id` (FK to `auth_access_requests`, UK)
    *   `grc_workflow_instance_id` (VARCHAR, link to a GRC workflow engine instance)
    *   `current_grc_approval_step` (VARCHAR)
    *   `grc_review_notes_text` (TEXT)

*   **`grc_firefighter_session_reviews`** (GRC oversight of FF sessions from AuthMgt)
    *   `id` (PK)
    *   `auth_firefighter_session_log_id` (FK to `auth_firefighter_sessions_log`, UK)
    *   `review_status` (ENUM: 'PendingReview', 'ReviewedApproved', 'ReviewedWithFindings')
    *   `reviewer_user_id` (FK to `auth_users`)
    *   `review_datetime` (TIMESTAMP)
    *   `review_comments` (TEXT)

## 3. Process Control

*   **`grc_business_processes`** (Optional, if GRC defines its own hierarchy, or links to one)
    *   `id` (PK)
    *   `process_code` (UK)
    *   `name` (VARCHAR)
    *   `description` (TEXT)
    *   `parent_process_id` (Self-referential FK, nullable)
    *   `owner_user_id` (FK to `auth_users`)

*   **`grc_internal_controls`** (Central library of controls)
    *   `id` (PK)
    *   `control_code` (UK)
    *   `name` (VARCHAR)
    *   `description` (TEXT)
    *   `control_objective` (TEXT)
    *   `control_type` (ENUM: 'Preventive', 'Detective', 'Manual', 'Automated', 'SemiAutomated')
    *   `control_frequency` (VARCHAR, e.g., "Daily", "Monthly", "PerTransaction")
    *   `owner_user_id` (FK to `auth_users`)
    *   `automation_potential` (ENUM: 'High', 'Medium', 'Low', 'None')
    *   `is_key_control` (Boolean)
    *   `status` (ENUM: 'Draft', 'Active', 'Inactive', 'Retired')

*   **`grc_process_control_mappings`** (Links controls to processes)
    *   `id` (PK)
    *   `grc_business_process_id` (FK)
    *   `grc_internal_control_id` (FK)
    *   UNIQUE (`grc_business_process_id`, `grc_internal_control_id`)

*   **`grc_control_tests_planned`** (Planned assessments of control effectiveness)
    *   `id` (PK)
    *   `internal_control_id` (FK)
    *   `test_plan_name` (VARCHAR)
    *   `test_methodology` (TEXT)
    *   `planned_test_date_start`, `planned_test_date_end`
    *   `assigned_tester_user_id` (FK to `auth_users`)
    *   `status` (ENUM: 'Planned', 'InProgress', 'Completed')

*   **`grc_control_test_results`**
    *   `id` (PK)
    *   `control_test_planned_id` (FK)
    *   `actual_test_date` (DATE)
    *   `outcome` (ENUM: 'Effective', 'Ineffective', 'PartiallyEffective')
    *   `summary_of_findings` (TEXT)
    *   `evidence_link_json` (JSON, links to documents or transaction IDs)
    *   `tested_by_user_id` (FK to `auth_users`)

*   **`grc_ccm_rules`** (Continuous Control Monitoring rules)
    *   `id` (PK)
    *   `rule_code` (UK)
    *   `description` (VARCHAR)
    *   `internal_control_id` (FK, the control this rule monitors)
    *   `target_module` (VARCHAR, e.g., "FICO", "LSCM_MM")
    *   `monitoring_logic_description` (TEXT)
    *   `monitoring_script_or_query_path` (VARCHAR, path to the executable logic if stored as code/script)
    *   `frequency` (VARCHAR, e.g., "Hourly", "Daily", "Weekly")
    *   `is_active` (Boolean)

*   **`grc_ccm_exceptions`** (Log of exceptions found by CCM rules)
    *   `id` (PK)
    *   `ccm_rule_id` (FK)
    *   `exception_datetime` (TIMESTAMP)
    *   `exception_details_json` (JSON, data that triggered the exception)
    *   `status` (ENUM: 'New', 'UnderInvestigation', 'RemediationPlanned', 'Closed')
    *   `assigned_to_user_id` (FK, for investigation)
    *   `severity` (ENUM: 'Low', 'Medium', 'High')

*   **`grc_control_deficiencies`** (Deficiencies found from tests or CCM)
    *   `id` (PK)
    *   `internal_control_id` (FK)
    *   `ccm_exception_id` (FK, nullable)
    *   `control_test_result_id` (FK, nullable)
    *   `description` (TEXT)
    *   `identified_date` (DATE)
    *   `owner_user_id` (FK)
    *   `status` (ENUM: 'Open', 'RemediationInProgress', 'Resolved', 'Closed')

*   **`grc_remediation_plans`** (Actions to address deficiencies or audit findings)
    *   `id` (PK)
    *   `control_deficiency_id` (FK, nullable)
    *   `audit_finding_id` (FK, nullable)
    *   `risk_mitigation_action_id` (FK, nullable - if this plan is part of risk treatment)
    *   `plan_description` (TEXT)
    *   `assigned_to_user_id` (FK)
    *   `due_date` (DATE)
    *   `status` (ENUM: 'Open', 'InProgress', 'Completed', 'Cancelled')
    *   `completion_notes` (TEXT)

## 4. Risk Management

*   **`grc_risk_categories`** (e.g., Financial, Operational, Strategic, Compliance, IT)
    *   `id` (PK)
    *   `category_name` (UK)
    *   `description`

*   **`grc_risks_register`** (Central Risk Register)
    *   `id` (PK)
    *   `risk_code` (UK)
    *   `risk_name` (VARCHAR)
    *   `description` (TEXT)
    *   `risk_category_id` (FK)
    *   `owner_user_id` (FK to `auth_users`)
    *   `business_process_id` (FK to `grc_business_processes`, nullable)
    *   `last_assessment_id` (FK to `grc_risk_assessments`, nullable - points to latest assessment)
    *   `current_likelihood_id` (FK to `grc_likelihood_scales_risk`)
    *   `current_impact_id` (FK to `grc_impact_scales_risk`)
    *   `current_risk_score` (INT)
    *   `risk_treatment_strategy` (ENUM: 'Avoid', 'Mitigate', 'Transfer', 'Accept')
    *   `status` (ENUM: 'Identified', 'Assessed', 'TreatmentPlanned', 'Monitoring', 'Closed')

*   **`grc_risk_assessments`** (Record of an assessment activity)
    *   `id` (PK)
    *   `risk_register_id` (FK)
    *   `assessment_date` (DATE)
    *   `assessed_by_user_id` (FK)
    *   `likelihood_id` (FK)
    *   `impact_id` (FK)
    *   `risk_score_calculated` (INT)
    *   `assessment_notes` (TEXT)

*   **`grc_likelihood_scales_risk` / `grc_impact_scales_risk`** (Similar to EHS scales, but for GRC risks)
    *   `id`, `scale_name`, `value_numeric`, `description`

*   **`grc_risk_control_mappings`** (Links risks to mitigating controls)
    *   `risk_register_id` (FK)
    *   `internal_control_id` (FK)
    *   PRIMARY KEY (`risk_register_id`, `internal_control_id`)

*   **`grc_risk_mitigation_actions`** (Specific actions for risk treatment)
    *   `id` (PK)
    *   `risk_register_id` (FK)
    *   `action_description` (TEXT)
    *   `assigned_to_user_id` (FK)
    *   `due_date` (DATE)
    *   `status` (ENUM: 'Planned', 'InProgress', 'Completed', 'Deferred')
    *   (May link to `grc_remediation_plans` if action involves fixing a control)

*   **`grc_kri_definitions`** (Key Risk Indicator definitions)
    *   `id` (PK)
    *   `kri_code` (UK)
    *   `description`
    *   `risk_register_id` (FK, the risk this KRI monitors)
    *   `measurement_unit`
    *   `threshold_green`, `threshold_yellow`, `threshold_red` (Decimal)
    *   `data_source_description` (TEXT)

*   **`grc_kri_readings_log`**
    *   `id` (PK)
    *   `kri_definition_id` (FK)
    *   `reading_date` (DATE)
    *   `value` (Decimal)
    *   `status_calculated` (ENUM: 'Green', 'Yellow', 'Red')

## 5. Audit Management

*   **`grc_audit_universe_items`** (List of all auditable entities)
    *   `id` (PK)
    *   `item_name` (VARCHAR)
    *   `item_type` (VARCHAR, e.g., "BusinessProcess", "Department", "System", "Regulation")
    *   `description` (TEXT)
    *   `risk_level_inherent` (ENUM, optional)

*   **`grc_audit_plans_annual`**
    *   `id` (PK)
    *   `year` (INT, UK)
    *   `plan_name` (VARCHAR)
    *   `status` (ENUM: 'Draft', 'Approved', 'InProgress', 'Completed')

*   **`grc_audit_engagements`** (Specific audit projects)
    *   `id` (PK)
    *   `audit_plan_annual_id` (FK, optional)
    *   `engagement_code` (UK)
    *   `engagement_name` (VARCHAR)
    *   `audit_universe_item_id` (FK, optional, if directly auditing one item)
    *   `scope_objectives` (TEXT)
    *   `lead_auditor_user_id` (FK)
    *   `planned_start_date`, `planned_end_date`
    *   `actual_start_date`, `actual_end_date`
    *   `status` (ENUM: 'Planned', 'Fieldwork', 'Reporting', 'FollowUp', 'Completed')

*   **`grc_audit_findings`**
    *   `id` (PK)
    *   `audit_engagement_id` (FK)
    *   `finding_code` (UK within engagement)
    *   `description` (TEXT)
    *   `criteria_breached` (TEXT)
    *   `root_cause` (TEXT, optional)
    *   `recommendation` (TEXT)
    *   `severity` (ENUM: 'High', 'Medium', 'Low', 'Informational')
    *   `status` (ENUM: 'Open', 'ManagementResponsePending', 'RemediationInProgress', 'Closed')
    *   `owner_user_id` (FK, responsible for remediation)
    *   `due_date_remediation` (DATE, nullable)

*   **`grc_audit_finding_responses`** (Management response to findings)
    *   `id` (PK)
    *   `audit_finding_id` (FK)
    *   `response_text` (TEXT)
    *   `agreed_action_plan` (TEXT)
    *   `responsible_user_id` (FK)
    *   `planned_completion_date` (DATE)
    *   `response_by_user_id` (FK)
    *   `response_date` (DATE)

## 6. Compliance Management

*   **`grc_regulatory_bodies`**
    *   `id` (PK)
    *   `name` (UK)
    *   `jurisdiction` (VARCHAR)

*   **`grc_regulations_standards`** (Library of regulations, laws, standards, internal policies)
    *   `id` (PK)
    *   `document_name` (UK)
    *   `document_type` (ENUM: 'Regulation', 'Standard', 'InternalPolicy')
    *   `regulatory_body_id` (FK, nullable)
    *   `version` (VARCHAR)
    *   `issue_date`, `effective_date`
    *   `description` (TEXT)
    *   `link_to_source_document` (VARCHAR, URL or DMS link)

*   **`grc_compliance_requirements`** (Specific actionable requirements from regulations/policies)
    *   `id` (PK)
    *   `regulation_standard_id` (FK)
    *   `requirement_code` (UK within regulation)
    *   `description` (TEXT)
    *   `responsible_department_id` (FK, nullable)

*   **`grc_requirement_control_mappings`**
    *   `compliance_requirement_id` (FK)
    *   `internal_control_id` (FK)
    *   PRIMARY KEY (`compliance_requirement_id`, `internal_control_id`)

*   **`grc_compliance_assessments`** (Record of assessment against a regulation/policy)
    *   `id` (PK)
    *   `regulation_standard_id` (FK)
    *   `assessment_name` (VARCHAR)
    *   `assessment_date` (DATE)
    *   `overall_compliance_status` (ENUM: 'Compliant', 'NonCompliant', 'PartiallyCompliant')
    *   `summary` (TEXT)

*   **`grc_policy_attestations`** (Tracking employee acknowledgement of policies)
    *   `id` (PK)
    *   `regulation_standard_id` (FK, where document_type is 'InternalPolicy')
    *   `auth_user_id` (FK)
    *   `attestation_datetime` (TIMESTAMP)
    *   `policy_version_attested` (VARCHAR)

This data model provides a comprehensive structure for the GRC module.
