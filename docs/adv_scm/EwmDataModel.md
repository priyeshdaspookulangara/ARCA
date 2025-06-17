# ARCA EWM (Extended Warehouse Management) Module: Data Model Design (MySQL)

This document outlines the proposed MySQL database schema for the ARCA Extended Warehouse Management (EWM) module. All EWM-specific tables will use the `ewm_` prefix. The design supports granular inventory management, complex warehouse processes, and integration with other ARCA modules.

## 1. General Principles

*   **Prefixing:** All tables specific to EWM are prefixed with `ewm_`.
*   **Modularity:** While detailed, the EWM schema links to core ARCA data (materials, business partners, plants) via IDs, maintaining modularity.
*   **Performance:** Indexing will be critical due to high transaction volumes. This document outlines key tables; specific indexes will be defined during implementation.
*   **Auditability:** Key transactional tables will include standard audit columns (`created_at`, `updated_at`, `created_by_user_id`).

## 2. Warehouse Structure (Physical & Logical Layout)

*   **`ewm_warehouses`** (Top-level warehouse identifier)
    *   `id` (PK)
    *   `warehouse_number` (UK, e.g., "WH001")
    *   `description` (VARCHAR)
    *   `lscm_plant_id` (FK to `lscm_plants` or `core_organization_units` representing a plant)
    *   `created_at`, `updated_at`

*   **`ewm_storage_types`** (Sections within a warehouse, e.g., High Rack, Bulk, GR Zone, GI Zone)
    *   `id` (PK)
    *   `warehouse_id` (FK to `ewm_warehouses`)
    *   `storage_type_code` (UK within warehouse, e.g., "RACK", "BLK01")
    *   `description` (VARCHAR)
    *   `putaway_strategy` (ENUM: 'FixedBin', 'EmptyBin', 'AdditionToStock', 'Manual', etc.)
    *   `picking_strategy` (ENUM: 'FIFO', 'LIFO', 'PartialQuantityFirst', etc.)
    *   `mixed_storage_allowed` (Boolean)
    *   `addition_to_stock_allowed` (Boolean)
    *   `capacity_check_method` (ENUM: 'None', 'Weight', 'Volume', 'QuantityBased', nullable)
    *   `created_at`, `updated_at`

*   **`ewm_storage_sections`** (Optional subdivision of a storage type, e.g., Aisle A, Fast-Moving Section)
    *   `id` (PK)
    *   `storage_type_id` (FK to `ewm_storage_types`)
    *   `section_code` (UK within storage type)
    *   `description` (VARCHAR)
    *   `created_at`, `updated_at`

*   **`ewm_storage_bins`** (Most granular location)
    *   `id` (PK)
    *   `storage_type_id` (FK to `ewm_storage_types`)
    *   `storage_section_id` (FK to `ewm_storage_sections`, nullable)
    *   `bin_code` (UK within storage type, e.g., "A-01-01-A")
    *   `description` (VARCHAR, optional)
    *   `bin_type_id` (FK to `ewm_storage_bin_types`, optional, e.g., Pallet Bin, Shelf Bin)
    *   `max_weight` (Decimal, nullable), `weight_unit_id` (FK, nullable)
    *   `max_volume` (Decimal, nullable), `volume_unit_id` (FK, nullable)
    *   `x_coordinate`, `y_coordinate`, `z_coordinate` (Decimal, for physical location/optimization)
    *   `is_blocked_for_putaway` (Boolean), `is_blocked_for_picking` (Boolean)
    *   `last_physical_inventory_date` (DATE, nullable)
    *   `created_at`, `updated_at`

*   **`ewm_storage_bin_types`**
    *   `id` (PK)
    *   `type_code` (UK)
    *   `description`
    *   `max_dimensions_json` (JSON for length/width/height if type implies size)

*   **`ewm_activity_areas`** (Logical grouping of bins for activities like picking, putaway)
    *   `id` (PK)
    *   `warehouse_id` (FK)
    *   `activity_area_code` (UK)
    *   `description`
    *   `activity_type` (ENUM: 'Picking', 'Putaway', 'Replenishment', 'PhysicalInventory')

*   **`ewm_activity_area_bins`** (Assigns bins to activity areas - ManyToMany)
    *   `activity_area_id` (FK)
    *   `storage_bin_id` (FK)
    *   PRIMARY KEY (`activity_area_id`, `storage_bin_id`)

