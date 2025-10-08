<?php

namespace Modules\HR\Benefits\Infrastructure\Persistence;

use Modules\HR\Benefits\Domain\Entities\BenefitPlan;
use Modules\HR\Benefits\Domain\Repositories\BenefitPlanRepositoryInterface;

class InMemoryBenefitPlanRepository implements BenefitPlanRepositoryInterface
{
    private $benefitPlans = [];

    public function findById(string $id): ?BenefitPlan
    {
        return $this->benefitPlans[$id] ?? null;
    }

    public function findAll(): array
    {
        return array_values($this->benefitPlans);
    }

    public function save(BenefitPlan $benefitPlan): void
    {
        $this->benefitPlans[$benefitPlan->getId()] = $benefitPlan;
    }

    public function delete(string $id): void
    {
        unset($this->benefitPlans[$id]);
    }
}