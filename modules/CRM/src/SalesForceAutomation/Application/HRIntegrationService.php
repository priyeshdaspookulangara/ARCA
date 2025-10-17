<?php

namespace Modules\CRM\SalesForceAutomation\Application;

class HRIntegrationService
{
    /**
     * Get sales agents from the HR module.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getSalesAgents()
    {
        // In a real implementation, this would make a call to the HR module's API
        // or query its database to get a list of sales agents.
        // For now, we will return a dummy collection.
        return collect([
            ['id' => 1, 'name' => 'John Doe'],
            ['id' => 2, 'name' => 'Jane Smith'],
        ]);
    }
}