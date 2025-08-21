<?php

namespace Modules\Fina\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Fina\FI\GL\Domain\Entities\GLAccount;
use Modules\Fina\FI\GL\Domain\Entities\ChartOfAccount;

class GLAccountTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadMigrationsFrom(__DIR__ . '/../../../database/migrations');
    }

    public function test_can_list_gl_accounts()
    {
        GLAccount::factory()->count(3)->create();

        $response = $this->getJson('/api/fina/gl/gl-accounts');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    public function test_can_create_gl_account()
    {
        $chartOfAccount = ChartOfAccount::factory()->create();
        $data = [
            'chart_of_accounts_id' => $chartOfAccount->id,
            'account_number' => '100000',
            'name' => 'Cash',
            'account_type' => 'Balance Sheet',
            'is_open_item_managed' => false,
        ];

        $response = $this->postJson('/api/fina/gl/gl-accounts', $data);

        $response->assertStatus(201)
                 ->assertJsonFragment(['account_number' => '100000']);

        $this->assertDatabaseHas('fina_gl_accounts', $data);
    }

    public function test_create_gl_account_validation_fails()
    {
        $response = $this->postJson('/api/fina/gl/gl-accounts', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['chart_of_accounts_id', 'account_number', 'name', 'account_type']);
    }

    public function test_account_number_must_be_unique_within_coa()
    {
        $chartOfAccount = ChartOfAccount::factory()->create();
        GLAccount::factory()->create([
            'chart_of_accounts_id' => $chartOfAccount->id,
            'account_number' => '100000',
        ]);

        $data = [
            'chart_of_accounts_id' => $chartOfAccount->id,
            'account_number' => '100000',
            'name' => 'Cash Duplicate',
            'account_type' => 'Balance Sheet',
            'is_open_item_managed' => false,
        ];

        $response = $this->postJson('/api/fina/gl/gl-accounts', $data);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['account_number']);
    }

    public function test_can_show_gl_account()
    {
        $glAccount = GLAccount::factory()->create();

        $response = $this->getJson('/api/fina/gl/gl-accounts/' . $glAccount->id);

        $response->assertStatus(200)
                 ->assertJsonFragment(['account_number' => $glAccount->account_number]);
    }

    public function test_can_update_gl_account()
    {
        $glAccount = GLAccount::factory()->create();

        $data = [
            'chart_of_accounts_id' => $glAccount->chart_of_accounts_id,
            'account_number' => $glAccount->account_number,
            'name' => 'Updated GL Account Name',
            'account_type' => $glAccount->account_type,
            'is_open_item_managed' => $glAccount->is_open_item_managed,
        ];

        $response = $this->putJson('/api/fina/gl/gl-accounts/' . $glAccount->id, $data);

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'Updated GL Account Name']);

        $this->assertDatabaseHas('fina_gl_accounts', ['id' => $glAccount->id, 'name' => 'Updated GL Account Name']);
    }

    public function test_can_delete_gl_account()
    {
        $glAccount = GLAccount::factory()->create();

        $response = $this->deleteJson('/api/fina/gl/gl-accounts/' . $glAccount->id);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('fina_gl_accounts', ['id' => $glAccount->id]);
    }
}
