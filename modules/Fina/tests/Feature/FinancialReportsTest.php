<?php

namespace Modules\Fina\Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Modules\Fina\FI\GL\Domain\Entities\ChartOfAccount;
use Modules\Fina\FI\GL\Domain\Entities\GLAccount;
use Modules\Fina\FI\GL\Domain\Entities\GLDocumentHeader;
use Modules\Fina\Tests\TestCase;

class FinancialReportsTest extends TestCase
{
    use RefreshDatabase;

    private $companyCode;
    private $assetAccount; // Balance Sheet
    private $revenueAccount; // P&L
    private $expenseAccount; // P&L
    private $equityAccount; // Balance Sheet

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadMigrationsFrom(__DIR__ . '/../../../database/migrations');

        DB::table('fina_company_codes')->insert([
            'id' => 1,
            'code' => '1000',
            'name' => 'Test Company',
            'country_code' => 'US',
            'local_currency_code' => 'USD',
            'chart_of_accounts_id' => 1,
            'fiscal_year_variant_id' => 1,
        ]);
        $this->companyCode = (object) ['id' => 1];

        $chartOfAccount = ChartOfAccount::factory()->create(['id' => 1]);

        $this->assetAccount = GLAccount::factory()->create(['chart_of_accounts_id' => $chartOfAccount->id, 'account_type' => 'Balance Sheet']);
        $this->equityAccount = GLAccount::factory()->create(['chart_of_accounts_id' => $chartOfAccount->id, 'account_type' => 'Balance Sheet']);
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

    public function test_can_generate_trial_balance()
    {
        // Sale: Debit Asset, Credit Revenue
        $this->createDocumentForTesting('2023-10-15', [
            ['gl_account_id' => $this->assetAccount->id, 'posting_type' => 'Debit', 'amount_local_currency' => 1000],
            ['gl_account_id' => $this->revenueAccount->id, 'posting_type' => 'Credit', 'amount_local_currency' => 1000],
        ]);
        // Expense: Debit Expense, Credit Asset
        $this->createDocumentForTesting('2023-10-20', [
            ['gl_account_id' => $this->expenseAccount->id, 'posting_type' => 'Debit', 'amount_local_currency' => 300],
            ['gl_account_id' => $this->assetAccount->id, 'posting_type' => 'Credit', 'amount_local_currency' => 300],
        ]);

        $response = $this->getJson('/api/fina/gl/reports/trial-balance?company_code_id=1&from_date=2023-10-01&to_date=2023-10-31');

        $response->assertStatus(200)
                 ->assertJsonPath('totals.balanced', true)
                 ->assertJsonPath('totals.debit', 1300)
                 ->assertJsonPath('totals.credit', 1300);
    }

    public function test_can_generate_p_and_l_statement()
    {
        // Sale
        $this->createDocumentForTesting('2023-10-15', [
            ['gl_account_id' => $this->assetAccount->id, 'posting_type' => 'Debit', 'amount_local_currency' => 1000],
            ['gl_account_id' => $this->revenueAccount->id, 'posting_type' => 'Credit', 'amount_local_currency' => 1000],
        ]);
        // Expense
        $this->createDocumentForTesting('2023-10-20', [
            ['gl_account_id' => $this->expenseAccount->id, 'posting_type' => 'Debit', 'amount_local_currency' => 300],
            ['gl_account_id' => $this->assetAccount->id, 'posting_type' => 'Credit', 'amount_local_currency' => 300],
        ]);

        $response = $this->getJson('/api/fina/gl/reports/profit-and-loss?company_code_id=1&from_date=2023-10-01&to_date=2023-10-31');

        $response->assertStatus(200)
                 ->assertJsonCount(2, 'data') // Should only contain the 2 P&L accounts
                 ->assertJsonPath('net_income', 700); // 1000 revenue (credit) - 300 expense (debit)
    }

    public function test_can_generate_balance_sheet()
    {
        // Initial investment: Debit Asset, Credit Equity
        $this->createDocumentForTesting('2023-01-01', [
            ['gl_account_id' => $this->assetAccount->id, 'posting_type' => 'Debit', 'amount_local_currency' => 5000],
            ['gl_account_id' => $this->equityAccount->id, 'posting_type' => 'Credit', 'amount_local_currency' => 5000],
        ]);
        // Sale
        $this->createDocumentForTesting('2023-10-15', [
            ['gl_account_id' => $this->assetAccount->id, 'posting_type' => 'Debit', 'amount_local_currency' => 1000],
            ['gl_account_id' => $this->revenueAccount->id, 'posting_type' => 'Credit', 'amount_local_currency' => 1000],
        ]);
        // Expense
        $this->createDocumentForTesting('2023-10-20', [
            ['gl_account_id' => $this->expenseAccount->id, 'posting_type' => 'Debit', 'amount_local_currency' => 300],
            ['gl_account_id' => $this->assetAccount->id, 'posting_type' => 'Credit', 'amount_local_currency' => 300],
        ]);

        $response = $this->getJson('/api/fina/gl/reports/balance-sheet?company_code_id=1&as_of_date=2023-10-31');

        $response->assertStatus(200)
                 ->assertJsonCount(2, 'data'); // Should only contain the 2 Balance Sheet accounts

        $data = collect($response->json('data'));
        $assetBalance = $data->where('gl_account_id', $this->assetAccount->id)->first()['balance'];
        $equityBalance = $data->where('gl_account_id', $this->equityAccount->id)->first()['balance'];

        // Asset: 5000 + 1000 - 300 = 5700
        $this->assertEquals(5700, $assetBalance);
        // Equity: -5000
        $this->assertEquals(-5000, $equityBalance);
    }
}
