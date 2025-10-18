<?php

namespace Modules\Analytics\Core\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Analytics\Core\Application\DashboardService;

class DashboardController extends Controller
{
    private $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function getPOSDashboard()
    {
        return $this->dashboardService->getPOSDashboardData();
    }

    public function getFinanceDashboard()
    {
        return $this->dashboardService->getFinanceDashboardData();
    }

    public function getInventoryDashboard()
    {
        return $this->dashboardService->getInventoryDashboardData();
    }

    public function getCRMDashboard()
    {
        return $this->dashboardService->getCRMDashboardData();
    }
}