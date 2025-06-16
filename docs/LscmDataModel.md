# "LSCM" Module: Data Model Design & Master Data Strategy (MySQL)

This document outlines the MySQL database schema design for the Logistics & Supply Chain Management (LSCM) module and presents a strategy for managing shared master data (Material, Customer, Vendor) to ensure LSCM's modularity. All LSCM-specific tables will use the `lscm_` prefix.

## 1. General Data Model Principles for LSCM

*   **Prefixing:** All LSCM-specific tables are prefixed with `lscm_`.
*   **Database:** Resides within the primary ERP database (modular monolith context).
*   **Internal Integrity:** Foreign keys enforce relationships within LSCM tables.
*   **Auditability:** Standard audit columns (`created_at`, `updated_at`, `created_by_user_id`, `updated_by_user_id` linked to `core_users`) on key transactional and master data extension tables.
*   **Modularity:** Design choices prioritize LSCM's ability to be added or removed with minimal impact on other modules, especially concerning master data.

## 2. Master Data Management Strategy (Crucial for LSCM Modularity)

A hybrid approach is proposed, using a lightweight "Core Master Data Service" (referred to as `CoreMDM`, potentially part of the `Core` ERP module or a very lean dedicated module) for essential global identifiers, with rich module-specific extensions.

### 2.1. Core Master Data Service (CoreMDM) - Conceptual Owner of Global IDs

*   **`core_materials`** (Managed by CoreMDM)
    *   `id` (PK, Global ERP Material ID)
    *   `material_number` (UK, User-facing/external material number)
    *   `base_unit_of_measure_id` (FK to `core_units_of_measure`)
    *   `material_type_id` (FK to `core_material_types` - e.g., Raw Material, Finished Good, Service)
    *   `description_short` (VARCHAR, basic global description)
    *   `is_active` (Boolean)
    *   `created_at`, `updated_at`

*   **`core_business_partners`** (Managed by CoreMDM)
    *   `id` (PK, Global ERP Business Partner ID)
    *   `bp_number` (UK, User-facing/external BP number)
    *   `bp_type` (ENUM: 'Organization', 'Person')
    *   `name1` (Main name line)
    *   `name2` (Additional name line, optional)
    *   `is_customer` (Boolean flag, indicates if BP *can* be a customer)
    *   `is_vendor` (Boolean flag, indicates if BP *can* be a vendor)
    *   `is_active` (Boolean)
    *   `created_at`, `updated_at`
    *   (Potentially a `core_addresses` and `core_communications` table linked here if truly global and simple, otherwise address/comm data is module-specific extension).

*   **`core_units_of_measure`** & **`core_material_types`**: Simple lookup tables managed by CoreMDM.

### 2.2. LSCM's Module-Specific Master Data Extensions

LSCM creates and manages its own detailed views/extensions of core master data, linked via the `core_material_id` or `core_business_partner_id`.

