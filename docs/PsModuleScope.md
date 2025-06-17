# "PS" Module: Scope and Core Functionalities

This document defines the scope and core functionalities for the Project System (PS) module. The PS module is designed for full lifecycle management of projects, from planning and budgeting to execution, monitoring, and settlement, with deep integration into other ERP modules.

## I. Project Structuring

### 1.1. Work Breakdown Structure (WBS) Management
*   **Creation & Management:** Ability to create, modify, delete, and manage hierarchical WBS elements.
*   **WBS Element Attributes:** Each WBS element must support:
    *   Unique ID (system-generated or user-defined, with validation).
    *   Description (short and long text).
    *   Start Dates (Planned, Forecast, Actual).
    *   End Dates (Planned, Forecast, Actual).
    *   Person Responsible (link to `core_users` or HR employee master).
    *   Costs (Planned, Budgeted, Actual - actuals will flow from Fina/MM).
    *   Revenue (Planned, Actual - actuals will flow from SD/Fina).
    *   Link to parent WBS element.
    *   Organizational assignments (e.g., Company Code, Profit Center, Business Area from Fina).
    *   Status (system and user statuses).
    *   Flags (e.g., billing element, account assignment element, planning element).
*   **Hierarchy:** Support for multiple levels of WBS hierarchy (e.g., Project -> Phase -> Task -> Sub-task).
*   **Templates:** Ability to create projects/WBS from predefined templates.

### 1.2. Network Diagram Management
*   **Network Definition:** Functionality to define project networks, which consist of activities and their relationships.
*   **Activity Linking:** Networks link to WBS elements (an activity is performed for a WBS element).
*   **Dependencies:** Establish dependencies between network activities:
    *   Finish-to-Start (FS)
    *   Start-to-Start (SS)
    *   Finish-to-Finish (FF)
    *   Start-to-Finish (SF)
*   **Lead/Lag Times:** Define lead times (overlap) or lag times (delay) between dependent activities.
*   **Graphical Representation:** System should support or provide data for a graphical representation of the network flow (e.g., PERT chart style).

### 1.3. Activity Management
*   **Definition:** Define individual project activities linked to WBS elements.
*   **Activity Attributes:**
    *   Unique Activity Number (within network/project).
    *   Description.
    *   WBS Element Assignment.
    *   Activity Type (e.g., Internal Processing, External Processing, Service, Material Component, Cost Activity).
    *   Work Center (link to LSCM/PP Work Centers or a PS specific resource pool).
    *   Control Key (defining how an activity is processed, e.g., schedulable, costed, confirmation required).
    *   Planned Duration (e.g., in days, hours).
    *   Planned Work (e.g., in person-hours).
    *   Resource Requirements (personnel, equipment - qualitative or quantitative).
    *   Material Component Requirements (link to LSCM/MM).
    *   Dates (Planned, Forecast, Actual Start/Finish, constraints).
    *   Status.
*   **External Processing/Services:** For activities outsourced to vendors, ability to trigger procurement (PR to LSCM/MM).

### 1.4. Milestone Management
*   **Definition:** Ability to define key project milestones representing significant events or achievements.
*   **Linking:** Link milestones to WBS elements or specific activities.
*   **Milestone Attributes:**
    *   Description.
    *   Scheduled Date (Planned, Actual).
    *   Status (e.g., Open, Released, Completed).
*   **Follow-up Actions:** Ability to trigger follow-up actions upon milestone completion (e.g., milestone billing in SD, notifications, releasing next project phase).

## II. Time Scheduling

### 2.1. Gantt Chart Visualization
*   **Interactive Gantt Chart:** Provide an interactive Gantt chart for:
    *   Visual representation of project timelines.
    *   Display of WBS elements, activities, milestones, and their durations.
    *   Visualization of dependencies between activities.
    *   Progress tracking (e.g., percentage complete shown on bars).
    *   Basic drag-and-drop for date adjustments (with recalculation).
*   **Customizable Views:** Allow users to customize the Gantt chart view (e.g., time scale, displayed columns).

### 2.2. Scheduling Methods
*   **Automatic Scheduling:**
    *   System calculates planned start/finish dates for WBS elements and activities based on network logic (dependencies, durations), resource availability (basic), and project/activity calendars.
    *   Forward and backward scheduling capabilities.
