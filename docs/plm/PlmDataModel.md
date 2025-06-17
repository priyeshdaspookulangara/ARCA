# ARCA PLM (Product Lifecycle Management) Module: Data Model Design (MySQL)

This document outlines the proposed MySQL database schema design for the ARCA Product Lifecycle Management (PLM) module. All PLM-specific tables will use the `plm_` prefix. The model supports product data management, BOM structures, change management, document management, and NPI project integration.

## 1. General Principles

*   **Prefixing:** All tables specific to PLM are prefixed with `plm_`.
*   **Modularity:** Links to core ARCA data (materials, users, projects) are via IDs.
*   **Versioning:** Key PLM entities (Items, BOMs, Documents) will have robust versioning.
*   **Auditability:** Standard audit columns (`created_at`, `updated_at`, `created_by_user_id`, `updated_by_user_id`) on major tables.

## 2. Core Product/Item Data (PDM Foundations)

*   **`plm_items`** (Represents a product, part, assembly, component, or even a document if managed as an item)
    *   `id` (PK)
    *   `item_number` (UK, User-friendly PLM specific item ID, e.g., "PART-000123")
    *   `item_type_id` (FK to `plm_item_types`, e.g., Product, Assembly, Component, Software, Document)
    *   `description_short` (VARCHAR)
    *   `description_long` (TEXT, nullable)
    *   `base_unit_of_measure_id` (FK to `core_units_of_measure`, nullable initially)
    *   `current_version_id` (FK to `plm_item_versions` - points to the latest active/released version, nullable)
    *   `status_id` (FK to `plm_item_statuses` - overall status like 'InDesign', 'Released', 'Obsolete')
    *   `owner_user_id` (FK to `core_users` - responsible engineer/designer)
    *   `core_material_id` (FK to `core_materials`, nullable - populated when item is released to manufacturing/procurement)
    *   `created_at`, `updated_at`, `created_by_user_id`, `updated_by_user_id`

*   **`plm_item_versions`** (Manages revisions/versions of an item)
    *   `id` (PK)
    *   `item_id` (FK to `plm_items`)
    *   `version_major` (INT)
    *   `version_minor` (INT)
    *   `version_string` (VARCHAR, e.g., "A.1", "2.0" - auto-generated or manual)
    *   `description` (VARCHAR, version specific description)
    *   `status_id` (FK to `plm_item_version_statuses` - e.g., 'WorkInProgress', 'InReview', 'Released', 'Archived')
    *   `effectivity_date_start` (DATE, nullable)
    *   `effectivity_date_end` (DATE, nullable)
    *   `change_order_id_released_by` (FK to `plm_change_orders`, nullable - ECO that released this version)
    *   `is_latest_released_version` (Boolean, calculated or maintained)
    *   `created_at`, `updated_at`, `created_by_user_id`

*   **`plm_item_types`** (Configurable types of items PLM manages)
    *   `id` (PK)
    *   `type_code` (UK, e.g., "PRODUCT", "COMPONENT_HW", "COMPONENT_SW", "DOCUMENT_SPEC")
    *   `description` (VARCHAR)

*   **`plm_item_statuses`** (Overall status of the item master)
    *   `id` (PK)
    *   `status_code` (UK)
    *   `description`

*   **`plm_item_version_statuses`** (Status of a specific version)
    *   `id` (PK)
    *   `status_code` (UK)
    *   `description`

*   **`plm_item_attributes_eav`** (Example of EAV for flexible attributes, can be JSON in `plm_item_versions` too)
    *   `id` (PK)
    *   `item_version_id` (FK to `plm_item_versions`)
    *   `attribute_name` (VARCHAR, e.g., "Color", "Voltage", "MaterialSpec")
    *   `attribute_value_string` (VARCHAR, nullable)
    *   `attribute_value_numeric` (DECIMAL, nullable)
    *   `attribute_value_date` (DATE, nullable)
    *   `attribute_unit_id` (FK to `core_units_of_measure`, nullable)

## 3. Bill of Material (BOM) Structures

*   **`plm_boms_header`**
    *   `id` (PK)
    *   `parent_item_version_id` (FK to `plm_item_versions` - the assembly this BOM defines)
    *   `bom_type` (ENUM: 'EBOM', 'MBOM', 'SalesBOM' - if needed)
    *   `bom_name` (VARCHAR, e.g., "Default EBOM for Product X Rev A.1")
    *   `version` (INT or VARCHAR, version of the BOM structure itself, distinct from item version)
    *   `status_id` (FK to a `plm_bom_statuses` table - e.g., 'InDesign', 'Released', 'Archived')
    *   `plant_id` (FK to `lscm_plants` or `core_organization_units`, nullable - MBOMs are often plant-specific)
    *   `alternative_bom_code` (VARCHAR, nullable, for multiple MBOMs for same item/plant)
    *   `created_at`, `updated_at`, `created_by_user_id`

