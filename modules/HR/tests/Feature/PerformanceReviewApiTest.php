<?php

namespace Modules\HR\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\HR\PersonnelAdmin\Domain\Entities\Employee;
use Modules\HR\TalentManagement\Domain\Entities\PerformanceReview;
use Tests\TestCase;

class PerformanceReviewApiTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private Employee $employee;
    private Employee $manager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->employee = Employee::factory()->create();
        $this->manager = Employee::factory()->create();
    }

    private function getValidReviewData(array $overrides = []): array
    {
        return array_merge([
            'review_period_start_date' => now()->subYear()->toDateString(),
            'review_period_end_date' => now()->subDay()->toDateString(),
            'reviewer_id' => $this->manager->id,
            'manager_comments' => 'Initial draft comments.',
        ], $overrides);
    }

    public function test_can_create_draft_performance_review()
    {
        $data = $this->getValidReviewData();
        $response = $this->postJson("/api/hr/employees/{$this->employee->id}/performance-reviews", $data);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'hr_employee_id' => $this->employee->id,
                     'reviewer_id' => $this->manager->id,
                     'status' => PerformanceReview::STATUS_DRAFT,
                 ]);
        $this->assertDatabaseHas('hr_performance_reviews', [
            'hr_employee_id' => $this->employee->id,
            'reviewer_id' => $this->manager->id,
        ]);
    }

    public function test_can_list_reviews_for_a_specific_employee()
    {
        PerformanceReview::factory()->count(2)->forEmployee($this->employee)->withReviewer($this->manager)->create();
        PerformanceReview::factory()->create(); // For another employee

        $response = $this->getJson("/api/hr/employees/{$this->employee->id}/performance-reviews");
        $response->assertStatus(200)->assertJsonCount(2);
    }

    public function test_can_list_all_reviews()
    {
        PerformanceReview::factory()->count(3)->create();
        $response = $this->getJson('/api/hr/performance/reviews');
        $response->assertStatus(200)->assertJsonCount(3);
    }

    public function test_can_get_a_specific_review()
    {
        $review = PerformanceReview::factory()->forEmployee($this->employee)->create();
        $response = $this->getJson("/api/hr/performance/reviews/{$review->id}");
        $response->assertStatus(200)->assertJsonFragment(['id' => $review->id]);
    }

    public function test_can_update_a_review()
    {
        $review = PerformanceReview::factory()->forEmployee($this->employee)->create(['status' => PerformanceReview::STATUS_PENDING_EMPLOYEE_REVIEW]);
        $updateData = [
            'employee_comments' => 'I agree with this assessment.',
            'status' => PerformanceReview::STATUS_PENDING_MANAGER_REVIEW,
        ];

        $response = $this->putJson("/api/hr/performance/reviews/{$review->id}", $updateData);
        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'employee_comments' => $updateData['employee_comments'],
                     'status' => $updateData['status'],
                 ]);
        $this->assertDatabaseHas('hr_performance_reviews', ['id' => $review->id, 'status' => $updateData['status']]);
    }

    public function test_can_finalize_a_review_with_rating()
    {
        $review = PerformanceReview::factory()->forEmployee($this->employee)->create(['status' => PerformanceReview::STATUS_PENDING_MANAGER_REVIEW]);
        $updateData = [
            'overall_rating' => 4,
            'status' => PerformanceReview::STATUS_FINALIZED,
        ];

        $response = $this->putJson("/api/hr/performance/reviews/{$review->id}", $updateData);
        $response->assertStatus(200)
                 ->assertJsonFragment(['status' => PerformanceReview::STATUS_FINALIZED, 'overall_rating' => 4]);
        $this->assertNotNull($review->fresh()->finalized_at);
    }

    public function test_cannot_finalize_a_review_without_rating()
    {
        $review = PerformanceReview::factory()->forEmployee($this->employee)->create(['status' => PerformanceReview::STATUS_PENDING_MANAGER_REVIEW, 'overall_rating' => null]);
        $updateData = ['status' => PerformanceReview::STATUS_FINALIZED];

        $response = $this->putJson("/api/hr/performance/reviews/{$review->id}", $updateData);
        $response->assertStatus(422)->assertJsonValidationErrors(['overall_rating']);
    }

    public function test_can_delete_a_review()
    {
        $review = PerformanceReview::factory()->create();
        $response = $this->deleteJson("/api/hr/performance/reviews/{$review->id}");
        $response->assertStatus(204);
        $this->assertSoftDeleted('hr_performance_reviews', ['id' => $review->id]);
    }
}
