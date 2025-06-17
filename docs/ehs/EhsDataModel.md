# ARCA EHS (Environmental, Health, and Safety) Management Module: Data Model Design (MySQL)

This document outlines the proposed MySQL database schema design for the ARCA Environmental, Health, and Safety (EHS) Management module. All EHS-specific tables will use the `ehs_` prefix.

## 1. General Principles

*   **Prefixing:** All tables specific to EHS are prefixed with `ehs_`.
*   **Modularity:** Links to core ARCA data (users, materials, equipment, locations) are via IDs.
*   **Data Privacy:** Occupational health records will require special consideration for access control and potential encryption, adhering to privacy regulations.
*   **Auditability:** Standard audit columns (`created_at`, `updated_at`, `created_by_user_id`, `updated_by_user_id`) on key tables.

## 2. Incident Management

*   **`ehs_incidents`**
    *   `id` (PK)
    *   `incident_number` (UK, system-generated)
    *   `title` (VARCHAR)
    *   `incident_type_id` (FK to `ehs_incident_types`)
    *   `incident_datetime` (DATETIME)
    *   `reported_datetime` (DATETIME)
    *   `reported_by_user_id` (FK to `core_users`)
    *   `location_description` (TEXT)
    *   `lscm_plant_id` (FK to `lscm_plants` or `core_organization_units`, nullable)
    *   `department_id` (FK to `hr_departments` or `core_organization_units`, nullable)
    *   `description_of_incident` (TEXT)
    *   `immediate_actions_taken` (TEXT, nullable)
    *   `severity_actual_id` (FK to `ehs_severity_levels`, nullable)
    *   `severity_potential_id` (FK to `ehs_severity_levels`, nullable)
    *   `status_id` (FK to `ehs_incident_statuses` - e.g., 'Reported', 'Investigation', 'PendingCAPA', 'Closed')
    *   `is_regulatory_reportable` (Boolean, default false)
    *   `closed_at` (DATETIME, nullable)
    *   `created_at`, `updated_at`, `created_by_user_id`, `updated_by_user_id`

*   **`ehs_incident_types`** (e.g., Injury/Illness, Near Miss, Environmental Spill, Property Damage, Safety Observation)
    *   `id` (PK)
    *   `type_name` (UK)
    *   `description`

*   **`ehs_severity_levels`** (Configurable, e.g., Minor, Moderate, Serious, Catastrophic - for actual & potential)
    *   `id` (PK)
    *   `level_name` (UK)
    *   `description`
    *   `sort_order` (INT)

*   **`ehs_incident_statuses`**
    *   `id` (PK)
    *   `status_name` (UK)

*   **`ehs_incident_involved_parties`**
    *   `id` (PK)
    *   `incident_id` (FK to `ehs_incidents`)
    *   `party_type` (ENUM: 'Employee', 'Contractor', 'Visitor', 'Equipment', 'Material', 'Other')
    *   `core_user_id_employee` (FK, nullable)
    *   `contractor_name` (VARCHAR, nullable)
    *   `visitor_name` (VARCHAR, nullable)
    *   `lscm_pm_equipment_id` (FK, nullable)
    *   `core_material_id` (FK, nullable)
    *   `role_in_incident` (VARCHAR, e.g., 'Injured', 'Witness', 'DamagedEquipment', 'SpilledMaterial')
    *   `details` (TEXT, nullable)

*   **`ehs_incident_investigations`**
    *   `id` (PK)
    *   `incident_id` (FK, UK - one investigation per incident initially)
    *   `lead_investigator_user_id` (FK to `core_users`)
    *   `start_date`, `end_date` (nullable)
    *   `investigation_summary` (TEXT)
    *   `root_cause_analysis_method` (VARCHAR, e.g., "5 Whys", "Fishbone")
    *   `root_causes_identified_text` (TEXT)
    *   `status` (ENUM: 'Planned', 'InProgress', 'Completed')

*   **`ehs_capa_actions`** (Corrective And Preventive Actions)
    *   `id` (PK)
    *   `incident_id` (FK to `ehs_incidents`, nullable - CAPA can also be from audits, risks)
    *   `risk_assessment_id` (FK to `ehs_risk_assessments_header`, nullable)
    *   `audit_finding_id` (FK to `ehs_audit_findings`, nullable)
    *   `action_type` (ENUM: 'Corrective', 'Preventive')
    *   `description` (TEXT)
    *   `assigned_to_user_id` (FK to `core_users`)
    *   `due_date` (DATE)
    *   `status` (ENUM: 'Open', 'InProgress', 'PendingVerification', 'Completed', 'Cancelled')
    *   `completion_date` (DATE, nullable)
    *   `effectiveness_verification_notes` (TEXT, nullable)
    *   `effectiveness_verified_by_user_id` (FK, nullable)
    *   `effectiveness_verified_date` (DATE, nullable)

## 3. Risk Assessment

