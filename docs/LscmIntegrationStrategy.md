# "LSCM" Module: Integration Strategy

This document outlines the integration strategy for the Logistics & Supply Chain Management (LSCM) module with other ERP modules (Fina, HR, CRM) and potential external systems. The strategy emphasizes modularity, loose coupling, and efficient data exchange.

## 1. Core Integration Principles

*   **Decoupling:** LSCM will be designed to minimize direct dependencies on other optional modules. Communication will primarily occur through well-defined contracts.
*   **Explicit Contracts:**
    *   **Internal PHP Interfaces:** For synchronous, intra-application communication, LSCM will define and expose PHP interfaces (Contracts) for its services. Other modules will depend on these interfaces, resolved via Laravel's service container.
    *   **API Endpoints:** LSCM will expose versioned RESTful API endpoints for specific functionalities that might be consumed by external systems or for certain decoupled internal interactions. These will be documented using OpenAPI.
    *   **Data Transfer Objects (DTOs):** Standardized, versioned DTOs will be used for all API request/response payloads and event payloads.
*   **Asynchronous Communication (Message Queues - RabbitMQ):**
    *   This is the **preferred method** for many cross-module processes, especially for high-volume transactions originating from LSCM or for notifications that don't require immediate synchronous feedback.
    *   LSCM will publish events for significant business occurrences (e.g., `LscmGoodsMovementPostedEvent`, `LscmSalesOrderConfirmedEvent`, `LscmProductionOrderCompletedEvent`).
    *   LSCM will also subscribe to relevant events from other modules (e.g., `FinaPaymentReceivedEvent` affecting SD credit release, `CrmSalesQuoteAcceptedEvent` triggering SD order creation).
*   **Idempotency:** Event listeners and API endpoints in consuming modules (and within LSCM for incoming events) must be designed to be idempotent.
*   **Transactional Integrity:** For critical operations spanning LSCM and other modules (especially Fina), strategies for ensuring eventual consistency or compensating transactions (if full distributed transactions are too complex) will be considered. Atomic operations within each module boundary are the first priority.

## 2. Integration with "Fina" (FI/CO) Module

LSCM has extensive and critical integration points with Fina.

*   **Materials Management (MM) -> Fina:**
    *   **Goods Movements & Inventory Valuation:**
        *   **Event:** LSCM MM publishes `LscmGoodsMovementPostedEvent` (detailing material, quantity, movement type, plant, storage location, valuation data).
        *   **Fina Listener:** Fina GL subscribes to this event.
        *   **Action in Fina:** Posts to relevant inventory accounts, consumption accounts (e.g., COGS for sales, material consumption for production/cost centers), and GR/IR clearing accounts. Updates material valuation in Fina if Fina holds a parallel valuation ledger.
    *   **Logistics Invoice Verification (LIV):**
        *   **Event/API:** When a vendor invoice is verified in LSCM MM against a PO and GR:
            *   LSCM MM publishes `LscmVendorInvoiceVerifiedEvent` with invoice details.
            *   OR, LSCM MM makes an internal API call to Fina AP to create a draft vendor invoice.
        *   **Action in Fina AP:** Creates an accounts payable open item for the vendor, posts to GR/IR clearing, and tax accounts.
*   **Sales and Distribution (SD) -> Fina:**
    *   **Customer Billing:**
        *   **Event:** LSCM SD publishes `LscmCustomerBillingDocumentCreatedEvent` (with customer, amounts, tax, products/services billed).
        *   **Fina Listener:** Fina AR subscribes to this event.
        *   **Action in Fina AR/GL:** Creates an accounts receivable open item for the customer, posts revenue to GL accounts, and to CO-PA (Profitability Analysis).
    *   **Credit Management:**
        *   **API Call:** During sales order creation/modification in LSCM SD, it makes a synchronous internal API call to Fina AR (e.g., `checkCustomerCredit(customerId, orderValue, currency)`).
        *   **Response:** Fina AR returns credit status (Approved, Blocked, Check Manually). LSCM SD then acts accordingly (e.g., blocks order if credit denied).
*   **Production Planning (PP) -> Fina CO:**
    *   **Production Costs & Confirmations:**
        *   **Event:** LSCM PP publishes `LscmProductionOrderActivityConfirmedEvent` (with order ID, work center, activity type, confirmed quantity/time, material components consumed if backflushed).
        *   **Fina Listener:** Fina CO subscribes.
        *   **Action in Fina CO:** Posts actual costs (labor, machine, overhead based on activity rates; material consumption costs) to the production order (as a cost object) and relevant cost centers/activity types.
    *   **Work In Progress (WIP) Calculation & Posting:**
        *   LSCM PP provides data (e.g., order status, confirmed quantities).
        *   A Fina CO process (or a joint process) calculates WIP at period end.
        *   Fina CO posts WIP to relevant GL accounts.
    *   **Production Order Settlement:**
        *   When a production order is fully delivered/closed in LSCM PP, it triggers a settlement process in Fina CO to move collected costs from the production order to inventory (finished goods) or variance accounts.
