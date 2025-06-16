# "Fina" Module: Integration Strategy

This document outlines the integration strategy for the "Fina" module with existing modules (like HR) and future modules (like Sales & Distribution, Materials Management), focusing on maintaining loose coupling and supporting the ERP's modular architecture.

## 1. Integration Principles

*   **Decoupling:** Modules should have minimal direct dependencies on each other. Communication should primarily occur through well-defined contracts (PHP interfaces or API endpoints) and asynchronous events.
*   **Explicit Contracts:** Interactions between Fina and other modules will be based on clear, versioned contracts (Data Transfer Objects for events, API request/response schemas).
*   **Asynchronous First (for many cross-module processes):** Message queues will be preferred for processes that don't require an immediate synchronous response, enhancing resilience and scalability.
*   **Internal APIs for Synchronous Needs:** For requests requiring immediate feedback (e.g., data validation, credit checks), internal PHP service interfaces or REST-like internal API calls will be used.
*   **Idempotency:** Event listeners and API endpoints in Fina that perform write operations should be designed to be idempotent where possible, to safely handle message retries.

## 2. Integration with HR Module

The HR module is a default module and has several key integration points with Fina.

*   **Payroll Postings (HR Payroll to Fina GL/AP):**
    *   **Event:** HR Payroll, upon completion of a pay run, will publish an event (e.g., `PayrollRunCompletedEvent`).
    *   **Payload:** This event will contain summarized financial data:
        *   Gross pay, deductions (tax, social security, other benefits), net pay.
        *   Aggregated by relevant dimensions for financial posting (e.g., company code, cost center for expenses).
    *   **Fina Listener:** Fina will have a listener for this event.
    *   **Action in Fina:**
        *   Post salary expenses and employer contributions to the appropriate GL accounts and CO objects (e.g., cost centers defined in HR employee master data and replicated/validated in Fina).
        *   Post net pay liabilities to vendor accounts (employees as vendors) in Fina AP for disbursement.
        *   Post tax and other deduction liabilities to respective vendor/clearing accounts in Fina AP.
    *   **Error Handling:** Robust error handling and reconciliation processes will be needed to ensure payroll data posts correctly.

*   **Employee Expense Reimbursements (HR/Travel to Fina AP):**
    *   **Process:** An employee submits an expense report via HR (or a dedicated Travel Management sub-system/module). After approval, this needs to be paid and accounted for.
    *   **Event/API Call:**
        *   **Event-driven:** `ExpenseReportApprovedEvent` from HR/Travel.
        *   **API-driven:** HR/Travel calls a Fina AP internal API endpoint to submit the approved expense report.
    *   **Payload:** Expense details, employee (as vendor), amounts, cost center allocation.
    *   **Action in Fina:**
        *   Create an AP invoice (or payment request) for the employee (as a one-time vendor or linked vendor account).
        *   Post expenses to the relevant GL accounts and CO objects (cost centers, internal orders specified in the expense report).
        *   Include in the next payment run.

*   **Time-Related Cost Allocations (HR Time Management to Fina CO):**
    *   **Context:** Labor hours recorded in HR Time Management for specific projects or activities can be a source for internal cost allocations in CO.
    *   **Data Transfer:** HR could provide summarized, valued time data (e.g., hours per cost center/project * standard labor rate). This could be via:
        *   A periodic batch job that pushes data to a Fina staging table or API.
        *   An event like `ValuedTimeDataAvailableEvent`.
    *   **Action in Fina CO:** This data can be used as a basis for:
        *   Internal activity allocations in Cost Center Accounting (CCA).
        *   Direct labor cost postings to Internal Orders (IO) or Product Costing (PC) objects.

## 3. API Design for Future Module Integration

Fina must expose clear APIs for other modules to interact with its financial capabilities. These will primarily be internal PHP service interfaces, but REST-like internal APIs can also be defined.

*   **General Principles for Fina's APIs:**
    *   **Granular Services:** Expose specific functionalities rather than broad, monolithic APIs.
    *   **Interface-Driven:** Define PHP interfaces (Contracts) for services. Other modules will depend on these interfaces, resolved via Laravel's service container.
    *   **Data Transfer Objects (DTOs):** Use DTOs for request and response payloads to ensure clear structure and type safety.
    *   **Versioning:** API contracts (interfaces, DTOs, internal REST endpoints) will be versioned if breaking changes are necessary, allowing older integrations to continue functioning temporarily.
    *   **Error Handling:** Consistent error reporting mechanisms (e.g., custom exceptions, standardized error DTOs for REST-like APIs).

