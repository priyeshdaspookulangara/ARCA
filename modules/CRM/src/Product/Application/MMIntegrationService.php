<?php

namespace Modules\CRM\Product\Application;

class MMIntegrationService
{
    /**
     * Sync products from the MM module.
     *
     * @return void
     */
    public function syncProducts()
    {
        // In a real implementation, this would make a call to the MM module's API
        // or query its database to get a list of products and update the CRM's
        // product catalog.
        \Log::info("Syncing products from MM module.");
    }
}