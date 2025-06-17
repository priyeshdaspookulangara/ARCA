# ARCA EWM (Extended Warehouse Management) Module: Integration Strategy

This document outlines the integration strategy for the ARCA Extended Warehouse Management (EWM) module with other ARCA ERP components (MM, SD, PP, QM, FICO) and potential external systems like Material Flow Systems (MFS).

## 1. Core Integration Principles

*   **Decoupling:** EWM will integrate with other modules primarily through well-defined service interfaces (internal PHP contracts for synchronous needs) and asynchronous events (using message queues like RabbitMQ). Direct database dependencies on other modules' tables will be avoided.
*   **Explicit Contracts:** All interactions will use explicit, versioned contracts:
    *   PHP Interfaces for internal services.
    *   Data Transfer Objects (DTOs) for API request/response payloads and event payloads.
    *   OpenAPI specifications for any RESTful APIs EWM might expose.
*   **Asynchronous Preferred for Notifications & Non-Blocking Tasks:** Event-driven communication is preferred for updates, status changes, and triggering follow-up processes in other modules that don't require immediate synchronous feedback to EWM.
*   **Transactional Consistency:** For operations that require updates across EWM and another module (e.g., Goods Issue updating EWM stock and MM inventory & FICO), careful consideration will be given to ensuring transactional integrity or robust reconciliation mechanisms if eventual consistency is adopted. Often, EWM completes its part, then informs MM/FICO to complete theirs.
*   **Idempotency:** Event listeners and API endpoints in EWM and consuming modules must be designed to be idempotent.
*   **Clear Master Data Ownership:** EWM will consume core master data (Material, Business Partner/Plant from CoreMDM/MM) and extend it with warehouse-specific attributes. EWM owns its warehouse structure data (bins, activity areas, etc.).

## 2. Integration with ARCA MM (Materials Management)

EWM typically takes over detailed inventory management from MM for EWM-managed warehouses. MM might still manage inventory at a higher, aggregated plant/storage location level for non-EWM warehouses or as a supervisory view.

*   **Inbound Deliveries & Goods Receipt:**
    *   **MM -> EWM:** When a Purchase Order in MM is due for delivery, an Inbound Delivery document is typically created in MM (or directly in EWM based on PO). This Inbound Delivery is replicated/sent to EWM as an "Expected Goods Receipt" or "Inbound Delivery Notification."
    *   **EWM Process:** EWM performs detailed GR (unloading, checking, booking), putaway, and quality inspection (if needed).
    *   **EWM -> MM/FICO:** Upon completion of GR in EWM (e.g., final putaway confirmation), EWM sends a confirmation message/event (e.g., `EwmGoodsReceiptConfirmedEvent`) back to MM. MM then posts its own GR against the PO/Inbound Delivery, which triggers the inventory and financial postings in FICO (updates stock quantities/values in MM tables like MARD, MSEG and FI/CO documents).
*   **Outbound Deliveries & Goods Issue (for non-SD processes):**
    *   **MM -> EWM:** For internal needs like material issues to production orders (from PP via MM reservation), cost centers, or stock transfers between plants, MM would create a delivery document (or a stock transfer order leading to a delivery) that is sent to EWM as an Outbound Delivery Order.
    *   **EWM Process:** EWM handles picking, packing (if needed), and goods issue.
    *   **EWM -> MM/FICO:** Upon goods issue from EWM, it sends a confirmation message/event (e.g., `EwmGoodsIssuePostedEvent`) to MM. MM then posts its GI, updating its inventory and triggering FICO postings (consumption accounts, inventory reduction).
*   **Inventory Management Synchronization:**
    *   EWM manages stock at bin level. MM manages stock at Plant/Storage Location level.
    *   These need to be kept consistent. EWM goods movements (receipts, issues, internal transfers, physical inventory adjustments) trigger corresponding summarized postings in MM to align stock figures.
    *   Reconciliation reports and tools will be necessary to ensure consistency.
*   **Material Master Data:**
    *   EWM consumes `core_material_id` and basic material data from CoreMDM/MM.
    *   EWM maintains its own warehouse-specific material master extensions (e.g., `ewm_material_warehouse_data` table) for parameters like fixed bin assignments, putaway/picking strategies specific to that warehouse, handling unit types, etc.

## 3. Integration with ARCA SD (Sales and Distribution)

