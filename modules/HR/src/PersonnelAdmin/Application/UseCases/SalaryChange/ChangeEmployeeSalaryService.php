<?php

namespace Modules\HR\PersonnelAdmin\Application\UseCases\SalaryChange;

use Modules\HR\PersonnelAdmin\Domain\Repositories\CompensationRepositoryInterface;
use Modules\HR\PersonnelAdmin\Domain\Services\EffectiveDateService;
use Modules\HR\PersonnelAdmin\Application\DTO\SalaryChange\ChangeEmployeeSalaryRequestDto;

class ChangeEmployeeSalaryService
{
    protected $compensationRepository;
    protected $effectiveDateService;

    public function __construct(
        CompensationRepositoryInterface $compensationRepository,
        EffectiveDateService $effectiveDateService
    ) {
        $this->compensationRepository = $compensationRepository;
        $this->effectiveDateService = $effectiveDateService;
    }

    public function __invoke(ChangeEmployeeSalaryRequestDto $request)
    {
        // 1. Initiate a PersonnelActionRequest record

        // 2. Initiate and manage a workflow instance

        // 3. Upon final workflow approval:
        //    a. Perform final business rule validations
        //    b. Manage Effective-Dated Records
        $this->compensationRepository->delimitCurrentRecord(
            $request->employeeId,
            $this->effectiveDateService->getPreviousDay($request->effectiveDate)
        );

        $this->compensationRepository->insertNewSlice([
            'employee_id' => $request->employeeId,
            'valid_from' => $request->effectiveDate,
            'valid_to' => '9999-12-31',
            'action_request_id_triggered_by' => $request->actionRequestId,
            'base_salary_amount' => $request->newBaseSalaryAmount,
            'salary_currency_code' => $request->newSalaryCurrencyCode,
            'pay_frequency' => $request->newPayFrequency,
            'other_components_json' => $request->newOtherComponentsJson,
            'reason_for_change_code' => 'SALCHG',
        ]);

        //    c. Dispatch domain events
    }
}
