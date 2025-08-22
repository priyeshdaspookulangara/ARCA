<?php

namespace Modules\Fina\Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Modules\Fina\FI\GL\Domain\Entities\ChartOfAccount;
use Modules\Fina\FI\GL\Domain\Entities\GLAccount;
use Modules\Fina\FI\GL\Domain\Entities\RecurringEntryDocument;
use Modules\Fina\Tests\TestCase;

class RecurringEntriesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadMigrationsFrom(__DIR__ . '/../../../database/migrations');

        DB::table('fina_company_codes')->insert(['id' => 1, 'code' => '1000', 'name' => 'Test Co', 'country_code' => 'US', 'local_currency_code' => 'USD', 'chart_of_accounts_id' => 1, 'fiscal_year_variant_id' => 1]);
        ChartOfAccount::factory()->create(['id' => 1]);
    }

    public function test_can_create_recurring_entry_template()
    {
        $glAccount1 = GLAccount::factory()->create();
        $glAccount2 = GLAccount::factory()->create();

        $data = [
            'company_code_id' => 1,
            'document_type' => 'SA',
            'transaction_currency_code' => 'USD',
            'header_text' => 'Monthly Insurance',
            'frequency' => 'MONTHLY',
            'start_date' => '2023-01-01',
            'items' => [
                ['gl_account_id' => $glAccount1->id, 'posting_type' => 'Debit', 'amount_transaction_currency' => 200],
                ['gl_account_id' => $glAccount2->id, 'posting_type' => 'Credit', 'amount_transaction_currency' => 200],
            ],
        ];

        $response = $this->postJson('/api/fina/gl/recurring-entries', $data);

        $response->assertStatus(201)
                 ->assertJsonFragment(['header_text' => 'Monthly Insurance']);

        $this->assertDatabaseHas('fina_gl_recurring_entry_documents', ['header_text' => 'Monthly Insurance']);
        $this->assertDatabaseCount('fina_gl_recurring_entry_items', 2);
    }

    public function test_recurring_entry_run_posts_due_documents()
    {
        $rentAccount = GLAccount::factory()->create();
        $cashAccount = GLAccount::factory()->create();

        // Create a recurring entry for rent that was due last month
        $recurringDoc = RecurringEntryDocument::factory()->create([
            'next_run_date' => Carbon::now()->subMonth(),
            'frequency' => 'MONTHLY',
        ]);
        $recurringDoc->items()->createMany([
            ['gl_account_id' => $rentAccount->id, 'posting_type' => 'Debit', 'amount_transaction_currency' => 1500],
            ['gl_account_id' => $cashAccount->id, 'posting_type' => 'Credit', 'amount_transaction_currency' => 1500],
        ]);

        // Run the posting service
        $response = $this->postJson('/api/fina/gl/recurring-entries/run');

        $response->assertStatus(200)
                 ->assertJsonFragment(['success' => [$recurringDoc->id]]);

        // Assert a real GL document was created
        $this->assertDatabaseHas('fina_gl_document_headers', [
            'header_text' => $recurringDoc->header_text . ' - Recurring Entry'
        ]);

        // Assert the next_run_date was updated correctly (to be in the future now)
        $updatedRecurringDoc = $recurringDoc->fresh();
        $this->assertTrue($updatedRecurringDoc->next_run_date->isFuture());
        $this->assertEquals($recurringDoc->next_run_date->addMonth()->toDateString(), $updatedRecurringDoc->next_run_date->toDateString());
    }

    public function test_recurring_entry_run_does_not_post_future_documents()
    {
        // Create a recurring entry for rent that is due next month
        RecurringEntryDocument::factory()->create([
            'next_run_date' => Carbon::now()->addMonth(),
        ]);

        // Run the posting service
        $response = $this->postJson('/api/fina/gl/recurring-entries/run');

        $response->assertStatus(200)
                 ->assertJsonPath('success', []);

        $this->assertDatabaseCount('fina_gl_document_headers', 0);
    }
}