*   **Outbound Delivery Processing for Sales Orders:**
    *   **SD -> EWM:** When a Sales Order in SD is due for fulfillment, SD creates an Outbound Delivery Order. This document is replicated/sent to EWM.
    *   **EWM Process:** EWM takes over the physical processing: creating picking warehouse tasks, managing packing, staging, and loading.
    *   **EWM -> SD:** EWM sends status updates back to the SD Outbound Delivery Order (e.g., "Picking Started," "Picking Complete," "Packed," "Loaded," "Goods Issued").
    *   **Goods Issue Posting:** Once goods are loaded and leave the warehouse, EWM posts the Goods Issue. This confirmation is sent to SD. SD then posts its own GI, which updates sales order status, delivery status, and triggers billing and FICO COGS/Inventory postings.
*   **Availability Check (ATP) Considerations:**
    *   While SD might perform an initial ATP check against MM-level inventory, EWM's detailed bin-level stock provides more accurate information on physically available and pickable stock.
    *   EWM could provide an API for SD to query real-time pickable stock for specific orders or materials.
*   **Returns Processing (Customer Returns):**
    *   If customer returns are processed via SD (Returns Orders, Returns Deliveries), these inbound deliveries would also be replicated to EWM for physical receipt, inspection (with QM), and putaway.

## 4. Integration with ARCA PP (Production Planning)

*   **Material Staging for Production Orders:**
    *   **PP -> EWM:** When a Production Order is released in PP, it generates material requirements (components). PP can send a request (e.g., "Production Material Request" or an internal delivery) to EWM to stage these components at the Production Supply Area (PSA) near the production line.
    *   **EWM Process:** EWM creates warehouse tasks to pick components from storage bins and move them to the specified PSA.
    *   **EWM -> PP:** EWM confirms the staging completion to PP.
*   **Receipt of Finished Goods from Production:**
    *   **PP -> EWM:** When PP confirms production of finished goods for a Production Order.
    *   **EWM Process:** EWM receives these goods physically (e.g., from the end of the production line), potentially performs quality inspection (with QM), and manages their putaway into finished goods storage bins.
    *   **EWM -> PP/MM/FICO:** EWM confirms receipt. This triggers PP to post GR for the production order, which in turn updates MM inventory and FICO financial accounts.

## 5. Integration with ARCA QM (Quality Management)

*   **Goods Receipt Inspections:**
    *   When EWM receives materials flagged for QM in their master data, upon initial goods receipt posting in EWM (before putaway or after basic unloading), an event can trigger QM to create an Inspection Lot.
    *   EWM moves the physical stock to a "Quality Inspection" stock type or a designated QI area/bin.
    *   **QM -> EWM:** QM performs the inspection and records a Usage Decision (UD). This UD is communicated back to EWM (e.g., `QmUsageDecisionMadeEvent`).
    *   **EWM Action:** Based on the UD, EWM creates warehouse tasks to move the stock from QI to unrestricted use, blocked stock, scrap area, or return to vendor.
*   **Internal Warehouse Inspections:**
    *   QM can request inspections for stock already in the EWM warehouse (e.g., recurring inspections, quality audits). EWM facilitates blocking the stock and making it available for QM.

## 6. Integration with ARCA FICO (Financial Accounting & Controlling)

Direct financial postings are generally NOT made from EWM. EWM triggers logistical transactions that result in financial postings in FICO, usually via MM or SD.

*   **Inventory Valuation:** All EWM goods movements that impact valuated stock (receipts, issues, transfers, physical inventory differences) result in a message/event to MM. MM then makes the inventory posting, which automatically triggers the relevant FI/CO G/L account postings (inventory accounts, COGS, consumption accounts, variance accounts).
*   **Warehouse Operational Costs:**
    *   Costs related to warehouse operations (e.g., labor for VAS if tracked and valued, consumption of packing materials not directly tied to a delivery, scrapping costs for damages identified within EWM) might need to be posted to specific cost centers in Fina CO.
    *   EWM can provide data for these costs, and a process (manual or automated via interface) would post them in FICO.

## 7. Integration with Automation (MFS - Material Flow Systems)

*   **EWM -> MFS:** EWM sends warehouse tasks (e.g., move HU from A to B) to the MFS.
*   **MFS -> EWM:** MFS sends confirmations (task completed, error) back to EWM.
*   **Interface:** This requires a specific technical interface (e.g., APIs, IDocs if using SAP-like middleware, TCP/IP sockets with defined telegrams). The exact mechanism depends on the MFS capabilities.

This integration strategy ensures EWM acts as a specialized execution system for warehousing, tightly coupled with the planning and financial modules of ARCA.