*   **Examples of Integration with Future Modules:**

    *   **Sales & Distribution (SD) Module Integration:**
        *   **Customer Invoicing:**
            *   **Event:** SD publishes `SalesOrderBilledEvent` (or similar) after a sales order is ready for invoicing.
            *   **Action in Fina AR:** Fina listens and creates a customer invoice in AR, posts revenue to GL/CO-PA, and manages receivables.
        *   **Credit Management:**
            *   **API Call:** SD calls a Fina AR API (`checkCustomerCredit(customerId, amount)`) during sales order creation.
            *   **Response:** Fina AR responds with credit check status (approved, denied, pending).
        *   **Incoming Payments:**
            *   Fina AR processes incoming payments. It may publish an `IncomingPaymentAppliedEvent` that SD could listen to for updating sales order statuses.

    *   **Materials Management (MM) Module Integration:**
        *   **Vendor Invoice Verification (Logistics Invoice Verification):**
            *   **Event/API:** MM, upon goods receipt (`GoodsReceiptPostedEvent`) or service entry, may trigger/provide data for Fina AP to create/verify vendor invoices against purchase orders.
            *   **Action in Fina AP:** Manages invoice processing, payment, and posts to GL/CO.
        *   **Inventory Valuation & Postings:**
            *   **Event:** MM publishes `MaterialMovementEvent` (goods issue, goods receipt, inventory transfer).
            *   **Action in Fina (GL/PC):** Fina listens and makes corresponding inventory G/L postings. Fina PC uses this data for actual costing and inventory valuation.
        *   **Material Master Data:**
            *   MM likely owns material master. Fina PC would consume material master data (e.g., valuation class) via API calls or replicated data to correctly value materials and post inventory movements.

    *   **Project System (PS) Module Integration:**
        *   **Project Budgeting & Costing:**
            *   **API Call:** PS module calls Fina IO APIs to create/update budgets for project-related internal orders.
            *   **Actual Costs:** Costs incurred for projects (e.g., from AP invoices, HR timesheets) are posted to the relevant Fina Internal Orders.
        *   **Project Settlement:**
            *   PS module might trigger Fina IO settlement processes to allocate project costs to final receivers (e.g., assets, cost centers).

## 4. Handling Cross-Module Transactions (Decoupled Approach)

*   **Asynchronous Communication via Message Queues (RabbitMQ):**
    *   This is the **preferred method** for many cross-module transactions that do not require an immediate synchronous response.
    *   **Process:**
        1.  Source module performs its action and publishes a detailed event to a specific RabbitMQ exchange/topic (e.g., `erp.sales.order_billed`).
        2.  Fina (and potentially other interested modules) subscribes to these events with dedicated queues.
        3.  Fina's event listener (consumer) processes the event, performs necessary financial transactions, and updates its own data.
    *   **Benefits:**
        *   **Decoupling:** Source module doesn't need to know about Fina's internal workings or even if Fina is currently available.
        *   **Resilience:** If Fina is temporarily down, messages queue up and are processed when Fina is back online.
        *   **Scalability:** Different parts of the system can be scaled independently.
    *   **Event Contracts:** Events will be strongly-typed DTOs, versioned to manage changes. A shared library or schema registry could define these contracts.

*   **Ensuring Data Consistency & Integrity in Asynchronous Scenarios:**
    *   **Idempotent Consumers:** Fina's event listeners should be designed to be idempotent (i.e., processing the same event multiple times has the same effect as processing it once). This is crucial for handling message retries due to transient failures.
    *   **Retry Mechanisms & Dead Letter Queues (DLQs):** Configure RabbitMQ with retry policies for transient errors. For persistent errors, messages should be moved to a DLQ for manual investigation.
    *   **Compensating Transactions (Saga Pattern - Conceptual):** For complex multi-step processes spanning modules, the Saga pattern could be considered in the long term.
        *   Initially, focus on atomicity within each module's processing of an event. If a Fina listener fails to process an `OrderBilledEvent` completely, it should roll back its own changes and allow for a retry.
        *   True distributed transactions are complex; aim for eventual consistency where appropriate.
    *   **Monitoring & Alerting:** Implement monitoring for queue lengths, processing errors, and DLQs.

*   **Synchronous Communication (Internal Service Calls):**
    *   Used when immediate feedback is essential (e.g., validation, credit checks, fetching data required for the current transaction to proceed).
    *   Implemented as calls to PHP interfaces provided by the Fina module and resolved through Laravel's service container.
    *   **Resilience Measures:**
        *   **Timeouts:** Implement timeouts for synchronous calls to prevent cascading failures.
        *   **Circuit Breaker Pattern (Consideration):** For high-traffic synchronous calls, a circuit breaker (e.g., using a library) could prevent repeated calls to an already failing Fina service.

This integration strategy aims to balance the need for robust financial processing within Fina with the flexibility and modularity required by the overall ERP architecture.
