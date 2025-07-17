<?php

namespace Modules\HR\PersonnelAdmin\Application\UseCases\PersonalData;

use Modules\HR\PersonnelAdmin\Domain\Repositories\EmployeeAddressRepositoryInterface;
use Modules\HR\PersonnelAdmin\Domain\Services\EffectiveDateService;
use Modules\HR\PersonnelAdmin\Application\DTO\PersonalData\UpdateEmployeeAddressRequestDto;

class UpdateEmployeeAddressService
{
    protected $employeeAddressRepository;
    protected $effectiveDateService;

    public function __construct(
        EmployeeAddressRepositoryInterface $employeeAddressRepository,
        EffectiveDateService $effectiveDateService
    ) {
        $this->employeeAddressRepository = $employeeAddressRepository;
        $this->effectiveDateService = $effectiveDateService;
    }

    public function __invoke(UpdateEmployeeAddressRequestDto $request)
    {
        // 1. Initiate a PersonnelActionRequest record

        // 2. Initiate and manage a workflow instance

        // 3. Upon final workflow approval:
        //    a. Perform final business rule validations
        //    b. Manage Effective-Dated Records
        $this->employeeAddressRepository->delimitCurrentRecord(
            $request->employeeId,
            $request->addressType,
            $this->effectiveDateService->getPreviousDay($request->effectiveDate)
        );

        $this->employeeAddressRepository->insertNewSlice([
            'employee_id' => $request->employeeId,
            'address_type' => $request->addressType,
            'valid_from' => $request->effectiveDate,
            'valid_to' => '9999-12-31',
            'action_request_id_triggered_by' => $request->actionRequestId,
            'street' => $request->street,
            'city' => $request->city,
            'postal_code' => $request->postalCode,
            'state_or_province' => $request->stateOrProvince,
            'country_code' => $request->countryCode,
        ]);

        //    c. Dispatch domain events
    }
}
