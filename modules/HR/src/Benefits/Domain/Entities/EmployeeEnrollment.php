<?php

namespace Modules\HR\Benefits\Domain\Entities;

class EmployeeEnrollment implements \JsonSerializable
{
    private $id;
    private $employeeId;
    private $planId;
    private $status; // e.g., 'active', 'inactive'

    public function __construct(string $id, string $employeeId, string $planId)
    {
        $this->id = $id;
        $this->employeeId = $employeeId;
        $this->planId = $planId;
        $this->status = 'active';
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmployeeId(): string
    {
        return $this->employeeId;
    }

    public function getPlanId(): string
    {
        return $this->planId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function deactivate(): void
    {
        $this->status = 'inactive';
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employeeId,
            'plan_id' => $this->planId,
            'status' => $this->status,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}