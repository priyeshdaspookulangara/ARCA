<?php

namespace Modules\HR\Payroll\Domain\Entities;

use DateTime;

class PayrollRun implements \JsonSerializable
{
    private $id;
    private $periodStartDate;
    private $periodEndDate;
    private $status; // e.g., 'pending', 'processing', 'completed'
    private $generatedPaycheckIds = [];

    public function __construct(string $id, DateTime $periodStartDate, DateTime $periodEndDate)
    {
        $this->id = $id;
        $this->periodStartDate = $periodStartDate;
        $this->periodEndDate = $periodEndDate;
        $this->status = 'pending';
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPeriodStartDate(): DateTime
    {
        return $this->periodStartDate;
    }

    public function getPeriodEndDate(): DateTime
    {
        return $this->periodEndDate;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function complete(): void
    {
        $this->status = 'completed';
    }

    public function addPaycheckId(string $paycheckId): void
    {
        $this->generatedPaycheckIds[] = $paycheckId;
    }

    public function getPaycheckIds(): array
    {
        return $this->generatedPaycheckIds;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'period_start_date' => $this->periodStartDate->format('Y-m-d'),
            'period_end_date' => $this->periodEndDate->format('Y-m-d'),
            'status' => $this->status,
            'paycheck_ids' => $this->generatedPaycheckIds,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}