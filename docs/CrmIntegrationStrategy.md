# "CRM" Module: Integration Strategy

This document outlines the integration strategy for the Customer Relationship Management (CRM) module with other existing and future ERP modules (like Fina, HR), as well as external systems. The strategy prioritizes decoupling, explicit contracts, and a mix of synchronous and asynchronous communication patterns.

## 1. Core Integration Principles

*   **Decoupling:** CRM will be designed to operate with a high degree of independence. Direct dependencies on other optional modules' internal code or database tables will be avoided.
*   **Explicit Contracts:**
    *   **Internal PHP Interfaces:** For synchronous communication between CRM and other modules within the monolith, PHP interfaces (Contracts) will be defined and resolved via Laravel's service container.
    *   **API Endpoints (Internal & External):** CRM will expose its own RESTful API endpoints for external integrations and potentially for certain internal cross-module interactions where HTTP is preferred. These APIs will be versioned and documented (e.g., using OpenAPI).
    *   **Data Transfer Objects (DTOs):** Standardized DTOs will be used for all API request/response payloads and for event payloads to ensure clear data contracts.
*   **Asynchronous Communication (Message Queues - RabbitMQ):**
    *   Preferred for many cross-module processes that do not require immediate synchronous feedback (e.g., notifying Fina of a won opportunity, syncing data to a data warehouse). This enhances resilience and scalability.
    *   CRM will publish events for significant occurrences (e.g., `CrmLeadCreatedEvent`, `CrmOpportunityWonEvent`, `CrmCaseEscalatedEvent`).
    *   CRM will also subscribe to events from other modules (e.g., `FinaInvoicePaidEvent`, `UserProvisionedEvent` from HR/Core).
*   **Idempotency:** Event listeners and API endpoints in CRM that perform write operations will be designed to be idempotent.

## 2. Integration with "Fina" Module (Finance & Controlling)

*   **Opportunity to Cash (CRM -> Fina AR):**
    *   When an Opportunity in CRM is marked as "Closed Won":
        *   CRM will publish an `OpportunityWonEvent` with relevant data (customer details, products/services sold, quantities, prices, terms).
        *   A Fina AR listener will consume this event.
        *   Fina AR will then:
            *   Check if the customer exists in its financial master data; if not, initiate a customer creation/sync process (potentially requiring CRM to provide more detailed account information via an API call if not all data is in the event).
            *   Create a sales order or directly a receivable invoice in Fina AR based on the opportunity data.
            *   Post revenue to GL and CO-PA.
*   **Customer Financial Insights (Fina -> CRM):**
    *   CRM Account/Contact profiles may need to display summarized financial information from Fina.
    *   CRM will call internal Fina APIs (e.g., `getCustomerFinancialSummary(customerId)`) to fetch data like:
        *   Total outstanding balance.
        *   Last payment date/amount.
        *   Credit limit status.
    *   This data is for display purposes in CRM; Fina remains the source of truth. Caching will be employed in CRM to avoid excessive API calls.
*   **Credit Management (CRM -> Fina AR):**
    *   During quote generation or opportunity progression in CRM, if a credit check is required:
        *   CRM will make a synchronous internal API call to Fina AR (e.g., `checkCustomerCredit(customerId, orderValue)`).
        *   Fina AR will perform the credit check based on its data and rules, returning a status (Approved, Denied, Needs Review).

## 3. Integration with "HR" Module

