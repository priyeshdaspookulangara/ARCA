# ARCA HR Module: "Employee Data Change" Integration Strategy

This document outlines the integration strategy for the "Employee Data Change" functionality within the ARCA Human Resources (HR) module. It details how various personnel actions (promotions, transfers, salary changes, etc.) will interact with other ARCA ERP components, particularly AuthMgt, FICO/Payroll, and other HR sub-modules.

## 1. Core Integration Principles

*   **Effective Dating:** All integrations transmitting HR data changes that are time-sensitive (e.g., salary, position, org unit) MUST include the effective date of the change. Consuming systems must be capable of handling effective-dated information.
*   **Event-Driven Architecture:** Approved employee data changes will primarily trigger asynchronous events to notify downstream systems, ensuring decoupling and resilience.
*   **Service APIs for Synchronous Needs:** For immediate validation or data consistency checks (e.g., validating a position exists before a promotion), internal service APIs (PHP Contracts) will be used.
*   **Data Consistency:** Focus on ensuring that employee data relevant to other modules is consistently updated upon final approval of a change.
*   **Security & Authorization Context:** Integrations with AuthMgt are critical to ensure user access rights are aligned with employee status and role changes.

## 2. Integration with ARCA AuthMgt (Authorization Management)

Changes to an employee's role, responsibilities, or organizational placement often necessitate changes to their system access.

*   **Triggering Role Review/Update upon Organizational Changes:**
    *   When an "Employee Data Change" action like Promotion, Transfer, or a significant Job Title change is approved in HR:
        *   HR will publish an event, e.g., `HrEmployeeOrgOrRoleDataChangedEvent({employee_id, core_user_id, new_position_id, new_department_id, previous_position_id, effective_date})`.
        *   ARCA AuthMgt's UserManagement or a dedicated GRC Access Control listener will subscribe to this event.
        *   **Action in AuthMgt/GRC:**
            *   Flag the user's current role assignments for review by a Security Administrator or trigger an access recertification workflow.
            *   Alternatively, if pre-defined rules map HR positions/jobs to system roles (Position-Based Security), AuthMgt could automatically propose or even apply role changes (e.g., remove old position's roles, add new position's default roles). This automated assignment would still require robust logging and potentially a review cycle.
*   **User Account Status Changes (Long-Term Leave, Termination):**
    *   **Start Long-Term Leave:** When an `EmployeeLongTermLeaveStartedEvent` is published by HR:
        *   AuthMgt subscribes and can trigger actions like:
            *   Temporarily suspending certain non-essential roles/permissions.
            *   Flagging the user account as "On Leave."
    *   **Return From Leave:** An `EmployeeReturnedFromLeaveEvent` can trigger AuthMgt to reinstate previously suspended roles/permissions.
    *   **Termination (covered by a separate "Employee Separation" process, but relevant here):** A termination event from HR must trigger immediate locking or deactivation of the user account in `AuthMgt`.

## 3. Integration with ARCA FICO / Payroll

Financial implications of employee data changes are critical. (Assuming Payroll is part of FICO or a very tightly coupled HR sub-module).

*   **Salary & Compensation Changes:**
    *   Events: `HrEmployeeCompensationChangedEvent({employee_id, core_user_id, new_base_salary, other_compensation_components_json, effective_date})`.
    *   FICO/Payroll subscribes to this event to update the employee's pay data for future payroll runs effective from the specified date.
*   **Cost Center / Company Code Changes:**
    *   Events: `HrEmployeeOrgAssignmentChangedEvent` (as mentioned above, containing new `cost_center_id`, `company_code_id`).
    *   FICO subscribes to update the employee's default cost center for expense postings and for correct allocation of payroll costs. Company code changes trigger more complex financial master data alignment in FICO.
*   **Bank Detail Changes:**
    *   Events: `HrEmployeeBankDetailsUpdatedEvent({employee_id, core_user_id, new_bank_details_encrypted_or_tokenized})`.
    *   Payroll subscribes to securely update employee bank details for salary disbursements. Secure data transfer mechanisms are paramount.
