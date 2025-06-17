# Authorization Module: Role Management Processes & Segregation of Duties (SoD)

This document outlines the processes for managing roles within the ARCA ERP system, including their design, definition, Segregation of Duties (SoD) considerations, versioning, and user assignment lifecycle.

## 1. Role Design & Definition Workflow

A structured workflow is essential for creating roles that accurately reflect business needs while adhering to security best practices.

*   **1.1. Business Requirement Gathering:**
    *   **Input:** Job descriptions, business process documentation, interviews with process owners, department heads, and key users.
    *   **Objective:** Identify the specific system access (transactions, reports, applications, data views) required for various job functions or business tasks.
    *   **Role Owner Concept:** Assign a "business role owner" responsible for defining and approving the access requirements for roles related to their functional area.
*   **1.2. Translation to System Authorizations:**
    *   Security administrators, in collaboration with module experts and role owners, translate the gathered business requirements into:
        *   Specific ARCA transactions, reports, and application identifiers for menu construction.
        *   Required Authorization Objects and the specific field values needed for each.
    *   Principle of Least Privilege: Grant only the minimum necessary authorizations required for a user to perform their job duties.
*   **1.3. Role Prototyping & Naming Conventions:**
    *   Develop clear naming conventions for single and composite roles to ensure consistency and understandability (e.g., `SR_FI_AP_CLERK` for a single role, `CR_FINANCE_MANAGER` for a composite role).
    *   Build prototype roles in a development/testing environment.
*   **1.4. Testing & Approval:**
    *   Business role owners and selected key users test the prototype roles to ensure they provide the correct access and functionality.
    *   Formal approval/sign-off process for role definitions before they are deployed to production.

## 2. Segregation of Duties (SoD) Analysis

SoD is a critical internal control to prevent fraud and errors by ensuring that no single individual has control over all phases of a sensitive transaction.

*   **2.1. SoD Rule Definition & Repository:**
    *   The Authorization module will provide a mechanism to define SoD rules.
    *   A rule consists of a set of two or more conflicting functions/authorizations (e.g., Function A: Create Vendor, Function B: Pay Vendor).
    *   Each function is defined by a set of critical transactions and/or authorization object values.
    *   These rules will be stored in a central SoD rule repository.
*   **2.2. Conflict Detection in Role Design & Assignment:**
    *   **During Role Building:** When authorizations are added to a single role, or single roles are added to a composite role, the system should automatically check against the SoD rule repository for potential conflicts *within that role*.
    *   **During User Assignment:** When a role (or multiple roles) is assigned to a user, the system should check for SoD conflicts that arise from the *combination* of all roles assigned to that user.
    *   **Reporting & Alerting:** If conflicts are detected, the system should alert the security administrator and/or role owner.
*   **2.3. SoD Risk Mitigation Strategies:**
    *   If an SoD conflict is identified and cannot be eliminated by redesigning roles (due to business constraints):
        *   **Documenting Mitigation:** The system should allow for documenting the conflict and the mitigating controls in place (e.g., "Compensating control: Manager reviews all vendor payments monthly").
        *   **Workflow for Mitigation Approval:** Potentially implement a workflow for approving SoD risk acceptance and mitigation strategies.
    *   The Authorization module primarily identifies risks; actual mitigating controls might be procedural or implemented in other ARCA modules (e.g., approval workflows for sensitive transactions).

## 3. Versioning of Roles

Roles evolve with business changes. Versioning is crucial for auditability and control.

*   **3.1. Change Management for Roles:**
    *   All changes to role definitions (menu items, authorization data) must create a new version of the role.
    *   The system will automatically assign version numbers or use timestamps.
*   **3.2. Auditability of Changes:**
    *   Each role version will store:
        *   The complete role definition (menu, authorizations) at that point in time.
        *   Date and time of the change.
        *   User ID of the administrator who made the change.
        *   Reason for change (optional, but recommended field).
*   **3.3. Rollback Capability:**
    *   Ability for administrators to revert a role to a previously active version if a new version introduces errors or unintended access.
*   **3.4. Comparison Functionality:**
    *   Provide a tool to compare two versions of a role (or even two different roles) to highlight differences in menu items and authorization data. This is essential for troubleshooting and auditing.
*   **3.5. Effective Dating for Versions (Advanced):**
    *   Consider allowing future-dated versions of roles to become active on a specific date.

## 4. User Assignment & Lifecycle Management

Managing the link between users and their roles throughout their tenure.

*   **4.1. Role Assignment Interface:**
    *   A UI for administrators to assign single roles and/or composite roles to user master records.
    *   Ability to view all roles currently assigned to a user, including those inherited via composite roles.
*   **4.2. Validity Periods for Role Assignments:**
    *   Each role assignment to a user can have its own `valid_from_date` and `valid_to_date`.
    *   This allows for:
        *   Temporary access assignments (e.g., for project work, covering for a colleague on leave).
        *   Pre-assigning roles that become active on a future date.
        *   Roles automatically expiring for contractors or temporary staff.
    *   The system will only consider assignments that are currently valid during authorization checks.
*   **4.3. Mass Assignment/De-assignment Tools:**
    *   Functionality for administrators to perform bulk updates:
        *   Assign a specific role to a list of users.
        *   Remove a specific role from a list of users.
        *   Replace an old role with a new role for a list of users.
    *   These tools should support input from CSV files or selection based on user attributes (e.g., department, user group).
*   **4.4. Automated Provisioning/De-provisioning (HR Integration Strategy):**
    *   **Objective:** Streamline user access management by linking it to HR employee lifecycle events.
    *   **Hiring Process (HR -> Auth Module):**
        *   When a new employee is hired and their master data created in ARCA HR, an event (`HREmployeeHiredEvent`) is published.
        *   The Authorization module subscribes to this event.
        *   Based on the employee's position, department, or job role (from HR data), the system can:
            *   Suggest a set of default roles to the security administrator.
            *   OR, automatically assign a pre-defined "birthright" set of roles (e.g., basic Employee Self-Service access).
            *   Trigger an access request workflow for further role assignments.
    *   **Termination/Separation Process (HR -> Auth Module):**
        *   When an employee's termination is processed in ARCA HR (`HREmployeeTerminatedEvent`):
            *   The Authorization module's listener automatically locks the user's ARCA account immediately.
            *   Optionally, roles can be de-assigned, or their validity end-dated.
    *   **Position Change Process (HR -> Auth Module):**
        *   When an employee changes position within the organization (`HREmployeePositionChangedEvent`):
            *   This can trigger a review of their current role assignments.
            *   The system might suggest removing old roles and adding new ones relevant to the new position.
            *   This often requires an approval workflow.
    *   **Requires:** Clear mapping between HR positions/jobs and ARCA roles, and robust event communication between HR and Authorization modules.

These role management processes aim to create a secure, compliant, and manageable authorization environment within the ARCA ERP system.
