# "Fina" Module: Data Model Design (MySQL)

This document outlines the proposed MySQL database schema design for the "Fina" module, adhering to the modular ERP architecture. Fina's tables will reside within the primary ERP database and use the `fina_` prefix.

## 1. General Principles

*   **Prefixing:** All Fina-specific tables will be prefixed with `fina_`.
*   **Modularity:** The design aims for Fina to be as self-contained as possible, allowing it to be added or (conceptually) removed with minimal impact on other modules or the core database structure.
*   **Data Integrity:** Foreign keys will be used extensively for relationships *within* the Fina module's tables and for links to stable `core_` tables.
*   **Shared Data Linkage:** Links to master data potentially managed by other modules (e.g., Business Partners, Materials) will be via stored IDs, not direct database foreign key constraints to those other optional modules' tables.
*   **Auditability:** Key transactional tables will include timestamps (`created_at`, `updated_at`) and user references (`created_by_user_id`, `updated_by_user_id`) linking to `core_users`.

## 2. Core Financial Structures & Master Data

These tables form the backbone of the financial system.

*   **`fina_company_codes`**
    *   `id` (PK)
    *   `code` (UK, e.g., "1000")
    *   `name`
    *   `country_code` (FK to `core_countries` if exists, or stores ISO code)
    *   `local_currency_code` (FK to `fina_currencies`)
    *   `chart_of_accounts_id` (FK to `fina_charts_of_accounts`)
    *   `fiscal_year_variant_id` (FK to `fina_fiscal_year_variants`)
    *   `default_tax_country_code`
    *   ... (other company code specific settings)

*   **`fina_charts_of_accounts`**
    *   `id` (PK)
    *   `code` (UK)
    *   `name`
    *   `language_key`
    *   `length_gl_account_number`

*   **`fina_gl_accounts`** (General Ledger Accounts)
    *   `id` (PK)
    *   `chart_of_accounts_id` (FK)
    *   `account_number` (UK within CoA)
    *   `name`
    *   `account_type` (ENUM: 'Balance Sheet', 'P&L')
    *   `gl_account_group_id` (FK to `fina_gl_account_groups`)
    *   `is_reconciliation_account_for` (ENUM: 'Vendor', 'Customer', 'Asset', NULL)
    *   `tax_category_id` (FK, optional)
    *   `is_balance_only_in_local_currency` (Boolean)
    *   `is_open_item_managed` (Boolean)
    *   `sort_key`
    *   ... (other attributes like functional area, controlling integration flags)

*   **`fina_gl_account_groups`**
    *   `id` (PK)
    *   `chart_of_accounts_id` (FK)
    *   `group_code` (UK within CoA)
    *   `name`
    *   `from_account_number`
    *   `to_account_number`

*   **`fina_fiscal_year_variants`**
    *   `id` (PK)
    *   `code` (UK)
    *   `name`
    *   `number_of_posting_periods`
    *   `number_of_special_periods`
    *   `is_year_dependent` (Boolean)

*   **`fina_financial_periods`** (Defines start/end dates for periods within a fiscal year variant)
    *   `id` (PK)
    *   `fiscal_year_variant_id` (FK)
    *   `year`
    *   `period` (1-16, for example)
    *   `start_date`
    *   `end_date`
    *   `is_open_for_posting` (Boolean, managed by period control transactions)

*   **`fina_currencies`** (May be a `core_currencies` table if used globally)
    *   `code` (PK, ISO 4217, e.g., "USD")
    *   `name`
    *   `symbol`
    *   `decimal_places`

*   **`fina_exchange_rate_types`** (e.g., 'M' for Average, 'B' for Buying, 'G' for Selling)
    *   `id` (PK)
    *   `code` (UK)
    *   `name`

*   **`fina_exchange_rates`**
    *   `id` (PK)
    *   `rate_type_id` (FK)
    *   `from_currency_code` (FK to `fina_currencies`)
    *   `to_currency_code` (FK to `fina_currencies`)
    *   `valid_from_date`
    *   `exchange_rate` (Decimal)
    *   `ratio_from` (Integer, for indirect quotes)
    *   `ratio_to` (Integer, for indirect quotes)

*   **`fina_tax_codes`**
    *   `id` (PK)
    *   `country_code` (relevant tax jurisdiction)
    *   `tax_code` (UK within country)
    *   `description`
    *   `tax_type` (e.g., Input, Output)
    *   `tax_rate_percentage` (Decimal)
    *   `gl_account_id_for_posting` (FK to `fina_gl_accounts`)