*   **`plm_bom_items`**
    *   `id` (PK)
    *   `bom_header_id` (FK to `plm_boms_header`)
    *   `child_item_version_id` (FK to `plm_item_versions` - the component)
    *   `find_number` (VARCHAR, e.g., "0010", "0020" - position in BOM)
    *   `quantity` (Decimal)
    *   `unit_of_measure_id` (FK to `core_units_of_measure`)
    *   `is_phantom` (Boolean, for EBOM/MBOM)
    *   `notes` (TEXT, nullable)
    *   `pp_routing_operation_assignment` (VARCHAR, nullable - for MBOM, which operation consumes this)
    *   `created_at`, `updated_at`

## 4. Change Management (ECR/ECO)

*   **`plm_change_requests`** (ECR - Engineering Change Request)
    *   `id` (PK)
    *   `ecr_number` (UK, system-generated)
    *   `title` (VARCHAR)
    *   `description` (TEXT)
    *   `reason_for_change` (TEXT)
    *   `proposed_solution` (TEXT)
    *   `requester_user_id` (FK to `core_users`)
    *   `status_id` (FK to `plm_change_statuses` - e.g., 'Submitted', 'InReview', 'Approved', 'Rejected', 'ConvertedToECO')
    *   `priority` (ENUM: 'Low', 'Medium', 'High')
    *   `created_at`, `updated_at`

*   **`plm_change_orders`** (ECO - Engineering Change Order)
    *   `id` (PK)
    *   `eco_number` (UK, system-generated)
    *   `ecr_id` (FK to `plm_change_requests`, nullable if ECO created directly)
    *   `title` (VARCHAR)
    *   `description` (TEXT)
    *   `implementation_plan_summary` (TEXT)
    *   `planner_user_id` (FK to `core_users`)
    *   `status_id` (FK to `plm_change_statuses` - e.g., 'Open', 'InProcess', 'PendingValidation', 'Implemented', 'Closed')
    *   `effectivity_date` (DATE, planned date for change to be effective)
    *   `disposition_code_inventory` (ENUM for existing stock: 'UseAsIs', 'Rework', 'Scrap', nullable)
    *   `disposition_code_wip` (ENUM for work in progress, nullable)
    *   `created_at`, `updated_at`

*   **`plm_change_statuses`** (Common for ECR/ECO, or separate status tables)
    *   `id` (PK)
    *   `status_code` (UK)
    *   `description`
    *   `applies_to_type` (ENUM: 'ECR', 'ECO', 'Both')

*   **`plm_change_affected_objects`** (Links ECR/ECO to items, BOMs, documents)
    *   `id` (PK)
    *   `change_document_type` (ENUM: 'ECR', 'ECO')
    *   `change_document_id` (BIGINT UNSIGNED - FK to `plm_change_requests.id` or `plm_change_orders.id`)
    *   `affected_object_type` (ENUM: 'ItemVersion', 'BomHeader', 'DocumentVersion')
    *   `affected_object_id` (BIGINT UNSIGNED - FK to the respective table, e.g., `plm_item_versions.id`)
    *   `action_required` (TEXT, e.g., "Revise to Ver B", "Add component X", "Obsolete")
    *   INDEX (`change_document_type`, `change_document_id`)
    *   INDEX (`affected_object_type`, `affected_object_id`)

*   **`plm_change_workflow_steps`** (To manage approval/task sequence for ECR/ECO)
    *   `id` (PK)
    *   `change_document_type` (ENUM: 'ECR', 'ECO')
    *   `change_document_id` (BIGINT UNSIGNED)
    *   `step_sequence` (INT)
    *   `step_name` (VARCHAR, e.g., "Engineering Review", "Manufacturing Approval", "Implement Design")
    *   `assigned_to_user_id` (FK, or role_id)
    *   `status` (ENUM: 'Pending', 'InProgress', 'Completed', 'Skipped')
    *   `due_date` (DATE, nullable)
    *   `completed_at` (DATETIME, nullable)
    *   `comments` (TEXT)

## 5. Document Management (Product-Related)

*   **`plm_documents_master`** (Metadata for a document - independent of version)
    *   `id` (PK)
    *   `document_number` (UK, system or user-defined)
    *   `document_type_id` (FK to `plm_document_types`)
    *   `title` (VARCHAR)
    *   `description` (TEXT, nullable)
    *   `author_user_id_original` (FK to `core_users`)
    *   `current_version_id` (FK to `plm_document_versions`, nullable)
    *   `status_id` (FK to `plm_document_statuses`)
    *   `created_at`, `updated_at`

