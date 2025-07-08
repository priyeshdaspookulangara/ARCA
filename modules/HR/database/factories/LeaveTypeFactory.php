<?php

namespace Modules\HR\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\HR\TimeManagement\Domain\Entities\LeaveType;

class LeaveTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LeaveType::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $leaveTypeName = $this->faker->unique()->randomElement([
            'Annual Leave', 'Sick Leave', 'Unpaid Leave', 'Maternity Leave',
            'Paternity Leave', 'Bereavement Leave', 'Study Leave', 'Public Holiday Compensation'
        ]);

        $isPaid = true;
        if (str_contains(strtolower($leaveTypeName), 'unpaid')) {
            $isPaid = false;
        }

        return [
            'name' => $leaveTypeName,
            'description' => $this->faker->optional()->sentence,
            'is_paid' => $isPaid,
            'default_entitlement_days' => $isPaid ? $this->faker->optional(0.7)->randomElement([10, 15, 20, 25]) : null,
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the leave type is unpaid.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unpaid()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_paid' => false,
                'default_entitlement_days' => null,
            ];
        });
    }

    /**
     * Indicate that the leave type is inactive.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false,
            ];
        });
    }
}
