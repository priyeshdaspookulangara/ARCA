# IS-Retail/Apparel: Merchandise Planning & Buying Integration Strategy

This document outlines the integration strategy for the Merchandise Planning and Buying functionalities (Merchandise Financial Planning, Assortment Planning, Open-to-Buy, Retail Vendor Management extensions, Retail Purchase Order Management) of the ARCA IS-Retail/Apparel and Footwear Solution with other ARCA ERP components.

## 1. Core Integration Principles

*   **Planning Feeds Execution:** Merchandise plans (financial, assortment, OTB) created in IS-Retail will drive and inform execution processes in procurement (MM) and sales (SD).
*   **Actuals Feedback Loop:** Actual performance data (sales, inventory, costs) from execution modules (FICO, MM, SD) will be fed back into IS-Retail planning components for monitoring, reporting, and re-planning.
*   **Service-Oriented & Event-Driven:** Interactions will use a combination of internal service APIs (for synchronous data needs or triggering actions) and asynchronous events (for notifications and decoupled updates).
*   **MDG as Master Data Source:** All planning and buying processes rely on consistent Article Master, Vendor Master, Site Master, and Merchandise Hierarchy data sourced from or governed by ARCA MDG and IS-Retail Master Data extensions.

## 2. Integration with ARCA FICO (Financial Accounting & Controlling)

*   **Merchandise Financial Planning (MFP) & FICO Budgeting:**
    *   **MFP -> FICO:** Approved high-level MFP targets (e.g., sales revenue, COGS, margin, inventory investment) can serve as input or be compared against financial budgets set in ARCA FICO. The exact flow (MFP feeding FICO budget, or MFP being a parallel plan reconciled against FICO budget) needs to be defined based on organizational process.
    *   **FICO -> MFP:** Actual financial data (actual sales, actual COGS, actual inventory values) from FICO general ledger and profitability analysis (CO-PA) will be regularly fed into MFP for plan vs. actual reporting and in-season forecasting/re-planning. This is typically via batch data loads or summarized data APIs.
*   **Open-to-Buy (OTB) & Financial Control:**
    *   **MM/FICO -> OTB:** OTB calculations in IS-Retail require data on committed purchases (open PO values). This data originates from ARCA MM POs, and their financial commitment value is reflected in FICO. OTB will query this commitment data via MM/FICO APIs.
    *   **Landed Costs (FICO/TM -> IS-Retail Buying):** For accurate margin planning and OTB consumption at cost, estimated and actual landed costs (freight, duties, insurance) associated with POs, managed in ARCA TM and FICO, need to be accessible to the IS-Retail buying functions.
*   **Markdown & Promotion Accounting:**
    *   When markdowns or promotions are executed (see Retail Inventory Mgt. scope), the financial impact (e.g., markdown expense, reduction in inventory value, promotional costs) needs to be posted in ARCA FICO. IS-Retail will trigger these financial postings via integration with FICO (e.g., by sending `IsRetailMarkdownActivatedEvent` with financial details).

## 3. Integration with ARCA SRM/Ariba (Strategic Sourcing)

*   **Assortment Plan -> Sourcing Request:**
    *   High-level assortment plans or identified product needs (especially for private label or exclusive large buys) from IS-Retail Assortment Planning can trigger strategic sourcing requests in ARCA SRM or an integrated Ariba solution.
    *   **IS-Retail -> SRM/Ariba:** Publish an event like `IsRetailSourcingNeedIdentifiedEvent` or call an SRM API to initiate an RFx process, providing product specifications (from PLM via Article Master) and estimated volumes.
*   **Contract Information -> Retail POs:**
    *   **SRM/Ariba -> IS-Retail/MM:** Negotiated supplier contracts, preferred vendor lists, and pricing agreements resulting from strategic sourcing in SRM/Ariba will be referenced or replicated to ARCA MM/IS-Retail.
    *   When creating Retail Purchase Orders, buyers in IS-Retail/MM can then leverage these centrally negotiated terms.

## 4. Integration with ARCA MM (Materials Management - for PO Execution)

