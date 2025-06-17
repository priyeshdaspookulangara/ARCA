# ARCA MDG (Master Data Governance) Module: Integration Strategy

This document outlines the integration strategy for the ARCA Master Data Governance (MDG) module with all other ARCA ERP components and potential external systems. MDG's role as the central authority for critical master data necessitates robust and well-defined integration patterns.

## 1. Core Integration Principles

*   **MDG as the Source of Truth:** For all governed master data objects (e.g., Customer, Vendor, Material, key Financial Master Data like GL Account, Cost Center), MDG is the definitive system of record for their creation, core global attributes, and lifecycle status (active, blocked, obsolete).
*   **Workflow-Driven Interactions:** Creation of and changes to governed master data are initiated and processed through MDG workflows. Consuming modules do not directly create or modify the core global attributes of this master data.
*   **Decoupling via Services & Events:**
    *   **Asynchronous (Events):** This is the primary method for distributing approved master data changes from MDG to consuming modules. MDG publishes events upon final workflow approval and activation of master data.
    *   **Synchronous (APIs):** MDG will expose internal service APIs (PHP Contracts) for specific synchronous needs, such as real-time validation lookups during transactional processes in other modules (e.g., "does this Material ID exist and is it active?") or for other modules to initiate MDG change request workflows.
*   **Explicit Contracts:** All interactions use versioned DTOs for API/event payloads and PHP interfaces for services.
*   **Data Consistency & Replication:** Consuming modules typically maintain a local, potentially partial, synchronized copy or a direct reference (ID) to the MDG master data they need for operational efficiency. MDG is responsible for ensuring these copies are updated.
*   **Idempotency:** Event listeners in consuming modules and MDG's own APIs must be idempotent.

## 2. Integration with All ARCA Modules (as Master Data Consumers)

This applies to FICO, LSCM (MM, SD, PP, PM, QM), CRM, PS, EHS, PLM, EWM, TM, LE, SRM, etc.

*   **Master Data Consumption:**
    *   All ARCA modules that utilize governed master data (Customer, Vendor, Material, Financial Master Data) will source this data from MDG.
    *   **Mechanism:**
        1.  **Initial Load/Full Sync:** When a consuming module is deployed or a new master data object is brought under MDG governance, an initial full synchronization may occur.
        2.  **Delta Updates (Event-Driven):** Upon final approval and activation of a master data record (create or change) in MDG, MDG publishes a specific event (e.g., `MdgCustomerMasterActivatedEvent`, `MdgMaterialGlobalDataChangedEvent`).
        3.  Consuming modules subscribe to these events. The event payload contains the relevant changed data (or the full record).
        4.  The consuming module's listener updates its local representation of the master data (e.g., its own `mm_materials_local_view` or `sd_customers_local_view` tables which might store a subset of fields plus module-specific extensions).
*   **Initiating Master Data Requests from Consuming Modules:**
    *   While users cannot directly create/change governed master data in consuming modules, these modules' UIs can provide a "Request New Material/Customer/Vendor" button.
    *   This action would call an MDG service API to initiate the appropriate MDG creation workflow, potentially pre-filling some data from the context of the consuming module (e.g., CRM pre-fills prospect details into an MDG "Create Customer" request).
*   **Validation Against MDG Data:**
    *   Transactional processes in consuming modules that reference governed master data (e.g., creating a sales order for a customer, a PO for a vendor/material) should validate the existence and active status of the master data ID against MDG (via a quick synchronous API call to MDG like `MdgValidationService::isMaterialActive(materialId)`).

## 3. Integration with ARCA AuthMgt (User Role & Authorization Management)

*   **Workflow User Assignments:** MDG's internal workflows for data stewardship and approvals will heavily rely on `AuthMgt`.
    *   Workflow tasks (e.g., "Approve Material Creation Request," "Steward Vendor Address Change") will be assigned to users or user groups/roles defined in `AuthMgt`.
    *   MDG's workflow engine will query `AuthMgt` to determine task assignees and verify if a user performing an approval has the necessary permissions (e.g., `MDG_Approve_Material_L1`).
