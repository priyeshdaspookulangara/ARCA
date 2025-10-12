<?php

namespace Modules\HR\TalentManagement\Infrastructure\Persistence;

use Modules\HR\TalentManagement\Domain\Entities\PerformanceReview;
use Modules\HR\TalentManagement\Domain\Repositories\PerformanceReviewRepositoryInterface;

class InMemoryPerformanceReviewRepository implements PerformanceReviewRepositoryInterface
{
    private $reviews = [];

    public function findById(string $id): ?PerformanceReview
    {
        return $this->reviews[$id] ?? null;
    }

    public function findByEmployee(string $employeeId): array
    {
        return array_filter($this->reviews, function (PerformanceReview $review) use ($employeeId) {
            return $review->getEmployeeId() === $employeeId;
        });
    }

    public function save(PerformanceReview $review): void
    {
        $this->reviews[$review->getId()] = $review;
    }
}