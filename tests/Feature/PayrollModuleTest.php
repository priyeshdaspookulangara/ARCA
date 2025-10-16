<?php

namespace Tests\Feature;

use Tests\TestCase;
use Modules\HR\Payroll\Domain\Repositories\PayrollRunRepositoryInterface;
use Modules\HR\Payroll\Domain\Repositories\PaycheckRepositoryInterface;
use Modules\Fina\FI\AP\Domain\Ledger\FinaPayrollLedgerInterface;

use Modules\Fina\Core\Providers\FinaServiceProvider;

class PayrollModuleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->app->register(FinaServiceProvider::class);
    }

    public function test_can_execute_payroll_run_and_post_to_fina()
    {
        $finaLedger = $this->app->make(FinaPayrollLedgerInterface::class);
        $initialGlPostings = $finaLedger->getGeneralLedgerPostings();
        $this->assertCount(0, $initialGlPostings);

        // Execute payroll run
        $response = $this->postJson('/api/payroll/run', [
            'period_start_date' => '2025-10-01',
            'period_end_date' => '2025-10-15'
        ]);
        $response->assertStatus(201)->assertJsonFragment(['status' => 'completed']);
        $payrollRunId = $response->json('id');

        // Verify HR repositories
        $payrollRunRepo = $this->app->make(PayrollRunRepositoryInterface::class);
        $payrollRun = $payrollRunRepo->findById($payrollRunId);
        $this->assertNotNull($payrollRun);
        $this->assertEquals('completed', $payrollRun->getStatus());

        $paycheckRepo = $this->app->make(PaycheckRepositoryInterface::class);
        $paychecks = $paycheckRepo->findByPayrollRun($payrollRunId);
        $this->assertCount(2, $paychecks); // Based on 2 seeded employees

        // Verify Fina ledger is updated
        $updatedGlPostings = $finaLedger->getGeneralLedgerPostings();
        $this->assertCount(1, $updatedGlPostings);
        $this->assertEquals($payrollRunId, $updatedGlPostings[0]['payroll_run_id']);
        $this->assertEquals(62500, $updatedGlPostings[0]['total_gross_pay']); // (50000/2) + (75000/2)
    }
}