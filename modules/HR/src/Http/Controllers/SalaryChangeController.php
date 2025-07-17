<?php

namespace Modules\HR\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\HR\PersonnelAdmin\Application\UseCases\SalaryChange\ChangeEmployeeSalaryService;
use Modules\HR\PersonnelAdmin\Application\DTO\SalaryChange\ChangeEmployeeSalaryRequestDto;

class SalaryChangeController extends Controller
{
    protected $changeEmployeeSalaryService;

    public function __construct(ChangeEmployeeSalaryService $changeEmployeeSalaryService)
    {
        $this->changeEmployeeSalaryService = $changeEmployeeSalaryService;
    }

    public function changeSalary(Request $request)
    {
        $dto = new ChangeEmployeeSalaryRequestDto(
            $request->input('employee_id'),
            $request->input('action_request_id'),
            $request->input('effective_date'),
            $request->input('new_base_salary_amount'),
            $request->input('new_salary_currency_code'),
            $request->input('new_pay_frequency'),
            $request->input('new_other_components_json')
        );

        ($this->changeEmployeeSalaryService)($dto);

        return response()->json(['message' => 'Salary change process started.']);
    }
}
