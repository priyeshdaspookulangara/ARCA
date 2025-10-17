<?php

namespace Modules\CRM\Loyalty\Application;

use Modules\CRM\CustomerMaster\Domain\Model\Customer;
use Modules\CRM\Loyalty\Domain\Model\LoyaltyProgram;
use Modules\CRM\Loyalty\Domain\Model\LoyaltyPoints;

class LoyaltyService
{
    public function accruePoints(Customer $customer, LoyaltyProgram $program, float $transactionAmount)
    {
        $pointsToAccrue = floor($transactionAmount * $program->points_per_dollar);

        if ($pointsToAccrue > 0) {
            LoyaltyPoints::create([
                'customer_id' => $customer->id,
                'loyalty_program_id' => $program->id,
                'points' => $pointsToAccrue,
                'transaction_type' => 'accrual'
            ]);
        }
    }

    public function redeemPoints(Customer $customer, LoyaltyProgram $program, int $points)
    {
        $balance = $this->getCustomerBalance($customer, $program);

        if ($balance >= $points) {
            LoyaltyPoints::create([
                'customer_id' => $customer->id,
                'loyalty_program_id' => $program->id,
                'points' => -$points,
                'transaction_type' => 'redemption'
            ]);
        }
    }

    public function getCustomerBalance(Customer $customer, LoyaltyProgram $program): int
    {
        return LoyaltyPoints::where('customer_id', $customer->id)
            ->where('loyalty_program_id', $program->id)
            ->sum('points');
    }

    public function updateCustomerTier(Customer $customer, LoyaltyProgram $program)
    {
        // Business logic to update customer's tier
    }
}