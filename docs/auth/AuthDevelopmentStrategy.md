# Authorization Module: PHP Development & Technical Strategy

This document outlines the PHP development and technical strategy for the User Role and Authorization Management Module within the ARCA ERP system. This module is foundational for security and requires a robust, performant, and secure implementation.

## 1. Module Type and Structure

*   **Module Type:** The Authorization Module will be developed as an independent **Laravel package**, proposed to be located in `modules/AuthMgt/` (alternative: `modules/Security/`). It will have its own `composer.json` and PSR-4 autoloading for `Modules\AuthMgt\`.

*   **High-Level Internal Directory Structure (PSR-4 Autoloading from `modules/AuthMgt/src/`):**
    The module will be organized by its core responsibilities to manage complexity.

    ```
    modules/AuthMgt/
    ├── src/
    │   ├── UserManagement/  # User master data services, user type logic, password policies
    │   │   ├── Application/   # CreateUserService, UpdateUserPasswordService, LockUserService
    │   │   ├── Domain/        # User entity (extending/wrapping core user), UserPolicy, PasswordManager
    │   │   └── Infrastructure/  # Eloquent models for auth_users, event listeners for HR integration
    │   ├── RoleManagement/  # Single & Composite role services, profile generation, menu construction
    │   │   ├── Application/   # CreateSingleRoleService, AssignAuthToRoleService, GenerateRoleProfileService
    │   │   ├── Domain/        # Role, CompositeRole entities, RoleProfile, MenuDefinition
    │   │   └── Infrastructure/
    │   ├── Authorization/   # Core authorization checking engine/service
    │   │   ├── Application/   # AuthorizationService (the core `check()` method)
    │   │   └── Domain/        # AuthorizationRequest, AuthorizationResult, EffectiveUserProfile (cached)
    │   ├── SoD/             # Segregation of Duties rule engine, conflict detection services
    │   │   ├── Application/   # CheckSoDConflictService
    │   │   └── Domain/        # SoDRule, SoDConflict
    │   ├── Audit/           # Audit logging services and infrastructure
    │   │   ├── Application/   # AuditLogService
    │   │   └── Infrastructure/  # AuditLog writers
    │   ├── Workflow/        # Access Request, Firefighter session management & workflow services
    │   │   ├── Application/   # RequestAccessService, ManageFirefighterSessionService
    │   │   └── Domain/        # AccessRequest, FirefighterSession
    │   ├── Core/            # Central AuthServiceProvider, shared base classes, core interfaces for the module
    │   └── SharedKernel/    # Value Objects (e.g., PermissionVO, ActivityCodeVO), common DTOs
    ├── config/
    │   └── auth_mgt.php     # Configuration for password policies, CUA, etc.
    ├── database/
    │   ├── migrations/
    │   └── seeders/         # For default activities, initial admin user (if applicable), core auth objects
    ├── resources/
    │   ├── lang/
    │   └── views/           # If any Blade views are needed for admin UI parts not covered by Vue
    ├── routes/
    │   └── api.php          # Primarily API routes for administration and potentially some user self-service
    ├── tests/               # Unit and Feature tests mirroring src structure
    └── composer.json
    ```

## 2. `AuthServiceProvider` Responsibilities

The `Modules\AuthMgt\Core\Providers\AuthServiceProvider` will be central:

*   **Registration:** Registering `config/auth_mgt.php`, loading migrations, seeders, and `routes/api.php`.
*   **Service Container Bindings:**
    *   Binding core services: `AuthorizationServiceInterface` to `AuthorizationService`.
    *   Binding repository interfaces (e.g., `UserRepositoryInterface`, `RoleRepositoryInterface`) to their Eloquent implementations.
    *   Registering application services for User Management, Role Management, SoD, Audit, and Workflow domains.
*   **Event Listener Registration:**
    *   Listeners for internal AuthMgt events (e.g., `UserPasswordChangedEvent`, `RoleUpdatedEvent`).
    *   Listeners for external events (e.g., `HREmployeeHiredEvent` for user provisioning if automated).
*   **Middleware Registration:** Registering any custom middleware related to authorization or user context if needed beyond Laravel's standard capabilities.
*   **Publishing Assets:** Making config, migrations, and seeders publishable.
*   **Policy Discovery:** Ensuring Laravel's Gate discovers policies defined within the AuthMgt module for its own entities (e.g., `RolePolicy` to control who can edit roles).

## 3. Core Authorization Check Logic

This is the most performance-critical part of the module.

*   **`AuthorizationService::check(Authenticatable $user, string $authObjectName, array $fieldsAndValuesToTest): bool`**
    *   **Input:** The authenticated user object, the name of the Authorization Object to check (e.g., "ARCA_MM_PO"), and an associative array of Field Names and their values to test (e.g., `['PLANT' => '1000', 'ACTVT' => '03']`).
    *   **Process:**
        1.  Retrieve the user's effective, compiled authorization profile. This profile should be **cached** per user session (e.g., in Redis) after first calculation to avoid repeated database lookups. The cache key should be invalidated if user's roles or relevant role versions change.
        2.  The compiled profile (from `auth_role_generated_profiles_data.profile_data_json`) should be structured for efficient lookup of `$authObjectName`.
        3.  Iterate through all authorization instances for the given `$authObjectName` found in the user's compiled profile.
        4.  For each instance, compare the field values stored in the profile against the `$fieldsAndValuesToTest`. This comparison must correctly handle:
            *   Exact matches.
            *   Ranges (e.g., Cost Center '100'-'199').
            *   Wildcards (`*` or specific patterns if supported).
            *   Lists of allowed values (if `operator` is 'IN').
            *   Exclusions (if `is_excluded` is true).
        5.  If any authorization instance in the user's profile satisfies *all* conditions in `$fieldsAndValuesToTest` for the given object, the check returns `true`.
        6.  If no instance satisfies the conditions after checking all relevant profile entries, return `false`.
*   **Integration with Laravel's Authorization (Gates & Policies):**
    *   Business modules (FICO, MM, SD, etc.) will define Laravel Policies for their Eloquent models (e.g., `PurchaseOrderPolicy`).
    *   Within these policy methods (e.g., `view(User $user, PurchaseOrder $po)`), developers will call the central `AuthorizationService::check()`:
        ```php
        // Example in a hypothetical PurchaseOrderPolicy
        public function view(User $user, PurchaseOrder $po): bool
        {
            return $this->authService->check($user, 'ARCA_MM_PO', [
                'EKORG' => $po->purchasing_organization_code, // Contextual data
                'EKGRP' => $po->purchasing_group_code,
                'ACTVT' => '03' // Display activity
            ]);
        }
        ```
    *   This keeps business modules decoupled from the specifics of role storage and focuses them on defining *what* needs to be checked.
*   **Performance of Profile Generation & Caching:**
    *   The process that generates `auth_role_generated_profiles_data.profile_data_json` must be efficient. It runs when a role definition changes or when a user's role assignments change significantly.
    *   The structure of this JSON data must be optimized for fast lookups during the `check()` method.

## 4. Password Management & Security

*   Utilize `Illuminate\Support\Facades\Hash` for one-way hashing of passwords.
*   Implement secure password reset flows (e.g., time-limited, single-use tokens sent via verified email).
*   Enforce password policies (complexity, minimum length, history, expiration) within User Management application services during password set/change operations. These policies will be configurable in `config/auth_mgt.php`.

## 5. SoD Engine Implementation

*   Services within the `SoD` domain will be responsible for:
    *   Loading SoD rules (`auth_sod_rules_header`, `auth_sod_rule_functions`).
    *   Analyzing a given role definition (or a user's effective combined profile) against these rules to identify conflicts.
    *   Logging detected conflicts to `auth_sod_conflicts_log`.

## 6. Central User Administration (CUA) - Technical Strategy (High-Level)

*   If multiple ARCA instances exist:
    *   One ARCA instance is designated as the CUA master. All user and role definitions occur here.
    *   **Distribution:** Changes in the CUA master (new users, role changes, profile regenerations) publish events or push data via dedicated APIs.
    *   **Child Systems:** Other ARCA instances (child systems) subscribe to these events/APIs and replicate the necessary authorization data (e.g., user assignments, relevant parts of generated profiles) locally.
    *   **Authorization Checks in Child Systems:** Primarily use their local replicated copy for performance. Fallback mechanisms or periodic sync checks with the master might be needed. This is a complex area requiring careful design for consistency and performance.

## 7. API Security for Communication/System Users

*   Roles assigned to `Communication User` or `System User` types will contain specific authorizations for API endpoints or system functions.
*   Laravel Sanctum (for SPA/mobile API auth) or Passport (for OAuth2) can be used. The permissions/scopes granted by these tokens can be derived from the user's ARCA roles and authorizations.
*   Middleware can be used to check specific ARCA authorizations for API routes, calling the `AuthorizationService`.

## 8. Configuration (`config/auth_mgt.php`)

*   Store critical settings:
    *   Password policies (minLength, complexity regex, historyCount, expiryDays).
    *   Session timeout settings for dialog users.
    *   CUA configuration (is_master, master_system_url, etc.).
    *   Default user validity periods.
    *   Audit log settings (retention, level of detail for certain events).
    *   Emergency Access Management ("Firefighter") parameters.

This strategy focuses on creating a secure, performant, flexible, and auditable Authorization Module that forms the security backbone of the ARCA ERP.
