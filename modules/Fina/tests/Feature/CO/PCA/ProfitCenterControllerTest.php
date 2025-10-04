<?php

namespace Modules\Fina\Tests\Feature\CO\PCA;

use Modules\Fina\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Fina\CO\PCA\Domain\ProfitCenter;
use Modules\Fina\CO\CCA\Domain\Entities\ControllingArea;

class ProfitCenterControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_profit_center()
    {
        $controllingArea = ControllingArea::factory()->create();

        $data = [
            'name' => 'Test Profit Center',
            'description' => 'This is a test profit center.',
            'controlling_area_id' => $controllingArea->id,
            'responsible_person' => 'John Doe',
        ];

        $response = $this->postJson('/api/fina/co/pca/profit-centers', $data);

        $response->assertStatus(201)
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('fina_co_pca_profit_centers', $data);
    }

    public function test_can_get_profit_center()
    {
        $profitCenter = ProfitCenter::factory()->create();

        $response = $this->getJson("/api/fina/co/pca/profit-centers/{$profitCenter->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => $profitCenter->name,
                'description' => $profitCenter->description,
            ]);
    }

    public function test_can_get_all_profit_centers()
    {
        ProfitCenter::factory()->count(3)->create();

        $response = $this->getJson('/api/fina/co/pca/profit-centers');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_can_update_profit_center()
    {
        $profitCenter = ProfitCenter::factory()->create();

        $data = [
            'name' => 'Updated Profit Center',
            'description' => 'This is an updated profit center.',
        ];

        $response = $this->putJson("/api/fina/co/pca/profit-centers/{$profitCenter->id}", $data);

        $response->assertStatus(200)
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('fina_co_pca_profit_centers', $data);
    }

    public function test_can_delete_profit_center()
    {
        $profitCenter = ProfitCenter::factory()->create();

        $response = $this->deleteJson("/api/fina/co/pca/profit-centers/{$profitCenter->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('fina_co_pca_profit_centers', ['id' => $profitCenter->id]);
    }
}