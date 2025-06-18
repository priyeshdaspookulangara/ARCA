# IS-Retail/Apparel: Retail Inventory Management Extensions Integration Strategy

This document outlines the integration strategy for the retail-specific Inventory Management extensions (Store Inventory, Allocation & Replenishment, Markdown/Promotion inventory aspects, Returns Management) of the ARCA IS-Retail/Apparel and Footwear Solution with other ARCA ERP components.

## 1. Core Integration Principles

*   **Leverage Core MM/EWM:** IS-Retail Inventory extensions primarily provide retail-centric processes and user interfaces that orchestrate or supplement the core inventory management functionalities of ARCA MM (Materials Management) and ARCA EWM (Extended Warehouse Management), if EWM is used for Distribution Centers (DCs). MM remains the system of record for inventory quantities and valuation at a plant/storage location level. EWM is the system of record for bin-level stock within EWM-managed warehouses (DCs).
*   **Real-Time or Near Real-Time Accuracy:** Accurate store inventory is critical for replenishment, omnichannel scenarios, and customer satisfaction. Integrations should aim for timely updates.
*   **Event-Driven & API-Based:** A combination of asynchronous events and synchronous internal service APIs will be used for communication.
*   **Clear Transactional Boundaries:** Ensure that inventory-affecting transactions are consistently posted across relevant modules (e.g., a POS sale reduces MM stock and posts to FICO).

## 2. Integration with ARCA MM (Materials Management)

MM is the foundational layer for inventory quantities and valuation. IS-Retail store operations trigger MM transactions.

*   **Store Representation in MM:**
    *   Each physical retail store (IS-Retail Site) will typically be configured as a **Plant** in ARCA MM, or at minimum as one or more **Storage Locations** within a plant that represents a larger legal or regional entity. The "Plant per Store" model offers better granularity for planning and valuation if required.
    *   Differentiated stock within a store (shop floor, backroom) can be represented by different storage locations within the store's plant.
*   **Store-Level Goods Movements (IS-Retail -> MM):**
    *   **Goods Receipts at Store:**
        *   From DC (Stock Transport Order - STO): IS-Retail store receiving UI will trigger an MM goods receipt transaction (e.g., MIGO equivalent) against the STO, updating MM inventory for the store plant/sloc.
        *   Direct from Vendor (Purchase Order - PO): IS-Retail store receiving UI triggers an MM goods receipt against the PO.
    *   **Inventory Adjustments:**
        *   Shrinkage, damages, or other discrepancies found in store and recorded in IS-Retail UI will trigger the appropriate MM inventory adjustment movement types (e.g., write-off to scrap, physical inventory difference posting).
    *   **Internal Store Transfers (e.g., Backroom to Shop Floor):** If these need to be systematically tracked with value, they trigger MM storage location to storage location transfer postings.
*   **Physical Inventory & Cycle Counting at Store:**
    *   IS-Retail UI for count entry will feed data into ARCA MM's physical inventory process.
    *   MM creates Physical Inventory Documents. Counts are entered against these. MM posts differences.
*   **Stock-in-Transit Visibility:** MM's STO process already tracks stock-in-transit between plants (DC to store). IS-Retail UIs will surface this information.

## 3. Integration with ARCA EWM (Extended Warehouse Management - for DCs)

If Distribution Centers supplying stores are managed by ARCA EWM:

*   **Allocation & Replenishment Orders (IS-Retail -> EWM via MM/SD):**
    *   Approved Allocation plans from IS-Retail generate Stock Transfer Orders (MM STOs) from the DC (EWM-managed plant) to the store (MM plant).
    *   Store Replenishment calculations in IS-Retail also generate STOs.
    *   These STOs are then reflected in EWM as outbound delivery order requests (if STO involves an SD delivery) or internal EWM tasks to pick, pack, and ship goods from the DC to the store.
*   **Returns to DC:**
    *   Customer returns processed at a store (or direct e-commerce returns) that need to be sent back to a central DC will trigger an MM STO (store to DC) or a returns delivery.
    *   This becomes an inbound delivery notification for the EWM-managed DC to process (receipt, inspection, putaway).

## 4. Integration with ARCA SD (Sales and Distribution)