*   **`plm_document_versions`**
    *   `id` (PK)
    *   `document_master_id` (FK to `plm_documents_master`)
    *   `version_major` (INT)
    *   `version_minor` (INT)
    *   `version_string` (VARCHAR)
    *   `status_id` (FK to `plm_document_version_statuses` - e.g., 'Draft', 'InReview', 'Released', 'Archived')
    *   `change_description` (TEXT, what changed in this version)
    *   `checked_out_by_user_id` (FK, nullable, if using check-in/out)
    *   `checked_out_at` (DATETIME, nullable)
    *   `created_at`, `updated_at`, `created_by_user_id`

*   **`plm_document_files`** (Actual electronic files associated with a document version)
    *   `id` (PK)
    *   `document_version_id` (FK to `plm_document_versions`)
    *   `file_name_original` (VARCHAR)
    *   `file_path_or_uri` (VARCHAR, points to secure storage or DMS link)
    *   `mime_type` (VARCHAR)
    *   `file_size_bytes` (BIGINT)
    *   `checksum_hash` (VARCHAR, e.g., SHA256, for integrity)
    *   `is_primary_file` (Boolean)
    *   `uploaded_at`, `uploaded_by_user_id`

*   **`plm_document_types`** (e.g., Specification, CADDrawing, TestReport, UserManual)
    *   `id` (PK)
    *   `type_code` (UK)
    *   `description`

*   **`plm_document_statuses` / `plm_document_version_statuses`** (Similar to item statuses)

*   **`plm_document_object_links`** (Links document versions to other PLM/ARCA objects)
    *   `id` (PK)
    *   `document_version_id` (FK)
    *   `linked_object_type` (ENUM: 'ItemVersion', 'BomHeader', 'ECO', 'ECR', 'PsWbsElement', etc.)
    *   `linked_object_id` (BIGINT UNSIGNED)
    *   `link_description` (VARCHAR, optional)
    *   UNIQUE (`document_version_id`, `linked_object_type`, `linked_object_id`)

## 6. NPI Project Integration Links

*   **`plm_npi_ps_links`**
    *   `id` (PK)
    *   `plm_entity_type` (ENUM: 'ItemVersion_DesignPhase', 'ECO_Implementation', 'DocumentVersion_Approval')
    *   `plm_entity_id` (BIGINT UNSIGNED - FK to `plm_item_versions`, `plm_change_orders`, `plm_document_versions` etc.)
    *   `ps_project_definition_id` (FK to `ps_projects_definition`)
    *   `ps_wbs_element_id` (FK to `ps_wbs_elements`, nullable)
    *   `ps_network_activity_id` (FK to `ps_network_activities`, nullable)
    *   `link_type` (VARCHAR, e.g., "ManagesDeliverable", "TracksProgressOf")
    *   `status_sync_direction` (ENUM: 'PLMtoPS', 'PStoPLM', 'Bidirectional', 'None', nullable)

## 7. Collaboration Tools Data

*   **`plm_collaboration_threads`**
    *   `id` (PK)
    *   `related_object_type` (ENUM: 'ItemVersion', 'BomHeader', 'ECO', 'DocumentVersion', etc.)
    *   `related_object_id` (BIGINT UNSIGNED)
    *   `title` (VARCHAR)
    *   `status` (ENUM: 'Open', 'Closed', 'Archived')
    *   `created_at`, `created_by_user_id`

*   **`plm_collaboration_comments`**
    *   `id` (PK)
    *   `thread_id` (FK to `plm_collaboration_threads`)
    *   `comment_text` (TEXT)
    *   `parent_comment_id` (Self-referential FK for replies, nullable)
    *   `created_at`, `created_by_user_id`

*   **`plm_tasks`** (Simple tasks for PLM specific activities not fitting PS)
    *   `id` (PK)
    *   `related_object_type` (ENUM: 'ItemVersion', 'ECO', 'DocumentVersion', etc.)
    *   `related_object_id` (BIGINT UNSIGNED)
    *   `description` (VARCHAR)
    *   `assigned_to_user_id` (FK to `core_users`)
    *   `due_date` (DATE, nullable)
    *   `status` (ENUM: 'Open', 'InProgress', 'Completed', 'Cancelled')
    *   `created_at`, `updated_at`, `created_by_user_id`

This data model forms a comprehensive basis for the PLM module. Indexing and further refinement will be necessary during detailed design.
