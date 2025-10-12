<?php

namespace Modules\HR\TalentManagement\Application\Services;

use DateTime;
use Modules\HR\TalentManagement\Domain\Entities\PerformanceReview;
use Modules\HR\TalentManagement\Domain\Entities\Goal;
use Modules\HR\TalentManagement\Domain\Repositories\PerformanceReviewRepositoryInterface;
use Modules\HR\TalentManagement\Domain\Repositories\GoalRepositoryInterface;

class TalentManagementService
{
    private $performanceReviewRepository;
    private $goalRepository;

    public function __construct(
        PerformanceReviewRepositoryInterface $performanceReviewRepository,
        GoalRepositoryInterface $goalRepository
    ) {
        $this->performanceReviewRepository = $performanceReviewRepository;
        $this->goalRepository = $goalRepository;
    }

    public function createPerformanceReview(string $employeeId, string $startDate, string $endDate): PerformanceReview
    {
        $id = uniqid('pr_');
        $review = new PerformanceReview($id, $employeeId, new DateTime($startDate), new DateTime($endDate));
        $this->performanceReviewRepository->save($review);
        return $review;
    }

    public function completePerformanceReview(string $reviewId, int $rating, string $comments): ?PerformanceReview
    {
        $review = $this->performanceReviewRepository->findById($reviewId);
        if ($review) {
            $review->completeReview($rating, $comments);
            $this->performanceReviewRepository->save($review);
        }
        return $review;
    }

    public function getPerformanceReviewsForEmployee(string $employeeId): array
    {
        return $this->performanceReviewRepository->findByEmployee($employeeId);
    }

    public function createGoal(string $employeeId, string $description): Goal
    {
        $id = uniqid('goal_');
        $goal = new Goal($id, $employeeId, $description);
        $this->goalRepository->save($goal);
        return $goal;
    }

    public function updateGoalStatus(string $goalId, string $newStatus): ?Goal
    {
        $goal = $this->goalRepository->findById($goalId);
        if ($goal) {
            $goal->updateStatus($newStatus);
            $this->goalRepository->save($goal);
        }
        return $goal;
    }

    public function getGoalsForEmployee(string $employeeId): array
    {
        return $this->goalRepository->findByEmployee($employeeId);
    }
}