# IS-Retail/Apparel: Master Data Integration Strategy

This document outlines the integration strategy for the IS-Retail/Apparel and Footwear solution's specific master data objects (Article extensions, Site, Assortment, Listing, Merchandise Hierarchy) with the core ARCA Master Data Governance (MDG) module and other relevant ARCA ERP components.

## 1. Core Integration Principles for Retail Master Data

*   **MDG as Governor of Core Entities:** ARCA MDG remains the central governor for the creation and core global attributes of fundamental master data like `Material` (which IS-Retail Article extends) and `Business Partner` (relevant for Site's legal entity or if Sites are modeled as specific BP roles). Organizational units like `Plant` or `Company Code` are also MDG-governed.
*   **IS-Retail Provides Extensions & Specifics:** The IS-Retail module introduces and manages the *additional attributes, structures, and relationships* crucial for retail and fashion (e.g., article variants, seasonality, assortments, merchandise hierarchy, site-specific retail attributes). These extensions are always linked back to MDG-governed core IDs.
*   **Workflow-Driven Creation via MDG:** Initiation of new core entities (like a new Material for a new Generic Article) that IS-Retail requires must go through MDG's approval workflows. IS-Retail processes can trigger these MDG workflows.
*   **Event-Driven Synchronization & Replication:** Approved changes to core attributes in MDG relevant to IS-Retail views will be communicated via events. Similarly, when IS-Retail specific data (that might be of interest to other modules, e.g. a new article variant being activated) is finalized, IS-Retail will publish events. MDG is the primary publisher for core data; IS-Retail for its extensions.
*   **Clear Data Ownership:**
    *   MDG owns: `core_material_id`, `material_number`, core `bp_id`, `bp_number`, core `plant_id`, `plant_code`.
    *   IS-Retail owns: Variant definitions linked to `core_material_id`, seasonal assignments, collection data, assortment rules, listing conditions, merchandise category structure, retail-specific site attributes extending a core plant/org unit.

## 2. Integration with ARCA MDG (Master Data Governance)

*   **Article Master (Generic Articles & Variants):**
    *   **Creation Process:**
        1.  User initiates "Create New Generic Article" in IS-Retail UI.
        2.  IS-Retail process triggers an MDG workflow to create a new `mdg_materials_core` record (providing basic description, proposed material type like "Fashion Generic," base UoM).
        3.  MDG workflow completes, approving and activating the `mdg_materials_core` record. MDG publishes `MdgMaterialActivatedEvent` with the `core_material_id`.
        4.  IS-Retail subscribes to this event, receives the `core_material_id`, and then allows the user to create the IS-Retail specific generic article extensions and subsequently its variants. Each variant will also trigger an MDG workflow for its own `mdg_materials_core` record (as each SKU is a distinct material).
    *   **Data Synchronization:** Changes to globally relevant attributes on `mdg_materials_core` (e.g., global description change) managed in MDG will be published via event and consumed by IS-Retail to update its local views/caches if necessary. IS-Retail specific attributes (season, collection, variant characteristics) are managed within IS-Retail.
*   **Site Master:**
    *   **Option A (Site as extended Plant):** A "Site" in IS-Retail is typically an ARCA Plant (`lscm_plants` or `core_organization_units` of type 'Plant') that is MDG-governed. IS-Retail will create/manage an `isretail_site_master_extensions` table linked to the `plant_id`, holding retail-specific attributes (store format, grading, etc.). Creation of a new retail store site first involves ensuring the Plant is created/active via MDG.
    *   **Option B (Site as distinct MDG object):** If "Site" is a more abstract concept than Plant (e.g., an online channel), it might be a separate MDG-governed object type. This requires more setup in MDG. Option A is generally preferred for physical stores.
*   **Merchandise Category Hierarchy:**
    *   The structure of the merchandise hierarchy itself (`isretail_merch_categories` and their parent-child links) is defined and managed within IS-Retail, as it's highly specific to retail merchandising.
    *   MDG is not expected to directly govern this hierarchy's structure, but individual articles assigned to these categories are, of course, MDG-governed at their core.
*   **Assortments & Listing:** These are purely IS-Retail specific business rules and structures, linking MDG-governed Articles and Sites. MDG is not involved in managing assortment definitions or listing conditions directly.

## 3. Integration with ARCA MM (Materials Management)

*   **Article Variants as MM Materials:** Every IS-Retail Article Variant (SKU) that is purchasable, inventoriable, or sellable MUST correspond to an active `mdg_materials_core` record and have associated MM views (purchasing, storage, accounting, costing) created/extended by the MM module.
*   **Inventory Management:** ARCA MM (or ARCA EWM for advanced warehousing) manages the physical inventory (quantities, valuation) at the Article Variant (SKU) level, per plant/storage location. IS-Retail's store inventory functionalities will read from and trigger updates to MM/EWM stock via defined integration points (e.g., POS sales data reducing stock, goods receipts for store replenishment increasing stock).
*   **Procurement:** Purchase Orders in ARCA MM are created for specific Article Variants. OTB checks in IS-Retail Merchandise Buying will influence PO creation.
*   **Material Valuation:** Article Variants are valuated in MM/FICO. IS-Retail uses this valuation for margin calculations.

## 4. Integration with ARCA SD (Sales and Distribution)

*   **Sales Orders & Variants:** ARCA SD sales orders (for wholesale, e-commerce if SD is the fulfillment engine) will specify Article Variants.
*   **Pricing in SD:** The SD pricing engine will determine prices. IS-Retail specific pricing conditions (e.g., promotional prices, markdown prices defined in IS-Retail Promotion/Markdown Management) must be passed to or accessible by the SD pricing engine.
*   **Listing & Assortment Checks in SD:** During sales order creation in SD, the system must perform checks against IS-Retail listing conditions to ensure the Article Variant is allowed for the specific customer, sales channel, or site (if relevant for SD context). This requires an API call from SD to IS-Retail.
*   **Customer Master:** Wholesale customers managed in SD/CRM are based on `mdg_business_partners_core`.

## 5. Integration with ARCA FICO (Financial Accounting & Controlling)

*   **Article Variant Valuation:** Financial valuation of Article Variant inventory is managed in FICO, based on data from MM.
*   **Site as Profit/Cost Center:** Each IS-Retail Site (Store) will typically be a Profit Center in FICO for store-level P&L reporting. It may also be a Cost Center or linked to one. This linkage is part of Site master setup.
*   **Merchandise Category Hierarchy in CO-PA:** The IS-Retail Merchandise Category Hierarchy is a critical dimension for profitability analysis (CO-PA) in FICO. Sales, COGS, and potentially other costs/revenues should be reportable along this hierarchy. This requires mapping or replication of the hierarchy structure to FICO/BI.
*   **Markdown & Promotion Accounting:** Financial impacts of markdowns and promotions (e.g., markdown provisions, actual markdown postings, promotional cost accruals) defined in IS-Retail need to be posted correctly in FICO GL and CO. This requires specific account determination and posting logic triggered by IS-Retail events.

## 6. Integration with ARCA BI (Business Intelligence)

*   **Critical Dimensions:** All IS-Retail specific master data (Article Variants with attributes like size/color, Seasons, Collections, Sites with their attributes, Assortments, Merchandise Category Hierarchy) are essential dimensions for BI.
*   **Data Sourcing:** BI will extract this master data from IS-Retail tables (or the MDG core tables + IS-Retail extension tables) to build its dimensional models for retail analytics (sales, inventory, margin, promotion effectiveness, etc.).

## 7. API Design for IS-Retail Master Data Services

*   IS-Retail will expose internal service APIs (PHP Contracts) for other ARCA modules to:
    *   Query Article Variant details (e.g., `getArticleVariantAttributes(core_material_id)`).
    *   Validate listing conditions (`isArticleListed(core_material_id, site_id, channel_id, date)`).
    *   Fetch Merchandise Category for an article.
    *   Retrieve retail-specific Site attributes.
*   These APIs will be used by SD, MM, PP, BI, etc., for real-time checks or data enrichment.

## 8. Event-Driven Communication

*   **Events Published by IS-Retail Master Data:**
    *   `IsRetailGenericArticleExtensionsDefinedEvent({core_material_id})`
    *   `IsRetailArticleVariantCreatedEvent({core_material_id_variant, generic_article_core_id})`
    *   `IsRetailSiteRetailAttributesUpdatedEvent({site_id})`
    *   `IsRetailAssortmentChangedEvent({assortment_id})`
    *   `IsRetailListingConditionChangedEvent({listing_id})`
    *   `IsRetailMerchCategoryStructureChangedEvent`
    *   `IsRetailSeasonDefinitionChangedEvent`
*   **Events Subscribed to by IS-Retail Master Data:**
    *   `MdgMaterialActivatedEvent` (from MDG, to complete Article setup)
    *   `MdgBusinessPartnerActivatedEvent` (if Site is linked to a BP, or for vendor/customer core data)
    *   `LscmPlantUpdatedEvent` (from LSCM/MDG, if Site is based on Plant and needs to react to core Plant changes)
    *   Events from PLM regarding new product designs that are candidates for becoming generic articles.

This integration strategy ensures that IS-Retail master data is centrally managed where appropriate (via MDG for core elements) but richly extended within IS-Retail itself, and that this data is consistently available to all relevant ARCA processes.
