<?php

namespace Modules\HR\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\HR\PersonnelAdmin\Domain\Entities\Contract;
use Modules\HR\PersonnelAdmin\Domain\Entities\Employee;

class ContractFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Contract::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $employee = Employee::factory()->create(); // Ensure an employee exists
        $startDate = $this->faker->dateTimeBetween('-2 years', '-1 month');
        $contractType = $this->faker->randomElement([
            Contract::TYPE_PERMANENT,
            Contract::TYPE_FIXED_TERM,
            Contract::TYPE_INTERNSHIP,
            Contract::TYPE_PART_TIME,
        ]);

        $endDate = null;
        if ($contractType === Contract::TYPE_FIXED_TERM || $contractType === Contract::TYPE_INTERNSHIP) {
            $endDate = $this->faker->dateTimeBetween($startDate, '+1 year');
        }

        $status = Contract::STATUS_ACTIVE;
        if ($endDate && $endDate < now()) {
            $status = Contract::STATUS_EXPIRED;
        }


        return [
            'hr_employee_id' => $employee->id,
            'contract_type' => $contractType,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'job_title_snapshot' => $employee->position ? $employee->position->job->job_title : $this->faker->jobTitle,
            'department_snapshot' => $employee->department ? $employee->department->name : $this->faker->company . ' Department',
            'salary_amount' => $this->faker->numberBetween(30000, 150000),
            'salary_currency' => $this->faker->randomElement(['USD', 'EUR', 'GBP']),
            'salary_frequency' => $this->faker->randomElement([
                Contract::FREQUENCY_MONTHLY,
                Contract::FREQUENCY_ANNUAL
            ]),
            'working_hours_per_week' => $this->faker->optional(0.8)->randomElement([20.00, 37.50, 40.00]),
            'probation_period_months' => $this->faker->optional(0.5)->randomElement([1, 3, 6]),
            'notice_period_days' => $this->faker->optional(0.7)->randomElement([15, 30, 60, 90]),
            'contract_document_path' => $this->faker->optional()->filePath(),
            'status' => $status,
            'remarks' => $this->faker->optional()->paragraph,
        ];
    }

    public function forEmployee(Employee $employee)
    {
        return $this->state([
            'hr_employee_id' => $employee->id,
            // Update snapshots if employee has position/department
            'job_title_snapshot' => $employee->position && $employee->position->job ? $employee->position->job->job_title : $this->faker->jobTitle,
            'department_snapshot' => $employee->department ? $employee->department->name : $this->faker->company . ' Department',
        ]);
    }

    public function active() {
        return $this->state([
            'status' => Contract::STATUS_ACTIVE,
            'start_date' => $this->faker->dateTimeBetween('-1 year', '-1 day'),
            'end_date' => $this->faker->optional(0.3)->dateTimeBetween('+6 months', '+2 years') // Optional end date for active contracts
        ]);
    }

    public function pendingSignature() {
        return $this->state(['status' => Contract::STATUS_PENDING_SIGNATURE]);
    }

    public function expired() {
         return $this->state([
            'status' => Contract::STATUS_EXPIRED,
            'start_date' => $this->faker->dateTimeBetween('-3 years', '-1 year'),
            'end_date' => $this->faker->dateTimeBetween('-6 months', '-1 day')
        ]);
    }
}
