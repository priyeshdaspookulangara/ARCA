# ARCA HR Module: "Employee Data Change" PHP Development & Implementation Strategy

This document outlines the PHP development and implementation strategy for the "Employee Data Change" functionality within the Personnel Administration (PA) sub-domain of the ARCA Human Resources (HR) module. It focuses on leveraging Laravel and DDD principles to manage various personnel actions, effective-dated data, workflows, and integrations.

## 1. Module & Directory Structure (Personnel Administration Focus)

The "Employee Data Change" functionalities will reside primarily within the `modules/HR/src/PersonnelAdmin/` namespace.

*   **`Application/UseCases/` or `Application/Actions/`:** This directory will house the specific Application Services for each type of personnel action.
    *   Example: `Modules\HR\PersonnelAdmin\Application\UseCases\Promotion\PromoteEmployeeService.php`
    *   Example: `Modules\HR\PersonnelAdmin\Application\UseCases\Transfer\TransferEmployeeService.php`
    *   Example: `Modules\HR\PersonnelAdmin\Application\UseCases\SalaryChange\ChangeEmployeeSalaryService.php`
    *   Example: `Modules\HR\PersonnelAdmin\Application\UseCases\PersonalData\UpdateEmployeeAddressService.php`
*   **`Application/DTO/`:** Will contain Data Transfer Objects specific to each action request (e.g., `PromoteEmployeeRequestDto`, `UpdateAddressDto`). Some might be shared if applicable.
*   **`Domain/Entities/`:** The core `Employee` entity (Eloquent model `Modules\HR\PersonnelAdmin\Infrastructure\Persistence\Models\Employee.php`) will be central. We might introduce:
    *   Value Objects for complex HR data types if not already present (e.g., `EffectiveDateRange`).
    *   Entities for `PersonnelActionRequest` (if it has significant domain logic beyond being a log).
    *   Entities representing the data slices (e.g., `JobAssignmentSlice`, `CompensationSlice`) if we opt for dedicated domain entities separate from Eloquent models for these versioned records. For simplicity, Eloquent models for `hr_employee_job_assignments`, etc., can often serve as these entities.
*   **`Domain/Repositories/`:** Interfaces like `EmployeeRepositoryInterface` will be extended, or new interfaces like `JobAssignmentRepositoryInterface`, `CompensationRepositoryInterface` will be created to manage the effective-dated data slices.
*   **`Domain/Services/`:** Domain services for complex business rules not fitting into an entity (e.g., `EffectiveDateValidationService`, `PromotionEligibilityService`).
*   **`Domain/Events/`:** Specific domain events for each personnel action (e.g., `EmployeePromotedEvent`, `EmployeeSalaryChangedEvent`).
*   **`Infrastructure/Persistence/Repositories/`:** Eloquent implementations of the new/extended repository interfaces.
*   **`Infrastructure/Persistence/Models/`:** Eloquent models for any new tables (e.g., `PersonnelActionRequest`, `EmployeeJobAssignment`, `EmployeeCompensation`).
*   **`Http/Controllers/`:** API controllers for handling requests related to initiating these personnel actions (e.g., `PromotionController`, `TransferController`).

## 2. Application Services for Each Personnel Action

Dedicated Application Services will orchestrate each type of data change. Each service will typically:

1.  Accept a specific Data Transfer Object (DTO) for the action.
2.  Perform initial validation and authorization checks (though primary auth is via middleware/policies).
3.  Initiate a `PersonnelActionRequest` record (e.g., in `hr_personnel_action_requests`).
4.  **Initiate and manage a workflow instance:**
    *   Interact with a core ARCA Workflow Service or a dedicated workflow component (e.g., Symfony Workflow) configured for HR personnel actions.
    *   The workflow definition will determine approval steps, notifications, etc.
5.  **Upon final workflow approval (handled by a workflow listener or a callback to the service):**
    *   Perform final business rule validations.
    *   **Manage Effective-Dated Records:**
        *   Call repository methods to delimit the previous active record slice (set `valid_to = new_effective_date - 1 day`).
        *   Call repository methods to insert the new active record slice (with `valid_from = new_effective_date`, `valid_to = high_date`).
        *   This logic will be encapsulated within the service or specialized domain services it calls.
    *   Update the status of the `PersonnelActionRequest` to 'Implemented'.
    *   **Dispatch specific domain events** (e.g., `EmployeePromotedEvent`).
    *   Trigger necessary integration actions via events or direct service calls to adapters (e.g., notify AuthMgt, FICO/Payroll).
6.  Handle exceptions and rollbacks if any step fails (especially the data update part post-approval).

## 3. Domain Logic for Effective-Dated Records

