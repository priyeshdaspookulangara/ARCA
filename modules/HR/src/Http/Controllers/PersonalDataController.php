<?php

namespace Modules\HR\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\HR\PersonnelAdmin\Application\UseCases\PersonalData\UpdateEmployeeAddressService;
use Modules\HR\PersonnelAdmin\Application\DTO\PersonalData\UpdateEmployeeAddressRequestDto;

class PersonalDataController extends Controller
{
    protected $updateEmployeeAddressService;

    public function __construct(UpdateEmployeeAddressService $updateEmployeeAddressService)
    {
        $this->updateEmployeeAddressService = $updateEmployeeAddressService;
    }

    public function updateAddress(Request $request)
    {
        $dto = new UpdateEmployeeAddressRequestDto(
            $request->input('employee_id'),
            $request->input('action_request_id'),
            $request->input('effective_date'),
            $request->input('address_type'),
            $request->input('street'),
            $request->input('city'),
            $request->input('postal_code'),
            $request->input('state_or_province'),
            $request->input('country_code')
        );

        ($this->updateEmployeeAddressService)($dto);

        return response()->json(['message' => 'Address update process started.']);
    }
}
