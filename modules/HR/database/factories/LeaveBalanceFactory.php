<?php

namespace Modules\HR\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\HR\TimeManagement\Domain\Entities\LeaveBalance;
use Modules\HR\PersonnelAdmin\Domain\Entities\Employee;
use Modules\HR\TimeManagement\Domain\Entities\LeaveType;

class LeaveBalanceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LeaveBalance::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $employee = Employee::factory()->create();
        $leaveType = LeaveType::factory()->create();

        $entitlement = $leaveType->default_entitlement_days ?? $this->faker->randomElement([10, 20, 25]);
        $taken = $this->faker->numberBetween(0, $entitlement);

        return [
            'hr_employee_id' => $employee->id,
            'hr_leave_type_id' => $leaveType->id,
            'fiscal_year' => now()->year,
            'entitlement_days' => $entitlement,
            'taken_days' => $taken,
            'notes' => $this->faker->optional()->sentence,
        ];
    }

    public function forEmployee(Employee $employee)
    {
        return $this->state(['hr_employee_id' => $employee->id]);
    }

    public function forLeaveType(LeaveType $leaveType)
    {
        return $this->state([
            'hr_leave_type_id' => $leaveType->id,
            'entitlement_days' => $leaveType->default_entitlement_days ?? 20,
        ]);
    }
}
