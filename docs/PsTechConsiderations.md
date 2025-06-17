# "PS" Module: Technical Considerations

This document outlines key technical considerations crucial for the successful development, deployment, and operation of the Project System (PS) module. These address non-functional requirements such as performance, security, auditability, data consistency, and error handling.

## 1. Performance

The PS module can involve complex data structures (hierarchical WBS, networks) and potentially large volumes of data, especially for large projects or many concurrent small projects.

*   **Database Query Optimization:**
    *   **Indexing:** Implement comprehensive indexing on `ps_` tables, especially for:
        *   Foreign keys used in joins (e.g., `project_definition_id`, `wbs_element_id`, `network_activity_id`).
        *   Fields used in WHERE clauses for common queries (e.g., `status_system`, date fields, `project_definition_code`).
        *   Fields used for sorting in reports.
    *   **Efficient Queries:** Write optimized SQL queries (leveraging Eloquent's capabilities or using raw DB queries where necessary for complex joins or aggregations). Avoid N+1 query problems.
    *   **Materialized Views (Consideration):** For very complex, frequently accessed reports (e.g., aggregated project cost summaries), consider using materialized views or pre-aggregated summary tables updated by scheduled jobs or event listeners, if direct queries become too slow.
*   **API Performance:**
    *   **Pagination:** All API endpoints returning lists of project entities (WBS, activities, issues, etc.) MUST implement pagination.
    *   **Selective Field Loading:** Design APIs to allow clients (especially the frontend) to request only necessary fields, reducing data transfer (e.g., a summary view vs. a detailed view).
    *   **Caching:** Cache frequently accessed, rarely changing PS data (e.g., project profiles, control keys, status definitions) using Laravel's cache (Redis).
*   **Background Processing (Queues):**
    *   Offload computationally intensive or long-running tasks to Laravel Queues (RabbitMQ/Redis):
        *   Full project rescheduling for very large projects.
        *   Critical Path Method (CPM) calculations if they become too slow for real-time UI updates.
        *   Result Analysis calculations.
        *   Period-end settlement runs.
        *   Generating large, complex reports.
        *   Processing batch updates or confirmations.
*   **Concurrent User Access:**
    *   Utilize database transactions appropriately for atomic operations (e.g., creating a WBS and its initial budget allocation).
    *   Consider optimistic locking mechanisms for entities that might be concurrently edited by multiple users if complex, multi-step updates are common outside of transactional boundaries. Laravel's default behavior is often sufficient for web request lifecycles.

## 2. Security

*   **Role-Based Access Control (RBAC) - Granular:**
    *   Define specific permissions within PS using Laravel's Gates and Policies. Examples:
        *   `ps:project:create`, `ps:project:view:{projectId}`, `ps:project:edit:{projectId}`, `ps:project:delete:{projectId}`
        *   `ps:wbs:create:{projectId}`, `ps:wbs:edit:{wbsId}`
        *   `ps:activity:confirm:{activityId}`
        *   `ps:budget:allocate:{wbsId}`, `ps:budget:view:{wbsId}`
        *   `ps:run:settlement:{projectId}`
    *   Permissions should be context-aware (e.g., a Project Manager role can only edit projects they are assigned to as "Person Responsible," unless they have global PS admin rights).
    *   Integrate seamlessly with the ERP's core user authentication and authorization model.
*   **Data Integrity & Input Validation:**
    *   Implement robust backend validation (using Laravel Form Requests or validators) for all data inputs to PS APIs and services.
    *   Validate data types, required fields, ranges, and inter-field consistency (e.g., finish date must be after start date).
    *   Protect against common web vulnerabilities (XSS, SQL Injection - largely handled by Laravel but requires careful use of its features).
*   **Access to Integrated Data:** When PS displays data from or sends data to other modules (Fina, LSCM, HR), ensure that the user's permissions for accessing/modifying that related data are also respected through service-level checks if necessary.

## 3. Audit Trails

*   **Comprehensive Logging:**
    *   Maintain a detailed audit trail for significant changes to PS entities in a dedicated `ps_audit_log` table (or a generic ERP audit log service).
    *   **Key data to audit:**
        *   Creation, deletion, and significant updates to `ps_projects_definition`, `ps_wbs_elements`, `ps_network_activities`, `ps_milestones`.
        *   Changes to statuses (system and user).
        *   Budget creation, supplements, returns, transfers.
        *   Baseline creation.
        *   Key date changes (planned, actual, forecast).
        *   Progress confirmations.
        *   Settlement rule changes and settlement runs.
    *   **Log details:** User performing the action (`core_user_id`), timestamp, entity type, entity ID, action performed (Create, Update, Delete), and ideally a JSON snapshot of changed fields (old and new values).
*   **Accessibility:** Provide an interface (for authorized administrators/auditors) to view and query audit logs related to specific projects or entities.

## 4. Data Consistency Across Integrated Modules

Given PS's deep integrations, maintaining data consistency is paramount.

*   **Financial Data (PS & Fina):**
    *   Actual costs and revenues for projects are primarily recorded in Fina (CO actual line items table, GL documents) with account assignment to PS WBS elements/activities.
    *   PS will either directly query these Fina tables (via internal Fina services/APIs) for reporting or consume events from Fina to update its own summarized/cached actuals (e.g., on `ps_wbs_elements.total_cost_actual`).
    *   Implement reconciliation reports or checks to compare PS planned/budgeted figures with Fina actuals and commitments.
    *   Ensure settlement and result analysis postings from PS to Fina are transactional and handle errors robustly (e.g., using database transactions for the Fina posting part, and mechanisms to retry or flag errors if an entire multi-step process fails).
*   **Logistics Data (PS & LSCM):**
    *   Ensure that material reservations, purchase requisitions, and purchase orders created from PS are accurately reflected and tracked in LSCM MM. Use unique reference IDs.
    *   Goods movements in LSCM MM related to projects must correctly update project stock (if applicable) and trigger cost postings that reflect in PS.
    *   Sales orders in LSCM SD linked to projects must have consistent status and billing information that PS can consume for revenue planning and recognition.
*   **HR Data (PS & HR):**
    *   Personnel assignments and time confirmations must be consistent. PS relies on HR for master data of employees and potentially work center definitions.
*   **Eventual Consistency for Asynchronous Processes:**
    *   For integrations relying on message queues, design listeners to be idempotent.
    *   Implement robust error handling, retry mechanisms, and dead-letter queues (DLQs) for event processing to handle transient failures or data issues.
    *   Provide tools or reports for monitoring the status of asynchronous integrations and identifying any discrepancies that require manual intervention.

## 5. Error Handling

*   **User Interface (UI):**
    *   Provide clear, user-friendly error messages in the UI when an operation fails or validation rules are violated. Avoid exposing technical stack traces to end-users.
*   **Backend (PHP):**
    *   Implement robust exception handling within PS services and controllers.
    *   Define custom, specific exceptions for PS domain errors (e.g., `BudgetExceededException`, `InvalidProjectStatusTransitionException`).
    *   Log all critical errors and exceptions with sufficient context (user, input data, stack trace) for troubleshooting using Laravel's logging facilities.
*   **Integration Points:**
    *   Properly handle errors from API calls to other modules (Fina, LSCM, HR) or external services (e.g., network issues, validation errors from the other service).
    *   Implement retry logic for transient errors in synchronous calls where appropriate (e.g., using Laravel's HTTP client retries).
    *   For event-driven integrations, ensure failed event processing is handled gracefully (logged, moved to DLQ, alerts generated).

Addressing these technical considerations proactively will contribute to a PS module that is not only functionally rich but also performant, secure, reliable, and maintainable.
