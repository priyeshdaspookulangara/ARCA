# ARCA GRC (Governance, Risk, and Compliance) Module: PHP Development & Implementation Strategy

This document outlines the strategy for developing the ARCA Governance, Risk, and Compliance (GRC) module as an independent PHP package within the Laravel-based ARCA ERP. The module's role as an oversight and control layer requires a robust architecture with strong workflow capabilities and deep integration with other ARCA modules, especially AuthMgt.

## 1. Module Type and Structure

*   **Module Type:** ARCA GRC will be developed as an independent **Laravel package** located in the `modules/GRC/` directory. It will have its own `composer.json` for managing dependencies and PSR-4 autoloading for `Modules\GRC\`.

*   **High-Level Internal Directory Structure (PSR-4 Autoloading from `modules/GRC/src/`):**
    GRC will be organized by its core functional pillars, applying Domain-Driven Design (DDD) principles within each.

    ```
    modules/GRC/
    ├── src/
    │   ├── AccessControl/  # SoD Analysis, User Provisioning Workflows, Firefighter Oversight
    │   │   ├── Application/    # Services (e.g., AnalyzeSoDForUserService, TriggerProvisioningWorkflowService)
    │   │   ├── Domain/         # Entities (SoDAnalysisResult, ProvisioningRequest), Repositories
    │   │   ├── Infrastructure/   # Adapters to AuthMgt module
    │   │   └── Http/           # API Controllers for GRC Access Control admin functions
    │   ├── ProcessControl/ # Internal Control Mgt, CCM, Remediation
    │   │   ├── Application/    # Services (e.g., ManageInternalControlService, ExecuteCcmRuleService)
    │   │   ├── Domain/         # Entities (InternalControl, CcmRule, CcmException, RemediationPlan)
    │   │   └── Infrastructure/   # Data collectors for CCM from other modules
    │   ├── RiskMgt/        # Enterprise Risk Management
    │   │   ├── Application/
    │   │   ├── Domain/         # Entities (Risk, RiskAssessment, MitigationAction, Kri)
    │   │   └── Infrastructure/
    │   ├── AuditMgt/       # Audit Planning, Execution Support, Finding Mgt
    │   │   ├── Application/
    │   │   ├── Domain/         # Entities (AuditPlan, AuditEngagement, AuditFinding)
    │   │   └── Infrastructure/   # Adapters for Audit Trail Analysis from AuthMgt/Core
    │   ├── ComplianceMgt/  # Regulatory Library, Compliance Assessment, Policy Mgt
    │   │   ├── Application/
    │   │   ├── Domain/         # Entities (Regulation, ComplianceRequirement, Policy, Attestation)
    │   │   └── Infrastructure/
    │   ├── Core/           # GRC's central ServiceProvider, shared GRC base classes, core interfaces
    │   └── SharedKernel/   # Value Objects (e.g., RiskScore, ControlEffectiveness), DTOs
    ├── config/
    │   └── grc.php         # Module specific configurations
    ├── database/
    │   ├── migrations/
    │   └── seeders/        # For default GRC settings, risk categories, control types
    ├── resources/
    │   ├── lang/
    │   └── js/             # Vue.js components for GRC UI
    ├── routes/
    │   └── api.php         # Main API routes for GRC (primarily admin/system focused)
    ├── tests/              # Mirroring src structure
    └── composer.json
    ```

## 2. `GrcServiceProvider` Responsibilities

The `Modules\GRC\Core\Providers\GrcServiceProvider` will be key for:

*   **Registration:** Registering `config/grc.php`, loading migrations, seeders, routes, views (if any for admin UIs not fully SPA), and translations.
*   **Service Container Bindings:**
    *   Binding repository interfaces (e.g., `InternalControlRepositoryInterface`, `RiskRegisterRepositoryInterface`) to their Eloquent implementations.
    *   Registering application services and domain services for each GRC pillar.
    *   Registering workflow engines or state machine services for user provisioning, control deficiency remediation, audit finding tracking, etc.
    *   Registering CCM rule execution services and data collection adapters.
*   **Event Listener Registration:** Subscribing GRC listeners to its own domain events and relevant events from `AuthMgt` and other ARCA business modules as identified in `GrcIntegrationStrategy.md`.
*   **Asset Publishing:** Making config, migrations, etc., publishable.
*   **Console Command Registration:** For GRC-specific batch jobs (e.g., `grc:run-sod-analysis-batch`, `grc:execute-ccm-rules`, `grc:generate-compliance-summary`).
*   **Policy Registration:** For any GRC-specific entities that require authorization policies for their management UIs.

## 3. Key Development Principles & Patterns

*   **Domain-Driven Design (DDD):** Essential for each GRC pillar.
*   **Repository Pattern & Service Layer:** Standard application.
*   **Event-Driven Architecture (EDA):**
    *   **Internal GRC Domain Events:** (e.g., `GrcControlFailedEvent`, `GrcRiskThresholdBreachedEvent`, `GrcAuditFindingLoggedEvent`, `GrcUserAccessRequestApprovedEvent`).
    *   **Integration Events:** Publish and subscribe as per `GrcIntegrationStrategy.md`.
*   **Workflow Engine / State Machine:**
    *   Crucial for User Provisioning Workflows, Remediation Management (control deficiencies, audit findings), Emergency Access request/approval, and potentially Policy Lifecycle Management.
    *   Utilize a robust workflow library (e.g., `symfony/workflow`) or implement a clear state machine pattern for these processes. Workflows should be configurable to some extent.
*   **Strategy Pattern:**
    *   For different risk assessment methodologies.
    *   For different CCM rule evaluation logic or data collection approaches.
*   **Specification Pattern:**
    *   Useful for defining complex criteria for SoD rules, CCM rules, or filtering conditions in compliance assessments.
*   **Adapter/Connector Pattern:** For integrations with other ARCA modules to fetch data for CCM or to push GRC-driven actions (like triggering provisioning in `AuthMgt`).

## 4. Continuous Control Monitoring (CCM) Engine Design - High Level

*   **Rule Definition:**
    *   GRC UIs will allow administrators to define CCM rules (stored in `grc_ccm_rules`).
    *   Rules will specify: target module, data points to monitor, conditions/logic for deviation, frequency, and severity of exception.
*   **Data Collectors (Adapters):**
    *   Develop specific services/adapters within `GRC\ProcessControl\Infrastructure\DataCollectors\` for each ARCA module that GRC needs to monitor.
    *   These collectors will use the target module's published APIs or subscribe to their event streams to get necessary data. They must be designed to be efficient and not overload source systems.
*   **Rule Evaluator Service:**
    *   A central service in GRC (`CcmRuleEvaluatorService`) will periodically (based on rule frequency) or upon receiving relevant events:
        1.  Fetch data using the appropriate Data Collector.
        2.  Apply the rule's logic to the data.
        3.  If a deviation is detected, create a `grc_ccm_exceptions` record.
        4.  Publish a `GrcCcmExceptionGeneratedEvent`.
*   **Exception Handling & Remediation:** GRC workflows will manage the lifecycle of these exceptions.

## 5. Integration Logic Implementation (with AuthMgt and others)

*   **AuthMgt Integration:**
    *   Services in `GRC\AccessControl\Infrastructure\Adapters\` will use `AuthMgt`'s internal service interfaces (PHP Contracts) to:
        *   Fetch user, role, and effective authorization data for SoD analysis.
        *   Trigger user creation, role assignment, or status changes in `AuthMgt` based on GRC provisioning workflow approvals.
        *   Fetch Firefighter session logs for GRC review.
*   **Other Modules:** Adapters will be built as needed to consume APIs or events from FICO, HCM, LSCM, etc., for CCM, risk context, and audit data gathering.

## 6. Configuration (`config/grc.php`)

*   The `modules/GRC/config/grc.php` file will store settings like:
    *   Default parameters for SoD analysis runs.
    *   CCM rule engine parameters (e.g., default polling intervals if not event-driven).
    *   Risk matrix configurations (likelihood, impact scales, heat map colors).
    *   Default workflow definitions for user provisioning, remediation, audit findings.
    *   Audit planning cycle defaults.
    *   Compliance framework details (if GRC supports multiple, e.g., SOX, GDPR).
    *   Feature flags for specific GRC sub-functionalities.

This development strategy aims to create a GRC module that is deeply integrated, workflow-driven, and provides robust oversight capabilities for the ARCA ERP system.
