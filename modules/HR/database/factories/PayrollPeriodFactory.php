<?php

namespace Modules\HR\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\HR\Payroll\Domain\Entities\PayrollPeriod;
use Carbon\Carbon;

class PayrollPeriodFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PayrollPeriod::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = Carbon::instance($this->faker->dateTimeThisYear)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        $paymentDate = $endDate->copy(); // Or a specific day like the 25th

        return [
            'name' => $startDate->format('F Y'),
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'payment_date' => $paymentDate->toDateString(),
            'status' => $this->faker->randomElement([
                PayrollPeriod::STATUS_OPEN,
                PayrollPeriod::STATUS_CLOSED,
                PayrollPeriod::STATUS_PAID,
            ]),
        ];
    }

    /**
     * Indicate that the payroll period is open.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function open()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => PayrollPeriod::STATUS_OPEN,
            ];
        });
    }
}