## 3. Financial Accounting (FI) Tables

### 3.1. General Ledger (GL)
*   **`fina_gl_document_headers`**
    *   `id` (PK)
    *   `company_code_id` (FK)
    *   `document_number` (UK within company_code/year, can be assigned by number range)
    *   `fiscal_year`
    *   `document_type` (e.g., 'SA' for GL, 'KR' for Vendor Invoice, 'RE' for Customer Invoice)
    *   `document_date`
    *   `posting_date`
    *   `reference_text`
    *   `header_text`
    *   `transaction_currency_code` (FK)
    *   `created_by_user_id` (FK to `core_users`)
    *   `created_at`, `updated_at`

*   **`fina_gl_document_items`**
    *   `id` (PK)
    *   `document_header_id` (FK)
    *   `item_number` (Integer)
    *   `gl_account_id` (FK)
    *   `posting_type` (ENUM: 'Debit', 'Credit')
    *   `amount_transaction_currency` (Decimal)
    *   `amount_local_currency` (Decimal)
    *   `tax_code_id` (FK, optional)
    *   `tax_amount_local_currency` (Decimal, optional)
    *   `cost_center_id` (FK to `fina_co_cost_centers`, optional)
    *   `internal_order_id` (FK to `fina_co_internal_orders`, optional)
    *   `profit_center_id` (FK to `fina_co_profit_centers`, optional)
    *   `assignment_text`
    *   `item_text`
    *   ... (other CO object assignments like WBS element from PS, profitability segment)

### 3.2. Accounts Payable (AP)
*   **`fina_ap_vendor_financials`** (Extends a potential `core_business_partners` or `core_vendors` table)
    *   `id` (PK)
    *   `vendor_id` (UK, Link to external vendor master ID, e.g., `core_business_partner_id`)
    *   `company_code_id` (FK) - Vendor financial data is company code specific
    *   `reconciliation_gl_account_id` (FK to `fina_gl_accounts`)
    *   `payment_terms_id` (FK to `fina_payment_terms`)
    *   `payment_methods` (JSON or link to separate table)
    *   `dunning_procedure_id` (FK, less common for vendors but possible)
    *   ... (other AP specific fields like tolerance groups)