*   **Material Master Extensions (LSCM):**
    *   **`lscm_material_general_data`**
        *   `id` (PK)
        *   `core_material_id` (FK, UK)
        *   `description_long` (TEXT)
        *   `gross_weight`, `net_weight`, `weight_unit_id` (FK to `core_units_of_measure`)
        *   `volume`, `volume_unit_id` (FK to `core_units_of_measure`)
        *   `size_dimensions` (VARCHAR)
        *   ... (other general logistic data)
    *   **`lscm_material_plant_data`** (Data specific to a material within a plant)
        *   `id` (PK)
        *   `core_material_id` (FK)
        *   `plant_id` (FK to `lscm_plants` or `core_organization_units`)
        *   `procurement_type` (ENUM: 'External', 'InHouse', 'Both')
        *   `mrp_type_id` (FK to `lscm_pp_mrp_types`)
        *   `mrp_controller_id` (FK to `core_users` or `lscm_pp_mrp_controllers` table)
        *   `lot_sizing_procedure_id` (FK to `lscm_pp_lot_sizing_procedures`)
        *   `reorder_point`, `safety_stock`
        *   `planned_delivery_time_days` (for procurement)
        *   `in_house_production_time_days`
        *   `storage_location_id_default_issue` (FK to `lscm_storage_locations`)
        *   `storage_location_id_default_receipt` (FK to `lscm_storage_locations`)
        *   `purchasing_group_id` (FK to `lscm_purchasing_groups`)
        *   `profit_center_id` (FK to `fina_co_profit_centers` - for CO integration)
        *   `valuation_class_id` (FK to `fina_valuation_classes` - for Fina integration)
        *   UNIQUE (`core_material_id`, `plant_id`)
    *   **`lscm_material_sales_org_data`** (Data specific to material for a sales organization)
        *   `id` (PK)
        *   `core_material_id` (FK)
        *   `sales_organization_id` (FK to `lscm_sales_organizations`)
        *   `distribution_channel_id` (FK to `lscm_distribution_channels`)
        *   `delivering_plant_id` (FK to `lscm_plants`)
        *   `item_category_group` (e.g., NORM for standard item)
        *   `tax_classification_json` (JSON for different tax types/countries)
        *   UNIQUE (`core_material_id`, `sales_organization_id`, `distribution_channel_id`)
    *   **`lscm_material_quality_data`**
        *   `id` (PK)
        *   `core_material_id` (FK)
        *   `plant_id` (FK)
        *   `inspection_setup_json` (JSON defining inspection types, plans)
        *   UNIQUE (`core_material_id`, `plant_id`)

