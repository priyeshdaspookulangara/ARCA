<?php

namespace Modules\HR\PersonnelAdmin\Application\UseCases\Promotion;

use Modules\HR\PersonnelAdmin\Domain\Repositories\JobAssignmentRepositoryInterface;
use Modules\HR\PersonnelAdmin\Domain\Repositories\CompensationRepositoryInterface;
use Modules\HR\PersonnelAdmin\Domain\Services\EffectiveDateService;
use Modules\HR\PersonnelAdmin\Application\DTO\Promotion\PromoteEmployeeRequestDto;
use Modules\HR\PersonnelAdmin\Domain\Repositories\PersonnelActionRequestRepositoryInterface;
use Symfony\Component\Workflow\Registry;
use Illuminate\Support\Facades\Event;
use Modules\HR\PersonnelAdmin\Domain\Events\EmployeePromoted;

class PromoteEmployeeService
{
    protected $jobAssignmentRepository;
    protected $compensationRepository;
    protected $effectiveDateService;
    protected $personnelActionRequestRepository;
    protected $workflowRegistry;

    public function __construct(
        JobAssignmentRepositoryInterface $jobAssignmentRepository,
        CompensationRepositoryInterface $compensationRepository,
        EffectiveDateService $effectiveDateService,
        PersonnelActionRequestRepositoryInterface $personnelActionRequestRepository,
        Registry $workflowRegistry
    ) {
        $this->jobAssignmentRepository = $jobAssignmentRepository;
        $this->compensationRepository = $compensationRepository;
        $this->effectiveDateService = $effectiveDateService;
        $this->personnelActionRequestRepository = $personnelActionRequestRepository;
        $this->workflowRegistry = $workflowRegistry;
    }

    public function __invoke(PromoteEmployeeRequestDto $request)
    {
        $personnelActionRequest = $this->personnelActionRequestRepository->create([
            'request_number' => uniqid(),
            'employee_id' => $request->employeeId,
            'action_type_id' => 1, // Assuming 1 is for Promotion
            'requested_effective_date' => $request->effectiveDate,
            'status' => 'draft',
            'initiator_user_id' => auth()->id(),
            'proposed_data_snapshot_json' => json_encode($request),
            'created_by_user_id' => auth()->id(),
            'updated_by_user_id' => auth()->id(),
        ]);

        $workflow = $this->workflowRegistry->get($personnelActionRequest, 'promotion');
        $workflow->apply($personnelActionRequest, 'submit_for_approval');
        $this->personnelActionRequestRepository->update($personnelActionRequest->id, ['status' => $personnelActionRequest->getStatus()]);

        // The rest of the logic will be handled by workflow listeners
    }

    public function implementPromotion(int $personnelActionRequestId)
    {
        $personnelActionRequest = $this->personnelActionRequestRepository->findById($personnelActionRequestId);
        $request = json_decode($personnelActionRequest->proposed_data_snapshot_json);

        $this->jobAssignmentRepository->delimitCurrentRecord(
            $request->employeeId,
            $this->effectiveDateService->getPreviousDay($request->effectiveDate)
        );

        $this->jobAssignmentRepository->insertNewSlice([
            'employee_id' => $request->employeeId,
            'valid_from' => $request->effectiveDate,
            'valid_to' => '9999-12-31',
            'action_request_id_triggered_by' => $personnelActionRequestId,
            'position_id' => $request->newPositionId,
            'job_title_id' => $request->newJobTitleId,
            'department_id' => $request->newDepartmentId,
            'cost_center_id' => $request->newCostCenterId,
            'company_code_id' => $request->newCompanyCodeId,
            'personnel_area_id' => $request->newPersonnelAreaId,
            'personnel_sub_area_id' => $request->newPersonnelSubAreaId,
            'employee_group_id' => $request->newEmployeeGroupId,
            'employee_sub_group_id' => $request->newEmployeeSubGroupId,
            'manager_core_user_id' => $request->newManagerCoreUserId,
            'employment_status_id' => $request->newEmploymentStatusId,
            'reason_for_change_code' => 'PROM',
        ]);

        $this->compensationRepository->delimitCurrentRecord(
            $request->employeeId,
            $this->effectiveDateService->getPreviousDay($request->effectiveDate)
        );

        $this->compensationRepository->insertNewSlice([
            'employee_id' => $request->employeeId,
            'valid_from' => $request->effectiveDate,
            'valid_to' => '9999-12-31',
            'action_request_id_triggered_by' => $personnelActionRequestId,
            'base_salary_amount' => $request->newBaseSalaryAmount,
            'salary_currency_code' => $request->newSalaryCurrencyCode,
            'pay_frequency' => $request->newPayFrequency,
            'other_components_json' => $request->newOtherComponentsJson,
            'reason_for_change_code' => 'PROM',
        ]);

        $workflow = $this->workflowRegistry->get($personnelActionRequest, 'promotion');
        $workflow->apply($personnelActionRequest, 'implement');
        $this->personnelActionRequestRepository->update($personnelActionRequest->id, ['status' => $personnelActionRequest->getStatus()]);

        Event::dispatch(new EmployeePromoted($personnelActionRequest->employee_id, $personnelActionRequestId));
    }
}
