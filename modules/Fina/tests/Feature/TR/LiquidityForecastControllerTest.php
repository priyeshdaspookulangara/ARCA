<?php

namespace Modules\Fina\Tests\Feature\TR;

use Modules\Fina\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Fina\TR\Domain\LiquidityForecast;

class LiquidityForecastControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_liquidity_forecast()
    {
        $data = [
            'forecast_date' => '2024-01-01',
            'currency' => 'USD',
            'inflows' => 150000.00,
            'outflows' => 75000.00,
            'net_flow' => 75000.00,
            'description' => 'Test liquidity forecast',
        ];

        $response = $this->postJson('/api/fina/tr/liquidity-forecasts', $data);

        $response->assertStatus(201)
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('fina_tr_liquidity_forecasts', $data);
    }

    public function test_can_get_liquidity_forecast()
    {
        $liquidityForecast = LiquidityForecast::factory()->create();

        $response = $this->getJson("/api/fina/tr/liquidity-forecasts/{$liquidityForecast->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'currency' => $liquidityForecast->currency,
                'net_flow' => $liquidityForecast->net_flow,
            ]);
    }

    public function test_can_get_all_liquidity_forecasts()
    {
        LiquidityForecast::factory()->count(3)->create();

        $response = $this->getJson('/api/fina/tr/liquidity-forecasts');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_can_update_liquidity_forecast()
    {
        $liquidityForecast = LiquidityForecast::factory()->create();

        $data = [
            'inflows' => 160000.00,
            'description' => 'Updated liquidity forecast',
        ];

        $response = $this->putJson("/api/fina/tr/liquidity-forecasts/{$liquidityForecast->id}", $data);

        $response->assertStatus(200)
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('fina_tr_liquidity_forecasts', $data);
    }

    public function test_can_delete_liquidity_forecast()
    {
        $liquidityForecast = LiquidityForecast::factory()->create();

        $response = $this->deleteJson("/api/fina/tr/liquidity-forecasts/{$liquidityForecast->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('fina_tr_liquidity_forecasts', ['id' => $liquidityForecast->id]);
    }
}