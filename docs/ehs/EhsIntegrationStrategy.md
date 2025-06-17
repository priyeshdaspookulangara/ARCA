# ARCA EHS (Environmental, Health, and Safety) Management Module: Integration Strategy

This document outlines the integration strategy for the ARCA Environmental, Health, and Safety (EHS) Management module with other ARCA ERP components. The goal is to ensure seamless data flow, consistent information, and integrated EHS processes across the enterprise.

## 1. Core Integration Principles

*   **Decoupling & Service-Oriented:** EHS will interact with other modules primarily through well-defined service interfaces (internal PHP contracts) and asynchronous events (message queues). Direct database dependencies on other modules' tables will be minimized.
*   **Explicit Contracts:** All interactions will use explicit, versioned contracts (Data Transfer Objects for API/event payloads, PHP interfaces).
*   **Central Authority for EHS Data:** EHS is the master system for incident records, risk assessments, hazardous substance details (extending core material data), waste management records, occupational health programs, and emissions/compliance data.
*   **Event-Driven Updates & Notifications:** Asynchronous events are preferred for notifying other modules of EHS-relevant occurrences (e.g., new high-severity incident, upcoming permit expiry) and for EHS to react to events from other modules (e.g., new hazardous material activated in MM).
*   **Idempotency:** Event listeners and API endpoints involved in EHS integrations must be idempotent.

## 2. Integration with ARCA HCM (Human Capital Management)

*   **Incident Management & Employee Linkage:**
    *   When an EHS incident is recorded involving employees (e.g., injured person, witness, reporter, investigator), the EHS module will link to the employee's master record in HCM (e.g., via `core_user_id` or a specific `hr_employee_id`).
    *   This allows for consistent employee information and supports reporting that correlates incidents with employee data (department, role, etc.).
*   **Occupational Health Programs:**
    *   EHS defines occupational health surveillance programs (e.g., hearing tests, respiratory checks).
    *   These programs are linked to specific employees or groups of employees (defined by job role, department, or exposure group from HCM data).
    *   EHS can send notifications or tasks (potentially via a central task management or workflow system, or directly to HR) for scheduling health appointments.
    *   Results of health surveillance (maintaining privacy) are stored in EHS, linked to the employee.
*   **EHS Training & Certification Tracking:**
    *   EHS defines required safety training or certifications for specific roles, tasks, or handling certain materials/equipment.
    *   **EHS -> HCM:** EHS can publish an event like `EhsTrainingRequiredEvent` (specifying training, target employee group/role).
    *   **HCM -> EHS:** HCM's training management sub-module manages course scheduling and records employee training completion. HCM then publishes an `HcmEmployeeTrainingCompletedEvent` (with employee ID, training ID, completion date).
    *   EHS subscribes to this event to update its records on employee EHS training compliance. This data is crucial for verifying if an employee is qualified for certain high-risk tasks.
*   **Absence Management:** Work-related injuries/illnesses recorded in EHS may lead to employee absences, which should be recorded and managed in HCM's absence/leave management system. EHS can notify HCM via an event (`EhsWorkRelatedAbsenceRecommendedEvent`).

## 3. Integration with ARCA MM (Materials Management) / CoreMDM

*   **Hazardous Substance Management & Material Master:**
    *   When a new material is created in `CoreMDM` or its status changes in MM:
        *   **MM/CoreMDM -> EHS:** MM/CoreMDM publishes `MaterialMasterChangedEvent` (or `MaterialHazardClassificationUpdatedEvent`).
        *   EHS subscribes to these events. If a material is classified as hazardous (e.g., via a flag in `core_materials` or extended MM data), EHS prompts for or links to detailed hazardous substance data (SDS, handling procedures, PPE) within the EHS module, associated with the `core_material_id`.
    *   EHS provides an API (`EhsHazardousSubstanceDetailsService::getDetails(core_material_id)`) for MM/PP/PM/SD to retrieve handling precautions or SDS links for display in their relevant processes (e.g., on POs, work orders, delivery notes).
*   **Inventory of Hazardous Materials:**
    *   EHS may need to query MM (or EWM if active) for current inventory levels and storage locations of specific hazardous substances for risk assessment, emergency response planning, and regulatory reporting (e.g., Tier II reporting in the US).
*   **Waste Material Management:**
    *   Waste generated (as defined in EHS Waste Management) can be set up as specific (non-valuated or low-value) material numbers in ARCA MM.
    *   This allows tracking of "waste inventory" in designated storage locations using MM functionalities.
    *   EHS waste disposal records can then trigger or be linked to MM goods issue transactions for "shipping" waste to disposal vendors.
*   **Procurement of Safety Equipment & EHS Supplies:**
    *   EHS can define requirements for Personal Protective Equipment (PPE), spill control materials, safety signage, etc.
    *   These requirements can generate Purchase Requisitions in ARCA MM for procurement.

