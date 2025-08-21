<?php

namespace Modules\Fina\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Modules\Fina\FI\GL\Domain\Entities\ChartOfAccount;
use Modules\Fina\FI\GL\Domain\Entities\GLAccount;
use Modules\Fina\Tests\TestCase;

class ClosingOperationsTest extends TestCase
{
    use RefreshDatabase;

    private $companyCode;
    private $assetAccount;
    private $revenueAccount;
    private $expenseAccount;
    private $retainedEarningsAccount;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadMigrationsFrom(__DIR__ . '/../../../database/migrations');

        $this->retainedEarningsAccount = GLAccount::factory()->create(['account_type' => 'Balance Sheet']);

        $chartOfAccount = ChartOfAccount::factory()->create([
            'id' => 1,
            'retained_earnings_gl_account_id' => $this->retainedEarningsAccount->id,
        ]);

        DB::table('fina_company_codes')->insert([
            'id' => 1,
            'code' => '1000',
            'name' => 'Test Company',
            'country_code' => 'US',
            'local_currency_code' => 'USD',
            'chart_of_accounts_id' => $chartOfAccount->id,
            'fiscal_year_variant_id' => 1,
        ]);
        $this->companyCode = (object) ['id' => 1];

        $this->assetAccount = GLAccount::factory()->create(['chart_of_accounts_id' => $chartOfAccount->id, 'account_type' => 'Balance Sheet']);
        $this->revenueAccount = GLAccount::factory()->create(['chart_of_accounts_id' => $chartOfAccount->id, 'account_type' => 'P&L']);
        $this->expenseAccount = GLAccount::factory()->create(['chart_of_accounts_id' => $chartOfAccount->id, 'account_type' => 'P&L']);
    }

    private function createDocumentForTesting(string $postingDate, array $items)
    {
        $data = [
            'company_code_id' => $this->companyCode->id,
            'posting_date' => $postingDate,
            'document_date' => $postingDate,
            'document_type' => 'SA',
            'transaction_currency_code' => 'USD',
            'created_by_user_id' => 1,
            'items' => $items,
        ];

        $this->postJson('/api/fina/gl/documents', $data)->assertStatus(201);
    }

    public function test_can_perform_year_end_closing_and_balance_carry_forward()
    {
        // Post transactions for fiscal year 2023
        // Revenue: 1000, Expense: 400 => Net Income: 600
        $this->createDocumentForTesting('2023-05-10', [
            ['gl_account_id' => $this->assetAccount->id, 'posting_type' => 'Debit', 'amount_local_currency' => 1000],
            ['gl_account_id' => $this->revenueAccount->id, 'posting_type' => 'Credit', 'amount_local_currency' => 1000],
        ]);
        $this->createDocumentForTesting('2023-06-15', [
            ['gl_account_id' => $this->expenseAccount->id, 'posting_type' => 'Debit', 'amount_local_currency' => 400],
            ['gl_account_id' => $this->assetAccount->id, 'posting_type' => 'Credit', 'amount_local_currency' => 400],
        ]);

        // Run the closing process for fiscal year 2023
        $response = $this->postJson('/api/fina/gl/closing-operations/balance-carry-forward', [
            'company_code_id' => $this->companyCode->id,
            'fiscal_year' => 2023,
        ]);

        $response->assertStatus(200)->assertJsonPath('status', 'success');

        // Verify P&L accounts are zero in the new year (2024)
        $pnlResponse = $this->getJson('/api/fina/gl/reports/profit-and-loss?company_code_id=1&from_date=2024-01-01&to_date=2024-12-31');
        $pnlResponse->assertStatus(200)->assertJsonPath('net_income', 0.0);

        // Verify Retained Earnings and other B/S accounts in the new year
        $bsResponse = $this->getJson('/api/fina/gl/reports/balance-sheet?company_code_id=1&as_of_date=2024-01-01');
        $bsResponse->assertStatus(200);

        $data = collect($bsResponse->json('data'));
        $retainedEarningsBalance = $data->where('gl_account_id', $this->retainedEarningsAccount->id)->first()['balance'];
        $assetBalance = $data->where('gl_account_id', $this->assetAccount->id)->first()['balance'];

        // Retained Earnings should now hold the net income of 600 (as a credit, so -600 balance)
        $this->assertEquals(-600, $retainedEarningsBalance);
        // Asset account should have its closing balance of 600 (1000 - 400)
        $this->assertEquals(600, $assetBalance);
    }
}
