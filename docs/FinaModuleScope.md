# "Fina" Module: Scope and Core Functionalities

This document defines the scope and core functionalities for the "Fina" module, which encompasses capabilities found in SAP Financial Accounting (FI) and Controlling (CO).

## I. Financial Accounting (FI)

### 1. General Ledger (GL) Accounting
*   **Chart of Accounts Management:**
    *   Define and manage multiple charts of accounts (operational, group, country-specific).
    *   Hierarchical structure for GL accounts.
    *   Account attributes (P&L account, balance sheet account, account type, reconciliation account, etc.).
*   **Real-time Journal Entries & Posting:**
    *   Manual GL postings with double-entry bookkeeping.
    *   Automated postings from subsidiary ledgers (AP, AR, AA).
    *   Document principle (unique document numbers, posting date, document date, audit trail).
    *   Accrual/deferral postings.
    *   Recurring entries.
    *   Reversal of documents.
*   **Financial Statement Generation:**
    *   Real-time generation of Balance Sheets.
    *   Real-time generation of Profit & Loss (P&L) Statements.
    *   Generation of Cash Flow Statements (direct or indirect method).
    *   Support for different accounting principles (e.g., IFRS, GAAP).
    *   Version management for financial statements.
*   **Parallel Accounting:** Maintain multiple ledgers for different accounting principles.
*   **Closing Operations:** Support for month-end and year-end closing processes (e.g., balance carry forward, foreign currency revaluation, regrouping).

### 2. Accounts Payable (AP)
*   **Vendor Master Data Management:**
    *   Maintain vendor information (address, bank details, payment terms, tax information).
    *   Link to general ledger reconciliation accounts.
    *   Vendor account groups.
*   **Invoice Processing (Vendor Invoices):**
    *   Invoice entry and verification (with or without purchase order reference).
    *   Handling of different tax codes and calculation.
    *   Credit memos and debit memos.
    *   Blocking invoices for payment.
*   **Payment Processing:**
    *   Automatic payment program (payment runs for due items).
    *   Manual outgoing payments.
    *   Support for various payment methods (bank transfer, checks).
    *   Down payment processing.
*   **Vendor Reconciliation & Reporting:**
    *   Vendor account balances and line items display.
    *   Open item management.
    *   Vendor correspondence (e.g., account statements).
    *   GR/IR (Goods Receipt/Invoice Receipt) clearing account analysis.

### 3. Accounts Receivable (AR)
*   **Customer Master Data Management:**
    *   Maintain customer information (address, bank details, payment terms, credit limits, tax information).
    *   Link to general ledger reconciliation accounts.
    *   Customer account groups.
*   **Invoice Creation & Processing (Customer Invoices):**
    *   Invoice entry (with or without sales order reference from an SD module).
    *   Handling of different tax codes and calculation.
    *   Credit memos and debit memos (customer).
*   **Incoming Payments Processing:**
    *   Manual and automatic clearing of open items.
    *   Processing of bank statements for cash application.
    *   Down payment processing.
    *   Handling of payment differences.
*   **Credit Management:**
    *   Define and monitor customer credit limits.
    *   Automatic credit checks during order entry (integration point with SD module).
*   **Dunning:**
    *   Automated dunning process for overdue receivables.
    *   Configurable dunning levels and procedures.
    *   Generation of dunning notices.
*   **Customer Reconciliation & Reporting:**
    *   Customer account balances and line items display.
    *   Open item management.
    *   Customer correspondence (e.g., account statements, payment reminders).

### 4. Asset Accounting (AA)
*   **Fixed Asset Master Data Management:**
    *   Maintain asset master records (description, location, cost center, depreciation terms).
    *   Asset classes and groups.
    *   Link to GL accounts.
*   **Asset Lifecycle Management:**
    *   **Acquisition:** Posting asset purchases (integrated with AP or direct).
    *   **Retirement:** Posting asset sales, scrapping (with revenue/loss calculation).
    *   **Transfers:** Intra-company and inter-company asset transfers.
    *   **Capitalization of Assets under Construction (AuC):** Settlement of AuC to final assets.
*   **Depreciation Management:**
    *   Configurable depreciation methods (straight-line, declining balance, etc.).
    *   Automatic depreciation calculation and posting runs.
    *   Support for multiple depreciation areas (e.g., book, tax).
*   **Revaluation:** Posting asset revaluations.
*   **Asset Reporting:**
    *   Asset history sheet.
    *   Depreciation forecasts.
    *   Asset balances.

### 5. Bank Accounting (BL)
*   **Bank Master Data & Account Management:**
    *   Maintain bank master data (bank keys, addresses).
    *   Manage house bank accounts and their GL account links.
*   **Cash Journals:** Processing petty cash transactions.
*   **Bank Reconciliation:**
    *   Manual and electronic bank statement processing.
    *   Matching bank transactions with system transactions.
    *   Posting of unrecorded items.
