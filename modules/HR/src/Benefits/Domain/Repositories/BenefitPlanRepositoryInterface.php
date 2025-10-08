<?php

namespace Modules\HR\Benefits\Domain\Repositories;

use Modules\HR\Benefits\Domain\Entities\BenefitPlan;

interface BenefitPlanRepositoryInterface
{
    public function findById(string $id): ?BenefitPlan;

    public function findAll(): array;

    public function save(BenefitPlan $benefitPlan): void;

    public function delete(string $id): void;
}