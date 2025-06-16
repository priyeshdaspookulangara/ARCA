# "LSCM" Module: PHP Development & Implementation Strategy

This document outlines the strategy for developing the Logistics & Supply Chain Management (LSCM) module as an independent PHP package within the Laravel-based modular ERP system. LSCM is a large module encompassing functionalities from Materials Management (MM), Sales & Distribution (SD), Production Planning (PP), Plant Maintenance (PM), and Quality Management (QM).

## 1. Module Type and Structure

*   **Module Type:** "LSCM" will be developed as an independent **Laravel package** located in the `modules/LSCM/` directory. It will have its own `composer.json` for managing dependencies and autoloading via PSR-4.

*   **High-Level Internal Directory Structure (PSR-4 Autoloading from `modules/LSCM/src/`):**
    To manage its complexity, LSCM will be internally organized by its main sub-components (MM, SD, PP, PM, QM). Domain-Driven Design (DDD) principles will be applied within each of these sub-components.

    ```
    modules/LSCM/
    ├── src/
    │   ├── MM/  # Materials Management
    │   │   ├── Application/  # Services (e.g., CreatePurchaseOrderService, PostGoodsMovementService), Commands, Queries
    │   │   ├── Domain/       # Entities (e.g., PurchaseOrder, GoodsMovement, MaterialStock), Repositories, Value Objects, Domain Events
    │   │   ├── Infrastructure/ # Eloquent Models, Repository Implementations, Event Listeners for MM
    │   │   └── Http/         # API Controllers (e.g., PurchaseOrdersController), Form Requests
    │   ├── SD/  # Sales and Distribution
    │   │   ├── Application/
    │   │   ├── Domain/       # Entities (e.g., SalesOrder, Delivery, BillingDocument)
    │   │   ├── Infrastructure/
    │   │   └── Http/
    │   ├── PP/  # Production Planning
    │   │   ├── Application/
    │   │   ├── Domain/       # Entities (e.g., ProductionOrder, BillOfMaterial, Routing, WorkCenter)
    │   │   ├── Infrastructure/
    │   │   └── Http/
    │   ├── PM/  # Plant Maintenance
    │   │   ├── Application/
    │   │   ├── Domain/       # Entities (e.g., Equipment, MaintenanceOrder, MaintenanceNotification)
    │   │   ├── Infrastructure/
    │   │   └── Http/
    │   ├── QM/  # Quality Management
    │   │   ├── Application/
    │   │   ├── Domain/       # Entities (e.g., InspectionLot, QualityNotification)
    │   │   ├── Infrastructure/
    │   │   └── Http/
    │   ├── Core/   # LSCM's central ServiceProvider, shared LSCM base classes, core LSCM interfaces,
    │   │           # coordination logic between LSCM sub-modules if necessary.
    │   └── SharedKernel/ # Value Objects (e.g., MaterialIdentifier, PlantCode, MovementType), DTOs,
    │                     # or interfaces used across multiple LSCM sub-components.
    ├── config/
    │   └── lscm.php # Module specific configurations, possibly with sub-arrays for MM, SD, etc.
    ├── database/
    │   ├── migrations/
    │   └── seeders/ # For default LSCM data (e.g., movement types, default plant settings if any)
    ├── resources/
    │   ├── lang/
    │   ├── views/  # If LSCM has any Blade views (e.g., for complex reports or specific admin UIs)
    │   └── js/     # Vue.js components for LSCM specific UI
    ├── routes/
    │   ├── api.php # Main API routes for LSCM, potentially including sub-component routes.
    │   └── web.php # Main web routes for LSCM, if any.
    ├── tests/      # Mirroring the src structure for Unit and Feature tests
    │   ├── Unit/
    │   │   ├── MM/
    │   │   ├── SD/
    │   │   └── ...
    │   └── Feature/
    │       ├── MM/
    │       ├── SD/
    │       └── ...
    └── composer.json
    ```

## 2. `LscmServiceProvider` Responsibilities

The primary service provider, likely `Modules\LSCM\Core\Providers\LscmServiceProvider`, will be central to LSCM's integration and operation:

