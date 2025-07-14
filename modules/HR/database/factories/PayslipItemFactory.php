<?php

namespace Modules\HR\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\HR\Payroll\Domain\Entities\PayslipItem;
use Modules\HR\Payroll\Domain\Entities\Payslip;

class PayslipItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PayslipItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $payslip = Payslip::factory()->create(); // Ensure a payslip exists
        $itemType = $this->faker->randomElement([PayslipItem::TYPE_EARNING, PayslipItem::TYPE_DEDUCTION]);

        $description = '';
        if ($itemType === PayslipItem::TYPE_EARNING) {
            $description = $this->faker->randomElement(['Basic Salary', 'Overtime Pay', 'Bonus', 'Commission']);
        } else {
            $description = $this->faker->randomElement(['Income Tax', 'Health Insurance', 'Pension Contribution', 'Loan Repayment']);
        }

        return [
            'hr_payslip_id' => $payslip->id,
            'item_type' => $itemType,
            'description' => $description,
            'amount' => $this->faker->numberBetween(100, 5000),
            'is_pre_tax' => ($itemType === PayslipItem::TYPE_DEDUCTION) ? $this->faker->boolean : false,
        ];
    }

    public function forPayslip(Payslip $payslip)
    {
        return $this->state(['hr_payslip_id' => $payslip->id]);
    }

    public function earning()
    {
        return $this->state([
            'item_type' => PayslipItem::TYPE_EARNING,
            'is_pre_tax' => false,
        ]);
    }

    public function deduction()
    {
        return $this->state([
            'item_type' => PayslipItem::TYPE_DEDUCTION,
        ]);
    }
}
