<?php

namespace Modules\Fina\Tests\Feature\TR;

use Modules\Fina\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Fina\TR\Domain\CashPosition;

class CashPositionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_cash_position()
    {
        $data = [
            'position_date' => '2024-01-01',
            'currency' => 'USD',
            'amount' => 100000.00,
            'description' => 'Test cash position',
        ];

        $response = $this->postJson('/api/fina/tr/cash-positions', $data);

        $response->assertStatus(201)
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('fina_tr_cash_positions', $data);
    }

    public function test_can_get_cash_position()
    {
        $cashPosition = CashPosition::factory()->create();

        $response = $this->getJson("/api/fina/tr/cash-positions/{$cashPosition->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'currency' => $cashPosition->currency,
                'amount' => $cashPosition->amount,
            ]);
    }

    public function test_can_get_all_cash_positions()
    {
        CashPosition::factory()->count(3)->create();

        $response = $this->getJson('/api/fina/tr/cash-positions');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_can_update_cash_position()
    {
        $cashPosition = CashPosition::factory()->create();

        $data = [
            'amount' => 120000.00,
            'description' => 'Updated cash position',
        ];

        $response = $this->putJson("/api/fina/tr/cash-positions/{$cashPosition->id}", $data);

        $response->assertStatus(200)
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('fina_tr_cash_positions', $data);
    }

    public function test_can_delete_cash_position()
    {
        $cashPosition = CashPosition::factory()->create();

        $response = $this->deleteJson("/api/fina/tr/cash-positions/{$cashPosition->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('fina_tr_cash_positions', ['id' => $cashPosition->id]);
    }
}