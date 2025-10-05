<?php

namespace Modules\HR\Payroll\Application\Services;

use DateTime;
use Modules\HR\Payroll\Domain\Entities\PayrollRun;
use Modules\HR\Payroll\Domain\Entities\Paycheck;
use Modules\HR\Payroll\Domain\Repositories\PayrollRunRepositoryInterface;
use Modules\HR\Payroll\Domain\Repositories\PaycheckRepositoryInterface;
use Modules\HR\PersonnelAdmin\Domain\Repositories\EmployeeRepositoryInterface;
use Modules\HR\TimeManagement\Domain\Repositories\TimeRecordRepositoryInterface;
use Modules\HR\Payroll\Domain\Events\PayrollRunCompletedEvent;
use Illuminate\Contracts\Events\Dispatcher;

class PayrollService
{
    private $payrollRunRepository;
    private $paycheckRepository;
    private $employeeRepository;
    private $timeRecordRepository;
    private $eventDispatcher;

    public function __construct(
        PayrollRunRepositoryInterface $payrollRunRepository,
        PaycheckRepositoryInterface $paycheckRepository,
        EmployeeRepositoryInterface $employeeRepository,
        TimeRecordRepositoryInterface $timeRecordRepository,
        Dispatcher $eventDispatcher
    ) {
        $this->payrollRunRepository = $payrollRunRepository;
        $this->paycheckRepository = $paycheckRepository;
        $this->employeeRepository = $employeeRepository;
        $this->timeRecordRepository = $timeRecordRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function executePayrollRun(string $periodStartDate, string $periodEndDate): PayrollRun
    {
        $startDate = new DateTime($periodStartDate);
        $endDate = new DateTime($periodEndDate);

        $payrollRun = new PayrollRun(uniqid('pr_'), $startDate, $endDate);
        $this->payrollRunRepository->save($payrollRun);

        $employees = $this->employeeRepository->findAll();

        foreach ($employees as $employee) {
            if ($employee->isOnLeave()) {
                continue; // Skip employees on leave
            }

            $timeRecords = $this->timeRecordRepository->findByEmployee($employee->getId());
            $approvedHours = 0;
            foreach ($timeRecords as $record) {
                if ($record->getStatus() === 'approved' && $record->getDate() >= $startDate && $record->getDate() <= $endDate) {
                    $approvedHours += $record->getHours();
                }
            }

            if ($approvedHours > 0) {
                $hourlyRate = $employee->getSalary() / 2080; // Assuming 2080 work hours in a year
                $grossPay = $hourlyRate * $approvedHours;
                $deductions = $grossPay * 0.2; // Flat 20% deduction

                $paycheck = new Paycheck(
                    uniqid('pc_'),
                    $payrollRun->getId(),
                    $employee->getId(),
                    $grossPay,
                    $deductions
                );
                $this->paycheckRepository->save($paycheck);
                $payrollRun->addPaycheckId($paycheck->getId());
            }
        }

        $payrollRun->complete();
        $this->payrollRunRepository->save($payrollRun);

        $paychecks = $this->paycheckRepository->findByPayrollRun($payrollRun->getId());
        $this->eventDispatcher->dispatch(new PayrollRunCompletedEvent($payrollRun, $paychecks));

        return $payrollRun;
    }

    public function getPayrollRunDetails(string $payrollRunId): ?PayrollRun
    {
        return $this->payrollRunRepository->findById($payrollRunId);
    }

    public function getPaychecksForPayrollRun(string $payrollRunId): array
    {
        return $this->paycheckRepository->findByPayrollRun($payrollRunId);
    }
}