*   **`ewm_doors`** (Loading/Unloading Doors)
    *   `id` (PK)
    *   `warehouse_id` (FK)
    *   `door_code` (UK)
    *   `direction` (ENUM: 'Inbound', 'Outbound', 'Both')
    *   `staging_area_id_default_inbound` (FK to `ewm_staging_areas`, nullable)
    *   `staging_area_id_default_outbound` (FK to `ewm_staging_areas`, nullable)

*   **`ewm_staging_areas`**
    *   `id` (PK)
    *   `warehouse_id` (FK)
    *   `staging_area_code` (UK)
    *   `description`
    *   `direction` (ENUM: 'Inbound', 'Outbound', 'CrossDocking')

*   **`ewm_work_centers`** (Physical locations for packing, VAS, deconsolidation, QI)
    *   `id` (PK)
    *   `warehouse_id` (FK)
    *   `work_center_code` (UK)
    *   `description`
    *   `storage_type_id_inbound` (FK, where materials arrive)
    *   `storage_type_id_outbound` (FK, where processed materials go)
    *   `supported_vas_activities_json` (JSON array of VAS types)

## 3. Inventory & Stock Management (Quants & Handling Units)

*   **`ewm_quants`** (Stock at bin level with specific characteristics)
    *   `id` (PK)
    *   `storage_bin_id` (FK to `ewm_storage_bins`)
    *   `handling_unit_id` (FK to `ewm_handling_units`, nullable if stock is loose)
    *   `core_material_id` (FK to `core_materials`)
    *   `batch_number` (VARCHAR, nullable)
    *   `serial_number` (VARCHAR, nullable, if material is serialized at individual piece level here)
    *   `quantity` (Decimal)
    *   `base_unit_of_measure_id` (FK to `core_units_of_measure`)
    *   `stock_type` (ENUM: 'Unrestricted', 'QualityInspection', 'Blocked', 'Returns')
    *   `owner_core_business_partner_id` (FK, for consignment or different plant stock, nullable)
    *   `goods_receipt_date` (DATE, for FIFO/LIFO)
    *   `shelf_life_expiry_date` (DATE, nullable)
    *   `is_frozen_for_physical_inventory` (Boolean)
    *   `created_at`, `updated_at`
    *   INDEX (`core_material_id`, `storage_bin_id`, `stock_type`)

*   **`ewm_handling_units`** (Pallets, Cartons, Bins containing stock)
    *   `id` (PK)
    *   `hu_identifier` (UK, system or externally assigned barcode/SSCC)
    *   `warehouse_id` (FK)
    *   `current_storage_bin_id` (FK to `ewm_storage_bins`, nullable if in resource/staging)
    *   `current_resource_id` (FK to `ewm_resources`, nullable if on a resource)
    *   `parent_handling_unit_id` (Self-referential FK for nested HUs, nullable)
    *   `hu_type_id` (FK to `ewm_handling_unit_types`)
    *   `status` (ENUM: 'Planned', 'Active', 'Loaded', 'Shipped', 'Empty')
    *   `total_weight`, `weight_unit_id`
    *   `total_volume`, `volume_unit_id`
    *   `closed` (Boolean)
    *   `created_at`, `updated_at`

*   **`ewm_handling_unit_items`** (Content of an HU - links HUs to Quants or other HUs)
    *   `id` (PK)
    *   `handling_unit_id` (FK, the container HU)
    *   `packed_quant_id` (FK to `ewm_quants`, nullable if packing another HU)
    *   `packed_handling_unit_id` (FK to `ewm_handling_units`, nullable if packing quants)
    *   `quantity_packed` (Decimal, if packing quants directly without a separate quant record for the item line)
    *   `core_material_id` (FK, if packing quants directly)
    *   `batch_number` (VARCHAR, if packing quants directly)

*   **`ewm_handling_unit_types`** (e.g., Pallet EUP1, Carton Small)
    *   `id` (PK)
    *   `type_code` (UK)
    *   `description`
    *   `length`, `width`, `height`, `dimension_unit_id`
    *   `max_weight`, `max_volume`

