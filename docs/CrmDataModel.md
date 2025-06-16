# "CRM" Module: Data Model Design (MySQL)

This document outlines the proposed MySQL database schema design for the Customer Relationship Management (CRM) module. All CRM-specific tables will use the `crm_` prefix and reside within the primary ERP database, adhering to the modular architecture.

## 1. General Principles

*   **Prefixing:** All tables specific to the CRM module will be prefixed with `crm_`.
*   **Modularity:** The schema is designed to be self-contained for CRM functionalities while allowing clean linkage to core ERP entities or other modules via IDs.
*   **Relationships:** Foreign keys will enforce relationships within CRM data. Links to external module data (e.g., Fina customers, HR employees as users) will be via shared IDs.
*   **Normalization:** Aim for a reasonable level of normalization to reduce data redundancy, but consider denormalization for specific reporting or performance needs where appropriate.
*   **Auditability:** Key tables will include `created_at`, `updated_at`, `created_by_user_id`, `updated_by_user_id` (linking to `core_users`). A generic `crm_audit_log` might also be implemented.
*   **Custom Fields:** A flexible approach for custom fields will be considered (e.g., a JSON column for less structured custom data on main entities, or a dedicated EAV-like structure for more complex custom field requirements).

## 2. Core CRM Entities

### 2.1. Leads
*   **`crm_leads`**
    *   `id` (PK)
    *   `first_name`, `last_name`, `email` (UK), `phone`, `company_name`
    *   `lead_source_id` (FK to `crm_lead_sources`, optional)
    *   `lead_status_id` (FK to `crm_lead_statuses`)
    *   `assigned_to_user_id` (FK to `core_users` - Sales Rep)
    *   `description` (TEXT)
    *   `industry`, `website`, `address_street`, `address_city`, `address_state`, `address_postal_code`, `address_country`
    *   `unqualified_reason_id` (FK to `crm_unqualified_reasons`, if status is unqualified)
    *   `unqualified_reason_notes` (TEXT)
    *   `converted_at` (Timestamp, if converted)
    *   `converted_to_contact_id` (FK to `crm_contacts`, nullable)
    *   `converted_to_account_id` (FK to `crm_accounts`, nullable)
    *   `converted_to_opportunity_id` (FK to `crm_opportunities`, nullable)
    *   `custom_fields_json` (JSON, for flexible custom fields)
    *   `created_at`, `updated_at`, `created_by_user_id`, `updated_by_user_id`

*   **`crm_lead_sources`** (e.g., Website, Referral, Trade Show)
    *   `id` (PK)
    *   `name` (UK)
    *   `description`

*   **`crm_lead_statuses`** (e.g., New, Contacted, Qualified, Unqualified)
    *   `id` (PK)
    *   `name` (UK)
    *   `is_qualified_status` (Boolean)
    *   `is_unqualified_status` (Boolean)

*   **`crm_unqualified_reasons`** (e.g., Not a fit, No budget, Unresponsive)
    *   `id` (PK)
    *   `name` (UK)

