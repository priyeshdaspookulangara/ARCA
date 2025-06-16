# "Fina" Module: PHP Development & Implementation Strategy

This document outlines the strategy for developing the "Fina" module as an independent PHP package within the Laravel-based modular ERP system.

## 1. Module Type and Structure

*   **Module Type:** "Fina" will be developed as an independent **Laravel package** located in the `modules/Fina/` directory. This ensures it is self-contained and can be managed via its own `composer.json`.

*   **High-Level Internal Directory Structure (PSR-4 Autoloading from `modules/Fina/src/`):**
    Given the complexity and breadth of Fina (encompassing FI and CO), a structure inspired by Domain-Driven Design (DDD) with clear subdomains is proposed:

    ```
    modules/Fina/
    ├── src/
    │   ├── FI/  # Financial Accounting Subdomain
    │   │   ├── GL/  # General Ledger
    │   │   │   ├── Application/  # Services, Commands, Queries
    │   │   │   ├── Domain/       # Entities (e.g., JournalDocument), Repositories, Value Objects, Domain Events
    │   │   │   ├── Infrastructure/ # Eloquent Models, Repository Implementations, Event Listeners
    │   │   │   └── Http/         # Controllers, API Requests (if Fina exposes direct HTTP endpoints)
    │   │   ├── AP/  # Accounts Payable (similar structure to GL)
    │   │   ├── AR/  # Accounts Receivable (similar structure)
    │   │   ├── AA/  # Asset Accounting (similar structure)
    │   │   └── BL/  # Bank Accounting (similar structure)
    │   ├── CO/  # Controlling Subdomain
    │   │   ├── CCA/ # Cost Center Accounting (similar structure)
    │   │   ├── IO/  # Internal Orders (similar structure)
    │   │   ├── PC/  # Product Costing (similar structure)
    │   │   ├── CEL/ # Cost Element Accounting (may involve shared logic with GL)
    │   │   ├── COPA/# Profitability Analysis (similar structure)
    │   │   └── PCA/ # Profit Center Accounting (similar structure)
    │   ├── Shared/ # Code shared across Fina's sub-modules (e.g., common DTOs, core financial logic, interfaces)
    │   └── Core/   # Fina's central ServiceProvider, bootstrapping logic, potentially core Fina interfaces
    ├── config/
    │   └── fina.php # Module specific configurations
    ├── database/
    │   ├── migrations/
    │   └── seeders/
    ├── resources/
    │   ├── lang/
    │   ├── views/  # If Fina has any direct UI elements not provided by other modules
    │   └── js/     # Vue.js components if applicable
    ├── routes/
    │   ├── api.php # For internal service APIs or external Fina APIs
    │   └── web.php # For any Fina-specific web UI routes
    ├── tests/
    │   ├── Unit/
    │   │   ├── FI/
    │   │   └── CO/
    │   └── Feature/
    │       ├── FI/
    │       └── CO/
    └── composer.json
    ```

## 2. `FinaServiceProvider` Responsibilities

The primary service provider for the module, `Modules\Fina\Core\Providers\FinaServiceProvider` (or similar name), will be responsible for:

*   **Registration with Laravel:**
    *   Registering the module's configuration file (`config/fina.php`) which will be publishable.
    *   Loading the module's database migrations.
    *   Loading and registering the module's route files (`api.php`, `web.php`).
    *   Registering any Blade view namespaces or view composers if Fina has UI elements.
    *   Registering translation files.
*   **Service Container Bindings:**
    *   Binding repository interfaces to their concrete Eloquent implementations (e.g., `JournalDocumentRepositoryInterface` to `EloquentJournalDocumentRepository`).
    *   Registering application services, domain services, and command/query handlers.
    *   Registering factories or value object builders.
*   **Event Listener Registration:**
    *   Subscribing Fina's internal listeners to its own domain events.
    *   Subscribing Fina's listeners to relevant events published by other ERP modules (e.g., `HR_PayrollRunCompletedEvent`, `SD_SalesOrderBilledEvent`).
*   **Asset Publishing:**
    *   Making configuration, migrations, and potentially views/JS assets publishable via `php artisan vendor:publish`.
*   **Console Command Registration:**
    *   Registering any Artisan commands specific to Fina (e.g., `fina:run-depreciation`, `fina:close-period`, `fina:generate-financial-statement`).

## 3. Key Development Principles & Patterns

*   **Domain-Driven Design (DDD):**
    *   **Bounded Contexts:** FI and CO will be treated as major bounded contexts, with sub-modules (GL, AP, CCA, etc.) as further refined contexts or aggregates within them.
    *   **Ubiquitous Language:** Develop a clear language for each Fina subdomain.
    *   **Entities & Value Objects:** Model domain concepts accurately.
    *   **Aggregates:** Define consistency boundaries (e.g., a `JournalDocument` with its `JournalDocumentItems` might be an aggregate).
    *   **Domain Services:** For logic that doesn't naturally fit within an entity.
*   **Repository Pattern:**
    *   Define repository interfaces in the Domain layer (e.g., `Modules\Fina\FI\GL\Domain\Repositories\JournalDocumentRepositoryInterface`).
    *   Implement these interfaces in the Infrastructure layer using Eloquent ORM (e.g., `Modules\Fina\FI\GL\Infrastructure\Persistence\EloquentJournalDocumentRepository`).
*   **Application Services / Service Layer:**
    *   Orchestrate use cases by coordinating domain objects and repositories.
    *   These services will be the primary entry point for interactions from other modules or Fina's own API controllers/event listeners.
    *   Example: `Modules\Fina\FI\GL\Application\PostJournalDocumentService`.
*   **Command Query Responsibility Segregation (CQRS) - Lite:**
    *   For complex areas, consider separating command (write) paths from query (read) paths.
    *   Commands (e.g., `PostJournalDocumentCommand`) are handled by command handlers that change state.
    *   Queries (e.g., `GetFinancialStatementQuery`) are handled by query services that retrieve data, possibly using optimized read models or direct database queries for reporting.
    *   Full Event Sourcing is likely too complex initially but using events to signal state changes is encouraged.
*   **Data Transfer Objects (DTOs):**
    *   Use DTOs for passing data to Application Services (Commands/Queries) and for returning data from them, ensuring clear contracts.
*   **Dependency Injection:**
    *   Leverage Laravel's service container for constructor and method injection throughout the Fina module.
*   **Event-Driven Architecture (Internal & External):**
    *   **Internal Domain Events:** Use events within Fina to decouple logic between its own sub-modules (e.g., `JournalDocumentPostedEvent` might be listened to by services in CO to update actuals).
    *   **Integration Events:** Listen for events from other ERP modules as defined in the `FinaIntegrationStrategy.md`. Fina may also publish events for other modules to consume (e.g., `FinancialPeriodClosedEvent`).
*   **Exception Handling:** Define custom, specific exceptions for Fina's domain and application layers.

## 4. Configuration

*   A dedicated configuration file `modules/Fina/config/fina.php` will store Fina-specific settings.
*   Examples:
    *   Default document types for various postings.
    *   Settings for CO-PA operating concern.
    *   Default parameters for financial statement generation.
    *   Feature flags for enabling/disabling certain Fina sub-functionalities.
*   This configuration will be publishable and overridable in the main application's `config` directory.

By following these development strategies, the "Fina" module will be built as a robust, maintainable, and well-structured component of the ERP system, aligning with the overall architectural goals of modularity and separation of concerns.
