<?php

namespace Modules\HR\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\HR\Payroll\Domain\Entities\Payslip;
use Modules\HR\PersonnelAdmin\Domain\Entities\Employee;
use Modules\HR\Payroll\Domain\Entities\PayrollPeriod;

class PayslipFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Payslip::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $employee = Employee::factory()->create();
        $payrollPeriod = PayrollPeriod::factory()->create();

        $gross = $this->faker->numberBetween(3000, 8000);
        $deductions = $this->faker->numberBetween(500, $gross * 0.4);
        $net = $gross - $deductions;

        return [
            'hr_employee_id' => $employee->id,
            'hr_payroll_period_id' => $payrollPeriod->id,
            'gross_salary' => $gross,
            'total_deductions' => $deductions,
            'net_salary' => $net,
            'status' => $this->faker->randomElement([
                Payslip::STATUS_DRAFT,
                Payslip::STATUS_CONFIRMED,
                Payslip::STATUS_PAID,
            ]),
            'notes' => $this->faker->optional()->sentence,
        ];
    }

    public function forEmployee(Employee $employee)
    {
        return $this->state(['hr_employee_id' => $employee->id]);
    }

    public function forPeriod(PayrollPeriod $period)
    {
        return $this->state(['hr_payroll_period_id' => $period->id]);
    }

    public function draft()
    {
        return $this->state(['status' => Payslip::STATUS_DRAFT]);
    }
}
