<?php

namespace Modules\Analytics\Core\Application;

class BatchETLService
{
    /**
     * Simulate pulling data from the SD module.
     *
     * @return void
     */
    public function pullSalesData()
    {
        \Log::info("Pulling sales data from SD module.");
    }

    /**
     * Simulate pulling data from the FINA module.
     *
     * @return void
     */
    public function pullFinancialData()
    {
        \Log::info("Pulling financial data from FINA module.");
    }

    /**
     * Simulate pulling data from the MM module.
     *
     * @return void
     */
    public function pullInventoryData()
    {
        \Log::info("Pulling inventory data from MM module.");
    }
}