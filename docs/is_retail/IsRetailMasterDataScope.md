# IS-Retail/Apparel: Master Data Scope

This document defines the scope for industry-specific master data extensions required for the ARCA IS-Retail/Apparel and Footwear Solution. This builds upon and extends core ARCA ERP master data concepts like Material Master (governed by MDG) and organizational structures.

## 1. Article Master (Retail Product Master Extensions)

The Article Master in IS-Retail extends the standard ARCA Material Master (`mdg_materials_core` and its MM/PLM extensions) with functionalities crucial for retail, apparel, and footwear.

*   **1.1. Generic Articles & Variants:**
    *   **Generic Article:** Represents a product at a style level without specific execution characteristics like size or color (e.g., "Men's Classic Polo Shirt"). The Generic Article will be a specific type or have specific attributes on the `mdg_materials_core` or its PLM extension.
    *   **Variants (SKUs - Stock Keeping Units):** Represent the actual sellable/stockable items derived from a Generic Article by applying variant-defining characteristics (e.g., "Men's Classic Polo Shirt, Red, Size M"). Each variant is a distinct `mdg_materials_core` record, linked to its parent Generic Article.
    *   **Variant-Defining Characteristics:** Define which characteristics (e.g., Color, Size, Fit, Style, Length) are used to create variants for a specific Generic Article or Merchandise Category. These characteristics themselves might be managed as master data (e.g., a list of valid Colors, Sizes).
    *   **Matrix Management:** Conceptual support for managing articles that have multiple variant-defining dimensions (e.g., a style with color and size dimensions). The system should facilitate the creation and management of all valid variant combinations.
*   **1.2. Seasonality:**
    *   Define Seasons (e.g., "SS24", "FW24", "Spring/Summer 2024", "Holiday 2023").
    *   Ability to assign Articles (Generic and/or Variants) to one or more Seasons.
    *   Season attributes: Start Date, End Date, Theme.
*   **1.3. Collections:**
    *   Define Collections (e.g., "Back to School 2024," "Winter Outerwear Collection," "Designer Collaboration X").
    *   Ability to group Articles (Generic and/or Variants) into Collections for marketing, planning, and reporting.
    *   Collections can have their own lifecycle and attributes.
*   **1.4. Fashion Cycles & Lifecycle Management:**
    *   Track the lifecycle status of fashion articles (e.g., 'New', 'Carry-Over', 'Promotional', 'Markdown', 'Phase-Out', 'Obsolete') beyond standard material statuses.
    *   Link to effectivity dates for different stages.
*   **1.5. Markdown & Promotion Planning Attributes:**
    *   Specific fields on the Article Master (or a related pricing/promotion entity) to support markdown strategies and promotional events:
        *   Original Retail Price.
        *   Current Retail Price.
        *   Planned markdown dates and percentage/value.
        *   Promotional price flag / link to active promotion.
        *   Price Point / Price Band assignment.
*   **1.6. Additional Retail-Specific Attributes:**
    *   Brand.
    *   Supplier Article Number.
    *   Country of Origin.
    *   Care Instructions (link to standardized codes or text).
    *   Composition (link to standardized material composition data).
    *   GTIN/EAN codes for variants.

## 2. Site Master (Retail Location Master)

Manages different types of locations relevant to retail and wholesale operations. This extends the concept of `lscm_plants` or `core_organization_units`.

*   **2.1. Site Definition:**
    *   Create and manage master data for various site types:
        *   Brick-and-Mortar Stores (Retail Outlets).
        *   Distribution Centers (DCs) – potentially EWM managed.
        *   Warehouses (for retail or wholesale stock).
        *   E-commerce Fulfillment Centers.
        *   Wholesale Showrooms.
        *   Shop-in-Shops.
*   **2.2. Site Attributes:**
    *   Unique Site ID/Number.
    *   Site Name, Address.
    *   Link to ARCA Company Code (`fina_company_codes`).
    *   Site Type (as above).
    *   Store Format / Banner (e.g., Flagship, Outlet, Boutique, Hypermarket).
    *   Selling Space Area (sq ft / m2).
    *   Store Capacity (e.g., for allocation).
    *   Opening Hours Profile.
    *   Grading/Clustering Attributes (e.g., Store Grade A/B/C, Urban/Suburban, High-Traffic).
    *   Default Price List / Currency.
    *   Link to Profit Center / Cost Center in ARCA FICO.
    *   POS System type used at site (informational).

## 3. Assortment & Listing

Defines which articles are planned to be sold or stocked at which sites or channels, and during which periods.

*   **3.1. Assortment Definition:**
    *   Create and manage Assortments (logical groupings of articles).
    *   Assign articles (Generic and/or Variants) to Assortments.
    *   Assortment types (e.g., Core Assortment, Seasonal Assortment, Promotional Assortment).
    *   Define validity periods for assortments.
*   **3.2. Listing Conditions:**
    *   The process of making articles available for sale/stocking at specific sites or sales channels (e.g., e-commerce channel).
    *   Create listing conditions that link an Assortment (or individual Articles/Variants) to one or more Sites or Site Clusters, or Sales Channels.
    *   Listing conditions must have validity periods (start date, end date), often driven by Season.
    *   The system will use listing conditions to control which articles can be ordered for a store, replenished, or sold through a specific channel.
*   **3.3. Store/Channel Clustering for Assortments:**
    *   Group similar sites (e.g., "Grade A Urban Stores") into clusters.
    *   Assign assortments to these clusters for efficient assortment planning and listing.

## 4. Merchandise Category Hierarchy

A flexible, multi-level hierarchy for classifying retail products, primarily used for merchandising, planning, buying, and reporting. This is distinct from engineering/production BOMs or financial hierarchies like cost center hierarchy.

*   **4.1. Hierarchy Definition:**
    *   Ability to define multiple merchandise category hierarchies if needed (e.g., one for apparel, one for footwear, one for accessories).
    *   Support for multiple levels (e.g., Division > Department > Product Category > Sub-Category > Segment > Sub-segment).
    *   User-defined level names and codes.
*   **4.2. Node Management:** Create, modify, and manage nodes within the hierarchy.
*   **4.3. Article Assignment:**
    *   Assign each Article (typically the Generic Article, with variants inheriting) to a node at the lowest applicable level of the merchandise category hierarchy.
    *   An article should generally belong to only one node within a given hierarchy for clear reporting.
*   **4.4. Hierarchy Versioning (Optional):** Support for versioning merchandise hierarchies if they change significantly over time.
*   **4.5. Attributes at Hierarchy Levels:** Ability to assign specific attributes or planning parameters at different levels of the hierarchy (e.g., target margin for a Product Category).

This master data scope provides the foundational data structures specific to the IS-Retail/Apparel and Footwear solution, enabling industry-tailored processes.
