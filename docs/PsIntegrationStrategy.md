# "PS" Module: Integration Strategy

This document outlines the integration strategy for the Project System (PS) module with other ERP modules including Fina (FI/CO), LSCM (MM, SD, PP, PM), and HR. The strategy emphasizes deep integration for seamless process flow while maintaining modular design principles.

## 1. Core Integration Principles

*   **Decoupling through Services and Events:** PS will interact with other modules primarily through well-defined service interfaces (internal PHP contracts) and asynchronous events (message queues) to minimize direct dependencies.
*   **Explicit Contracts:** All interactions will use explicit, versioned contracts (DTOs for API/event payloads, PHP interfaces).
*   **Central Role of PS for Project Data:** PS is the master for project definitions, structures (WBS, Networks), and project-specific planning data (dates, costs, resources). Other modules provide actuals or execute processes triggered by PS.
*   **Data Consistency:** Focus on mechanisms ensuring eventual consistency for asynchronous processes and validation checks for synchronous interactions.
*   **Idempotency:** Event listeners and API endpoints involved in PS integrations must be idempotent.

## 2. Integration with "Fina" (Financial Accounting & Controlling)

PS is tightly integrated with Fina for all financial aspects of projects.

*   **Cost & Revenue Postings:**
    *   **Actual Costs:**
        *   External material/service costs from LSCM MM (via POs linked to PS WBS/activities) will be posted to Fina AP, which then posts actual costs to PS WBS/activities (as CO objects) and relevant GL accounts.
        *   Internal material consumption (goods issues from LSCM MM to PS WBS/activities) will trigger postings in Fina GL (inventory reduction, expense to project) and update PS actual costs.
        *   Labor costs (from HR/Time Management confirmations valued at rates from Fina CO/HR) will be posted to PS WBS/activities in Fina CO.
        *   Other direct FI postings assigned to a PS WBS element will update project actuals.
        *   **Mechanism:** PS WBS elements and relevant network activities will be valid CO account assignment objects. Fina receives these account assignments during postings from other modules or direct FI entries. PS subscribes to `FinaActualCostPostedToProjectEvent` or similar to update its own reporting views if needed beyond what Fina CO provides.
    *   **Actual Revenues:**
        *   Project-related billing documents from LSCM SD (linked to PS WBS billing elements or milestones) will trigger revenue postings in Fina FI/CO.
        *   PS subscribes to `FinaActualRevenuePostedToProjectEvent` or similar.
*   **Cost Element Accounting:** All project costs will be tracked by cost elements within Fina CO, linked to the originating PS activities/WBS elements.
*   **Budget Integration & Availability Control (AVC):**
    *   PS project budgets (defined per WBS element) are made available to Fina CO.
    *   Fina CO's AVC functionality will check against these PS budgets for relevant cost postings (commitments from POs, actuals from invoices/goods issues/confirmations).
    *   PS can query Fina for budget consumption details.
*   **Asset Under Construction (AUC) Management:**
    *   For capital investment projects, specific WBS elements in PS will be designated to collect costs for AUCs.
    *   **Periodic Settlement (PS -> Fina FI-AA):** PS settlement rules will define how costs from these WBS elements are periodically transferred to an AUC master record in Fina FI-AA.
    *   **Final Settlement (PS -> Fina FI-AA):** Upon project completion, the accumulated value on the AUC is settled to one or more final fixed asset master records in Fina FI-AA. PS triggers this settlement.
*   **Profitability Analysis (CO-PA):**
    *   Project revenues and costs (including settled costs from WIP and AUCs) will flow to Fina CO-PA.
    *   PS WBS elements can be assigned to profitability segments, or derivation rules in CO-PA can determine the segment based on project attributes (e.g., project type, customer linked to project).
*   **Settlement (Period-End Closing in PS):**
    *   PS defines settlement rules for WBS elements and network orders (if they carry costs/revenues).
    *   The PS settlement process calculates amounts and triggers postings in Fina CO to transfer project costs/revenues to other CO objects (cost centers, other projects, profitability segments, sales orders) or GL accounts (for balance sheet items like WIP).
*   **Result Analysis (RA - Period-End Closing in PS):**
    *   PS calculates Work In Progress (WIP), reserves for unrealized costs, or recognizes revenue based on defined RA keys and methods (e.g., Percentage of Completion).
    *   PS triggers postings of these RA results to specific GL accounts in Fina FI (e.g., WIP assets, accrued revenue, cost of sales adjustments).

## 3. Integration with "LSCM" (Logistics & Supply Chain Management)

PS integrates with various LSCM sub-modules for materials, services, sales, production, and maintenance related to projects.

*   **LSCM MM (Materials Management):**
    *   **Purchase Requisitions/Orders for External Needs:**
        *   PS network activities (for external services or non-stock materials) will generate Purchase Requisitions (PRs) in LSCM MM. The PR will include the PS WBS element/activity as the account assignment.
        *   LSCM MM converts PRs to Purchase Orders (POs), retaining the project account assignment.
    *   **Goods Receipts & Invoice Verification:**
        *   GRs against project POs in LSCM MM will trigger value-based updates to PS actual costs (via Fina).
        *   Logistics Invoice Verification (LIV) in LSCM MM for project POs will also update PS actual costs (via Fina AP).
    *   **Stock Material Assignment & Issue:**
        *   PS network activities can specify requirements for stock materials.
        *   This creates a reservation in LSCM MM inventory against the project WBS/activity.
        *   Goods Issues from LSCM MM to the project (consuming the reservation) will update material consumption actual costs in PS (via Fina).
