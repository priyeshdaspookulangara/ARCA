# ARCA Analytics Data Lake Structure

This document outlines the structure of the data lake for the ARCA Analytics module. The data lake is organized into three layers: raw, curated, and marts.

## Raw Layer

The raw layer contains unprocessed data ingested from various source systems. This data is stored in its original format and is immutable.

-   **raw/pos/sales:** Contains raw sales transaction data from the POS module.
-   **raw/crm/loyalty:** Contains raw loyalty event data from the CRM module.
-   **raw/sd/orders:** Contains raw sales order data from the SD module.
-   **raw/fina/journals:** Contains raw journal entry data from the FINA module.
-   **raw/mm/stock:** Contains raw stock snapshot data from the MM module.

## Curated Layer

The curated layer contains cleaned, transformed, and enriched data that is ready for analysis. This data is typically stored in a more structured format, such as Parquet.

-   **curated/facts/sales:** Contains the `facts_sales` table.
-   **curated/facts/payments:** Contains the `facts_payments` table.
-   **curated/facts/stock_movements:** Contains the `facts_stock_movements` table.
-   **curated/dimensions/customers:** Contains the `dim_customers` table.
-   **curated/dimensions/products:** Contains the `dim_products` table.
-   **curated/dimensions/stores:** Contains the `dim_stores` table.
-   **curated/dimensions/date:** Contains the `dim_date` table.

## Marts Layer

The marts layer contains aggregated and summarized data that is optimized for specific business use cases.

-   **marts/sales/daily_store_sales:** Contains daily sales summaries by store.
-   **marts/crm/customer_rfm:** Contains RFM (Recency, Frequency, Monetary) scores for customers.
-   **marts/inventory/stock_ageing:** Contains stock ageing reports.