# "CRM" Module: PHP Development & Implementation Strategy

This document outlines the strategy for developing the Customer Relationship Management (CRM) module as an independent PHP package within the Laravel-based modular ERP system.

## 1. Module Type and Structure

*   **Module Type:** "CRM" will be developed as an independent **Laravel package** located in the `modules/CRM/` directory. It will have its own `composer.json` for managing dependencies and autoloading.

*   **High-Level Internal Directory Structure (PSR-4 Autoloading from `modules/CRM/src/`):**
    The CRM module will be internally organized by its main functional domains (Sales, Marketing, Service) to manage complexity and promote separation of concerns. DDD principles will be applied pragmatically within these domains.

    ```
    modules/CRM/
    ├── src/
    │   ├── Sales/  # Sales-related functionalities
    │   │   ├── Application/  # Services (e.g., ConvertLeadService, ManageOpportunityPipelineService), Commands, Queries
    │   │   ├── Domain/       # Entities (Lead, Account, Contact, Opportunity, Quote), Repositories, Value Objects, Domain Events
    │   │   ├── Infrastructure/ # Eloquent Models, Repository Implementations, Event Listeners for Sales
    │   │   └── Http/         # API Controllers (e.g., LeadsController, OpportunitiesController), Form Requests
    │   ├── Marketing/ # Marketing automation functionalities
    │   │   ├── Application/
    │   │   ├── Domain/       # Entities (Campaign, TargetList), Repositories, etc.
    │   │   ├── Infrastructure/
    │   │   └── Http/
    │   ├── Service/ # Customer Service & Support functionalities
    │   │   ├── Application/
    │   │   ├── Domain/       # Entities (Case, KnowledgeBaseArticle, SLA), Repositories, etc.
    │   │   ├── Infrastructure/
    │   │   └── Http/
    │   ├── Core/   # CRM's central ServiceProvider, shared CRM base classes, core interfaces, module bootstrap
    │   └── SharedKernel/ # Value Objects, DTOs, or interfaces used across Sales, Marketing, and Service within CRM
    ├── config/
    │   └── crm.php # Module specific configurations
    ├── database/
    │   ├── migrations/
    │   └── seeders/ # For default data like lead sources, sales stages, etc.
    ├── resources/
    │   ├── lang/
    │   ├── views/  # If CRM has any Blade views (e.g., for admin config, or basic UI not handled by Vue)
    │   └── js/     # Vue.js components for CRM specific UI
    ├── routes/
    │   ├── api.php # For CRM's RESTful APIs (internal and external)
    │   └── web.php # For any CRM-specific web UI routes (e.g., customer self-service portal if part of CRM module)
    ├── tests/
    │   ├── Unit/
    │   │   ├── Sales/
    │   │   ├── Marketing/
    │   │   └── Service/
    │   └── Feature/
    │       ├── Sales/
    │       ├── Marketing/
    │       └── Service/
    └── composer.json
    ```

## 2. `CrmServiceProvider` Responsibilities

The primary service provider, likely `Modules\CRM\Core\Providers\CrmServiceProvider`, will handle:

*   **Registration with Laravel:**
    *   Registering `config/crm.php`.
    *   Loading database migrations and seeders.
    *   Loading `routes/api.php` and `routes/web.php`.
    *   Registering Blade view namespaces and Vue component paths.
    *   Registering translation files.
*   **Service Container Bindings:**
    *   Binding repository interfaces (e.g., `LeadRepositoryInterface`) to their Eloquent implementations.
    *   Registering application services, domain services, command handlers, and query handlers.
    *   Registering event listeners for both internal CRM events and events from other ERP modules.
*   **Asset Publishing:** Making config, migrations, views, and JS assets publishable.
*   **Console Command Registration:** For CRM-specific tasks (e.g., `crm:process-lead-nurturing-batch`, `crm:check-sla-escalations`, `crm:generate-sales-forecast-snapshot`).

## 3. Key Development Principles & Patterns