*   **`ehs_risk_assessments_header`**
    *   `id` (PK)
    *   `assessment_code` (UK)
    *   `title` (VARCHAR)
    *   `scope_description` (TEXT)
    *   `assessment_date` (DATE)
    *   `team_members_text` (TEXT, or link to users)
    *   `lscm_plant_id` (FK, nullable)
    *   `department_id` (FK, nullable)
    *   `status` (ENUM: 'Draft', 'InProgress', 'Completed', 'Reviewed')
    *   `review_date_next` (DATE, nullable)

*   **`ehs_hazards_catalogue`** (Predefined list of common hazards)
    *   `id` (PK)
    *   `hazard_code` (UK)
    *   `description` (VARCHAR)
    *   `category` (VARCHAR, e.g., "Physical", "Chemical", "Biological", "Ergonomic")

*   **`ehs_risks_register`** (Identified risks within an assessment)
    *   `id` (PK)
    *   `risk_assessment_header_id` (FK)
    *   `hazard_catalogue_id` (FK, optional)
    *   `custom_hazard_description` (VARCHAR, if not from catalogue)
    *   `activity_or_area_at_risk` (VARCHAR)
    *   `potential_consequence` (TEXT)
    *   `existing_controls_text` (TEXT)
    *   `likelihood_initial_id` (FK to `ehs_likelihood_scales`)
    *   `severity_initial_id` (FK to `ehs_severity_scales` - can be same as incident severities)
    *   `risk_score_initial` (INT, calculated)
    *   `status` (ENUM: 'Open', 'MitigationPlanned', 'Mitigated', 'Accepted')

*   **`ehs_likelihood_scales` / `ehs_severity_scales`** (Configurable scales for risk matrix)
    *   `id`, `scale_name`, `value_numeric`, `description`

*   **`ehs_risk_mitigation_controls`** (Controls planned/implemented for a risk)
    *   `id` (PK)
    *   `risk_register_id` (FK)
    *   `control_description` (TEXT)
    *   `control_type` (ENUM: 'Elimination', 'Substitution', 'Engineering', 'Administrative', 'PPE')
    *   `capa_action_id` (FK to `ehs_capa_actions`, if control is a CAPA)
    *   `status` (ENUM: 'Planned', 'Implemented', 'Verified')
    *   `likelihood_residual_id` (FK, nullable)
    *   `severity_residual_id` (FK, nullable)
    *   `risk_score_residual` (INT, nullable)

## 4. Hazardous Substance Management

*   **`ehs_hazardous_substances`**
    *   `id` (PK)
    *   `core_material_id` (FK to `core_materials`, UK - one EHS record per material)
    *   `common_name` (VARCHAR, if different from material master)
    *   `cas_number` (VARCHAR, indexed)
    *   `ghs_hazard_classifications_json` (JSON array of GHS codes/phrases)
    *   `sds_document_id_current` (FK to `ehs_sds_documents`, nullable)
    *   `storage_compatibility_groups_json` (JSON array)
    *   `ppe_requirements_text` (TEXT)

*   **`ehs_sds_documents`** (Safety Data Sheets)
    *   `id` (PK)
    *   `hazardous_substance_id` (FK)
    *   `sds_version` (VARCHAR)
    *   `supplier_name` (VARCHAR, optional)
    *   `issue_date` (DATE)
    *   `expiry_date` (DATE, nullable for review)
    *   `language_code` (VARCHAR)
    *   `document_file_id` (FK to `plm_document_files` or a generic DMS file ID if EHS docs are in PLM/DMS)
    *   `is_active` (Boolean)

## 5. Waste Management

*   **`ehs_waste_streams`**
    *   `id` (PK)
    *   `stream_code` (UK)
    *   `description` (VARCHAR)
    *   `waste_classification_code` (VARCHAR, e.g., EWC code)
    *   `is_hazardous` (Boolean)
    *   `disposal_method_default_id` (FK to `ehs_disposal_methods`, nullable)

*   **`ehs_waste_generation_log`**
    *   `id` (PK)
    *   `waste_stream_id` (FK)
    *   `generation_date` (DATE)
    *   `quantity` (Decimal)
    *   `unit_of_measure_id` (FK to `core_units_of_measure`)
    *   `lscm_plant_id` (FK)
    *   `source_department_or_cost_center_id` (FK, optional)
    *   `core_material_id_waste` (FK, if tracked as a material in MM)
    *   `notes` (TEXT)

*   **`ehs_waste_disposal_records`**
    *   `id` (PK)
    *   `generation_log_id` (FK, optional if consolidating multiple generations)
    *   `waste_stream_id` (FK)
    *   `disposal_date` (DATE)
    *   `quantity_disposed` (Decimal)
    *   `unit_of_measure_id` (FK)
    *   `disposal_method_id` (FK to `ehs_disposal_methods`)
    *   `waste_transporter_bp_id` (FK to `core_business_partners` - vendor)
    *   `disposal_facility_bp_id` (FK to `core_business_partners` - vendor)
    *   `manifest_document_number` (VARCHAR, nullable)
    *   `fina_cost_amount` (Decimal, nullable)
    *   `fina_invoice_reference` (VARCHAR, nullable)