*   **POS Sales Data & Inventory Decrement:**
    *   Sales transactions from POS systems (see Store Operations integration) must decrement store inventory in near real-time.
    *   **Flow:** POS -> (Integration Layer) -> ARCA SD (creates sales order & outbound delivery for the store sale, posts Goods Issue) -> ARCA MM (inventory updated) -> ARCA FICO (COGS posting).
    *   IS-Retail replenishment processes heavily depend on this accurate sales data and resulting inventory updates.
*   **Customer Returns (via SD):**
    *   If customer returns are initiated through ARCA SD (e.g., a Sales Return Order), the subsequent goods receipt process for the return (at store or DC) needs to update MM/EWM inventory correctly. IS-Retail disposition logic (return to stock, scrap) will determine the correct MM movement type.
*   **Omnichannel ATP (Available-to-Promise):**
    *   IS-Retail store inventory views (reading from MM, updated by POS and store operations) are crucial for SD's ATP checks in omnichannel scenarios:
        *   "Find in Store": SD needs to query real-time stock at specific stores.
        *   "Ship from Store": If a store fulfills an online order, SD needs to confirm stock and trigger a store-level "sale/delivery" process.
*   **Promotions & Markdowns:** Pricing for promotions/markdowns executed at POS (driven by SD pricing engine) must be based on plans from IS-Retail Markdown & Promotion Management. Sales at these prices are recorded by SD/POS.

## 5. Integration with ARCA FICO (Financial Accounting & Controlling)

All physical inventory movements that have a financial impact must be reflected in FICO. This is typically achieved via the tight integration between MM and FICO (and SD-FICO for COGS).

*   **Inventory Valuation:** All store-level goods movements (receipts, issues, adjustments, physical inventory differences) triggered by IS-Retail UIs but executed as MM transactions will result in corresponding FI/CO postings based on MM's account determination (e.g., updating inventory G/L accounts, posting to shrinkage/damage expense accounts, COGS).
*   **Markdown Financial Impact:**
    *   While IS-Retail plans markdowns, the actual financial recognition of reduced sales revenue or potential inventory revaluation due to permanent markdowns is handled in FICO, often triggered by SD billing documents reflecting lower prices or specific MM/FI transactions for inventory write-downs.
*   **Cost of Goods Sold (COGS):** Posted by FICO upon goods issue for sales transactions processed through SD.
*   **Physical Inventory Differences:** Financial impact of PI differences posted in FICO via MM.

## 6. Integration with IS-Retail Merchandise Planning & Buying

This is an internal IS-Retail integration.

*   **Allocation & Replenishment Inputs:**
    *   Allocation plans use Assortment Plans (which articles, target store clusters) as a primary input.
    *   Replenishment algorithms use actual sales data (from POS/SD via MM stock updates) and can also consider targets from Merchandise Financial Plans (e.g., desired stock turn, target stock levels).
*   **Inventory Feedback Loop:**
    *   Current actual inventory levels (from stores via MM) and stock-in-transit are crucial inputs for OTB calculations and for MFP re-forecasting and in-season adjustments.
    *   Sales data (units and value) resulting from inventory being sold feeds back into MFP performance tracking.

## 7. Event-Driven Communication (Examples)

*   **Events Published by IS-Retail Inventory functions (or related modules):**
    *   `IsRetailStoreStockAdjustmentPostedEvent({site_id, article_variant_id, quantity, reason_code})`
    *   `IsRetailAllocationPlanApprovedForDcProcessingEvent({allocation_id, target_stores_data})`
    *   `IsRetailReplenishmentOrderGeneratedForStoreEvent({sto_id, store_id, items})`
    *   `IsRetailMarkdownActivatedEvent({markdown_plan_id, articles_affected, new_prices})` (for pricing engine & FICO)
    *   `IsRetailCustomerReturnReceivedAtStoreEvent({return_id, article_variant_id, disposition_code})`
*   **Events Subscribed to by IS-Retail Inventory functions:**
    *   `PosSalesDataAggregatedForStoreEvent({store_id, period, sales_by_sku})` (from POS/SD integration layer)
    *   `MmGoodsReceiptPostedAtStoreEvent({sto_id_or_po_id, store_id, items_received})`
    *   `MmInventoryDifferencePostedForStoreEvent({store_id, items_adjusted})`
    *   `SdCustomerReturnAuthorizedForStoreReceiptEvent({return_order_id, store_id, items_expected})`

This integration strategy ensures that retail-specific inventory processes are tightly linked with ARCA's core logistical and financial systems, providing accurate data for planning and operations.
