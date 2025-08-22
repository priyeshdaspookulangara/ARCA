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
    private $cashAccount;
    private $arAccount; // Accounts Receivable
    private $apAccount; // Accounts Payable
    private $ppeAccount; // Property, Plant, Equipment
    private $revenueAccount;
    private $expenseAccount;
    private $equityAccount;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadMigrationsFrom(__DIR__ . '/../../../database/migrations');

        DB::table('fina_company_codes')->insert(['id' => 1, 'code' => '1000', 'name' => 'Test Company', 'country_code' => 'US', 'local_currency_code' => 'USD', 'chart_of_accounts_id' => 1, 'fiscal_year_variant_id' => 1]);
        $this->companyCode = (object) ['id' => 1];

        $chartOfAccount = ChartOfAccount::factory()->create(['id' => 1]);

        // Create accounts with specific classifications for CFS
        $this->cashAccount = GLAccount::factory()->create(['chart_of_accounts_id' => $chartOfAccount->id, 'account_type' => 'Balance Sheet', 'classification' => 'Cash']);
        $this->arAccount = GLAccount::factory()->create(['chart_of_accounts_id' => $chartOfAccount->id, 'account_type' => 'Balance Sheet', 'classification' => 'Accounts Receivable']);
        $this->apAccount = GLAccount::factory()->create(['chart_of_accounts_id' => $chartOfAccount->id, 'account_type' => 'Balance Sheet', 'classification' => 'Accounts Payable']);
        $this->ppeAccount = GLAccount::factory()->create(['chart_of_accounts_id' => $chartOfAccount->id, 'account_type' => 'Balance Sheet', 'classification' => 'Property, Plant, Equipment']);
        $this->equityAccount = GLAccount::factory()->create(['chart_of_accounts_id' => $chartOfAccount->id, 'account_type' => 'Balance Sheet', 'classification' => 'Common Stock']);
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
        // Sale: Debit Cash, Credit Revenue
        $this->createDocumentForTesting('2023-10-15', [
            ['gl_account_id' => $this->cashAccount->id, 'posting_type' => 'Debit', 'amount_local_currency' => 1000],
            ['gl_account_id' => $this->revenueAccount->id, 'posting_type' => 'Credit', 'amount_local_currency' => 1000],
        ]);
        // Expense: Debit Expense, Credit Cash
        $this->createDocumentForTesting('2023-10-20', [
            ['gl_account_id' => $this->expenseAccount->id, 'posting_type' => 'Debit', 'amount_local_currency' => 300],
            ['gl_account_id' => $this->cashAccount->id, 'posting_type' => 'Credit', 'amount_local_currency' => 300],
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
            ['gl_account_id' => $this->cashAccount->id, 'posting_type' => 'Debit', 'amount_local_currency' => 1000],
            ['gl_account_id' => $this->revenueAccount->id, 'posting_type' => 'Credit', 'amount_local_currency' => 1000],
        ]);
        // Expense
        $this->createDocumentForTesting('2023-10-20', [
            ['gl_account_id' => $this->expenseAccount->id, 'posting_type' => 'Debit', 'amount_local_currency' => 300],
            ['gl_account_id' => $this->cashAccount->id, 'posting_type' => 'Credit', 'amount_local_currency' => 300],
        ]);

        $response = $this->getJson('/api/fina/gl/reports/profit-and-loss?company_code_id=1&from_date=2023-10-01&to_date=2023-10-31');

        $response->assertStatus(200)
                 ->assertJsonCount(2, 'data') // Should only contain the 2 P&L accounts
                 ->assertJsonPath('net_income', 700); // 1000 revenue (credit) - 300 expense (debit)
    }

    public function test_can_generate_balance_sheet()
    {
        // Initial investment: Debit Cash, Credit Equity
        $this->createDocumentForTesting('2023-01-01', [
            ['gl_account_id' => $this->cashAccount->id, 'posting_type' => 'Debit', 'amount_local_currency' => 5000],
            ['gl_account_id' => $this->equityAccount->id, 'posting_type' => 'Credit', 'amount_local_currency' => 5000],
        ]);
        // Sale
        $this->createDocumentForTesting('2023-10-15', [
            ['gl_account_id' => $this->cashAccount->id, 'posting_type' => 'Debit', 'amount_local_currency' => 1000],
            ['gl_account_id' => $this->revenueAccount->id, 'posting_type' => 'Credit', 'amount_local_currency' => 1000],
        ]);
        // Expense
        $this->createDocumentForTesting('2023-10-20', [
            ['gl_account_id' => $this->expenseAccount->id, 'posting_type' => 'Debit', 'amount_local_currency' => 300],
            ['gl_account_id' => $this->cashAccount->id, 'posting_type' => 'Credit', 'amount_local_currency' => 300],
        ]);

        $response = $this->getJson('/api/fina/gl/reports/balance-sheet?company_code_id=1&as_of_date=2023-10-31');

        $response->assertStatus(200);

        $data = collect($response->json('data'));
        // Cash: 5000 + 1000 - 300 = 5700
        $this->assertEquals(5700, $data->where('gl_account_id', $this->cashAccount->id)->first()['balance']);
        // Equity: -5000
        $this->assertEquals(-5000, $data->where('gl_account_id', $this->equityAccount->id)->first()['balance']);
    }

    public function test_can_generate_cash_flow_statement()
    {
        // Period is Oct 1 to Oct 31
        // Initial State at Sep 30: AR = 100, AP = 50
        $this->createDocumentForTesting('2023-09-20', [['gl_account_id' => $this->arAccount->id, 'posting_type' => 'Debit', 'amount_local_currency' => 100], ['gl_account_id' => $this->revenueAccount->id, 'posting_type' => 'Credit', 'amount_local_currency' => 100]]);
        $this->createDocumentForTesting('2023-09-25', [['gl_account_id' => $this->expenseAccount->id, 'posting_type' => 'Debit', 'amount_local_currency' => 50], ['gl_account_id' => $this->apAccount->id, 'posting_type' => 'Credit', 'amount_local_currency' => 50]]);

        // Transactions during October
        // 1. Sale on credit: +200 AR, +200 Revenue. Net Income = +200
        $this->createDocumentForTesting('2023-10-10', [['gl_account_id' => $this->arAccount->id, 'posting_type' => 'Debit', 'amount_local_currency' => 200], ['gl_account_id' => $this->revenueAccount->id, 'posting_type' => 'Credit', 'amount_local_currency' => 200]]);
        // 2. Collect cash from previous sale: +100 Cash, -100 AR
        $this->createDocumentForTesting('2023-10-15', [['gl_account_id' => $this->cashAccount->id, 'posting_type' => 'Debit', 'amount_local_currency' => 100], ['gl_account_id' => $this->arAccount->id, 'posting_type' => 'Credit', 'amount_local_currency' => 100]]);
        // 3. Buy PPE with cash: +300 PPE, -300 Cash
        $this->createDocumentForTesting('2023-10-20', [['gl_account_id' => $this->ppeAccount->id, 'posting_type' => 'Debit', 'amount_local_currency' => 300], ['gl_account_id' => $this->cashAccount->id, 'posting_type' => 'Credit', 'amount_local_currency' => 300]]);

        $response = $this->getJson('/api/fina/gl/reports/cash-flow-statement?company_code_id=1&from_date=2023-10-01&to_date=2023-10-31');

        $response->assertStatus(200);

        // NI = 200 (from revenue)
        // Change in AR = (100+200-100) - 100 = +100. Use of cash, so subtract 100.
        // Change in AP = 0 - 50 = -50. Use of cash, so subtract 50.
        // CFO = 200 - 100 - 50 = 50
        $this->assertEquals(50, $response->json('operating_activities.total'));

        // Change in PPE = +300. Use of cash, so subtract 300.
        // CFI = -300
        $this->assertEquals(-300, $response->json('investing_activities.total'));

        // CFF = 0
        $this->assertEquals(0, $response->json('financing_activities.total'));

        // Net change = 50 - 300 = -250
        $this->assertEquals(-250, $response->json('net_cash_change'));
    }
}
