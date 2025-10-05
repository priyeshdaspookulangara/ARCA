<?php

namespace Modules\Fina\Tests\Feature\CO\PC;

use Modules\Fina\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Fina\CO\PC\Domain\ProductCostHeader;
use Modules\Fina\CO\PC\Domain\CostElement;
use Modules\Fina\CO\PC\Domain\ActivityType;

class ProductCostingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_product_cost_estimate()
    {
        $costElement = CostElement::factory()->create();
        $activityType = ActivityType::factory()->create();

        $data = [
            'header' => [
                'product_id' => 'TEST-PRODUCT-001',
                'costing_variant' => 'standard',
                'costing_date' => '2024-01-01',
            ],
            'items' => [
                [
                    'cost_element_id' => $costElement->id,
                    'quantity' => 10,
                    'rate' => 5,
                ],
                [
                    'activity_type_id' => $activityType->id,
                    'quantity' => 2,
                    'rate' => 25,
                ],
            ],
        ];

        $response = $this->postJson('/api/fina/co/pc/product-cost-estimates', $data);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'product_id' => 'TEST-PRODUCT-001',
                'total_cost' => '100.00',
            ]);

        $this->assertDatabaseHas('fina_co_pc_product_cost_headers', [
            'product_id' => 'TEST-PRODUCT-001',
        ]);

        $this->assertDatabaseHas('fina_co_pc_product_cost_items', [
            'quantity' => 10,
            'rate' => 5,
        ]);
    }

    public function test_can_get_product_cost_estimate()
    {
        $productCostEstimate = ProductCostHeader::factory()->create();

        $response = $this->getJson("/api/fina/co/pc/product-cost-estimates/{$productCostEstimate->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'product_id' => $productCostEstimate->product_id,
            ]);
    }

    public function test_can_get_all_product_cost_estimates()
    {
        ProductCostHeader::factory()->count(3)->create();

        $response = $this->getJson('/api/fina/co/pc/product-cost-estimates');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_can_delete_product_cost_estimate()
    {
        $productCostEstimate = ProductCostHeader::factory()->create();

        $response = $this->deleteJson("/api/fina/co/pc/product-cost-estimates/{$productCostEstimate->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('fina_co_pc_product_cost_headers', ['id' => $productCostEstimate->id]);
    }
}