<?php

namespace Tests\Feature;

use Tests\TestCase;
use Modules\HR\TalentManagement\Domain\Repositories\PerformanceReviewRepositoryInterface;
use Modules\HR\TalentManagement\Domain\Repositories\GoalRepositoryInterface;

class TalentManagementModuleTest extends TestCase
{
    public function test_can_create_and_complete_performance_review()
    {
        // 1. Create a Performance Review
        $createResponse = $this->postJson('/api/talent/performance-reviews', [
            'employee_id' => '123',
            'start_date' => '2025-01-01',
            'end_date' => '2025-12-31'
        ]);
        $createResponse->assertStatus(201);
        $reviewId = $createResponse->json('id');

        // 2. Complete the Performance Review
        $completeResponse = $this->putJson("/api/talent/performance-reviews/{$reviewId}", [
            'rating' => 4,
            'comments' => 'Exceeded expectations in all areas.'
        ]);
        $completeResponse->assertStatus(200)
                         ->assertJsonFragment(['rating' => 4, 'comments' => 'Exceeded expectations in all areas.']);

        // 3. Verify the review is stored correctly
        $reviewRepository = $this->app->make(PerformanceReviewRepositoryInterface::class);
        $review = $reviewRepository->findById($reviewId);
        $this->assertEquals(4, $review->getRating());
        $this->assertEquals('Exceeded expectations in all areas.', $review->getComments());
    }

    public function test_can_create_and_update_goal()
    {
        // 1. Create a Goal
        $createResponse = $this->postJson('/api/talent/goals', [
            'employee_id' => '456',
            'description' => 'Complete the Q4 project'
        ]);
        $createResponse->assertStatus(201)
                       ->assertJsonFragment(['description' => 'Complete the Q4 project', 'status' => 'not_started']);
        $goalId = $createResponse->json('id');

        // 2. Update the Goal's status
        $updateResponse = $this->putJson("/api/talent/goals/{$goalId}", ['status' => 'in_progress']);
        $updateResponse->assertStatus(200)->assertJsonFragment(['status' => 'in_progress']);

        // 3. Verify the goal is stored correctly
        $goalRepository = $this->app->make(GoalRepositoryInterface::class);
        $goal = $goalRepository->findById($goalId);
        $this->assertEquals('in_progress', $goal->getStatus());
    }
}