<?php

namespace Modules\HR\TalentManagement\Domain\Entities;

class Goal implements \JsonSerializable
{
    private $id;
    private $employeeId;
    private $description;
    private $status; // e.g., 'not_started', 'in_progress', 'completed'

    public function __construct(string $id, string $employeeId, string $description)
    {
        $this->id = $id;
        $this->employeeId = $employeeId;
        $this->description = $description;
        $this->status = 'not_started';
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmployeeId(): string
    {
        return $this->employeeId;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function updateStatus(string $newStatus): void
    {
        $this->status = $newStatus;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employeeId,
            'description' => $this->description,
            'status' => $this->status,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}