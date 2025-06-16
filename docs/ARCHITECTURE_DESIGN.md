# Core Architecture & Design Principles

This document outlines the core architectural and design principles for the modular ERP system.

## 1. Architectural Approach: Modular Monolith with Laravel

We will adopt a **Modular Monolith** architecture using the **Laravel PHP framework**.

**Justification:**

*   **Balance:** This approach provides a strong balance between achieving high modularity ("plug-and-play" modules) and managing development complexity, especially in the initial phases.
*   **Clear Boundaries:** Modules will be developed as distinct PHP packages within the main Laravel application. Each package will encapsulate a specific business capability (e.g., HR, CRM).
*   **Leveraging Laravel Ecosystem:** We can utilize Laravel's robust features like its service container, Eloquent ORM, routing, middleware, and package development capabilities to build and manage modules effectively.
*   **Simplified Deployment & Operations (Initially):** Compared to a full microservices architecture, a modular monolith is generally simpler to deploy, monitor, and manage initially.
*   **Evolution Path:** This architecture doesn't preclude a future transition to microservices for specific modules if scalability or independent deployment needs dictate. Modules designed with clear boundaries and API-based communication internally can be more easily extracted later.

## 2. Leveraging PHP's Flexibility for Modularity

PHP, especially when combined with a modern framework like Laravel, offers several features to support dynamic module loading and overall modularity:

*   **Dynamic Module Loading & Service Providers:**
    *   Each module will be a Composer package with its own `ServiceProvider`.
    *   Laravel's package auto-discovery mechanism will detect and register these service providers.
    *   The module's service provider is responsible for registering its specific components with the Laravel application:
        *   Routes (web and API)
        *   Database migrations and models
        *   Views and language files
        *   Configuration files
        *   Console commands
        *   Event listeners and subscribers
    *   Modules can be "enabled" or "disabled" (e.g., via a configuration setting or a database flag). A disabled module's service provider would simply not register its components, effectively removing it from the active system. The core system will be designed to gracefully handle the absence of optional modules.

*   **Event System for Decoupled Communication:**
    *   Laravel's event system will be extensively used for inter-module communication and for communication between modules and the core.
    *   Modules can dispatch events (e.g., `UserCreated`, `OrderPlaced`) withoutknowing which other modules are listening.
    *   Other modules can subscribe to these events to perform actions, ensuring loose coupling. If a module that subscribes to an event is removed, the event dispatcher simply has one less listener, without affecting the event producer.

*   **Dependency Injection and Interface Binding (Contracts):**
    *   Modules will depend on **contracts (PHP interfaces)** rather than concrete implementations wherever possible.
    *   Laravel's service container will be used to bind these interfaces to their concrete implementations, which can reside in other modules or the core.
    *   This allows for swapping implementations or for a module to provide a "null" implementation if a dependency module is not present. For example, a `CRM` module might depend on an `OrderServiceInterface`. If the `Sales` module (which provides orders) is not installed, a default null implementation could be provided to prevent errors.

*   **Configuration Management:**
    *   Each module can publish its own configuration file. The core system can provide sensible defaults, and users can override these settings.
    *   This allows modules to be configured independently.

## 3. Design Principles

The following design principles will be prioritized to maximize module independence and create a robust, maintainable system:

*   **SOLID:**
    *   **Single Responsibility Principle (SRP):** Each class, and by extension each module, should have one primary responsibility. Modules will be focused on specific business domains (HR, CRM, etc.).
    *   **Open/Closed Principle (OCP):** Modules should be open for extension but closed for modification. This will be achieved through service providers, events, and clear extension points (hooks, interfaces).
    *   **Liskov Substitution Principle (LSP):** Subtypes must be substitutable for their base types. This is crucial when modules provide alternative implementations for core interfaces.
    *   **Interface Segregation Principle (ISP):** Clients should not be forced to depend on interfaces they do not use. Modules will define granular interfaces for their services.
    *   **Dependency Inversion Principle (DIP):** High-level modules should not depend on low-level modules. Both should depend on abstractions (interfaces). Laravel's service container facilitates this.

*   **Separation of Concerns:**
    *   Modules are the primary tool for separating concerns at a high level (e.g., HR concerns are handled by the HR module).
    *   Within modules, further separation (e.g., MVC, services, repositories) will be applied.

*   **Dependency Injection (DI):**
    *   Laravel's service container will be used for DI throughout the application. This makes modules easier to test and decouples them from the instantiation of their dependencies.

*   **Domain-Driven Design (DDD) - Applied Pragmatically:**
    *   Each module will aim to represent a **Bounded Context** with its own ubiquitous language, domain model (entities, value objects), and application services.
    *   This helps in isolating domain logic within modules and reducing conceptual overlap.
    *   Shared concepts will be carefully managed, possibly residing in a "core" domain or via clearly defined integration contracts.

## 4. Data Consistency and Integrity (MySQL in a Modular Monolith)

With a modular monolith using a single MySQL database, specific strategies are needed:

*   **Schema Design for Modularity:**
    *   **Table Prefixes:** All tables belonging to a specific module will be prefixed with a short, unique module identifier (e.g., `hr_employees`, `hr_departments`, `crm_contacts`, `core_users`). This clearly delineates table ownership and prevents naming collisions.
    *   **Core Tables:** A few essential tables might be considered "core" (e.g., `users`, `organizations`, `roles`, `permissions`). These will have a `core_` prefix or no prefix if they are truly central and managed by the core application logic. Modules will interact with these via defined APIs or core services.

*   **Managing Relationships and Foreign Keys:**
    *   **Intra-Module Relationships:** Foreign keys can be strictly enforced within a module's own tables (e.g., `hr_employees.department_id` referencing `hr_departments.id`).
    *   **Inter-Module Relationships:**
        *   **Option 1 (Loose Coupling - Preferred for optional modules):** Avoid direct database-level foreign key constraints between tables of different *optional* modules. Instead, store the related ID (e.g., `crm_contacts.user_id` storing an ID from `core_users`). Data integrity would be maintained at the application level or through asynchronous checks. This allows a module to be removed without MySQL raising foreign key errors on the remaining module's tables.
        *   **Option 2 (Tight Coupling - For essential relationships):** If two modules are very tightly coupled and one cannot exist without the other (though this should be minimized for "plug-and-play"), direct foreign keys can be used. However, this makes module removal more complex, requiring careful migration management.
        *   **Relationship to Core Entities:** Relationships from module tables to core tables (e.g., an `hr_employees` table having a `user_id` that links to `core_users.id`) can use foreign keys, as the core tables are always present. The `ON DELETE` behavior (e.g., `SET NULL`, `CASCADE`) needs careful consideration. For example, if a user is deleted, what happens to their HR record? Often, a soft delete or archiving strategy is better than cascading deletes for ERP data.

