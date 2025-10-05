<?php

namespace Modules\HR\TimeManagement\Domain\Entities;

use DateTime;

class Absence implements \JsonSerializable
{
    private $id;
    private $employeeId;
    private $absenceType;
    private $startDate;
    private $endDate;
    private $status; // e.g., 'requested', 'approved', 'rejected'

    public function __construct(string $id, string $employeeId, string $absenceType, DateTime $startDate, DateTime $endDate)
    {
        $this->id = $id;
        $this->employeeId = $employeeId;
        $this->absenceType = $absenceType;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->status = 'requested';
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmployeeId(): string
    {
        return $this->employeeId;
    }

    public function getAbsenceType(): string
    {
        return $this->absenceType;
    }

    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    public function getEndDate(): DateTime
    {
        return $this->endDate;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function approve(): void
    {
        $this->status = 'approved';
    }

    public function reject(): void
    {
        $this->status = 'rejected';
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employeeId,
            'absence_type' => $this->absenceType,
            'start_date' => $this->startDate->format('Y-m-d'),
            'end_date' => $this->endDate->format('Y-m-d'),
            'status' => $this->status,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}