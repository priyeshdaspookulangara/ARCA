# "LSCM" Module: Scope and Core Functionalities

This document defines the scope and core functionalities for the Logistics & Supply Chain Management (LSCM) module. LSCM encompasses capabilities typically found in SAP's Materials Management (MM), Sales and Distribution (SD), Production Planning (PP), Plant Maintenance (PM), and Quality Management (QM) modules.

## I. Materials Management (MM)

### 1.1. Procurement
*   **Purchase Requisitions:**
    *   Creation and approval workflows for internal purchase requests.
    *   Conversion of requisitions to Purchase Orders.
*   **Purchase Orders (POs):**
    *   Creation and management of POs for materials and services.
    *   Referencing contracts or quotations.
    *   Output determination (e.g., printing, emailing POs to vendors).
    *   PO tracking and history.
*   **Vendor Selection & Source Determination (Basic):**
    *   Maintaining vendor master data links (integration with core Vendor Master).
    *   Source lists or purchasing info records (basic for preferred vendors).
*   **Contract Management (Basic):**
    *   Management of outline agreements/longer-term contracts with vendors (quantity contracts, value contracts).
    *   POs can be created with reference to these contracts.

### 1.2. Inventory Management
*   **Stock Tracking & Overview:**
    *   Real-time tracking of material stock levels across different storage locations and plants.
    *   Display of various stock types (e.g., unrestricted, quality inspection, blocked, in-transit).
*   **Goods Movements:**
    *   **Goods Receipts (GR):**
        *   Posting GRs against POs, production orders, or without reference.
        *   Stock updates and potential quality inspection lot creation.
    *   **Goods Issues (GI):**
        *   Posting GIs for sales deliveries, production orders, cost centers, sampling, scrapping.
    *   **Stock Transfers & Transfer Postings:**
        *   Between storage locations within a plant.
        *   Between plants (one-step or two-step procedures).
        *   Changing stock type (e.g., from quality inspection to unrestricted).
*   **Physical Inventory:**
    *   Planning and execution of physical inventory counts.
    *   Posting inventory differences.
    *   Support for various counting methods (e.g., periodic, cycle counting - basic).
*   **Reservation Management:** Reserving materials for future use (e.g., for production orders, cost centers).

### 1.3. Warehouse Management (Basic)
*   **Storage Location Management:** Defining and managing multiple storage locations within a plant.
*   **Putaway (Basic):** Simple strategies for suggesting putaway locations upon goods receipt.
*   **Picking (Basic):** Simple strategies for suggesting picking locations for goods issues (e.g., for deliveries or production).
*   **Packing (Basic):** Basic handling unit management, packing items into containers for shipment.
*   **Shipping (Basic):** Preparation for goods issue, linking with delivery documents from SD.

### 1.4. Valuation
*   **Material Valuation:**
    *   Valuation of material stocks at the plant or company code level.
    *   Support for standard price or moving average price valuation methods.
    *   Automatic account determination for inventory postings (integration with Fina GL).
*   **Split Valuation (Consideration):** Valuing different batches or types of the same material differently.

### 1.5. Invoice Verification (Logistics Invoice Verification - LIV)
*   **Verifying Vendor Invoices:**
    *   Matching vendor invoices with POs and Goods Receipts (three-way match concept).
    *   Handling price and quantity variances.
    *   Posting invoices (creates AP documents in Fina).
    *   Managing blocked invoices due to discrepancies.
    *   Handling credit memos from vendors.

## II. Sales and Distribution (SD)

### 2.1. Sales Order Management
*   **Inquiries & Quotations:**
    *   Creating and managing customer inquiries (requests for information).
    *   Creating and managing sales quotations (binding offers to customers).
    *   Conversion of quotations to sales orders.
*   **Sales Orders:**
    *   Creating and managing sales orders with customer, material, quantity, pricing, and delivery information.
    *   Availability checks (ATP - Available-to-Promise, basic).
    *   Order confirmation and communication to customers.
*   **Contracts (Basic):**
    *   Management of sales outline agreements (quantity contracts, value contracts).
    *   Sales orders can be created with reference to these contracts.

### 2.2. Pricing (Basic)
*   **Pricing Conditions:**
    *   Defining basic pricing elements (e.g., price lists, customer-specific prices, material discounts).
    *   Condition technique (basic: condition types, access sequences, condition tables) for automatic price determination in sales documents.
*   **Discounts & Surcharges:** Applying simple discounts or surcharges.

### 2.3. Delivery Processing
*   **Outbound Deliveries:**
    *   Creating outbound delivery documents from sales orders.
    *   Managing delivery due lists.
*   **Picking (Integration with MM-WM Basic):** Triggering picking activities for deliveries.
*   **Packing (Integration with MM-WM Basic):** Packing items for shipment, handling unit creation.
*   **Goods Issue Posting:** Posting goods issue for deliveries, which reduces inventory (MM) and posts Cost of Goods Sold (Fina COGS).

