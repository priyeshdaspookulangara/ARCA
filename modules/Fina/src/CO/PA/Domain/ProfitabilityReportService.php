<?php

namespace Modules\Fina\CO\PA\Domain;

use Modules\Fina\CO\PA\Domain\Repositories\ProfitabilityReportRepository;

class ProfitabilityReportService
{
    private ProfitabilityReportRepository $profitabilityReportRepository;

    public function __construct(ProfitabilityReportRepository $profitabilityReportRepository)
    {
        $this->profitabilityReportRepository = $profitabilityReportRepository;
    }

    public function createProfitabilityReport(array $data): ProfitabilityReport
    {
        $profitabilityReport = new ProfitabilityReport($data);
        $this->profitabilityReportRepository->save($profitabilityReport);
        return $profitabilityReport;
    }

    public function getProfitabilityReport(int $id): ?ProfitabilityReport
    {
        return $this->profitabilityReportRepository->findById($id);
    }

    public function getAllProfitabilityReports()
    {
        return $this->profitabilityReportRepository->getAll();
    }

    public function updateProfitabilityReport(int $id, array $data): ?ProfitabilityReport
    {
        $profitabilityReport = $this->profitabilityReportRepository->findById($id);
        if ($profitabilityReport) {
            $profitabilityReport->fill($data);
            $this->profitabilityReportRepository->save($profitabilityReport);
        }
        return $profitabilityReport;
    }

    public function deleteProfitabilityReport(int $id): bool
    {
        $profitabilityReport = $this->profitabilityReportRepository->findById($id);
        if ($profitabilityReport) {
            $this->profitabilityReportRepository->delete($profitabilityReport);
            return true;
        }
        return false;
    }
}