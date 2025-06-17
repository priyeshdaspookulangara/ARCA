# ARCA EWM (Extended Warehouse Management) Module: PHP Development & Implementation Strategy

This document outlines the strategy for developing the ARCA Extended Warehouse Management (EWM) module as an independent PHP package within the Laravel-based ARCA ERP. EWM's real-time operational nature and complexity demand a robust and performant development approach.

## 1. Module Type and Structure

*   **Module Type:** ARCA EWM will be developed as an independent **Laravel package** located in the `modules/EWM/` directory, with its own `composer.json` and PSR-4 autoloading for `Modules\EWM\`.

*   **High-Level Internal Directory Structure (PSR-4 Autoloading from `modules/EWM/src/`):**
    EWM will be organized by its core process areas and supporting functions, applying Domain-Driven Design (DDD) principles within these contexts.

    ```
    modules/EWM/
    ├── src/
    │   ├── Inbound/        # Goods Receipt, Putaway, Deconsolidation
    │   │   ├── Application/    # Services (e.g., ProcessInboundDeliveryService, CreatePutawayTasksService)
    │   │   ├── Domain/         # Entities (InboundDeliveryOrder, PutawayTask), Repositories, Strategies (PutawayStrategyInterface)
    │   │   ├── Infrastructure/   # Eloquent Models, Repository Implementations
    │   │   └── Http/           # API Controllers for inbound operations (e.g., for RF or external systems)
    │   ├── Outbound/       # Picking, Packing, Staging, Loading, Wave Management
    │   │   ├── Application/    # Services (e.g., CreatePickingWaveService, ConfirmPackingService)
    │   │   ├── Domain/         # Entities (OutboundDeliveryOrder, PickingTask, Wave, HandlingUnit), Strategies (PickingStrategyInterface)
    │   │   ├── Infrastructure/
    │   │   └── Http/
    │   ├── Internal/       # Stock Transfers, Physical Inventory, Replenishment, Rearrangement
    │   │   ├── Application/
    │   │   ├── Domain/         # Entities (WarehouseTaskInternal, PhysicalInventoryDocument)
    │   │   └── Infrastructure/
    │   ├── StockManagement/  # Core quant management, HU management logic (shared by Inbound, Outbound, Internal)
    │   │   ├── Application/
    │   │   └── Domain/         # Entities (Quant, HandlingUnit)
    │   ├── ResourceMgt/    # Warehouse Resource (labor, equipment) Management & Task Assignment
    │   │   ├── Application/
    │   │   └── Domain/         # Entities (WarehouseResource, WarehouseOrderQueue)
    │   ├── MasterData/     # Management of EWM-specific master data (warehouse structure, bin types, strategies config)
    │   │   ├── Application/
    │   │   └── Domain/         # Entities (StorageBin, StorageType, ActivityArea)
    │   ├── VAS/            # Value-Added Services
    │   ├── YardMgt/        # Yard Management
    │   ├── CrossDocking/   # Cross-Docking logic
    │   ├── Automation/     # Interfaces for Material Flow Systems (MFS)
    │   │   └── Infrastructure/ # Adapters for specific MFS protocols
    │   ├── RF/             # Backend support for RF/Mobile UIs (specialized controllers/services if different from desktop)
    │   │   └── Http/
    │   ├── Core/           # EWM's central ServiceProvider, shared EWM base classes, core EWM interfaces
    │   └── SharedKernel/   # Value Objects (e.g., BinLocation, HuIdentifier), DTOs used across EWM domains
    ├── config/
    │   └── ewm.php
    ├── database/
    │   ├── migrations/
    │   └── seeders/ # For default EWM settings, warehouse structure examples, etc.
    ├── resources/  # For UI components if any are directly served by EWM module (e.g. monitoring dashboards)
    │   ├── lang/
    │   └── js/
    ├── routes/
    │   ├── api.php # Main API routes for EWM (for RF, automation, external systems)
    │   └── web.php # For EWM monitoring dashboards or configuration UIs
    ├── tests/      # Mirroring src structure
    └── composer.json
    ```

## 2. `EwmServiceProvider` Responsibilities

The `Modules\EWM\Core\Providers\EwmServiceProvider` will be crucial for:

*   **Registration:** Registering `config/ewm.php`, loading migrations, seeders, routes, views (if any), and translations.
*   **Service Container Bindings:**
    *   Binding repository interfaces (e.g., `QuantRepositoryInterface`, `WarehouseTaskRepositoryInterface`) to Eloquent implementations for each EWM domain.
    *   Registering application services, domain services, strategy implementations (e.g., specific picking/putaway strategies).
    *   Registering event listeners for internal EWM events and events from MM, SD, QM, PP.
*   **Asset Publishing:** Making config, migrations, etc., publishable.
*   **Console Command Registration:** For EWM-specific batch jobs (e.g., `ewm:process-wave-release`, `ewm:calculate-replenishment`, `ewm:archive-completed-tasks`).

## 3. Key Development Principles & Patterns

*   **Domain-Driven Design (DDD):**
    *   **Bounded Contexts:** Each major EWM process area (Inbound, Outbound, Internal, StockManagement, ResourceMgt) will be treated as a bounded context.
    *   **Aggregates, Entities, Value Objects:** Model concepts like `StorageBin`, `HandlingUnit`, `Quant`, `WarehouseTask`, `WarehouseOrder` precisely.
*   **Repository Pattern & Service Layer:** Standard application of these patterns for data abstraction and use case orchestration.
*   **Event-Driven Architecture (EDA):**
    *   **Internal EWM Domain Events:** (e.g., `EwmHandlingUnitCreatedEvent`, `EwmWarehouseTaskConfirmedEvent`, `EwmStockLevelChangedInBinEvent`). These are critical for decoupling internal EWM processes and maintaining data consistency (e.g., updating aggregated stock views when a quant changes).
    *   **Integration Events:** Publish and subscribe to events as detailed in `EwmIntegrationStrategy.md`.
*   **Strategy Pattern:**
    *   For implementing different putaway strategies (Fixed Bin, Next Empty Bin, etc.).
    *   For different picking strategies (FIFO, LIFO, Wave, Zone).
    *   For replenishment strategies.
*   **State Pattern:** To manage the lifecycle and allowed operations for entities like `WarehouseTask`, `WarehouseOrder`, `HandlingUnit`, `InboundDeliveryOrder`, `OutboundDeliveryOrder` based on their status.
*   **Optimistic/Pessimistic Locking:**
    *   For high-contention resources (e.g., a specific storage bin during concurrent putaway/picking attempts for the same bin, or a resource being assigned tasks), optimistic locking should be the default.
    *   Pessimistic locking might be considered for very short, critical sections if optimistic locking leads to too many retries, but should be used sparingly. Task reservation mechanisms can also help.
*   **Performance & Real-time Processing:**
    *   Code must be highly performant, especially for task creation, confirmation, and inventory (quant) updates.
    *   Minimize database queries; use caching for EWM master data (e.g., bin types, storage types).
    *   Offload any non-critical post-processing to queues (e.g., updating statistics, triggering lower-priority notifications).

## 4. RF Framework / Mobile UI Backend Support

*   If EWM uses dedicated RF (Radio Frequency) mobile devices, the backend must provide:
    *   **Specialized APIs:** Highly responsive, lightweight APIs tailored for RF screen flows. These might be distinct from APIs used for web dashboards.
    *   **Minimal Data Transfer:** Send only data essential for the current RF screen/step.
    *   **Fast Confirmations:** Ensure quick processing of scan confirmations and task updates.
    *   **State Management:** Manage session state for RF users if necessary, or design stateless interactions.
    *   These APIs would reside under `modules/EWM/src/RF/Http/Controllers/`.

## 5. Automation Integration (MFS - Material Flow Systems)

*   Develop services and adapters within `modules/EWM/src/Automation/Infrastructure/` to:
    *   Format and send warehouse tasks to MFS controllers according to their specific protocols/APIs.
    *   Receive and interpret confirmations or error messages from MFS.
    *   Handle communication errors and retries.

## 6. Configuration (`config/ewm.php`)

*   The `modules/EWM/config/ewm.php` file will store EWM-specific settings:
    *   Warehouse structure defaults (if not fully master data driven).
    *   Default putaway/picking strategies per storage type or material group.
    *   Parameters for wave processing, replenishment calculations.
    *   Configuration for RF screen behavior or MFS integration endpoints.
    *   Feature flags for enabling/disabling specific EWM advanced features.

This development strategy is designed to build a robust, performant, and maintainable EWM module capable of handling complex warehouse operations and integrating smoothly with the ARCA ERP.