### 2.4. Billing
*   **Customer Invoices:**
    *   Creating billing documents (invoices) with reference to sales orders or deliveries.
    *   Generating invoice outputs (print, email).
    *   Automatic posting of billing documents to Fina AR (customer accounts) and Fina GL (revenue accounts).
*   **Credit Memos & Debit Memos:** Processing credit/debit memos for customers.

### 2.5. Credit Management (Basic)
*   **Customer Credit Monitoring:**
    *   Basic credit limit checks during sales order creation (integration with Fina AR credit data).
    *   Blocking sales orders if credit limit is exceeded.

## III. Production Planning (PP)

### 3.1. Demand Management
*   **Basic Forecasting:** Simple statistical forecasting methods based on historical consumption or sales data.
*   **Demand Planning:** Inputting planned independent requirements (PIRs) for make-to-stock scenarios.

### 3.2. Material Requirements Planning (MRP)
*   **MRP Runs:** Calculating net material requirements based on demand (PIRs, sales orders), current stock, and existing supply elements (POs, production orders).
*   **Procurement Proposals:** Generating planned orders (for in-house production) or purchase requisitions (for external procurement).
*   **Lot Sizing Procedures (Basic):** Static lot sizes, lot-for-lot.

### 3.3. Capacity Planning (Basic)
*   **Work Center Capacity:** Defining available capacity for work centers.
*   **Production Scheduling (Basic):** Simple scheduling of planned orders based on routing times.
*   **Capacity Leveling (Basic):** Identifying overloads and basic tools for adjusting schedules.

### 3.4. Shop Floor Control
*   **Production Orders:**
    *   Converting planned orders into production orders.
    *   Managing production order lifecycle (created, released, confirmed, delivered).
*   **Material Staging:** Triggering material withdrawal for production orders (integration with MM).
*   **Confirmations:**
    *   Recording actual production quantities (yield, scrap).
    *   Confirming actual activity times (labor, machine hours).
    *   Backflushing of components (optional).
*   **Goods Receipt from Production:** Posting goods receipt of finished products from production orders into inventory (MM).

### 3.5. PP Master Data
*   **Bills of Material (BOMs):**
    *   Creating and managing single-level and multi-level BOMs for finished products and assemblies.
    *   Item categories (stock item, non-stock item).
*   **Routings:**
    *   Defining production steps (operations), sequence, work centers, and standard times (setup, machine, labor).
*   **Work Centers:**
    *   Defining production resources (machines, labor groups, production lines).
    *   Assigning cost centers (for Fina CO integration) and standard value keys for activity costing.

## IV. Plant Maintenance (PM)

### 4.1. Equipment Management
*   **Technical Objects:**
    *   Managing equipment master records (machines, devices with maintenance history).
    *   Managing functional locations (hierarchical representation of where equipment is installed).
    *   Building equipment hierarchies and structures.
*   **Bills of Material (Equipment BOMs):** Managing spare parts lists for equipment.

### 4.2. Maintenance Order Processing
*   **Notifications (Simplified from QM):** Initial recording of issues or requirements for maintenance.
*   **Corrective Maintenance Orders:**
    *   Creating maintenance orders for unplanned repairs.
    *   Planning labor, materials (spare parts), and external services.
*   **Preventive Maintenance Orders (Basic):**
    *   Creating orders based on simple maintenance plans (e.g., time-based, counter-based - basic).
*   **Order Execution:** Releasing orders, withdrawing spare parts, recording time.
*   **Order Completion & Settlement:** Confirming work, technical completion, and settling costs to Fina CO.

### 4.3. Maintenance Planning (Basic)
*   **Task Lists:** Defining standard sequences of maintenance tasks.
*   **Basic Scheduling:** Scheduling maintenance orders based on priority and resource availability (simplified).
*   **Resource Allocation (Basic):** Assigning technicians or work centers to maintenance tasks.

## V. Quality Management (QM)

### 5.1. Quality Planning (Basic)
*   **Inspection Plans:**
    *   Defining basic inspection plans for materials (e.g., for goods receipt, in-process inspections, final product inspection).
    *   Specifying characteristics to be inspected and basic methods.
*   **Master Data:** Basic quality-related settings in material master.

### 5.2. Quality Inspection
*   **Inspection Lots:**
    *   Automatic or manual creation of inspection lots (e.g., upon goods receipt from vendor or production).
*   **Results Recording:** Recording inspection results for characteristics (quantitative or qualitative).
*   **Usage Decision:** Making a decision on the inspection lot (e.g., accept, reject, scrap).
*   **Stock Postings:** Triggering inventory postings based on usage decision (e.g., moving stock from quality inspection to unrestricted or blocked).

### 5.3. Quality Notifications (Basic)
*   **Defect Handling:**
    *   Creating basic notifications to record internal problems, vendor defects, or customer complaints related to quality.
    *   Tracking status and basic corrective actions.

This comprehensive scope for LSCM will guide the detailed design of its sub-components and their interactions.