*   **`ewm_serial_numbers_stock`** (If serial numbers are tracked individually at EWM bin level)
    *   `id` (PK)
    *   `core_material_id` (FK)
    *   `serial_number` (VARCHAR)
    *   `current_quant_id` (FK to `ewm_quants`, indicates current location/status)
    *   `status` (ENUM: 'Available', 'InUse', 'Scrapped', 'Shipped')
    *   UNIQUE (`core_material_id`, `serial_number`)

## 4. Warehouse Tasks & Orders (Movement Execution)

*   **`ewm_warehouse_orders`** (Group of tasks for a resource)
    *   `id` (PK)
    *   `warehouse_id` (FK)
    *   `warehouse_order_number` (UK)
    *   `status` (ENUM: 'Pending', 'Active', 'Completed', 'Cancelled')
    *   `assigned_resource_id` (FK to `ewm_resources`, nullable)
    *   `queue_id` (FK to `ewm_queues`, nullable)
    *   `activity_area_id` (FK, area this order primarily pertains to)
    *   `latest_start_datetime` (nullable)
    *   `created_at`, `updated_at`

*   **`ewm_warehouse_tasks`** (Smallest unit of work: move product/HU from A to B)
    *   `id` (PK)
    *   `warehouse_order_id` (FK, nullable if task not yet part of an order)
    *   `warehouse_id` (FK)
    *   `task_type` (ENUM: 'Putaway', 'Picking', 'InternalTransfer', 'Replenishment', 'PhysicalInventoryCount')
    *   `status` (ENUM: 'Open', 'Confirmed', 'Cancelled')
    *   `core_material_id` (FK)
    *   `quantity_to_move` (Decimal), `base_unit_of_measure_id` (FK)
    *   `source_storage_bin_id` (FK), `source_handling_unit_id` (FK, nullable)
    *   `destination_storage_bin_id` (FK), `destination_handling_unit_id` (FK, nullable, e.g. pick HU)
    *   `batch_number` (nullable)
    *   `serial_numbers_json` (JSON array if multiple serials moved in one task)
    *   `reference_document_type` (ENUM: 'InboundDeliveryOrder', 'OutboundDeliveryOrder', 'ProductionOrder', 'PI_Document')
    *   `reference_document_id` (ID of the source document)
    *   `reference_document_item_id`
    *   `confirmed_by_user_id` (FK, nullable)
    *   `confirmed_at` (DATETIME, nullable)
    *   `created_at`, `updated_at`

## 5. Inbound & Outbound Process Documents

