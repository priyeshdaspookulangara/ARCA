<?php

namespace Modules\HR\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\HR\TimeManagement\Domain\Entities\LeaveAccrualPolicy;
use Modules\HR\TimeManagement\Domain\Entities\LeaveType;

class LeaveAccrualPolicyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LeaveAccrualPolicy::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $leaveType = LeaveType::factory()->create();
        $frequency = $this->faker->randomElement([
            LeaveAccrualPolicy::FREQUENCY_ANNUALLY,
            LeaveAccrualPolicy::FREQUENCY_MONTHLY,
        ]);

        $rate = 0;
        if ($frequency === LeaveAccrualPolicy::FREQUENCY_ANNUALLY) {
            $rate = $this->faker->randomElement([15, 20, 25]);
        } else { // Monthly
            $rate = $this->faker->randomFloat(2, 1, 2.5);
        }

        return [
            'name' => $leaveType->name . ' Policy',
            'hr_leave_type_id' => $leaveType->id,
            'accrual_frequency' => $frequency,
            'accrual_rate_days' => $rate,
            'max_carry_over_days' => $this->faker->optional(0.7)->randomElement([5, 10]),
            'description' => $this->faker->optional()->sentence,
            'is_active' => true,
        ];
    }

    public function forLeaveType(LeaveType $leaveType)
    {
        return $this->state(['hr_leave_type_id' => $leaveType->id]);
    }
}