*   **Manual Adjustments:** Allow authorized users to override automatically scheduled dates for specific elements, with options to show impact on dependent tasks.
*   **Constraint Dates:** Support for imposing constraint dates on activities/WBS (e.g., Must Start On, Finish No Later Than).

### 2.3. Critical Path Analysis
*   **Automated Identification:** System automatically identifies and highlights the critical path(s) in the project network.
*   **Float/Slack Calculation:** Calculate total float and free float for activities.
*   **Visualization:** Critical path clearly visible in Gantt charts and network diagrams.

### 2.4. Baseline Management
*   **Saving Baselines:** Ability to save multiple baseline schedules (snapshots of planned dates, durations, costs, work) at different points in the project lifecycle.
*   **Comparison:** Functionality to compare the current schedule against a chosen baseline to track deviations.

### 2.5. Date Types
*   Consistent tracking and display of various date types for WBS elements and activities:
    *   Planned Start/Finish
    *   Forecast Start/Finish (system or manually updated)
    *   Actual Start/Finish (from confirmations)
    *   Baseline Start/Finish

## III. Cost Planning & Budgeting

### 3.1. Detailed Cost Planning
*   **Hierarchical Planning:** Plan costs at WBS element and/or network activity levels.
*   **Cost Categories/Elements:** Support for planning costs by various cost categories/cost elements (linked to Fina CO Cost Elements):
    *   Personnel Costs (internal labor).
    *   Material Costs (components, raw materials).
    *   External Services & Subcontracting.
    *   Travel Expenses.
    *   Overheads.
*   **Planning Methods:**
    *   **Unit Costing:** Planning based on quantities and unit costs (e.g., X hours * Y rate/hour).
    *   **Overall Planning:** Entering lump-sum planned costs.
    *   Referencing historical data or estimation models (future enhancement).
*   **Time-Phased Planning:** Ability to distribute planned costs over time periods (e.g., monthly, quarterly).

### 3.2. Budget Allocation & Control
*   **Budget Definition & Allocation:**
    *   Define overall project budgets.
    *   Allocate budgets hierarchically to WBS elements.
    *   Distinguish between original budget, supplements, returns, and current budget.
*   **Availability Control (AVC):**
    *   Real-time monitoring of budget consumption against actual commitments and costs.
    *   Configurable tolerance limits for warnings and error messages (hard stops).
    *   AVC checks triggered during relevant transactions (e.g., PO creation for project, FI postings to project WBS).
*   **Budget Versions:** Support for managing different budget versions (e.g., initial, revised).

### 3.3. Cost and Revenue Tracking
*   **Actual Cost Capture:** Automated capture of actual costs posted to project WBS elements or network activities from integrated modules:
    *   Fina FI (direct FI postings, vendor invoices from AP).
    *   LSCM MM (goods issues for project, GR for project POs).
    *   HR/Time Management (confirmed labor hours valued at cost rates).
*   **Actual Revenue Capture:** Automated capture of actual revenues from project-related sales orders/billing documents (LSCM SD posting to Fina, linked to PS).
*   **Real-time Comparison:** Comparison of actual vs. planned costs and actual vs. budgeted costs/revenues.
*   **Variance Analysis Reporting:** Reports showing deviations, enabling cause analysis.

## IV. Resource Management

### 4.1. Internal Resource Planning
*   **Personnel Allocation:**
    *   Allocate internal personnel (employees linked from HR module) to project activities or WBS elements.
    *   Specify planned work hours for allocated personnel.
    *   Consideration for skills matching (integration with HR skills catalog - advanced).
*   **Equipment/Machinery Planning:**
    *   Plan usage of internal equipment/machinery (linked from LSCM/PM equipment master or a PS specific resource master) for project activities.
*   **Time Recording Integration:**
    *   Integration with a time recording system (e.g., a central Time module, CATS-like functionality, or HR Time Management) for employees to record actual hours spent on project activities/WBS.
    *   These actual hours will update project actual costs and remaining work.

### 4.2. External Resource/Service Planning
*   **Procurement Initiation:**
    *   Generate Purchase Requisitions (PRs) in LSCM MM directly from PS network activities for external services or non-stock materials required for the project.
    *   PRs will carry project account assignment (WBS element, network activity).
