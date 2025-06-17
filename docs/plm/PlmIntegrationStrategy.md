# ARCA PLM (Product Lifecycle Management) Module: Integration Strategy

This document outlines the integration strategy for the ARCA Product Lifecycle Management (PLM) module with other ARCA ERP components (PP, MM, PS, QM, SCM/SRM, CoreMDM) and potential external systems. The strategy focuses on seamless data flow and process synchronization across the product lifecycle.

## 1. Core Integration Principles

*   **Decoupling & Service-Oriented:** PLM will interact with other modules primarily through well-defined service interfaces (internal PHP contracts) and asynchronous events (message queues). Direct database dependencies on other modules' tables will be minimized.
*   **Explicit Contracts:** All interactions will use explicit, versioned contracts (Data Transfer Objects for API/event payloads, PHP interfaces).
*   **Central Authority for Product Definition:** PLM is the master system for product definitions, engineering BOMs (EBOMs), manufacturing BOMs (MBOMs), product-related documents, and engineering change management (ECR/ECO).
*   **Event-Driven Updates:** Asynchronous events are preferred for notifying other modules of changes in PLM data (e.g., new product version, BOM release, ECO implementation) and for PLM to react to relevant events from other modules.
*   **Idempotency:** Event listeners and API endpoints involved in PLM integrations must be idempotent.

## 2. Integration with ARCA PP (Production Planning)

PLM provides the "what" and "how" (from a design perspective) for PP to execute production.

*   **MBOM & Routing Provision to PP:**
    *   **Event/API:** When an MBOM (and its associated routing, or routing reference) is released or updated in PLM for a specific plant, PLM publishes an event (e.g., `PlmMBOMReleasedEvent` with MBOM details and routing ID).
    *   **PP Action:** ARCA PP subscribes to this event and ingests/updates the MBOM and routing information into its own operational tables used for production order creation, MRP, and scheduling. PP may maintain its own copy or a link to the PLM-mastered version.
*   **Production Feedback to PLM (ECO Trigger):**
    *   If recurring issues are found in production (e.g., high defect rates for a component, assembly difficulties related to design), ARCA PP users can initiate a process that triggers an Engineering Change Request (ECR) in PLM.
    *   **Mechanism:** PP could publish a `PpProductionIssueFeedbackEvent` or provide an interface for users to create a pre-filled ECR in PLM.
*   **Component Changes & Effectivity:**
    *   When an ECO in PLM leads to a change in components (e.g., phase-in/phase-out), PLM communicates these changes (including effectivity dates/lot numbers) to PP so that MRP and production orders use the correct components.

## 3. Integration with ARCA MM (Materials Management)

PLM defines materials from a design perspective; MM manages their procurement, inventory, and logistical aspects.

*   **Material Master Linkage & Creation (with CoreMDM):**
    *   **New Part in PLM:** When a new product, assembly, or component is designed in PLM and needs to become a managed material:
        1.  PLM initiates a request to `CoreMDM` (Core Master Data Module/Service) to create a new `core_material_id`. This request includes basic data like description, base UoM, proposed material type.
        2.  CoreMDM creates the core material record and returns the `core_material_id`.
        3.  PLM stores this `core_material_id` and links its detailed PLM product/part definition to it.
        4.  CoreMDM (or PLM via CoreMDM) publishes a `CoreMaterialCreatedEvent`.
    *   **MM Action:** ARCA MM subscribes to `CoreMaterialCreatedEvent`. Upon receiving the event, MM users can then enrich the material master with MM-specific data (purchasing views, storage views, planning parameters in `lscm_material_plant_data`, etc.).
*   **Procurement for Prototypes & NPI:**
    *   For components required for prototypes or early NPI builds (often defined in PLM EBOMs before full MM setup):
        *   PLM, often via an NPI project in ARCA PS, will trigger the creation of Purchase Requisitions (PRs) in ARCA MM.
        *   These PRs will specify the (potentially new) material, quantity, required date, and link to the PS project WBS for costing.
*   **Material Attribute Synchronization/Reference:**
    *   PLM may reference certain MM attributes (e.g., preferred vendor, standard cost if available) for information during design.
    *   Conversely, PLM provides design specifications (via Document Management links) that MM uses for procurement and quality inspection of incoming materials.
*   **Material Classification:** PLM can provide input to the classification of materials (e.g., based on technical characteristics) which is also used in MM.

## 4. Integration with ARCA PS (Project System)

PLM NPI processes are typically managed as projects in ARCA PS.

*   **NPI Project Structure & Activity Linking:**
    *   Standard NPI project templates in PS can include WBS elements and activities that correspond to typical PLM phases (Concept, Design, Prototype, Test, Launch).
    *   Specific PLM tasks (e.g., "Complete EBOM for Product X", "Execute ECO-123") can be linked as activities or tracked against WBS elements in the NPI project within PS.
