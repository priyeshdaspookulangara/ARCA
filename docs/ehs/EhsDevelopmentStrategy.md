# ARCA EHS (Environmental, Health, and Safety) Management Module: PHP Development & Implementation Strategy

This document outlines the strategy for developing the ARCA Environmental, Health, and Safety (EHS) Management module as an independent PHP package within the Laravel-based ARCA ERP. The module's diverse functionalities and critical compliance aspects demand a structured and robust development approach.

## 1. Module Type and Structure

*   **Module Type:** ARCA EHS will be developed as an independent **Laravel package** located in the `modules/EHS/` directory. It will have its own `composer.json` for managing dependencies and PSR-4 autoloading for `Modules\EHS\`.

*   **High-Level Internal Directory Structure (PSR-4 Autoloading from `modules/EHS/src/`):**
    EHS will be organized by its core functional domains, applying Domain-Driven Design (DDD) principles within each.

    ```
    modules/EHS/
    ├── src/
    │   ├── IncidentMgt/    # Incident & CAPA Management
    │   │   ├── Application/    # Services (e.g., ReportIncidentService, ManageCapaService)
    │   │   ├── Domain/         # Entities (Incident, CapaAction), Repositories, Value Objects
    │   │   ├── Infrastructure/   # Eloquent Models, Repository Implementations
    │   │   └── Http/           # API Controllers for incident reporting/management
    │   ├── RiskMgt/        # Risk Assessment & Mitigation
    │   │   ├── Application/
    │   │   ├── Domain/         # Entities (RiskAssessment, RiskRegisterEntry, MitigationPlan)
    │   │   └── Infrastructure/
    │   ├── HazMat/         # Hazardous Substance & SDS Management
    │   │   ├── Application/
    │   │   ├── Domain/         # Entities (HazardousSubstance, SafetyDataSheet)
    │   │   └── Infrastructure/
    │   ├── WasteMgt/       # Waste Stream & Disposal Management
    │   │   ├── Application/
    │   │   └── Domain/         # Entities (WasteStream, WasteDisposalRecord)
    │   ├── OccHealth/      # Occupational Health & Exposure Tracking
    │   │   ├── Application/
    │   │   ├── Domain/         # Entities (HealthSurveillanceProgram, EmployeeHealthRecord - with privacy focus)
    │   │   └── Infrastructure/   # Secure data handling for medical records
    │   ├── Compliance/     # Emissions, Permits, Audits Management
    │   │   ├── Application/
    │   │   ├── Domain/         # Entities (EmissionLog, Permit, EhsAudit, AuditFinding)
    │   │   └── Infrastructure/
    │   ├── Reporting/      # EHS Performance Reporting Services & Data Aggregation
    │   │   └── Application/
    │   ├── Core/           # EHS's central ServiceProvider, shared EHS base classes, core interfaces
    │   └── SharedKernel/   # Value Objects (e.g., LocationVO, SeverityLevelVO, UnitOfMeasure), DTOs
    ├── config/
    │   └── ehs.php         # Module specific configurations
    ├── database/
    │   ├── migrations/
    │   └── seeders/        # For default EHS types, statuses, risk matrix templates
    ├── resources/
    │   ├── lang/
    │   └── js/             # Vue.js components for EHS specific UI
    ├── routes/
    │   ├── api.php         # Main API routes for EHS
    │   └── web.php         # Main web routes for EHS (e.g., dashboards, reporting UI)
    ├── tests/              # Mirroring src structure for Unit and Feature tests
    └── composer.json
    ```

## 2. `EhsServiceProvider` Responsibilities

The `Modules\EHS\Core\Providers\EhsServiceProvider` will be central to EHS's operation:

*   **Registration:** Registering `config/ehs.php`, loading migrations, seeders, routes, views (if any), and translations.
*   **Service Container Bindings:**
    *   Binding repository interfaces (e.g., `IncidentRepositoryInterface`, `RiskAssessmentRepositoryInterface`) to their Eloquent implementations.
    *   Registering application services and domain services for each EHS domain.
    *   Registering workflow engines or state machine services for Incident Management/CAPA.
*   **Event Listener Registration:** Subscribing EHS listeners to its own domain events and relevant events from ARCA HCM, MM, QM, PM, FICO as outlined in `EhsIntegrationStrategy.md`.
*   **Asset Publishing:** Making config, migrations, etc., publishable.
*   **Console Command Registration:** For EHS-specific tasks (e.g., `ehs:check-permit-expirations`, `ehs:generate-incident-summary-report`, `ehs:process-capa-escalations`).
*   **Policy Registration:** Registering any Laravel Policies specific to EHS entities to manage access within the module's own administrative UIs.

## 3. Key Development Principles & Patterns

*   **Domain-Driven Design (DDD):**
    *   **Bounded Contexts:** Each EHS functional area (IncidentMgt, RiskMgt, etc.) will be treated as a distinct bounded context.
    *   **Aggregates, Entities, Value Objects:** Model concepts like `Incident`, `RiskAssessment`, `HazardousSubstance` with clear responsibilities.
*   **Repository Pattern & Service Layer:** Standard application for data abstraction and use case orchestration.
*   **Event-Driven Architecture (EDA):**
    *   **Internal EHS Domain Events:** (e.g., `EhsIncidentReportedEvent`, `EhsRiskMitigationImplementedEvent`, `EhsCapaEffectivenessVerifiedEvent`). These facilitate decoupling within EHS's own processes.
    *   **Integration Events:** Publish and subscribe to events as detailed in `EhsIntegrationStrategy.md`.
*   **Workflow Engine / State Machine:**
    *   Essential for Incident Management (Reported -> Investigation -> RootCauseAnalysis -> CAPAPlanned -> CAPAInProgress -> PendingVerification -> Closed).
    *   Also applicable to CAPA lifecycle, audit finding resolution, and potentially permit application/renewal processes.
    *   Consider using a library like `symfony/workflow` or a well-defined state machine pattern for these entities.
*   **Strategy Pattern:**
    *   Could be used for different risk assessment methodologies (e.g., qualitative matrix vs. quantitative).
    *   Different root cause analysis techniques (if the system provides structured support beyond text fields).
*   **Data Privacy by Design & by Default:**
    *   For the `OccHealth` domain, all services and data handling must prioritize employee data privacy.
    *   Implement strict access controls (potentially attribute-based access control - ABAC - for medical data fields).
    *   Consider encryption for sensitive fields within `ehs_employee_health_records` if required by compliance, managed by Laravel's built-in encryption capabilities. Audit access to these records rigorously.

## 4. Integration Logic Implementation

*   **Adapters/Connectors:** Logic for interacting with other ARCA modules (HCM, MM, QM, PM, FICO, CoreMDM) will reside in "Adapter" classes within the `Infrastructure` layer of the relevant EHS domain.
    *   Example: `Modules\EHS\IncidentMgt\Infrastructure\HcmEmployeeAdapter` to fetch employee details for an incident.
    *   Example: `Modules\EHS\HazMat\Infrastructure\CoreMaterialAdapter` to get hazardous properties from `core_materials` or link EHS substance data.
*   **Event Listeners:** EHS listeners for events from other modules will translate those events into EHS commands or trigger relevant EHS processes.

## 5. Configuration (`config/ehs.php`)

*   The `modules/EHS/config/ehs.php` file will store EHS-specific settings:
    *   Configurable incident types, severity levels, status codes.
    *   Risk matrix definitions (likelihood, severity scales, risk level thresholds).
    *   Default CAPA assignment rules or escalation timers.
    *   Waste stream categories and codes.
    *   Occupational health surveillance program templates.
    *   Emission parameter definitions and alert thresholds.
    *   Permit types and default review frequencies.
    *   Templates for regulatory reports (if any are generated directly).
    *   Feature flags for specific EHS sub-functionalities.

This development strategy aims to build an EHS module that is comprehensive, compliant, robust in its process management, and securely integrated within the ARCA ERP ecosystem.
