# ARCA HR Module: "Employee Data Change" UI/UX Strategy (Vue.js)

This document outlines the User Interface (UI) and User Experience (UX) strategy for the "Employee Data Change" functionality within the ARCA HR module's Personnel Administration (PA) sub-domain. The strategy focuses on providing intuitive, role-based interfaces for initiating, approving, and managing personnel actions, built with Vue.js and adhering to ARCA ERP design standards.

## 1. UI Development with Vue.js

*   **Core Frontend Stack:** All UI components for Employee Data Change will use **Vue.js 3+**, Vite, and Pinia.
*   **Component Location:** Components will reside in `modules/HR/resources/js/components/personnelAdmin/dataChange/`, further organized by role or action type (e.g., `ess/`, `mss/`, `admin/`, `promotion/`, `transfer/`).
*   **Compilation & Build:** Part of the main ARCA application's frontend build.
*   **Routing:** Vue routes will be registered under relevant HR sections:
    *   ESS: e.g., `/app/hr/my-info/update-address`, `/app/hr/my-info/update-bank-details`
    *   MSS: e.g., `/app/hr/team/employee/{employeeId}/promote`, `/app/hr/team/employee/{employeeId}/request-transfer`
    *   HR Admin: e.g., `/app/hr/admin/personnel-actions/dashboard`, `/app/hr/admin/personnel-actions/{actionId}/view`

## 2. Adherence to UI/UX Standards & ARCA Design System

*   **Shared Vue.js Component Library:** Mandatory use of ARCA's shared library for consistency.
*   **ARCA Design System (Fiori/Modern UX):** Strict adherence for look, feel, and interaction patterns.
*   **Role-Based Navigation & Views:**
    *   **ESS:** Simplified menu focused on actions an employee can initiate for themselves.
    *   **MSS:** Menu focused on actions a manager can initiate for their team, plus team overview.
    *   **HR Admin:** Comprehensive access to all personnel actions, overview dashboards, and configuration.
*   **Clarity & Guidance:** UI should clearly guide users through multi-step processes, indicating required fields and current workflow status.

## 3. Specific UI Features for Employee Data Change

### 3.1. Employee Self-Service (ESS) Forms

*   **Target Users:** All employees.
*   **Available Actions (Examples):**
    *   Update Personal Address.
    *   Update Marital Status.
    *   Update Emergency Contact Information.
    *   Update Bank Details (for payroll).
*   **Design Focus:**
    *   Extremely simple and intuitive forms.
    *   Display current data alongside fields for new data.
    *   Clear instructions and help text.
    *   Attachment capabilities if needed (e.g., for proof of name change).
    *   Clear submission process with confirmation messages.
    *   View status of their submitted requests.

### 3.2. Manager Self-Service (MSS) Forms & Dashboards

*   **Target Users:** Managers with direct reports.
*   **Available Actions (Examples for a selected employee from their team):**
    *   Initiate Promotion.
    *   Initiate Transfer (Organizational Reassignment).
    *   Initiate Salary Change.
    *   Initiate Start of Long-Term Leave.
    *   Request Change in Work Schedule/Employment Type for an employee.
*   **Design Focus:**
    *   Easy selection of the employee from their team list/org chart view.
    *   Forms pre-filled with current employee data where possible.
    *   Clear input fields for proposed changes and the **requested effective date**.
    *   Contextual information or guidance (e.g., if a promotion to a certain position has typical salary bands, these might be displayed for reference).
    *   Calculated fields if applicable (e.g., percentage salary increase).
    *   Attachment capabilities for supporting documentation.
    *   Dashboard/list view of pending and recently completed personnel action requests for their team members.

### 3.3. HR Administrator Workbench

*   **Target Users:** HR Administrators, HR Business Partners.
*   **Comprehensive Dashboard:**
    *   Overview of all in-progress personnel action requests across the organization.
    *   Filterable/searchable list of requests (by employee, action type, status, effective date range, initiator).
    *   KPIs like average processing time for actions, number of pending approvals.
*   **Detailed Action Request View:**
    *   Complete history of the request (who initiated, when, proposed data, workflow steps taken, approvers, comments).
    *   Ability to view the full "before" and "after" snapshot of relevant employee data sections.
*   **Workflow Management (Admin Privileges):**
    *   Interface to view current workflow step and assigned approver.
    *   Ability for authorized HR Admins to reassign tasks, correct data in a request (with audit trail), or manually advance/manage workflow exceptions if necessary.
*   **"View Employee Data As Of Date" Functionality (Critical for HR Admin):**
    *   A powerful UI tool allowing HR Admins to select an employee and an "as of" date.
    *   The system then displays the employee's complete profile (job information, organizational assignment, compensation, personal data slices) as it was effective on that specific past, present, or future date by querying the effective-dated historical records. This is essential for historical reporting, auditing, and understanding data context.

### 3.4. Workflow Task List Integration (for Approvers)

*   Users who are designated approvers in a personnel action workflow (e.g., Line Managers, HRBPs, Department Heads, Payroll Admins) will see these tasks in their central ARCA Task List / Workflow Inbox.
*   Clicking a task will navigate them to a dedicated approval UI:
    *   Clear display of the requested change (e.g., "Promotion for John Doe to Senior Engineer, effective YYYY-MM-DD").
    *   Side-by-side comparison of current data vs. proposed new data.
    *   Access to any attached documents or comments from previous steps.
    *   Clear "Approve," "Reject," "Send Back for Revision" action buttons.
    *   Field for mandatory or optional approval comments.

## 4. Effective Date Handling in UI

*   All forms initiating personnel actions MUST have a clear, mandatory input for the **"Requested Effective Date"** of the change.
*   Date pickers should be user-friendly.
*   When displaying employee data, especially historical views or lists of changes, the `valid_from` and `valid_to` dates for each data slice should be clearly visible or usable for filtering.

## 5. API Communication & UI Authorization

*   **API Communication:** All Employee Data Change UI components will interact with the HR backend services via secure RESTful APIs, using the centralized API client.
*   **Authorization in UI:**
    *   Visibility of specific personnel actions in ESS/MSS will be role-driven.
    *   HR Administrator access to the workbench and specific sensitive actions (e.g., viewing full salary history, overriding workflows) will be strictly controlled by granular permissions defined in ARCA `AuthMgt`.
    *   Field-level security might be applied in some views if certain HR data is highly sensitive and only viewable by specific HR roles.

This UI/UX strategy aims to streamline the Employee Data Change processes, make them accessible to the appropriate roles (ESS, MSS, HR Admin), and provide transparency through clear workflow status and effective-dated data views.
