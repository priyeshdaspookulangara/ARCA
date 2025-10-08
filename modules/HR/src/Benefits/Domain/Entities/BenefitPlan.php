<?php

namespace Modules\HR\Benefits\Domain\Entities;

class BenefitPlan implements \JsonSerializable
{
    private $id;
    private $name;
    private $type;
    private $deductionAmount;

    public function __construct(string $id, string $name, string $type, float $deductionAmount)
    {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->deductionAmount = $deductionAmount;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getDeductionAmount(): float
    {
        return $this->deductionAmount;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'deduction_amount' => $this->deductionAmount,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}