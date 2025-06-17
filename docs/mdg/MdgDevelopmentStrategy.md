# ARCA MDG (Master Data Governance) Module: PHP Development & Implementation Strategy

This document outlines the strategy for developing the ARCA Master Data Governance (MDG) module as an independent PHP package within the Laravel-based ARCA ERP. The module's core responsibilities—centralized master data management, workflow automation, data quality enforcement, and data distribution—require a robust, extensible, and maintainable architecture.

## 1. Module Type and Structure

*   **Module Type:** ARCA MDG will be developed as an independent **Laravel package** located in the `modules/MDG/` directory. It will have its own `composer.json` for dependencies and PSR-4 autoloading for `Modules\MDG\`.

*   **High-Level Internal Directory Structure (PSR-4 Autoloading from `modules/MDG/src/`):**
    MDG will be organized by its core functional capabilities and the master data objects it governs. Domain-Driven Design (DDD) principles will be applied.

    ```
    modules/MDG/
    ├── src/
    │   ├── ChangeRequestMgt/ # Managing the lifecycle of master data change requests
    │   │   ├── Application/    # Services (e.g., CreateChangeRequestService, SubmitChangeRequestService)
    │   │   ├── Domain/         # Entities (ChangeRequest), Repositories, Value Objects
    │   │   ├── Infrastructure/   # Eloquent Models for CRs
    │   │   └── Http/           # API Controllers for CR operations
    │   ├── WorkflowEngine/   # Core workflow logic, task management, approvals, state transitions
    │   │   ├── Application/    # WorkflowOrchestrationService, TaskManagementService
    │   │   ├── Domain/         # Entities (WorkflowInstance, WorkflowTask), WorkflowDefinition
    │   │   └── Infrastructure/   # Implementation of workflow state machine (e.g., using Symfony Workflow)
    │   ├── DataQuality/      # Validation rule engine, deduplication services, cleansing workflows
    │   │   ├── Application/    # ValidateStagedDataService, FindPotentialDuplicatesService
    │   │   ├── Domain/         # Entities (DataQualityRule, ValidationResult, DuplicateSet)
    │   │   └── Infrastructure/
    │   ├── Replication/      # Services for distributing approved master data
    │   │   ├── Application/    # ReplicateMasterDataService
    │   │   ├── Domain/         # Entities (ReplicationSubscriber, ReplicationLog)
    │   │   └── Infrastructure/   # Adapters for different replication methods (event, API, batch)
    │   ├── MasterDataObjects/  # Logic specific to each governed master data object type
    │   │   ├── Material/
    │   │   │   ├── Application/  # MaterialGovernanceService (handles CR for materials)
    │   │   │   ├── Domain/       # Entity (MaterialCore - representing mdg_materials_core)
    │   │   │   └── Infrastructure/ # Eloquent Model for MaterialCore
    │   │   ├── BusinessPartner/
    │   │   │   ├── Application/  # BusinessPartnerGovernanceService
    │   │   │   ├── Domain/       # Entity (BusinessPartnerCore)
    │   │   │   └── Infrastructure/
    │   │   ├── FinancialData/  # For GLAccountCore, CostCenterCore etc. if managed similarly
    │   │   │   └── ...
    │   ├── Core/             # MDG's central ServiceProvider, shared base classes, core interfaces
    │   └── SharedKernel/     # Value Objects (e.g., ChangeRequestId, MasterDataObjectId), DTOs
    ├── config/
    │   └── mdg.php           # Module specific configurations
    ├── database/
    │   ├── migrations/
    │   └── seeders/          # For default workflow definitions, DQ rule types, etc.
    ├── resources/
    │   ├── lang/
    │   └── js/               # Vue.js components for MDG UI
    ├── routes/
    │   └── api.php           # Main API routes for MDG operations and administration
    ├── tests/                # Mirroring src structure
    └── composer.json
    ```

## 2. `MdgServiceProvider` Responsibilities

The `Modules\MDG\Core\Providers\MdgServiceProvider` will be central to MDG's operation:

*   **Registration:** Registering `config/mdg.php`, loading migrations, seeders, routes, views (if any admin UIs are Blade-based), and translations.
*   **Service Container Bindings:**
    *   Binding repository interfaces (e.g., `ChangeRequestRepositoryInterface`, `MaterialCoreRepositoryInterface`) to their Eloquent implementations.
    *   Registering application services for each MDG domain (ChangeRequestMgt, WorkflowEngine, DataQuality, Replication, specific MasterDataObjects).
    *   Registering the core Workflow Engine implementation.
    *   Registering Data Validation services and Deduplication services.
*   **Event Listener Registration:** Subscribing MDG listeners to its own internal domain events (e.g., `ChangeRequestApprovedEvent` triggering replication) and potentially to events from other modules if they can initiate MDG review processes.
*   **Asset Publishing:** Making config, migrations, etc., publishable.
*   **Console Command Registration:** For MDG-specific batch jobs (e.g., `mdg:run-batch-deduplication`, `mdg:process-replication-queue`, `mdg:escalate-overdue-workflow-tasks`, `mdg:run-data-quality-checks`).

## 3. Key Development Principles & Patterns

*   **Domain-Driven Design (DDD):**
    *   **Bounded Contexts:** Each major MDG capability (Workflow, Data Quality, Replication) and each governed Master Data Object type can be considered a distinct bounded context or aggregate root.
*   **Repository Pattern & Service Layer:** Standard application.
*   **Workflow Engine / State Machine:**
    *   This is **critical** for MDG. A robust, configurable workflow engine is required to manage the multi-step processes of creating, changing, and approving master data.
    *   Consider using a mature library like `symfony/workflow` or building a flexible state machine pattern.
    *   Workflows should support: defined states, transitions, guards (conditions for transitions), event listeners on transitions, assignment of tasks to roles/users at different steps.
    *   Workflow definitions should be configurable (e.g., via `config/mdg.php` or database-stored definitions).
*   **Strategy Pattern:**
    *   For different data validation rule types (e.g., `RegexValidationStrategy`, `LookupValidationStrategy`).
    *   For different deduplication matching algorithms.
    *   For different data replication methods (e.g., `EventReplicationStrategy`, `BatchApiReplicationStrategy`).
*   **Event-Driven Architecture (EDA):**
    *   **Internal MDG Domain Events:** (e.g., `MdgChangeRequestSubmittedEvent`, `MdgWorkflowTaskAssignedEvent`, `MdgDataValidationPassedEvent`, `MdgMasterDataRecordApprovedForActivationEvent`).
    *   **Integration Events:** As defined in `MdgIntegrationStrategy.md`, MDG will be a major publisher of events like `MdgCustomerMasterActivatedEvent`, `MdgMaterialActivatedEvent`.
*   **Data Mapper Pattern (or similar for Staged Data):**
    *   For mapping data from the flexible `mdg_cr_staged_data` (e.g., JSON) to strongly-typed domain entities for specific master data objects (e.g., `MaterialCore`, `BusinessPartnerCore`) during workflow processing and validation. This ensures domain logic operates on rich objects.
*   **Data Governance Policies as Code (Consideration):** Advanced: some data quality or validation rules could be implemented in a way that they are highly configurable, perhaps even externally defined and dynamically loaded.

## 4. Data Validation & Deduplication Services

*   **`DataQualityService`:**
    *   Responsible for loading and executing defined data quality rules (`mdg_dq_rules`) against staged data within a change request or against existing master data.
    *   Logs results to `mdg_dq_validation_log`.
*   **`DeduplicationService`:**
    *   Provides methods for real-time search/matching during CR creation.
    *   Manages batch deduplication runs, identifies potential duplicates (`mdg_potential_duplicates`), and supports merge/link decisions (which might trigger specific CR workflows).

## 5. Replication Services

*   **`ReplicationService`:**
    *   Triggered when a master data record is fully approved and activated.
    *   Determines subscribers for the given master data object (`mdg_replication_object_config`).
    *   Uses the configured replication method (e.g., dispatches an event, calls a subscriber's API, adds to a batch extract).
    *   Logs replication attempts and status to `mdg_replication_log`.
    *   Handles retries for failed replications.

## 6. Configuration (`config/mdg.php`)

*   The `modules/MDG/config/mdg.php` file will store settings like:
    *   Paths to workflow definitions or direct workflow configurations.
    *   Default data quality rule sets for different master data objects.
    *   Deduplication matching thresholds and default criteria.
    *   Replication settings (e.g., default retry attempts, endpoint configurations for API-based subscribers if not managed elsewhere).
    *   Master data object types governed by MDG and their specific handling service classes.
    *   Feature flags for specific MDG functionalities.

This development strategy focuses on building MDG as a robust, workflow-centric module that can reliably govern critical master data across the ARCA ERP.