## 4. Integration with ARCA QM (Quality Management)

*   **EHS Incidents Leading to Quality Non-Conformance:**
    *   If an EHS incident (e.g., environmental spill, product safety issue reported by customer) indicates a potential quality defect in a product or process:
        *   **EHS -> QM:** EHS can publish an `EhsIncidentSuggestsQualityIssueEvent` or provide a mechanism for an EHS investigator to manually trigger a Non-Conformance Report (NCR) in ARCA QM.
        *   The QM NCR process then handles the quality-related root cause analysis and corrective actions.
*   **Quality Inspections & EHS Relevance:**
    *   Results from QM inspections (e.g., incoming inspection of hazardous materials, in-process quality checks for safety-critical parameters) can be made available to EHS for:
        *   Verifying supplier compliance for hazardous materials.
        *   Input into EHS risk assessments.
    *   A QM finding might trigger an EHS review or incident report.

## 5. Integration with ARCA PM (Plant Maintenance)

*   **Equipment-Related EHS Risks & Safety Procedures:**
    *   EHS risk assessments may identify hazards associated with specific equipment managed in ARCA PM.
    *   Safety procedures, required PPE, and lockout/tagout (LOTO) requirements defined in EHS can be linked to equipment master records or maintenance task lists in PM. PM users should see these EHS requirements when planning/executing maintenance.
*   **Maintenance Triggered by EHS:**
    *   EHS inspections or incident investigations might reveal that equipment requires corrective maintenance (e.g., a safety guard is broken, a leak is detected).
    *   **EHS -> PM:** EHS can trigger a maintenance request or notification in ARCA PM, providing details of the EHS concern and the affected equipment.
*   **EHS Incidents Linked to Equipment:**
    *   Incidents recorded in EHS (e.g., an injury involving a machine) should be linkable to the specific equipment master record in PM for tracking equipment safety history.
*   **Permit to Work:** For high-risk maintenance jobs, a "Permit to Work" process managed or initiated by EHS might be required before PM work can commence. This involves safety checks, isolations, and authorizations.

## 6. Integration with ARCA FICO (Financial Accounting & Controlling)

*   **Tracking EHS-Related Costs:**
    *   **Incident Costs:**
        *   Medical expenses for injuries, costs of repairing property damage, fines from regulators, cleanup/remediation costs for environmental spills.
        *   EHS will capture these cost details. For actual financial posting, EHS can send an `EhsIncidentCostForPostingEvent` to FICO, or a financial administrator can use EHS data to make manual postings in FICO to specific EHS cost centers, internal orders (e.g., per incident), or WBS elements (if part of an EHS project).
    *   **Waste Disposal Costs:** Invoices from waste disposal vendors (processed in Fina AP) should be linkable to EHS waste disposal records for cost tracking and allocation.
    *   **Compliance Costs:** Costs for permits, licenses, EHS consulting, specialized EHS training, safety equipment procurement. These are typically managed directly in FICO but can be categorized or tagged for EHS reporting.
*   **Provisions & Accruals for EHS Liabilities:**
    *   EHS risk assessments or incident investigations might identify potential future liabilities (e.g., for site remediation, future claims).
    *   EHS provides this information to FICO to create necessary financial provisions or accruals.

## 7. EHS API Design

*   **Internal Service APIs (PHP Interfaces):** EHS will expose services for other ARCA modules to query EHS data (e.g., `getEmployeeEhsTrainingStatus(employeeId)`, `getHazardousMaterialDetails(materialId)`, `getActivePermitsForLocation(locationId)`).
*   **External RESTful APIs (Consideration):**
    *   Potentially for electronic submission of regulatory reports if supported by agencies.
    *   For integration with specialized EHS data providers (e.g., regulatory content subscriptions, chemical databases).

## 8. Event-Driven Communication involving EHS

*   **Events Published by EHS:**
    *   `EhsIncidentCreatedEvent` (with severity, type)
    *   `EhsNearMissReportedEvent`
    *   `EhsCorrectiveActionRequiredEvent`
    *   `EhsRiskIdentifiedEvent` (with risk level, location/equipment)
    *   `EhsPermitExpiringSoonEvent`
    *   `EhsComplianceStatusChangedEvent`
*   **Events Subscribed to by EHS:**
    *   `HcmEmployeeHiredEvent`, `HcmEmployeeTerminatedEvent`, `HcmEmployeePositionChangedEvent` (for occupational health & training)
    *   `CoreMaterialCreatedEvent`, `CoreMaterialUpdatedEvent` (to check for hazardous classifications)
    *   `PmMaintenanceOrderCreatedForSafetyIssueEvent` (from PM)
    *   `QmNonConformanceLinkedToEhsEvent` (from QM)
    *   `FinaCostPostedToEhsOrderEvent` (from FICO)

This integration strategy aims to embed EHS considerations and processes throughout the relevant ARCA ERP workflows.