*   **Entity Behavior (if using rich domain entities):** Entities representing time-sliced data (e.g., `JobAssignmentSlice`) would understand their validity period.
*   **Repository Responsibilities (Crucial):**
    *   `findCurrentByEmployeeId(employeeId, date = 'today')`: Fetches the currently active record slice for an employee as of a given date.
    *   `findAllByEmployeeId(employeeId)`: Fetches all historical and current slices for an employee, ordered by `valid_from`.
    *   `delimitCurrentRecord(employeeId, newValidToDate, recordTypeTable)`: Sets the `valid_to` date on the currently active slice.
    *   `insertNewSlice(employeeId, dataArray, recordTypeTable)`: Inserts a new slice with its `valid_from` and high `valid_to`.
    *   These repository methods will encapsulate the core SQL logic for managing time slices.
*   **`EffectiveDateService` (Domain Service - Optional):** A utility service for common date calculations related to effective dating (e.g., determining previous day, checking for overlapping periods).

## 4. Repository Enhancements

*   The existing `EmployeeRepositoryInterface` (managing `hr_employees` which holds non-versioned core data) might remain as is.
*   New repository interfaces and implementations will be needed for each effective-dated entity:
    *   `JobAssignmentRepositoryInterface` for `hr_employee_job_assignments`.
    *   `CompensationRepositoryInterface` for `hr_employee_compensation`.
    *   `EmployeeAddressRepositoryInterface` for `hr_employee_addresses`.
    *   `PersonalDataVersionRepositoryInterface` for `hr_employee_personal_data_versions`.
    *   `WorkScheduleRepositoryInterface` for `hr_employee_work_schedules`.
    *   `PersonnelActionRequestRepositoryInterface` for `hr_personnel_action_requests`.
    *   `LongTermLeaveRepositoryInterface` for `hr_employee_long_term_leaves`.
*   These repositories will implement the effective-dating logic described above.

## 5. Workflow Integration (Using a Core ARCA Workflow Engine or Symfony Workflow)

*   **Workflow Definitions:** Define separate workflow definitions (e.g., in YAML or PHP configuration) for each major personnel action type (Promotion, Transfer, Salary Change, etc.). These specify states (e.g., 'Draft', 'PendingManagerApproval', 'PendingHRBPApproval', 'Approved', 'Rejected', 'Implemented') and transitions.
*   **Service Interaction:** Application Services will:
    *   Get the appropriate workflow definition for the action type.
    *   Create a new workflow instance for the `PersonnelActionRequest`.
    *   Apply initial transitions (e.g., 'submit_for_approval').
*   **Workflow Event Listeners:**
    *   Listeners will react to workflow transition events (e.g., when a 'final_approve' transition occurs).
    *   These listeners will then call the appropriate Application Service method to apply the data changes to the effective-dated tables and dispatch domain events.
    *   Listeners will also handle sending notifications via the Notification Service.
*   **Task Assignment:** The workflow engine should integrate with a task management system or notify users/roles when an approval task is assigned to them.

## 6. Event Dispatching

Each Application Service, upon successful implementation of a personnel action (after workflow approval and data persistence), will dispatch a highly specific domain event. Examples:

*   `EmployeePromotedEvent(employeeId, actionRequestId, newPositionId, oldPositionId, effectiveDate)`
*   `EmployeeSalaryChangedEvent(employeeId, actionRequestId, newSalaryAmount, oldSalaryAmount, effectiveDate)`
*   `EmployeeAddressUpdatedEvent(employeeId, actionRequestId, newAddressDto, effectiveDate)`
These events will carry sufficient payload for consuming modules (FICO/Payroll, AuthMgt, etc.) to act upon.

## 7. Integration Service Calls (Adapters)

*   Within Application Services or Workflow Listeners, after a change is finalized:
    *   Call an adapter service for `AuthMgt` to notify of potential role changes needed (e.g., `AuthMgtIntegrationService::requestRoleReviewForUser(coreUserId, reason)`).
    *   The primary mechanism for FICO/Payroll updates will be via the dispatched domain events, which FICO/Payroll listeners will consume. Direct synchronous calls to FICO for these changes are less common and riskier for decoupling.

## 8. Configuration (`config/hr.php` or a new `config/personnel_actions.php`)

*   Store configurations for:
    *   Personnel action types and their corresponding workflow definition keys.
    *   Default approver roles for different workflow steps if not fully dynamic.
    *   Rules for effective date handling (e.g., minimum days in advance for future-dated actions).

This strategy emphasizes a clear separation of concerns, robust workflow management, and careful handling of effective-dated data, which is critical for HR processes.
