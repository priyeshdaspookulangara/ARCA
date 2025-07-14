<?php

namespace Modules\HR\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\HR\TalentManagement\Domain\Entities\PerformanceReview;
use Modules\HR\PersonnelAdmin\Domain\Entities\Employee;
use Carbon\Carbon;

class PerformanceReviewFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PerformanceReview::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $employee = Employee::factory()->create();
        // A simple way to get a manager. In a real app, this would use the employee's actual manager.
        $reviewer = Employee::factory()->create();

        $endDate = $this->faker->dateTimeThisYear();
        $startDate = Carbon::instance($endDate)->subYear()->addDay();

        return [
            'hr_employee_id' => $employee->id,
            'reviewer_id' => $reviewer->id,
            'review_period_start_date' => $startDate->format('Y-m-d'),
            'review_period_end_date' => $endDate->format('Y-m-d'),
            'overall_rating' => $this->faker->optional(0.8)->numberBetween(1, 5),
            'strengths' => $this->faker->optional()->paragraph,
            'areas_for_improvement' => $this->faker->optional()->paragraph,
            'employee_comments' => $this->faker->optional()->paragraph,
            'manager_comments' => $this->faker->optional()->paragraph,
            'status' => $this->faker->randomElement([
                PerformanceReview::STATUS_DRAFT,
                PerformanceReview::STATUS_PENDING_EMPLOYEE_REVIEW,
                PerformanceReview::STATUS_PENDING_MANAGER_REVIEW,
                PerformanceReview::STATUS_FINALIZED,
            ]),
            'finalized_at' => function (array $attributes) {
                return $attributes['status'] === PerformanceReview::STATUS_FINALIZED ? now() : null;
            },
        ];
    }

    public function forEmployee(Employee $employee)
    {
        return $this->state(['hr_employee_id' => $employee->id]);
    }

    public function withReviewer(Employee $reviewer)
    {
        return $this->state(['reviewer_id' => $reviewer->id]);
    }
}
