# IS-Retail/Apparel: Merchandise Planning & Buying Data Model Design (MySQL)

This document outlines the proposed MySQL database schema for the Merchandise Planning and Buying functionalities within the ARCA IS-Retail/Apparel and Footwear Solution. These tables support Merchandise Financial Planning (MFP), Assortment Planning, Open-to-Buy (OTB), and retail-specific extensions for Purchase Orders and Vendor data. All tables use the `isretail_` prefix.

## 1. General Principles

*   **Prefixing:** All tables are prefixed with `isretail_`.
*   **Linkage to Core Data:** These tables will link to existing core ARCA master data (e.g., `isretail_merch_category_nodes`, `isretail_seasons`, `mdg_materials_core`, `mdg_business_partners_core`, `lscm_mm_purchase_orders_header`).
*   **Planning Granularity:** MFP and Assortment Planning data models need to support planning at various levels of product and location hierarchies and time dimensions.
*   **Auditability:** Standard audit columns where appropriate.

## 2. Merchandise Financial Planning (MFP)

*   **`isretail_mfp_plan_headers`** (Defines a specific financial plan instance)
    *   `id` (PK)
    *   `plan_name` (VARCHAR, e.g., "SS24 Pre-Season Sales & Margin Plan")
    *   `plan_type` (ENUM: 'PreSeason', 'InSeasonForecast', 'RevisedPlan')
    *   `version_number` (INT, default 1)
    *   `status_id` (FK to `isretail_planning_statuses` - e.g., 'Draft', 'Submitted', 'Approved', 'Active', 'Archived')
    *   `time_hierarchy_level` (ENUM: 'Week', 'Month', 'Quarter', 'Season', 'Year' - granularity of this plan)
    *   `product_hierarchy_root_node_id` (FK to `isretail_merch_category_nodes`, defines scope)
    *   `location_hierarchy_root_node_id` (FK to a potential `isretail_site_hierarchy_nodes` or based on site clusters/channels, defines scope)
    *   `fiscal_year_start` (INT, if applicable)
    *   `currency_code` (FK to `fina_currencies`)
    *   `description` (TEXT, nullable)
    *   `created_at`, `updated_at`, `created_by_user_id`, `updated_by_user_id`