*   **Deliverable & Milestone Synchronization:**
    *   Key PLM deliverables (e.g., "Approved Design Specification," "EBOM Released," "Prototype Test Successful," "ECO Implemented") are defined as milestones in the PS NPI project.
    *   Completion of these deliverables in PLM (signaled by status changes or events like `PlmDesignApprovedEvent`) updates the corresponding milestone status in ARCA PS. This can trigger stage-gate reviews or release of next project phases in PS.
*   **Resource & Budget Management (PS leads, PLM informs):**
    *   NPI project budgets are managed in ARCA PS.
    *   Resource requirements for design, engineering, and prototyping activities (defined in PLM) are requested and allocated within ARCA PS, which integrates with HR for personnel resources.
    *   Costs associated with PLM activities (e.g., engineering hours, prototype material costs) are collected in PS.

## 5. Integration with ARCA QM (Quality Management)

Quality is integral throughout the product lifecycle.

*   **Quality in Design (QM -> PLM):**
    *   QM can provide quality standards, preferred component lists, or reliability data as input to the PLM design process.
*   **Inspection Plans & Specifications (PLM -> QM):**
    *   Product specifications and critical characteristics defined in PLM (e.g., in PDM or attached documents) are used by QM to create detailed inspection plans for incoming materials, in-process checks (linked to PP routings), and final product inspection.
*   **Prototype & First Article Inspection (QM -> PLM):**
    *   Results of quality inspections performed by QM on prototypes or first production articles are fed back to PLM.
    *   Failures or deviations can trigger ECRs in PLM to address design or manufacturing process issues. (`QmInspectionFailedForProductEvent` -> PLM creates ECR).
*   **Change Management & QM:**
    *   ECOs implemented in PLM (e.g., component change, design modification) may require updates to QM inspection plans. PLM publishes an `PlmProductChangedByECOEvent` that QM subscribes to.

## 6. Integration with ARCA SCM (SRM/Ariba - Supplier Collaboration)

For engaging suppliers in the product lifecycle.

*   **Component Co-Design & Specification Sharing:**
    *   PLM can securely share relevant (non-sensitive) parts of product designs, specifications, or drawings with strategic suppliers via an integrated SRM portal or Ariba network for collaborative design, DFM (Design for Manufacturability) feedback, or early supplier quotes.
*   **Sourcing New Components (PLM -> SRM/Ariba):**
    *   When a new component is specified in PLM (especially if it requires new tooling or has critical cost/supply implications), this can trigger an RFQ/RFP process in SRM/Ariba.
    *   The component specification from PLM is linked to the sourcing event.
*   **Approved Supplier/Manufacturer Lists (ASL/AML):**
    *   PLM may need to reference ASL/AML data (which suppliers are approved for which components/technologies) that is typically managed in conjunction with MM, QM, and SRM.

## 7. Core Master Data (CoreMDM) Integration

*   As described in section 3.1, PLM is a key system for initiating the creation of new `core_material_id` records for products, assemblies, and components via the CoreMDM service.
*   PLM relies on CoreMDM for the uniqueness and basic definition of these material identifiers.
*   Synchronization of basic descriptive data (e.g., short description, base UoM) between PLM's detailed product definition and the `core_materials` record should be ensured, typically with PLM pushing updates for these core fields to CoreMDM after initial creation.

## 8. PLM's API Design

*   **Internal Service APIs (PHP Interfaces):**
    *   Primary method for other ARCA modules to query PLM data (e.g., `getReleasedBom(materialId, version)`, `getDocumentLink(documentNumber, version)`).
    *   Used to trigger PLM actions (e.g., `initiateEcrFromExternal(sourceModule, details)`).
*   **External RESTful APIs (Consideration):**
    *   If PLM needs to integrate with standalone CAD systems (beyond file linking), specialized design tools, or external collaboration platforms not covered by SRM/Ariba, specific RESTful APIs might be exposed. These would need robust security and versioning.

## 9. Event-Driven Communication involving PLM

PLM is both a significant publisher and consumer of events.

*   **Events Published by PLM:**
    *   `PlmProductCreatedEvent` (after CoreMaterialID is assigned)
    *   `PlmProductVersionReleasedEvent` (for a specific product/part)
    *   `PlmEbomReleasedEvent`
    *   `PlmMbomReleasedEvent` (critical for PP)
    *   `PlmChangeRequestCreatedEvent`, `PlmChangeRequestStatusChangedEvent`
    *   `PlmChangeOrderCreatedEvent`, `PlmChangeOrderStatusChangedEvent` (e.g., Approved, Implemented)
    *   `PlmDocumentReleasedEvent`
*   **Events Subscribed to by PLM:**
    *   `CoreMaterialAttributesChangedEvent` (for basic data changes from CoreMDM)
    *   `PsNPIProjectPhaseCompletedEvent` (from ARCA PS)
    *   `QmInspectionResultRecordedEvent` (especially for prototypes or critical components)
    *   `PpProductionIssueFeedbackEvent` (from ARCA PP)
    *   `SrmSupplierDesignFeedbackReceivedEvent` (from ARCA SRM/Ariba)

This integration strategy positions PLM as a central hub for product intellectual property, tightly interwoven with the execution and planning systems of ARCA ERP.