*   **Tracking:** Link PS activities to the corresponding PRs/POs in LSCM MM for status tracking.

### 4.3. Capacity Planning
*   **Workload Analysis:** Analyze resource (personnel, work center) availability vs. planned workload from project activities.
*   **Identification of Overloads/Underutilization:** Reports and alerts for resource capacity issues.
*   **Resource Leveling (Basic):** Tools to help manually or semi-automatically adjust activity schedules to resolve resource conflicts by shifting non-critical activities.

## V. Material Management (PS-MM Integration)

*   **Material Requirements Specification:**
    *   Assign material components (from LSCM Material Master) to network activities or BOM items within a project structure.
    *   Specify required quantities and dates.
*   **Procurement or Reservation Trigger:**
    *   For non-stock materials: Automatically generate Purchase Requisitions in LSCM MM.
    *   For stock materials: Automatically generate Reservations in LSCM MM against project stock or general plant stock.
*   **Material Consumption Tracking:**
    *   Track actual material consumption (goods issues from LSCM MM) against project activities/WBS elements.
    *   Costs of consumed materials posted to the project in Fina.

## VI. Project Execution & Monitoring

### 6.1. Progress Confirmation
*   **Recording Actuals:** Allow users (project team members, managers) to record:
    *   Actual work performed (e.g., actual hours spent).
    *   Percentage of completion for activities/WBS.
    *   Actual quantities produced/delivered for relevant activities.
    *   Actual start and finish dates.
*   **Impact on Schedule & Cost:** Confirmations update remaining work, forecast dates, and actual costs.

### 6.2. Status Management
*   **Customizable Statuses:** Define and manage system statuses and user statuses for projects, WBS elements, and network activities (e.g., Created, Released, Budgeted, Technically Completed, Closed, Locked).
*   **Status Network & Automation:** Configure automated status changes based on business transactions (e.g., releasing a WBS automatically releases its activities; confirming an activity as complete updates WBS status).
*   **Authorization Control:** Statuses can control which business transactions are allowed (e.g., costs cannot be posted to a "Closed" WBS).

### 6.3. Earned Value Management (EVM) - *Optional but Recommended*
*   **Metric Calculation:**
    *   Planned Value (PV) - Budgeted Cost of Work Scheduled (BCWS).
    *   Earned Value (EV) - Budgeted Cost of Work Performed (BCWP).
    *   Actual Cost (AC) - Actual Cost of Work Performed (ACWP).
*   **Performance Indices:**
    *   Schedule Performance Index (SPI = EV/PV).
    *   Cost Performance Index (CPI = EV/AC).
*   **Forecasting:** Estimate at Completion (EAC), Variance at Completion (VAC).

### 6.4. Issue & Risk Management (Basic)
*   **Logging:** Ability to log project-related issues and risks.
*   **Tracking:** Assign responsible persons, due dates, severity, status for issues/risks.
*   **Reporting:** Basic reports on open/overdue issues and risks.

## VII. Period-End Closing Operations

### 7.1. Settlement
*   **Settlement Rules Definition:** Define rules for how costs and revenues collected on project WBS elements or orders are settled to one or more receivers.
*   **Receivers:**
    *   Cost Centers (Fina CO-CCA).
    *   Fixed Assets (AUC settlement to final assets in Fina FI-AA).
    *   Profitability Segments (Fina CO-PA).
    *   Sales Orders (for customer projects).
    *   Other WBS elements or GL accounts.
*   **Settlement Execution:** Periodic execution of settlement runs to transfer costs/revenues.

### 7.2. Result Analysis (RA)
*   **WIP Calculation:** Automate calculation of Work In Progress (WIP) for projects based on costs incurred and progress, for capitalization on the balance sheet.
*   **Reserves for Unrealized Costs:** Calculation of reserves for costs not yet incurred but anticipated.
*   **Revenue Recognition:** Automated recognition of project revenues based on completion methods (e.g., percentage of completion, milestone completion linked to billing).
*   **Posting RA Results:** Posting results of RA (WIP, reserves, recognized revenue) to Fina GL.

This scope forms the basis for the design and development of the Project System module.