*   **Payment Medium Workbench (Integration with AP/AR):** Generating payment files for banks.

### 6. Funds Management (FM) - *Integration Focus*
*   **Scope:** Primarily for public sector or organizations requiring strict budget control.
*   **Integration Points with Fina:**
    *   Budget availability checks during FI postings (e.g., AP invoices, GL postings).
    *   Commitment tracking from purchasing (integration with MM module).
    *   Revenue and expenditure budget management.
    *   If FM is a separate module, Fina will provide APIs for budget consumption updates and receive budget check results. If part of Fina, it's tightly integrated.

### 7. Travel Management (TR) - *Integration Focus*
*   **Scope:** Managing employee travel requests, planning, and expense reporting.
*   **Integration Points with Fina:**
    *   Posting of employee travel expenses from TR to Fina AP (vendor account for employee or clearing account).
    *   Posting to appropriate cost centers (CO) or internal orders (CO).
    *   If TR is a separate module, Fina AP will provide APIs to receive expense reports for payment. If part of Fina (less common for core FI/CO), it's more integrated.

## II. Controlling (CO)

### 1. Cost Element Accounting (CEL)
*   **Master Data:**
    *   Manage primary cost elements (derived from FI GL expense/revenue accounts).
    *   Manage secondary cost elements (for internal allocations within CO).
*   **Real-time Posting:**
    *   Primary costs automatically flow from FI postings to relevant CO objects (cost centers, internal orders, etc.).
    *   Secondary costs are posted via CO internal allocations (e.g., assessments, distributions).
*   **Reporting:** Cost element reports showing origins and destinations of costs/revenues.

### 2. Cost Center Accounting (CCA)
*   **Master Data:**
    *   Define and manage cost center hierarchies.
    *   Assign cost centers to organizational units.
*   **Overhead Cost Control & Planning:**
    *   Planning costs and activities at the cost center level.
    *   Budgeting for cost centers.
*   **Actual Postings:** Accumulation of actual costs on cost centers from FI and internal CO allocations.
*   **Variance Analysis:** Comparing planned costs with actual costs, identifying variances.
*   **Allocations:**
    *   Distribution of primary costs.
    *   Assessment of secondary costs.
    *   Activity allocation.

### 3. Internal Orders (IO)
*   **Master Data:** Define and manage internal orders (e.g., for specific projects, events, capital investments).
    *   Order types (overhead orders, investment orders, orders with revenue).
*   **Budgeting & Planning:** Assign budgets to internal orders and plan costs/revenues.
*   **Actual Postings:** Accumulate actual costs and revenues on internal orders.
*   **Monitoring & Reporting:** Track spending against budget, analyze variances.
*   **Settlement:** Settle costs collected on internal orders to other CO objects (cost centers, assets, profitability segments, etc.).

### 4. Product Cost Controlling (PC)
*   **Cost Calculation for Products/Services:**
    *   Material costing (using Bill of Materials - BOMs, and routings from a production planning module).
    *   Calculation of Cost of Goods Manufactured (COGM) and Cost of Goods Sold (COGS).
    *   Overhead costing using costing sheets.
*   **Inventory Valuation:**
    *   Standard cost valuation.
    *   Actual cost valuation (e.g., moving average, FIFO - requires MM integration).
*   **Cost Object Controlling:** Controlling costs for manufacturing orders or product cost collectors.
*   **Profitability Analysis for Products:** Analyzing the profitability of individual products.

### 5. Profitability Analysis (CO-PA)
*   **Market Segment Analysis:**
    *   Define profitability segments based on characteristics (e.g., product, customer, region, sales channel).
    *   Analyze profitability for these segments.
*   **Data Collection:**
    *   Collect revenues and costs of sales from billing documents (SD integration).
    *   Collect other costs relevant to profitability from FI and other CO modules.
*   **Reporting & Decision Support:** Generate reports for management to make decisions on pricing, product mix, customer strategy, etc.
    *   Account-based CO-PA (integrated with GL accounts).
    *   Costing-based CO-PA (value field based, allows for more flexible analysis).

### 6. Profit Center Accounting (PCA)
*   **Master Data:** Define and manage profit center hierarchies (representing independent organizational units responsible for their own profits/losses).
*   **Internal Profit/Loss Measurement:**
    *   Assign balance sheet items and P&L items to profit centers.
    *   Transfer pricing for internal goods/services exchange between profit centers.
*   **Reporting:** Generate profit center balance sheets and P&L statements for internal management.

### 7. Activity-Based Costing (ABC) - *Consideration*
*   **Scope:** More detailed cost allocation method where activities consume resources, and cost objects (products, services) consume activities.
*   **Integration:**
    *   Define activities and activity drivers.
    *   Allocate costs from cost centers to activities, and then from activities to cost objects.
    *   If implemented, it would be tightly integrated with CCA and PC. This can be an advanced feature.

This detailed scope will guide the subsequent design and development phases for the "Fina" module.