*   **`ewm_inbound_delivery_orders`** (EWM's representation of an inbound request)
    *   `id` (PK)
    *   `warehouse_id` (FK)
    *   `external_ido_number` (UK, e.g., from MM Inbound Delivery)
    *   `status` (ENUM: 'Expected', 'PartiallyReceived', 'FullyReceived', 'PutawayComplete')
    *   `expected_arrival_datetime`
    *   `actual_arrival_datetime` (nullable)
    *   `document_date`
    *   `supplier_core_business_partner_id` (FK, nullable)
    *   `source_document_reference` (e.g., MM PO number)

*   **`ewm_inbound_delivery_order_items`**
    *   `id` (PK)
    *   `inbound_delivery_order_id` (FK)
    *   `core_material_id` (FK)
    *   `expected_quantity` (Decimal)
    *   `received_quantity` (Decimal, default 0)
    *   `base_unit_of_measure_id` (FK)
    *   `batch_number` (nullable)
    *   `status` (ENUM: 'Pending', 'PartiallyReceived', 'FullyReceived')

*   **`ewm_outbound_delivery_orders`** (EWM's representation of an outbound request)
    *   `id` (PK)
    *   `warehouse_id` (FK)
    *   `external_odo_number` (UK, e.g., from SD Outbound Delivery)
    *   `status` (ENUM: 'PendingPicking', 'PickingInProgress', 'Picked', 'Packed', 'Staged', 'Loaded', 'GoodsIssued')
    *   `planned_shipping_datetime`
    *   `actual_shipping_datetime` (nullable)
    *   `customer_core_business_partner_id` (FK, nullable)
    *   `ship_to_address_json` (JSON)
    *   `route_id` (FK to TM Routes, if TM integrated)
    *   `carrier_id` (FK to TM Carriers, if TM integrated)

*   **`ewm_outbound_delivery_order_items`**
    *   `id` (PK)
    *   `outbound_delivery_order_id` (FK)
    *   `core_material_id` (FK)
    *   `requested_quantity` (Decimal)
    *   `picked_quantity` (Decimal, default 0)
    *   `base_unit_of_measure_id` (FK)
    *   `batch_number` (nullable)
    *   `picking_strategy_id` (FK, optional)
    *   `status` (ENUM: 'Pending', 'PickingComplete', 'Packed')

*   **`ewm_waves`** (For grouping outbound items for picking)
    *   `id` (PK)
    *   `warehouse_id` (FK)
    *   `wave_number` (UK)
    *   `status` (ENUM: 'Created', 'ReleasedForPicking', 'PickingComplete', 'Closed')
    *   `release_datetime`

*   **`ewm_wave_items`**
    *   `wave_id` (FK)
    *   `outbound_delivery_order_item_id` (FK)
    *   PRIMARY KEY (`wave_id`, `outbound_delivery_order_item_id`)

## 6. Resource Management

*   **`ewm_resources`** (Personnel, Forklifts, RF Devices)
    *   `id` (PK)
    *   `warehouse_id` (FK)
    *   `resource_code` (UK)
    *   `description`
    *   `resource_type_id` (FK to `ewm_resource_types`)
    *   `core_user_id` (FK, if resource is a person and linked to ERP user)
    *   `is_active` (Boolean)
    *   `current_queue_id` (FK to `ewm_queues`, nullable)

*   **`ewm_resource_types`** (e.g., ForkliftDriver, Picker, RFDevice, HighRackForklift)
    *   `id` (PK)
    *   `type_code` (UK)
    *   `description`

*   **`ewm_queues`** (For task assignment to groups of resources)
    *   `id` (PK)
    *   `warehouse_id` (FK)
    *   `queue_code` (UK)
    *   `description`
    *   `priority` (INT)

## 7. Other EWM Specific Tables

*   **`ewm_physical_inventory_documents`**
    *   `id` (PK)
    *   `warehouse_id` (FK)
    *   `document_number` (UK)
    *   `fiscal_year`
    *   `status` (ENUM: 'New', 'CountingActive', 'Counted', 'Posted', 'Cancelled')
    *   `planned_count_date`

*   **`ewm_physical_inventory_items`**
    *   `id` (PK)
    *   `physical_inventory_document_id` (FK)
    *   `storage_bin_id` (FK, if bin specific count)
    *   `core_material_id` (FK, if material specific count)
    *   `handling_unit_id` (FK, if HU count)
    *   `counted_quantity` (Decimal, nullable)
    *   `book_quantity_at_count_time` (Decimal)
    *   `difference_quantity` (Decimal)
    *   `is_recounted` (Boolean)
    *   `posted_at` (DATETIME, nullable)

*   **`ewm_vas_orders`** (Value Added Services)
    *   `id` (PK)
    *   `warehouse_id` (FK)
    *   `vas_order_number` (UK)
    *   `reference_document_type` (e.g., InboundDelivery, OutboundDelivery, Internal)
    *   `reference_document_id`
    *   `core_material_id_target` (FK, material being produced/modified)
    *   `quantity_target` (Decimal)
    *   `status` (ENUM: 'Planned', 'Active', 'Confirmed', 'Cancelled')
    *   `work_center_id` (FK)

*   **`ewm_vas_order_items_components`** (Materials consumed for VAS)
    *   `id` (PK)
    *   `vas_order_id` (FK)
    *   `core_material_id_component` (FK)
    *   `quantity_required` (Decimal)
    *   `quantity_consumed` (Decimal)

*   **`ewm_yard_vehicles`**
    *   `id` (PK)
    *   `vehicle_number` (UK, e.g., license plate)
    *   `vehicle_type` (ENUM: 'Truck', 'Trailer')
    *   `carrier_core_business_partner_id` (FK, optional)

*   **`ewm_yard_movements`**
    *   `id` (PK)
    *   `warehouse_id` (FK)
    *   `yard_vehicle_id` (FK)
    *   `door_id` (FK, nullable)
    *   `parking_spot_id` (FK to `ewm_yard_parking_spots`, nullable)
    *   `movement_type` (ENUM: 'CheckIn', 'MoveToDoor', 'MoveToParking', 'CheckOut')
    *   `timestamp`

This data model provides a detailed foundation for EWM. Further refinement will occur during detailed design and implementation of specific processes.
