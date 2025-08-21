<?php

namespace Modules\Fina\Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Modules\Fina\FI\GL\Domain\Entities\ChartOfAccount;
use Modules\Fina\FI\GL\Domain\Entities\GLAccount;
use Modules\Fina\FI\GL\Domain\Entities\GLDocumentHeader;
use Modules\Fina\Tests\TestCase;

class GLDocumentEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    private $companyCode;
    private $glAccount1;
    private $glAccount2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadMigrationsFrom(__DIR__ . '/../../../database/migrations');

        // Manually create a company code as it likely has no factory
        DB::table('fina_company_codes')->insert([
            'id' => 1,
            'code' => '1000',
            'name' => 'Test Company',
            'country_code' => 'US',
            'local_currency_code' => 'USD',
            'chart_of_accounts_id' => 1, // Assuming this exists or is created
            'fiscal_year_variant_id' => 1,
        ]);
        $this->companyCode = (object) ['id' => 1];

        $chartOfAccount = ChartOfAccount::factory()->create(['id' => 1]);
        $this->glAccount1 = GLAccount::factory()->create(['chart_of_accounts_id' => $chartOfAccount->id]);
        $this->glAccount2 = GLAccount::factory()->create(['chart_of_accounts_id' => $chartOfAccount->id]);
    }

    private function createDocumentForTesting(array $headerData = [], array $itemsData = []): GLDocumentHeader
    {
        $defaultHeader = [
            'company_code_id' => $this->companyCode->id,
            'document_date' => '2023-11-16',
            'posting_date' => '2023-11-16',
            'document_type' => 'SA',
            'transaction_currency_code' => 'USD',
            'created_by_user_id' => 1,
        ];

        $defaultItems = [
            'items' => [
                [
                    'gl_account_id' => $this->glAccount1->id,
                    'posting_type' => 'Debit',
                    'amount_transaction_currency' => 150,
                    'amount_local_currency' => 150,
                ],
                [
                    'gl_account_id' => $this->glAccount2->id,
                    'posting_type' => 'Credit',
                    'amount_transaction_currency' => 150,
                    'amount_local_currency' => 150,
                ],
            ],
        ];

        $data = array_merge($defaultHeader, $headerData, $itemsData ?: $defaultItems);

        $response = $this->postJson('/api/fina/gl/documents', $data);
        $response->assertStatus(201);

        return GLDocumentHeader::find($response->json('id'));
    }

    public function test_can_list_and_filter_documents()
    {
        $this->createDocumentForTesting(['posting_date' => '2023-01-15']);
        $this->createDocumentForTesting(['posting_date' => '2023-02-20']);
        $this->createDocumentForTesting(['posting_date' => '2023-03-25']);

        // Test list all
        $this->getJson('/api/fina/gl/documents')->assertStatus(200)->assertJsonCount(3);

        // Test filter by date
        $this->getJson('/api/fina/gl/documents?posting_date_from=2023-02-01&posting_date_to=2023-02-28')
            ->assertStatus(200)
            ->assertJsonCount(1);
    }

    public function test_can_reverse_a_document()
    {
        $originalDocument = $this->createDocumentForTesting();

        $reversalData = [
            'reversal_reason' => 'R01',
            'reversal_date' => Carbon::now()->toDateString(),
        ];

        $response = $this->postJson('/api/fina/gl/documents/' . $originalDocument->id . '/reverse', $reversalData);
        $response->assertStatus(201);

        $reversalDocumentId = $response->json('id');
        $this->assertDatabaseHas('fina_gl_document_headers', ['id' => $reversalDocumentId, 'reverses_document_id' => $originalDocument->id]);
        $this->assertDatabaseHas('fina_gl_document_headers', ['id' => $originalDocument->id, 'reversed_by_document_id' => $reversalDocumentId]);

        $this->assertEquals(150, $response->json('items.0.amount_transaction_currency'));
        $this->assertEquals('Credit', $response->json('items.0.posting_type'));
        $this->assertEquals('Debit', $response->json('items.1.posting_type'));
    }

    public function test_cannot_reverse_an_already_reversed_document()
    {
        $originalDocument = $this->createDocumentForTesting();

        $reversalData = [
            'reversal_reason' => 'R01',
            'reversal_date' => Carbon::now()->toDateString(),
        ];

        $this->postJson('/api/fina/gl/documents/' . $originalDocument->id . '/reverse', $reversalData)->assertStatus(201);

        // Try to reverse it again
        $this->postJson('/api/fina/gl/documents/' . $originalDocument->id . '/reverse', $reversalData)->assertStatus(409);
    }
}