*   **LSCM SD (Sales and Distribution):**
    *   **Customer Projects & Sales Orders:**
        *   LSCM SD sales orders can be account-assigned to PS WBS elements (especially for make-to-order or engineer-to-order scenarios). This links the sales demand directly to a project.
        *   Alternatively, PS can be the starting point for a customer project, and relevant project WBS elements/milestones can trigger the creation of sales documents in LSCM SD for billing.
    *   **Project-Based Billing:**
        *   PS defines billing plans (e.g., milestone-based, percentage-of-completion based on progress).
        *   When a billing milestone is reached or progress confirmed in PS, it triggers a request to LSCM SD to create a billing document (invoice, debit/credit memo) for the customer.
        *   `PsRequestBillingEvent` -> LSCM SD creates billing doc -> `LscmCustomerBillingDocumentCreatedEvent` -> Fina AR/GL.
    *   **Revenue Recognition Alignment:** PS revenue recognition calculations should align with the billing status from LSCM SD and actual revenue postings in Fina.
*   **LSCM PP (Production Planning):**
    *   **Production for Projects (Make-to-Project):**
        *   PS WBS elements representing items to be manufactured for the project can trigger demand in LSCM PP.
        *   LSCM PP creates production orders account-assigned to the PS WBS element.
    *   **Material Requirements from Projects:** MRP runs in LSCM PP will consider material demands originating from PS network activities/BOMs exploded for projects.
    *   **Production Costs:** Costs from these project-specific production orders (material consumption, labor, overhead from LSCM PP confirmations) are collected on the production order and then settle to the PS WBS element in Fina CO, updating project actuals.
*   **LSCM PM (Plant Maintenance):**
    *   **Maintenance Projects:** Large-scale maintenance activities (e.g., overhauls, shutdowns) can be managed as PS projects.
    *   PS WBS elements can group multiple LSCM PM work orders.
    *   Costs from these PM orders (labor, spare parts, external services) are collected on the PM order and can be settled to the overarching PS WBS element in Fina CO, thus consolidating all maintenance project costs in PS.

## 4. Integration with "HR" (Human Resources)

*   **Resource Availability & Skills:**
    *   When planning resources for PS network activities, PS can make API calls to HR to query employee availability (considering calendars, leave) and potentially skills profiles to find suitable personnel.
*   **Time Confirmation:**
    *   Actual labor hours spent by employees on project activities/WBS elements are typically recorded in a central Time Management system (which could be part of HR or a dedicated module).
    *   **Event/API:** The Time Management system, upon approval of timesheets, will publish `TimeSheetApprovedEvent` or call APIs in both PS (to update actual work, progress) and Fina CO (to post labor costs to projects).
*   **Payroll Cost Allocation:**
    *   HR Payroll processes calculate personnel costs. If employees have worked on projects, a portion of their payroll cost needs to be allocated to those projects.
    *   This allocation is typically handled by Fina CO, using the confirmed times (from Time Management) and cost rates (from HR/Fina CO). PS reflects these allocated actual costs.

## 5. API Design for PS Module

*   **Internal Service APIs (PHP Interfaces):**
    *   PS will expose well-defined PHP interfaces for its core services (e.g., `ProjectService`, `WbsService`, `NetworkService`, `BudgetService`).
    *   Other modules can use these interfaces for synchronous queries (e.g., `getProjectStatus(projectId)`) or to trigger specific PS actions (e.g., `createSimpleProjectFromTemplate(templateId, basicData)`).
*   **External RESTful APIs (Consideration):**
    *   For potential integration with external project management tools (e.g., Microsoft Project for schedule import/export, JIRA for issue tracking linked to activities), PS may expose a limited set of secure, versioned RESTful APIs.
    *   Authentication would use standard ERP mechanisms (OAuth2).

## 6. Event-Driven Communication involving PS

PS will be both a publisher and subscriber of key business events.

*   **Events Published by PS:**
    *   `PsProjectCreatedEvent`
    *   `PsProjectStatusChangedEvent` (e.g., Released, Technically Completed, Closed)
    *   `PsWbsBudgetExceededEvent` (if AVC triggers a significant budget issue)
    *   `PsMilestoneReachedEvent` (can trigger billing in SD or other workflows)
    *   `PsMaterialRequirementCreatedEvent` (for LSCM MM to pick up)
    *   `PsServiceRequirementCreatedEvent` (for LSCM MM to create PR for external service)
*   **Events Subscribed to by PS:**
    *   `FinaActualCostPostedToProjectEvent` (from Fina)
    *   `FinaActualRevenuePostedToProjectEvent` (from Fina)
    *   `LscmMaterialIssuedToProjectEvent` (from LSCM MM)
    *   `LscmServiceConfirmationForProjectEvent` (from LSCM MM for external services)
    *   `HRTimeConfirmedForProjectEvent` (from Time Management/HR)
    *   `LscmSalesOrderLinkedToProjectEvent` (from LSCM SD)

This robust integration strategy ensures that PS functions as a central coordinating module for all project-related activities and data across the ERP landscape.
