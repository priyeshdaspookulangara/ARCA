<?php

namespace Modules\Fina\Tests\Feature\CO\PCA;

use Modules\Fina\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Fina\CO\PCA\Domain\PcaPosting;
use Modules\Fina\CO\PCA\Domain\ProfitCenter;
use Modules\Fina\FI\GL\Domain\Entities\GLAccount;

class PcaPostingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_posting()
    {
        $profitCenter = ProfitCenter::factory()->create();
        $glAccount = GLAccount::factory()->create();

        $data = [
            'profit_center_id' => $profitCenter->id,
            'gl_account_id' => $glAccount->id,
            'document_number' => 'DOC-12345',
            'amount' => 123.45,
            'posting_date' => '2024-01-01',
            'description' => 'Test posting',
        ];

        $response = $this->postJson('/api/fina/co/pca/postings', $data);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'document_number' => 'DOC-12345',
                'amount' => '123.45',
            ]);

        $this->assertDatabaseHas('fina_co_pca_postings', [
            'document_number' => 'DOC-12345',
        ]);
    }

    public function test_can_get_posting()
    {
        $posting = PcaPosting::factory()->create();

        $response = $this->getJson("/api/fina/co/pca/postings/{$posting->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'document_number' => $posting->document_number,
            ]);
    }

    public function test_can_get_postings_for_profit_center()
    {
        $profitCenter = ProfitCenter::factory()->create();
        PcaPosting::factory()->count(3)->create(['profit_center_id' => $profitCenter->id]);

        $response = $this->getJson("/api/fina/co/pca/postings?profit_center_id={$profitCenter->id}");

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_can_delete_posting()
    {
        $posting = PcaPosting::factory()->create();

        $response = $this->deleteJson("/api/fina/co/pca/postings/{$posting->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('fina_co_pca_postings', ['id' => $posting->id]);
    }
}