### 2.2. Accounts (Companies/Organizations)
*   **`crm_accounts`**
    *   `id` (PK)
    *   `name` (UK)
    *   `account_number` (UK, system-generated or manual)
    *   `website`, `phone_main`
    *   `industry_id` (FK to `crm_industries`, optional)
    *   `account_type_id` (FK to `crm_account_types`, optional, e.g., Customer, Partner, Prospect)
    *   `assigned_to_user_id` (FK to `core_users` - Account Manager)
    *   `billing_address_street`, `billing_address_city`, `billing_address_state`, `billing_address_postal_code`, `billing_address_country`
    *   `shipping_address_street`, `shipping_address_city`, `shipping_address_state`, `shipping_address_postal_code`, `shipping_address_country`
    *   `description` (TEXT)
    *   `number_of_employees`
    *   `annual_revenue` (Decimal)
    *   `fina_customer_id` (Link to Fina's customer entity ID, if applicable for financial integration)
    *   `parent_account_id` (Self-referential FK for hierarchies)
    *   `custom_fields_json` (JSON)
    *   `created_at`, `updated_at`, `created_by_user_id`, `updated_by_user_id`

*   **`crm_industries`**
    *   `id` (PK)
    *   `name` (UK)

*   **`crm_account_types`**
    *   `id` (PK)
    *   `name` (UK)

### 2.3. Contacts (People)
*   **`crm_contacts`**
    *   `id` (PK)
    *   `first_name`, `last_name`
    *   `email_primary` (UK), `email_secondary`
    *   `phone_mobile`, `phone_work`, `phone_home`
    *   `job_title`
    *   `primary_account_id` (FK to `crm_accounts`, optional - primary company)
    *   `assigned_to_user_id` (FK to `core_users`)
    *   `mailing_address_street`, `mailing_address_city`, `mailing_address_state`, `mailing_address_postal_code`, `mailing_address_country`
    *   `description` (TEXT)
    *   `do_not_call` (Boolean), `email_opt_out` (Boolean)
    *   `custom_fields_json` (JSON)
    *   `created_at`, `updated_at`, `created_by_user_id`, `updated_by_user_id`

*   **`crm_account_contact_relations`** (Many-to-many for contacts associated with multiple accounts, or specific roles at accounts)
    *   `id` (PK)
    *   `account_id` (FK to `crm_accounts`)
    *   `contact_id` (FK to `crm_contacts`)
    *   `role_at_account` (VARCHAR, e.g., Decision Maker, Influencer)
    *   PRIMARY KEY (`account_id`, `contact_id`)

### 2.4. Opportunities
*   **`crm_opportunities`**
    *   `id` (PK)
    *   `name` (VARCHAR)
    *   `account_id` (FK to `crm_accounts`)
    *   `primary_contact_id` (FK to `crm_contacts`, optional)
    *   `assigned_to_user_id` (FK to `core_users`)
    *   `sales_stage_id` (FK to `crm_sales_stages`)
    *   `opportunity_type_id` (FK to `crm_opportunity_types`, e.g., New Business, Upsell)
    *   `lead_source_id` (FK to `crm_lead_sources`, optional)
    *   `amount` (Decimal - estimated deal value)
    *   `probability_percent` (Integer, 0-100)
    *   `expected_close_date` (DATE)
    *   `actual_close_date` (DATE, nullable)
    *   `description` (TEXT)
    *   `next_step_notes` (TEXT)
    *   `lost_reason_id` (FK to `crm_lost_reasons`, if stage is Closed Lost)
    *   `lost_reason_notes` (TEXT)
    *   `custom_fields_json` (JSON)
    *   `created_at`, `updated_at`, `created_by_user_id`, `updated_by_user_id`

*   **`crm_sales_pipelines`** (If multiple pipelines are supported)
    *   `id` (PK)
    *   `name` (UK)
    *   `is_default` (Boolean)

*   **`crm_sales_stages`** (e.g., Prospecting, Qualification, Proposal)
    *   `id` (PK)
    *   `pipeline_id` (FK to `crm_sales_pipelines`, if multiple pipelines)
    *   `name` (VARCHAR)
    *   `sort_order` (Integer)
    *   `probability_percent_default` (Integer)
    *   `is_won_stage` (Boolean)
    *   `is_lost_stage` (Boolean)

*   **`crm_opportunity_types`**
    *   `id` (PK)
    *   `name` (UK)

*   **`crm_lost_reasons`** (e.g., Price, Competition, No Decision)
    *   `id` (PK)
    *   `name` (UK)

*   **`crm_opportunity_contact_roles`** (Many-to-many for contacts involved in an opportunity)
    *   `id` (PK)
    *   `opportunity_id` (FK)
    *   `contact_id` (FK)
    *   `role_in_opportunity` (VARCHAR, e.g., Champion, Evaluator)

### 2.5. Activities
*   **`crm_activities`** (A unified activity table)
    *   `id` (PK)
    *   `activity_type` (ENUM: 'Call', 'Email', 'Meeting', 'Task', 'Note')
    *   `subject` (VARCHAR)
    *   `description` (TEXT)
    *   `start_datetime` (DATETIME, for scheduled activities)
    *   `end_datetime` (DATETIME, for scheduled activities)
    *   `due_date` (DATE, for tasks)
    *   `status` (VARCHAR, e.g., 'Planned', 'Held', 'Not Held' for calls/meetings; 'Open', 'In Progress', 'Completed' for tasks)
    *   `priority` (VARCHAR, e.g., 'High', 'Medium', 'Low')
    *   `assigned_to_user_id` (FK to `core_users`)
    *   `related_to_entity_type` (VARCHAR, e.g., 'Lead', 'Account', 'Contact', 'Opportunity', 'Case')
    *   `related_to_entity_id` (BIGINT UNSIGNED)
    *   `outcome` (TEXT, e.g., notes from a call)
    *   `created_at`, `updated_at`, `created_by_user_id`, `updated_by_user_id`
    *   INDEX (`related_to_entity_type`, `related_to_entity_id`)

## 3. Sales Force Automation (SFA) Specific Tables

*   **`crm_sfa_quotes`**
    *   `id` (PK)
    *   `quote_number` (UK)
    *   `opportunity_id` (FK to `crm_opportunities`, optional)
    *   `account_id` (FK to `crm_accounts`)
    *   `contact_id` (FK to `crm_contacts`, optional)
    *   `prepared_by_user_id` (FK to `core_users`)
    *   `valid_from_date`, `valid_until_date`
    *   `status` (VARCHAR, e.g., 'Draft', 'Presented', 'Accepted', 'Rejected')
    *   `subtotal_amount` (Decimal)
    *   `discount_amount` (Decimal)
    *   `tax_amount` (Decimal)
    *   `total_amount` (Decimal)
    *   `terms_and_conditions` (TEXT)
    *   `created_at`, `updated_at`

*   **`crm_sfa_quote_items`**
    *   `id` (PK)
    *   `quote_id` (FK to `crm_sfa_quotes`)
    *   `product_id` (Link to a shared Product module's ID or stores product name/SKU if no product module)
    *   `product_name_snapshot` (VARCHAR)
    *   `description` (TEXT)
    *   `quantity` (Decimal)
    *   `unit_price` (Decimal)
    *   `discount_percent` (Decimal)
    *   `line_total` (Decimal)

*   **`crm_sfa_sales_orders`** (If CRM handles basic order entry)
    *   (Similar structure to `crm_sfa_quotes` but for confirmed orders, status like 'Pending Fulfillment', 'Fulfilled', 'Billed')
    *   `fina_sales_order_id` (Link to Fina's sales order ID, if applicable)

*   **`crm_sfa_sales_order_items`**
    *   (Similar structure to `crm_sfa_quote_items`)

*   **`crm_sfa_territories`**
    *   `id` (PK)
    *   `name` (UK)
    *   `parent_territory_id` (Self-referential FK)
    *   `description` (TEXT)

*   **`crm_sfa_territory_rules`** (Defines criteria for territory assignment, e.g., based on postal code, industry. Can be complex.)
    *   `id` (PK)
    *   `territory_id` (FK)
    *   `rule_field` (e.g., 'address_postal_code', 'industry_id')
    *   `rule_operator` (e.g., 'EQUALS', 'STARTS_WITH', 'IN_LIST')
    *   `rule_value` (TEXT)

*   **`crm_sfa_account_territories`** (Many-to-many linking accounts to territories, if not solely rule-based)
    *   `account_id` (FK)
    *   `territory_id` (FK)
    *   PRIMARY KEY (`account_id`, `territory_id`)

*   **`crm_sfa_user_territories`** (Many-to-many linking sales reps to territories)
    *   `user_id` (FK to `core_users`)
    *   `territory_id` (FK)
    *   PRIMARY KEY (`user_id`, `territory_id`)

*   **`crm_sfa_quotas`**
    *   `id` (PK)
    *   `assigned_to_user_id` (FK to `core_users`, for individual quota) OR `territory_id` (FK, for territory quota)
    *   `period_start_date` (DATE)
    *   `period_end_date` (DATE)
    *   `quota_amount` (Decimal)
    *   `quota_type` (VARCHAR, e.g., 'Revenue', 'UnitsSold')
    *   `created_at`, `updated_at`

## 4. Marketing Automation (Basic) Specific Tables

*   **`crm_mkt_campaigns`**
    *   `id` (PK)
    *   `name` (VARCHAR)
    *   `campaign_type_id` (FK to `crm_mkt_campaign_types`, e.g., Email, Webinar, Event)
    *   `status` (VARCHAR, e.g., 'Planned', 'Active', 'Completed', 'Cancelled')
    *   `start_date`, `end_date`
    *   `budgeted_cost` (Decimal)
    *   `actual_cost` (Decimal)
    *   `expected_response_count`
    *   `expected_revenue` (Decimal)
    *   `description` (TEXT)
    *   `owner_user_id` (FK to `core_users`)
    *   `created_at`, `updated_at`

*   **`crm_mkt_campaign_types`**
    *   `id` (PK)
    *   `name` (UK)

*   **`crm_mkt_target_lists`** (Segments)
    *   `id` (PK)
    *   `name` (VARCHAR)
    *   `description` (TEXT)
    *   `type` (ENUM: 'Static', 'Dynamic' - dynamic lists are based on saved criteria)
    *   `criteria_json` (JSON, for dynamic lists)
    *   `created_at`, `updated_at`

*   **`crm_mkt_target_list_members`** (Many-to-many for static lists)
    *   `target_list_id` (FK)
    *   `member_entity_type` (ENUM: 'Contact', 'Lead', 'Account')
    *   `member_entity_id` (BIGINT UNSIGNED)
    *   PRIMARY KEY (`target_list_id`, `member_entity_type`, `member_entity_id`)

*   **`crm_mkt_campaign_target_lists`** (Link campaigns to target lists)
    *   `campaign_id` (FK)
    *   `target_list_id` (FK)
    *   PRIMARY KEY (`campaign_id`, `target_list_id`)

*   **`crm_mkt_campaign_responses`** (Track responses, e.g., email open, click, event attendance)
    *   `id` (PK)
    *   `campaign_id` (FK)
    *   `related_entity_type` (ENUM: 'Contact', 'Lead')
    *   `related_entity_id` (BIGINT UNSIGNED)
    *   `response_type` (VARCHAR, e.g., 'EmailOpened', 'LinkClicked', 'AttendedEvent')
    *   `response_datetime` (DATETIME)
    *   `details` (TEXT)

## 5. Customer Service & Support Specific Tables

*   **`crm_cs_cases`** (Service Tickets)
    *   `id` (PK)
    *   `case_number` (UK, system-generated)
    *   `subject` (VARCHAR)
    *   `description` (TEXT)
    *   `account_id` (FK to `crm_accounts`, optional)
    *   `contact_id` (FK to `crm_contacts`, optional)
    *   `reported_by_user_id` (FK to `core_users`, if reported by an internal user for a customer)
    *   `assigned_to_user_id` (FK to `core_users` - Service Agent)
    *   `status_id` (FK to `crm_cs_case_statuses`)
    *   `priority_id` (FK to `crm_cs_case_priorities`)
    *   `case_type_id` (FK to `crm_cs_case_types`, e.g., Problem, Question, Request)
    *   `case_origin_id` (FK to `crm_cs_case_origins`, e.g., Email, Phone, Web Portal)
    *   `sla_id` (FK to `crm_cs_slas`, optional)
    *   `resolution_due_datetime` (DATETIME, calculated based on SLA)
    *   `resolution_notes` (TEXT)
    *   `resolved_at` (DATETIME, nullable)
    *   `closed_at` (DATETIME, nullable)
    *   `created_at`, `updated_at`

*   **`crm_cs_case_statuses`** (e.g., New, Open, Pending Customer, Resolved, Closed)
    *   `id` (PK)
    *   `name` (UK)
    *   `is_resolved_status` (Boolean)
    *   `is_closed_status` (Boolean)

*   **`crm_cs_case_priorities`** (e.g., Low, Medium, High, Urgent)
    *   `id` (PK)
    *   `name` (UK)
    *   `sort_order`

*   **`crm_cs_case_types`**
    *   `id` (PK)
    *   `name` (UK)

*   **`crm_cs_case_origins`**
    *   `id` (PK)
    *   `name` (UK)

*   **`crm_cs_case_comments`** (Thread of communication on a case)
    *   `id` (PK)
    *   `case_id` (FK to `crm_cs_cases`)
    *   `comment_text` (TEXT)
    *   `is_internal_note` (Boolean)
    *   `created_by_user_id` (FK to `core_users`, or contact_id if from portal)
    *   `created_at`

*   **`crm_cs_knowledge_base_categories`**
    *   `id` (PK)
    *   `name` (VARCHAR)
    *   `parent_category_id` (Self-referential FK)
    *   `description` (TEXT)

*   **`crm_cs_knowledge_base_articles`**
    *   `id` (PK)
    *   `category_id` (FK to `crm_cs_knowledge_base_categories`)
    *   `title` (VARCHAR)
    *   `content` (LONGTEXT)
    *   `status` (ENUM: 'Draft', 'Published', 'Archived')
    *   `author_user_id` (FK to `core_users`)
    *   `views_count` (INT, default 0)
    *   `created_at`, `updated_at`

*   **`crm_cs_article_attachments`** (If articles can have file attachments)
    *   `id` (PK)
    *   `article_id` (FK)
    *   `file_name`, `file_path`, `mime_type`, `file_size`

*   **`crm_cs_slas`** (Service Level Agreements)
    *   `id` (PK)
    *   `name` (VARCHAR)
    *   `description` (TEXT)
    *   `applies_to_criteria_json` (JSON, e.g., specific customer type, case priority)
    *   `response_time_minutes` (INT)
    *   `resolution_time_minutes` (INT)
    *   `business_hours_id` (FK to a potential `core_business_hours_definitions` table)

## 6. Reporting & Analytics Helper Tables (Potentially)

*   While most reporting will query the operational tables, some aggregated data or snapshot tables might be created for performance for very complex or frequently accessed reports (e.g., `crm_rpt_daily_sales_summary`). This would be an optimization step.

## 7. Audit Trail

*   **`crm_audit_log`**
    *   `id` (PK)
    *   `user_id` (FK to `core_users`, nullable if system action)
    *   `entity_type` (VARCHAR, e.g., 'Account', 'Opportunity')
    *   `entity_id` (BIGINT UNSIGNED)
    *   `action_type` (ENUM: 'Create', 'Update', 'Delete')
    *   `changed_fields_json` (JSON, storing old and new values for changed fields)
    *   `timestamp` (DATETIME)
    *   `ip_address` (VARCHAR, optional)

This data model provides a comprehensive structure for the CRM module. Specific indexing strategies will be crucial for performance and will be determined during implementation based on query patterns.