*   **Leave Impact on Payroll:**
    *   Events like `EmployeeLongTermLeaveStartedEvent` (with leave type and pay impact rules if known by HR) and `EmployeeReturnedFromLeaveEvent` must be consumed by Payroll to:
        *   Adjust salary payments (e.g., switch to statutory leave pay, half-pay, no-pay).
        *   Manage benefit contributions during leave.
        *   Reinstate regular pay upon return.

## 4. Integration with ARCA Organizational Management (HR Sub-module)

Ensures consistency between employee assignments and the defined organizational structure.

*   **Position Management Validation:**
    *   When processing a Promotion or Transfer involving a change to an employee's `position_id`:
        *   The `EmployeeDataChange` service (e.g., `PromoteEmployeeService`) will make a synchronous API call to the Organizational Management sub-module (e.g., `PositionQueryService::isPositionValidForAssignment(position_id, effective_date)`) to ensure the target position exists, is approved, and is vacant or that rules for incumbent management are followed.
*   **Reporting Line Updates:**
    *   If an `EmployeeDataChange` (e.g., Transfer, Manager Change) results in a new Reporting Manager for the employee or for their subordinates:
        *   The Organizational Management sub-module, upon receiving an event like `HrEmployeeManagerChangedEvent` or `HrEmployeeOrgAssignmentChangedEvent`, updates its reporting hierarchy data.
*   **Departmental Consistency:** Changes to an employee's assigned department are validated against the active departments in the Organizational Management module.

## 5. Integration with ARCA Time Management (if applicable)

*   **Work Schedule Changes:**
    *   Events: `HrEmployeeWorkScheduleChangedEvent({employee_id, core_user_id, new_work_schedule_rule_id, new_employment_percentage, effective_date})`.
    *   The Time Management module subscribes to this event to update the employee's standard working hours, rules for overtime calculation, and potentially leave accrual rates.

## 6. Integration with Notification System (Core ARCA Service)

*   The "Employee Data Change" workflows will utilize a central ARCA Notification Service (via API calls or by dispatching specific notification request events).
*   This service will handle sending emails or in-app notifications to initiators, approvers, and other stakeholders (e.g., the employee themselves, payroll department upon certain changes) at various stages of the workflow.

## 7. Event-Driven Communication Summary (Key Events)

*   **Events Published by HR "Employee Data Change" processes:**
    *   `HrEmployeePromotedEvent({employee_id, new_position_id, new_salary_details, effective_date, ...})`
    *   `HrEmployeeTransferredEvent({employee_id, new_org_unit_details, new_cost_center, effective_date, ...})`
    *   `HrEmployeeSalaryChangedEvent({employee_id, new_salary_components, reason, effective_date, ...})`
    *   `HrEmployeePersonalDataUpdatedEvent({employee_id, changed_fields_summary, effective_date, ...})`
    *   `HrEmployeeWorkScheduleChangedEvent({employee_id, new_schedule_details, effective_date, ...})`
    *   `HrEmployeeLongTermLeaveStartedEvent({employee_id, leave_type, start_date, expected_end_date, ...})`
    *   `HrEmployeeReturnedFromLeaveEvent({employee_id, actual_return_date, ...})`
    *   `HrEmployeeOrgAssignmentChangedEvent` (a more generic event that can cover multiple scenarios like transfer, promotion impacting org data)
    *   `HrEmployeeCompensationChangedEvent` (generic for various pay-impacting changes)

*   **Events Consumed by HR "Employee Data Change" processes (less common for this specific feature, but possible):**
    *   Potentially events from `AuthMgt` if a security-driven role change necessitates an HR review or formal job data update.
    *   Events from a Performance Management cycle completion that might trigger a promotion or salary change workflow.

This integration strategy ensures that employee data changes are managed centrally within HR Personnel Administration but are communicated effectively and consistently to all dependent ARCA modules and processes.
