# IS-Retail/Apparel: Merchandise Planning & Buying Scope

This document defines the scope for the Merchandise Planning and Buying functionalities within the ARCA IS-Retail/Apparel and Footwear Solution. These capabilities are crucial for optimizing inventory investments, margins, and aligning product offerings with customer demand.

## 1. Merchandise Financial Planning (MFP)

MFP involves setting financial targets for sales, inventory, markdowns, and margins, typically before and during a selling season.

*   **1.1. Planning Dimensions & Levels:**
    *   Ability to plan across multiple dimensions:
        *   **Time:** Season, Year, Quarter, Month, Week.
        *   **Product Hierarchy:** Merchandise Category (at various levels from `isretail_merch_category_nodes`).
        *   **Location Hierarchy:** Total Company, Sales Channel (e.g., Retail, Wholesale, E-com), Store Cluster, individual Site (`isretail_site_extensions`).
*   **1.2. Key Planning Metrics (Variables):**
    *   Sales (at Retail Value, Cost Value, Units).
    *   Gross Margin, Gross Margin Percentage (GMROI - Gross Margin Return on Investment).
    *   Inventory (Opening Stock, Closing Stock, Average Stock - at Retail Value, Cost Value, Units).
    *   Stock Turn / Forward Weeks of Supply.
    *   Planned Receipts (at Retail Value, Cost Value, Units).
    *   Markdowns (Value, Percentage of Sales).
    *   Shrinkage (Planned Percentage).
*   **1.3. Planning Methods:**
    *   **Top-Down Planning:** Setting high-level targets (e.g., total company sales) and distributing them down to lower levels.
    *   **Bottom-Up Planning:** Developing plans at lower levels (e.g., store/category) and aggregating them upwards.
    *   **Middle-Out Planning:** A combination of both.
    *   Support for planning based on historical performance (Last Year - LY), and applying growth/change factors.
*   **1.4. Plan Versioning & Scenarios:**
    *   Support for multiple plan versions (e.g., Pre-Season Plan, Current Plan/Forecast, Revised Plan).
    *   Ability to create "what-if" scenarios.
*   **1.5. Workflow & Approval:** Basic workflow for plan submission, review, and approval.

## 2. Assortment Planning

Assortment Planning determines the specific articles and the breadth/depth of product offerings for different stores, channels, or customer segments.

*   **2.1. Assortment Strategy & Guidelines:**
    *   Define overall assortment strategies (e.g., target width vs. depth, price point strategy, brand mix).
    *   Link to Merchandise Financial Plan targets (e.g., sales contribution per category).
*   **2.2. Store/Channel Clustering for Assortment:**
    *   Utilize `isretail_site_clusters` to group stores with similar characteristics for targeted assortment planning.
*   **2.3. Placeholder Management & New Product Introduction (NPI) Link:**
    *   Ability to include placeholders for new products (not yet fully defined in Article Master) in assortment plans, linking to NPI projects or PLM product ideas.
    *   Track planned launch dates and estimated sales for these new items.
*   **2.4. Assortment Building & Item Selection:**
    *   Tools for buyers/planners to select specific articles (Generic Articles and/or Variants from `isretail_article_variants`) for inclusion in assortments.
    *   Consideration of historical sales performance, forecasts (if available from a demand planning tool), market trends, visual merchandising guidelines, and store capacity (e.g., shelf space, fixture capacity).
    *   Define planned buy quantities (or depth) per article/variant within an assortment for a given store/cluster.
*   **2.5. Assortment Balancing & Analytics:**
    *   Analyze planned assortments for balance (e.g., price point distribution, brand mix, color spread, size curve analysis).
    *   Key metrics: planned sales, margin, turn for the assortment.
    *   "What-if" analysis for changes in assortment mix.
*   **2.6. Assortment Versioning & Lifecycle:** Manage different versions of assortments (e.g., initial plan, revised plan based on buy execution). Link to Seasons.

## 3. Open-to-Buy (OTB) Management

OTB is a financial control mechanism to ensure that planned purchases are in line with planned sales and inventory targets, preventing overbuying or underbuying.

*   **3.1. OTB Calculation:**
    *   Calculate OTB amounts (at cost and/or retail value) for specific time periods (e.g., monthly) and merchandise categories.
    *   Formula components: Planned Sales + Planned Markdowns + Planned End-of-Period Inventory - Planned Beginning-of-Period Inventory - Committed Purchases (Open POs).
*   **3.2. OTB Tracking & Consumption:**
    *   Track OTB budgets in real-time.
    *   When new Purchase Orders are created or existing ones are modified, the system should check against and update the available OTB for the relevant category and period.
    *   Alerts or blocks if a planned purchase exceeds available OTB.
*   **3.3. OTB Reporting & Analysis:**
    *   Reports showing planned OTB, actual OTB consumption, and remaining OTB.
    *   Ability to drill down by merchandise category, time period, and buyer.
*   **3.4. OTB Adjustments:** Mechanisms for adjusting OTB budgets based on changes in sales trends or inventory levels (requires approval).

## 4. Vendor Management for Retail (Extensions)

Extends core ARCA Vendor Master (`mdg_business_partners_core` where `is_vendor` = true) with retail/fashion specific attributes and performance tracking.

*   **4.1. Retail-Specific Vendor Attributes:**
    *   Typical lead times for new collections vs. reorders.
    *   Factory compliance status (e.g., social audits, environmental certifications).
    *   Quality performance history.
    *   Return policies specific to the vendor.
    *   Payment terms common in fashion (e.g., net terms, early payment discounts).
    *   Vendor portal access credentials (if SRM/Ariba is used).
*   **4.2. Vendor Performance Tracking (Retail Context):**
    *   Track vendor performance on criteria like:
        *   On-time delivery for seasonal goods / initial collection shipments.
        *   Quality of goods received (link to QM data).
        *   Cost accuracy (invoice vs. PO price).
        *   Compliance with packaging and labeling requirements.
    *   Vendor scorecards specific to retail needs.

## 5. Purchase Order Management (Retail Specifics)

Leverages ARCA MM Purchase Order functionality but adds retail-specific context and controls.

*   **5.1. Retail Purchase Order Creation:**
    *   Create POs for finished goods (apparel, footwear, accessories) often specifying Article Variants (SKUs).
    *   Support for creating POs with reference to Assortment Plans or buying sheets.
    *   Ability to include pre-pack definitions on a PO (e.g., "Assorted T-Shirt Pack - 12 units: 2S-Red, 4M-Red, 4L-Red, 2XL-Red").
*   **5.2. OTB Integration:**
    *   As described in OTB Management, PO creation/modification must check against and consume available OTB budget.
*   **5.3. Seasonal POs & Delivery Windows:**
    *   Clearly associate POs with specific Seasons and define required delivery windows critical for fashion.
    *   Tracking against these windows.
*   **5.4. Landed Cost Calculation (Basic Consideration):**
    *   Ability to estimate or track additional costs associated with imported goods (freight, duty, insurance) to arrive at a landed cost for purchased articles, for better margin visibility. (Deep integration with ARCA TM & FICO for actuals).
*   **5.5. Integration with Strategic Sourcing (ARCA SRM/Ariba):**
    *   If strategic sourcing (vendor selection, contract negotiation for large buys) is done in SRM/Ariba, the resulting contracts or supplier agreements should be referenced or used to create operational POs in ARCA MM/IS-Retail.

This scope for Merchandise Planning and Buying provides the essential tools for retailers to plan their product offerings and manage their purchasing budgets effectively.
EOL

echo "docs/is_retail/IsRetailMerchPlanningScope.md created successfully."
