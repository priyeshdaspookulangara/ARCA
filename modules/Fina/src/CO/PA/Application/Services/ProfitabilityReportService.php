<?php

namespace Modules\Fina\CO\PA\Application\Services;

use Modules\Fina\CO\PA\Domain\Repositories\ProfitabilityReportRepository;

class ProfitabilityReportService
{
    protected $profitabilityReportRepository;

    public function __construct(ProfitabilityReportRepository $profitabilityReportRepository)
    {
        $this->profitabilityReportRepository = $profitabilityReportRepository;
    }

    public function getAllProfitabilityReports()
    {
        return $this->profitabilityReportRepository->getAll();
    }

    public function getProfitabilityReportById(int $id)
    {
        return $this->profitabilityReportRepository->findById($id);
    }

    public function createProfitabilityReport(array $data)
    {
        // Add any business logic/validation here before creating
        $data['gross_profit'] = $data['revenue'] - $data['cost_of_sales'];

        $detailedCostsTotal = 0;
        if (isset($data['detailed_costs']) && is_array($data['detailed_costs'])) {
            foreach ($data['detailed_costs'] as $cost) {
                $detailedCostsTotal += $cost['amount'];
            }
        }

        $data['net_profit'] = $data['gross_profit'] - $detailedCostsTotal;

        return $this->profitabilityReportRepository->create($data);
    }

    public function updateProfitabilityReport(int $id, array $data)
    {
        // Add any business logic/validation here before updating
        if (isset($data['revenue']) && isset($data['cost_of_sales'])) {
            $data['gross_profit'] = $data['revenue'] - $data['cost_of_sales'];
        }

        if (isset($data['detailed_costs']) && is_array($data['detailed_costs'])) {
            $detailedCostsTotal = 0;
            foreach ($data['detailed_costs'] as $cost) {
                $detailedCostsTotal += $cost['amount'];
            }
            if(isset($data['gross_profit'])){
                $data['net_profit'] = $data['gross_profit'] - $detailedCostsTotal;
            }
        }

        return $this->profitabilityReportRepository->update($id, $data);
    }

    public function deleteProfitabilityReport(int $id)
    {
        // Add any business logic/validation here before deleting
        return $this->profitabilityReportRepository->delete($id);
    }

    public function getProfitabilityReportsByMarketSegment(int $marketSegmentId)
    {
        return $this->profitabilityReportRepository->findByMarketSegment($marketSegmentId);
    }
}