*   **`isretail_mfp_plan_data`** (Stores the actual planning figures)
    *   `id` (PK)
    *   `mfp_plan_header_id` (FK)
    *   `time_period_key` (VARCHAR, e.g., "202401" for Jan 2024, "2024W10" for Week 10 2024 - format depends on `time_hierarchy_level` in header)
    *   `merch_category_node_id` (FK to `isretail_merch_category_nodes`)
    *   `location_node_id` (FK, representing a site, cluster, or channel node)
    *   `kpi_code` (VARCHAR, e.g., "SALES_RETAIL_VALUE", "SALES_UNITS", "GROSS_MARGIN_PERCENT", "PLANNED_RECEIPTS_COST", "BOP_INV_UNITS", "EOP_INV_VALUE")
    *   `planned_value` (Decimal)
    *   `actual_value` (Decimal, nullable - periodically updated from FICO/SD/MM)
    *   `ly_value` (Decimal, nullable - Last Year's actual for comparison)
    *   `forecast_value` (Decimal, nullable - if integrating with a forecast)
    *   UNIQUE (`mfp_plan_header_id`, `time_period_key`, `merch_category_node_id`, `location_node_id`, `kpi_code`)

*   **`isretail_planning_statuses`**
    *   `id` (PK)
    *   `status_code` (UK)
    *   `description`

## 3. Assortment Planning

*   **`isretail_assortment_plans_header`**
    *   `id` (PK)
    *   `plan_name` (VARCHAR, e.g., "SS24 Men's Casual Shirts - Urban Stores")
    *   `season_id` (FK to `isretail_seasons`)
    *   `target_scope_type` (ENUM: 'SiteCluster', 'Channel', 'SiteGroup', 'AllStores')
    *   `target_scope_id` (BIGINT UNSIGNED, FK to `isretail_site_clusters.id` or other relevant ID based on type)
    *   `status_id` (FK to `isretail_planning_statuses`)
    *   `merch_category_node_id_focus` (FK, optional - if plan is for a specific category)
    *   `description` (TEXT, nullable)
    *   `created_at`, `updated_at`, `created_by_user_id`

*   **`isretail_assortment_plan_items`** (Placeholders or actual articles in the plan)
    *   `id` (PK)
    *   `assortment_plan_header_id` (FK)
    *   `mdg_materials_core_id_generic` (FK to `mdg_materials_core.id` for the generic article/style, nullable if it's a true placeholder not yet in MDG)
    *   `placeholder_name` (VARCHAR, if `mdg_materials_core_id_generic` is null)
    *   `number_of_variants_planned` (INT, e.g., how many color/size SKUs are planned for this style)
    *   `target_average_retail_price` (Decimal, nullable)
    *   `target_average_cost_price` (Decimal, nullable)
    *   `planned_total_buy_quantity` (Decimal, across all variants for this item in this assortment)
    *   `planned_sales_units` (Decimal, nullable)
    *   `planned_sales_value` (Decimal, nullable)
    *   `notes` (TEXT)

*   **`isretail_assortment_plan_item_attributes`** (e.g. target colorways, size ranges for a style before specific variants are chosen)
    *   `id` (PK)
    *   `assortment_plan_item_id` (FK)
    *   `attribute_type` (VARCHAR, e.g. "TARGET_COLOR_PALETTE", "SIZE_CURVE_PROFILE")
    *   `attribute_value_json` (JSON)

## 4. Open-to-Buy (OTB) Management

*   **`isretail_otb_ledgers`** (Stores calculated OTB and its consumption)
    *   `id` (PK)
    *   `merch_category_node_id` (FK)
    *   `time_period_key` (VARCHAR, e.g., "YYYYMM" for monthly OTB)
    *   `currency_code` (FK)
    *   `planned_bop_inventory_value` (Decimal, Beginning of Period)
    *   `planned_sales_value` (Decimal)
    *   `planned_markdown_value` (Decimal)
    *   `planned_eop_inventory_value` (Decimal, End of Period)
    *   `planned_receipts_value` (Decimal, calculated: EOP_Inv + Sales + Markdown - BOP_Inv)
    *   `open_po_commitments_value` (Decimal, sum of open POs for this category/period from MM)
    *   `actual_receipts_value` (Decimal, sum of actual GRs for this cat/period from MM)
    *   `calculated_otb_value` (Decimal, Planned Receipts - Open PO Commitments not yet received)
    *   `adjustments_to_otb_value` (Decimal, manual adjustments with audit)
    *   `last_calculated_at` (TIMESTAMP)
    *   UNIQUE (`merch_category_node_id`, `time_period_key`, `currency_code`)

## 5. Vendor Management for Retail (Extensions)

*   **`isretail_vendor_extensions`** (Extends `mdg_business_partners_core` where BP is a vendor)
    *   `id` (PK)
    *   `mdg_business_partner_id` (FK, UK - the core vendor ID)
    *   `typical_collection_lead_time_weeks` (INT, nullable)
    *   `reorder_lead_time_weeks` (INT, nullable)
    *   `factory_compliance_status_id` (FK to a new `isretail_compliance_statuses` table, nullable)
    *   `last_social_audit_date` (DATE, nullable)
    *   `vendor_tier` (VARCHAR, nullable, e.g., "Strategic", "Preferred", "Transactional")
    *   `notes_retail_specific` (TEXT)

## 6. Purchase Order Management (Retail Specific Extensions)

*   **`isretail_retail_po_header_extensions`**
    *   `id` (PK)
    *   `lscm_mm_purchase_order_id` (FK to `lscm_mm_purchase_orders_header.id`, UK - the core MM PO)
    *   `season_id` (FK to `isretail_seasons`, nullable)
    *   `collection_id` (FK to `isretail_collections`, nullable)
    *   `otb_merch_category_node_id` (FK, the category whose OTB was consumed)
    *   `otb_time_period_key` (VARCHAR, the period whose OTB was consumed)
    *   `is_prepack_order` (Boolean, default false)
    *   `required_delivery_window_start` (DATE, nullable)
    *   `required_delivery_window_end` (DATE, nullable)
    *   `buyer_user_id` (FK to `core_users`, if not on MM PO)

*   **`isretail_retail_po_item_prepack_definitions`** (If a PO line item itself is a pre-pack with a defined component mix)
    *   `id` (PK)
    *   `lscm_mm_purchase_order_item_id` (FK to `lscm_mm_purchase_orders_items.id` - the MM PO line for the pre-pack "article")
    *   `component_mdg_materials_core_id` (FK - the actual SKU material ID within the pre-pack)
    *   `quantity_of_component_in_prepack` (Decimal)
    *   `notes` (VARCHAR, nullable)

This data model provides structures for key retail planning and buying processes, ensuring they are linked to core ARCA data and can drive execution.