*   **Sales & Service Team Assignment (HR/Core User -> CRM):**
    *   CRM entities like Leads, Opportunities, Accounts, Territories, and Service Cases need to be assigned to specific employees (sales reps, account managers, service agents).
    *   CRM will typically store the `core_user_id` of the assigned employee.
    *   CRM will consume user/employee master data (name, email, team/department) from the Core User module (which might be part of HR or a central module) via internal APIs for display purposes (e.g., showing assigned user's name).
    *   When a user is de-provisioned in HR/Core, an event (e.g., `UserDeactivatedEvent`) should allow CRM to handle reassignments or flag records.
*   **Sales Performance Data (CRM -> HR - Potential Future Integration):**
    *   CRM will track sales performance metrics (e.g., quota attainment, deals closed, revenue generated per sales rep).
    *   This data could be exposed via API by CRM for the HR module to consume as input for employee performance reviews or incentive calculations. This is a longer-term, more complex integration.

## 4. External System Integration & CRM's APIs

*   **CRM's Own APIs (for External Use):**
    *   CRM will provide a comprehensive suite of versioned, documented (OpenAPI) RESTful APIs to allow external systems to interact with its data. Examples:
        *   `POST /api/crm/leads` (Create a new lead - e.g., from a corporate website).
        *   `GET /api/crm/accounts/{id}` (Retrieve account details).
        *   `PUT /api/crm/contacts/{id}` (Update contact information).
        *   `POST /api/crm/cases` (Create a new service case - e.g., from a support chatbot).
    *   Authentication for these external APIs will likely use OAuth 2.0 or API keys managed by a central API gateway.
*   **Integration with Marketing Automation Platforms:**
    *   If a dedicated external marketing automation platform is used (e.g., HubSpot, Marketo), CRM will integrate via:
        *   APIs provided by the marketing platform (CRM calls them to sync leads/contacts, campaign responses).
        *   APIs provided by CRM (marketing platform calls them to push new leads or update prospect status based on campaign interactions).
*   **Email Gateway Integration (for CRM sending emails):**
    *   For sending marketing emails (if basic functionality is built-in) or transactional emails (e.g., case notifications, lead assignment alerts), CRM will integrate with email sending services (e.g., SendGrid, Amazon SES, Mailgun) via their APIs.
*   **SMS Gateway Integration (Optional):**
    *   If SMS notifications are part of CRM workflows (e.g., case updates), integration with SMS gateways (e.g., Twilio) via API will be necessary.

## 5. Email & Calendar Synchronization

*   **Email Integration:**
    *   **Logging Incoming/Outgoing Emails:**
        *   **Option 1 (Automated Sync):** Direct API integration with Microsoft Graph API (for Outlook/Exchange) or Gmail API to synchronize emails associated with CRM contacts/leads. This requires user OAuth consent.
        *   **Option 2 (Manual Logging/BCC):** Provide a unique CRM dropbox email address where users can BCC/forward emails to have them automatically logged against the relevant records.
        *   **Option 3 (Browser/Outlook Add-in):** A companion browser extension or Outlook Add-in that allows users to manually link emails to CRM records.
    *   **Sending Emails from CRM:**
        *   Allow users to send emails to contacts/leads directly from the CRM interface.
        *   These emails can be sent via the integrated email gateway or, if full sync is set up, via the user's connected Outlook/Gmail account.
*   **Calendar Synchronization:**
    *   **Objective:** Synchronize CRM activities (meetings, tasks with due dates) with users' primary work calendars (e.g., Outlook Calendar, Google Calendar).
    *   **Method:** Typically involves using the calendar providers' APIs (Microsoft Graph API, Google Calendar API) with user OAuth consent.
    *   **Synchronization Scope:**
        *   Create/update/delete calendar events from CRM to the user's calendar.
        *   Potentially a read-only view of the user's calendar within CRM to help schedule new CRM activities without conflicts (requires careful permission handling).
    *   Synchronization can be bidirectional (changes in either system reflect in the other) or unidirectional (CRM pushes to calendar). Bidirectional is more complex.

## 6. Handling Specific Cross-Module Workflows

*   **Lead Conversion (CRM internal, then potentially to Fina):**
    *   When a Lead is qualified in CRM:
        1.  CRM internally creates/updates Account and Contact records.
        2.  The original Lead record is marked as "Converted."
        3.  An Opportunity might be automatically created.
        4.  CRM publishes a `LeadConvertedEvent` which might include Account and Contact IDs.
        5.  Fina might listen to this if it needs to pre-provision any financial shell for new accounts/contacts, though often Fina interaction waits until an Opportunity is Won.
*   **Quote/Order Management (CRM to Order Management/Fina):**
    *   When a Quote is accepted in CRM:
        1.  CRM converts it into a Sales Order (either within CRM or by calling an Order Management module).
        2.  If CRM creates the Sales Order, it publishes a `SalesOrderCreatedEvent`.
        3.  An Order Management module or Fina AR would listen to this event to take further action (e.g., order fulfillment, invoicing).

This integration strategy provides a framework for CRM to be a well-connected yet independent module within the ERP system.