*   **Vendor Master Extensions (LSCM MM):**
    *   **`lscm_vendor_purchasing_data`**
        *   `id` (PK)
        *   `core_business_partner_id` (FK, where `is_vendor` is true)
        *   `purchasing_organization_id` (FK to `lscm_purchasing_organizations`)
        *   `purchase_order_currency_code` (FK to `fina_currencies`)
        *   `payment_terms_id` (FK to `fina_payment_terms` - LSCM uses Fina's definition)
        *   `incoterms_id` (FK to `lscm_incoterms`)
        *   `gr_based_invoice_verification` (Boolean)
        *   UNIQUE (`core_business_partner_id`, `purchasing_organization_id`)

*   **Customer Master Extensions (LSCM SD):**
    *   **`lscm_customer_sales_data`**
        *   `id` (PK)
        *   `core_business_partner_id` (FK, where `is_customer` is true)
        *   `sales_organization_id` (FK to `lscm_sales_organizations`)
        *   `distribution_channel_id` (FK to `lscm_distribution_channels`)
        *   `division_id` (FK to `lscm_divisions`)
        *   `shipping_conditions_id` (FK to `lscm_shipping_conditions`)
        *   `delivery_priority_id` (FK to `lscm_delivery_priorities`)
        *   `order_currency_code` (FK to `fina_currencies`)
        *   `payment_terms_id` (FK to `fina_payment_terms`)
        *   `incoterms_id` (FK to `lscm_incoterms`)
        *   `credit_control_area_id` (FK to `fina_credit_control_areas` for credit mgt link)
        *   UNIQUE (`core_business_partner_id`, `sales_organization_id`, `distribution_channel_id`, `division_id`)

### 2.3. Synchronization & Data Flow for Master Data

*   **Creation:**
    1.  Initiating module (e.g., LSCM MM for a new material, CRM for a new prospect/customer) calls CoreMDM service/API to create the `core_material` or `core_business_partner` record, providing minimal required data.
    2.  CoreMDM creates the record, assigns the global ID, and returns it.
    3.  The initiating module then creates its own module-specific extension records (e.g., `lscm_material_plant_data`) linking to the global ID.
    4.  CoreMDM publishes an event (e.g., `CoreMaterialCreatedEvent`, `CoreBusinessPartnerCreatedEvent`).
*   **Updates:**
    *   Core fields (e.g., `material_number`, `bp_name1`) are updated via CoreMDM service, which then publishes update events.
    *   Module-specific extension data is updated within the respective module (e.g., LSCM updates `lscm_material_plant_data.mrp_type_id`). LSCM might publish events about its own data changes if other modules need to know (e.g., `LscmMaterialPlantMrpTypeChangedEvent`).
*   **Consumption:** Modules query CoreMDM for lists/searches of materials/BPs, then retrieve their own extension data using the global ID.

## 3. LSCM Organizational Structures

These define the framework for LSCM operations. They can be LSCM-specific tables that might link to `core_organization_units` if a global org model exists and these are considered specific types of core units.

*   **`lscm_plants`**
    *   `id` (PK)
    *   `plant_code` (UK)
    *   `name`
    *   `company_code_id` (FK to `fina_company_codes` - crucial link to Finance)
    *   `address_id` (FK to `core_addresses` or stores address directly)
    *   `factory_calendar_id` (FK to `core_calendars`)
*   **`lscm_storage_locations`**
    *   `id` (PK)
    *   `plant_id` (FK)
    *   `sloc_code` (UK within plant)
    *   `description`
*   **`lscm_sales_organizations`**
    *   `id` (PK)
    *   `sorg_code` (UK)
    *   `name`
    *   `company_code_id` (FK to `fina_company_codes`)
*   **`lscm_distribution_channels`** (e.g., Wholesale, Retail, Online)
    *   `id` (PK)
    *   `channel_code` (UK)
    *   `name`
*   **`lscm_divisions`** (Product divisions, e.g., Electronics, Apparel)
    *   `id` (PK)
    *   `division_code` (UK)
    *   `name`
*   **`lscm_sales_areas`** (Combination of Sales Org, Dist Channel, Division)
    *   `id` (PK)
    *   `sales_organization_id` (FK)
    *   `distribution_channel_id` (FK)
    *   `division_id` (FK)
    *   UNIQUE (`sales_organization_id`, `distribution_channel_id`, `division_id`)
*   **`lscm_purchasing_organizations`**
    *   `id` (PK)
    *   `porg_code` (UK)
    *   `name`
    *   `company_code_id` (FK to `fina_company_codes`, optional - can be cross-company)
*   **`lscm_purchasing_groups`** (Buyers or groups of buyers)
    *   `id` (PK)
    *   `pgroup_code` (UK)
    *   `name`
*   **`lscm_shipping_points`**
    *   `id` (PK)
    *   `plant_id` (FK)
    *   `spoint_code` (UK within plant)
    *   `name`

## 4. Key Tables for LSCM Sub-Modules (Illustrative)

This is not exhaustive but shows key entities for each sub-module. Many will have `_header` and `_items` tables.

### 4.1. Materials Management (MM)
*   **Procurement:**
    *   `lscm_mm_purchase_requisitions_header`, `lscm_mm_purchase_requisitions_items`
    *   `lscm_mm_purchase_orders_header`, `lscm_mm_purchase_orders_items`
    *   `lscm_mm_vendor_contracts_header`, `lscm_mm_vendor_contracts_items`
    *   `lscm_mm_source_lists` (Material-vendor supply arrangements)
*   **Inventory Management:**
    *   `lscm_mm_inventory_documents_header` (Material documents for movements)
    *   `lscm_mm_inventory_documents_items` (Details of movement: material, qty, plant, sloc, movement_type, links to PO/SO/ProdOrder)
    *   `lscm_mm_material_stock` (Aggregated current stock: `core_material_id`, `plant_id`, `storage_location_id`, `stock_type` (unrestricted, quality, blocked), `quantity`) - This table is updated by goods movements.
    *   `lscm_mm_physical_inventory_header`, `lscm_mm_physical_inventory_items` (for stock counts)
    *   `lscm_mm_reservations`
*   **Invoice Verification:**
    *   `lscm_mm_liv_documents_header` (Logistics Invoice Verification docs)
    *   `lscm_mm_liv_documents_items` (linking to PO items, amounts, variances)
    *   (Actual AP posting is in Fina, this tracks the MM part of verification)

### 4.2. Sales and Distribution (SD)
*   `lscm_sd_sales_documents_header` (For inquiries, quotations, orders, contracts)
    *   `document_type` (ENUM: 'Inquiry', 'Quotation', 'Order', 'Contract')
    *   `core_business_partner_id_sold_to` (FK)
    *   `core_business_partner_id_ship_to` (FK)
    *   `sales_area_id` (FK)
    *   `status` (e.g., Open, In Process, Completed, Cancelled)
    *   ... (pricing procedure, dates, totals)
*   `lscm_sd_sales_documents_items`
    *   `sales_document_header_id` (FK)
    *   `core_material_id` (FK)
    *   `quantity`, `unit_of_measure_id`
    *   `net_price`, `net_value`, `tax_amount`
    *   `delivering_plant_id` (FK)
*   `lscm_sd_pricing_conditions` (Stores condition records for pricing)
*   `lscm_sd_outbound_deliveries_header`, `lscm_sd_outbound_deliveries_items`
*   `lscm_sd_billing_documents_header` (Invoices, Credit Memos, Debit Memos)
*   `lscm_sd_billing_documents_items`

### 4.3. Production Planning (PP)
*   `lscm_pp_boms_header`, `lscm_pp_boms_items` (Bill of Materials)
*   `lscm_pp_work_centers`
    *   `cost_center_id` (FK to `fina_co_cost_centers`)
    *   `capacity_category_id`, `standard_available_capacity`
*   `lscm_pp_routings_header`, `lscm_pp_routing_operations`
    *   `work_center_id` (FK)
    *   `standard_times_json` (setup, machine, labor)
*   `lscm_pp_planned_independent_requirements` (PIRs)
*   `lscm_pp_mrp_results` (Output of MRP runs, e.g., planned orders, purchase requisitions generated)
*   `lscm_pp_production_orders_header`
    *   `core_material_id` (FK, material to produce)
    *   `quantity_to_produce`, `quantity_produced`
    *   `bom_id` (FK), `routing_id` (FK)
    *   `status` (e.g., Created, Released, Confirmed, Delivered, Closed)
*   `lscm_pp_production_order_components` (Material components required)
*   `lscm_pp_production_order_operations` (Operations from routing)
*   `lscm_pp_production_order_confirmations` (Activity and quantity confirmations)

### 4.4. Plant Maintenance (PM)
*   `lscm_pm_equipment_master`
    *   `functional_location_id` (FK, optional)
    *   `cost_center_id` (FK to `fina_co_cost_centers`, responsible CC)
*   `lscm_pm_functional_locations` (Hierarchical structure)
*   `lscm_pm_maintenance_notifications` (Simplified)
*   `lscm_pm_maintenance_orders_header`
    *   `equipment_id` (FK, optional)
    *   `functional_location_id` (FK, optional)
    *   `order_type` (e.g., Corrective, Preventive)
    *   `status`
*   `lscm_pm_maintenance_order_operations`
*   `lscm_pm_maintenance_order_components` (Spare parts)
*   `lscm_pm_maintenance_plans` (Basic: time-based, counter-based triggers)

### 4.5. Quality Management (QM)
*   `lscm_qm_inspection_plans_header`
*   `lscm_qm_inspection_plan_characteristics`
    *   `characteristic_description`, `test_method`, `target_value`, `upper_limit`, `lower_limit`
*   `lscm_qm_inspection_lots`
    *   `core_material_id` (FK)
    *   `lot_origin` (e.g., 'GoodsReceiptPO', 'ProductionReceipt')
    *   `reference_document_id` (e.g., PO number, Production Order number)
    *   `status` (e.g., Created, Inspected, UsageDecisionMade)
*   `lscm_qm_inspection_lot_results` (Recorded values for characteristics)
*   `lscm_qm_usage_decisions`
*   `lscm_qm_quality_notifications` (Basic defect tracking)

This data model provides a foundational structure. Detailed attributes, indexing, and relationships will be refined during the development of each LSCM sub-component. The master data strategy is key to LSCM's successful modular integration.