*   **`ehs_disposal_methods`** (e.g., Recycle, Landfill, Incineration)
    *   `id` (PK)
    *   `method_code` (UK)
    *   `description`

## 6. Occupational Health

*   **`ehs_health_surveillance_programs`**
    *   `id` (PK)
    *   `program_name` (UK)
    *   `description` (TEXT)
    *   `frequency_months` (INT, nullable)
    *   `target_employee_group_description` (TEXT, e.g., "Welders", "Chemical Lab Staff")

*   **`ehs_employee_health_records`** (Highly sensitive - requires strict access controls)
    *   `id` (PK)
    *   `core_user_id_employee` (FK)
    *   `surveillance_program_id` (FK, nullable)
    *   `record_type` (VARCHAR, e.g., "AudiometryTest", "LungFunctionTest", "Vaccination")
    *   `record_date` (DATE)
    *   `summary_or_result` (TEXT)
    *   `next_due_date` (DATE, nullable)
    *   `clinic_or_provider_name` (VARCHAR, nullable)
    *   `attached_document_id` (FK to a secure document storage, nullable)

*   **`ehs_exposure_log`** (Employee exposure records)
    *   `id` (PK)
    *   `core_user_id_employee` (FK)
    *   `hazardous_substance_id` (FK, nullable)
    *   `exposure_agent_name` (VARCHAR, if not a substance, e.g., "Noise")
    *   `exposure_date` (DATE)
    *   `duration_minutes` (INT, nullable)
    *   `exposure_level_value` (Decimal, nullable)
    *   `exposure_level_unit` (VARCHAR, nullable, e.g., "ppm", "dB")
    *   `ppe_used_text` (TEXT, nullable)

## 7. Emissions & Compliance Management

*   **`ehs_emission_sources`** (e.g., Stack A1, Wastewater Outlet 001)
    *   `id` (PK)
    *   `source_code` (UK)
    *   `description`
    *   `lscm_plant_id` (FK)
    *   `emission_type` (ENUM: 'Air', 'Water', 'WasteToLand')

*   **`ehs_emission_parameters`** (Parameters monitored for a source, e.g., CO2, NOx, pH, COD)
    *   `id` (PK)
    *   `parameter_name` (UK)
    *   `description`
    *   `default_unit_of_measure_id` (FK)

*   **`ehs_emission_readings_log`**
    *   `id` (PK)
    *   `emission_source_id` (FK)
    *   `emission_parameter_id` (FK)
    *   `reading_datetime` (DATETIME)
    *   `value_measured` (Decimal)
    *   `unit_of_measure_id` (FK)
    *   `is_within_limits` (Boolean, nullable)
    *   `notes` (TEXT)

*   **`ehs_permits_licenses`**
    *   `id` (PK)
    *   `permit_number` (UK)
    *   `permit_type_id` (FK to `ehs_permit_types`)
    *   `description` (VARCHAR)
    *   `issuing_authority` (VARCHAR)
    *   `issue_date`, `expiry_date`
    *   `lscm_plant_id` (FK, if plant specific)
    *   `status` (ENUM: 'Active', 'Expired', 'PendingRenewal')
    *   `conditions_summary_text` (TEXT, nullable)

*   **`ehs_permit_types`** (e.g., Air Permit, Water Discharge Permit, Waste Handling License)
    *   `id` (PK)
    *   `type_name` (UK)

*   **`ehs_compliance_obligations_register`** (Specific legal or other requirements)
    *   `id` (PK)
    *   `obligation_source` (VARCHAR, e.g., "Clean Air Act Section 5.2", "ISO 14001 Clause 6.1.3")
    *   `description` (TEXT)
    *   `responsible_department_id` (FK, nullable)
    *   `frequency_of_check` (VARCHAR, nullable)
    *   `last_check_date` (DATE, nullable)
    *   `next_check_due_date` (DATE, nullable)

*   **`ehs_audits`**
    *   `id` (PK)
    *   `audit_code` (UK)
    *   `audit_type` (ENUM: 'InternalEHS', 'ExternalISO14001', 'RegulatoryInspection')
    *   `scope` (TEXT)
    *   `planned_start_date`, `planned_end_date`
    *   `actual_start_date`, `actual_end_date`
    *   `lead_auditor_user_id` (FK, nullable)
    *   `status` (ENUM: 'Planned', 'InProgress', 'Completed', 'Reported')

*   **`ehs_audit_findings`**
    *   `id` (PK)
    *   `audit_id` (FK)
    *   `finding_type` (ENUM: 'NonConformance', 'Observation', 'OpportunityForImprovement')
    *   `description` (TEXT)
    *   `severity` (ENUM: 'Major', 'Minor', 'Low', nullable)
    *   `reference_clause_or_area` (VARCHAR, nullable)
    *   `capa_action_id` (FK to `ehs_capa_actions`, nullable, if CAPA created)

This data model structure provides a comprehensive foundation for the ARCA EHS module.
