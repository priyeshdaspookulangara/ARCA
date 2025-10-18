# ARCA Analytics Data Model & ETL Process

This document provides an overview of the data model and the ETL (Extract, Transform, Load) process for the ARCA Analytics module.

## Data Model

The data model for the Analytics module is a dimensional model, consisting of dimension and fact tables.

### Dimensions

-   **dim_customers:** Stores customer information.
-   **dim_products:** Stores product information.
-   **dim_stores:** Stores store information.
-   **dim_date:** Stores date information.

### Facts

-   **facts_sales:** Stores sales transaction data.
-   **facts_payments:** Stores payment transaction data.
-   **facts_stock_movements:** Stores stock movement data.

## ETL Process

The ETL process is responsible for extracting data from the source systems, transforming it into the dimensional model, and loading it into the data warehouse.

### Real-time Ingestion

-   **Source:** `SaleCompletedEvent` from the POS module.
-   **Process:** An event listener (`IngestSaleData`) subscribes to the `SaleCompletedEvent` and inserts the data into the `facts_sales` table in near-real-time.

### Batch ETL

-   **Source:** SD, FINA, and MM modules.
-   **Process:** A scheduled job (`BatchETLService`) runs periodically (e.g., nightly) to pull data from the source modules, transform it, and load it into the corresponding dimension and fact tables.
-   **Example:** The `pullSalesData` method in the `BatchETLService` would be responsible for pulling sales data from the SD module and updating the `facts_sales` table.