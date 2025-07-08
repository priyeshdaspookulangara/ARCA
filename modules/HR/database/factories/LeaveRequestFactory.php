<?php

namespace Modules\HR\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\HR\TimeManagement\Domain\Entities\LeaveRequest;
use Modules\HR\PersonnelAdmin\Domain\Entities\Employee;
use Modules\HR\TimeManagement\Domain\Entities\LeaveType;
use Carbon\Carbon;

class LeaveRequestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LeaveRequest::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $employee = Employee::factory()->create();
        $leaveType = LeaveType::factory()->create();

        $startDate = $this->faker->dateTimeBetween('+1 week', '+3 months');
        $durationDays = $this->faker->randomElement([1, 2, 3, 5, 0.5]);

        // Calculate end date based on simple duration (not accounting for weekends/holidays for factory simplicity)
        // The actual duration calculation might be more complex in the application.
        $endDate = Carbon::instance($startDate)->addDays(ceil($durationDays) - 1)->toDateString();
        if ($durationDays == 0.5) { // For half day, start and end are same
            $endDate = Carbon::instance($startDate)->toDateString();
        }


        $statuses = [
            LeaveRequest::STATUS_PENDING,
            LeaveRequest::STATUS_APPROVED,
            LeaveRequest::STATUS_REJECTED,
            LeaveRequest::STATUS_CANCELLED_BY_EMPLOYEE,
        ];
        $status = $this->faker->randomElement($statuses);

        $approved_at = null;
        $rejected_at = null;
        $rejection_reason = null;
        $approver_user_id = null;

        if ($status === LeaveRequest::STATUS_APPROVED) {
            $approved_at = now();
            $approver_user_id = $this->faker->optional()->numberBetween(1,10); // Placeholder
        } elseif ($status === LeaveRequest::STATUS_REJECTED) {
            $rejected_at = now();
            $rejection_reason = $this->faker->sentence;
            $approver_user_id = $this->faker->optional()->numberBetween(1,10); // Placeholder
        }


        return [
            'hr_employee_id' => $employee->id,
            'hr_leave_type_id' => $leaveType->id,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate,
            'duration_days' => $durationDays,
            'reason' => $this->faker->optional()->sentence,
            'status' => $status,
            'approver_user_id' => $approver_user_id,
            'approved_at' => $approved_at,
            'rejected_at' => $rejected_at,
            'rejection_reason' => $rejection_reason,
            'cancelled_at' => ($status === LeaveRequest::STATUS_CANCELLED_BY_EMPLOYEE) ? now() : null,
            'cancelled_by_role' => ($status === LeaveRequest::STATUS_CANCELLED_BY_EMPLOYEE) ? LeaveRequest::CANCELLED_BY_EMPLOYEE_ROLE : null,
            'employee_remarks' => $this->faker->optional()->paragraph,
            'approver_remarks' => ($status === LeaveRequest::STATUS_APPROVED || $status === LeaveRequest::STATUS_REJECTED) ? $this->faker->optional()->sentence : null,
        ];
    }

    public function forEmployee(Employee $employee)
    {
        return $this->state(['hr_employee_id' => $employee->id]);
    }

    public function ofType(LeaveType $leaveType)
    {
        return $this->state(['hr_leave_type_id' => $leaveType->id]);
    }

    public function pending()
    {
        return $this->state([
            'status' => LeaveRequest::STATUS_PENDING,
            'approved_at' => null, 'rejected_at' => null, 'rejection_reason' => null, 'approver_user_id' => null
        ]);
    }

    public function approved()
    {
         return $this->state([
            'status' => LeaveRequest::STATUS_APPROVED,
            'approved_at' => now(),
            'rejected_at' => null,
            'rejection_reason' => null,
            'approver_user_id' => $this->faker->numberBetween(1,10) // Placeholder
        ]);
    }
}
