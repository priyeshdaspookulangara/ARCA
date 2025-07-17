<?php

namespace Modules\HR\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\HR\PersonnelAdmin\Application\UseCases\Transfer\TransferEmployeeService;
use Modules\HR\PersonnelAdmin\Application\DTO\Transfer\TransferEmployeeRequestDto;

class TransferController extends Controller
{
    protected $transferEmployeeService;

    public function __construct(TransferEmployeeService $transferEmployeeService)
    {
        $this->transferEmployeeService = $transferEmployeeService;
    }

    public function transfer(Request $request)
    {
        $dto = new TransferEmployeeRequestDto(
            $request->input('employee_id'),
            $request->input('action_request_id'),
            $request->input('effective_date'),
            $request->input('new_position_id'),
            $request->input('new_job_title_id'),
            $request->input('new_department_id'),
            $request->input('new_cost_center_id'),
            $request->input('new_company_code_id'),
            $request->input('new_personnel_area_id'),
            $request->input('new_personnel_sub_area_id'),
            $request->input('new_employee_group_id'),
            $request->input('new_employee_sub_group_id'),
            $request->input('new_manager_core_user_id'),
            $request->input('new_employment_status_id')
        );

        ($this->transferEmployeeService)($dto);

        return response()->json(['message' => 'Transfer process started.']);
    }
}