*   **Retail Purchase Order (IS-Retail -> MM):**
    *   While IS-Retail might have its own UI and specific logic for creating "Retail Purchase Orders" (capturing seasonal aspects, pre-packs, OTB links), the operational execution of these POs (sending to vendor, goods receipt, invoice verification) is typically handled by ARCA MM.
    *   **Flow:**
        1.  Retail PO created and approved in IS-Retail (passing OTB checks).
        2.  IS-Retail publishes `IsRetailPurchaseOrderApprovedEvent` or calls an MM service API to create/replicate the corresponding Purchase Order in ARCA MM.
        3.  The ARCA MM PO will contain all necessary details (vendor, article variants/SKUs, quantities, prices, delivery dates, plant/store as delivery location).
*   **PO Status Updates (MM -> IS-Retail):**
    *   Updates to the PO status in MM (e.g., Vendor Confirmation, Advance Ship Notification received, Goods Receipted, Invoice Verified) should be communicated back to IS-Retail planning and buying modules.
    *   **Mechanism:** MM publishes events like `MmPurchaseOrderConfirmedEvent`, `MmGoodsReceiptPostedForPOEvent`, `MmInvoiceVerifiedForPOEvent`. IS-Retail subscribes to these to update its view of PO lifecycle, actual receipts, and OTB consumption.
*   **Inventory Data for Planning (MM -> IS-Retail):**
    *   MFP and Assortment Planning require current inventory data (on-hand, in-transit) to accurately plan future needs.
    *   IS-Retail will query ARCA MM (or EWM if applicable) via API for this stock information, aggregated at appropriate levels (e.g., by article variant, by store/DC).

## 5. Integration with ARCA MDG & IS-Retail Master Data

*   Merchandise Financial Planning, Assortment Planning, OTB, and Retail PO Management all heavily rely on:
    *   `isretail_article_variants` (and their link to `mdg_materials_core`).
    *   `isretail_generic_articles`.
    *   `isretail_seasons`, `isretail_collections`.
    *   `isretail_merch_category_nodes`.
    *   `isretail_site_extensions` (and their link to `lscm_plants`).
    *   Vendor master data (from `mdg_business_partners_core` and IS-Retail vendor extensions).
*   These planning modules will consume this master data via internal service APIs provided by the IS-Retail Master Data sub-domain and ARCA MDG.

## 6. Integration with Demand Planning/Forecasting Tools (if a separate ARCA module or external tool)

*   **Forecast Input -> MFP & Assortment Planning:**
    *   If a dedicated demand planning/forecasting tool exists, its outputs (e.g., sales forecasts by SKU/store/week) will be a critical input for both Merchandise Financial Planning (for sales targets) and Assortment Planning (for determining buy quantities).
    *   **Mechanism:** The forecasting tool would provide an API for IS-Retail to query forecasts, or it would publish forecast data via events/batch files that IS-Retail consumes.

## 7. Event-Driven Communication (Summary for Planning & Buying)

*   **Events Published by IS-Retail Planning & Buying:**
    *   `IsRetailFinancialPlanUpdatedEvent({planId, version, status})`
    *   `IsRetailAssortmentPlanFinalizedEvent({assortmentPlanId, season, targetClusters})`
    *   `IsRetailOtbBudgetCalculatedEvent({merchCategory, period, otbAmount})`
    *   `IsRetailPurchaseOrderCreatedEvent({isRetailPoId, mmPoId_if_replicated})` (after internal approval & OTB check)
*   **Events Subscribed to by IS-Retail Planning & Buying:**
    *   `FicoActualSalesDataAvailableEvent({period, category, salesValue, salesUnits})`
    *   `FicoInventoryValuationUpdatedEvent({period, category, inventoryValue})`
    *   `MmPurchaseOrderConfirmedByVendorEvent({poId, confirmedDeliveryDate})`
    *   `MmGoodsReceiptPostedForRetailPOEvent({poId, articleVariantId, receivedQuantity, receiptDate})`
    *   `DemandPlanningForecastUpdatedEvent({articleVariantId, locationId, period, forecastQuantity})`
    *   `SrmContractFinalizedEvent({vendorId, materialGroupId, contractTerms})`

This integration strategy ensures that IS-Retail's planning and buying functions are data-driven, financially controlled, and effectively drive downstream execution processes.