*   **Plant Maintenance (PM) -> Fina CO:**
    *   **Maintenance Costs:**
        *   **Event:** LSCM PM publishes `LscmMaintenanceOrderSettledEvent` or `LscmMaintenanceActivityConfirmedEvent` (with order ID, equipment, cost center, materials consumed, labor hours, external service costs).
        *   **Fina Listener:** Fina CO subscribes.
        *   **Action in Fina CO:** Posts actual maintenance costs to the maintenance order (as a cost object) and responsible cost centers or equipment (as statistical postings).

## 3. Integration with "HR" Module

*   **Resource & Capacity Planning (PP/PM & HR):**
    *   LSCM PP (Work Centers) and LSCM PM (Maintenance Work Centers/Technicians) may need to reference employee skills, availability, or roles defined in the HR module.
    *   **API Call:** LSCM makes API calls to HR to fetch employee/role data for planning or assignment suggestions. HR remains the master for personnel data.
*   **Time Confirmations (PP/PM -> HR - Optional):**
    *   If actual labor times are captured in detail in LSCM PP (shop floor terminals) or LSCM PM:
        *   **Event/API:** LSCM could publish `LscmLaborTimeConfirmedEvent` or call an HR API.
        *   **Action in HR:** This data could be used by HR for payroll input (if employees are paid based on confirmed production/maintenance hours) or for project time tracking if integrated with an HR time module. Often, time is entered directly in an HR system and allocated to CO objects, so this flow depends on the primary time recording system.

## 4. Integration with "CRM" Module

*   **Sales Process (CRM -> LSCM SD):**
    *   When a sales opportunity is won or a quote is accepted in CRM:
        *   **Event:** CRM publishes `CrmSalesQuoteAcceptedEvent` or `CrmOpportunityWonEvent` including customer data, product/service line items, quantities, agreed prices, delivery terms.
        *   **LSCM SD Listener:** Subscribes to this event.
        *   **Action in LSCM SD:** Creates a sales order, performs ATP checks, initiates delivery processing.
*   **Order Fulfillment Updates (LSCM SD -> CRM):**
    *   LSCM SD will publish events that CRM can consume to update its records and potentially notify customers:
        *   `LscmSalesOrderStatusChangedEvent` (e.g., Confirmed, In Delivery, Partially Shipped, Fully Shipped, Invoiced).
        *   `LscmDeliveryCreatedEvent` (with tracking number if available).
        *   `LscmBillingDocumentPostedEvent` (for CRM to know the order is invoiced).
*   **Customer & Product Data Synchronization (Consideration):**
    *   A robust master data strategy (see LscmDataModel.md) is key. CRM and LSCM SD will consume core customer and product master data. Updates to core master data should trigger events that both CRM and LSCM consume to keep their local extensions consistent if needed.

## 5. LSCM's API Design (Internal & External)

*   **Internal Service APIs (PHP Interfaces):**
    *   Primary method for intra-ERP communication where synchronous interaction is needed (e.g., Fina calling an LSCM API to get current stock level for a material during a financial audit report).
*   **External RESTful APIs:**
    *   LSCM may expose specific APIs for external partners:
        *   **Suppliers/Vendors:** API for PO acknowledgement, Advanced Shipping Notifications (ASNs), potentially invoice submission.
        *   **Logistics Providers (3PLs):** API for shipment status updates, proof of delivery.
        *   **Customers (B2B):** API for order status tracking, placing orders (if direct B2B portal).
    *   These APIs will be versioned, secured (OAuth2/API Keys), and documented via OpenAPI.

## 6. Message Queues for High-Volume & Decoupled Processes

*   Message queues (RabbitMQ) are vital for LSCM due to high transaction volumes and the need for decoupling.
*   **Key Use Cases:**
    *   **Inventory Updates (MM):** Every goods movement in MM can publish an event. This allows various subscribers (SD for ATP, PP for MRP, Fina for GL, QM for inspection lot creation) to react without MM directly calling each one.
    *   **Order Status Propagation (SD, PP):** Changes in sales order or production order status are published as events.
    *   **MRP Run Results (PP):** Completion of an MRP run can trigger events for procurement (new requisitions) or production (new planned orders).
    *   **Invoice Generation Triggers (MM to Fina, SD to Fina):** As described above.

This integration strategy aims to make LSCM a powerful, interconnected module that enhances the overall ERP's capabilities without creating tight dependencies that would hinder modularity or independent evolution.
