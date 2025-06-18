# IS-Retail/Apparel: Retail Inventory Management Extensions Scope

This document defines the scope for retail-specific Inventory Management extensions within the ARCA IS-Retail/Apparel and Footwear Solution. These functionalities build upon or enhance core ARCA MM (Materials Management) and potentially ARCA EWM (Extended Warehouse Management) capabilities to address the unique needs of retail environments.

## 1. Store Inventory Management (Granular Tracking & Operations)

Focuses on managing inventory accurately and efficiently at the individual store (site) level.

*   **1.1. Differentiated Stock within Store:**
    *   Ability to conceptually (and potentially systematically if store layout is detailed) differentiate stock within a store:
        *   **Backroom/Stockroom Stock:** Available but not on display.
        *   **Shop Floor/Selling Stock:** Available for immediate sale to customers.
        *   **Damaged/Unavailable Stock:** Stock physically in store but not sellable (awaiting write-off, return to vendor).
    *   This may involve specific storage locations within an MM plant representing the store, or more detailed EWM-like structures if a store is managed as a mini-warehouse.
*   **1.2. Store-Level Goods Movements:**
    *   **Goods Receipts:**
        *   Receiving goods from internal DCs (Stock Transport Orders from MM/SD).
        *   Receiving goods directly from external vendors (Purchase Orders from MM).
        *   Simplified receipt processes tailored for store staff.
    *   **Internal Store Transfers:** Moving stock between backroom and shop floor, or between different sections/departments within a large store.
    *   **Inventory Adjustments:**
        *   Recording shrinkage/stock loss.
        *   Adjusting for damages.
        *   Corrections after cycle counts/physical inventory.
    *   **Stock-in-Transit Visibility:** Tracking goods in transit to the store.
*   **1.3. Cycle Counting & Physical Inventory at Store:**
    *   Support for periodic physical inventory counts and more frequent cycle counts for specific articles or sections within the store.
    *   Simplified count entry interfaces suitable for store staff (potentially on mobile devices).
*   **1.4. Real-time Inventory Look-up:** Accurate, real-time (or near real-time) visibility of stock levels (by Article Variant/SKU) at the store for staff and for omnichannel processes (e.g., "find in store").

## 2. Allocation & Replenishment (Store Focused)

Ensuring stores have the right products, in the right quantities, at the right time.

*   **2.1. Allocation of New Merchandise:**
    *   **Strategies & Rules:** Define strategies for allocating initial shipments of new collections or seasonal products to stores. Allocation can be based on:
        *   Store grading/clustering (`isretail_site_clusters`).
        *   Historical sales performance of similar articles/categories at the store.
        *   Store capacity (selling space, fixture capacity).
        *   Sales potential / forecast for the new items.
        *   Predefined allocation tables or curves.
    *   **Allocation Workbench/Process:** Tools for merchandisers/allocators to review system-proposed allocations and make manual adjustments.
    *   **Creation of Outbound Deliveries/Stock Transfer Orders:** Approved allocations generate the necessary fulfillment documents from DCs to stores.
*   **2.2. Store Replenishment:**
    *   **Automated Replenishment:** System-driven calculation of store replenishment needs based on:
        *   Actual sales data (from POS integration).
        *   Sales forecasts (if available).
        *   Defined target stock levels, safety stock, or min/max parameters per article/variant at store level.
        *   Lead times from DC to store.
    *   **Replenishment Methods:** Support for different methods (e.g., Min/Max, Reorder Point, Time-Phased).
    *   **Replenishment Run/Process:** Periodic or event-driven process to calculate needs and generate replenishment orders (e.g., STOs to the supplying DC).
    *   **Manual Replenishment Requests:** Store staff ability to request replenishment for specific items if needed (subject to approval).
*   **2.3. Inter-Store Transfers (Basic):**
    *   Functionality to manage stock transfers between stores to balance inventory (e.g., move slow-moving items from one store to another where demand is higher). Requires approval and clear tracking.

## 3. Markdown & Promotion Management (Inventory Execution Aspects)

Managing the inventory implications of pricing strategies defined in Merchandise Planning.

*   **3.1. Identifying Eligible Stock:**
    *   Identify inventory (Article Variants at specific sites) that is subject to planned markdowns or promotions based on criteria (e.g., age of stock, season, slow sellers).
*   **3.2. Price Activation:** Ensuring that when a markdown or promotion becomes active, the POS systems and e-commerce channels reflect the correct pricing for the eligible stock. (Integration with Pricing module/SD).
*   **3.3. Tracking Sales at Promotional/Markdown Prices:**
    *   Sales transactions need to clearly indicate if an item was sold at a markdown or promotional price for accurate sales analysis and margin calculation. (Integration with POS data and SD).
*   **3.4. Inventory Valuation during Markdowns (FICO Integration):** Consideration for how significant, permanent markdowns might impact inventory valuation in FICO.

## 4. Returns Management (Retail-Specific Customer & Vendor Returns)

Streamlined processes for handling returns in a retail environment.

*   **4.1. Customer Returns (Omnichannel):**
    *   **In-Store Returns:** Process for accepting customer returns at the POS, with or without original receipt.
        *   Inspection and grading of returned items (e.g., sellable, damaged, refurbish).
        *   Disposition logic (e.g., return to shop floor stock, move to damaged goods area, send for refurbishment).
        *   Triggering refund or exchange processes (POS/FICO integration).
    *   **Online Returns (to Store or DC):** Managing receipt and processing of returns from e-commerce sales, which might be returned to a physical store or a central returns processing center/DC.
*   **4.2. Vendor Returns (Return to Vendor - RTV):**
    *   Processes for returning goods to vendors due to:
        *   Defects or quality issues found upon receipt or later.
        *   Overstock of seasonal goods (if vendor agreements allow).
        *   End-of-lifecycle items.
    *   Creation of RTV orders, managing shipments back to vendor, and tracking credits from vendors (MM and FICO integration).
*   **4.3. Refurbishment & Repair (Basic):**
    *   If returned items can be refurbished, basic tracking of items sent for refurbishment and returned to sellable stock. (More advanced repair might involve ARCA PM or a service module).

This scope for Retail Inventory Management Extensions focuses on the unique operational needs of stores and the dynamic nature of retail product flow, building upon the foundational inventory capabilities of ARCA MM/EWM.
EOL

echo "docs/is_retail/IsRetailInventoryScope.md created successfully."
