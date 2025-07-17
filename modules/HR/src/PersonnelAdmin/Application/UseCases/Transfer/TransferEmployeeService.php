<?php

namespace Modules\HR\PersonnelAdmin\Application\UseCases\Transfer;

use Modules\HR\PersonnelAdmin\Domain\Repositories\JobAssignmentRepositoryInterface;
use Modules\HR\PersonnelAdmin\Domain\Services\EffectiveDateService;
use Modules\HR\PersonnelAdmin\Application\DTO\Transfer\TransferEmployeeRequestDto;

class TransferEmployeeService
{
    protected $jobAssignmentRepository;
    protected $effectiveDateService;

    public function __construct(
        JobAssignmentRepositoryInterface $jobAssignmentRepository,
        EffectiveDateService $effectiveDateService
    ) {
        $this->jobAssignmentRepository = $jobAssignmentRepository;
        $this->effectiveDateService = $effectiveDateService;
    }

    public function __invoke(TransferEmployeeRequestDto $request)
    {
        // 1. Initiate a PersonnelActionRequest record

        // 2. Initiate and manage a workflow instance

        // 3. Upon final workflow approval:
        //    a. Perform final business rule validations
        //    b. Manage Effective-Dated Records
        $this->jobAssignmentRepository->delimitCurrentRecord(
            $request->employeeId,
            $this->effectiveDateService->getPreviousDay($request->effectiveDate)
        );

        $this->jobAssignmentRepository->insertNewSlice([
            'employee_id' => $request->employeeId,
            'valid_from' => $request->effectiveDate,
            'valid_to' => '9999-12-31',
            'action_request_id_triggered_by' => $request->actionRequestId,
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
            'reason_for_change_code' => 'TRAN',
        ]);

        //    c. Dispatch domain events
    }
}
