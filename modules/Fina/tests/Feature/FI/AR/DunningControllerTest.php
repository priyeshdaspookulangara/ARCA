<?php

namespace Modules\Fina\Tests\Feature\FI\AR;

use Modules\Fina\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Fina\FI\AR\Domain\Entities\ARCustomerFinancials;
use Modules\Fina\FI\AR\Domain\Entities\ARInvoiceHeader;
use Modules\Fina\FI\AR\Domain\Entities\ARDunningProcedure;

class DunningControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_run_dunning_process()
    {
        // Create a dunning procedure
        $dunningProcedure = ARDunningProcedure::factory()->create([
            'dunning_levels' => [
                ['level' => 1, 'days_in_arrears' => 10, 'charge' => 5.00, 'grace_period_days' => 7],
                ['level' => 2, 'days_in_arrears' => 20, 'charge' => 15.00, 'grace_period_days' => 7],
            ]
        ]);

        // Create customer financials linked to the dunning procedure
        $customerFinancials = ARCustomerFinancials::factory()->create([
            'dunning_procedure_id' => $dunningProcedure->id,
            'dunning_level' => 0,
            'last_dunned_on' => null,
        ]);

        // Create an overdue invoice for the customer
        ARInvoiceHeader::factory()->create([
            'customer_id' => $customerFinancials->customer_id,
            'due_date' => now()->subDays(15)->toDateString(),
            'payment_status' => 'Open',
        ]);

        $runDate = now()->toDateString();
        $response = $this->postJson('/api/fina/ar/dunning-runs', ['run_date' => $runDate]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Dunning run completed successfully.',
                'dunned_customers_count' => 1,
            ]);

        $this->assertDatabaseHas('fina_ar_customer_financials', [
            'id' => $customerFinancials->id,
            'dunning_level' => 1,
            'last_dunned_on' => $runDate,
        ]);

        $this->assertDatabaseHas('fina_ar_dunning_history', [
            'customer_financials_id' => $customerFinancials->id,
            'dunning_level' => 1,
            'dunning_date' => $runDate,
        ]);
    }
}