*   **Role-Based Access to MDG Functionality:** Access to MDG's own administrative UIs (for managing workflows, data quality rules, viewing audit trails) will be controlled by specific administrative roles defined in `AuthMgt`.

## 4. Integration with ARCA BI (Business Intelligence)

*   **Foundation for Accurate Analytics:** MDG is critical for BI. By providing clean, consistent, and de-duplicated master data, MDG ensures that BI reports and analytics are accurate and reliable.
*   **BI Sourcing Master Data Dimensions:** The ARCA BI solution will source its core master data dimensions (Customer, Vendor, Material, Product Hierarchy, GL Account Hierarchy, etc.) directly from MDG's approved, active master data records or from the synchronized representations in the primary transactional modules.

## 5. Integration with External Systems

*   **Data Inbound to MDG (Initial Load / Synchronization from External Sources):**
    *   MDG will provide staging tables and import/API capabilities to load master data from external sources (e.g., during initial ERP implementation, from acquired company systems, or from specialized external data providers).
    *   Imported data MUST pass through MDG's data quality validation rules and may trigger MDG workflows for approval before activation.
*   **Data Outbound from MDG (Distribution to External Systems):**
    *   When approved master data is created or changed, MDG can distribute it to configured external systems (e.g., a global CRM different from ARCA CRM, a specialized procurement platform like Ariba, partner portals, regulatory agencies).
    *   **Mechanisms:**
        *   Publishing events that an integration layer/middleware can subscribe to and then transform/send data to the external system.
        *   MDG exposing specific APIs that external systems (or middleware) can call to fetch master data.
        *   Scheduled batch extracts/feeds prepared by MDG.
    *   Data mapping and transformation capabilities may be required within the integration layer if external systems have different data formats or standards.

## 6. Event-Driven Architecture (EDA) for MDG

MDG is a central publisher of master data lifecycle events.

*   **Key Events Published by MDG:**
    *   `MdgChangeRequestCreatedEvent({objectType, crId, requestedData})`
    *   `MdgChangeRequestWorkflowStepCompletedEvent({crId, workflowStep, status, approverUserId})`
    *   `MdgChangeRequestApprovedEvent({crId, objectType, objectId, finalData})`
    *   `MdgChangeRequestRejectedEvent({crId, reason})`
    *   **`MdgMasterDataRecordActivatedEvent({objectType, objectId, data})`**: This is a critical event for consuming systems. It signifies new or changed master data is ready for use. (e.g., `MdgCustomerActivatedEvent`, `MdgMaterialActivatedEvent`).
    *   `MdgMasterDataRecordAttributesChangedEvent({objectType, objectId, changedAttributesData})`
    *   `MdgMasterDataRecordStatusChangedEvent({objectType, objectId, newStatus})` (e.g., marked for deletion, blocked)
    *   `MdgDataQualityIssueDetectedEvent({objectType, objectId, issueDetails})`
    *   `MdgDuplicateFoundEvent({objectType, objectId, potentialDuplicateIds})`
*   **Events Subscribed to by MDG (Examples):**
    *   Potentially, events from `AuthMgt` like `AuthMgtUserRoleChangedEvent` if it impacts data steward assignments within MDG workflows.
    *   Events from consuming systems that might indicate a data quality issue with existing master data, prompting an MDG review workflow.

## 7. Initial Data Load & Migration Strategy Considerations

*   During MDG implementation, a significant effort will be the initial load and consolidation of existing master data from various ARCA modules (if they pre-exist MDG with their own local master data) or from legacy systems.
*   **Process:**
    1.  Extract data from source systems.
    2.  Perform extensive data profiling and cleansing.
    3.  Map source data to MDG's target structures for governed objects.
    4.  Run deduplication processes on the extracted/mapped data.
    5.  Load cleansed, de-duplicated data into MDG staging tables.
    6.  Process staged data through (potentially simplified initial load) MDG workflows for validation and approval.
    7.  Once activated in MDG, this data is then replicated to consuming ARCA modules to establish the baseline.
*   This process requires careful planning and robust tooling.

This integration strategy ensures that MDG serves as the central nervous system for master data quality and consistency across the entire ARCA ERP landscape.
