<?php

namespace Modules\HR\TimeManagement\Application\Services;

use DateTime;
use Modules\HR\TimeManagement\Domain\Entities\Absence;
use Modules\HR\TimeManagement\Domain\Repositories\AbsenceRepositoryInterface;

class AbsenceService
{
    private $absenceRepository;

    public function __construct(AbsenceRepositoryInterface $absenceRepository)
    {
        $this->absenceRepository = $absenceRepository;
    }

    public function requestAbsence(string $employeeId, string $absenceType, string $startDate, string $endDate): Absence
    {
        $id = uniqid('abs_');
        $absence = new Absence($id, $employeeId, $absenceType, new DateTime($startDate), new DateTime($endDate));
        $this->absenceRepository->save($absence);
        return $absence;
    }

    public function approveAbsence(string $absenceId): ?Absence
    {
        $absence = $this->absenceRepository->findById($absenceId);
        if ($absence) {
            $absence->approve();
            $this->absenceRepository->save($absence);
        }
        return $absence;
    }

    public function getAbsencesForEmployee(string $employeeId): array
    {
        return $this->absenceRepository->findByEmployee($employeeId);
    }
}