*   **Domain-Driven Design (DDD) - Pragmatic Application:**
    *   **Bounded Contexts:** Sales, Marketing, and Service can be viewed as distinct bounded contexts within CRM, each with its own specific models and language.
    *   **Entities & Value Objects:** Model concepts like `Lead`, `Account`, `Opportunity`, `Case`, `Campaign` as entities with clear identities and lifecycles. Use Value Objects for descriptive aspects (e.g., `Address`, `Money`).
    *   **Aggregates:** Define consistency boundaries (e.g., an `Opportunity` with its `OpportunityLineItems` and related `Activities`).
    *   **Domain Services:** For operations that span multiple aggregates or don't naturally belong to a single entity.
*   **Repository Pattern:**
    *   Define repository interfaces in the Domain layer (e.g., `Modules\CRM\Sales\Domain\Repositories\OpportunityRepositoryInterface`).
    *   Implement these in the Infrastructure layer using Eloquent.
*   **Application Services / Service Layer:**
    *   Orchestrate use cases and business logic (e.g., `Modules\CRM\Sales\Application\ConvertLeadToOpportunityService`). These services will be the main entry points from API controllers, event listeners, or console commands.
*   **Data Transfer Objects (DTOs):** Use for input to and output from Application Services to ensure clear, structured data contracts.
*   **Event-Driven Architecture:**
    *   **Internal CRM Domain Events:** Publish events for significant state changes within CRM subdomains (e.g., `LeadQualifiedEvent`, `OpportunityStageChangedEvent`, `CaseResolvedEvent`). This helps decouple logic within the CRM module itself.
    *   **Integration Events:**
        *   Publish events for other ERP modules to consume (e.g., `CrmOpportunityWonEvent` for Fina, `CrmNewHighPriorityCaseEvent` for potential notification systems).
        *   Subscribe to events from other modules (e.g., `FinaCustomerCreditLimitChangedEvent`, `UserDeactivatedEvent`).
*   **Policies & Gates (Authorization):** Use Laravel's authorization features to control access to CRM functionalities and data based on user roles and permissions. Define policies for major entities like `LeadPolicy`, `OpportunityPolicy`, `CasePolicy`.
*   **Modularity within CRM:** While CRM is one module, its sub-domains (Sales, Marketing, Service) should also be designed with separation of concerns in mind, potentially even having their own service providers if they grow very large, though initially one main `CrmServiceProvider` is fine.

## 4. Specific Technical Considerations from Prompt

*   **Mobile Access:** Backend APIs for CRM (especially for Sales SFA and Service Case Management) will be designed to be stateless and suitable for consumption by a responsive mobile web interface or a dedicated mobile application. This means clear API contracts, efficient data retrieval, and appropriate authentication.
*   **API Design for External Integrations:**
    *   CRM will expose a versioned, well-documented (OpenAPI/Swagger) set of RESTful APIs for external systems.
    *   Focus on standard HTTP methods, status codes, and error responses.
    *   Implement robust authentication (e.g., OAuth2 for third-party apps, API keys for server-to-server).
*   **Scalability:** Design database queries and application logic efficiently. Utilize background jobs (Laravel Queues) for long-running tasks (e.g., bulk email campaigns, complex report generation, lead nurturing workflows) to ensure responsiveness of user-facing interactions.
*   **Security:**
    *   Implement Role-Based Access Control (RBAC) thoroughly using Laravel Policies/Gates for all CRM entities and functionalities.
    *   Ensure data validation for all inputs.
    *   Follow best practices for preventing common web vulnerabilities (XSS, SQLi - largely handled by Laravel but still requires diligence).
    *   Data encryption considerations (e.g., for sensitive custom fields if any) should align with overall ERP policy.
*   **Audit Trails:** Implement hooks (e.g., using Eloquent model observers or event listeners) to record changes to critical CRM entities in the `crm_audit_log` table.

## 5. Configuration

*   A `modules/CRM/config/crm.php` file will provide settings for:
    *   Default sales pipeline stages.
    *   Lead scoring parameters.
    *   SLA timers and rules.
    *   Default settings for campaigns.
    *   Configuration for customer self-service portal features.
    *   Feature flags for enabling/disabling specific CRM sub-features.

This development strategy aims to produce a CRM module that is feature-rich, robust, maintainable, and well-integrated into the broader ERP ecosystem, while also being internally well-structured.
