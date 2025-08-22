<?php

namespace Modules\Fina\Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Modules\Fina\FI\GL\Domain\Entities\ChartOfAccount;
use Modules\Fina\FI\GL\Domain\Entities\GLAccount;
use Modules\Fina\FI\GL\Domain\Entities\GLDocumentHeader;
use Modules\Fina\Tests\TestCase;

class AccrualReversalTest extends TestCase
{
    use RefreshDatabase;

    private $companyCode;
    private $glAccount1;
    private $glAccount2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadMigrationsFrom(__DIR__ . '/../../../database/migrations');

        DB::table('fina_company_codes')->insert(['id' => 1, 'code' => '1000', 'name' => 'Test Co', 'country_code' => 'US', 'local_currency_code' => 'USD', 'chart_of_accounts_id' => 1, 'fiscal_year_variant_id' => 1]);
        $this->companyCode = (object) ['id' => 1];

        $chartOfAccount = ChartOfAccount::factory()->create(['id' => 1]);
        $this->glAccount1 = GLAccount::factory()->create(['chart_of_accounts_id' => $chartOfAccount->id]);
        $this->glAccount2 = GLAccount::factory()->create(['chart_of_accounts_id' => $chartOfAccount->id]);
    }

    private function createAccrualDocument(string $postingDate, string $reversalDate): GLDocumentHeader
    {
        $data = [
            'company_code_id' => $this->companyCode->id,
            'posting_date' => $postingDate,
            'document_date' => $postingDate,
            'document_type' => 'SA',
            'transaction_currency_code' => 'USD',
            'header_text' => 'Accrued Expense',
            'is_reversing_entry' => true,
            'reverses_on_date' => $reversalDate,
            'items' => [
                ['gl_account_id' => $this->glAccount1->id, 'posting_type' => 'Debit', 'amount_transaction_currency' => 500],
                ['gl_account_id' => $this->glAccount2->id, 'posting_type' => 'Credit', 'amount_transaction_currency' => 500],
            ],
        ];

        $response = $this->postJson('/api/fina/gl/documents', $data);
        $response->assertStatus(201);

        return GLDocumentHeader::find($response->json('id'));
    }

    public function test_can_post_document_marked_for_reversal()
    {
        $reversalDate = Carbon::now()->addMonth()->toDateString();
        $doc = $this->createAccrualDocument(Carbon::now()->toDateString(), $reversalDate);

        $this->assertTrue($doc->is_reversing_entry);
        $this->assertEquals($reversalDate, $doc->reverses_on_date->format('Y-m-d'));
    }

    public function test_reversal_run_processes_due_accruals()
    {
        // Create an accrual document that was due for reversal yesterday
        $doc = $this->createAccrualDocument(
            Carbon::now()->subMonths(1)->toDateString(),
            Carbon::now()->subDay()->toDateString()
        );

        // Run the reversal service
        $response = $this->postJson('/api/fina/gl/closing-operations/run-accrual-reversals');

        $response->assertStatus(200)
                 ->assertJsonFragment(['success' => [$doc->id]]);

        // Assert that the original document was updated
        $originalDoc = $doc->fresh();
        $this->assertFalse($originalDoc->is_reversing_entry);
        $this->assertNotNull($originalDoc->reversed_by_document_id);

        // Assert that a new reversal document was created
        $this->assertDatabaseHas('fina_gl_document_headers', [
            'id' => $originalDoc->reversed_by_document_id,
            'reverses_document_id' => $originalDoc->id,
        ]);
    }

    public function test_reversal_run_does_not_process_future_accruals()
    {
        // Create an accrual document due for reversal next month
        $this->createAccrualDocument(
            Carbon::now()->toDateString(),
            Carbon::now()->addMonth()->toDateString()
        );

        // Run the reversal service
        $response = $this->postJson('/api/fina/gl/closing-operations/run-accrual-reversals');

        $response->assertStatus(200)
                 ->assertJsonPath('success', []);
    }
}