*   **`fina_ap_invoices_header`** (Links to `fina_gl_document_headers` for GL posting)
    *   `id` (PK)
    *   `gl_document_header_id` (FK, the FI document created)
    *   `vendor_id` (Link to external vendor master ID)
    *   `invoice_number_vendor` (Vendor's reference number)
    *   `invoice_date`
    *   `due_date`
    *   `gross_amount`
    *   `net_amount`
    *   `tax_amount`
    *   `payment_status` (ENUM: 'Open', 'Partially Paid', 'Paid')
    *   `po_number` (Reference to Purchase Order in MM module, stores ID)

*   **`fina_ap_payments_header`** (Links to `fina_gl_document_headers` for GL posting)
    *   `id` (PK)
    *   `gl_document_header_id` (FK, the FI document created for payment)
    *   `payment_run_id` (optional, if part of automatic payment program)
    *   `payment_method_used`
    *   `house_bank_account_id` (FK to `fina_bl_bank_accounts`)

*   **`fina_ap_payment_invoice_links`** (Links payments to invoices)
    *   `payment_header_id` (FK)
    *   `invoice_header_id` (FK)
    *   `cleared_amount`

*   **`fina_payment_terms`**
    *   `id` (PK)
    *   `code` (UK)
    *   `description`
    *   `rules` (JSON or structured fields for days, discount percentages)

### 3.3. Accounts Receivable (AR) - (Structure similar to AP)
*   **`fina_ar_customer_financials`** (Extends `core_business_partners` or `core_customers`)
    *   `id` (PK)
    *   `customer_id` (UK, Link to external customer master ID)
    *   `company_code_id` (FK)
    *   `reconciliation_gl_account_id` (FK)
    *   `payment_terms_id` (FK)
    *   `credit_limit` (Decimal)
    *   `dunning_procedure_id` (FK to `fina_ar_dunning_procedures`)

*   **`fina_ar_invoices_header`** (Links to `fina_gl_document_headers`)
    *   ... (similar to `fina_ap_invoices_header` but for customers, e.g., `sales_order_number` ref)

*   **`fina_ar_receipts_header`** (Links to `fina_gl_document_headers`)
    *   ... (similar to `fina_ap_payments_header` but for incoming payments)

*   **`fina_ar_receipt_invoice_links`**
    *   ... (similar to `fina_ap_payment_invoice_links`)

*   **`fina_ar_dunning_procedures`**
    *   `id` (PK)
    *   `code` (UK)
    *   `description`
    *   `dunning_levels` (JSON or link to `fina_ar_dunning_levels` table)

### 3.4. Asset Accounting (AA)
*   **`fina_aa_asset_classes`**
    *   `id` (PK)
    *   `code` (UK)
    *   `name`
    *   `gl_account_determination_key` (links to rules for GL account posting)

*   **`fina_aa_asset_master`**
    *   `id` (PK)
    *   `company_code_id` (FK)
    *   `asset_number` (UK within company)
    *   `asset_subnumber` (UK within asset_number)
    *   `description`
    *   `asset_class_id` (FK)
    *   `capitalization_date`
    *   `cost_center_id` (FK, responsible cost center)
    *   `quantity` (optional)
    *   `unit_of_measure` (optional)
    *   `vendor_id` (Link to original vendor, optional)
    *   `status` (ENUM: 'Active', 'Retired', 'UnderConstruction')

*   **`fina_aa_asset_values`** (Stores values per depreciation area)
    *   `id` (PK)
    *   `asset_master_id` (FK)
    *   `depreciation_area_id` (FK to `fina_aa_depreciation_areas`)
    *   `fiscal_year`
    *   `acquisition_cost`
    *   `accumulated_depreciation`
    *   `planned_depreciation_for_year`
    *   `net_book_value`

*   **`fina_aa_depreciation_areas`**
    *   `id` (PK)
    *   `code` (UK, e.g., '01' for Book, '15' for Tax)
    *   `name`
    *   `posts_to_gl` (Boolean)
    *   `depreciation_method_key`

*   **`fina_aa_asset_transactions`** (Links to `fina_gl_document_headers`)
    *   `id` (PK)
    *   `gl_document_header_id` (FK)
    *   `asset_master_id` (FK)
    *   `transaction_type` (ENUM: 'Acquisition', 'Retirement', 'Transfer', 'DepreciationRun')
    *   `amount`
    *   `posting_date`

### 3.5. Bank Accounting (BL)
*   **`fina_bl_house_banks`**
    *   `id` (PK)
    *   `company_code_id` (FK)
    *   `house_bank_code` (UK)
    *   `bank_master_id` (Link to a potential `core_banks` table)

*   **`fina_bl_bank_accounts`**
    *   `id` (PK)
    *   `house_bank_id` (FK)
    *   `account_identifier` (e.g., current account, savings)
    *   `account_number_iban`
    *   `currency_code` (FK)
    *   `gl_account_id` (FK, main bank account in GL)

*   **`fina_bl_bank_statement_headers`**
    *   `id` (PK)
    *   `bank_account_id` (FK)
    *   `statement_number`
    *   `statement_date`
    *   `opening_balance`
    *   `closing_balance`
    *   `status` (ENUM: 'Uploaded', 'Processing', 'Reconciled')

*   **`fina_bl_bank_statement_items`**
    *   `id` (PK)
    *   `statement_header_id` (FK)
    *   `transaction_date`
    *   `amount`
    *   `description`
    *   `is_reconciled` (Boolean)
    *   `gl_document_item_id_cleared` (FK, if reconciled)

## 4. Controlling (CO) Tables

### 4.1. Cost Element Accounting (CEL)
*   (Primary cost elements are `fina_gl_accounts` of P&L type. Secondary cost elements might be a separate table or a type within `fina_gl_accounts` if CoA is shared, though typically distinct).
*   **`fina_co_secondary_cost_elements`**
    *   `id` (PK)
    *   `cost_element_number` (UK, within controlling area)
    *   `name`
    *   `category` (e.g., 'Assessment', 'InternalActivityAllocation')

### 4.2. Cost Center Accounting (CCA)
*   **`fina_co_controlling_areas`** (A CO organizational unit, can span multiple company codes if they use the same CoA and Fiscal Year Variant)
    *   `id` (PK)
    *   `code` (UK)
    *   `name`
    *   `currency_code` (FK)

*   **`fina_co_cost_centers_master`**
    *   `id` (PK)
    *   `controlling_area_id` (FK)
    *   `cost_center_code` (UK within controlling area)
    *   `name`
    *   `valid_from_date`, `valid_to_date`
    *   `person_responsible_user_id` (FK to `core_users`)
    *   `hierarchy_node_id` (FK to `fina_co_cost_center_hierarchy_nodes`)
    *   `company_code_id` (FK, for primary assignment)
    *   `profit_center_id` (FK, optional)

*   **`fina_co_cost_center_hierarchy_nodes`** (for standard hierarchy)
    *   `id` (PK)
    *   `controlling_area_id` (FK)
    *   `node_name`
    *   `parent_node_id` (Self-referential FK)

*   **`fina_co_cost_center_plan_data`**
    *   `id` (PK)
    *   `cost_center_id` (FK)
    *   `fiscal_year`
    *   `period`
    *   `cost_element_id` (FK, primary or secondary)
    *   `planned_amount`
    *   `activity_type_id` (FK, optional)
    *   `planned_activity_quantity` (optional)

*   **`fina_co_actual_postings`** (Central table for actual costs/revenues on CO objects)
    *   `id` (PK)
    *   `gl_document_item_id` (FK, if originated from FI)
    *   `controlling_area_id` (FK)
    *   `cost_element_id` (FK)
    *   `amount`
    *   `posting_date`
    *   `co_object_type` (ENUM: 'CostCenter', 'InternalOrder', 'ProductCostCollector', 'ProfitabilitySegment', etc.)
    *   `co_object_id` (The ID of the specific cost center, order, etc.)
    *   `source_co_object_type` (for allocations)
    *   `source_co_object_id` (for allocations)
    *   `activity_type_id` (FK, optional)
    *   `quantity` (optional)

### 4.3. Internal Orders (IO)
*   **`fina_co_internal_order_master`**
    *   `id` (PK)
    *   `controlling_area_id` (FK)
    *   `order_number` (UK)
    *   `description`
    *   `order_type_id` (FK to `fina_co_internal_order_types`)
    *   `responsible_cost_center_id` (FK, optional)
    *   `status` (ENUM: 'Created', 'Released', 'Budgeted', 'Closed', 'Settled')

*   **`fina_co_internal_order_types`**
    *   `id` (PK)
    *   `type_code` (UK)
    *   `description`
    *   `settlement_profile_id` (FK)

*   **`fina_co_internal_order_budget`**
    *   `id` (PK)
    *   `internal_order_id` (FK)
    *   `fiscal_year`
    *   `budget_amount`

*   **`fina_co_io_settlement_rules`**
    *   `id` (PK)
    *   `internal_order_id` (FK)
    *   `receiver_co_object_type`
    *   `receiver_co_object_id`
    *   `percentage` or `amount`

### 4.4. Product Cost Controlling (PC) - Simplified
*   **`fina_co_pc_costing_variants`** (Defines how costing is done)
    *   `id` (PK)
    *   `code` (UK)
    *   `name`
*   **`fina_co_pc_material_cost_estimates`**
    *   `id` (PK)
    *   `material_id` (Link to external Material Master ID)
    *   `costing_variant_id` (FK)
    *   `costing_date`
    *   `total_cost`
    *   `cost_component_split_json` (e.g., material, labor, overhead)
    *   `status` (ENUM: 'New', 'Calculated', 'Marked', 'Released')

### 4.5. Profitability Analysis (CO-PA) - Simplified (Account-Based)
*   (Account-based CO-PA primarily uses `fina_gl_document_items` and `fina_co_actual_postings` with additional `profitability_segment_id`)
*   **`fina_co_pa_profitability_segments`** (Defines unique combinations of characteristics)
    *   `id` (PK)
    *   `characteristic_1_value` (e.g., customer_group_id)
    *   `characteristic_2_value` (e.g., product_group_id)
    *   `characteristic_3_value` (e.g., region_id)
    *   ... (dynamically defined or a fixed set of well-known characteristics)
    *   `hash` (UK, generated from characteristic values for quick lookup)

*   (Actual postings to profitability segments would be recorded in `fina_co_actual_postings` with `co_object_type = 'ProfitabilitySegment'` and `co_object_id` linking to `fina_co_pa_profitability_segments.id`)

### 4.6. Profit Center Accounting (PCA)
*   **`fina_co_profit_centers_master`**
    *   `id` (PK)
    *   `controlling_area_id` (FK)
    *   `profit_center_code` (UK)
    *   `name`
    *   `valid_from_date`, `valid_to_date`
    *   `person_responsible_user_id` (FK to `core_users`)
    *   `hierarchy_node_id` (FK to profit center hierarchy)

*   (Profit center postings are typically derived from FI and other CO postings where the profit center is assigned on the original document or CO object.)

This data model provides a starting point. Each area would require further refinement and detailed column specifications during actual implementation.
