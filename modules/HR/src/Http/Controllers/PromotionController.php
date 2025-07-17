<?php

namespace Modules\HR\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\HR\PersonnelAdmin\Application\UseCases\Promotion\PromoteEmployeeService;
use Modules\HR\PersonnelAdmin\Application\DTO\Promotion\PromoteEmployeeRequestDto;

class PromotionController extends Controller
{
    protected $promoteEmployeeService;

    public function __construct(PromoteEmployeeService $promoteEmployeeService)
    {
        $this->promoteEmployeeService = $promoteEmployeeService;
    }

    public function promote(Request $request)
    {
        $dto = new PromoteEmployeeRequestDto(
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
            $request->input('new_employment_status_id'),
            $request->input('new_base_salary_amount'),
            $request->input('new_salary_currency_code'),
            $request->input('new_pay_frequency'),
            $request->input('new_other_components_json')
        );

        ($this->promoteEmployeeService)($dto);

        return response()->json(['message' => 'Promotion process started.']);
    }
}
