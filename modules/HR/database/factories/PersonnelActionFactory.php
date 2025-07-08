<?php

namespace Modules\HR\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\HR\PersonnelAdmin\Domain\Entities\PersonnelAction;
use Modules\HR\PersonnelAdmin\Domain\Entities\Employee;

class PersonnelActionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PersonnelAction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $actionTypes = [
            PersonnelAction::ACTION_TYPE_HIRE,
            PersonnelAction::ACTION_TYPE_PROMOTION,
            PersonnelAction::ACTION_TYPE_TRANSFER,
            PersonnelAction::ACTION_TYPE_TERMINATION,
            PersonnelAction::ACTION_TYPE_CONTRACT_UPDATE,
            PersonnelAction::ACTION_TYPE_SALARY_CHANGE,
        ];

        $statuses = [
            PersonnelAction::STATUS_PENDING,
            PersonnelAction::STATUS_APPROVED,
            PersonnelAction::STATUS_EXECUTED,
            PersonnelAction::STATUS_REJECTED,
        ];

        $employee = Employee::factory()->create(); // Ensure an employee exists

        return [
            'hr_employee_id' => $employee->id,
            'action_type' => $this->faker->randomElement($actionTypes),
            'effective_date' => $this->faker->date(),
            'reason' => $this->faker->optional()->sentence,
            'details_json' => $this->faker->optional()->randomElement([
                ['new_salary' => $this->faker->numberBetween(50000, 100000)],
                ['new_position_id' => $this->faker->numberBetween(1, 20), 'old_position_id' => $this->faker->numberBetween(1,20)],
                ['notes' => $this->faker->paragraph]
            ]),
            'status' => $this->faker->randomElement($statuses),
            'created_by_user_id' => $this->faker->optional()->numberBetween(1, 10), // Placeholder
            'approved_by_user_id' => $this->faker->optional()->numberBetween(1, 10), // Placeholder
            'executed_at' => $this->faker->optional()->dateTimeThisYear(),
        ];
    }

    public function forEmployee(Employee $employee)
    {
        return $this->state(['hr_employee_id' => $employee->id]);
    }

    public function withType(string $type)
    {
        return $this->state(['action_type' => $type]);
    }

    public function withStatus(string $status)
    {
        return $this->state(['status' => $status]);
    }
}
