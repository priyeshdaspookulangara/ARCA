# ARCA HR Module: "Employee Data Change" Functionality Scope

This document defines the scope for the "Employee Data Change" functionality within the Personnel Administration (PA) sub-domain of the ARCA Human Resources (HR) module. This feature set aims to manage various updates to existing employee data, often involving approval workflows and integrations with other ARCA modules.

## I. Scope of Employee Data Change Scenarios ("Personnel Actions")

The following common personnel actions or data change scenarios are within the scope of this functionality:

### 1. Promotion

*   **Core Changes:**
    *   Update to new Position ID / Job Title.
    *   Update to new Grade/Level.
*   **Associated Changes:**
    *   Salary Increase (new base salary, other compensation component adjustments).
    *   Potential update to Reporting Manager.
    *   Potential update to assigned Organizational Unit (e.g., Department, Business Unit).
    *   Potential update to assigned Cost Center (for FICO).
*   **Integration Impact:**
    *   Triggers review/update of user authorizations/roles in ARCA AuthMgt.
    *   Updates data for ARCA FICO/Payroll.

### 2. Transfer (Organizational Reassignment)

*   **Core Changes:**
    *   Update to new primary Organizational Unit (e.g., Department, Company Code, Personnel Area, Personnel Sub-Area).
*   **Associated Changes:**
    *   Potential update to Position ID / Job Title if the transfer implies a new role.
    *   Potential update to Cost Center.
    *   Potential update to Reporting Manager.
    *   May or may not involve a direct salary change as part of the transfer itself (though a separate salary action might coincide).
*   **Integration Impact:**
    *   Updates data for ARCA FICO/Payroll (especially cost center, company code).
    *   May trigger review/update of user authorizations in ARCA AuthMgt.

### 3. Salary Change (Standalone)

*   **Core Changes:**
    *   Increase or decrease in base salary.
    *   Changes to other recurring compensation components (e.g., fixed allowances, bonuses if managed as part of base structure).
*   **Attributes:**
    *   Effective date of change.
    *   Reason for change (e.g., 'MeritIncrease', 'MarketAdjustment', 'Correction', 'PromotionRelated' - though promotion action handles its own salary change).
*   **Integration Impact:**
    *   Direct updates to ARCA FICO/Payroll.

### 4. Personal Data Update

*   **Address Change:** Update to employee's primary residential address, mailing address.
*   **Marital Status Change.**
*   **Last Name Change** (e.g., due to marriage).
*   **Emergency Contact Information Update.**
*   **Bank Details Update** (for payroll direct deposit).
*   **Other Personal Identifiers:** (e.g., national ID changes, if applicable and stored).
*   **Integration Impact:**
    *   Bank detail changes directly impact Payroll.
    *   Address changes might be relevant for taxation (FICO/Payroll) or benefits.

### 5. Change in Work Schedule / Employment Type

*   **Core Changes:**
    *   Transition between employment types (e.g., Full-Time to Part-Time, Contractor to Permanent).
    *   Change in standard weekly/monthly working hours.
    *   Change in work schedule rule (if defined, e.g., shift patterns).
*   **Associated Impacts:**
    *   Likely impact on salary calculation (pro-rata adjustments).
    *   Potential impact on benefits eligibility.
*   **Integration Impact:**
    *   Updates to ARCA Time Management (if applicable).
    *   Updates to ARCA FICO/Payroll.

### 6. Long-Term Leave Management (Start & End of Leave)

*   **Core Actions:**
    *   Recording the start of a long-term leave (e.g., Sabbatical, Extended Medical Leave, Maternity/Paternity Leave beyond short-term absence).
    *   Recording the (expected and actual) end of the long-term leave and processing the return to active work.
*   **Attributes:**
    *   Leave Type.
    *   Start Date, Expected Return Date, Actual Return Date.
    *   Reason for leave.
*   **Associated Impacts:**
    *   Change in active employment status (e.g., 'ActiveOnLeave').
    *   Impact on payroll (e.g., stop regular pay, start leave-specific pay if applicable).
    *   Impact on benefits accrual or status.
    *   Potential temporary changes in organizational assignment or manager (e.g., if backfilled).
*   **Integration Impact:**
    *   Updates to ARCA FICO/Payroll.
    *   Updates to ARCA Time Management.
    *   Potential temporary updates to ARCA AuthMgt (e.g., suspend certain access).

## II. Workflow & Approval Management (Common Scope Across Scenarios)

The following workflow and approval management capabilities are essential for all Employee Data Change scenarios:

*   **2.1. Configurable Workflows:**
    *   A flexible system (potentially leveraging a core ARCA workflow engine) to define distinct, multi-step approval workflows for different types of personnel actions (e.g., a simple address change might have fewer approval steps than a promotion with a significant salary increase).
    *   Ability to configure approval paths based on criteria like organizational unit, type of data being changed, or financial impact (e.g., salary changes > X% need additional VP approval).
*   **2.2. Definition of Approval Steps:**
    *   Support for common approval roles:
        *   Initiator (Employee for ESS, Manager for MSS, HR Admin).
        *   Line Manager.
        *   HR Business Partner (HRBP).
        *   Department Head / Higher Level Management.
        *   Payroll Administrator (for changes affecting pay).
        *   Security Administrator (for changes affecting roles/authorizations, via AuthMgt).
*   **2.3. Request Initiation Channels:**
    *   **Manager Self-Service (MSS):** Managers can initiate requests for their direct reports (e.g., promotion, transfer, salary adjustment, work schedule change).
    *   **Employee Self-Service (ESS):** Employees can initiate requests for changes to their own personal data (e.g., address, bank details, emergency contacts), typically subject to HR or manager approval.
    *   **HR Administrator Initiated:** HR administrators can initiate any type of employee data change on behalf of others or for administrative corrections, usually with appropriate system privileges.
*   **2.4. Automated Notifications:**
    *   Email or in-app notifications to be sent automatically to:
        *   The initiator upon submission.
        *   The current approver when a task is pending their action.
        *   The initiator and relevant parties upon final approval or rejection.
*   **2.5. Full Audit Trail:**
    *   Comprehensive logging of every change request: who initiated it, when, what data was proposed, who approved/rejected each step, when, and any comments made.
    *   This audit trail is crucial for compliance and historical tracking.

This scope document will guide the detailed design of the "Employee Data Change" functionality, ensuring it is robust, compliant, and meets the diverse needs of the organization.
EOL

echo "docs/hr/personnel_admin/HrEmployeeDataChangeScope.md created successfully."
