# Authorization Module: UI/UX Strategy (Vue.js)

This document outlines the User Interface (UI) and User Experience (UX) strategy for the administrative functions of the ARCA User Role and Authorization Management Module. The goal is to provide security administrators with an intuitive, efficient, and powerful cockpit for managing system access, roles, and security policies, consistent with the ARCA ERP's design language.

## 1. UI Development with Vue.js

*   **Core Frontend Stack:** All administrative UI components for the Authorization Module will be developed using **Vue.js 3+**, with **Vite** for build tooling and **Pinia** for state management.
*   **Component Location:** Components will reside in `modules/AuthMgt/resources/js/components/`, organized by administrative function (e.g., `userManagement/`, `roleManagement/`, `sodManagement/`, `auditViewer/`, `emergencyAccess/`).
*   **Compilation & Build:** Components will be part of the main ARCA application's frontend build.
*   **Routing:** Dedicated Vue routes for Authorization Module administration will be registered under a main administrative section of the ERP (e.g., `/app/admin/security/users`, `/app/admin/security/roles/{id}`, `/app/admin/security/sod-rules`). Access to these routes will be strictly controlled by high-level administrative permissions.

## 2. Adherence to UI/UX Standards & ARCA Design System

*   **Shared Vue.js Component Library:** Mandatory use of the ARCA ERP's shared component library for all standard UI elements (tables, forms, buttons, modals, navigation, etc.) to ensure consistency and reusability.
*   **ARCA Design System (Fiori/Modern UX):** Strict adherence to the specified ARCA design system (e.g., "ARCA Fiori" or other modern UX guidelines) for layout, typography, colors, iconography, and interaction patterns.
*   **Intuitive Administration Cockpit:**
    *   The primary UI will be a central "Security Administration Cockpit" or dashboard providing easy access to all key functions.
    *   Clear, task-oriented navigation within the module (e.g., a dedicated sidebar or tab structure for User Management, Role Management, SoD Rules, Audit Logs, Emergency Access, Access Requests).
*   **Efficiency for Administrators:** Design workflows to minimize clicks and data entry for common administrative tasks. Support for keyboard navigation and shortcuts where appropriate.

## 3. Specific UI Features for Authorization Management

### 3.1. User Management UI

*   **User Listing:**
    *   Paginated table displaying users with key information (User ID, Full Name, Email, User Type, Status (Active/Locked/Expired), Last Logon).
    *   Powerful search and filtering capabilities (by ID, name, email, type, status).
*   **User Creation/Editing Forms:**
    *   Intuitive forms for creating new users and editing existing ones.
    *   Clear sections for User Details, Password Management (e.g., set initial password, require change on next logon, trigger reset email), Validity Dates, User Type selection.
    *   Controls to Lock/Unlock users.
*   **User Role Assignment:**
    *   Interface to assign/unassign single and composite roles to users.
    *   Searchable list of available roles.
    *   Ability to set validity periods (start/end dates) for each role assignment.
    *   Clear display of currently assigned roles (both direct and inherited from composite roles).
*   **View User's Effective Permissions (Read-Only):** A diagnostic tool to display all effective authorizations for a selected user, showing which role(s) grant each permission.

### 3.2. Role Management UI

*   **Single Role Management:**
    *   **Listing:** Table of single roles with name, description, version, status. Search/filter capabilities.
    *   **Creation/Editing Forms:** Define role name, description.
    *   **Menu Assignment Tool:**
        *   A visual tool (e.g., a dual-panel interface with available ARCA menu items/transactions/apps on one side and the role's menu structure on the other).
        *   Support for creating folders and organizing menu items hierarchically within the role. Drag-and-drop functionality if feasible.
    *   **Authorization Assignment Interface:**
        *   Mechanism to add/remove Authorization Objects to/from the role.
        *   For each added Authorization Object, a structured way to maintain its field values:
            *   Display fields of the selected object.
            *   Input controls appropriate for field data types (text input, dropdowns for predefined values like Activities, date pickers).
            *   Support for entering specific values, ranges, and wildcards (`*`).
            *   Clear indication of active/inactive authorization lines within the role.
    *   **Profile Generation:** A button to trigger the generation/regeneration of the role's technical authorization profile. Display status of generation.
    *   **Versioning:** UI to view role version history, compare versions, and potentially revert to a previous version.
*   **Composite Role Management:**
    *   **Listing:** Table of composite roles.
    *   **Creation/Editing Forms:** Define composite role name, description.
    *   **Single Role Aggregation:** Interface to search for and add/remove existing single roles to/from the composite role.

### 3.3. SoD Rule Management & Reporting UI

*   **SoD Rule Definition:** Interface for creating and maintaining SoD rules (defining conflicting functions/authorizations).
*   **SoD Analysis Reports Viewer:** UI to display reports of SoD conflicts detected in roles or user assignments, with drill-down to conflicting elements.
*   **Mitigation Control Management:** Interface to document and track mitigating controls for accepted SoD risks.

### 3.4. Audit Log Viewer UI

*   Secure interface to query and view `auth_audit_log` entries.
*   Powerful filtering options (by date range, performing user, target user, action type, IP address, etc.).
*   Clear, readable display of log details, including old/new values for changes.
*   Export capabilities for audit log data.

### 3.5. Emergency Access Management ("Firefighter") UI

*   **For End-Users (Requesters - if self-service request is enabled):**
    *   Simple form to request Firefighter access: select FF ID/Role, input reason, requested duration.
    *   View status of their requests.
*   **For Approvers:**
    *   Dashboard/list of pending Firefighter access requests.
    *   View request details and approve/reject with comments.
*   **For Administrators/Auditors:**
    *   Monitor active Firefighter sessions in real-time.
    *   Review and sign off on completed Firefighter session logs (which should detail all actions performed).
    *   Configuration UI for Firefighter IDs/Roles and approval workflows.

### 3.6. Access Request Workflow UI

*   **For End-Users/Managers (Requesters):**
    *   Form to submit access requests (new user creation, role additions/removals for existing users).
    *   Track status of submitted requests.
*   **For Approvers (Managers, Role Owners, Security Admins):**
    *   Dashboard/list of pending access requests assigned to them.
    *   View request details, justification, and approve/reject with comments.
*   **For Administrators:**
    *   Overview of all access requests and their statuses.
    *   Interface to manually provision access once a request is fully approved (if not fully automated).
    *   Configuration UI for access request workflows and approval steps.

## 4. User Self-Service (Minimal - Focused on Security)

*   **Password Reset:** If a centralized "Forgot Password" mechanism is part of the ARCA logon screen, this module would provide the backend logic. UI components for entering User ID/email and handling reset tokens/security questions.
*   **View Own Profile (Security Aspects - Read-Only):** Potentially allow users to view their assigned roles and basic user master data (validity, last logon). This might be part of a broader Employee Self-Service (ESS) module that fetches data from AuthMgt via API.

## 5. API Communication & UI Authorization

*   The Authorization Module's administrative UI components will interact with its own backend services via dedicated, secure APIs.
*   Access to the entire Security Administration Cockpit and its various sections will be strictly controlled by specific high-level administrative roles and permissions defined within the Authorization module itself (e.g., `AuthAdminUserRole`, `AuthAdminRoleManagementRole`, `SecurityAuditorRole`).

This UI/UX strategy aims to make the administration of ARCA's security framework as clear, efficient, and secure as possible.
