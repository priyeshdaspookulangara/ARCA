<?php

namespace Modules\HR\TimeManagement\Domain\Entities;

use DateTime;

class TimeRecord implements \JsonSerializable
{
    private $id;
    private $employeeId;
    private $date;
    private $hours;
    private $status; // e.g., 'submitted', 'approved', 'rejected'

    public function __construct(string $id, string $employeeId, DateTime $date, float $hours)
    {
        $this->id = $id;
        $this->employeeId = $employeeId;
        $this->date = $date;
        $this->hours = $hours;
        $this->status = 'submitted';
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmployeeId(): string
    {
        return $this->employeeId;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function getHours(): float
    {
        return $this->hours;
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
            'date' => $this->date->format('Y-m-d'),
            'hours' => $this->hours,
            'status' => $this->status,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}