*   **Registration with Laravel:**
    *   Registering `config/lscm.php`.
    *   Loading database migrations and seeders from `modules/LSCM/database/`.
    *   Loading route files (`api.php`, `web.php`). These files might further include route files from sub-component directories (e.g., `loadRoutesFrom(__DIR__.'/../../MM/routes.php');`).
    *   Registering Blade view namespaces and Vue component paths if LSCM has UI elements.
    *   Registering translation files.
*   **Service Container Bindings:**
    *   Binding repository interfaces (e.g., `PurchaseOrderRepositoryInterface`) to their Eloquent implementations for each sub-component.
    *   Registering application services, domain services, command handlers, and query handlers for all LSCM sub-components.
    *   Registering event listeners for internal LSCM events and events from other ERP modules (Fina, HR, CRM).
*   **Asset Publishing:** Making config, migrations, and potentially views/JS assets publishable.
*   **Console Command Registration:** For LSCM-specific batch jobs or utilities (e.g., `lscm:run-mrp`, `lscm:generate-maintenance-orders`, `lscm:archive-old-stock-documents`).
*   **Dynamic Registration of Sub-components (for Enable/Disable):**
    *   The `LscmServiceProvider` can read its configuration (`config/lscm.php`) to determine which sub-components (MM, SD, PP, PM, QM) are enabled.
    *   It can then conditionally register routes, services, event listeners, etc., only for the active sub-components. This allows for a granular level of modularity *within* LSCM itself. For example, a company might only need MM and SD, but not PP, PM, or QM.

## 3. Key Development Principles & Patterns

*   **Domain-Driven Design (DDD):**
    *   **Bounded Contexts:** Each LSCM sub-component (MM, SD, PP, PM, QM) will be treated as a primary bounded context. Further sub-domains within these (e.g., "Procurement" within MM, "OrderFulfillment" within SD) will be identified.
    *   **Ubiquitous Language:** Establish clear and consistent terminology for each bounded context.
    *   **Aggregates, Entities, Value Objects:** Model domain concepts with precision.
*   **Repository Pattern:** Define repository interfaces in the Domain layer of each sub-component, implemented in their Infrastructure layer using Eloquent.
*   **Application Services / Service Layer:** Orchestrate use cases and business logic. These are the primary entry points for API controllers, console commands, and event listeners.
*   **Data Transfer Objects (DTOs):** For structured data exchange with Application Services and for event payloads.
*   **Event-Driven Architecture (EDA):**
    *   **Internal LSCM Events:** Crucial for decoupling logic *between LSCM sub-components*. For example:
        *   A Goods Receipt in MM (`LscmMM_GoodsReceiptPostedEvent`) might be listened to by QM (to create an inspection lot) and PP (to update production order status).
        *   A Sales Order confirmation in SD (`LscmSD_SalesOrderConfirmedEvent`) might trigger demand updates in PP.
    *   **Integration Events:** LSCM will publish events for other ERP modules (Fina, HR, CRM) and subscribe to their events as detailed in `LscmIntegrationStrategy.md`.
*   **Policies & Gates (Authorization):** Use Laravel's authorization features for controlling access to LSCM functionalities and data. Define policies for major entities within each sub-component.
*   **SharedKernel within LSCM:** The `src/SharedKernel/` directory can house value objects (e.g., `MaterialNumber`, `PlantCode`, `StorageLocationCode`, `MovementType`), common DTOs, or interfaces that are used by multiple LSCM sub-components to ensure consistency.

## 4. Configuration (`config/lscm.php`)

*   The central `lscm.php` configuration file will allow administrators to:
    *   Enable/disable specific LSCM sub-components (MM, SD, PP, PM, QM).
    *   Set default values for various LSCM processes (e.g., default order types, MRP parameters, default plant for certain users if not derived).
    *   Configure integration parameters specific to LSCM's interaction with other modules.
    *   Define number range segments for LSCM documents if not handled by a core number range service.

By applying these development strategies, the LSCM module, despite its breadth, can be developed in a structured, maintainable, and modular fashion, aligning with the overall ERP architecture. The ability to enable/disable sub-components within LSCM itself will provide an additional layer of flexibility.
