# Authorization Module: Scope and Core Components

This document defines the scope and core components for the User Role and Authorization Management Module within the ARCA ERP system. This module is critical for ensuring data security, system integrity, and process compliance.

## I. Core Components and Data Structures

### 1. User Master Data

Provides functionalities for managing user identities and their fundamental access properties.

*   **1.1. User Creation/Maintenance Interface:**
    *   A dedicated administrative interface (UI) for creating, modifying (e.g., changing contact info, assigning initial roles), and deleting user accounts.
    *   Functionality for displaying user details and their current status.
*   **1.2. Key User Master Fields:**
    *   **Unique User ID:** System-wide unique identifier for each user (e.g., ). Cannot be changed after creation.
    *   **Full Name:** First name, last name, middle name/initial.
    *   **Contact Information:** Email address, phone number, department (optional link to HR org structure).
    *   **Password Management:**
        *   Secure storage of password hashes (no plain text passwords).
        *   Administrator-initiated password reset (forcing user to change on next logon).
        *   User-initiated password reset (e.g., via "forgot password" mechanism, if implemented).
        *   Configurable password policies: minimum length, complexity (uppercase, lowercase, numbers, special characters), history (prevent reuse of last X passwords), expiration period.
    *   **Validity Dates:**
        *   : Date from which the user account is active.
        *   : Date until which the user account is active (for temporary users or planned deactivation).
    *   **User Group Assignment:** (Optional) Assigning users to groups for easier management or default parameter settings.
*   **1.3. User Types (Technical Classification):**
    Implement distinct user types, each with specific technical properties and intended use:
    *   **Dialog User:**
        *   Purpose: For interactive human users who log on to the ARCA system via the UI.
        *   Properties: Subject to full password policies (expiration, complexity, change on first logon). Last logon date/time tracked. Multiple logon checks (configurable: allow, disallow, disallow and terminate previous).
    *   **System User:**
        *   Purpose: For automated background processes, internal system communications (e.g., batch jobs, workflow engine).
        *   Properties: No interactive logon permitted. Typically uses internally stored, secure credentials or system-level trust. Not subject to dialog user password policies.
    *   **Communication User:**
        *   Purpose: For system-to-system integration where external systems communicate with ARCA via APIs or other protocols (e.g., RFC-like calls, web services).
        *   Properties: No interactive logon permitted. Uses specific authentication methods suitable for programmatic access (e.g., API keys, client certificates, service account credentials). Password policies might be different (e.g., long-lived but complex).
    *   **Reference User:**
        *   Purpose: Acts as a template to assign identical authorizations to a group of dialog users. Not used for logon itself.
        *   Properties: No logon permitted. Dialog users can be linked to a reference user to inherit its authorizations (in addition to their directly assigned roles). This simplifies mass changes: modify the reference user, and all linked users get the update.
    *   **Service User:**
        *   Purpose: For shared, potentially anonymous access for specific applications or services (e.g., a public-facing portal accessing limited ARCA data, or a specific web service endpoint used by multiple clients).
        *   Properties: Allows interactive logon (if needed by the service) but often with restricted UIs. Password may be non-expiring by default or have very long expiration. Use with caution due to shared nature.
*   **1.4. Lock/Unlock User Accounts:**
    *   Functionality for administrators to manually lock or unlock user accounts (e.g., due to security concerns, extended leave).
    *   Automatic locking due to inactivity (configurable period) or too many failed logon attempts.
*   **1.5. Last Logon Information:** Track last successful logon date, time, and potentially terminal/IP for dialog users.

### 2. Authorization Objects

Predefined security constructs that represent specific protectable actions or data segments within ARCA. They enable granular access control.

*   **2.1. Definition:**
    *   Authorization Objects are defined by security administrators or module developers during module creation.
    *   Each object groups together related authorization fields that are checked in conjunction.
    *   Examples:
        *    (Materials Management - Purchase Order)
        *    (Financial Accounting - GL Account Posting)
        *    (Human Resources - Employee Master Data)
        *    (Project System - Project Definition)
*   **2.2. Authorization Fields:**
    *   Each Authorization Object contains one or more fields (typically 1 to 10).
    *   These fields represent criteria for which an authorization can be granted (e.g., Plant, Company Code, Document Type, Cost Center, Activity).
    *   Values for these fields are specified in roles.
    *   Example for :
        *    (Activity)
        *    (Purchasing Organization)
        *    (Purchasing Group)
        *    (Plant)
*   **2.3. Activities (Standardized Field Values):**
    *   A common authorization field, typically named  or similar, will use standardized codes for common actions across modules:
        *   : Create or generate
        *   : Change
        *   : Display
        *   : Print, output
        *   : Lock
        *   : Delete (logical or physical)
        *   : Maintain (broader than change, includes create/change/delete for some objects)
        *   Other specific activities as needed by modules (e.g., 'Approve', 'Release', 'Post').
*   **2.4. Authorization Object Repository:** A central place to define and manage all available Authorization Objects in the ARCA system.

### 3. Roles (Authorization Containers)

Roles are used to group authorizations and assign them to users. They are the primary mechanism for managing user access.

*   **3.1. Single Roles:**
    *   **Definition Interface:** An administrative UI for creating, maintaining, and documenting single roles.
    *   **Role Name & Description:** Unique technical name and descriptive text.
    *   **Menu Assignment (User Menu Generation):**
        *   Ability to assign specific ARCA transactions (backend codes), reports, Fiori apps (if applicable), web links, or custom functionalities to the role.
        *   This assignment defines the navigation menu that users assigned this role will see upon logon.
        *   Hierarchical menu structure within the role.
    *   **Authorization Assignment (Profile Data):**
        *   Link one or more Authorization Objects to the role.
        *   For each linked Authorization Object, specify the allowed values for its Authorization Fields. This can include:
            *   Specific values (e.g., Plant '1000').
            *   Ranges (e.g., Cost Center '100' to '199').
            *   Wildcards (e.g., '*' for all values, if appropriate and controlled).
            *   Exclusion of values.
    *   **Profile Generation (Technical Authorizations):**
        *   An automated process (triggered by admin) that compiles the assigned authorizations (objects and field values) within a single role into a technical "authorization profile" or "generated profile."
        *   This generated profile is what the system's authorization check mechanism actually evaluates against at runtime for performance.
        *   The role definition is the source; the profile is the runtime object.
*   **3.2. Composite Roles:**
    *   **Grouping Mechanism:** Allow security administrators to create composite roles by grouping multiple single roles together.
    *   **Purpose:** Simplifies user assignment for common job functions that require authorizations from several different single roles (e.g., a "Production Supervisor" composite role might include single roles for production order display, material overview, and basic time confirmation).
    *   **Inheritance:** Users assigned a composite role automatically inherit all the menu items and authorizations (via their generated profiles) from all constituent single roles.
    *   **No Direct Authorization Assignment:** Composite roles themselves do not have direct authorization object assignments; they only aggregate single roles.
    *   **Menu Aggregation:** The menu for a user assigned a composite role is an aggregation of the menus from all included single roles.

This scope lays the groundwork for a robust and granular authorization system within ARCA ERP.
