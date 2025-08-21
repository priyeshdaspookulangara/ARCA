<?php

namespace Modules\Fina\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Fina\FI\GL\Domain\Entities\ChartOfAccount;

class ChartOfAccountTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadMigrationsFrom(__DIR__ . '/../../../database/migrations');
    }

    public function test_can_list_charts_of_accounts()
    {
        ChartOfAccount::factory()->count(3)->create();

        $response = $this->getJson('/api/fina/gl/charts-of-accounts');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    public function test_can_create_chart_of_account()
    {
        $data = [
            'code' => 'INT',
            'name' => 'International Chart of Accounts',
            'language_key' => 'EN',
            'length_gl_account_number' => 8,
        ];

        $response = $this->postJson('/api/fina/gl/charts-of-accounts', $data);

        $response->assertStatus(201)
                 ->assertJsonFragment($data);

        $this->assertDatabaseHas('fina_charts_of_accounts', $data);
    }

    public function test_create_chart_of_account_validation()
    {
        $response = $this->postJson('/api/fina/gl/charts-of-accounts', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['code', 'name', 'language_key', 'length_gl_account_number']);
    }

    public function test_can_show_chart_of_account()
    {
        $chartOfAccount = ChartOfAccount::factory()->create();

        $response = $this->getJson('/api/fina/gl/charts-of-accounts/' . $chartOfAccount->id);

        $response->assertStatus(200)
                 ->assertJsonFragment(['code' => $chartOfAccount->code]);
    }

    public function test_can_update_chart_of_account()
    {
        $chartOfAccount = ChartOfAccount::factory()->create();

        $data = [
            'name' => 'Updated Name',
            'code' => $chartOfAccount->code,
            'language_key' => $chartOfAccount->language_key,
            'length_gl_account_number' => $chartOfAccount->length_gl_account_number,
        ];

        $response = $this->putJson('/api/fina/gl/charts-of-accounts/' . $chartOfAccount->id, $data);

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'Updated Name']);

        $this->assertDatabaseHas('fina_charts_of_accounts', ['id' => $chartOfAccount->id, 'name' => 'Updated Name']);
    }

    public function test_can_delete_chart_of_account()
    {
        $chartOfAccount = ChartOfAccount::factory()->create();

        $response = $this->deleteJson('/api/fina/gl/charts-of-accounts/' . $chartOfAccount->id);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('fina_charts_of_accounts', ['id' => $chartOfAccount->id]);
    }
}