*   **Transaction Management:**
    *   Laravel's `DB::transaction()` method will be used to ensure that operations involving multiple database statements (especially within a single module's process) are atomic.
    *   For operations spanning multiple modules (e.g., triggered by an event), distributed transaction patterns like Sagas might be considered if strong consistency is required, but this adds complexity. Often, eventual consistency via queued jobs is acceptable.

*   **Data Migration for Modules:**
    *   Each module will contain its own database migrations (using Laravel's migration system).
    *   Migrations will be timestamped to ensure correct order.
    *   When a module is "installed" or "enabled", its migrations will be run.
    *   When a module is "uninstalled" or "disabled", it should ideally have "down" migrations to remove its schema. This needs careful handling, especially regarding existing data (archival vs. deletion). For optional modules, removing schema might be acceptable if the data is truly isolated to that module.

*   **Shared Data Management:**
    *   **Central User Management:** A core component/module will manage users, roles, and permissions. Other modules will reference user IDs.
    *   **Organizational Structure:** Core entities like `Organizations` or `BusinessUnits` might be managed centrally if they are fundamental to all modules.
    *   Modules will access shared data through well-defined PHP interfaces and services provided by the core or a dedicated shared kernel module. Direct table access to another module's tables should be discouraged; API-like service calls are preferred even within the monolith.

This architectural foundation aims to provide the necessary modularity while leveraging the strengths of PHP and Laravel for rapid development and a rich feature set.

## 5. Technology Stack Recommendation

This section details the recommended technology stack for the ERP system.

*   **Backend Language/Framework:**
    *   **PHP 8.1+**
    *   **Laravel 10+**: Chosen for its comprehensive ecosystem, developer-friendly features, robust support for package development (ideal for modules), Eloquent ORM, advanced routing, security features, and integrated testing utilities.

*   **Database:**
    *   **MySQL 8.0+** or **MariaDB 10.6+**: Both are powerful, open-source relational database management systems that integrate seamlessly with PHP and Laravel. MariaDB offers potential compatibility and performance advantages.

*   **Frontend (UI/UX):**
    *   **Vue.js 3+** (with **Vite** for build tooling): Selected for its progressive framework design, ease of integration with backend APIs, excellent performance, and component-based architecture. This aligns well with a micro-frontend strategy where each module can manage its own UI components loaded into a central UI shell.
    *   **Pinia** for state management in Vue.js.

*   **API Development:**
    *   **RESTful APIs**: Implemented using Laravel's routing, controllers, and Eloquent API Resources.
    *   **Laravel Sanctum**: For lightweight API authentication (suitable for SPAs, mobile apps).
    *   **OpenAPI (Swagger)**: For API design, documentation, and contract definition.

*   **PHP Package Management:**
    *   **Composer 2+**: For managing PHP libraries and dependencies for the core application and individual modules.

*   **Containerization & Orchestration:**
    *   **Docker**: For creating consistent development, testing, and production environments for the core application and each module/service.
    *   **Kubernetes (K8s)**: For orchestrating, scaling, managing, and deploying containerized applications in production, crucial for a modular and scalable system.

*   **Queueing & Messaging:**
    *   **Redis 6+**: Primarily for caching, session management, and can also serve as a simple, fast message broker for Laravel's queue system for background jobs.
    *   **RabbitMQ 3.9+**: As a dedicated and robust message broker for critical asynchronous inter-module communication, ensuring reliable message delivery and complex routing scenarios. Laravel's queue system can be configured to use RabbitMQ.

*   **Caching:**
    *   **Redis**: For application-level caching (e.g., database query results, computed configurations, rate limiting).
    *   **OPcache**: Bundled with PHP for opcode caching to improve PHP performance.

*   **Web Server:**
    *   **Nginx**: As the primary high-performance web server and reverse proxy.
    *   **PHP-FPM (FastCGI Process Manager)**: For processing PHP requests efficiently.

*   **Testing Frameworks:**
    *   **PHP Backend:**
        *   **PHPUnit**: For unit, feature, and integration tests in PHP.
        *   **PestPHP**: An alternative/complement to PHPUnit, offering a more expressive testing syntax (optional, based on team preference).
        *   **Laravel's built-in testing helpers.**
    *   **JavaScript Frontend (Vue.js):**
        *   **Vitest** or **Jest**: For unit and component testing of Vue.js components.
        *   **Vue Test Utils**: The official library for testing Vue components.
    *   **End-to-End (E2E) Testing:**
        *   **Laravel Dusk**: For browser automation and E2E testing of the application.
        *   **Cypress.io** or **Playwright** as alternatives if more advanced E2E capabilities are needed, especially for complex micro-frontend interactions.

*   **Version Control:**
    *   **Git**: Distributed version control system.
    *   **Hosting Platform**: GitHub, GitLab, Bitbucket, or a similar service for repository hosting and collaboration features.

This stack provides a modern, robust, and scalable foundation for building the modular ERP system, with strong support from the PHP and JavaScript ecosystems.

## 6. Module Development Strategy

This section outlines the strategy for developing individual modules as independent PHP packages within the Laravel-based modular monolith, ensuring they are "plug-and-play."

### 6.1. Strategy for Independent Modules (PHP Packages)

*   **Modules as Composer Packages:**
    *   Each functional unit of the ERP (e.g., HR, CRM, Finance) will be developed as a distinct PHP Composer package.
    *   These packages will typically reside in a dedicated `modules/` directory within the root of the main Laravel application (e.g., `modules/HR`, `modules/CRM`).
    *   Each module package will have its own `composer.json` file, defining its specific dependencies, autoloading rules (PSR-4), and any scripts.

*   **Module Service Providers:**
    *   The cornerstone of each module is its primary **Service Provider** (e.g., `HRServiceProvider`). This class is the main entry point for the module.
    *   The service provider is responsible for:
        *   Registering the module's configuration with Laravel's config system.
        *   Loading database migrations, factories, and seeders.
        *   Registering Eloquent models.
        *   Loading routes (web and API).
        *   Loading views, view components, and Blade directives.
        *   Loading translation files.
        *   Registering console commands.
        *   Binding services into the Laravel service container.
        *   Subscribing to application events or events from other modules.
        *   Defining policies for authorization.

*   **Package Auto-Discovery:**
    *   Laravel's package auto-discovery feature will be utilized. By defining the service provider in the module's `composer.json`, Laravel can automatically register it when the package is installed via Composer.

*   **Independent Assets (Frontend):**
    *   Modules can contain their own frontend assets (JavaScript, CSS, images, Vue components).
    *   These assets will be compiled using Laravel Mix or Vite (as configured for the main project).
    *   Modules can publish their assets to the main application's `public` directory, typically under a module-specific path (e.g., `public/vendor/hr/`).
    *   For a micro-frontend approach, each module's frontend part would be a separate build, potentially loaded dynamically by the main UI shell.

*   **Enabling/Disabling Modules:**
    *   A configuration system (e.g., an array in `config/app.php` or a dedicated `config/modules.php`, potentially managed via an admin interface) will control which modules are "active."
    *   The application's bootstrap process or a core service provider will only load the service providers of active modules. If a module is disabled, its service provider is not loaded, effectively removing its functionality from the system.

### 6.2. Inter-Module and Core System Communication

Effective and decoupled communication is key to modularity.

*   **Synchronous Communication (Internal Services & APIs):**
    *   **Primary Method: PHP Interfaces (Contracts) & Service Container:**
        *   Modules should expose their services/functionalities through PHP interfaces (Contracts).
        *   Other modules or the core system will type-hint these interfaces in their constructors or methods.
        *   Laravel's service container will resolve these interfaces to concrete implementations provided by the source module. This is the most performant and tightly integrated method for intra-application communication.
    *   **Internal RESTful APIs (Secondary, for specific use-cases):**
        *   If a module's functionality needs to be exposed in a way that resembles an external API (e.g., for future extraction to a microservice, or for consumption by a very distinct part of the system), it can define internal API routes (e.g., `/internal/api/v1/hr/...`).
        *   These are still standard Laravel routes but are designated for internal use. Access should be restricted appropriately (e.g., specific middleware).

*   **Asynchronous Communication (Events & Queues):**
    *   **Laravel Events:**
        *   Modules can dispatch events to signal that something significant has occurred (e.g., `HR_EmployeeHiredEvent`, `CRM_LeadConvertedEvent`).
        *   Other modules (or the core) can have listeners that subscribe to these events and react accordingly. This promotes very loose coupling.
        *   Events should be well-documented data transfer objects (DTOs).
    *   **Message Queues (RabbitMQ/Redis via Laravel Queues):**
        *   For tasks that are time-consuming, can fail and need retries, or that should not block the main request-response cycle.
        *   Also used for ensuring eventual consistency across modules when direct synchronous calls are not desirable.
        *   Example: After an employee is marked as 'terminated' in HR, an event could trigger a queued job to de-provision their access in an IT Management module.
        *   Laravel's Queue system, configured with RabbitMQ (for robustness) or Redis (for simpler setups), will be used.

### 6.3. Module Registration, Discovery, and Runtime Adaptation

The core system needs to be aware of available modules and adapt its behavior.

*   **Central Module Registry (Recommended):**
    *   A `ModuleRegistry` service within the core application can maintain a list of all known modules, their status (enabled/disabled), version, and potentially metadata like registered menu items, permissions, or service contracts.
    *   This registry can be populated by:
        *   Scanning `modules/` directory for `composer.json` files.
        *   A central configuration file (e.g., `config/modules.php`).
    *   The registry can provide helper methods like `Module::isLoaded('HR')` or `Module::getEnabledModules()`.

*   **Dynamic Adaptation by the Core System:**
    *   **Navigation/UI Elements:** The main application shell (e.g., sidebars, top menus) will query the `ModuleRegistry` or use data aggregated from module service providers to dynamically render links and sections relevant to active modules. Modules can "register" their UI components or menu items with the core.
    *   **Routes:** As module service providers are only loaded if active, their routes are automatically included or excluded. The core system must handle attempts to access routes of disabled modules gracefully (e.g., showing a 404 error or a "Module Not Available" page).
    *   **Conditional Functionality:** The core system or other modules can check if a specific module is active using the `ModuleRegistry` before attempting to invoke its services or dispatch events that are only relevant to it.
    *   **Dashboard Widgets:** Modules can register widgets to be displayed on a central dashboard. The dashboard will dynamically load widgets from active modules.

### 6.4. Module Standards and Guidelines

Consistency is vital for seamless integration and maintainability.

*   **Coding Standards:**
    *   Strict adherence to **PSR-12 (Extended Coding Style)**.
    *   Use of static analysis tools like **PHPStan** and coding standards fixers like **PHP CS Fixer** or **Laravel Pint**.
*   **API Contracts & Interfaces:**
    *   Services exposed for inter-module communication MUST be defined via PHP interfaces (Contracts).
    *   Internal RESTful APIs (if any) MUST be documented using **OpenAPI v3 (Swagger)**.
*   **Dependency Management (Composer):**
    *   Each module defines its dependencies in its `composer.json`.
    *   Strive for minimal dependencies. Avoid version constraints that are too strict or too loose, to prevent conflicts with the core or other modules.
    *   Favor depending on abstractions (e.g., `psr/log-interface`) rather than concrete library implementations where possible.
*   **Configuration:**
    *   Modules SHOULD provide default configuration files that can be published to the main application's `config` directory using `php artisan vendor:publish`.
    *   All configuration keys MUST be namespaced with the module's name (e.g., `hr.payroll.default_currency`, `crm.lead_scoring.enabled`).
*   **Database Migrations & Seeding:**
    *   Modules MUST manage their own database schema via Laravel migrations.
    *   Migrations MUST have a reversible `down()` method.
    *   Seeders can be provided for default/test data.
    *   Table names MUST be prefixed (e.g., `hr_employees`).
*   **Views & Translations:**
    *   Blade views MUST be namespaced (e.g., `return view('hr::employees.index');`).
    *   Language files (translations) MUST also be namespaced and published appropriately.
*   **Testing:**
    *   Each module MUST include a comprehensive suite of tests:
        *   **Unit Tests (PHPUnit/Pest):** For individual classes and methods.
        *   **Feature/Integration Tests (PHPUnit/Pest with Laravel's testing helpers):** For testing module functionality, including API endpoints, service interactions, and database interactions within the module's scope.
    *   Tests should be runnable independently for each module and as part of the entire application's test suite.
*   **Documentation:**
    *   Each module should include a `README.md` file explaining its purpose, setup, configuration, and any APIs or events it provides/consumes.

### 6.5. Default HR Module: Scope and Essential Functionalities

The HR module will be a foundational module present by default. Its scope includes:

**I. Core HR & Personnel Administration**
    *   **Personnel Administration (PA):**
        *   Employee Master Data: Storing comprehensive employee information (personal details, employment history, banking information, contact details, qualifications, previous employment records).
        *   Personnel Actions: Managing key HR processes like hiring, promotions, transfers, contract changes, terminations, and rehires, with appropriate workflows and audit trails.
        *   Organizational Assignment: Linking employees to specific organizational units, job roles, positions, and cost centers.
        *   Contract Management: Storing and managing employee contracts, including contract types, durations, and terms.
    *   **Organizational Management (OM):**
        *   Defining and visualizing the company's organizational structure (e.g., departments, sub-departments, jobs, positions, reporting hierarchies).
        *   Managing cost center and profit center assignments for organizational units.

**II. Time Management**
    *   **Time Data Recording:** Capturing employee attendance, absences (sick leave, vacation), overtime, and potentially clock-in/clock-out data.
    *   **Leave Management:** Handling various leave types (annual, sick, maternity, etc.), managing employee leave requests, implementing approval workflows, and tracking leave balances.
    *   **Time Evaluation:** Processing recorded time data to calculate actual working hours, overtime, and absence periods for input into the payroll system.

**III. Payroll**
    *   **Gross Pay Calculation:** Calculating gross salaries based on basic pay, various allowances, bonuses, and overtime.
    *   **Deductions and Taxes:** Managing statutory deductions (e.g., income tax, social security) and voluntary deductions (e.g., loan repayments, provident fund), ensuring compliance with local regulations.
    *   **Net Pay Calculation:** Determining the final net pay after all additions and deductions.
    *   **Bank Transfers and Direct Deposit:** Generating bank files for salary disbursement and managing direct deposit information.
    *   **Basic Reporting:** Generating essential payroll reports like payslips, tax summaries, and payroll reconciliation reports.

**IV. Talent Management (Core Features Only)**
    *   **Basic Recruitment:** Managing job postings, tracking applicants through initial stages (e.g., application received, initial screening, interview scheduled), and storing candidate data.
    *   **Basic Performance Management:** Setting basic performance goals for employees and conducting simple performance reviews.
    *   **Basic Learning and Development:** Tracking employee skills and qualifications, and recording participation in training programs or courses.

**V. Employee Self-Service (ESS) & Manager Self-Service (MSS) (Core Features Only)**
    *   **ESS:** Allowing employees to view and update their personal information (with approval workflows), access their payslips, submit leave requests, and check leave balances.
    *   **MSS:** Enabling managers to view information about their team members, approve/reject leave requests, and potentially initiate certain personnel actions for their direct reports.

**VI. Basic Reporting & Analytics**
    *   Generating standard operational reports such as employee headcount reports, attendance summaries, turnover rates, and basic payroll cost summaries.
    *   Providing simple dashboards for key HR metrics.

This detailed module development strategy will serve as a blueprint for creating robust, independent, and maintainable modules that form the core of the ERP system.

## 7. Data Model Considerations (MySQL Specifics for Modularity)

This section details how the MySQL database schema will be designed and managed to maximize module independence, prevent tight coupling, and allow modules to be added or removed without extensive database changes. This is within the context of our chosen Modular Monolith architecture.

### 7.1. Database Schema Design for Modularity

*   **Single Database, Logical Schema Separation:**
    *   We will use a single MySQL database for the entire ERP system in the modular monolith approach.
    *   Logical separation of schemas will be achieved primarily through **table naming conventions**.

*   **Table Prefixes:**
    *   All database tables belonging to a specific module MUST be prefixed with a short, unique, lowercase identifier for that module, followed by an underscore.
        *   Example: `hr_employees`, `hr_departments` for the HR module.
        *   Example: `crm_contacts`, `crm_leads` for a CRM module.
        *   Example: `core_users`, `core_settings` for tables managed by the core application logic or a dedicated core module.
    *   This convention prevents naming collisions between modules and clearly indicates table ownership.

*   **Well-Defined Relationships & Foreign Keys:**
    *   **Intra-Module Relationships:**
        *   Within a module's own set of tables, standard foreign key constraints (e.g., `InnoDB` foreign keys) SHOULD be used to enforce relational integrity.
        *   Example: `hr_employees.department_id` can have a foreign key constraint referencing `hr_departments.id`.
    *   **Core-to-Module Relationships (Module referencing Core):**
        *   Tables belonging to any module (e.g., `hr_employees`) can and often will reference tables from the `core` namespace (e.g., `core_users.id` via an `hr_employees.user_id` column).
        *   Foreign key constraints SHOULD be used here, as core tables are guaranteed to exist.
        *   **`ON DELETE` Behavior:** The `ON DELETE` behavior for these foreign keys (e.g., `SET NULL`, `RESTRICT`, `CASCADE`) must be carefully chosen based on business rules.
            *   `RESTRICT`: Prevents deletion of a core record if dependent module records exist. Often too strict.
            *   `SET NULL`: Allows deletion of the core record and sets the foreign key in the module table to `NULL`. Requires the FK column to be nullable. Useful if the module record can exist without the core record.
            *   `CASCADE`: Deletes dependent module records when the core record is deleted. Use with extreme caution in ERP systems as it can lead to data loss.
            *   **Logical/Soft Deletes:** Often, a better approach is to use soft deletes (e.g., a `deleted_at` timestamp) on core records, preserving data history, and then handle related module data through application logic or scheduled cleanup tasks.
    *   **Inter-Module Relationships (Between Optional Modules):**
        *   **AVOID direct database-level foreign key constraints** between tables of two different *optional* modules if these modules are intended to be truly plug-and-play and one can operate without the other.
        *   **Reasoning:** If Module A has a table `module_a_records` and Module B has `module_b_items.record_a_id` with a foreign key to `module_a_records.id`, then Module A cannot be easily uninstalled (its tables dropped) if any records in Module B reference it.
        *   **Alternative:** Store the ID of the related entity (e.g., `module_b_items.record_a_id` stores the ID from Module A). Integrity is then maintained at the application level:
            *   The application checks for the existence of the related record before use.
            *   Eventual consistency: Use events. If a record in Module A is deleted, Module A dispatches an event, and Module B listens for this to clean up or update its related records (e.g., set `record_a_id` to null, or archive the item).
        *   This approach prioritizes module independence over strict database-level referential integrity between optional modules.

*   **Schema Management Tool:**
    *   **Laravel Migrations:** Laravel's built-in migration system (`php artisan migrate`) is the designated tool for all schema changes. Each module will contain its own migration files.

### 7.2. Handling Shared Data

Shared data refers to entities that multiple modules might need to access or reference (e.g., users, organizational units).

*   **Central Identity and Access Management (IAM) / Core Entities:**
    *   A dedicated `CoreModule` (or a specific `UserModule`, `OrganizationModule`) will own and manage truly global entities.
    *   **Key Shared Tables (Examples):**
        *   `core_users`: Stores all system users (employees, customers, partners).
        *   `core_roles`: Defines user roles (e.g., Admin, HR Manager, Sales Rep).
        *   `core_permissions`: Defines granular permissions.
        *   `core_model_has_roles`, `core_role_has_permissions`: Standard tables for role-permission linking (e.g., from `spatie/laravel-permission` or a similar package).
        *   `core_organization_units` (optional, if fundamental): Defines company structures like business units, legal entities, if shared globally.
    *   Modules (HR, CRM, etc.) will typically store a `user_id` (or `organization_unit_id`) as a foreign key referencing the corresponding `core_` table.

*   **Strategy for Accessing Shared Tables:**
    *   **Primary Access via Services/APIs:** Modules SHOULD interact with shared entities (especially for write operations) through well-defined PHP interfaces/services or internal APIs provided by the owning (Core) module. This encapsulates the logic and ensures consistency.
    *   **Eloquent Relationships for Reads:** Read operations using Laravel's Eloquent relationships (e.g., an `HREmployee` model having a `belongsTo` relationship with a `CoreUser` model) are acceptable and encouraged for convenience.
    *   **Data Consistency with Events:** When a shared entity is created, updated, or deleted in its owning module (e.g., a user's email is changed in `core_users`), the owning module MUST dispatch an event (e.g., `UserUpdatedEvent`).
        *   Other modules that might store or cache user-related information can listen to these events to update their local data, ensuring eventual consistency.

### 7.3. MySQL Migrations Across Modules

Managing database migrations effectively is crucial for modularity.

*   **Laravel Migrations per Module:**
    *   Each module will have its own `database/migrations` directory.
    *   Laravel's migration runner executes all pending migrations from all registered service providers (i.e., from all active modules) in chronological order based on the timestamp prefix in the migration filename.

*   **Migration Dependencies and Order:**
    *   **Timestamping:** The timestamp in migration filenames (`YYYY_MM_DD_HHMMSS_create_example_table.php`) generally dictates the execution order.
    *   **Implicit Dependencies:** If Module B's migration creates a table that has a foreign key to a table created by Module A, then Module A's table creation migration MUST run before Module B's. This is usually handled by ensuring Module A's migration has an earlier timestamp.
    *   **Explicit Dependencies (Avoid if Possible):** Avoid situations where a migration in Module B *alters* a table created by Module A if Module A is optional. This creates tight coupling. If Module A is a core module, this is more acceptable.
    *   **Best Practice:** Migrations for an optional module should primarily focus on creating/modifying tables *owned by that module* or establishing foreign keys to *core tables*. They should not directly alter tables of other *optional* modules.

*   **Module Installation/Uninstallation and Migrations:**
    *   **Installation:** When a module is enabled/installed, its migrations are run via `php artisan migrate`.
    *   **Uninstallation:**
        *   All migrations MUST have a functional and safe `down()` method.
        *   When a module is disabled/uninstalled, a process should ideally run its `down()` migrations to remove its tables and any alterations it made. This needs to be handled with care, especially regarding data. An option to "archive" data before dropping tables might be necessary.
        *   The ability to cleanly run `down()` migrations is essential for the "plug-and-play" nature.

*   **Seeding Data:**
    *   Each module can provide its own database seeders in its `database/seeders` directory.
    *   A main `DatabaseSeeder` in the `database/seeders` directory of the core application can be configured to call module-specific seeders, allowing for organized data population for testing or initial setup.

This data modeling strategy, centered on table prefixes, careful foreign key management, and module-owned migrations, aims to provide a robust yet flexible database structure for the modular ERP system using MySQL.

## 8. Deployment & Scalability (PHP/MySQL Optimized for Modularity)

This section outlines the strategy for deploying and scaling the modular ERP system, built with PHP (Laravel) and MySQL, ensuring that modularity benefits are maintained and the system can handle growth.

### 8.1. Deployment Strategy for Modularity

For our Modular Monolith architecture, the entire application, including all available modules (whether active or inactive by configuration), will be packaged into a single deployment unit.

*   **Containerization with Docker:**
    *   A single `Dockerfile` will be maintained at the project root to build an image containing:
        *   The chosen PHP version (e.g., PHP 8.1+).
        *   Nginx (as the web server) and PHP-FPM.
        *   All necessary PHP extensions.
        *   The Laravel application code, including all modules located in the `modules/` directory.
        *   Composer dependencies installed.
        *   Pre-compiled frontend assets (JS, CSS).
    *   This approach simplifies the build process compared to managing separate images for different module combinations. The active state of modules is determined by runtime configuration.

*   **Orchestration with Kubernetes (K8s):**
    *   **Production Environment:** Kubernetes is the recommended platform for orchestrating the deployment in production.
    *   **`Deployment` Object:** A Kubernetes `Deployment` will manage the application pods (running instances of the Docker image). This handles rolling updates and rollbacks.
    *   **`Service` Object:** A Kubernetes `Service` (e.g., type `LoadBalancer` or `ClusterIP` fronted by an Ingress) will provide a stable network endpoint to access the application.
    *   **`Ingress` Object:** An Ingress controller (e.g., Nginx Ingress, Traefik) will manage external access to the services, handling HTTP/S routing, SSL termination, and load balancing.

*   **Build and CI/CD Pipeline:**
    *   A robust CI/CD pipeline (e.g., using GitHub Actions, GitLab CI, Jenkins) will automate:
        *   Running tests (unit, integration, feature).
        *   Building the Docker image.
        *   Pushing the image to a container registry (e.g., Docker Hub, AWS ECR, Google Artifact Registry, GitLab Registry).
        *   Deploying the new image version to Kubernetes (e.g., by updating the `Deployment` object).

*   **Configuration Management:**
    *   **Kubernetes ConfigMaps and Secrets:** Environment-specific configurations (database credentials, API keys, mail server settings, module enablement flags like `MODULE_HR_ENABLED=true`) will be stored in Kubernetes ConfigMaps and Secrets.
    *   These will be injected into the application pods as environment variables or mounted as configuration files at runtime.
    *   Laravel's configuration system will read these environment variables. This allows the same Docker image to be used across different environments (dev, staging, prod) without modification.

### 8.2. Ensuring Scalability with Dynamic Modules

*   **Horizontal Scaling of PHP Application (Pods):**
    *   The Kubernetes `HorizontalPodAutoscaler` (HPA) will be configured to automatically increase or decrease the number of application pods based on metrics like CPU utilization, memory usage, or custom metrics (e.g., requests per second).
    *   Since all modules are part of the same application image in the modular monolith, scaling a pod means scaling the capacity for all *active* modules within that pod.

*   **Load Balancing:**
    *   The Kubernetes Ingress controller or Service of type `LoadBalancer` will distribute incoming traffic evenly across all available application pods.

*   **PHP-FPM Optimization:**
    *   The PHP-FPM configuration within the Docker image will be tuned for optimal performance (e.g., `pm` (process manager settings like `dynamic` or `ondemand`), `pm.max_children`, `pm.start_servers`, `pm.min_spare_servers`, `pm.max_spare_servers`).

*   **MySQL Database Scalability:**
    *   **Read Replicas:** For read-intensive operations, configure MySQL read replicas. Laravel's database configuration can be set up to direct read queries (e.g., `SELECT` statements not within a transaction) to read replicas and write queries (e.g., `INSERT`, `UPDATE`, `DELETE`) to the primary database instance.
    *   **Connection Pooling:** While PHP typically handles connections per request, for very high-traffic sites, external connection poolers (like ProxySQL for MySQL, or PgBouncer if PostgreSQL were used) could be considered to reduce connection overhead on the database server. Monitor database connection limits.
    *   **Database Sharding/Partitioning (Advanced/Future):**
        *   If data volume or write throughput becomes a significant bottleneck on a single primary database, sharding (distributing data across multiple database servers) or partitioning (dividing large tables into smaller, more manageable pieces within a single server) can be implemented.
        *   This adds considerable complexity to the application logic and operations. It should be a last resort, typically considered when scaling vertically (bigger server) or with read replicas is no longer sufficient.
        *   For a modular monolith, sharding could potentially be designed around module boundaries if data isolation is very high (e.g., CRM data on one shard, HR data on another), but this requires careful planning.

*   **Stateless Application Design:**
    *   The PHP application pods MUST be stateless. Any state that needs to be persisted between requests (e.g., user sessions, shopping carts) should be stored in a distributed external store like **Redis** or a database.
    *   This allows any pod to handle any user request, simplifying scaling and improving resilience. Laravel's session and cache drivers can be configured to use Redis.

### 8.3. Caching Strategies for Performance

Effective caching is vital for reducing PHP processing time and database load.

*   **Opcode Caching:**
    *   **OPcache** (included with PHP) will be enabled and tuned in `php.ini`. This significantly speeds up PHP execution by caching precompiled script bytecode. Ensure `opcache.revalidate_freq` is set appropriately for development versus production.

*   **Application-Level Caching (Distributed Cache - Redis):**
    *   **Data Caching:** Cache results of expensive or frequently executed database queries, aggregated data, or complex computations. Use Laravel's Cache facade (`Cache::remember()`).
    *   **Configuration Caching:** In production, use `php artisan config:cache` and `php artisan route:cache` as part of the deployment script to cache Laravel's configuration and route definitions, speeding up application bootstrap. `php artisan view:cache` can also be used.
    *   **Object Caching:** Cache frequently accessed, rarely changed Eloquent models or other PHP objects (after serialization).
    *   **Rate Limiting:** Use Redis for implementing rate limiting on APIs or sensitive endpoints.

*   **HTTP Caching:**
    *   Utilize HTTP cache headers (e.g., `Cache-Control`, `ETag`, `Last-Modified`) to allow browsers and intermediate proxies (like CDNs) to cache responses effectively.

*   **Content Delivery Network (CDN) for Static Assets:**
    *   Serve static assets (CSS, JavaScript, images, fonts) via a CDN (e.g., AWS CloudFront, Cloudflare, Akamai).
    *   This reduces load on the origin server (Nginx/PHP-FPM) and improves asset loading times for users globally by serving content from edge locations closer to them.
    *   Laravel Mix/Vite can be configured to output assets with cache-busting filenames.

This deployment and scalability strategy provides a robust framework for running the modular ERP system efficiently in a cloud-native environment, allowing it to grow with user demand while maintaining operational stability.

## 9. User Interface (UI) / User Experience (UX) Strategy (PHP & JS Integration for Modularity)

This section details the UI/UX strategy, focusing on how a modern JavaScript framework (Vue.js) will integrate with the PHP (Laravel) backend to create a modular yet unified user experience.

### 9.1. Micro-Frontend-Inspired Architecture with Vue.js

While the backend is a modular monolith, the frontend will adopt principles from micro-frontend architectures to enhance modularity and independent development of UI sections.

*   **Core UI Shell (Host Application):**
    *   A primary Laravel application will serve as the "shell" or "host" for the user interface.
    *   This shell will be built using Vue.js 3+ (with Vite for tooling and Pinia for state management).
    *   Responsibilities of the core UI shell include:
        *   Overall page structure (header, footer, main content area).
        *   Global navigation (main menu, sidebar).
        *   User authentication state management (displaying logged-in user, logout button).
        *   Loading and orchestrating module-specific frontend components.
        *   Providing core JavaScript services (e.g., API client, notification service, internationalization).

*   **Module-Specific Frontends (Vue.js Components/Applications):**
    *   Each module (e.g., HR, CRM) will be responsible for developing its own set of Vue.js components that constitute its user interface.
    *   **Initial Approach (Compiled with Core):**
        *   Module-specific Vue components will be developed within the module's directory structure (e.g., `modules/HR/resources/js/components/`).
        *   These components will be compiled as part of the main application's frontend build process (managed by Laravel Vite).
        *   The core UI shell will then dynamically render these components based on Vue Router routes. Routes will be namespaced (e.g., `/app/hr/employees`, `/app/crm/contacts`). Module service providers can register their frontend routes with a core Vue router instance.
    *   **Future Evolution (True Micro-frontends - Optional):**
        *   If greater frontend independence is required (e.g., separate deployment cycles for module frontends), a true micro-frontend approach could be adopted using techniques like:
            *   **Web Components:** Encapsulating module UIs as custom elements.
            *   **Dynamic Script Loading:** Loading separately built JS bundles from each module.
            *   **Frameworks like single-spa or Module Federation (Webpack/Vite).**
        *   This adds complexity and will be considered only if the benefits outweigh the costs. The initial approach is designed to allow for easier transition if needed.

*   **Communication with PHP Backend APIs:**
    *   All module frontends (Vue.js components) will interact with the Laravel backend exclusively through the defined RESTful APIs.
    *   A centralized API client service (e.g., using Axios) will be provided by the core UI shell, configured with base URL, authentication headers, and error handling.

### 9.2. Maintaining a Unified User Experience (UX)

Consistency across modules is paramount for a good user experience.

*   **Shared Component Library (Vue.js):**
    *   A comprehensive library of common UI components (e.g., buttons, form inputs, modals, tables, cards, alerts) will be developed using Vue.js.
    *   This library will enforce the ERP's visual style and interaction patterns.
    *   It can be built from scratch, based on a utility-first CSS framework like **Tailwind CSS** (with custom Vue components), or by customizing a component framework like **Vuetify** or **Quasar** (if a richer set of pre-built components is preferred).
    *   This shared library will be used by both the core UI shell and all module-specific frontends.

*   **Design System:**
    *   A clear design system document will define:
        *   Color palette
        *   Typography (fonts, sizes, weights)
        *   Spacing and layout principles
        *   Iconography
        *   Interaction patterns and animations
    *   All module development must adhere to this design system.

*   **Centralized Navigation and Menu System:**
    *   The core UI shell will render the main navigation elements (e.g., sidebar, top navigation bar).
    *   Modules will "register" their menu items with the core system. This can be done via:
        *   A configuration array provided by the module's service provider.
        *   A dedicated `ModuleRegistry` service that collects navigation items from active modules.
    *   Each registered menu item will include: display name, icon, Vue Router route, and required permissions.
    *   The core UI shell will dynamically build and render the navigation menu based on the registered items from *active* modules and the current user's permissions.

*   **Consistent Page Layouts:**
    *   Standard page layout Vue components will be provided (e.g., for list views with filtering/sorting, detail/view pages, form pages for create/edit).
    *   Modules should utilize these standard layouts to ensure consistency in structure and behavior.

### 9.3. Authentication and Authorization (Frontend Integration)

Seamless and secure authentication/authorization across the PHP backend and JS frontend, adaptable to module changes.

*   **Authentication (Laravel Sanctum for SPAs):**
    *   The Vue.js SPA will authenticate against Laravel Sanctum.
    *   **Login Process:**
        1.  Frontend sends credentials to a Laravel login API endpoint (e.g., `/login`).
        2.  Backend (Laravel) authenticates, creates a session, and issues Sanctum's CSRF token cookie and session cookie.
    *   **Authenticated API Requests:** Subsequent API requests from Vue.js (using Axios or Fetch) will automatically include the session cookie, authenticating the user. Axios will be configured to handle CSRF tokens.
    *   **State Management:** User authentication status and basic user information will be stored in the Vue.js application's global state (e.g., using Pinia).

*   **Authorization (Permissions on Frontend & Backend):**
    *   **Backend (Source of Truth):**
        *   Laravel's Gates and Policies, potentially integrated with a package like `spatie/laravel-permission`, will protect all API endpoints.
        *   Permissions are defined by modules (e.g., `hr_create_employee`) and registered with the central IAM system if the module is active.
    *   **Frontend (UX Enhancement):**
        *   **Fetching Permissions:** After login, the frontend will fetch the authenticated user's roles and a comprehensive list of their assigned permissions from a dedicated API endpoint. This permission set will be stored in the Vuex/Pinia store.
        *   **Conditional Rendering:** Vue.js components will conditionally render UI elements (e.g., buttons, links, form fields, menu items) based on the user's permissions. A global helper (e.g., `v-if="$user.can('hr_create_employee')"`) or a Vue directive can be used.
        *   **Route Guards:** Vue Router navigation guards will prevent users from navigating to routes/views for which they lack the necessary permissions.
        *   **Dynamic UI Adaptation:** If a module is not active, its associated permissions won't be available/assigned, and UI elements related to that module will not be rendered or will be disabled.
    *   **CRITICAL REMINDER:** Frontend permission checks are for UX purposes (hiding/disabling elements) and are not a substitute for backend authorization. Every API request MUST be re-authorized on the server-side.

This UI/UX strategy ensures that while modules can manage their own frontend aspects, the overall user experience remains cohesive, secure, and dynamically adapts to the available modules.

## 10. Testing Strategy (Critical for Modularity)

A comprehensive testing strategy is paramount to ensure the stability, functionality, and integrity of the core ERP system and its individual modules, especially when modules are added, removed, or updated.

### 10.1. Backend Testing (PHP - Laravel)

*   **1. Unit Tests (PHPUnit / Pest):**
    *   **Scope:** Focus on testing individual PHP classes, methods, and functions in isolation within a specific module or the core application. Dependencies are typically mocked.
    *   **Tools:** PHPUnit (default in Laravel) or Pest (alternative offering a different syntax).
    *   **Location:**
        *   Module-specific: `modules/ModuleName/tests/Unit/`
        *   Core application: `tests/Unit/`
    *   **Responsibility:** Each module developer is responsible for writing unit tests for their module's business logic, services, helpers, etc.

*   **2. Integration Tests (PHPUnit / Pest - Laravel's Testbench):**
    *   **Scope:** Verify interactions between components *within a single module* or between a module and core Laravel services (e.g., database, cache, events within its own context). For example, testing if a module's service correctly interacts with its repository, or if an event listener within the module functions as expected.
    *   **Tools:** PHPUnit/Pest, leveraging Laravel's testing helpers (e.g., database migrations, transactions, mocking facades). Laravel's `Feature` tests often serve this purpose.
    *   **Location:**
        *   Module-specific: `modules/ModuleName/tests/Feature/`
        *   Core application: `tests/Feature/`
    *   **Database:** These tests often interact with a real (test) database, which is migrated and reset for each test or test suite.

*   **3. Inter-Module Contract / API Integration Tests (PHPUnit / Pest):**
    *   **Scope:** Crucial for modularity. These tests verify the contracts between modules.
        *   **PHP Interface Contracts:** If Module A depends on a PHP interface provided by Module B, tests ensure Module B's implementation adheres to this contract and that Module A correctly handles interactions.
        *   **Internal API Contracts:** If modules communicate via internal RESTful APIs, these tests validate request/response formats, status codes, and overall behavior of these internal API endpoints.
    *   **Tools:** PHPUnit/Pest using Laravel's HTTP testing utilities (`$this->getJson('/internal/api/...')`).
    *   **Importance:** Helps detect breaking changes when one module is updated independently of others that rely on its exposed interfaces or internal APIs.
    *   **Location:** Can reside in the consuming module's `tests/Feature` or a dedicated `tests/Contracts` directory.

### 10.2. Frontend Testing (JavaScript - Vue.js)

*   **1. Unit Tests (Vitest / Jest):**
    *   **Scope:** Test individual JavaScript/Vue.js utility functions, composables (Vue 3 Composition API), and store logic (Pinia) in isolation.
    *   **Tools:** Vitest (recommended for Vite-based Vue projects) or Jest.
    *   **Location:** Within each module's frontend directory (e.g., `modules/HR/resources/js/tests/unit/`).

*   **2. Component Tests (Vue Test Utils with Vitest / Jest):**
    *   **Scope:** Test individual Vue.js components in isolation. Verify component rendering, props, events, slots, and internal logic.
    *   **Tools:** Vue Test Utils library, running with Vitest or Jest.
    *   **Location:** Within each module's frontend directory (e.g., `modules/HR/resources/js/tests/components/`).

### 10.3. End-to-End (E2E) Tests

*   **Scope:** Simulate real user scenarios by testing complete user flows through the application, from UI interactions in the browser to backend API calls and database results.
    *   Cover critical paths for each module.
    *   Crucially, test scenarios involving interactions *between different active modules*.
    *   Test system behavior when *optional modules are disabled* (e.g., UI elements are hidden, functionality degrades gracefully).
*   **Tools:**
    *   **Laravel Dusk:** Good for applications where the frontend and backend are tightly coupled within Laravel.
    *   **Cypress or Playwright:** Recommended for more complex JavaScript applications (like our Vue.js SPA). They offer robust features for interacting with modern web UIs and debugging.
*   **Location:** A root-level directory, e.g., `tests/e2e/` or `tests/cypress/`.

### 10.4. General Testing Principles

*   **Regression Test Suite:** All automated tests (unit, integration, E2E, component) form the regression test suite. This suite is run automatically to catch any regressions introduced by new code or modifications.
*   **Test Coverage:** Aim for high test coverage for critical parts of the application. Use code coverage tools (e.g., PHPUnit's code coverage generation, Istanbul for JS) to monitor. However, focus on quality of tests over just quantity.
*   **Bug Fixes Require Tests:** When a bug is discovered, a test case that reproduces the bug should be written and added to the suite *before* the bug is fixed. This ensures the bug is truly fixed and does not reappear.
*   **CI/CD Integration:**
    *   All test suites (backend and frontend) MUST be integrated into the Continuous Integration / Continuous Deployment (CI/CD) pipeline.
    *   The build/deployment process MUST fail if any tests fail.
*   **Testing Module Activation/Deactivation:**
    *   The CI pipeline should include stages that run test suites with different configurations of *enabled/disabled optional modules*. This helps verify that the system behaves correctly under various module combinations.
    *   Manual testing of the module installation and uninstallation processes (including migrations up/down) in a staging environment is also recommended, especially for new modules or significant updates.

This multi-layered testing strategy is designed to build confidence in the modular ERP system's stability and ensure that modules can be developed, integrated, and maintained effectively without negatively impacting the overall system.

## 11. CRM Module (Example of an Addable Optional Module)

This section outlines the typical key features of a Customer Relationship Management (CRM) module. The CRM module serves as a prime example of an optional, "plug-and-play" component that users might choose to add to the core ERP system. It would be developed following all the modularity principles, standards, and communication strategies previously defined.

### 11.1. Core CRM Functionalities

*   **Contact Management:**
    *   **Companies/Accounts:** Storing and managing detailed information about client organizations or companies, including industry, size, address, and relationships (parent/subsidiary).
    *   **People/Contacts:** Storing and managing information about individual contacts associated with companies or as independent entities (name, title, email, phone, address).
    *   **Communication History:** Logging interactions with contacts and companies (e.g., emails, calls, meetings, notes).
    *   **Segmentation & Grouping:** Ability to categorize and group contacts and companies using tags, lists, or custom fields for targeted marketing or communication.
    *   **Duplicate Detection & Merging:** Basic tools to identify and merge duplicate contact or company records.

*   **Lead Management:**
    *   **Lead Capture:** Mechanisms for capturing leads from various sources (e.g., website forms, manual entry, CSV imports, potential API integrations).
    *   **Lead Information:** Storing lead-specific details (source, status, industry, interests).
    *   **Lead Qualification & Scoring:** Tools or frameworks to define criteria for qualifying leads and potentially score them based on their profile and engagement.
    *   **Lead Assignment:** Rules or manual processes for assigning leads to specific sales representatives or teams.
    *   **Lead Conversion:** Process to convert qualified leads into contacts, companies/accounts, and potentially create an initial sales opportunity.

*   **Opportunity (Deal) Management:**
    *   **Opportunity Tracking:** Managing the lifecycle of potential sales deals, from initiation to closure.
    *   **Association:** Linking opportunities to relevant contacts and companies.
    *   **Sales Stages:** Customizable sales pipeline stages (e.g., Prospecting, Qualification, Needs Analysis, Proposal, Negotiation, Closed Won, Closed Lost).
    *   **Deal Value & Probability:** Tracking estimated deal value, weighted revenue, and probability of closing.
    *   **Activity Tracking:** Logging activities (tasks, meetings, calls) related to specific opportunities.
    *   **Pipeline Visualization:** Kanban boards or list views to visualize the sales pipeline.

*   **Sales Automation (Basic Features):**
    *   **Task Management:** Creating, assigning, and tracking sales-related tasks and activities (e.g., follow-up calls, send proposal).
    *   **Calendar Integration (Basic):** Ability to schedule meetings and appointments related to CRM entities, possibly with iCal export or basic integration hooks.
    *   **Email Templates:** Pre-defined email templates for common sales communications to improve efficiency and consistency.
    *   **Simple Workflow Automation:** Basic automation rules, e.g., automatically create a follow-up task when a lead reaches a certain stage.

*   **Reporting & Analytics (Basic):**
    *   **Standard Sales Reports:**
        *   Sales Pipeline Report (opportunities by stage, value).
        *   Lead Source Effectiveness Report.
        *   Sales Activity Reports (calls made, meetings scheduled).
        *   Won/Lost Opportunity Analysis.
        *   Sales Cycle Length.
    *   **Basic Dashboards:** Visual overview of key CRM metrics (e.g., number of new leads, open opportunities, conversion rates).

### 11.2. Integration with Core ERP / Other Modules

*   **User Linking:** CRM contacts might be linked to `core_users` if they are also system users, or remain as CRM-specific contacts.
*   **Product/Service Integration (if Sales Order module exists):** Opportunities might link to products/services defined in a common catalog for quote generation.
*   **Event-Driven Interactions:**
    *   Example: A `CustomerCreatedEvent` from the CRM (when a deal is won and a new customer account is finalized) could trigger actions in a Finance module to set up billing or in an HR module if it's a new client requiring an account manager assignment.

The CRM module would have its own prefixed database tables (e.g., `crm_contacts`, `crm_leads`, `crm_opportunities`), its own service provider, routes, migrations, Vue.js frontend components, and tests, all developed as an independent Composer package within the `modules/` directory.
