<?php

namespace Modules\Fina\Listeners;

use Modules\HR\Payroll\Domain\Events\PayrollRunCompletedEvent;
use Modules\Fina\FI\AP\Application\Services\PayrollIntegrationServiceInterface;

class PostPayrollResultsListener
{
    private $payrollIntegrationService;

    public function __construct(PayrollIntegrationServiceInterface $payrollIntegrationService)
    {
        $this->payrollIntegrationService = $payrollIntegrationService;
    }

    /**
     * Handle the event.
     *
     * @param  PayrollRunCompletedEvent  $event
     * @return void
     */
    public function handle(PayrollRunCompletedEvent $event)
    {
        $totalGrossPay = 0;
        $totalDeductions = 0;

        foreach ($event->paychecks as $paycheck) {
            $totalGrossPay += $paycheck->getGrossPay();
            $totalDeductions += $paycheck->getDeductions();
        }

        $postingData = [
            'payroll_run_id' => $event->payrollRun->getId(),
            'period_start_date' => $event->payrollRun->getPeriodStartDate()->format('Y-m-d'),
            'period_end_date' => $event->payrollRun->getPeriodEndDate()->format('Y-m-d'),
            'total_gross_pay' => $totalGrossPay,
            'total_deductions' => $totalDeductions,
            'total_net_pay' => $totalGrossPay - $totalDeductions,
        ];

        $this->payrollIntegrationService->postPayrollRunResults($postingData);
    }
}