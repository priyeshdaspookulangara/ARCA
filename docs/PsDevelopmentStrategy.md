# "PS" Module: PHP Development & Implementation Strategy

This document outlines the strategy for developing the Project System (PS) module as an independent PHP package within the Laravel-based modular ERP system. PS is a complex module requiring a structured approach to manage its diverse functionalities and deep integrations.

## 1. Module Type and Structure

*   **Module Type:** "PS" will be developed as an independent **Laravel package** located in the `modules/PS/` directory, with its own `composer.json` and PSR-4 autoloading.

*   **High-Level Internal Directory Structure (PSR-4 Autoloading from `modules/PS/src/`):**
    To manage complexity, PS will be internally organized by its core functional domains. Domain-Driven Design (DDD) principles will guide the structure within these domains.

    ```
    modules/PS/
    ├── src/
    │   ├── Structuring/  # WBS, Network, Activity, Milestone definition & management
    │   │   ├── Application/  # Services (e.g., CreateWbsElementService), Commands, Queries
    │   │   ├── Domain/       # Entities (ProjectDefinition, WbsElement, Network, Activity), Repositories, Value Objects
    │   │   ├── Infrastructure/ # Eloquent Models, Repository Implementations
    │   │   └── Http/         # API Controllers for structuring operations
    │   ├── Scheduling/ # Time Scheduling, Gantt logic, Critical Path, Baselines
    │   │   ├── Application/
    │   │   ├── Domain/       # Entities (Schedule, Baseline), Services (CriticalPathCalculator)
    │   │   └── Infrastructure/
    │   ├── Costing/    # Cost Planning, Budgeting, Links to Actual Costs/Revenues
    │   │   ├── Application/
    │   │   ├── Domain/       # Entities (ProjectBudget, CostPlan), Services (AvailabilityControlService)
    │   │   └── Infrastructure/ # Adapters to Fina for actuals and budget checks
    │   ├── ResourceMgt/ # Resource Planning & Allocation
    │   │   ├── Application/
    │   │   └── Domain/
    │   ├── MaterialMgt/ # Project-specific Material Requirements & MM Integration
    │   │   ├── Application/
    │   │   └── Infrastructure/ # Adapters to LSCM/MM
    │   ├── Execution/  # Progress Confirmation, Status Management, Issue/Risk Mgt
    │   │   ├── Application/
    │   │   └── Domain/
    │   ├── Closing/    # Period-End Closing: Settlement, Result Analysis
    │   │   ├── Application/
    │   │   ├── Domain/       # Entities (SettlementRule, ResultAnalysisData)
    │   │   └── Infrastructure/ # Adapters to Fina for settlement postings
    │   ├── Core/       # PS's central ServiceProvider, shared PS base classes, core PS interfaces,
    │   │               # module bootstrap logic.
    │   └── SharedKernel/ # Value Objects (e.g., ProjectDate, CostAmount), DTOs,
    │                     # or interfaces used across multiple PS functional domains.
    ├── config/
    │   └── ps.php # Module specific configurations
    ├── database/
    │   ├── migrations/
    │   └── seeders/ # For default data like project profiles, status profiles etc.
    ├── resources/
    │   ├── lang/
    │   ├── views/  # If PS has any Blade views
    │   └── js/     # Vue.js components for PS specific UI
    ├── routes/
    │   ├── api.php # Main API routes for PS
    │   └── web.php # Main web routes for PS
    ├── tests/      # Mirroring the src structure for Unit and Feature tests
    │   └── ...
    └── composer.json
    ```

## 2. `PsServiceProvider` Responsibilities

The primary service provider, `Modules\PS\Core\Providers\PsServiceProvider`, will be key:

*   **Registration:** Registering `config/ps.php`, loading migrations, seeders, routes (`api.php`, `web.php`), views, and translations.
*   **Service Container Bindings:**
    *   Binding repository interfaces (e.g., `WbsElementRepositoryInterface`) to Eloquent implementations for each PS domain.
    *   Registering application services, domain services, command handlers, query handlers.
    *   Registering integration adapters/connectors (e.g., `FinaBudgetCheckerAdapter`, `LscmPurchaseRequisitionAdapter`).
*   **Event Listener Registration:** Subscribing PS listeners to its own domain events and relevant events from Fina, LSCM, HR.
*   **Asset Publishing:** Making config, migrations, etc., publishable.
*   **Console Command Registration:** For PS-specific tasks (e.g., `ps:schedule-projects --project=ID`, `ps:run-period-end-settlement`, `ps:calculate-result-analysis`).

## 3. Key Development Principles & Patterns

*   **Domain-Driven Design (DDD):**
    *   **Bounded Contexts:** Each major functional area within PS (Structuring, Scheduling, Costing, Execution, Closing) will be treated as a distinct bounded context with its own specific models and language.
    *   **Aggregates, Entities, Value Objects:** Model domain concepts like `ProjectDefinition`, `WbsElement`, `NetworkActivity`, `ProjectBudget` with precision.
*   **Repository Pattern:** For abstracting data persistence.
*   **Application Services / Service Layer:** To orchestrate use cases (e.g., `CreateProjectService`, `ConfirmActivityProgressService`, `SettleProjectCostsService`).
*   **Data Transfer Objects (DTOs):** For clear data contracts with Application Services and for event payloads.
*   **Event-Driven Architecture (EDA):**
    *   **Internal PS Domain Events:** (e.g., `ProjectReleasedEvent`, `WbsBudgetUpdatedEvent`, `ActivityConfirmedEvent`, `MilestoneCompletedEvent`). These decouple logic *within* the PS module (e.g., releasing a project might trigger budget activation).
    *   **Integration Events:** PS will publish and subscribe to events as detailed in `PsIntegrationStrategy.md`.
*   **Strategy Pattern:** Could be used for:
    *   Different project scheduling algorithms (if multiple are supported).
    *   Various cost planning methods.
    *   Different settlement rule types or result analysis methods.
*   **State Pattern:** To manage the complex lifecycle (statuses) of `ProjectDefinition`, `WbsElement`, and `NetworkActivity` entities and control allowed transitions and operations based on state.
*   **Facade Pattern (Optional):** The `Modules\PS\Core` might offer a simplified Facade for other modules to perform common high-level PS operations, abstracting the internal domain services if needed.

## 4. Integration Logic Implementation

*   **Adapters/Connectors:** Logic for interacting with other modules (Fina, LSCM, HR) will often reside in "Adapter" or "Connector" classes within the `Infrastructure` layer of the relevant PS domain.
    *   Example: `Modules\PS\Costing\Infrastructure\FinaBudgetAdapter` might implement an interface `BudgetAvailabilityCheckerInterface` from PS Costing Domain, and internally call Fina's budget services.
    *   Example: `Modules\PS\MaterialMgt\Infrastructure\LscmPurchaseRequisitionAdapter` would be responsible for calling LSCM MM services to create PRs.
*   **Event Listeners:** PS listeners for events from other modules will translate those events into PS commands or actions. Example: A `FinaActualCostPostedEvent` listener in PS might trigger an update to a WBS element's actual cost tracking.

## 5. Configuration (`config/ps.php`)

*   The `modules/PS/config/ps.php` file will store PS-specific settings:
    *   Default project profiles, network profiles, control keys.
    *   Parameters for scheduling engine (e.g., default calendar).
    *   Configuration for budget availability control tolerance limits specific to projects.
    *   Default settlement profiles or result analysis keys.
    *   Feature flags for enabling/disabling specific PS sub-features or integration points.

This development strategy aims to manage the inherent complexity of the Project System module by promoting a clear separation of concerns, leveraging established design patterns, and ensuring robust, maintainable code that aligns with the ERP's modular architecture.
