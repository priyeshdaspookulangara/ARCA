<?php

namespace Modules\HR\TalentManagement\Domain\Repositories;

use Modules\HR\TalentManagement\Domain\Entities\PerformanceReview;

interface PerformanceReviewRepositoryInterface
{
    public function findById(string $id): ?PerformanceReview;

    public function findByEmployee(string $employeeId): array;

    public function save(PerformanceReview $review): void;
}