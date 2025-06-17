# IS-Retail/Apparel: Master Data Model Design (MySQL)

This document outlines the proposed MySQL database schema for the industry-specific master data extensions required for the ARCA IS-Retail/Apparel and Footwear Solution. These tables extend and link to core ARCA master data entities governed by MDG (e.g., `mdg_materials_core`, `lscm_plants`). All IS-Retail specific tables will use the `isretail_` prefix.

## 1. General Principles

*   **Prefixing:** All tables specific to IS-Retail master data are prefixed with `isretail_`.
*   **Extension Model:** These tables primarily store retail-specific attributes and structures, linking back to core ARCA master data IDs (e.g., `mdg_materials_core_id`, `lscm_plant_id`).
*   **Variant Focus:** The model must robustly support the concept of generic articles and their many variants (SKUs).
*   **Auditability:** Standard audit columns on key tables.

## 2. Article Master Extensions

### 2.1. Generic Articles & Variants
*   **`isretail_generic_articles`** (Stores retail-specific data for the "style" or generic level)
    *   `id` (PK)
    *   `mdg_materials_core_id` (FK to `mdg_materials_core.id`, UK - this IS the generic article's material master)
    *   `style_number` (VARCHAR, UK - user-friendly style code, optional if `material_number` suffices)
    *   `collection_id` (FK to `isretail_collections`, nullable)
    *   `default_merch_category_node_id` (FK to `isretail_merch_category_nodes`, nullable)
    *   `brand_id` (FK to a potential `core_brands` or `isretail_brands` table, nullable)
    *   `lifecycle_status_id` (FK to `isretail_fashion_lifecycle_statuses`, nullable)
    *   `created_at`, `updated_at`, `created_by_user_id`, `updated_by_user_id`

*   **`isretail_characteristics`** (Defines characteristics like Color, Size, Fit)
    *   `id` (PK)
    *   `characteristic_code` (UK, e.g., "COLOR", "SIZE", "FIT")
    *   `name` (VARCHAR)
    *   `description` (TEXT, nullable)
    *   `is_variant_defining` (Boolean, indicates if this characteristic can be used to define variants globally or per category)

*   **`isretail_characteristic_values`** (Allowed values for characteristics)
    *   `id` (PK)
    *   `characteristic_id` (FK to `isretail_characteristics`)
    *   `value_code` (UK within characteristic_id, e.g., "RED", "SML", "SLIMFIT")
    *   `name` (VARCHAR, e.g., "Red", "Small", "Slim Fit")
    *   `sort_order` (INT, nullable)

*   **`isretail_article_variant_definitions`** (Defines which characteristics apply to a generic article to form variants)
    *   `id` (PK)
    *   `generic_article_id` (FK to `isretail_generic_articles`)
    *   `characteristic_id` (FK to `isretail_characteristics`)
    *   `is_mandatory_for_variant` (Boolean)
    *   UNIQUE (`generic_article_id`, `characteristic_id`)

*   **`isretail_article_variants`** (The actual SKUs - each is also an `mdg_materials_core` record)
    *   `id` (PK)
    *   `mdg_materials_core_id` (FK to `mdg_materials_core.id`, UK - this IS the variant's material master)
    *   `generic_article_id` (FK to `isretail_generic_articles`)
    *   `gtin_ean` (VARCHAR, UK, nullable - Global Trade Item Number)
    *   `variant_description_override` (VARCHAR, nullable - if specific variant needs a different description part)
    *   `created_at`, `updated_at`

*   **`isretail_article_variant_characteristic_values`** (Links a variant to its specific characteristic values)
    *   `article_variant_id` (FK to `isretail_article_variants`)
    *   `characteristic_value_id` (FK to `isretail_characteristic_values`)
    *   PRIMARY KEY (`article_variant_id`, `characteristic_value_id`)

### 2.2. Seasonality & Collections
*   **`isretail_seasons`**
    *   `id` (PK)
    *   `season_code` (UK, e.g., "SS24", "FW24_HOLIDAY")
    *   `name` (VARCHAR)
    *   `year` (INT)
    *   `start_date`, `end_date`
    *   `theme_description` (TEXT, nullable)

*   **`isretail_article_season_assignments`** (Links generic articles or variants to seasons)
    *   `id` (PK)
    *   `mdg_materials_core_id` (FK - can be generic or variant material ID)
    *   `season_id` (FK to `isretail_seasons`)
    *   `notes` (VARCHAR, nullable)
    *   UNIQUE (`mdg_materials_core_id`, `season_id`)

*   **`isretail_collections`**
    *   `id` (PK)
    *   `collection_code` (UK)
    *   `name` (VARCHAR)
    *   `description` (TEXT, nullable)
    *   `season_id` (FK to `isretail_seasons`, nullable - a collection can be part of a season)
    *   `valid_from_date`, `valid_to_date` (nullable)

*   **`isretail_article_collection_assignments`** (Links generic articles or variants to collections)
    *   `id` (PK)
    *   `mdg_materials_core_id` (FK)
    *   `collection_id` (FK to `isretail_collections`)
    *   UNIQUE (`mdg_materials_core_id`, `collection_id`)

*   **`isretail_fashion_lifecycle_statuses`** (e.g., New, Carry-Over, Markdown, Obsolete)
    *   `id` (PK)
    *   `status_code` (UK)
    *   `description`

### 2.3. Article Pricing & Promotion Extensions
*   **`isretail_article_pricing_extensions`**
    *   `id` (PK)
    *   `mdg_materials_core_id` (FK to `isretail_article_variants.mdg_materials_core_id` or `isretail_generic_articles.mdg_materials_core_id`)
    *   `price_point_id` (FK to `isretail_price_points`, nullable)
    *   `original_retail_price` (Decimal, nullable)
    *   `current_retail_price` (Decimal, nullable - could be managed by a dedicated Pricing module, this is for reference/planning)
    *   `target_margin_percent` (Decimal, nullable)
    *   `is_promotional_item_flag` (Boolean, default false)
    *   `is_markdown_candidate_flag` (Boolean, default false)
    *   UNIQUE (`mdg_materials_core_id`)

*   **`isretail_price_points`**
    *   `id` (PK)
    *   `price_point_value` (Decimal, UK)
    *   `description` (VARCHAR, nullable)

## 3. Site Master (Retail Location Extensions)

*   **`isretail_site_extensions`** (Extends `lscm_plants` or `core_organization_units` if site is an org unit type)
    *   `id` (PK)
    *   `lscm_plant_id` (FK to `lscm_plants.id` or `core_organization_units.id`, UK - the core site entity)
    *   `site_format_id` (FK to `isretail_site_formats`, nullable)
    *   `site_banner_id` (FK to `isretail_site_banners`, nullable)
    *   `selling_space_area` (Decimal, nullable)
    *   `selling_space_area_uom_id` (FK to `core_units_of_measure`, nullable)
    *   `store_grade_id` (FK to `isretail_store_grades`, nullable)
    *   `default_price_list_id` (FK to a potential `sd_price_lists` table, nullable)
    *   `default_currency_code` (FK to `fina_currencies`, if site specific overrides company code)
    *   `pos_system_type` (VARCHAR, informational)
    *   `opening_hours_profile_id` (FK to a potential `core_calendar_working_hours` table, nullable)
    *   `created_at`, `updated_at`

*   **`isretail_site_formats`** (e.g., Flagship, Outlet, Standard Mall Store)
    *   `id` (PK)
    *   `format_code` (UK)
    *   `description`

*   **`isretail_site_banners`** (If a retailer operates multiple banners/brands of stores)
    *   `id` (PK)
    *   `banner_code` (UK)
    *   `name`

*   **`isretail_store_grades`** (e.g., A, B, C based on sales volume, location importance)
    *   `id` (PK)
    *   `grade_code` (UK)
    *   `description`

## 4. Assortment & Listing

*   **`isretail_assortments_header`**
    *   `id` (PK)
    *   `assortment_code` (UK)
    *   `description` (VARCHAR)
    *   `assortment_type_id` (FK to `isretail_assortment_types`)
    *   `valid_from_date`, `valid_to_date`
    *   `created_at`, `updated_at`

*   **`isretail_assortment_types`** (e.g., Core, Seasonal, Promotional, StoreSpecific)
    *   `id` (PK)
    *   `type_code` (UK)
    *   `description`

*   **`isretail_assortment_items`** (Links articles/variants to an assortment)
    *   `id` (PK)
    *   `assortment_header_id` (FK)
    *   `mdg_materials_core_id` (FK - typically the variant/SKU material ID)
    *   `notes` (VARCHAR, nullable)
    *   UNIQUE (`assortment_header_id`, `mdg_materials_core_id`)

*   **`isretail_site_clusters`** (Groups of sites for assortment planning, e.g., "Urban High Traffic Stores")
    *   `id` (PK)
    *   `cluster_code` (UK)
    *   `description`

*   **`isretail_site_cluster_members`**
    *   `site_cluster_id` (FK)
    *   `lscm_plant_id` (FK - the site)
    *   PRIMARY KEY (`site_cluster_id`, `lscm_plant_id`)

*   **`isretail_listing_conditions`** (Authorizes articles/assortments for sale/stock at sites/clusters/channels)
    *   `id` (PK)
    *   `listing_target_type` (ENUM: 'Site', 'SiteCluster', 'SalesChannel')
    *   `target_id` (BIGINT UNSIGNED - FK to `lscm_plants.id` OR `isretail_site_clusters.id` OR a `core_sales_channels.id` table)
    *   `assortment_header_id` (FK, nullable - if listing a whole assortment)
    *   `mdg_materials_core_id` (FK, nullable - if listing an individual article/variant)
    *   `valid_from_date`, `valid_to_date`
    *   `status` (ENUM: 'Active', 'Future', 'Expired', 'Blocked')
    *   `created_at`, `updated_at`
    *   CHECK (`assortment_header_id` IS NOT NULL OR `mdg_materials_core_id` IS NOT NULL)

## 5. Merchandise Category Hierarchy

*   **`isretail_merch_category_nodes`**
    *   `id` (PK)
    *   `hierarchy_id` (VARCHAR, e.g., "DEFAULT_APPAREL" - to support multiple hierarchies if needed)
    *   `node_code` (UK within hierarchy_id)
    *   `name` (VARCHAR)
    *   `description` (TEXT, nullable)
    *   `parent_node_id` (Self-referential FK to `isretail_merch_category_nodes.id`, nullable for root nodes)
    *   `level` (INT - for hierarchy depth)
    *   `sort_order` (INT, nullable)
    *   `attributes_json` (JSON, for category-specific attributes or planning parameters)
    *   `created_at`, `updated_at`

*   **`isretail_article_merch_category_assignments`**
    *   `mdg_materials_core_id` (FK - usually the generic article's material ID)
    *   `merch_category_node_id` (FK to `isretail_merch_category_nodes`)
    *   PRIMARY KEY (`mdg_materials_core_id`, `merch_category_node_id`)

This data model provides the specialized structures for IS-Retail master data, ensuring proper linkage with core ARCA master data.
